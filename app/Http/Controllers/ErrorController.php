<?php

/**
 * 错误页面控制器20200408
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes\PlatformApplicationClass;

class ErrorController extends Controller
{
    public function error(Request $request, $act = null)
    {

        if (is_null($act)) {
            $act = 'error';
        }

        $message = $request->input('message', '');
        if (empty($message)) {
            $message = $request->input('msg', '');
        }

        $type = $request->has('type') ? $request->input('type') : 1;

        if (!empty($_SERVER['HTTP_REFERER'])) {
            if(strpos($_SERVER['HTTP_REFERER'], '/application/') !== false){
                $type = 3;
            } elseif (strpos($_SERVER['HTTP_REFERER'], '/model/') !== false) {
                $type = 1;
            } elseif (strpos($_SERVER['HTTP_REFERER'], '/console') !== false) {
                $type = 2;
            } elseif (strpos($_SERVER['HTTP_REFERER'], '/sendbox') !== false) {
                $type = 4;
            }
        }

        $data = [
            'message' => $message,
            'type' => $type
        ];

        return view('errors/' . $act, $data);
    }


    /**
     * 404页面返回首页
     * @param Request $request
     */
    public function returnHome(Request $request){
        switch ($request->input('type')) {
            case 1: //建模
                header ( "location: /model/dashboard");
                break;
            case 3: //应用
                header ( "location: /application/dashboard");
                break;
            case 2: //管理
                header ( "location: /console");
                break;
            default: //沙箱
                header ( "location: /sendbox/dashboard");
                break;
        }
    }

}
