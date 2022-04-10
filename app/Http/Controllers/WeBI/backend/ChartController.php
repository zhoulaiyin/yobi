<?php

namespace App\Http\Controllers\WeBI\backend;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\BiChartGroupClass;
use App\Models\Classes\Console\BiChartMasterClass;
use App\Models\Classes\Console\Document\DocGroupClass;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis as Redis;

class ChartController extends Controller
{
    //图标类型维护列表
    public function index()
    {
        $data = [];
        $group_id = "";
        $master = "";
        $group_already = [];
        $code_already = [];

        $limit = [
            'pageSize' => 999,
            'orderBy' => 'group_id',
            'sort' => 'ASC'
        ];
        $column = 'group_id，group_name，group_code';
        $group_data = BiChartGroupClass::getList([], $limit, $column);


        if ($group_data['count']) {
            $group_id = $group_data['data'][0]['_id'];

            $where = ['group_id' => $group_id];
            $master = BiChartMasterClass::getList($where, $limit, 'chart_id, chart_title, photo_link');

            foreach ($group_data['data'] as $v) {
                array_push($group_already, $v['group_name']);
                array_push($code_already, $v['group_code']);
            }
        }

        $data['code_already'] = json_encode($code_already);
        $data['group_already'] = json_encode($group_already);
        $data['group_id'] = $group_id;
        $data['group'] = $group_data['data'];
        $data['master'] = $master['data'];

        return view('webi/backend/chart/list', $data);

    }

    //图标类型分组列表
    public function groupList($group_id)
    {
        $data = [];

        $findStatus = BiChartGroupClass::fetch($group_id);
        if (!$findStatus) {
            return response()->json(array('code' => 10001, 'msg' => '无此分组，请刷新页面重试！'));
        }

        $where = ['group_id' => $group_id];
        $column = 'chart_id, chart_title, photo_link';
        $map_data = BiChartMasterClass::getList($where, [], $column);


        $data['master'] = $map_data['data'];
        $data['group_id'] = $group_id;
        return response()->json(array('code' => 200, 'msg' => $data));
    }

    //图标类型分组查询
    public function inquiry($group_id)
    {

        $statusGroup = BiChartGroupClass::fetch($group_id);

        if (!$statusGroup) {
            return response()->json(['code' => 400, 'message' => '该分组不存在，请刷新页面！']);
        }
        $data['group_code'] = $statusGroup['group_code'];
        $data['group_name'] = $statusGroup['group_name'];
        return response()->json(['code' => 200, 'message' => $data]);
    }

    //图标类型分组保存
    public function save(Request $request)
    {

        $type = $request->input('type');
        $group_name = $request->input('title');
        $group_code = $request->input('group_code');
        $group_id = $request->input('s');
        if (empty($group_name)) {
            return response()->json(array('code' => 10002, 'message' => '请输入图表分组名'));
        }
        if (empty($group_code)) {
            return response()->json(array('code' => 10002, 'message' => '请输入分类代码'));
        }
        $chartgroup = BiChartGroupClass::fetch($group_id);

        if (!$chartgroup) {//新增
            $data = BiChartGroupClass::save([
                'group_name' => $group_name,
                'group_code' => $group_code
            ]);
            BiChartGroupClass::save([
                'group_id' => $data['data']
            ], $data['data']);
        } else {
            BiChartGroupClass::save([
                'group_name' => $group_name,
                'group_code' => $group_code
            ], $group_id);

        }

        return response()->json(['code' => 200, 'message' => "保存成功！", 'id' => $group_id]);

    }

    //图标类型维护新增、编辑  0 增加 其他是修改
    public function edit($id, $type)
    {
        $data['type'] = $type;
        $data['id'] = $id;//主键id
        $data['c_id'] = 0;
        $title = "新增图表类型维护";

        if ($type != 0) {

            $map_data = BiChartMasterClass::fetch($id);
            if (!$map_data) {
                return response()->json(['code' => 10003, 'message' => "无此分组，请刷新页面！"]);
            }
            $data['chart_title'] = $map_data['chart_title'];
            $data['photo_link'] = $map_data['photo_link'];
            $data['chart_json'] = $map_data['chart_json'];
          
            $title = "编辑图表类型维护";
            $data['c_id'] = $map_data['_id'];//编辑下的group_id
        }

        $data['title'] = $title;
        return view('webi/backend/chart/edit', $data);
    }

    //图标类型维护保存
    public function chartSave(Request $request)
    {
        $type = $request->input('type');
        $g_id = $request->input('g_id');
        $table_name = $request->input('table_name');
        $chart_json = $request->input('chart_json');
        $photo_link = $request->input('photo_link');
        $chartJson = $request->input('chartJson');

        if ($type == 0) {//增加
            BiChartMasterClass::save([
                'group_id' => $g_id,
                'chart_title' => $table_name,
                'photo_link' => $photo_link,
                'chart_json' => json_encode($chart_json),
                'stringify' => $chartJson
            ]);

        } else {

            $findStatus = BiChartMasterClass::fetch($g_id);
            if (!$findStatus) {
                return response()->json(['code' => 10005, 'message' => '不存在图表类型项目']);
            }

            //更新
            BiChartMasterClass::save([
                'chart_title' => $table_name,
                'photo_link' => $photo_link,
                'chart_json' => json_encode($chart_json),
                'stringify' => $chartJson
            ], $g_id);

        }

        return response()->json(['code' => 200, 'message' => '保存成功']);

    }

    //图标类型维护删除
    public function del($chart_id)
    {

        BiChartGroupClass::del($chart_id);

        return response()->json(['code' => 200, 'message' => '删除成功']);
    }

    //查询删除分组  分组下有无图表
    public function moveList($group_id)
    {
        $data = [];

        $group_map = BiChartGroupClass::fetch($group_id);
        if (!$group_map) {
            return response()->json(['code' => 10003, 'message' => '该分组不存在，请刷新页面！']);
        }

        $where = [['group_id', '=', $group_id]];
        $master_map = BiChartMasterClass::getList($where, [], 'chart_id');

        if (!$master_map) {
            BiChartGroupClass::del($group_id);
            return response()->json(['code' => 10004, 'message' => '']);
        }

        foreach($master_map['data'] as $item){
            BiChartMasterClass::del($item['_id']);
        }

        BiChartGroupClass::del($group_id);


        $where = [['group_id', '!=', $group_id]];
        $column = 'group_id, group_name';
        $group = BiChartGroupClass::getList($where, [], $column);

        $data['group'] = $group;
        return response()->json(['code' => 200, 'message' => $data['group']['data']]);

    }

    //删除分组
    public function moveSave(Request $request)
    {

        $group_id = $request->input('group');
        $move_g = $request->input('move_g');//将要删除的分组

        $where = [['group_id' => $move_g]];
        $column = 'group_code';
        $group_code = BiChartGroupClass::getList($where, [], $column);

        BiChartGroupClass::del($move_g);

        BiChartMasterClass::save(['group_id' => $group_id, 'updated_at' => Carbon::now()], $move_g);

        return response()->json(['code' => 200, 'message' => "操作成功", 'group_code' => $group_code['data'][0]['group_code']]);
    }

}
