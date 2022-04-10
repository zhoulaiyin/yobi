<?php

namespace App\Http\Controllers\WeBI\backend;

use App\Http\Controllers\Controller;
use App\Models\Classes\Control\DataSource\BiViewClass;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Classes\Console\BiChartGroupClass;
use App\Models\Classes\Console\BiChartMasterClass;
use App\Models\Classes\Console\BiTemplateDatabaseClass;
use App\Models\Classes\Console\BiTemplateDatabaseModuleClass;
use App\Models\Classes\Console\BiTemplateGroupClass;
use App\Models\Classes\Control\ProjectClass;
use App\Http\Controllers\WeBI\WeBIGlobalController;

class BiTemplateController extends Controller
{

    //BI模板列表
    public function index()
    {
        $data = [];

        $where = [];
        $limit = [
            'orderBy' => 'project_id',
            'sort' => 'ASC'
        ];
        $column = 'project_name, project_id';
        $templ_data = ProjectClass::getList($where, $limit, $column);

        $data['templ_list'] = json_encode($templ_data['data']);

        return view('webi/backend/templateDB/list', $data);

    }

    //模板发布、下架操作
    public function del($template_id, $type)
    {

        $templ_map = BiTemplateDatabaseClass::fetch($template_id);
        if (!$templ_map) {
            return response()->json(['code' => 400, 'message' => '该模板不存在，请刷新页面！']);
        }


        BiTemplateDatabaseClass::save([
            'template_status' => $type,
            'updated_at' => Carbon::now()
        ]);


        return response()->json(['code' => 200, 'message' => 'ok']);
    }

    //搜索操作
    public function search(Request $request)
    {
        $where = [];
        $status = array(0 => "创建", 1 => "发布", 2 => "下架");

        if ($request->input('project_id')) {
            $where[] = ['project_id', $request->input('project_id')];
        }
        if ($request->input('template_status') == 3) {
            $where[] = ['template_status', 0];
        }
        if ($request->input('template_status') && (int)$request->input('template_status') != 3) {
            $where[] = ['template_status', (int)$request->input('template_status')];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10)
        ];

        $column = 'template_id，template_title，template_pic，bi_uid，creator，project_id，template_status';
        $sdk_data = BiTemplateDatabaseClass::getList($where, $limit, $column);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => isset($sdk_data['count']) ? $sdk_data['count'] : 0,
            'data' => array()
        );

        if ($sdk_data['count'] > 0) {

            foreach ($sdk_data['data'] as $key => $val) {

                $source[0]['project_name'] = "系统";

                if ($val['project_id'] != 0) {
                    $where = ['project_id' => $val['project_id']];
                    $column = 'project_name';
                    $source = ProjectClass::getList($where, [], $column);
                }

                $return_data['data'][] = array(
                    'creator' => $val['creator'],
                    'template_title' => $val['template_title'],
                    'template_pic' => !empty($val['template_pic']) ? '<a href="' . $val['template_pic'] . '" target="_blank" title="点击预览展示图"><img style="width: 30px;height: 30px; " src="' . $val['template_pic'] . '" ></a>' : '<a  herf="javascript:(0)" title="未上传展示图"><img style="width: 30px;height: 30px; " src="' . $val['template_pic'] . '" ></a>',
                    'project' => $source[0]['project_name'],
                    'status' => $status[$val['template_status']],
                    'template_id' => $val['_id'],
                    '_id' => $val['_id']
                );
            }

        }

        return $return_data;
    }

    //编辑、新增模板
    public function edit($type)
    {
        $data = [];
        if ($type != 0) {//编辑
            $where = [['template_id' => $type]];
            $column = 'group_id, template_title, template_icon, template_pic';
            $map_data = BiTemplateDatabaseClass::getList($where, [], $column);

            if (!$map_data) {
                return response()->json(['code' => 10003, 'message' => "无此模板，请刷新页面！"]);
            }

            $data['group_id'] = $map_data['data'][0]['group_id'];
            $data['template_title'] = $map_data['data'][0]['template_title'];
            if (!empty($map_data['data'][0]['template_pic'])) {
                $data['template_pic'] = $map_data['data'][0]['template_pic'];
            }
        }
        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $data]);
    }

    //保存模板
    public function save(Request $request)
    {

        $type_id = $request->input("type");//0是新增 其他是主键编辑
        $template_group = $request->input("template_group", 0);
        $template_title = $request->input("template_title");
        $template_pic = $request->input("template_pic");

        if ($type_id != 0) {//编辑

            $findStatus = BiTemplateDatabaseClass::fetch($type_id);
            if (!$findStatus) {
                return response()->json(['code' => 10005, 'message' => '不存在此模板']);
            }

            BiTemplateDatabaseClass::save([
                'group_id' => $template_group,
                'template_title' => $template_title,
                'template_pic' => $template_pic
            ], $type_id);

        } else {//新增

            $attribute_json = [
                'project_id' => 0,
                'bi_id' => 0,
                'uid' => '',
                'general' => [
                    'title' => $template_title,
                    'backgroundColor' => '',
                    'backgroundImage' => '',
                    'border' => '5',
                    'border_image_slice' => '',
                    'border_image_repeat' => 'repeat',
                    'border_image_source' => '',
                    'refresh_frequency' => ''
                ]
            ];


            BiTemplateDatabaseClass::save([
                'group_id' => $template_group,
                'template_title' => $template_title,
                'template_pic' => $template_pic,
                'project_id' => 0,
                'bi_id' => 0,
                'bi_uid' => "",
                'template_status' => 0,
                'attribute_json' => json_encode($attribute_json)
            ]);

        }

        return response()->json(['code' => 200, 'message' => '保存成功']);

    }

    /**
     * 设计页面
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function design($template_id)
    {
        global $WS;

        $master = BiTemplateDatabaseClass::fetch($template_id);
        if (!$master) {
            return redirect('error/error?msg=模板不存在');
        }

        //调用报表接口数组
        $webi_dt = array(
            'master' => json_decode($master['attribute_json'], true), //全局属性
            'module' => array()
        );
        //操作对象
        $where = ['template_id' => $template_id];
        $module = BiTemplateDatabaseModuleClass::getList($where);

        if ($module) {
            foreach ($module['data'] as $k => $v) {

                //子模板
                if (!isset($webi_dt['module'][$v['module_uid']])) {
                    $bi_json = json_decode($v['bi_json'], true);
                    $webi_dt['module'][$v['module_uid']] = array(
                        'db_json' => json_decode($v['db_json'], true),
                        'bi_json' => $bi_json,
                        'attribute_json' => json_decode($v['attribute_json'], true),
                        'chart_json' => json_decode($v['chart_json'], true),
                    );
                }
            }
        }

        //查询视图列表
        $where = [['project_id', 0]];
        $column = 'view_id, view_name';
        $view = BiViewClass::getList($where, [], $column);

        //页面数组
        $data = array(
            'bi_id' => $template_id,
            'group_id' => 0,
            'm_uid' => '"' . $master['bi_uid'] . '"',
            'webi_dt' => json_encode($webi_dt),
            'rule' => $view
        );

        return view('webi/backend/templateDB/edit', $data);
    }

    /**
     * 全局保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function save_master(Request $request)
    {

        $template_id = $request->input('bi_id');
        $data = $request->input('data');

        if (empty($template_id) || empty($data)) {
            return response()->json(array('code' => 400, 'message' => '参数错误'));
        }

        $master_obj = BiTemplateDatabaseClass::fetch($template_id);
        if (!$master_obj) {
            return response()->json(array('code' => 10001, 'message' => 'BI主表不存在，请刷新'));
        }

        $master_obj->updated_at = Carbon::now();
        $master_obj->template_title = $data['general']['title'];
        $master_obj->attribute_json = json_encode($data);
        $master_obj->save();

        return response()->json(array('code' => 200, 'message' => 'ok'));

    }

    /**
     * 保存BI样式信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_bi_attr(Request $request)
    {

        $bi_uid = $request->input('uid');
        $data = $request->input('data');

        if (empty($bi_uid) || empty($data)) {
            return response()->json(array('code' => 400, 'message' => '参数错误'));
        }

        $where = ['module_uid', $bi_uid];
        $module_obj = BiTemplateDatabaseModuleClass::getList($where);
        if (!$module_obj) {
            return response()->json(array('code' => 10001, 'message' => '待编辑子模板不存在，请刷新'));
        }

        BiTemplateDatabaseModuleClass::save([
            'attribute_json' => json_encode($data)
        ], $bi_uid);

        return response()->json(array('code' => 200, 'message' => 'ok'));
    }

    //复制子报表
    public function copy(Request $request)
    {

        $uid = $request->input('uid');
        $top_percent = $request->input('top_percent');
        $height_percent = $request->input('height_percent');
        $top = $request->input('top');

        $copy_module_obj = BiTemplateDatabaseModuleClass::fetch($uid);

        if (!$copy_module_obj) {
            return response()->json(array('code' => 100002, 'message' => '复制的子模板不存在'));
        }

        $attribute_json = json_decode($copy_module_obj['attribute_json'], true);
        $attribute_json['top'] = $top;
        $attribute_json['top_percent'] = $top_percent;
        $attribute_json['height_percent'] = $height_percent;

        $module_uid = makeUuid();

        $db_json = json_decode($copy_module_obj['db_json'], true);
        $db_json['view_id'] = $copy_module_obj['view_id']; //修改复制的BI视图ID

        //生成子模块ID
        BiTemplateDatabaseModuleClass::save([
            'created_at' => date('Y-m-d H:i:s'),
            'creator' => 'system',
            'updated_at' => date('Y-m-d H:i:s'),
            'module_uid' => $module_uid,
            'template_id' => $copy_module_obj['template_id'],
            'view_id' => $copy_module_obj['view_id'],
            'bi_json' => $copy_module_obj['bi_json'],
            'db_json' => json_encode($db_json),
            'attribute_json' => json_encode($attribute_json),
            'chart_json' => $copy_module_obj['chart_json']
        ]);

        return response()->json(array(
            'code' => 200,
            'message' => 'ok',
            'uid' => $module_uid,
            'module' => array(
                'bi_json' => json_decode($copy_module_obj['bi_json'], true),
                'attribute_json' => $attribute_json,
                'chart_json' => json_decode($copy_module_obj['chart_json'], true)
            ),
            'callback_fun' => $request->input('callback_fun')
        ));

    }

    //创建子报表
    public function module(Request $request)
    {

        $chart_id = $request->input('chart_id');
        $template_id = $request->input('bi_id');
        $modul_data = $request->input('modul_data');

        if (empty($chart_id) || empty($template_id)) {
            return response()->json(array('code' => 400, 'message' => '参数错误'));
        }

        //检查BI主表
        $master_obj = BiTemplateDatabaseClass::fetch($template_id);
        if (!$master_obj) {
            return response()->json(array('code' => 400, 'message' => 'BI主表不存在，请刷新'));
        }

        //检查类型主表
        $chart_master_obj = BiChartMasterClass::fetch($chart_id);
        if (!$chart_master_obj) {
            return response()->json(array('code' => 400, 'message' => '图表不存在，请刷新'));
        }

        $chart_group_obj = BiChartGroupClass::fetch($chart_master_obj['group_id']);
        if (!$chart_group_obj) {
            return response()->json(array('code' => 400, 'message' => '图表类型分组不存在，请刷新'));
        }

        $modul_data = json_decode($modul_data, true);

        $modul_data['bi_json']['type'] = $chart_group_obj['group_code'];
        $modul_data['bi_json']['chart_json'] = json_decode($chart_master_obj['stringify'], true);

        $db_json = $this->db_json($chart_group_obj['group_code']);//生成db_json结构数据

        $module_uid = makeUuid();

        //生成子模块ID
        BiTemplateDatabaseModuleClass::save([
            'created_at' => date('Y-m-d H:i:s'),
            'creator' => 'system',
            'updated_at' => date('Y-m-d H:i:s'),
            'module_uid' => $module_uid,
            'view_id' => $db_json['view_id'],
            'template_id' => $template_id,
            'bi_json' => json_encode($modul_data['bi_json']),
            'db_json' => json_encode($db_json),
            'attribute_json' => json_encode($modul_data['attribute_json']),
            'chart_json' => json_encode($modul_data['chart_json']),
        ]);

        return response()->json(array(
            'code' => 200,
            'message' => 'ok',
            'uid' => $module_uid,
            'db_json' => $db_json,
            'bi_json' => $modul_data['bi_json'],
            'callback_fun' => $request->input('callback_fun')
        ));

    }


    /**
     * @param $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function replace_module(Request $request)
    {
        $chart_id = $request->input('chart_id');
        $uid = $request->input('uid');

        if (empty($chart_id) || empty($uid)) {
            return response()->json(array('code' => 400, 'message' => '参数错误'));
        }

        $module_obj = BiTemplateDatabaseModuleClass::fetch($uid);
        if (!$module_obj) {
            return response()->json(array('code' => 10001, 'message' => '待编辑报表不存在，请刷新'));
        }

        //检查类型主表
        $chart_master_obj = BiChartMasterClass::fetch($chart_id);
        if (!$chart_master_obj) {
            return response()->json(array('code' => 400, 'message' => '图表不存在，请刷新'));
        }

        $chart_group_obj = BiChartGroupClass::fetch($chart_master_obj['group_id']);
        if (!$chart_group_obj) {
            return response()->json(array('code' => 400, 'message' => '图表类型分组不存在，请刷新'));
        }

        $bi_json = array(
            'type' => $chart_group_obj->group_code,
            'chart_json' => json_decode($chart_master_obj->stringify)
        );

        $module_obj->updated_at = Carbon::now();
        $module_obj->bi_json = json_encode($bi_json);
        //暂定
        $module_obj->save();

        return response()->json(array(
            'code' => 200,
            'message' => 'ok',
            'data' => $bi_json,
            'callback_fun' => $request->input('callback_fun')
        ));

    }

    //删除子报表
    public function del_module($uid, Request $request)
    {

        $bi_chart_module_obj = BiTemplateDatabaseModuleClass::fetch($uid);

        if (!$bi_chart_module_obj) {
            return response()->json(array('code' => 100001, 'message' => '子模板已删除，请刷新'));
        }

        $bi_chart_module_obj->delete();

        return response()->json(array('code' => 200, 'message' => '删除成功', 'callback' => $request->input('callback_fun')));

    }

    //获取BI子模板信息
    public function get_bi($uid, Request $request)
    {

        $module_obj = BiTemplateDatabaseModuleClass::fetch($uid);
        if (!$module_obj) {
            return response()->json(array('code' => 100009, 'message' => '该子模板不存在，请刷新'));
        }

        $data = array(
            'bi_id' => $module_obj->template_id,
            'bi_json' => json_decode($module_obj->bi_json, true),
            'db_json' => json_decode($module_obj->db_json, true),
            'attribute_json' => json_decode($module_obj->attribute_json, true),
            'callback_fun' => $request->input('callback_fun')
        );

        return response()->json(array('code' => 200, 'message' => 'ok', 'data' => $data));

    }

    public function grouping()
    {
        return view('webi/backend/templateDB/group');

    }

    public function s_grouping(Request $request)
    {
        $grouping = $request->input('grouping', '');

        $where = ['group_name' => $grouping];
        $BiGroup = BiTemplateGroupClass::getList($where);
        if ($BiGroup['count']) {
            return response()->json(['code' => 10004, 'message' => '该分组已存在']);
        }

        BiTemplateGroupClass::save([
            'group_name' => $grouping
        ]);

        return response()->json(['code' => 200, 'message' => '保存成功']);
    }

    public function del_grouping($group_id)
    {

        $BiGroup = BiTemplateGroupClass::fetch($group_id);
        if (!$BiGroup) {
            return response()->json(array('code' => 100001, 'message' => '该分组不存在'));
        }

        try {

            DB::beginTransaction();

            DB::table('bi_template_database')->update(['group_id' => 0]);

            BiTemplateGroupClass::del($group_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    public function g_search(Request $request)
    {
        $where = [];

        if ($request->input('trueName')) {
            $where[] = ['group_name', 'like', '%' . $request->input('trueName') . '%'];
        }

//        $sdk_data = BiTemplateGroup::where($where)
//            ->orderBy('group_id', 'desc')
//            ->paginate($request->input('limit'))
//            ->toArray();
        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'group_id',
            'sort' => 'DESC'
        ];
        $sdk_data = BiTemplateGroupClass::getList($where, $limit);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => isset($sdk_data['total']) ? $sdk_data['total'] : 0,
            'data' => array()
        );


        if ($sdk_data['count'] > 0) {

            foreach ($sdk_data['data'] as $data) {
                $return_data['data'][] = array(
                    'group_name' => $data['group_name'],
                    '_id' => $data['_id']
                );
            }
        }

        return $return_data;
    }

    public function group_list()
    {

//        $biTemplateGroup =[];
//        $biTemplateGroup = BiTemplateGroup::orderBy('group_id','ASC')
//            ->get()
//            ->toArray();
        $limit = [
            'page' => 1,
            'pageSize' => 99,
            'orderBy' => 'group_id',
            'sort' => 'ASC'
        ];
        $biTemplateGroup = BiTemplateGroupClass::getList([], $limit);

        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $biTemplateGroup['data']]);
    }

    /**
     * 更新BI数据集模块信息
     * @param Request $request
     */
    public function save_all_module(Request $request)
    {

        $module_data = $request->input('master_module');

        if (empty($module_data)) {
            return response(['code' => 400, 'msg' => '数据不可为空']);
        }

        $module_data = json_decode($module_data, true);

        foreach ($module_data as $module_uid => $module) {

//            $module_obj = BiTemplateDatabaseModule::find($module_uid);
            $module_obj = BiTemplateDatabaseModuleClass::fetch($module_uid);
            if (!$module_obj) {
                continue;
            }

            $module_obj->updated_at = Carbon::now();
            $module_obj->attribute_json = json_encode($module['attribute_json']);
            $module_obj->save();

        }

        return response(['code' => 200, 'msg' => 'ok']);
    }

    /**
     * 生成db_json结构数据
     */
    public static function db_json($type)
    {
        $view_id = '5efdb09cba8df433c02784cf';//视图ID
        $row = "";
        $col = "";
        $sum = "";
        $sort = "";
        $where = "";
        $view_table = [];

        switch ($type) {
            case 'bi_text':    //文本
                $sum = "dt_cust_rank.money:消费金额";
                break;
            case 'bar':        //柱状图
                $row = "dt_cust_rank.rank_name:会员等级";
                $col = "dt_cust_rank.person_num:人数,dt_cust_rank.bill_num:订单数";
                break;
            case 'line':       //折线图
                $row = "dt_cust_rank.rank_name:会员等级";
                $col = "dt_cust_rank.money:消费金额";
                break;
            case 'pie':        //饼图
                $row = "dt_cust_rank.rank_name:会员等级";
                $col = "dt_cust_rank.money:消费金额";
                break;
            case 'map':        //地图
            case 'wordCloud':  //词云
                $view_id = "5efdb09cba8df433c02784d6";
                $row = "dt_region.province_name:区域名称";
                $col = "dt_region.sale_amount:销售量";
                break;
            case 'scatter':    //散点图
                $row = "dt_cust_rank.person_num:人数";
                $col = "dt_cust_rank.bill_num:订单数";
                break;
            case 'funnel':     //漏斗图
                $row = "dt_cust_rank.rank_name:会员等级";
                $col = "dt_cust_rank.money:消费金额";
                break;
            case 'Gauge':      //仪表盘
                $sum = "dt_cust_rank.money:消费金额";
                break;
            default:          //表格
                $col = "dt_cust_rank.rank_name:会员等级,dt_cust_rank.person_num:人数,dt_cust_rank.bill_num:订单数,dt_cust_rank.money:消费金额,dt_cust_rank.unit_price:客单价";
        }

        $view_data = WeBIGlobalController::get_view_table($view_id);
        if ($view_data['code'] == 200) {
            foreach ($view_data['data'] as $n => $f) {
                $view_table[$n] = [
                    'desc' => $f['desc'],
                    'field' => $f['field'],
                    'table_name' => $f['table_name'],
                ];
            }
        }

        $db_json = [
            'view_id' => $view_id,
            'view_table' => $view_table,
            'row' => $row,
            'column' => $col,
            'sum' => $sum,
            'limit' => '20',
            'sort' => $sort,
            'where' => $where
        ];

        return $db_json;
    }

    /**
     * 保存BI_CHART属性设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chart_save(Request $request)
    {

        $uid = $request->input('uid');
        $data = $request->input('data');

        if (empty($uid) || empty($data)) {
            return response()->json(array('code' => 400, 'message' => '参数错误'));
        }

//        $module_obj = BiTemplateDatabaseModule::where('module_uid',$uid)->first();
        $module_obj = BiTemplateDatabaseModuleClass::fetch($uid);
        if (!$module_obj) {
            return response()->json(array('code' => 10001, 'message' => '待编辑子报表不存在，请刷新'));
        }

        $module_obj->updated_at = Carbon::now();
        $module_obj->chart_json = json_encode($data);
        $module_obj->save();

        return response()->json(array('code' => 200, 'message' => 'ok'));
    }
}
