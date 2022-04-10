<?php

namespace App\Http\Controllers\Components;

use App\Models\System\User;

use Illuminate\Http\Request;

use App\Service\Wx\WxQyService;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;

class WxQyOAuthController extends Controller
{

    /**
     * 发送企业微信网页授权请求
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {

        $redirect_url = $request->input('redirect_url');

        $http_array = array(
            'appid' => env('WX_QY_APP_ID'),
            'redirect_uri' => 'http://www.ebsig.com/components/wx-qy/oauth/notify?redirect_url=' . $redirect_url,
            'response_type' => 'code',
            'scope' => 'snsapi_base',
            'state' => 'state'
        );

        $requestURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($http_array) . '#wechat_redirect';

        return redirect()->to($requestURL);

    }

    /**
     * 企业微信授权回调
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notify(Request $request)
    {

        $code = $request->input('code');
        if (empty($code)) {
            return '企业微信授权失败:code获取失败';
        }

        $redirect_url = $request->input('redirect_url');
        if (empty($redirect_url)) {
            $redirect_url = '/';
        }

        $wx_qy_service = new WxQyService();

        $wx_user = $wx_qy_service->getOauthUser($code);
        if ($wx_user['errcode'] != 0) {
            return '企业微信授权失败:' . $wx_user['errmsg'];
        }

        //查看user_id是否存在
        $user = User::where('user_id', $wx_user['UserId'])->first();
        if (!$user) {
            return '企业微信授权失败:该用户不存在';
        }

        //存储user_id至redis
        $session_id = session()->getId();
        Redis::setex('ADMIN_USER_ID' . $session_id, 86400, $user->user_id);

        return redirect()->to($redirect_url);

    }


}