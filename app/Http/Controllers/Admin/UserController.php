<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\System\UserClass;
use Illuminate\Http\Request;
use DB;


class UserController extends Controller
{
    public function index()
    {
        return view('admin/user/list');
    }

    /**
     * 查询
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $where = [];

        if ($request->input('trueName')) {
            $where[] = ['true_name', 'like', '%' . $request->input('trueName') . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'created_at',
            'sort' => 'DESC'
        ];
        $sdk_data = UserClass::getList($where, $limit);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => isset($sdk_data['count']) ? $sdk_data['count'] : 0,
            'data' => array()
        );

        if ($sdk_data['count'] > 0) {

            foreach ($sdk_data['data'] as $data) {

                $return_data['data'][] = array(
                    '_id' => $data['_id'],
                    'user_id' => $data['user_id'],
                    'true_name' => $data['true_name'],
                    'mobile' => $data['mobile'],
                );

            }

        }
        return $return_data;

    }

    /**
     * 添加、编辑
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($_id)
    {

        if (empty($_id)) {
            $data['title'] = '新增用户信息';
        } else {
            $data['title'] = '编辑用户信息';
        }

        $edit_data = [];
        $xb = 1;
        if ($_id) {
            $edit_data = UserClass::fetch($_id);
            $xb = 2;
        }

        $data['edit_data'] = $edit_data;
        $data['xb'] = $xb;
        $data['_id'] = $_id;

        return view('admin/user/edit', $data);
    }


    /**
     * 保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $args = $request->all();

        if (!$args['user_id']) {
            return response()->json(['code' => 10001, 'message' => '成员不能为空']);
        }
        if (!$args['true_name']) {
            return response()->json(['code' => 10001, 'message' => '姓名不能为空']);
        }
        if (!$args['mobile']) {
            return response()->json(['code' => 10001, 'message' => '手机号不能为空']);
        }

        $Performance = UserClass::fetch($args['_id']);

        if (!empty($args['user_pwd'])) {
            $Performance['user_pwd'] = md5($args['user_pwd']);
        }

        if ($args['_id'] == 1) {
            $Performance['useFlg'] = 1;
        } else {
            $Performance['useFlg'] = 0;
        }

        if ($args['_id'] == 1) {
            //新增
            UserClass::save([
                'user_id' => $args['user_id'],
                'true_name' => $args['true_name'],
                'mobile' => $args['mobile'],
                'user_pwd' => md5($args['user_pwd']),
                'phone' => $args['phone']
            ]);
        } else {
            //修改
            UserClass::save([
                'user_id' => $args['user_id'],
                'true_name' => $args['true_name'],
                'mobile' => $args['mobile'],
                'phone' => $args['phone']
            ], $args['_id']);
        }

        return response()->json(['code' => 200, 'message' => '保存成功']);
    }


    /**
     * 删除用户
     * @param string $uuid 表唯一字段
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($user_id)
    {

        try {

            UserClass::del($user_id);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'ok', 'code' => 200]);

    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editPwd(Request $request)
    {

        $oldPwd = $request->input('pwd');
        $newPwd = $request->input('user_pwd');
        $userId = $request->input('user_id');

        if (empty($oldPwd)) {
            return response()->json(['code' => 100001, 'message' => '请输入原密码']);
        }

        if (empty($newPwd)) {
            return response()->json(['code' => 100002, 'message' => '请输入新密码']);
        }

        if (!is_pwd($newPwd)) {
            return response()->json(['code' => 100003, 'message' => '新密码格式不正确']);
        }

        if ($newPwd == '12345678') {
            return response()->json(['code' => 100004, 'message' => '新的密码不能为12345678']);
        }

        //查询用户数据
        $user = UserClass::fetch($userId);

        if (!$user) {
            return response()->json(['code' => 100005, 'message' => '当前用户不存在']);
        }

        if ($user['user_pwd'] != md5($oldPwd)) {
            return response()->json(['code' => 100006, 'message' => '当前用户原密码输入错误']);
        }

        if ($newPwd == $oldPwd) {
            return response()->json(['code' => 100007, 'message' => '新的密码不能和原密码相同']);
        }

        $user['useFlg'] = 1;
        UserClass::save([
            'user_pwd' => md5($newPwd)
        ], $userId);

        return response()->json(['code' => 200, 'message' => 'ok']);

    }

}