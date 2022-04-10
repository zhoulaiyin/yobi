<?php
namespace App\Http\Controllers\WeBI\shop;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Mockery\Exception;

use App\Models\Control\Statement\BiMasterModule;

use App\Http\Controllers\WeBI\backend\BiTemplateController;
use App\Http\Controllers\WeBI\WeBIGlobalController;
use App\Models\Classes\Console\BiChartGroupClass;
use App\Models\Classes\Console\BiChartMasterClass;
use App\Models\Classes\Console\BiFontFamilyClass;
use App\Models\Classes\Console\BiTemplateDatabaseClass;
use App\Models\Classes\Console\BiTemplateDatabaseModuleClass;
use App\Models\Classes\Control\BiUserClass;
use App\Models\Classes\Control\DataSource\BiViewClass;
use App\Models\Classes\Control\DataSource\DataSourceGroupClass;
use App\Models\Classes\Control\Statement\BiMasterClass;
use App\Models\Classes\Control\Statement\BiMasterModuleClass;
use App\Models\Classes\Control\Template\BiUserTemplateDatabaseClass;
use App\Models\Classes\Control\Template\BiUserTemplateDatabaseModuleClass;

use App\Models\Control\DataSource\BiView;

class BIEditController extends Controller
{

    public function index( $uid ) {
        global $WS;

        if (empty($uid)) {
            return redirect('error/error?msg=参数错误');
        }

        $where = [
            ['bi_user_id', $WS->mainUserID],
            ['uid', $uid]
        ];

        $bi_master = BiMasterClass::getList($where);
        if (empty($bi_master)) {
            return redirect('error/error?msg=参数错误');
        }

        //无设计权限，跳转预览页面
        if (!$WS->isDesigner()) {
            return redirect('webi/show?uid='. $uid);
        }

        $data = [
            'uid'=> $uid
        ];

        return view('webi/shop/webiEdit',$data);
    }

    //数据集列表
    public function dts_list(Request $request){
        global $WS;
        $args =  $request->all();
        $group_classify = $args['group_id'];

        if (!empty($args['view_id'] ) && $args['view_id']) {
            $viewResult = BiViewClass::fetch($args['view_id']);

            $args['group_id'] = $viewResult['group_id'];

            $group = DataSourceGroupClass::fetch($viewResult['group_id']);
            $group_classify = $group['group_classify'];
        }


        if ($args['group_id'] == 0 || !in_array($args['group_id'], [0,1,2,3])) {
            $where[] = ['group_id',$args['group_id']];
        } else {
            $group = DataSourceGroupClass::get(['group_classify'=> $args['group_id']]);
            $where[] = ['group_id',$group['_id']];
        }

        //查询视图列表
        $view = BiView::select('view_id','view_name','group_id')
            ->whereIn('bi_user_id',[$WS->mainUserID, "0"])
            ->where($where)
            ->get()->toArray();

        $data = [
            'rule' => $view,
            'group' => $group_classify
        ];

        return response()->json(array('code'=>200,'message'=>'ok','data' => $data));
    }

    public function get_data($uid){
        return response()->json(WeBIGlobalController::get($uid,2));
    }

    public function get_rule($id) {
        return response()->json( WeBIGlobalController::get_view_table($id) );
    }

    public function choose(Request $request) {
        $column = 'group_id,group_name,group_code,icon';
        $chart_group = BiChartGroupClass::getList([], ['pageSize' => 9999], $column);

        $data['chart_group'] = $chart_group['data'];
        $data['callback_fun'] = $request->input('callback');

        return view('webi/choosebi',$data);
    }

    public function get($group_id) {
        $master = [];

        $where = ['group_id' => $group_id];
        $chart_master = BiChartMasterClass::getList($where, ['pageSize' => 9999]);

        if ($chart_master['count'] > 0) {
            foreach ( $chart_master['data'] as $m ) {
                if ( !isset($master[$m['chart_id']]) ) {
                    $master[$m['_id']] = [
                        'group_id' => $m['group_id'],
                        'chart_title' => $m['chart_title'],
                        'photo_link' => $m['photo_link'],
                        'chart_json' => json_decode($m['stringify'], true)
                    ];
                }
            }
        }

        return response()->json(array('code'=>200,'message'=>'ok', 'data'=>$master));
    }

    //创建子报表
    public function add_module(Request $request) {
        $chart_id = $request->input('chart_id', '');
        $bi_id = $request->input('bi_id', '');
        $modul_data =$request->input('modul_data', '');

        if (empty($chart_id) || empty($bi_id)) {
            return response()->json(array('code'=>400,'message'=>'参数错误'));
        }

        //检查BI主表
        $master_obj = BiMasterClass::fetch($bi_id);
        if (!$master_obj) {
            return response()->json(array('code'=>400,'message'=>'BI主表不存在，请刷新'));
        }

        //检查类型主表
        $chart_master_obj = BiChartMasterClass::fetch($chart_id);
        if (!$chart_master_obj) {
            return response()->json(array('code'=>400,'message'=>'图表不存在，请刷新'));
        }

        $chart_group_obj = BiChartGroupClass::fetch($chart_master_obj['group_id']);
        if (!$chart_group_obj) {
            return response()->json(array('code'=>400,'message'=>'图表类型分组不存在，请刷新'));
        }

        $modul_data = json_decode($modul_data,true);

        $modul_data['bi_json']['type'] = $chart_group_obj['group_code'];
        $modul_data['bi_json']['chart_json'] = json_decode($chart_master_obj['stringify'], true);
        $db_json = BiTemplateController::db_json($chart_group_obj['group_code']);//生成db_json结构数据
        $module_uid = makeUuid();

        //生成子模块ID
        BiMasterModuleClass::save([
            'uid' => $module_uid,
            'bi_id' => $bi_id,
            'view_id' => $db_json['view_id'],
            'bi_json' => json_encode($modul_data['bi_json']),
            'db_json' => json_encode($db_json),
            'attribute_json' => json_encode($modul_data['attribute_json']),
            'chart_json' => json_encode($modul_data['chart_json'])
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'ok',
            'uid' => $module_uid,
            'bi_json' => $modul_data['bi_json'],
            'db_json' => $db_json,
            'callback_fun' => $request->input('callback_fun')
        ]);
    }

    /**
     * @param $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function replace_module(Request $request){
        $chart_id = $request->input('chart_id');
        $uid = $request->input('uid');

        if ( empty($chart_id) || empty($uid) ) {
            return response()->json(array('code'=>400,'message'=>'参数错误'));
        }

        $where = ['uid' => $uid];
        $MasterModuleResult = BiMasterModuleClass::getList($where);
        if ($MasterModuleResult['count'] == 0) {
            return response()->json(array('code'=>10001,'message'=>'待编辑报表不存在，请刷新'));
        }

        $module_obj = $MasterModuleResult['data'][0];

        //检查类型主表
        $chart_master_obj = BiChartMasterClass::fetch($chart_id);
        if (!$chart_master_obj) {
            return response()->json(array('code'=>400,'message'=>'图表不存在，请刷新'));
        }

        $chart_group_obj = BiChartGroupClass::fetch($chart_master_obj['group_id']);
        if (!$chart_group_obj) {
            return response()->json(array('code'=>400,'message'=>'图表类型分组不存在，请刷新'));
        }

        $bi_json = [
            'type' => $chart_group_obj['group_code'],
            'chart_json' => json_decode($chart_master_obj['stringify'], true)
        ];

        $attribute_json = json_decode($module_obj['attribute_json'], true);
        $attribute_json['series'] = '';

        $saveData = [];
        $db_json = "";
        //更换类型使用系统数据集 更新db_json结构
        if((int) json_decode($module_obj['db_json'],true)['view_id'] <= 100){
            $db_json = BiTemplateController::db_json($chart_group_obj['group_code']);

            $saveData['bi_json'] = json_encode($db_json);
            $saveData['view_id'] = $db_json['view_id'];
        }

        $saveData['bi_json'] = json_encode($bi_json);
        $saveData['attribute_json'] = json_encode($attribute_json);

        BiMasterModuleClass::save($saveData, $MasterModuleResult['data'][0]['_id']);

        return response()->json([
            'code'=>200,
            'message'=>'ok',
            'data' => array(
                'bi_json' => $bi_json,
                'db_json' => $db_json,
                'attribute_json' => $attribute_json
            ) ,
            'callback_fun' => $request->input('callback_fun')
        ]);
    }

    //删除子报表
    public function del_module( $uid,Request $request ) {
        $where = ['uid' => $uid];
        $bi_chart_module_obj = BiMasterModuleClass::getList($where);
        if($bi_chart_module_obj['count'] == 0) {
            return response()->json(array('code'=>100001,'message'=>'子报表已删除，请刷新'));
        }

        BiMasterModuleClass::del($bi_chart_module_obj['data'][0]['_id']);

        return response()->json(array('code'=>200,'message'=>'删除成功','callback'=>$request->input('callback_fun')));
    }

    //复制子报表
    public function copy_module( Request $request ) {
        $uid = $request->input('uid', '');
        $top_percent = $request->input('top_percent', 0);
        $height_percent = $request->input('height_percent', 0);
        $top = $request->input('top');

        $where = ['uid' => $uid];
        $copy_module_result = BiMasterModuleClass::getList($where);

        if ($copy_module_result['count'] == 0) {
            return response()->json(array('code'=>100002,'message'=>'复制的报表不存在'));
        }

        $copy_module_obj = $copy_module_result['data'][0];

        $attribute_json = json_decode($copy_module_obj['attribute_json'], true);
        $attribute_json['top_percent'] = $top_percent;
        $attribute_json['height_percent'] = $height_percent;
        $attribute_json['top'] = $top;

        $module_uid = makeUuid();

        //生成子模块ID
        BiMasterModuleClass::save([
            'uid' => $module_uid,
            'bi_id' => $copy_module_obj['bi_id'],
            'view_id' => $copy_module_obj['view_id'],
            'bi_json' => $copy_module_obj['bi_json'],
            'db_json' => $copy_module_obj['db_json'],
            'attribute_json' => json_encode($attribute_json),
            'chart_json' => $copy_module_obj['chart_json']
        ]);

        return response()->json([
            'code'=>200,
            'message'=>'ok',
            'uid'=>$module_uid,
            'module' => array(
                'bi_json' => json_decode($copy_module_obj['bi_json'], true),
                'db_json' => json_decode($copy_module_obj['db_json'], true),
                'chart_json' => json_decode($copy_module_obj['chart_json'], true),
                'attribute_json' => $attribute_json
            ),
            'callback_fun' => $request->input('callback_fun')
        ]);
    }

    public function get_bi( $uid ,Request $request) {
        $where = ['uid' => $uid];
        $moduleResult = BiMasterModuleClass::getList($where);
        if ($moduleResult['count'] == 0) {
            return response()->json(array('code'=>100003,'message'=>'该报表不存在，请刷新'));
        }

        $module_obj = $moduleResult['data'][0];

        $BiMaster = BiMasterClass::fetch($module_obj['bi_id']);

        $data = [
            'bi_id' => $module_obj['bi_id'],
            'view_id' => $module_obj['view_id'],
            'bi_json' => json_decode($module_obj['bi_json'], true),
            'db_json' => json_decode($module_obj['db_json'], true),
            'attribute_json' => json_decode($module_obj['attribute_json'], true),
            'chart_json' => json_decode($module_obj['chart_json'], true),
            'master' => json_decode($BiMaster['attribute_json'], true),
            'callback_fun' => $request->input('callback_fun')
        ];

        return response()->json(array('code'=>200,'message'=>'ok', 'data'=>$data));
    }

    /**
     * 保存主表属性
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_master(Request $request) {
        $bi_id = $request->input('bi_id');
        $data = $request->input('data');

        if (empty($bi_id) || empty($data)) {
            return response()->json(array('code'=>400,'message'=>'参数错误'));
        }

        $master_obj = BiMasterClass::fetch($bi_id);
        if (!$master_obj) {
            return response()->json(array('code'=>10001,'message'=>'BI主表不存在，请刷新'));
        }

        BiMasterClass::save([
            'bi_title' => $data['general']['title'],
            'attribute_json' => $data
        ], $bi_id);

        return response()->json(array('code'=>200,'message'=>'ok'));
    }

    /**
     * 保存BI_ATTRIBUTE属性设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_bi_attr(Request $request) {
        $uid = $request->input('uid');
        $data = $request->input('data');

        if (empty($uid) || empty($data)) {
            return response()->json(array('code'=>400,'message'=>'参数错误'));
        }

        $where = ['uid' => $uid];
        $MasterModuleResult = BiMasterModuleClass::get($where);
        if (!$MasterModuleResult) {
            return response()->json(array('code'=>10001,'message'=>'待编辑子报表不存在，请刷新'));
        }

        BiMasterClass::save([
            'attribute_json' => $data
        ], $MasterModuleResult['_id']);

        return response()->json(array('code'=>200,'message'=>'ok'));
    }

    /**
     * 保存BI_CHART属性设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_bi_chart(Request $request) {
        $uid = $request->input('uid');
        $data = $request->input('data');

        if (empty($uid) || empty($data)) {
            return response()->json(array('code'=>400,'message'=>'参数错误'));
        }

        $where = ['uid' => $uid];
        $MasterModuleResult = BiMasterModuleClass::get($where);
        if (!$MasterModuleResult) {
            return response()->json(array('code'=>10001,'message'=>'待编辑子报表不存在，请刷新'));
        }

        BiMasterClass::save([
            'chart_json' => $data
        ], $MasterModuleResult['_id']);

        return response()->json(array('code'=>200,'message'=>'ok'));
    }


    /**
     * 保存BI数据集设置信息
     * @param Request $request
     */
    public function save_bi_dts(Request $request) {
        $uid = $request->input('uid', '');
        $data = $request->input('data');
        $bi_module = $request->input('bi_module');

        if (empty($uid) || empty($data)) {
            return response()->json(array('code'=>400,'message'=>'参数错误'));
        }

        if(isset($bi_module) && $bi_module){
            $where = ['module_uid' => $uid];
        }else{
            $where = ['uid' => $uid];
        }

        $MasterModuleResult = BiMasterModuleClass::get($where);
        if (!$MasterModuleResult) {
            return response()->json(array('code'=>10001,'message'=>'待编辑子报表不存在，请刷新'));
        }

        BiMasterModuleClass::save([
            'view_id' => $data['view_id'],
            'db_json' => json_encode($data)
        ], $MasterModuleResult['_id']);

        return response()->json(array('code'=>200,'message'=>'ok'));
    }

    /**
     * 更新BI数据集模块信息
     * @param Request $request
     */
    public function save_all_module(Request $request){
        $module_data = $request->input('master_module');

        foreach ($module_data as $uid=>$module){
            $where = ['uid' => $uid];
            $MasterModuleResult = BiMasterModuleClass::get($where);
            if (!$MasterModuleResult) {
                continue;
            }

            BiMasterClass::save([
                'attribute_json' => json_encode($module['attribute_json'])
            ], $MasterModuleResult['_id']);
        }

        return response(['code' => 200,'msg'=> 'ok']);
    }

    /**
     * 获取BI维护字体
     */
    public function font_get(){
        $column = 'val';
        $font_family = BiFontFamilyClass::getList([], ['pageSize' => 999], $column);

        return response(['code' => 200,'msg' => 'ok','data' => $font_family['data']]);
    }

    /**
     * 保存调整后的位置信息
     * * @param Request $request
     */
    public function save_position(Request $request){
        $module_position = $request->input('module');
        $flg = $request->input('flg', 1);

        try{

            DB::beginTransaction();

            foreach ( $module_position AS $key => $val){
                if($flg == 1){
                    $where[] = ['uid',$key];
                    $bi_module = BiMasterModuleClass::getList($where);
                }else{
                    $where[] = ['module_uid',$key];
                    $bi_module = BiTemplateDatabaseModuleClass::getList($where);
                }

                if (!$bi_module) {
                    throw new \Exception('无此数据BI模块', 401);
                }

                $bi_module = $bi_module['data'][0];

                $attribute_json = json_decode($bi_module['attribute_json'], true);
                $attribute_json['height'] = $val['height'];
                $attribute_json['top'] = $val['top'];
                $attribute_json['height_percent'] = $val['height_percent'];
                $attribute_json['top_percent'] = $val['top_percent'];
                $attribute_json['width_percent'] = $val['width_percent'];
                $attribute_json['left_percent'] = $val['left_percent'];

                $saveData = [
                    'attribute_json' => json_encode($attribute_json)
                ];

                if($flg == 1) {
                    BiMasterModuleClass::save($saveData, $bi_module['_id']);
                } else {
                    BiTemplateDatabaseModuleClass::save($saveData, $bi_module['_id']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 'code' => $e->getCode(), 'message' => $e->getMessage() ]);
        }

        return response()->json([ 'code' => '200', 'message' => 'ok' ]);
    }

    //BI模板列表
    public function  theme_list(Request $request){
        global $WS;
        $id = $request->input('id');
        $theme_title = $request->input('theme_title');
        $keyword = $request->input('keyword');
        $where = [];

        switch ($keyword){
            case 1:
                $orderBy = 'total_download';
                break;
            case 2:
                $orderBy = 'total_collect';
                break;
            default:
                $orderBy = 'template_id';
        }

        $BiUser = BiUserClass::fetch($WS->mainUserID);

        //云端
        if(isset($id) && $id ==1){
            $where[] = ['template_status',1];
            $where[] = ['group_id', $BiUser['group_id']];
        }

        //本地
        if(isset($id)  && $id ==2){
            $where[] = ['bi_user_id', $WS->mainUserID];
        }

        //关键词搜索
        if(isset($theme_title)  && $theme_title != ""){
            $where[]= ['template_title', 'like', '%'.$theme_title.'%'];
        }

        $limit = [
            'orderBy' => $orderBy,
            'page' => $request->input('page'),
            'pageSize' => $request->input('limit'),
        ];

        if (isset($id) && $id ==1) {//云端模板
            $chart_group = BiTemplateDatabaseClass::getList($where, $limit);
        } else {//本地模板
            $chart_group = BiTemplateDatabaseClass::getList($where);
        }

        $data['template'] = $chart_group['data'];
        $data['count'] = $chart_group['count'];
        $data['page'] = $request->input('page');

        return response()->json(['code' => 200,'msg' => 'ok','data' => $data]);
    }

    //BI组件添加主题
    public function theme_choose(Request $request){
        $template_id = $request->input('template_id');
        $bi_id =  $request->input('bi_id');
        $module_id = $request->input('module_id');

        if(isset($module_id) && $module_id == 1){//当前BI报表存在BI组件
            BiMasterModule::where('bi_id',$bi_id)->delete();
        }

        $TemplateDatabase = BiTemplateDatabaseClass::fetch($template_id);
        if(!$TemplateDatabase){
            return response()->json(['code' => 100004,'message' => '主题不存在，请刷新']);
        }

        //获取模板数据
        $where = [
            'template_id' => $template_id
        ];
        $templateDatabaseModule = BiTemplateDatabaseModuleClass::getList($where, ['pageSize' => 9999]);

        //更换BI全局属性
        $BiMaster =  BiMasterClass::fetch($bi_id);
        $attribute_json = json_decode($BiMaster['attribute_json'], true);

        $tem_attribute_json = json_decode($TemplateDatabase['attribute_json'], true);
        $attribute_json['general'] = $tem_attribute_json['general'];

        BiMasterClass::save([
            'attribute_json' => json_encode($attribute_json)
        ], $bi_id);


        //模板下载量增加
        BiTemplateDatabaseClass::save([
            'total_download' => ($TemplateDatabase['total_download'])+1
        ], $template_id);

        //BI组件
        foreach ($templateDatabaseModule['data'] as $key => $value){
            //生成子模块ID
            $db_json = json_decode($value['db_json'], true);
            BiMasterModuleClass::save([
                'uid' => makeUuid(),
                'bi_id' => $bi_id,
                'view_id' => $db_json['view_id'],
                'bi_json' => $value['bi_json'],
                'db_json' => $value['db_json'],
                'attribute_json' => $value['attribute_json'],
                'chart_json' => $value['chart_json']
            ]);
        }

        return response()->json(['code' => 200,'message' => 'ok']);
    }

    //用户上传主题
    public function template_add(Request $request){
        global $WS;
        $template_title = $request->input('template_title');
        $template_group =  $request->input('template_group');
        $template_pic = $request->input('template_pic');
        $bi_id = $request->input('bi_id');

        $BiMaster = BiMasterClass::fetch($bi_id);
        if(!$BiMaster){
            return response()->json(['code' => 100004,'message' => 'BI组件不存在，请刷新']);
        }

        //获取模板数据
        $where = [
            'bi_id' => $bi_id
        ];
        $BiMasterModuleResult = BiMasterModuleClass::getList($where, ['pageSize' => 9999]);

        $BiMasterModule = $BiMasterModuleResult['data'];

        $attribute_json = json_decode($BiMaster['attribute_json'], true);
        $attribute_json['general']['title'] = $template_title;

        try {

            DB::beginTransaction();

            if( $template_group == 1){//云端

                $result = BiTemplateDatabaseClass::save([
                    'bi_user_id' => $WS->mainUserID,
                    'template_title' => $template_title,
                    'template_pic' => $template_pic,
                    'attribute_json' => json_encode($attribute_json)
                ]);
                if ($result['code'] != 200) {
                    throw new Exception($result['message'], 10000);
                }

                $masterId =  $result['data'];

                //BI组件
                foreach ($BiMasterModule as $key => $value){
                    //生成子模块ID
                    BiTemplateDatabaseModuleClass::save([
                        'uid' => makeUuid(),
                        'bi_id' => $masterId,
                        'bi_json' => $value['bi_json'],
                        'db_json' => $value['db_json'],
                        'attribute_json' => $value['attribute_json'],
                        'chart_json' => $value['chart_json']
                    ]);
                }

            } else {//本地

                $result = BiUserTemplateDatabaseClass::save([
                    'bi_user_id' => $WS->mainUserID,
                    'template_title' => $template_title,
                    'template_pic' => $template_pic,
                    'attribute_json' => json_encode($attribute_json)
                ]);
                if ($result['code'] != 200) {
                    throw new Exception($result['message'], 10000);
                }

                $masterId = $result['data'];

                //BI组件
                foreach ($BiMasterModule as $key => $value){
                    $bi_json =  $value['bi_json'];
                    $db_json =  BiTemplateController::db_json($bi_json['type']);

                    //生成子模块ID
                    BiUserTemplateDatabaseModuleClass::save([
                        'module_uid' => makeUuid(),
                        'template_id' => $masterId,
                        'bi_json' =>  json_encode($value['bi_json']),
                        'db_json' =>  json_encode($db_json),
                        'attribute_json' => json_encode($value['attribute_json']),
                        'chart_json' =>  json_encode($value['chart_json'])
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json(['code' => 200,'message' => '操作成功']);
    }

}