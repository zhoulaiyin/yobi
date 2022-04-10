<?php

namespace App\Http\Controllers\WeBI\shop;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Classes\Control\BiUserClass;
use Illuminate\Support\Facades\Redis as Redis;

class RegisterController extends Controller
{
    //注册页
    public function index (Request $request) {
        return view('webi/shop/register');
    }

    //登录操作
    public function register (Request $request) {
        global $WS;

        $mobile = $request->input('mobile', '');
        $username = $request->input('username', '');
        $true_name = $request->input('true_name', '');
        $sms_yzm = $request->input('sms_yzm', '');
        $yzm = $request->input('yzm', '');
        $user_pwd = $request->input('user_pwd', '');
        $again_pwd = $request->input('again_pwd', '');

        if (empty($username)) {
            return response()->json(['code' => 100001, 'message' => '请输入用户名']);
        }

        if (strlen($username) < 4 || strlen($username) >20) {
            return response()->json(['code' => 100001, 'message' => '用户名长度超出范围']);
        }

        if (empty($true_name)) {
            return response()->json(['code' => 100001, 'message' => '请输入姓名']);
        }

        if (empty($mobile)) {
            return response()->json(['code' => 100002,'message' => '请输入手机号']);
        }

        if (!isMobile($mobile)) {
            return response()->json(['code' => 100002,'message' => '手机号格式错误']);
        }

        if (empty($sms_yzm)) {
            return response()->json(['code' => 100002,'message' => '请输入短信验证码']);
        }

        if ($sms_yzm != '6789') {
            return response()->json(['code' => 100002,'message' => '短信验证码错误']);
        }

        if (empty($yzm)) {
            return response()->json(['code' => 100002,'message' => '请输入验证码']);
        }

        if (strtolower($yzm) != $WS->sessionGet('yzm' . session()->getId(), true)) {
            return response()->json(['code' => 100004, 'message' => '验证码错误']);
        }

        if (empty($user_pwd)) {
            return response()->json(['code' => 100002,'message' => '请输入密码']);
        }

        if (!is_pwd($user_pwd)) {
            return response()->json(['code' => 100003, 'message' => '密码格式不正确']);
        }

        if (empty($again_pwd)) {
            return response()->json(['code' => 100002,'message' => '请输入确认密码']);
        }

        if (trim($again_pwd) != trim($user_pwd)) {
            return response()->json(['code' => 100002,'message' => '两次密码不一致']);
        }

        //查询用户是否存在
        $user_data = BiUserClass::get(['user_id' => $username]);
        if (!empty($user_data)) {
            return response()->json(['code' => 100004, 'message' => '用户已存在']);
        }

        BiUserClass::save([
            'saas_client_id' => '',
            'saas_client_code' => '',
            'project_id' => '',
            'project_name' => '',
            'user_id' => $username,
            'true_name' => $true_name,
            'mobile' => $mobile,
            'user_pwd' => md5(trim($user_pwd)),
            'user_type' => 0,
            'group_id' => 0,
            'parent_user_id' => '0',
            'user_permission' => '',
            'role_id' => '',
            'role_bind' => '',
            'template_group_id' => '0',
            'head_pic' => '',
            'email' => '',
            'useFlg' => 1,
        ]);

        $redirect_url = '/webi/shop/login';
        if (!empty($request->input('redirect_url', ''))) {
            $redirect_url = $request->input('redirect_url');
        }

        return response()->json(array(
            'code' => 200,
            'message' => '注册成功',
            'data' => [
                'redirect_url' => $redirect_url
            ]
        ));
    }
}
