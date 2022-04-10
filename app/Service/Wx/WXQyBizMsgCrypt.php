<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 微信企业消息加密解密服务
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */
namespace App\Service\Wx;

use App\Service\Wx\WxPKCS7Encoder;

class WXQyBizMsgCrypt
{

    private $token;

    private $encodingAesKey;

    private $app_id;

    public function __construct($token, $encodingAesKey, $app_id)
    {

        $this->token = $token;
        $this->encodingAesKey = $encodingAesKey;
        $this->app_id = $app_id;

    }

    /**
     * 验证URL
     * @param string $msgSignature: 签名串，对应URL参数的msg_signature
     * @param string $timeStamp: 时间戳，对应URL参数的timestamp
     * @param string $nonce: 随机串，对应URL参数的nonce
     * @param string $echoStr: 随机串，对应URL参数的echostr
     * @return array
     */
    public function verifyURL($msgSignature, $timeStamp, $nonce, $echoStr)
    {

        if (strlen($this->encodingAesKey) != 43) {
            return ['code' => 40004, 'msg' => 'encodingAesKey 非法'];
        }

        $signature = $this->getSign($this->token, $timeStamp, $nonce, $echoStr);
        if (empty($signature)) {
            return ['code' => 40003, 'msg' => 'sha加密生成签名失败'];
        }

        if ($signature != $msgSignature) {
            return ['code' => 40001, 'msg' => '签名验证错误'];
        }

        $WxPKCS7Encoder = new WxPKCS7Encoder($this->encodingAesKey);

        return $WxPKCS7Encoder->decrypt($echoStr, $this->app_id);

    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param string $msg_signature 签名串，对应URL参数的msg_signature
     * @param string $timeStamp 时间戳 对应URL参数的timestamp
     * @param string $nonce 随机串，对应URL参数的nonce
     * @param string $post_data 密文，对应POST请求的数据
     * @return array
     */
    public function decryptMsg($msg_signature, $timeStamp, $nonce, $post_data)
    {

        if (strlen($this->encodingAesKey) != 43) {
            return ['code' => 40004, 'msg' => 'encodingAesKey 非法'];
        }

        //提取密文
        $result = $this->extract($post_data);
        if ($result['code'] != 200) {
            return $result;
        }

        $encrypt = $result['data']['encrypt'];
        $to_username = $result['data']['to_username'];

        //验证安全签名
        $signature = $this->getSign($this->token, $timeStamp, $nonce, $encrypt);
        if (empty($signature)) {
            return ['code' => 40003, 'msg' => 'sha加密生成签名失败'];
        }
        if ($signature != $msg_signature) {
            return ['code' => 40001, 'msg' => '签名验证错误'];
        }

        $WxPKCS7Encoder = new WxPKCS7Encoder($this->encodingAesKey);

        return $WxPKCS7Encoder->decrypt($encrypt, $this->app_id);

    }

    /**
     * 提取出xml数据包中的加密消息
     * @param string $xml_text 待提取的xml字符串
     * @return array
     */
    public function extract($xml_text)
    {
        try {
            $xml = new \DOMDocument();
            $xml->loadXML($xml_text);
            $array_e = $xml->getElementsByTagName('Encrypt');
            $array_a = $xml->getElementsByTagName('ToUserName');
            $encrypt = $array_e->item(0)->nodeValue;
            $to_username = $array_a->item(0)->nodeValue;
            return ['code' => 200, 'msg' => 'ok', 'data' => [
                'encrypt' => $encrypt,
                'to_username' => $to_username,
            ]];
        } catch (\Exception $e) {
            return ['code' => 40002, 'msg' => 'xml解析失败'];
        }

    }


    /**
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt_msg 密文消息
     * @return string
     */
    private function getSign($token, $timestamp, $nonce, $encrypt_msg)
    {

        //排序
        try {
            $array = [$encrypt_msg, $token, $timestamp, $nonce];
            sort($array, SORT_STRING);
            $str = implode($array);
            return sha1($str);
        } catch (\Exception $e) {
            return null;
        }
    }

}