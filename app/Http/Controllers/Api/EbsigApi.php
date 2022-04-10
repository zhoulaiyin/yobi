<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoulaiyin <zhoulaiyin@ebsig.com>
// +----------------------------------------------------------------------

/**
 * EBSIG - api接口请求
 * @package  module/eai
 * @author   zhoulaiyin <zhoulaiyin@ebsig.com>
 * @version 1.0
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\EbsigHttp;

class EbsigApiEai extends Controller
{

    const APP_ID = 'dcf7da0be34211e4882200163e00313a';

    const APP_KEY = '5d6011fc3373d26cbc7916ff41878925';

    const GATEWAY = 'http://www.ebsig.com/api/';

    /**
     * 生成签名字符串
     * @param array $http_data http请求参数数组
     * @return string
     */
    private function createSignature($http_data){

        ksort($http_data);

        $sign_str = '';
        foreach ($http_data as $k => $v) {

            if ( is_string($v) && $v == '' ) {
                continue;
            }

            if ($sign_str == '') {
                $sign_str .= $k . '=' . $v;
            } else {
                $sign_str .= '&' . $k . '=' . $v;
            }

        }
        $sign_str .= 'key=' . self::APP_KEY;

        return strtoupper(md5($sign_str));

    }

    /**
     * 请求接口， 并保存日志
     * @param string $api_name 接口地址
     * @param array $http_data 业务参数数组
     * @param string $request_way 请求方式
     * @return array
     */
    public function request( $api_name, $http_data = array(), $request_way = 'post' ) {

        //实例http类
        $ebsigHttp = new ebsigHttp();

        //连接接口参数
        $http_opts = array(
            CURLOPT_TIMEOUT => 50,
            CURLOPT_CONNECTTIMEOUT => 10
        );

        $http_data['appId'] = self::APP_ID;
        $http_data['timestamp'] = time();

        //生成请求数组
        $http_data['sign'] = $this->createSignature($http_data);

        //请求接口
        if ($request_way == 'get') {
            $http_result_data = EbsigHttp::get(self::GATEWAY . $api_name . '?' . http_build_query($http_data), $http_opts);
        } else {
            $http_result_data = EbsigHttp::post(self::GATEWAY . $api_name, $http_data, $http_opts);
        }
      
        if ($http_result_data['code'] == 200) {
            return $http_result_data['data'];
        } else {
            return $http_result_data;
        }

    }

    /**
     * 发送短信
     * @param $args_data
     * @return array
     */
    public function sendMessage($args_data) {
        return $this->request('sms/send', $args_data, 'get');
    }


}
