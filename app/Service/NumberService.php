<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 数字加密解密
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */

namespace  App\Service;

class NumberService
{

    private $str = 'Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI';

    private $key, $length, $code_len,$code_num,$code_ext;

    function __construct($length = 15, $key = 2543.5415412812)
    {
        $this->key = $key;
        $this->length = $length;
        $this->code_len = substr($this->str, 0, $this->length);
        $this->code_num = substr($this->str, $this->length, 10);
        $this->code_ext = substr($this->str, $this->length + 10);
    }

    public function encode($num)
    {

        $rtn = '';

        $num_len = strlen($num);

        //密文第一位标记数字的长度
        $begin = substr($this->code_len, $num_len - 1,1);

        //密文的扩展位
        $ext_len = $this->length - $num_len - 1;
        $temp = str_replace('.', '', $num / $this->key);
        $temp = substr($temp, -$ext_len);
        $arr_ext_temp = str_split($this->code_ext);
        $arr_ext = str_split($temp);
        foreach ($arr_ext as $v) {
            $rtn .= $arr_ext_temp[$v];
        }
        $arr_num_temp = str_split($this->code_num);
        $arr_num = str_split($num);
        foreach ($arr_num as $v) {
            $rtn .= $arr_num_temp[$v];
        }

        return $begin . $rtn;

    }

    public function decode($code)
    {

        $begin = substr($code,0,1);
        $rtn = '';
        $len = strpos($this->code_len, $begin);
        if($len!== false){
            $len++;
            $arr_num = str_split(substr($code, -$len));
            foreach ($arr_num as $v) {
                $rtn .= strpos($this->code_num, $v);
            }
        }

        return $rtn;

    }

}