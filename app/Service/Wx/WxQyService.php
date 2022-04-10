<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 微信企业服务
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */
namespace App\Service\Wx;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Redis as Redis;

class WxQyService
{

    const api_url = 'https://qyapi.weixin.qq.com/cgi-bin';

    private $access_token;

    public function __construct()
    {

    }

    private function getAccessToken($secret_type = 'app')
    {

        $app_id = env('WX_QY_APP_ID');
        if ($secret_type == 'app') {
            $secret = env('WX_QY_APP_SECRET');
            $redis_key = 'wx:qy:app:access:token';
        } else {
            $secret = env('WX_QY_CONTACTS_SECRET');
            $redis_key = 'wx:qy:app:contacts:token';
        }

        //获取access_token
        $access_token = Redis::get($redis_key);

        if ( !$access_token ) {

            //实例化http请求类
            $client = new Client(['verify' => false]);

            //组装接口传参参数
            $http_data = [
                'corpid' => $app_id,
                'corpsecret' => $secret
            ];

            //调用企业微信获取access_token接口
            $http_result = $client->get(self::api_url . '/gettoken' . '?' . http_build_query($http_data));
            if ( $http_result->getStatusCode() != 200 ) {
                return [ 'code'=>$http_result->getStatusCode(), 'message'=>'接口调取失败'];
            }

            //获取接口内容
            $result =  $http_result->getBody()->getContents();
            $result = json_decode( $result, true );
            if ( $result['errcode'] != 0 ) {
                return ['code'=>$result['errcode'], 'message'=>$result['errmsg']];
            }

            //access_token值
            $access_token = $result['access_token'];

            Redis::setex($redis_key, 7000, $access_token);

        }

        return $access_token;

    }

    /**
     * 创建部门接口
     * @param array $http_data 部门数组
     * @return array
     */
    public function createDepartment($http_data)
    {

        // 实例化http请求类
        $client = new Client(['verify' => false]);

        // 调用创建部门接口
        $http_result = $client->post(self::api_url . '/department/create?access_token=' . $this->getAccessToken('contacts') , [
            'body' => json_encode($http_data)
        ]);
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10005 , 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10006 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return ['code' => 200 , 'message' => 'ok'];

    }

    /**
     * 更新部门接口
     * @param array $http_data
     * @return array
     */
    public function updateDepartment( $http_data )
    {
        //实例化http请求类
        $client = new Client(['verify' => false]);

        //调用更新部门接口
        $http_result = $client->post(self::api_url . '/department/update?access_token=' . $this->getAccessToken('contacts') , [
            'body' => json_encode($http_data)
        ]);
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10007, 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10008 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return [ 'code' => 200 , 'message' => 'ok' ];
    }

    /** 删除部门接口
     * @param $id
     * @return array
     */
    public function deleteDepartment( $id )
    {

        //实例化http请求类
        $client = new Client(['verify' => false]);

        // 获取删除部门接口
        $http_result = $client->get(self::api_url . '/department/delete?access_token=' . $this->getAccessToken('contacts') . '&id=' . $id );
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10002, 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10003, 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return ['code' => 200, 'message' => 'ok' ];

    }

    /**
     *  创建成员
     * @param array $http_data
     * @return array
     */
    public function createUser ( $http_data )
    {

        //实例化http请求类
        $client = new Client(['verify' => false]);

        // 创建部门成员接口
        $http_result = $client->post(self::api_url . '/user/create?access_token=' . $this->getAccessToken('contacts') ,[
            'body' => json_encode($http_data)
        ]);
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 20013, 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 20014 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return [ 'code' => 200 , 'message' => 'ok' ];

    }

    /**
     *  更新成员
     * @param array $http_data
     * @return array
     */
    public function updateUser ( $http_data )
    {

        //实例化http请求类
        $client = new Client(['verify' => false]);

        // 创建部门成员接口
        $http_result = $client->post(self::api_url . '/user/update?access_token=' . $this->getAccessToken('contacts') ,[
            'body' => json_encode($http_data)
        ] );
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 20015, 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 20016 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return [ 'code' => 200 , 'message' => 'ok' ];
    }

    /**
     * 删除部门成员
     * @param $userid
     * @return array
     */
    public function deleteUser ( $userid )
    {
        //组装接口传参参数
        $http_data = [
            'access_token' => $this->getAccessToken('contacts'),
            'userid' => $userid
        ];
        //实例化http请求类
        $client = new Client(['verify' => false]);

        // 删除成员接口
        $http_result = $client->get(self::api_url . '/user/delete?' . http_build_query($http_data));
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10002 , 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10003 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return ['code' => 200 , 'message' => 'ok'];
    }

    /**
     * 获取成员的openId
     * @param string $userid
     * @return array
     */
    public function getUserOpenid ( $userid )
    {

        //组装接口传参参数
        $http_data = [
            'userid' => $userid
        ];

        //实例化http请求类
        $client = new Client(['verify' => false]);

        // 获取成员的openId接口
        $http_result = $client->post(self::api_url . '/user/convert_to_openid?access_token=' . $this->getAccessToken('contacts') , ['body' => json_encode($http_data , true)]);
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10002 , 'message' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10003 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return [ 'code' => 200 , 'message' => 'ok' , 'data' => $result['openid'] ];

    }

    /**
     * 邀请成员
     * @param array $http_data
     * @return array
     */
    public  function inviteUser ( $http_data = [] )
    {

        // 实例化http请求类
        $client = new Client(['verify' => false]);

        // 邀请成员接口
        $http_result = $client->post(self::api_url . '/batch/invite?access_token=' . $this->getAccessToken('contacts') , [ 'body' => json_encode($http_data) ]);
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10001 , 'message' => '接口请求失败'];
        }

        // 获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10002 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

         return ['code' => 200, 'message' => 'ok'] ;

    }

    /**
     * 发送文本卡片消息
     * @param array $user 成员ID数组
     * @param array $party 部门ID数组
     * @param string $title 标题
     * @param string $description 描述
     * @param string $url 点击后跳转的链接
     * @param string $btn_txt 按钮文字
     * @return array|string
     */
    public function sendTextCard($title, $description, $url,  $user = [], $party = [], $btn_txt = '详情')
    {

        // 组装参数
        $http_data = [
            'touser' => implode('|', $user),
            'toparty' => implode('|', $party),
            'msgtype' => 'textcard',
            'agentid' => env('WX_QY_AGENT_ID'),
            'textcard' => [
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'btntxt' => $btn_txt
            ]
        ];

        // 实例化http请求类
        $client = new Client(['verify' => false]);

        // 发送文本卡片消息接口
        $http_result = $client->post(self::api_url . '/message/send?access_token=' . $this->getAccessToken() , [
            'body' => json_encode($http_data, true )
        ]);
        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'code' => 10001 , 'message' => '接口请求失败' ];
        }

        // 获取接口返回内容
        $result = $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        if ( $result['errcode'] != 0 ) {
            return ['code' => 10002 , 'message' => $result['errcode'] . ':' . $result['errmsg']];
        }

        return ['code' => 200 , 'message' => 'ok' , 'data' => $result];

    }

    /**
     * 获取授权用户信息
     * @param string $code 授权code
     * @return array|mixed|string
     */
    public function getOauthUser($code)
    {

        //实例化http请求类
        $client = new Client(['verify' => false]);

        $http_result = $client->get(self::api_url . '/user/getuserinfo?access_token=' . $this->getAccessToken() . '&code=' . $code);

        if ( $http_result->getStatusCode() != 200 ) {
            return [ 'errcode' => $http_result->getStatusCode(), 'errmsg' => '接口调取失败'];
        }

        //获取接口内容
        $result =  $http_result->getBody()->getContents();
        $result = json_decode( $result, true );
        return $result;

    }

}