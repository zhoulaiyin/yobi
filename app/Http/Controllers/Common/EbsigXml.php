<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

class EbsigXml extends Controller
{

    private $encoding; //XML 对象的编码

    private $version; //XML 版本号

    public function __construct($encoding = 'UTF-8', $version = '1.0')
    {
        $this->encoding = $encoding;
        $this->version = $version;
    }

    /**
     * 解析xml字符串为数组
     * @param string $xml_string xml字符串
     * @return mixed|null
     */
    public function decode($xml_string)
    {

        if (!isset($xml_string) || empty($xml_string)) {
            return null;
        }

        if ($this->encoding != 'UTF-8') {
            $xml_string = iconv($this->encoding, 'UTF-8', $xml_string);
        }

        $xml_array = (array)simplexml_load_string($xml_string, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml_array) {
            return null;
        }
        foreach ($xml_array as &$value) {
            if (is_object($value)) {
                $value = (array)$value;
            }
        }
        return $xml_array;

    }

    /**
     * 把数组转换成xml格式
     * @param array $xmlArray
     * @param int $isXmlHeader 是否显示xml头部
     * @return int|mixed|string
     */
    public function encode($xmlArray, $isXmlHeader = 0)
    {

        if ($isXmlHeader) {
            $xml = '<?xml version="' . $this->version . '" encoding="' . $this->encoding . '"?>';
        } else {
            $xml = '';
        }
        $xml .= $this->arrayToXml($xmlArray);
        return $xml;

    }

    public function arrayToXml($xmlArray)
    {

        $xml = '';
        foreach ($xmlArray as $key => $val) {
            if (!is_numeric($key)) {
                $xml .= "<$key>";
            }
            if (is_array($val)) {
                $xml .= $this->arrayToXml($val);
            } elseif (is_integer($val)) {
                $xml .= $val;
            } else {
                $xml .= '<![CDATA[' . $val . ']]>';
            }
            if (!is_numeric($key)) {
                $xml .= "</$key>";
            }
        }
        return $xml;

    }

}