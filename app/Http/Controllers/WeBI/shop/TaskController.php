<?php

namespace App\Http\Controllers\WeBI\shop;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis as Redis;

class TaskController extends Controller
{

    public function delete () {

        //3天前日期
        $preDate   = date('Y-m-d',strtotime('-3 day'));
        $startDate = $preDate . " 00:00:00";
        $endDate  = $preDate . " 23:59:59";

        $user_deledt_sql = "DELETE FROM bi_user 
                            WHERE created_at 
                            BETWEEN '". $startDate ."' AND '". $endDate ."'";

        DB::delete($user_deledt_sql);

    }



}
