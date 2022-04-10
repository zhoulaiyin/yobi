<?php

namespace App\Http\Controllers\Components;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

define('FFMPEG_CMD', 'ffmpeg -i "%s" 2>&1');

class UploadController extends Controller
{

    //定义允许上传的文件扩展名
    const EXT_ARR = [
        'photo' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
        'media' => ['swf', 'flv', 'mp3', 'mp4', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
        'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'csv'],
        'excel_file' => ['xls', 'xlsx']
    ];

    public function upload(Request $request)
    {
        $args = $request->all();

        $appid = $request->input('appid');
        $file = $request->file('file');

        $upload_error_code = $file->getError();

        if (!empty($upload_error_code)) {
            switch ($upload_error_code) {
                case 1:
                    $error = '超过允许上传的大小。';
                    break;
                case 2:
                    $error = '超过表单允许上传的大小';
                    break;
                case 3:
                    $error = '图片只有部分被上传';
                    break;
                case 4:
                    $error = '请选择图片';
                    break;
                case 5:
                    $error = '找不到临时目录';
                    break;
                case 6:
                    $error = '写文件到硬盘出错';
                    break;
                case 8:
                    $error = 'File upload stopped by extension';
                    break;
                case 999:
                default:
                    $error = '未知错误';
            }
            return response()->json([
                'code' => 1000002,
                'message' => $error
            ])->header('Content-Type', 'text/html');
        }

        if ($file->isValid()) {
            $file_name = $file->getClientOriginalName();
            if (!$file_name) {
                return response()->json([
                    'code' => 1000003,
                    'message' => '请选择文件'
                ])->header('Content-Type', 'text/html');
            }

            $file_ext = $file->getClientOriginalExtension();
            $file_type = '';
            foreach ($this::EXT_ARR as $type => $file_ext_arr) {
                if (in_array($file_ext, $file_ext_arr)) {
                    $file_type = $type;
                }
            }

            $directory = 'uploads/' . $appid . '/' . $file_type . '/' . date('Ymd');
            $new_file_name = date('YmdHis') . rand(10000, 99999) . '.' . $file_ext;

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
                chmod($directory, 0777);
            }

            if (!is_writable($directory)) {
                chmod($directory, 0777);
            }

            $file->move($directory, $new_file_name);

            $relative_path = '/' . $directory . '/' . $new_file_name;

            $type = empty($args['t']) ? 1 : $args['t'];
            $uid = empty($args['uid']) ? '' : $args['uid'];
            $sub_child = empty($args['sub_child']) ? '' : $args['sub_child'];
            $alias = empty($args['alias']) ? '' : $args['alias'];
            $itemIndex = !isset($args['itemIndex']) || $args['itemIndex'] == '' ? '' : $args['itemIndex'];

            //获取图片文件宽高
            $file_width = 0;
            $file_height = 0;
            if ($file_type == 'photo' && file_exists(public_path() . $relative_path)) {
                $img_info = getimagesize(public_path() . $relative_path);
                $sizeArr = explode(' ', $img_info[3]);
                $sizeArr[0] = str_replace('width=', '', $sizeArr[0]);
                $sizeArr[0] = str_replace('"', '', $sizeArr[0]);
                $sizeArr[0] = str_replace("'", '', $sizeArr[0]);
                $sizeArr[1] = str_replace('height=', '', $sizeArr[1]);
                $sizeArr[1] = str_replace('"', '', $sizeArr[1]);
                $sizeArr[1] = str_replace("'", '', $sizeArr[1]);
                $file_width = $sizeArr[0];
                $file_height = $sizeArr[1];
            }

            //获取视频宽高
            if ($file_type == 'media' && file_exists(public_path() . $relative_path)) {
                ob_start();
                passthru(sprintf(FFMPEG_CMD, public_path() . $relative_path));
                $video_info = ob_get_contents();
                ob_end_clean();

                if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $video_info, $match)) {
//                    $data['vcodec'] = $match[1]; //视频编码格式
//                    $data['vformat'] = $match[2]; //视频格式
//                    $data['resolution'] = $match[3]; //视频分辨率
                    $arr_resolution = explode('x', $match[3]);
                    $file_width = $arr_resolution[0];
                    $file_height = $arr_resolution[1];
                }
            }

            return response()->json([
                'code' => 200,
                'data' => [
                    'type' => $type,
                    'subfield_uid' => $uid,
                    'sub_child' => $sub_child,
                    'field_alias' => $alias,
                    'itemIndex' => $itemIndex,
                    'name' => $file_name,
                    'src' => $relative_path,
                    'file_type' => $file_type,
                    'file_size' => number_format($file->getClientSize() / 1024, 2, '.', ''),
                    'file_width' => $file_width,
                    'file_height' => $file_height
                ]
            ])->header('Content-Type', 'text/html');

        } else {
            return response()->json([
                'code' => 1000010,
                'message' => '上传失败'
            ])->header('Content-Type', 'text/html');
        }
    }

}
