<?php

namespace App\Http\Controllers\WeBI\shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mockery\Exception;
use DB;

use App\Models\Control\BiUser;
use App\Models\Classes\Control\BiUserClass;

class BiUserController extends Controller
{
    public function index(){
        $data['is_admin'] = BiUserClass::isAdmin();

        return view('webi/shop/biuser/list',$data);
    }

    /**
     * 查询
     * @param Request $request
     * @return array
     */
    public function search(Request $request){
        global $WS;
        $where = [];

        if ($request->input('trueName')) {
            $where[] = ['true_name', 'like', '%' . $request->input('trueName') . '%'];
        }

        $sdk_data = BiUser::where($where)
            ->where(function ($query) use ($WS) {
                $query->where('_id'  , '=', $WS->mainUserID)
                    ->orwhere('parent_user_id', '=', $WS->mainUserID);
            })
            ->orderBy('id', 'asc')
            ->paginate((int)$request->input('limit'))
            ->toArray();

        $return_data = array(
            'code' => 0,
            'msg'  => '',
            'count'=>isset($sdk_data['total']) ? $sdk_data['total'] : 0,
            'data' =>array()
        );

        if ($sdk_data['total']>0) {
            $role_type = [0=>'',1=>'总部',2=>'分部',3=>'门店'];
            foreach ($sdk_data['data'] as $data) {

                $operation = '';

                if(BiUserClass::isAdmin()){
                    $operation .= '<a href="javascript: void(0);" class="layui-btn layui-btn-danger layui-btn-xs" onclick="stat.del(' . $data['_id'] . ')">删除</a>';
                    $operation .= '<a href="javascript: void(0);" class="layui-btn layui-btn-xs" onclick="stat.edit(' . $data['_id'] . ')">编辑</a>';
                    $operation .= '<a href="javascript: void(0);" class="layui-btn layui-btn-primary layui-btn-xs" onclick="stat.update(' . $data['_id'] . ')">修改密码</a>';

                    if(empty($data['parent_user_id'])){
                        $operation = '';
                    }

                    if(empty($data['parent_user_id']) && $WS->shopUserID == $WS->mainUserID){
                        $operation .= '<a href="javascript: void(0);" class="layui-btn layui-btn-xs" onclick="stat.edit(' . $data['_id'] . ')">编辑</a>';
                        $operation .= '<a href="javascript: void(0);" class="layui-btn layui-btn-primary layui-btn-xs" onclick="stat.update(' . $data['_id'] . ')">修改密码</a>';
                    }

                }

                $return_data['data'][] = array(
                    'user_id' =>$data['user_id'],
                    'true_name' => $data['true_name'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'role_id' => $role_type[$data['role_id']],
                    'role_bind' => $data['role_bind'],
                    'project_name' => $data['project_name'],
                    'operation' => $operation
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
    public function edit( $user_id  ){
        if(!BiUserClass::isAdmin()){
            return redirect('/error');
        }

        if (empty($user_id)) {
            $data['title'] = '新增用户信息';
        } else{
            $data['title'] = '编辑用户信息';
        }

        $edit_data = [];
        $user_permission = [];
        $role_bind = '';

        if ($user_id) {
            $edit_data = BiUserClass::fetch($user_id);

            if($edit_data && $edit_data['user_permission']){

                if(strlen($edit_data['user_permission'])>1){

                    foreach (explode(',',$edit_data['user_permission']) as $g){
                        array_push($user_permission,$g);
                    }
                }else{
                    $user_permission[0] = $edit_data['user_permission'];
                }
            }

            if($edit_data&&$edit_data['role_bind']){
                $role_bind = json_encode($edit_data['role_bind']);
            }
        }

        $data['role_bind'] = $role_bind;
        $data['edit_data'] = $edit_data;
        $data['user_permission'] = $user_permission;

        return view('webi/shop/biuser/edit',$data);
    }

    /**
     * 保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function save( Request $request ) {
        global $WS;

        $args = $request->all();

        if(!$args['role_id']){
            return response()->json(['code'=>10002, 'message'=>'用户角色类型不能为空']);
        }
        if(!$args['user_id']){
            return response()->json(['code'=>10002, 'message'=>'用户名不能为空']);
        }
        if(!$args['true_name']){
            return response()->json(['code'=>10003, 'message'=>'姓名不能为空']);
        }
        if (!in_array($args['role_id'], array('1', '2','3',1,2,3))) {
            return response()->json(['code' => 10001, 'message' => '参数错误']);
        }

        $user_permission = '';
        if (!empty($args['user_permission'])) {

            foreach ($args['user_permission'] as $g){
                if (!in_array($g, array('1', '2',1,2))) {
                    return response()->json(['code' => 10001, 'message' => '参数错误']);
                }
            }

            if(count($args['user_permission'])>=1){
                foreach ($args['user_permission'] as $g){
                    $user_permission .= $g.',';
                }
            }else{
                $user_permission = $args['user_permission'][0];
            }
        }

        $Webiuser = BiUserClass::fetch($args['uid']);
        $saveData = [];

        if (!$Webiuser) {
            $saveData['parent_user_id'] = $WS->mainUserID;
            $saveData['useFlg'] = 1;
        }

        $saveData['role_id'] = $args['role_id'];
        $saveData['user_id'] = $args['user_id'];
        $saveData['true_name'] = $args['true_name'];
        $saveData['mobile'] = $args['mobile'];
        $saveData['email'] = $args['email'];
        $saveData['user_permission'] = strlen($user_permission)>1 ? substr($user_permission,0,-1) : $user_permission;

        if(!empty($args['user_pwd'])){
            $saveData['user_pwd'] = md5($args['user_pwd']);
        }

        if(!empty($args['role_bind'])){
            $saveData['role_bind'] = $args['role_bind'];
        }

        $result = BiUserClass::save($saveData, !$Webiuser ? '' : $args['uid']);

        return response()->json($result);
    }

    /**
     * 删除用户
     * @param string $uuid 表唯一字段
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete( $user_id )
    {
        $user = BiUserClass::fetch($user_id);
        if (!$user) {
            return response()->json(['code' => 100000, 'message' => '用户信息没有找到']);
        }

        if(empty($user->parent_user_id)){
            return response()->json(['code' => 100000, 'message' => '父级账号不允许删除']);
        }

        $result = BiUserClass::del($user_id);

        return response()->json($result);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editPwd(Request $request) {
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

        BiUserClass::save(['user_pwd' => md5($newPwd)], $userId);

        return response()->json(['code' => 200, 'message' => 'ok']);
    }

}