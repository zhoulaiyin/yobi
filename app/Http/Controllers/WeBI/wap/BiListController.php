<?php
namespace App\Http\Controllers\WeBI\wap;

use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis as Redis;

class BiListController extends Controller
{

    public function index() {

        $data = [];

        return view('webi/wap/biList',$data);

    }

  

}