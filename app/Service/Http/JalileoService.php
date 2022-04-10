<?php

namespace App\Service;

use GuzzleHttp\Client;

class JalileoService {

    const APPID = 'dcf7da0be34211e4882200163e00313a';

    const APPKEY = '5d6011fc3373d26cbc7916ff41878925';

    const REQUESTURL = 'http://galileo.ebsig.com/';

    /**
     * 接口请求方法(GET/POST)
     * @param string $url 接口地址
     * @param array $data 请求参数数组
     * @param string $mothod 请求方法
     * @return \Illuminate\Http\JsonResponse
     */
    public function request($url, $data, $mothod='get')
    {

        $client = new Client();

        $http_data = [
            'appId' => self::APPID,
            'params' => json_encode($data),
            'sign' => ''
        ];
        $http_data['sign'] = $this->create_sign($http_data);

        if ($mothod == 'get') {
            $response = $client->get(self::REQUESTURL . $url . '?' . http_build_query($http_data));
        } else {
            $response = $client->post(self::REQUESTURL . $url, ['form_params'=>$http_data]);
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
    private function create_sign($data)
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