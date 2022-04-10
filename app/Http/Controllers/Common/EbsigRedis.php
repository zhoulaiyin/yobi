<?php
/**
 * redis操作通用控制器
 * User: zhoulaiyin
 * Date: 2017/6/1
 * Time: 18:57
 */

namespace App\Http\Controllers\Common;

use Illuminate\Support\Facades\Redis as Redis;
use App\Http\Controllers\Controller;

class EbsigRedis extends Controller
{

    /**
     * 设置redis
     * @param $key
     * @param $value
     * @param $timeout
     * @param bool $global
     * @return mixed
     */
    public static function set($key, $value, $timeout, $global = false)
    {

        if ($global)
            $tmp_key = $key;
        else
            $tmp_key = session()->getId() . $key;

        $tmp = array('SC_TIMESTAMP' => time(), 'SC_TIMEOUT' => $timeout, 'SC_VALUE' => $value);
        if ($timeout == 0) {
            return Redis::set($key, json_encode($tmp));
        } else {
            return Redis::setex($key, $timeout, json_encode($tmp));
        }

    }

    /**
     * 获取redis
     * @param $key
     * @param bool $global
     */
    public static function get($key, $global = false)
    {

        if ($global)
            $tmp_key = $key;
        else
            $tmp_key = session()->getId() . $key;

        if ($tmp = json_decode(Redis::get($tmp_key), true)) {
            $sc_timestamp = $tmp['SC_TIMESTAMP'];
            $sc_timeout = $tmp['SC_TIMEOUT'];
            if (($sc_timeout == -1 || $sc_timeout == 0) || ($sc_timeout > 0 && time() - $sc_timestamp <= $sc_timeout))
                return $tmp['SC_VALUE'];
            else {  // time out
                Redis::del($tmp_key);
                return null;
            }
        } else
            return null;

    }

    /**
     * 删除redis
     * @param $key
     * @param bool $global
     */
    public static function remove($key, $global = false)
    {

        if ($global)
            $tmp_key = $key;
        else
            $tmp_key = session()->getId() . $key;

        Redis::del($tmp_key);
    }

}
