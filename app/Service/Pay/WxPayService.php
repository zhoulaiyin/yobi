<?php
namespace App\Service\Pay;

use App\Models\PayType;

use App\Service\XmlService;

use App\Service\Pay\OnlinePayService;

class WxPayService extends OnlinePayService
{
    
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function request()
    {

        $xml = new XmlService();

        $out_trade_no = $this->get('out_trade_no');
        $trade_type = $this->get('trade_type');

        //设置参数
        $http_data = array (
            'appid' => $this->get('app_id'),
            'mch_id' => $this->get('mch_id'),
            'device_info' => $this->get('device_info'),
            'nonce_str' => uniqid(),
            'body' => $this->get('body'),
            'attach' => $trade_type,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $this->get('total_fee') * 100,
            'spbill_create_ip' => $this->get('ip'),
            'notify_url' => $this->get('notify_url'),
            'trade_type' => $trade_type
        );

        if ($trade_type == 'JSAPI') {
            $openId = $this->get('openId');
            if (empty($openId)) {
                return ['code' => 10001, 'message' => '缺少支付身份标识'];
            }
            $http_data['openid'] = $openId;
        } else if ($trade_type == 'NATIVE') {
            $http_data['product_id'] = $out_trade_no;
        } else {
            return ['code' => 10002, 'message' => '缺少交易类型'];
        }

        $http_data['sign'] = $this->createSign($http_data, $this->get('md5_key'));

        $response = $this->postRequest('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml->encode(['xml' => $http_data]));
        
        $http_data['unifiedorder'] = $response;
        $this->addLog($out_trade_no, $this->get('order_type'), $trade_type, 1, $http_data);
        
        if ($response['code'] != 200) {
            return $response;
        }

        $dt = $xml->decode($response['data']);

        if ($trade_type == 'JSAPI') {
            return ['code' => 200, 'message' => 'OK', 'data' => $dt];
        } else {
            return ['code' => 200, 'message' => 'OK', 'data' => [
                'url' => $this->get('native_url') . $dt['code_url']
            ]];
        }

    }

    /**
     * 回调参数检查
     * @return array
     */
    public function notify()
    {

        $xml = new XmlService();

        //微信返回参数
        $this->set($xml->decode(file_get_contents("php://input")));

        $out_trade_no = $this->get('out_trade_no');
        if (empty($out_trade_no)) {
            return ['code' => 10000, 'message' => $xml->encode( $this->returnMsg('订单号为空') )];
        }

        if ($this->get('return_code') != 'SUCCESS' || $this->get('result_code') != 'SUCCESS') {
            $this->updateLog($out_trade_no, 2, '支付失败');
            return ['code' => 10000, 'message' => $xml->encode( $this->returnMsg('支付失败') )];
        }

        //查询支付方式数据
        $pay_type = PayType::find(2);
        if (!$pay_type) {
            $this->updateLog($out_trade_no, 2, '支付方式不存在');
            return ['code' => 10001, 'message' => $xml->encode( $this->returnMsg('支付方式不存在') )];
        }
        $pay_params = json_decode($pay_type->extend_json, true);

        //检查签名
        $sign = $this->createSign($this->getAll(), $pay_params['md5_key']);
        if ($sign != $this->get('sign')) {
            $this->updateLog($out_trade_no, 2, '签名错误，签名值：' . $sign);
            return ['code' => 10003, 'message' => $xml->encode( $this->returnMsg('签名错误') )];
        }

        $this->updateLog($out_trade_no, 3);

        return [
            'code' => 200,
            'message' => $xml->encode( $this->returnMsg('OK', 'SUCCESS') ),
            'data' => [
                'order_no' => $out_trade_no,
                'pay_code' => $this->get('transaction_id'),
                'pay_time' => date('Y-m-d H:i:s', strtotime($this->get('time_end'))),
                'total_fee' => round($this->get('total_fee') / 100, 2)
            ]
        ];

    }

    /**
     * post请求
     * GuzzleHttp\Client库的post请求必须使用数组，微信支付post请求数据是xml格式
     * @param string $url 接口地址
     * @param string $data 请求数据
     * @param null $http_opts
     * @return array
     */
    public function postRequest( $url, $data, $http_opts = null ) {

        if (!isset($url) || empty($url)) {
            return array( 'code' => 400, 'message' => '缺少请求链接' );
        }
        if (!isset($data)) {
            return array( 'code' => 400 ,'message' => '缺少请求参数');
        }

        //解析链接，判断请求协议
        //$parse_url_array = parse_url($url);

        $curl_handler = curl_init();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HEADER	 => false,
            CURLOPT_POST => TRUE,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => $data
        );

        if (is_array($http_opts)) {
            foreach ($http_opts as $key => $value) {
                $options[$key] = $value;
            }
        }

        curl_setopt_array($curl_handler, $options);
        $curl_result = curl_exec($curl_handler); //获取URL站点内容 并打印出来
        $curl_http_status = curl_getinfo($curl_handler,CURLINFO_HTTP_CODE); //获取最后一次收到的HTTP代码
        $curl_http_info = curl_getinfo($curl_handler);

        if ($curl_result == false) {
            $error = curl_error($curl_handler);
            curl_close($curl_handler);
            return array('code' => $curl_http_status, 'message' => $error);
        }
        curl_close($curl_handler);

        $encode = mb_detect_encoding($curl_result, array('ASCII', 'UTF-8','GB2312', 'GBK', 'BIG5')); //进行编码识别
        if ($encode != 'UTF-8') {
            $curl_result = iconv($encode, 'UTF-8', $curl_result);
        }

        $result = json_decode($curl_result, true);

        if (is_null($result)) {
            $result = $curl_result;
        }

        return array('code' => $curl_http_status, 'message' => 'ok', 'data' => $result);

    }

    /**
     * 生成签名
     * @param array $params 签名参数
     * @param string $md5_key md5密钥
     * @return string
     */
    private function createSign($params, $md5_key) {

        ksort($params);

        $sign_str = '';
        foreach($params as $k => &$v) {
            if (empty($v) || in_array($k, ['sign', '_url'])) {
                continue;
            }
            if ($sign_str == '') {
                $sign_str .= $k . '=' . $v;
            } else {
                $sign_str .= '&' . $k . '=' . $v;
            }
        }
        $sign_str .= '&key=' . $md5_key;

        return strtoupper(md5($sign_str));

    }

    /**
     * 生成微信返回数据
     * @param string $msg 错误消息
     * @param string $code 错误代码
     * @return array
     */
    private function returnMsg($msg, $code='FAIL')
    {
        return ['xml' => [ 'return_code' => $code, 'return_msg' => $msg]];
    }

}