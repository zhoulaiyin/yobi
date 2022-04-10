<?php
/**
 * redis操作通用控制器
 * User: zhoulaiyin
 * Date: 2017/6/1
 * Time: 18:57
 1111
 */
namespace App\Http\Controllers;

use App\Http\Models\WeBI\BiMasterModule;
use App\Http\Models\WeBI\BiRule;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Log;
use App\Http\Controllers\Common\EbsigRedis;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis as Redis;
use App\Http\Models\ProjectData\bill\ProjectDateSaleBill;
use App\Http\Controllers\Wechat\wxApiController;
use App\Http\Controllers\WeBI\WeBIGlobalController;
use App\Http\Models\WeBI\DataSource;

class ZlyController extends Controller
{

    /**
     * 验证redis
     * @param $key
     * @param bool $global
     */
    public function freefunc(Request $request){

        DB::connection()->enableQueryLog();

        $biRule = BiMasterModule::find(4)->toArray();

        //Log::info('-->',DB::getQueryLog());

        echo 'sql-->'.print_r(DB::getQueryLog(), true);

        //echo 'char_json-->'.print_r(json_decode($biRule['chart_json'],true), true) . '<br><br>';
    }

    public function layui(){
        return view('zly/layui');
    }

    public function testPage(){
        return view('zly/testPage');
    }

    public function webiColor(){
        return view('webi/test/color');
    }

    public function vueJs(){
        return view('zly/vue');
    }

    public function selfSuit(){
        return view('test/freedrag');
    }

    public function testEcharts(){

        $dt = DB::table('stat_demo')->select()->limit(7)->get();

        return view('test/echarts', ['statdata' => json_encode($dt)]);
    }

}
