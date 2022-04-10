<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Models\WeBI\BiMasterModule;

class TestController extends Controller
{
    public function changeData() {

        $flg = 1;

        switch ( $flg ){
            case 1:
            case 2:  //1和2处理逻辑一致
                break;

            case 3:
                break;
            default:

        }

        //echo print_r($all_data,true);

        $all_data = BiMasterModule::get()->toArray();
        foreach ( $all_data as $d ){
            $db = json_decode($d['db_json'],true);
            $BiMasterModule = BiMasterModule::find($d['module_id']);
            $BiMasterModule->view_id = empty($db['view_id']) ? 0 : $db['view_id'];
            $BiMasterModule->save();
        }

    }

    public function excelExport(){
        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];

        $TestData = [
            ['学号','姓名','成绩']
        ];
        for( $i=1; $i<=10000; $i++){
            $TestData[] = [
                '100'.$i,'姓名'.$i,mt_rand(15,99)
            ];
        }

        Excel::create('学生成绩',function($excel) use ($TestData){
            $excel->sheet('score', function($sheet) use ($TestData){
                $sheet->rows($TestData);
            });
        })->export('xlsx');
    }

    public function excelImport() {

        //$filePath = 'public/uploads/file/'.iconv('UTF-8', 'GBK', '学生成绩').'.xlsx';
        $filePath = 'public/uploads/file/10000.xlsx';

        echo '导入文件<br><br>';

        echo microtime(true).'--开始<br><br>';

        $reader = Excel::load($filePath);//要开始导入文件，可以使用->load($filename)。回调是可选的。
        $reader = $reader->getSheet(0);//得到Excel的第一页内容
        $fileData = $reader->toArray();

        echo microtime(true).'--结束<br><br>';

        echo print_r($fileData,true);

    }

    public function import(){

        $filePath = 'public/uploads/file/2000.xlsx';
        $reader = Excel::load($filePath);
        $reader = $reader->getSheet(0);
        $fileData = $reader->toArray();
        $dataLength = count($fileData);
        $pageNum = ceil($dataLength/1000);

        $user_id = 100;

        $table_name = 'excel_'.$user_id.'_'.time();

        $create_table_sql = '';

        for($i = 1;$i<count($res);$i++){
            $check = Students::where('name',$res[$i][0])->where('title',$res[$i][4])->count();
            if($check){
                continue;
            }
            $stu = new Students;
            $stu->name = $res[$i][0];
            $stu->group = $res[$i][1];
            $stu->teacher = $res[$i][2];
            $stu->school = $res[$i][3];
            $stu->mobile = $res[$i][4];
            $stu->title = $res[$i][5];
            $stu->save();
        }

    }

    public function getSql() {
        DB::connection()->enableQueryLog();

        $biRule = BiMasterModule::find(4)->toArray();

        echo 'sql-->'.print_r(DB::getQueryLog(), true);
    }

    public function conn_mysql() {

        $conn = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), 3306);
        if (!$conn) {
            die('链接错误：'.mysqli_connect_error());
        }

        mysqli_query($conn,'set names utf8');

        $sql = 'SELECT source_id,bi_user_id,bi_user_id,group_id FROM data_source';

        $get_data_result = mysqli_query($conn, $sql);
        if( !empty($get_data_result) ){
            while ($dt = mysqli_fetch_assoc($get_data_result)) {
                echo '$dt-->'.print_r($dt,true) . '<br><br>';
            }
        }

        error_log('链接错误：'.$conn->error);

        echo '$get_data_result-->'.print_r($get_data_result,true) . '<br><br>';
    }

    public function dbTest(){

        $data = array(
            'project_id' => 10010,
            'project_name' => '微电汇O2O',
            'cal_date' => '2017-08-15',
            'sale_money' => 98765.97,
            'trade_amount' => 5,
            'online_pay_money' => 5,
            'offline_pay_money' => 5,
            'refund_amount' => 5,
            'return_amount' => 5
        );

        DB::connection()->enableQueryLog();
        $ProjectDateSaleBill = ProjectDateSaleBill::where(
            [
                [ 'project_id','=',$data['project_id'] ],
                [ 'cal_date','=',$data['cal_date'] ]
            ]
        )->first();

        $ddd = DB::getQueryLog();

        echo print_r($ddd,true).'<br><br>';

        if (!$ProjectDateSaleBill) {
            $ProjectDateSaleBill = new ProjectDateSaleBill;
            $ProjectDateSaleBill->creator = 'testphp';
            $ProjectDateSaleBill->created_at = Carbon::now();
            $ProjectDateSaleBill->project_id = $data['project_id'];
            $ProjectDateSaleBill->cal_date = $data['cal_date'];
            $ProjectDateSaleBill->project_name = $data['project_name'];
        }

        $ProjectDateSaleBill->updated_at = Carbon::now();
        $ProjectDateSaleBill->sale_money = $data['sale_money'];
        $ProjectDateSaleBill->trade_amount = $data['trade_amount'];
        $ProjectDateSaleBill->online_pay_money = $data['online_pay_money'];
        $ProjectDateSaleBill->offline_pay_money = $data['offline_pay_money'];
        $ProjectDateSaleBill->refund_amount = $data['refund_amount'];
        $ProjectDateSaleBill->return_amount = $data['return_amount'];

        $ProjectDateSaleBill->save();

        echo 'over';

    }

    public function testListData(Request $request){
        $args = $request->all();

        $page = empty($args['page'])?1:$args['page'];
        $limit = empty($args['limit'])?20:$args['limit'];

        $count = DB::table('stat_cust_consume')->count();

        $result = DB::table('stat_cust_consume')
            ->select('cal_date','pcustID', 'custID', 'total_amount', 'total_money', 'cust_unit_price')
            ->orderBy('pcustID', 'desc')
            ->offset(($page-1)*$limit)
            ->limit($limit)
            ->get();

        return response()->json([ 'code' => 0, 'count' => $count , 'data'=>$result ]);
    }

    public function delWebiRedis(Request $request){

        $args = $request->all();

        if( !empty($args['view_id']) && is_numeric($args['view_id']) ){
            Redis::del('G_BI_VIEW_TABLE_SQL_'.$args['view_id']);
            Redis::del('G_BI_VIEW_TABLE_'.$args['view_id']);
            echo '删除成功';
        } else if ( !empty($args['view_id']) && $args['view_id'] == 'all' ){
            $view_list = DB::table('bi_view')->select('view_id')->get();
            if( !empty($view_list) ){
                foreach ( $view_list as $list ){
                    Redis::del('G_BI_VIEW_TABLE_SQL_'.$list['view_id']);
                    Redis::del('G_BI_VIEW_TABLE_'.$list['view_id']);
                    echo 'G_BI_VIEW_TABLE_SQL_'.$list['view_id'].'和'.'G_BI_VIEW_TABLE_'.$list['view_id'].'—删除成功<br><br>';
                }
            }
        }

    }

}