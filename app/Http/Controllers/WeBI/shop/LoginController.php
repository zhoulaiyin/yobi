<?php

namespace App\Http\Controllers\WeBI\shop;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Classes\Control\BiUserClass;
use Illuminate\Support\Facades\Redis as Redis;

class LoginController extends Controller
{
    //登录页
    public function index (Request $request) {
        global $WS;

        $redirect_url = $request->input('redirect_url');
        if ( empty($redirect_url) ) {
            $redirect_url = '/webi/list/index';
        }

        Redis::setex('G_WEBI_LOGIN_REDIRECT', 1800, $redirect_url);
		
		if($request->input('login')){  //演示账户直接登录

            $user_data = BiUserClass::get(['user_id' => '13688886666']);

            $LOGIN_SESSION['BI_USER_ID'] = $user_data['id'];
            $LOGIN_SESSION['USER_NAME'] = $user_data['user_id'];
            $LOGIN_SESSION['PROJECT_ID'] = empty($user_data['project_id']) ? null : $user_data['project_id'];
            $LOGIN_SESSION['MAIN_USER_ID'] = empty($user_data['parent_user_id']) ? $user_data['id'] : $user_data['parent_user_id'];
            $LOGIN_SESSION['USER_PERMISSION'] = [1,2];
            $WS->sessionSet('SHOP_LOGIN_SESSION', $LOGIN_SESSION, 86400*5);
        }

        //已经存在登录状态
        if($WS->isCustomerLogin){
            return redirect('/webi/list/index');
        }

        return view('webi/shop/login');
    }

    //登录操作
    public function login (Request $request) {
        global $WS;

        $login_name = $request->input('login_name', '');
        $password = $request->input('password', '');
        $yzm = $request->input('yzm', '');

        if (empty($login_name)) {
            return response()->json(array(
                'code' => 100001,
                'message' => '请输入账号'
            ));
        }

        if (empty($password)) {
            return response()->json(array(
                'code' => 100002,
                'message' => '请输入密码'
            ));
        }

        if (strtolower($yzm) != strtolower($WS->sessionGet('yzm' . session()->getId(), true))) {
            return response()->json(['code' => 100004, 'message' => '验证码错误']);
        }

        //查询用户是否存在
        $user_data = BiUserClass::get(['user_id' => $login_name]);
        if (empty($user_data)) {
            return response()->json(array(
                'code' => 100004,
                'message' => '用户不存在'
            ));
        }

        if ($user_data['user_pwd'] != md5($password)) {
            return response()->json(array(
                'code' => 100005,
                'message' => '密码不匹配',
                'data' => $user_data
            ));
        }

        //判断跳转是否允许
        $redirect_url = Redis::get('G_WEBI_LOGIN_REDIRECT');
        if (!$redirect_url) {
            $redirect_url = '/webi/list/index';
        }

        //跳转BI设计页
        if( strpos($redirect_url,'webi/design/edit') !== false ){
           $uuid =   $redirect_url[count(explode("/", $redirect_url))-1];

            $bi_master = DB::table('bi_master')->where( [ ['bi_user_id',$user_data['id']], ['uid',$uuid] ] )->first();
            if (empty($bi_master)) {
                $redirect_url = '/webi/list/index';
            }
        }

        //保存登录状态
        $LOGIN_SESSION['BI_USER_ID'] = $user_data['_id'];
        $LOGIN_SESSION['USER_NAME'] = $user_data['user_id'];
        $LOGIN_SESSION['PROJECT_ID'] = empty($user_data['project_id']) ? null : $user_data['project_id'];
        $LOGIN_SESSION['MAIN_USER_ID'] = empty($user_data['parent_user_id']) ? $user_data['_id'] : $user_data['parent_user_id'];
        $LOGIN_SESSION['USER_ROLE'] = $user_data['role_id'];
        $LOGIN_SESSION['USER_PERMISSION'] = empty($user_data['user_permission']) ? null : explode(',', $user_data['user_permission']);
        $WS->sessionSet('SHOP_LOGIN_SESSION', $LOGIN_SESSION, 86400*5);

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
        global $WS;
        $redirect_url = $request->input('redirect_url');
        $WS->sessionRemove('SHOP_LOGIN_SESSION');

        header('Location: /webi/shop/login?redirect_url=' . urlencode($redirect_url));
    }

    public function wapLogin(Request $request){
        global $WS;

        $redirect_url = $request->input('redirect_url');
        if ( empty($redirect_url) ) {
            $redirect_url = '/webi/wap/group/list';
        }

        Redis::setex('G_WEBI_LOGIN_REDIRECT', 1800, $redirect_url);

        //已经存在登录状态
        if($WS->isCustomerLogin){
            return redirect('/webi/wap/group/list');
        }

        return view('webi/wap/wapLogin');
    }

    public function wapLogout(Request $request){
        global $WS;

        $redirect_url = $request->input('redirect_url');
        $WS->sessionRemove('SHOP_LOGIN_SESSION');

        header('Location: /webi/wap/login?redirect_url=' . urlencode($redirect_url));
    }

    public function tvLogin(Request $request){
        global $WS;

        $redirect_url = $request->input('redirect_url');
        if ( empty($redirect_url) ) {
            $redirect_url = '/webi/tv/group/list';
        }

        Redis::setex('G_WEBI_LOGIN_REDIRECT', 1800, $redirect_url);

        //已经存在登录状态
        if($WS->isCustomerLogin){
            return redirect('/webi/tv/group/list');
        }

        return view('webi/TV/login');
    }

    public function tvLogout(Request $request){
        global $WS;

        $redirect_url = $request->input('redirect_url');
        $WS->sessionRemove('SHOP_LOGIN_SESSION');

        header('Location: /webi/tv/login?redirect_url=' . urlencode($redirect_url));
    }

}
