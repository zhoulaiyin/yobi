<?php

namespace App\Service\Http;

use GuzzleHttp\Client;

class StewardClientService {

    const APPID = 'dcf7da0be34211e4882200163e00313a';

    const APPKEY = '5d6011fc3373d26cbc7916ff41878925';

    const BASE = ['client_id'];

    /**
     * 接口请求方法(GET/POST)
     * @param string $url 接口地址
     * @param array $data 请求参数数组
     * @param string $mothod 请求方法
     * @return \Illuminate\Http\JsonResponse
     */
    static public function request($url, $data, $mothod='get')
    {

        $client = new Client();

        $http_data = [
            'appId' => self::APPID,
            'sign' => ''
        ];

        foreach($data as $key=>$val) {
            if (in_array($key, self::BASE)) {
                $http_data[$key] = $val;
                unset($data[$key]);
            }
        }

        $http_data['params'] = json_encode($data);
        $http_data['sign'] = self::create_sign($http_data);

        if ($mothod == 'get') {
            $response = $client->get($url . '?' . http_build_query($http_data));
        } else {
            $response = $client->post($url, ['form_params'=>$http_data]);
        }

        if ($response->getStatusCode() != 200) {
            return ['code'=>$response->getStatusCode(), 'message'=>'接口调取失败'];
        }

        $content =  $response->getBody()->getContents();
        $content = json_decode($content, true);
        if ($content['code'] != 200) {
            return ['code'=>$content['code'], 'message'=>$content['message']];
        }

        return $content;

    }

    /**
     * 生成参数签名
     * @param $data
     * @return string
     */
    static private function create_sign($data)
    {

        ksort($data);

        $sign_str = '';
        foreach ($data as $k => $v) {
            if ($v === '' || $k == 'sign' || $k == '_url' || $k == 'file' || $k == 'fileKey') {
                continue;
            }
            $sign_str .= $k . '=' . $v;
        }

        $sign_str .= self::APPKEY;
        return strtoupper(md5($sign_str));

    }

}