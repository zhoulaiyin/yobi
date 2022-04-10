<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 提供基于PKCS7算法的加解密接口
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */
namespace App\Service\Wx;

class WxPKCS7Encoder
{

    const block_size = 32;

    private $key;

    function __construct($key)
    {
        $this->key = base64_decode($key . '=');
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @param string $app_id 应用id【公众号、小程序、企业号】
     * @return array
     */
    public function encrypt($text, $app_id)
    {

        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $text = $random.pack('N', strlen($text)) . $text. $app_id;
            // 网络字节序
            $iv = substr($this->key, 0, 16);
            //使用自定义的填充方式对明文进行补位填充
            $text = $this->encode($text);
            $encrypted = openssl_encrypt($text, 'aes-256-cbc', $this->key, $options= 1 | OPENSSL_NO_PADDING, $iv);
            return ['code' => 200, 'msg' => 'ok', 'data' => base64_encode($encrypted)];
        } catch (\Exception $e) {
            return ['code' => 40006, 'msg' => 'aes 加密失败'];
        }
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @param string $app_id 应用id【公众号、小程序、企业号】
     * @return array
     */
    public function decrypt($encrypted, $app_id)
    {

        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $iv = substr($this->key, 0, 16);
            $decrypted = openssl_decrypt($ciphertext_dec, 'aes-256-cbc', $this->key, $options = 1 | OPENSSL_NO_PADDING, $iv);
        } catch (\Exception $e) {
            return ['code' => 40007, 'msg' => 'aes 解密失败'];
        }

        try {
            //去除补位字符
            $result = $this->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return ['code' => 40008, 'msg' => '解密后得到的buffer非法'];
            }
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_app_id = substr($content, $xml_len + 4);
        } catch (\Exception $e) {
            return ['code' => 40008, 'msg' => '解密后得到的buffer非法'];
        }
        if ($from_app_id != $app_id) {
            return ['code' => 40005, 'msg' => 'app_id 校验错误'];
        }

        return ['code' => 200, 'msg' => 'ok', 'data' => $xml_content];

    }


    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    private function getRandomStr()
    {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }


    /**
     * 对需要加密的明文进行填充补位
     * @param string $text 需要进行填充补位操作的明文
     * @return string 补齐明文字符串
     */
    private function encode($text)
    {
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = self::block_size - ($text_length % self::block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = self::block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param string decrypted 解密后的明文
     * @return string 删除填充补位后的明文
     */
    private function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > self::block_size) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}