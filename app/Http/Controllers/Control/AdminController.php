<?php
/**
 * redis操作通用控制器
 * User: zhoulaiyin
 * Date: 2017/6/1
 * Time: 18:57
 */

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{

    public function main()
    {
        return view('webi/control/main');
    }

    public function index()
    {
        return view('webi/control/index');
    }

}
