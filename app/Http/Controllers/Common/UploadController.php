<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{

    public function index()
    {
        return view('test');
    }

    //定义允许上传的文件扩展名
    const EXT_ARR = [
        'photo' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
        'media' => ['swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
        'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'msi', 'exe'],
    ];

    /**
     * 允许的上传操作
     * 1. key为上传的文件目录
     * 2. value为允许上传的文件类型
     */
    const ACTION = [
        'chart/photo' => [ //图表维护上传图片
            'type' => ['photo'],
            'type_error' => '当前操作只允许上传图片',
            'max_size' => 1,  //单位M
            'max_size_error' => '上传图片大小不能大于1M'
        ],
        'template/photo' => [ //图表维护上传图片
            'type' => ['photo'],
            'type_error' => '当前操作只允许上传图片',
            'max_size' => 1,  //单位M
            'max_size_error' => '上传图片大小不能大于1M'
        ],
        'webi' => [ //webi上传图片
            'type' => ['photo'],
            'type_error' => '当前操作只允许上传图片',
            'max_size' => 1,  //单位M
            'max_size_error' => '上传图片大小不能大于1M'
        ],
        'document' => [ //document上传图片
            'type' => ['photo'],
            'type_error' => '当前操作只允许上传图片',
            'max_size' => 3,  //单位M
            'max_size_error' => '上传图片大小不能大于3M'
        ],

    ];

    public function upload(Request $request)
    {

        //获取上传文件
        $file = $request->file('file');

        //检查上传操作
        $action = $request->input('action');


        if (!$action || !isset($this::ACTION[$action])) {
            return ['code' => 1000002, 'message' => '上传参数错误'];
        }

        $action_data = $this::ACTION[$action];
        //检查上传错误
        $upload_error_code = $file->getError();

        if (!empty($upload_error_code)) {
            switch ($upload_error_code) {
                case 1:
                    $error = '超过允许上传的大小。';   // 配置项
                    break;
                case 2:
                    $error = '超过表单允许上传的大小';   // 表单设置
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
            return response()->json(['code' => 1000002, 'message' => $error]);
        }

        //文件上传成功
        if ($file->isValid()) {

            //文件名
            $file_name = $file->getClientOriginalName();

            //获得文件扩展名
            $file_ext = $file->guessExtension();
            if (empty($file_ext)) {
                $file_ext = $file->getClientOriginalExtension();
            }

            //文件大小
            $file_size = $file->getClientSize();
            //检查文件名
            if (!$file_name) {
                return response()->json(['code' => 1000003, 'message' => '请选择文件']);
            }

            //检查文件扩展名是否允许上传，并获取上传文件类型
            $file_type = '';
            foreach ($this::EXT_ARR as $type => $file_ext_arr) {
                if (in_array($file_ext, $file_ext_arr)) {
                    $file_type = $type;
                }
            }
            if (!$file_type) {
                return response()->json(['code' => 1000004, 'message' => '扩展名是[' . $file_ext . ']的文件禁止上传']);
            }
            //  检查文件类型
            if (!in_array($file_type, $action_data['type'])) {
                return response()->json(['code' => 1000005, 'message' => $action_data['type_error']]);
            }

            //检查文件大小
            if ($file_size > $action_data['max_size'] * 1048576) {
                return response()->json(['code' => 1000006, 'message' => $action_data['max_size_error']]);
            }

            $directory = 'uploads/' . $file_type . '/' . $action;

            $new_file_name = date('YmdHis') . rand(10000, 99999) . '.' . $file_ext;

            $file->move($directory, $new_file_name);
            $data = array(
                'url' => '/' . $directory . '/' . $new_file_name,
                'name' => $file_name,
            );

            return response()->json(['code' => 200, 'data' => $data]);

        } else {

            return response()->json(['code' => 1000010, 'message' => '上传失败']);
        }

    }

    //layui上传图片
    public function uploadLayui(Request $request)
    {

        //获取上传文件
        $file = $request->file('file');
        //检查上传操作
        $action = $request->input('action');

        if (!$action || !isset($this::ACTION[$action])) {
            return ['code' => 1000001, 'msg' => '上传参数错误'];
        }
//
        $action_data = $this::ACTION[$action];
        //检查上传错误
        $upload_error_code = $file->getError();
        if (!empty($upload_error_code)) {
            switch ($upload_error_code) {
                case 1:
                    $error = '超过允许上传的大小。';   // 配置项
                    break;
                case 2:
                    $error = '超过表单允许上传的大小';   // 表单设置
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
            return response()->json(['code' => 1000002, 'msg' => $error]);
        }

        //文件上传成功
        if ($file->isValid()) {

            //文件名
            $file_name = $file->getClientOriginalName();

            //获得文件扩展名
            $file_ext = $file->guessExtension();

            //文件大小
            $file_size = $file->getClientSize();
            //检查文件名
            if (!$file_name) {
                return response()->json(['code' => 1000003, 'msg' => '请选择文件']);
            }

            //检查文件扩展名是否允许上传，并获取上传文件类型
            $file_type = '';
            foreach ($this::EXT_ARR as $type => $file_ext_arr) {
                if (in_array($file_ext, $file_ext_arr)) {
                    $file_type = $type;
                }
            }
            if (!$file_type) {
                return response()->json(['code' => 1000004, 'msg' => '扩展名是[' . $file_ext . ']的文件禁止上传']);
            }
            //  检查文件类型
            if (!in_array($file_type, $action_data['type'])) {
                return response()->json(['code' => 1000005, 'msg' => $action_data['type_error']]);
            }

            //检查文件大小
            if ($file_size > $action_data['max_size'] * 1048576) {
                return response()->json(['code' => 1000006, 'msg' => $action_data['max_size_error']]);
            }

            $directory = 'uploads/' . $file_type . '/' . $action;

            $new_file_name = date('YmdHis') . rand(10000, 99999) . '.' . $file_ext;

            $file->move($directory, $new_file_name);
            $data = array(
                'src' => '/' . $directory . '/' . $new_file_name,
                'title' => $file_name,
            );

            return response()->json(['code' => 0, 'data' => $data]);

        } else {

            return response()->json(['code' => 1000010, 'msg' => '上传失败']);
        }


    }

}
