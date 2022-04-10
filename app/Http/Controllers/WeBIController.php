<?php
/**
 * redis操作通用控制器
 * User: zhoulaiyin
 * Date: 2017/6/1
 * Time: 18:57
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class WeBIController extends Controller
{

    public function index(){
        return view('zly/webiShop');
    }

}
