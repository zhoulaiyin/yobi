<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use Carbon\Carbon;

class ApiService
{

    private $api_name = '';
    private $request_data = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $this->api_name = $request->path();
        $this->request_data = $request->all();

        if ($this->apiKeyCheck()) {
            return $next($request);
        }

        if (!isset($request_data['appId']) || empty($request_data['appId'])) {
            $return_data = [
                'code' => 100000 ,
                'message' => '缺少参数：appId'
            ];
            $this->save_log($return_data);
            return response()->json($return_data);
        }

        if (!isset($request_data['sign']) || empty($request_data['sign'])) {
            $return_data = [
                'code' => 100000 ,
                'message' => '缺少参数：sign'
            ];
            $this->save_log($return_data);
            return response()->json($return_data);
        }

        $api_data = [
            'dcf7da0be34211e4882200163e00313a' => '5d6011fc3373d26cbc7916ff41878925',
            '7000001' => '5d6011fc3373d26cbc7916ff41878925'
        ];

        if (!isset($api_data[$request_data['appId']])) {
            $return_data = [
                'code' => 100000 ,
                'message' => 'appId参数错误'
            ];
            $this->save_log($return_data);
            return response()->json($return_data);
        }

        //生成签名
        $sign = $this->createSign($request_data, $api_data[$request_data['appId']]);
     
        if ($sign != $request_data['sign']) {
            $return_data = [
                'code' => 100001 ,
                'message' => '数据加密验签失败，服务器签名：' . $sign
            ];
            $this->save_log($return_data);
            return response()->json($return_data);
        }

        $response = $next($request);
     
        $this->save_log($response->getData(true));

        return $response;

    }

    private function createSign($data, $key) {

        ksort($data);

        $sign_str = '';
        foreach ($data as $k => $v) {
            if ($v === '') {
                continue;
            }
            if ($k == 'sign' || $k == '_url' || $k == 'file' || $k == 'fileKey') {
                continue;
            }
            if ($sign_str == '') {
                $sign_str .= $k . '=' . $v;
            } else {
                $sign_str .= '&' . $k . '=' . $v;
            }

        }
        $sign_str .= 'key=' . $key;
        return strtoupper(md5($sign_str));

    }

    /**
     * 添加接口请求日志
     * @param string $api_name 接口名称
     * @param array $request_data 请求数据数组
     * @return mixed
     */
    private function save_log($return_data=array()){
        DB::table('api_log')->insertGetId([
            'update_at' => Carbon::now(),
            'creator' => 'system',
            'create_at' => Carbon::now(),
            'api_name' => $this->api_name,
            'request_data' => print_r($this->request_data, true),
            'return_data' => print_r($return_data, true)
        ]);
    }

    /**
     * 验证内部测试密钥
     * @param $request
     * @return bool
     */
    private function apiKeyCheck() {

        if (!isset($this->request_data['e_secret_key']) || empty($this->request_data['e_secret_key'])) {
            return false;
        }
        $api_key = file_get_contents('http://www.ebsig.net/key/api');
        $api_key = json_decode($api_key);
        if ($this->request_data['e_secret_key'] != $api_key) {
            return false;
        }
        return true;

    }

}