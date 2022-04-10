<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis as Redis;

use App\Models\Classes\Console\System\UserClass;
use App\Models\Classes\Console\System\UserLoginClass;

class LoginController extends Controller
{
    //登录页
    public function index(Request $request)
    {

        $redirect_url = $request->input('redirect_url');
        if (empty($redirect_url)) {
            $redirect_url = '/admin';
        }
        Redis::setex('G_ETS_LOGIN_REDIRECT', 1800, $redirect_url);

        //已经存在登录状态
        if (isLogin()) {
            return redirect('/admin');
        }

        return view('admin/login');

    }

    //登录操作
    public function login(Request $request)
    {
        global $WS;

        $login_name = $request->input('login_name');
        $password = $request->input('password');
        $yzm = $request->input('yzm', '');

        if (!isset($login_name) || empty($login_name)) {
            return response()->json(array(
                'code' => 100001,
                'message' => '登录账号不能为空'
            ));
        }

        if (!isset($password) || empty($password)) {
            return response()->json(array(
                'code' => 100002,
                'message' => '登录密码不能为空'
            ));
        }

        if (isset($password) && !is_pwd($password)) {
            return response()->json(array(
                'code' => 100003,
                'message' => '登录密码格式不正确'
            ));
        }

        if (strtolower($yzm) != strtolower($WS->sessionGet('yzm' . session()->getId(), true))) {
            return response()->json(['code' => 100004, 'message' => '验证码错误']);
        }

        //查询用户是否存在
        $userResult = UserClass::getList(['user_id' => $login_name], ['pageSize' => 1]);
        if (!$userResult['count']) {
            return response()->json(array(
                'code' => 100004,
                'message' => '该用户不存在'
            ));
        }

        $user_data = $userResult['data'][0];

        if ($user_data['user_pwd'] != md5($password)) {
            return response()->json(array(
                'code' => 100005,
                'message' => '密码不正确'
            ));
        }

        //保存用户id到redis
        $session_id = session()->getId();

        $LOGIN_SESSION['USER_ID'] = $user_data['user_id'];
        $LOGIN_SESSION['USER_NAME'] = $user_data['true_name'];
        Redis::setex('ADMIN_LOGIN_SESSION' . $session_id, 86400 * 5, json_encode($LOGIN_SESSION));

        $redirect_url = Redis::get('G_ETS_LOGIN_REDIRECT');
        if (!$redirect_url) {
            $redirect_url = '/admin';
        }

        //添加登录日志 新增
        UserLoginClass::save([
            'creator' => $user_data['user_id'],
            'user_id' => $user_data['user_id'],
            'ip' => $request->ip(),
            'session_id' => $session_id
        ]);

        return response()->json(array(
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'redirect_url' => $redirect_url
            ]
        ));
    }

    //退出
    public function logout(Request $request)
    {
        $redirect_url = $request->input('redirect_url');
        Redis::del('ADMIN_LOGIN_SESSION' . session()->getId());
        Redis::del('WEBI_LOGIN_USER_ID' . session()->getId());
        header('Location: /admin/login?redirect_url=' . urlencode($redirect_url));
    }
}
