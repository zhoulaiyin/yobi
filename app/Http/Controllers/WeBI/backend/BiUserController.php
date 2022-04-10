<?php

namespace App\Http\Controllers\WeBI\backend;

use App\Http\Controllers\Controller;
use App\Models\Classes\Control\BiUserClass;
use App\Models\Classes\Control\ProjectClass;
use Illuminate\Http\Request;
use Mockery\Exception;
use DB;


class BiUserController extends Controller
{
    public function index()
    {
        $limit = [
            'pageSize' => 99,
            'orderBy' => 'project_id',
            'sort' => 'DESC'
        ];
        $column = 'project_id, project_name';
        $project_data = ProjectClass::getList([], $limit, $column);

        return view('webi/backend/biuser/list')->with('project_data', json_encode($project_data['data']));
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
        if ($request->input('project_id')) {
            $where[] = ['project_id', 'like', '%' . $request->input('project_id') . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
        ];
        $sdk_data = BiUserClass::getList($where, $limit);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => isset($sdk_data['count']) ? $sdk_data['count'] : 0,
            'data' => array()
        );

        if ($sdk_data['count'] > 0) {

            foreach ($sdk_data['data'] as $data) {

                $return_data['data'][] = array(
                    'user_id' => $data['user_id'],
                    'true_name' => $data['true_name'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'project_name' => $data['project_name'],
                    '_id' => $data['_id']
//                    'id' => $data['id']
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
    public function edit($user_id)
    {

        if (empty($user_id)) {
            $data['title'] = '新增用户信息';
        } else {
            $data['title'] = '编辑用户信息';
        }
        $edit_data = [];
        $xb = 1;
        if ($user_id) {
            $edit_data = BiUserClass::fetch($user_id);
            $xb = 2;
        }
        $limit = [
            'pageSize' => 99,
            'orderBy' => 'project_id',
            'sort' => 'DESC'
        ];
        $column = 'project_id, project_name';
        $project_data = ProjectClass::getList([], $limit, $column);

        $data['edit_data'] = $edit_data;
        $data['xb'] = $xb;
        $data['_id'] = $user_id;
        $data['project_data'] = json_encode($project_data['data']);
        return view('webi/backend/biuser/edit', $data);
    }

    /**
     * 保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {

        $args = $request->all();

        if (!$args['role_id']) {
            return response()->json(['code' => 10002, 'message' => '用户角色类型不能为空']);
        }
        if (!$args['user_id']) {
            return response()->json(['code' => 10002, 'message' => '成员不能为空']);
        }
        if (!$args['true_name']) {
            return response()->json(['code' => 10003, 'message' => '姓名不能为空']);
        }

        if ($args['_id']) {
            $where = [['user_id', "!=", $args['user_id']]];
            $column = 'project_id';
            $project_data = BiUserClass::getList($where, [], $column);

        } else {
            $where = ['user_id' => $args['user_id']];
            $column = 'project_id';
            $project_data = BiUserClass::getList($where, [], $column);

        }

        $Performance = BiUserClass::fetch($args['_id']);

        if (!$Performance) {
            //新增
            BiUserClass::save([
                'parent_user_id' => 0,
                'user_permission' => [1, 2],
                'role_id' => $args['role_id'],
                'role_bind' => $args['role_bind'],
                'user_id' => $args['user_id'],
                'true_name' => $args['role_id'],
                'project_id' => $args['project_id'],
                'mobile' => $args['mobile'],
                'email' => $args['email'],
                'project_name' => $args['project_name'],
                'useFlg' => 1
            ]);
        } else {
            //修改
            BiUserClass::save([
                'role_id' => $args['role_id'],
                'role_bind' => $args['role_bind'],
                'user_id' => $args['user_id'],
                'true_name' => $args['role_id'],
                'project_id' => $args['project_id'],
                'mobile' => $args['mobile'],
                'email' => $args['email'],
                'project_name' => $args['project_name'],
                'useFlg' => 1
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
            //删除用户
            BiUserClass::del($user_id);

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
        $user = BiUserClass::fetch($userId);

        if (!$user) {
            return response()->json(['code' => 100005, 'message' => '当前用户不存在']);
        }

        if ($user['user_pwd'] != md5($oldPwd)) {
            return response()->json(['code' => 100006, 'message' => '当前用户原密码输入错误']);
        }

        if ($newPwd == $oldPwd) {
            return response()->json(['code' => 100007, 'message' => '新的密码不能和原密码相同']);
        }

        BiUserClass::save([
            'user_pwd' => md5($newPwd)
        ], $userId);

        return response()->json(['code' => 200, 'message' => 'ok']);

    }

}