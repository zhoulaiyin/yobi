<?php
namespace App\Service\Pay;

use Carbon\Carbon;

use App\Service\Pay\OnlinePayService;

class AliPayService extends OnlinePayService
{

    const gateway_url = 'https://openapi.alipay.com/gateway.do';

    const format = 'JSON';

    const version = '1.0';

    const charset = 'utf-8';

    const sign_type = 'RSA2';

    public function __construct($params)
    {
        parent::__construct($params);
    }

    /**
     * 支付请求
     * @return array
     */
    public function request()
    {

        $trade_type = $this->get('trade_type', 'NATIVE');
        $out_trade_no = $this->get('out_trade_no');

        if ($trade_type == 'NATIVE') { //PC
            $method = 'alipay.trade.page.pay';
            $product_code = 'FAST_INSTANT_TRADE_PAY';
        } else {
            $method = 'alipay.trade.wap.pay';
            $product_code = 'QUICK_WAP_PAY';
        }

        $http_data = [
            'app_id' => $this->get('app_id'),
            'method' => $method,
            'format' => self::format,
            'return_url' => $this->get('return_url'),
            'charset' => self::charset,
            'sign_type' => self::sign_type,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'version' => self::version,
            'notify_url' => $this->get('notify_url'),
            'biz_content' => json_encode([
                'product_code' => $product_code,
                'out_trade_no' => $out_trade_no,
                'total_amount' => $this->get('total_fee'),
                'subject' => $this->get('body'),
            ])
        ];

        //签名
        $sign = $this->rsaSign($http_data);
        if (false === $sign) {
            return ['code' => 20000, 'message' => '签名生成失败'];
        }
        $http_data['sign'] = $sign;

        $this->addLog($out_trade_no, $this->get('order_type'), $trade_type, 1, $http_data);

        $url = self::gateway_url . '?' . http_build_query($http_data);

        return ['code' => 200, 'message' => 'OK', 'data' => ['url' => $url]];


    }

    /**
     * 异步通知
     * @return array
     */
    public function notify()
    {

        $out_trade_no = $this->get('out_trade_no');
        if (empty($out_trade_no)) {
            return ['code' => 10000, 'message' => 'fail'];
        }

        if (!in_array($this->get('trade_status'), ['TRADE_FINISHED', 'TRADE_SUCCESS'])) {
            $this->updateLog($out_trade_no, 2, '支付失败');
            return ['code' => 10000, 'message' => 'fail'];
        }

        //检查签名
        $sign_check = $this->rsaVerify();
        if (!$sign_check) {
            $this->updateLog($out_trade_no, 2, '签名错误');
            return ['code' => 10000, 'message' => 'fail'];
        }

        $this->updateLog($out_trade_no, 3);

        return [
            'code' => 200,
            'message' => 'success',
            'data' => [
                'order_no' => $out_trade_no,
                'pay_code' => $this->get('trade_no'),
                'pay_time' => $this->get('gmt_payment'),
                'total_fee' => $this->get('total_amount')
            ]
        ];
    }
    

    /**
     * 生成签名字符串
     * @param array $params 签名参数
     * @return string
     */
    private function getSignContent($params) {

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

        return $sign_str;

    }

    /**
     * RSA签名
     * @param array $params 请求参数
     * @return bool|string
     */
    private function rsaSign($params)
    {

        //待签名字符串
        $sign_str = $this->getSignContent($params);

        //私钥文件地址
        $private_key_file = sprintf('%s/alipay/%s/rsa_private_key_pkcs8.pem', storage_path('cert'), $params['app_id']);
        if (!is_file($private_key_file)) {
            return false;
        }

        //获取私钥文件内容
        $pri_key = file_get_contents($private_key_file);

        //转换为openssl格式密钥
        $res = openssl_get_privatekey($pri_key);
        if (!$res) {
            return false;
        }

        //生成签名
        if ('RSA2' == $params['sign_type']) {
            openssl_sign($sign_str, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($sign_str, $sign, $res);
        }

        openssl_free_key($res);

        return base64_encode($sign);

    }

    /**
     * RSA验签
     * @return bool
     */
    private function rsaVerify()
    {

        $params = $this->getAll();
        $params['sign_type'] = null;

        $sign = base64_decode($params['sign']);

        //待签名字符串
        $sign_str = $this->getSignContent($params);

        //公钥文件地址
        $public_key_file = sprintf('%s/alipay/%s/rsa_alipay_public_key.pem', storage_path('cert'), $params['app_id']);
        if (!is_file($public_key_file)) {
            return false;
        }

        //获取公钥文件内容
        $public_key = file_get_contents($public_key_file);

        //转换为openssl格式密钥
        $res = openssl_get_publickey($public_key);
        if (!$res) {
            return false;
        }

        if ('RSA2' == self::sign_type) {
            $result = (bool)openssl_verify($sign_str, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($sign_str, $sign, $res);
        }

        openssl_free_key($res);
        
        return $result;

    }

}