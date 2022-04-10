<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReleaseController extends Controller
{

    public function release(Request $request)
    {

        $revision = $request->input('revision');
        $ips = $request->input('ips');

        if (empty($revision)) {
            return response()->json(['code' => 100001, 'message' => 'revision 参数设置错误']);
        }

        if (empty($ips)) {
            return response()->json(['code' => 100002, 'message' => 'ips 参数设置错误']);
        }

        $ip_arr = preg_split("/[\s,]+/", $ips);
        foreach ($ip_arr as &$ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                return response()->json(['code' => 100003, 'message' => 'IP错误 : ' . $ip]);
            }
        }

        //shell文件地址
        $release_cli_cmd = storage_path('app/shell/release_cli.sh');

        if (!is_file($release_cli_cmd)) {
            return response()->json(['code' => 100004, 'message' => 'shell脚本文本不存在']);
        }

        if (!is_executable($release_cli_cmd)) {
            $chmod = chmod($release_cli_cmd, 500);
            if (!$chmod) {
                return response()->json(['code' => 100004, 'message' => 'shell脚本权限不正确，无法执行']);
            }
        }


        $release_cli_cmd .= ' -r ' . $revision . ' -p ' . $ips;

        exec($release_cli_cmd, $show_info, $ret);

        $release_file = array();
        if ($ret == 0) {
            $sync_result = true;
            foreach ($show_info as $text) {
                $release_result = explode('||', $text);
                $server_ip = $release_result[0];
                if (count($release_result) > 1) {
                    $release_file_list = explode(" ", $release_result[1]);
                    $release_file[$server_ip] = $release_file_list;
                } else {
                    $release_file[$server_ip] = array();
                    $sync_result = false;
                }
            }
            if ($sync_result) {
                return response()->json(['code' => 200, 'message' => 'OK', 'data' => $release_file]);
            } else {
                return response()->json(['code' => 100006, 'message' => '部分文件可能未发布成功，请核查', 'data' => $release_file]);
            }
        } else {
            return response()->json(['code' => 100005, 'message' => '错误信息：' . implode(',', $show_info)]);
        }


        $data = [
            'revision' => $request->input('revision'),
            'ips' => $request->input('ips'),
        ];

        return response()->json(['code' => 1000, 'message' => $data]);
    }

}
