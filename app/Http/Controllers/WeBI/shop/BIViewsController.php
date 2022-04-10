<?php

namespace App\Http\Controllers\WeBI\shop;

use App\Models\Classes\Control\Statement\BiMasterModuleClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Redis as Redis;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Classes\Control\ImportsClass;

use App\Models\Control\DataSource\BiView;
use App\Models\Control\DataSource\DataSource;
use App\Models\Classes\Control\BiUserClass;
use App\Models\Classes\Control\DataSource\DataSourceGroupClass;
use App\Models\Classes\Control\DataSource\DataSourceClass;
use App\Models\Classes\Control\DataSource\BiViewClass;
use App\Models\Classes\Console\BiRuleClass;
use App\Models\Classes\Control\DataSource\DataTableClass;

class BIViewsController extends Controller
{

    const ACTION = [
        'webi' => [ //图表维护上传图片
            'type' => ['xls','xlsx'],
            'type_error' => '当前操作只允许上传图片',
            'max_size' => 1,  //单位M
            'max_size_error' => '上传图片大小不能大于1M'
        ]
    ];

    //首页
    public function index() {
        global $WS;

        $BiUser = BiUserClass::fetch($WS->shopUserID);
        $user_permission = empty($BiUser['user_permission']) ? [] : explode(',', $BiUser['user_permission']);

        if (
            count($user_permission)
            && !empty(array_intersect([1,2], $user_permission))
        ) {
            return view('webi/shop/views');
        }

        return redirect('error/access');
    }

    //数据集列表
    public function  groupList($group_id,$source_id){
        global $WS;
        $data= [
            'view' => '',
            'view_id' =>''
        ];

        if($group_id != 1 && $source_id == 0){
            return response()->json(['code' => 100008,'message' => '未选择数据源' ]);
        }

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => $group_id]);
        if(empty($DataSourceGroup)){
            return response()->json(['code' => 100001,'message' => '无效请求' ]);
        }

        $where = [];
        $where[]=['group_id',$DataSourceGroup['_id']];
        $where[]=['bi_user_id',$WS->mainUserID];

        if($group_id != 1 &&  $source_id != 0){
            $where[]=['source_id', $source_id];
        }

        $BiView = BiViewClass::getList($where, ['pageSize' => 999], 'view_id,view_name');
        if($BiView['count']){
            $data['view'] =$BiView['data'];
            $data['view_id'] =$BiView['data'][0]['_id'];
        }

        return response()->json(['code' => 200,'message' => 'ok','data' => $data ]);
    }

    //新建、编辑数据集
    public function editGroup(Request $request) {
        global $WS;
        $view_id = $request->input('view_id');
        $view_name = $request->input('title');
        $data = [];

        if (!isset($view_name) || empty($view_name)  ) {
            return response()->json(array('code' => 100002,'message' => '数据集名称不能为空'));
        }

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => $request->input('group_id')]);
        if(empty($DataSourceGroup)){
            return response()->json(['code' => 100001,'message' => '数据集不存在' ]);
        }

        $projectID = $WS->projectID;

        //新增数据集
        if($view_id==0){
            $result = BiViewClass::save([
                'view_name' => $view_name,
                'bi_user_id' => $WS->mainUserID,
                'group_id' => $DataSourceGroup['_id'],
                'source_id' => $request->input('source_id'),
                'project_id'=> $projectID,
                'table_json' => '',
            ]);

            $data['id'] = $result['data'];
        }else{
            //编辑数据集
            $setMap = BiViewClass::fetch($view_id);
            if(!$setMap){
                return response()->json([ 'code' => 400 , 'message' => '该数据集不存在，请刷新页面！' ]);
            }

            BiViewClass::save([
                'view_name' => $view_name
            ], $view_id);

            $data['id'] = $view_id;
        }

        return response()->json([ 'code' => 200, 'message' => '保存成功','data'=> $data ]);
    }

    //删除数据集
    public function del($id){
        $groupSet = BiViewClass::fetch($id);
        if(!$groupSet){
            return response()->json([ 'code' => 10003 , 'message' => "该数据集不存在,请刷新页面后重试！"]);
        }

        BiViewClass::del($id);

        return response()->json([ 'code' => 200 , 'message' => "操作成功"]);
    }

    //搜索数据源
    public function search(Request $request){
        global $WS;
        $args = $request->all();

        if (empty($args['group_id'])) {
            return response()->json([ 'code' => 10001 , 'message' => "参数错误" ]);
        }

        if (!isset($args['source_id'])) {
            return response()->json([ 'code' => 10002 , 'message' => "参数错误" ]);
        }

        if (isset($args['table_name']) && empty($args['table_name'])) {
            return response()->json([ 'code' => 10001 , 'message' => "搜索内容不能为空" ]);
        }

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => $args['group_id']]);
        if(empty($DataSourceGroup)){
            return response()->json(['code' => 10001,'message' => '数据源分组不存在' ]);
        }

        if (!empty($args['table_name'])) {
            $args['table_name'] = trim($args['table_name']);
        }

        $data = [];
        $where_data = [];

        switch ($DataSourceGroup['group_classify']){
            case 1: //微电汇数据源
                if( !empty($args['table_name']) ){
                    $where_data[] = ['description', 'like', '%' . $args['table_name'] . '%'];
                }

                $ruleResult = BiRuleClass::getList($where_data, ['pageSize' => 999, 'orderBy' => 'table_id'], 'table_id,table_name,description');
                $data['data'] = $ruleResult['data'];
                break;

            case 2: //excel数据源
                    $where_data = [
                        ['bi_user_id',$WS->mainUserID],
                        ['source_id',$args['source_id']]
                    ];

                    if( !empty($args['table_name']) ){
                        $where_data[] = ['description', 'like', '%' . $args['table_name'] . '%'];
                    }

                    $dataTable = DataTableClass::getList($where_data, ['pageSize' => 999, 'orderBy' => 'table_id'], 'table_id,table_name,description');
                    $data['data'] = $dataTable['data'];
                break;

            case 3: //mysql数据源
                $data_source = DataSourceClass::fetch($args['source_id']);
                if(empty($data_source) ){
                    return response()->json([ 'code' => 10001 , 'message' => "没有找到数据源" ]);
                }

                $and_sql = '';
                if( !empty($args['table_name']) ){
                    $and_sql .= "TABLE_COMMENT LIKE '%".$args['table_name']."%'";
                }

                //分页查詢数据库下所有集合数量
                if( isset($args['page']) ){
                    $and_sql .= ' LIMIT  '. ( $args['page']-1 ) * $args['limit'] .','. $args['limit'];

                    $count_sql = "SELECT count(*) as count 
                                    FROM information_schema.tables
                                    WHERE table_schema='%s' AND table_type='base table' ";
                    $count_sql = sprintf($count_sql,$data_source['db_database']);
                    $count = DataSourceClass::conn_query($args['source_id'],$count_sql);

                    $data['count'] = $count['data'][0]['count'];
                }

                //分页查詢数据库下所有集合名称及描述
                $sql = "SELECT 0 AS table_id,TABLE_NAME AS table_name,TABLE_COMMENT AS description 
                        FROM information_schema.tables
                        WHERE table_schema='%s' AND table_type='base table' ".$and_sql ;
                $sql = sprintf($sql,$data_source['db_database']);
                $result_list = DataSourceClass::conn_query($args['source_id'],$sql);

                $data['data'] = $result_list['data'];
                break;

            default:
                return response()->json([ 'code' => 10004 , 'message' => "无效的请求" ]);
        }

        return response()->json([ 'code' => 200 , 'message' => "ok" , 'data'=>$data]);
    }

    //数据集下关联表
    public function linkList(Request $request){
        $args = $request->all();
        $data = [];

        $BiView= BiViewClass::fetch($args['view_id']);
        if( !$BiView ){
            return response()->json([ 'code' => 10004 , 'msg' => "该数据集不存在,请刷新页面后重试！"]);
        }

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => $args['group_id']]);
        if( empty($DataSourceGroup)){
            return response()->json(['code' => 10001,'message' => '无效请求' ]);
        }

        //存在链表
        if (empty($BiView['table_json'])){
            //分组下为空
            $data['table'] = "";
        } else {
            $data['table'] = json_decode($BiView['table_json'], true);
        }

        return response()->json([ 'code' => 200 ,'msg'=> 'ok','data' => $data]);
    }

    //数据集下关联表字段
    public function makeTableStructure(Request $request){
        $args = $request->all();

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => $args['group_id']]);
        if (empty($DataSourceGroup)) {
            return response()->json(['code' => 10001,'message' => '数据源类型不存在' ]);
        }

        $data_source = DataSourceClass::fetch($args['source_id']);
        if (empty($data_source)) {
            return response()->json([ 'code' => 10001 , 'message' => "没有找到数据源" ]);
        }

        $BiView= BiViewClass::fetch($args['view_id']);
        if (empty($BiView)) {
            return response()->json([ 'code' => 10004 , 'msg' => "该数据集不存在,请刷新页面~"]);
        }

        if (empty($args['table_name'])) {
            return response()->json(['code' => 10001,'message' => '无效的请求' ]);
        }

        $args['table_name'] = trim($args['table_name']);

        //查询将要链表的表名
        switch ($DataSourceGroup['group_classify']){
            case 1:
                $DataTable = BiRuleClass::get(['table_name' => $args['table_name']]);
                if (empty($DataTable)) {
                    return response()->json(['code' => 10001,'message' => '数据表不存在' ]);
                }

                $fields_json = empty($DataTable['fields_json']) ? [] : $DataTable['fields_json'];
                break;

            case 2:
            case 3:
                $DataTable = DataTableClass::get([
                    ['source_id',$BiView['source_id']],
                    ['table_name',$args['table_name']],
                ]);
                if (!empty($DataTable)) {
                    $fields_json = empty($DataTable['fields_json']) ? [] : $DataTable['fields_json'];
                } else {  //生成表并插入数据库
                    $fields_json =  BIViewsController::assembly($data_source['db_database'], $args['table_name'], $args['source_id']);
                }
                break;

            default:
                return response()->json([ 'code' => 10013 , 'msg' => "无效请求"]);
        }

        if(empty($fields_json)){
            return response()->json([ 'code' => 10013 , 'msg' => "请求失败，请重试~"]);
        }

        //获取数据集表序列
        $table_list = [];
        $selectTable = "";
        $view_table_json = empty($BiView['table_json']) ? [] : json_decode($BiView['table_json'], true);
        if( !empty($view_table_json) ){
            $i = 0;
            foreach ( $view_table_json as $uid => $d ){
                if( $d['table'] == $args['table_name'] ){
                    return response()->json(['code' => 10008, 'msg' => "页面已存在该表！"]);
                }

                $table_list[$d['table']] = $d['table_name'];

                if( $i == 0){
                  $selectTable = $d['table'];
                }

                $i++;
            }
        }

        switch ($DataSourceGroup['group_classify']){
            case 1:
                $selectFieldsJson = BiRuleClass::get(['table_name' => $selectTable]);
                break;

            case 2:
            case 3:
            $selectFieldsJson = DataTableClass::get([
                ['source_id',$BiView['source_id']],
                ['table_name',$args['table_name']],
            ]);
            break;

            default:
                return response()->json([ 'code' => 10013 , 'msg' => "无效请求"]);
        }

        $data = [];
        $data['tableL']['table'] = $table_list;
        $data['tableL']['fields'] = empty($selectFieldsJson['fields_json']) ? [] : $selectFieldsJson['fields_json'];
        $data['tableR']['table'] = array($args['table_name'], $DataTable['description']);
        $data['tableR']['fields'] = $fields_json;

        return response()->json([ 'code' => 200 ,'msg'=> 'ok','data' => $data]);
    }

    //展示关联表关联信息
    public function editLinked($view_id,$uuid){
        $data = [];
        $field=[];
        $fieldl="";
        $fieldr="";
        $fieldL="";
        $fieldR="";
        $tableL="";
        $tableR="";
        $allFieldR="";
        $allFieldL="";
        $innerType="";
        $selected="";

        $set=BiView::find($view_id);
        if(!$set){
            return response()->json([ 'code' => 10002 , 'msg' => "数据集不存在，请刷新页面重试"]);
        }

        if( !empty($set['table_json']) ){

            $table_json=json_decode($set['table_json'],true);

            foreach($table_json as $key => $val){

                if(!empty($val['join'])){
                    foreach($val['join'] as $k => $v){

                        if( $k == $uuid ){

                            $tableR=array($v[0],$table_json[$uuid]['table_name']);
                            $innerType=$v[1];
                            $field=$v[2];
                            $selected=$val['table'];

                            if( $set['group_id'] <= 1 ){  //微电汇数据源表
                                $tableFieldL = BiRule::select('table_name','description','fields_json')
                                    ->where('table_name',$v[0])
                                    ->first();
                            }else{  //Excel、SQL数据源表
                                $tableFieldL = DataTable::select('table_name','description','fields_json')
                                    ->where('table_name',$v[0])
                                    ->first();
                            }

                            $allFieldR= $tableFieldL['fields_json'];
                        }
                        if( $set['group_id'] <= 1 ) { //微电汇数据源表
                            $tableFieldR = BiRule::select('table_name', 'description', 'fields_json')
                                ->where('table_name', $val['table'])
                                ->first();
                        }else{  //Excel、SQL数据源表
                            $tableFieldR = DataTable::select('table_name', 'description', 'fields_json')
                                ->where('table_name', $val['table'])
                                ->first();
                        }

                        $allFieldL = $tableFieldR['fields_json'];
                    }
                }
                 $tableL[$val['table']] = $val['table_name'];
            }

            foreach($field as $keys => $vals){
                $to_parse=explode(':',$vals);
                $fieldL[]=$to_parse[0];
                $fieldR[]=$to_parse[1];
                $to_parse="";
            }

            $tableL = array_unique($tableL);
            unset($tableL[array_search($tableR,$tableL) ]);

            $data['tableL']['table']=$tableL;
            $data['tableL']['fields']=$fieldL;
            $data['tableL']['allFields']=$allFieldL;
            $data['tableL']['selected']=$selected;

            $data['tableR']['table']=$tableR;
            $data['tableR']['innerType']=$innerType;
            $data['tableR']['allFields']=$allFieldR;
            $data['tableR']['fields']=$fieldR;
        }

        return response()->json([ 'code' => 200 ,'msg'=>'ok' , 'data' => $data]);
    }

    //添加关联表信息
    public function addLinked(Request $request){
        global $WS;

        $data = [];
        $view_id = $request->input('view_id');
        $table_json = $request->input('table_json');

        $set=BiViewClass::fetch($view_id);
        if(!$set){
            return response()->json([ 'code' => 10002 , 'msg' => "数据集不存在，请刷新页面重试"]);
        }

        $data['table'] = $table_json;

        BiViewClass::save([
            'table_json' => json_encode($table_json)
        ], $view_id);

        $WS->sessionRemove('G_BI_VIEW_TABLE_SQL_'.$view_id, true);
        $WS->sessionRemove('G_BI_VIEW_TABLE_'.$view_id, true);

        return response()->json([ 'code' => 200 , 'msg' => "ok",'data'=>$data]);
    }

    //删除关联表信息
    public function delLinked(Request $request){
        $view_id=$request->input('view_id');
        $table_json=$request->input('table_json');

        $set=BiViewClass::fetch($view_id);
        if(!$set){
            return response()->json([ 'code' => 10002 , 'msg' => "数据集不存在，请刷新页面重试"]);
        }

        if(empty($set['table_json']) ){
            return response()->json([ 'code' => 10006 , 'msg' => "该数据集不存在链表关系"]);
        }

        if(strlen($table_json) < 3){
            $table_json="";
        }

        BiViewClass::save([
            'table_json' => $table_json
        ], $view_id);


        $set_view = BiViewClass::fetch($view_id);
        $data['table']= json_decode( $set_view['table_json'],true );

        return response()->json([ 'code' => 200 , 'msg' => "ok",'data'=>$data]);
    }

    //查询表中字段
    public function searchFields(Request $request){
        $table_name = $request->input('table_name');
        $group_id = $request->input('group_id');

        $DataSourceGroup = DataSourceGroupClass::fetch($group_id);
        if(empty($DataSourceGroup)){
            return response()->json(['code' => 100001,'message' => '数据源分组不存在' ]);
        }

        if($group_id == 1){
            $tableField=BiRuleClass::get(['table_name' => $table_name]);
        }else{
            $tableField=DataTableClass::get(['table_name' => $table_name]);
        }

        $data['fields'] = $tableField['fields_json'];

        return response()->json([ 'code' => 200 , 'msg' => "ok",'data'=>$data]);
    }

    public function download( Request $request ){
        $source_id = $request->input('source_id');

        $DataSource = DataSourceClass::fetch($source_id);
        if(empty($DataSource)){
            return response()->json([ 'code' => 400 , 'msg' => "没有找到此条数据源",'data'=>'']);
        }

        $data['file_path'] = '/'.$DataSource['file_path'];

        return response()->json([ 'code' => 200 , 'msg' => "ok",'data'=>$data]);
    }

    //上传excel
    public function upload(Request $request)
    {
        global $WS;

        //获取上传文件
        $file = $request->file('file');
        if( empty($file)){
            return response()->json(['code' => 10001, 'message' => '请上传文件']);
        }

        //检查上传操作
        $action = $request->input('action');
        $source_id = $request->input('source_id');

        if (!$action || !isset($this::ACTION[$action])) {
            return response()->json(['code' => 1000003, 'message' => '上传参数错误']);
        }

        $file_size = $_FILES["file"]["size"];
        $file_size = getRealSize($file_size);//转换文件字节单位

        $path = 'uploads/file/' . $action.'/' . $WS->mainUserID;
        if(!file_exists($path)){
            mkdir($path, 0777, true);//创建目录
        }

        $capacity = getDirSize($path);//检查已上传容量

        if( (int)$capacity > 1024*1024*1024 || (int)$file_size + (int)$capacity > 1024*1024*1024 ){  //大于1G
            return response()->json(['code' => 10014, 'message' => '上传容量超出1G']);
        }

        //检查上传错误
        $upload_error_code = $file->getError();

        if (!empty($upload_error_code)) {
            switch($upload_error_code){
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

            //检查文件名
            if (!$file_name) {
                return response()->json(['code' => 1000003, 'message' => '请选择文件']);
            }

            $directory = 'uploads/file/' . $action.'/' . $WS->mainUserID;

            $new_file_name = date('YmdHis') . $_FILES['file']['name'];

            $file->move($directory,  $new_file_name);
            $data = array(
                'url' => $directory  .'/'. $new_file_name,
                'name' => $file_name,
            );

            if(isset($source_id) && $source_id){
                $DataSource = DataSourceClass::fetch($source_id);
                if(empty($DataSource)){
                    return response()->json([ 'code' => 400 , 'msg' => "没有找到此条数据源"]);
                }

                try {

                    DB::beginTransaction();//开启事务

                    $DataTable = DataTableClass::get(['source_id' => $source_id]);
                    if(empty($DataTable)){
                        return response()->json([ 'code' => 400 , 'msg' => "没有找到此条数据源"]);
                    }

                    //清空该集合
                    DB::collection($DataTable['table_name'])->delete();

                    $tableDate =  BIViewsController::creatTable($DataTable['table_name'],$data['url'],$DataSource['source_name']);//生成表结构

                    @unlink($DataSource['file_path']); //删除本地存储文件

                    DataSourceClass::save([
                        'file_path' => $data['url'],
                        'file_size' => $file_size,
                        'line_num' => $tableDate['line_num'],
                    ], $DataSource['_id']);

                    DataTableClass::save([
                        'fields_json' => $tableDate['fields_json']
                    ], $DataTable['_id']);

                    DB::commit();//提交事务
                }catch (Exception $e) {
                    DB::rollBack();//回滚

                    return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
                }

            }else{
                $WS->sessionSet('WeBI_SHOP_OPLOAD_FILE'. session()->getId(), $data['url'], 3600, true);//存储文件路径--1小时
            }

            $data['source_name'] = $_FILES['file']['name'];//数据源名称，默认取excel文件名
            return response()->json(['code' => 200,'msg' => 'ok', 'data' => $data]);
        } else {
            return response()->json(['code' => 1000010, 'message' => '上传失败']);
        }
    }

    //导入excel表数据
    public function import(Request $request)
    {
        global $WS;

        $filePath = $WS->sessionGet('WeBI_SHOP_OPLOAD_FILE' . session()->getId(), true);
        $source_name = $request->input('source_name', '');//来源名

        $WS->sessionRemove('WeBI_SHOP_OPLOAD_FILE' . session()->getId(), true);//删除文件路径缓存

        if(empty($filePath)){
            return response()->json(['code' => 100009, 'msg' => '请上传数据源文件']);
        }

        if($source_name == ""){
            return response()->json(['code' => 100003, 'msg' => '请输入来源名称']);
        }

        $reader = Excel::toArray(new ImportsClass, $filePath);
//        $reader = Excel::load($filePath);
//        $reader = $reader->getSheet(0);
//        $fileData = $reader->toArray();

        $fileData = $reader[0];

        //文件大小
        $file_size = filesize($filePath);
        $file_size = getRealSize($file_size);//转换文件字节单位

        $user_id = $WS->mainUserID;
        $table_name = 'excel_'. $user_id .'_'.time();
        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => '2']);

        /*  插入数据来源--data_source  */
        $DataSourceResult = DataSourceClass::save([
            'bi_user_id' => $user_id,
            'group_id' => $DataSourceGroup['_id'],
            'source_name' => $source_name,
            'table_name' => $table_name,
            'file_size'=> $file_size,
            'line_num' => count($fileData)-1,
            'file_path' => $filePath
        ]);

        $source_id =  $DataSourceResult['data'];

        /*  插入数据表信息--data_table  */
        $table_data =  BIViewsController::creatTable($table_name,$filePath,$source_name);//生成表结构

        DataTableClass::save([
            'table_id'=> generate_seqno('table_id'),
            'source_id'=> $source_id,
            'bi_user_id'=> $user_id,
            'table_name'=> $table_name,
            'description'=> $source_name,
            'fields_json'=> $table_data['fields_json'],
            'is_default' => 0
        ]);

        clearstatcache();//清除文件状态缓存

        $retun_data['line_num'] = count($fileData);
        $retun_data['source_name'] = $source_name;

        return response()->json(['code' => 200, 'msg' => 'ok','data' => $retun_data]);
    }

    //拼装表sql 生成表插入数据
    public function assembly($db_database,$table_name,$source_id){
        global $WS;

        $col_sql = "SELECT COLUMN_NAME,DATA_TYPE,COLUMN_TYPE,COLUMN_COMMENT 
                    FROM information_schema.columns 
                    WHERE table_schema='%s' AND table_name='%s'";
        $col_sql = sprintf($col_sql,$db_database,$table_name);
        $table_structure = DataSourceClass::conn_query($source_id,$col_sql);//拉取表结构

        if(empty($table_structure['data'])){
            return [];
        }

        $source_sql = "SELECT TABLE_COMMENT FROM information_schema.TABLES WHERE table_schema='%s' AND table_name='%s'";
        $source_sql = sprintf($source_sql,$db_database,$table_name);
        $table_source_name = DataSourceClass::conn_query($source_id,$source_sql);//获取表备注

        if(empty($table_source_name['data'])){
            return [];
        }

        $data_sql = "SELECT * FROM %s";
        $data_sql = sprintf($data_sql,$table_name);
        $table_data = DataSourceClass::conn_query($source_id,$data_sql);//拉取表数据

        //字段
        $table_field = "";
        $fields_json = [];
        foreach($table_structure['data'] as $key){
            $table_field .= $key['COLUMN_NAME'] ." ". $key['COLUMN_TYPE'] ." COMMENT '".$key['COLUMN_COMMENT']."',";

            //规则表  db_json
            $fields_json[] = [
                'field_name' =>  $key['COLUMN_NAME'],
                'data_type' => $key['DATA_TYPE'],
                'field_type' => $key['DATA_TYPE'],
                'field_remark' => $key['COLUMN_COMMENT']
            ];
        }

        //插入DataTable数据
         DataTableClass::save([
            'table_id'=> generate_seqno('table_id'),
            'source_id'=> $source_id,
            'bi_user_id'=> $WS->mainUserID,
            'table_name'=> $table_name,
            'description'=> $table_source_name['data'][0]['TABLE_COMMENT'],
            'fields_json'=> $fields_json,
             'is_default' => 0
        ]);

       //批量插入表数据
        DB::table($table_name)->insert( $table_data['data']);

        return $fields_json;
    }

    //生成数据源对应集合及数据
    public function creatTable($table_name,$filePath,$source_name){
        $reader = Excel::toArray(new ImportsClass, $filePath);
        $fileData = $reader[0];

        $remarks = $fileData[0];//字段备注
        unset($fileData[0]);//删除数据第一行---表头

        //生成表结构
        $fields_json = [];
        $field_count = count($fileData[1]);//统计字段个数
        //字段类型、长度判断
        for ($i = 0; $i < $field_count; $i++ ){
            //规则表  db_json
            $fields_json[] = [
                'field_name' => 'field_'.$i,
                'data_type' => 'varchar',
                'field_type' => 'varchar',
                'field_remark' => $remarks[$i]
            ];
        }

        //生成表数据
        $data = [];
        foreach($fileData AS $key => $value){
            $tableData = [];
            foreach ($value as $k=>$g){
                if(isset($date_position) && $date_position && $k == $date_position){
                    $g = date('Y-m-d H:i',strtotime($g));
                }

                $tableData['field_'.$k] = $g;
            }

            $data[] = $tableData;
        }

        //批量插入导入的数据
        DB::table($table_name)->insert($data);

        $tableDate['line_num'] =  count($fileData);//行数
        $tableDate['fields_json'] =  $fields_json;//字段结构
        return $tableDate;
    }

    //数据源列表
    public function sourceList(Request $request){
        global $WS;
        $group_id = $request->input('group_id');

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => $group_id]);
        if (empty($DataSourceGroup)) {
            return response()->json(['code' => 100001,'message' => '无效请求' ]);
        }

        $columns = '_id,updated_at,created_at,source_id,source_name,table_name,file_size,line_num,file_path,db_host,db_port,db_database,db_user';
        $DataSourceResult = DataSourceClass::getList([
            ['group_id',$DataSourceGroup['_id']],
            ['bi_user_id',$WS->mainUserID]
        ], ['pageSize' => 99], $columns);

        $data['source'] = $DataSourceResult['data'];
        return response()->json(['code' => 200, 'msg' => 'ok','data' => $data]);
    }

    //保存数据源名称
    public function source_name_save(Request $request){
        $source_id = $request->input('source_id');
        $source_name = $request->input('source_name');

        if (empty($source_name)) {
            return response()->json([ 'code' => 100005,'message' => '数据源名称不能为空']);
        }
        if(empty($source_id) || !$source_id){
            return response()->json([ 'code' => 10013,'message' => '无效请求']);
        }

        $DataSource = DataSourceClass::fetch($source_id);
        if (!$DataSource) {
            return response()->json(['code'=>100006,'message'=>'数据源信息不存在']);
        }

        DataSourceClass::save(['source_name' => $source_name], $source_id);

        return response()->json(['code'=>200, 'message'=>'保存成功']);
    }


    public function source_table_save(Request $request){
        $args = $request->all();

        if(empty($args['table_id']) || !$args['table_id']){
            return response()->json([ 'code' => 10013,'message' => '无效请求']);
        }

        $DataTable = DataTableCLass::fetch($args['table_id']);
        if (!$DataTable) {
            return response()->json(['code'=>100006,'message'=>'数据源信息不存在']);
        }

        $saveData = [];
        if($args['edit_field'] == 1){
            if (empty($args['table_name']) ) {
                return response()->json([ 'code' => 100005,'message' => '名称不能为空']);
            }

            $saveData['description'] = $args['table_name'];
        }else{
            if (empty($args['field_remark'])) {
                return response()->json([ 'code' => 100005,'message' => '名称不能为空']);
            }

            foreach ($DataTable['fields_json'] as $k => $v){
                if($args['field_name'] == $k){
                    $DataTable['fields_json'][$k]['field_remark'] = $args['field_remark'];
                }
            }

            $saveData['fields_json'] = $DataTable['fields_json'];
        }

        DataTableCLass::save($saveData, $args['table_id']);

        return response()->json(['code'=>200, 'message'=>'保存成功']);
    }

    //数据源删除
    public function source_del(Request $request){
        $source_id = $request->input('source_id');

        $groupSet = BiViewClass::getList(['source_id' => $source_id], ['pageSize' => 999], 'view_id');
        if($groupSet['count']){
            foreach ($groupSet['data'] as $g){
                $bModule = BiMasterModuleClass::getList(['view_id' => $g['view_id']]);
                if(empty($bModule['data'])){
                    return response()->json([ 'code' => 400 , 'message' => "删除数据源失败，该数据源是否已被使用!" ]);
                }
            }
        }

        $DataSource = DataSourceClass::fetch($source_id);
        if(empty($DataSource)){
            return response()->json([ 'code' => 400 , 'msg' => "没有找到此条数据源"]);
        }

        try {

            DB::beginTransaction();//开启事务

            $DataTable = DataTableClass::get(['source_id' => $source_id]);

            if(!empty($DataTable)){
                DB::collection($DataTable['table_name'])->delet();//删除临时表

                DataTableClass::del($DataTable['_id']);

                BiView::where('source_id',$source_id)->update(['table_json' => '']);
            }

            @unlink($DataSource['file_path']); //删除本地存储文件

            DataSourceClass::del($source_id);

            DB::commit();//提交事务
        }catch (Exception $e) {
            DB::rollBack();//回滚

            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json([ 'code' => 200 , 'message' => "ok" ]);
    }

    //数据源表数据信息
    public function source_table_list(Request $request){
        $source_id = $request->input('source_id');
        $DataTable = DataTableClass::get(['source_id' => $source_id]);

        $table = DB::table($DataTable['table_name'])
            ->paginate((int)$request->input('limit'))
            ->toArray();

        if(empty($table)){
            return response()->json([ 'code' => 400 , 'message' => "没有找到此数据源"]);
        }

        $count = DB::table($DataTable['table_name'])
            ->count();

        $fields_json = json_decode($DataTable['fields_json'], true);
        $tables = [];
        foreach ($fields_json as $k => $v){
            $tables[0][] = $v['field_remark'];
        }

        foreach ($table['data'] as $v=>$k){
            unset($k['_id']);
            $tables[1][] = $k;
        }

        $table_data = [
            'table' => $tables,
            'table_name' => $DataTable['description'],
            'table_id' => $DataTable['_id'],
            'count' => $count,
        ];

        return response()->json([ 'code' => 200 , 'message' => "ok" , 'data' => $table_data]);
    }

    //保存、测试数据库连接
    public function sqlSave(Request $request){
        global $WS;
        $data = $request->all();

        if (empty($data['db_host'])) {
            return response()->json([ 'code' => 100011 , 'message' => "请输入数据库主机地址"]);
        }
        if (empty($data['db_user'])) {
            return response()->json([ 'code' => 100012 , 'message' => "请输入数据库用户名"]);
        }
        if (empty($data['db_pwd'])) {
            return response()->json([ 'code' => 100013 , 'message' => "请输入数据库用户密码"]);
        }
        if (empty($data['db_database'])) {
            return response()->json([ 'code' => 100014 , 'message' => "请输入数据库名称"]);
        }
        if (empty($data['db_port'])) {
            return response()->json([ 'code' => 100015 , 'message' => "请输入数据库主机端口号"]);
        }

       $connect = @mysqli_connect($data['db_host'],$data['db_user'],$data['db_pwd'],$data['db_database'],$data['db_port']);
        if( !$connect){
            return response()->json([ 'code' => 100016 , 'message' => "连接失败" ]);
        }

        $saveData = [];
        $_id = "";

        if((!empty($data['operation_type']) && $data['operation_type'] == 4)){//新增数据源数据库地址
            $saveData['bi_user_id'] = $WS->mainUserID;
            $saveData['source_name'] = $data['db_database'];
        } else {  //编辑
            $DataSource = DataSourceClass::fetch($data['source_id']);
            if(empty($DataSource)){
                return response()->json([ 'code' => 10012 , 'message' => "数据源不存在，请刷新页面！"]);
            }

            $_id = $DataSource['_id'];
        }

        $DataSourceGroup = DataSourceGroupClass::get(['group_classify' => '3']);

        $saveData['group_id'] = (string)$DataSourceGroup['_id'];
        $saveData['db_host'] = $data['db_host'];
        $saveData['db_user'] = $data['db_user'];
        $saveData['db_pwd'] = $data['db_pwd'];
        $saveData['db_database'] = $data['db_database'];
        $saveData['db_port'] = $data['db_port'];

        DataSourceClass::save($saveData, $_id);
        return response()->json([ 'code' => 200 , 'message' => "连接成功"]);
    }

    //获取表字段信息
    public function tableStructure(Request $request){
       $data = $request->all();

        if(empty($data['source_id']) || empty($data['table_name'])){
            return response()->json([ 'code' => 100017 , 'message' => "数据无效"]);
        }

        $DataSource = DataSourceClass::fetch($request->input('source_id'));
        if(!$DataSource) {
            return response()->json([ 'code' => 100017 , 'message' => "数据无效"]);
        }

        $DataSourceGroup = DataSourceGroupClass::fetch($DataSource['group_id']);
        if($DataSourceGroup['group_classify'] == 3){ //mysql数据源表
            $sql = 'SELECT column_name,data_type,column_comment 
                FROM information_schema.columns
                WHERE table_name = "%s"';
            $sql = sprintf($sql, trim( $data['table_name']));
            $conResult = DataSourceClass::conn_query($data['source_id'],$sql);

            $tables = $conResult['data'];
        } else {
            $DataTableResult = DataTableClass::get(['table_name' => $data['table_name']]);

            $tables = json_decode($DataTableResult['fields_json'], true);
        }

        $fieldList = [];
        $fieldList['field'] = $tables;

        return response()->json([ 'code' => 200 , 'message' => "ok", 'data' => $fieldList ]);
    }

}