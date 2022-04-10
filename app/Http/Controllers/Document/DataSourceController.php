<?php

namespace App\Http\Controllers\Document;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Console\BiRule;
use App\Models\Classes\Console\BiRuleClass;
use App\Models\Classes\Console\BiRuleGroupClass;

class DataSourceController extends Controller
{

    //首页
    public function index()
    {
        $chart = BiRule::first();
        if (!$chart) {
            return redirect('error/doc?msg=数据集不存在');
        }

        $BIRuleGroup = BIRuleGroupClass::getList([], ['pageSize' => 999]);
        if (empty($BIRuleGroup['data'])) {
            return redirect('error/doc?msg=数据集不存在');
        }

        $flow_array = [];
        foreach ($BIRuleGroup['data'] as $group) {
            $rule = BiRuleClass::getList(['rule_group_id' => $group['group_id']], ['pageSize' => 999]);
            $flow_array[] = [
                'id' => $group['group_id'],
                'name' => $group['group_name'],
                'table' => !$rule['count'] ? [] : $rule['data']
            ];
        }

        //页面数据
        $data = [
            'group' => $flow_array
        ];

        return view('document/dataSource/showbi', $data);
    }


    //查询统计规则
    public function get($id)
    {
        $BiRule = BiRuleClass::fetch($id);
        if (!$BiRule) {
            return response()->json(['code' => 10000, 'message' => '数据集不存在']);
        }

        $return_data = array(
            'code' => 200,
            'message' => 'ok',
            'data' => array()
        );

        $frequency = [
            '0' => '',
            '1' => '一分钟',
            '2' => '五分钟',
            '3' => '十分钟',
            '4' => '半小时',
            '5' => '一小时',
            '6' => '凌晨0点',
            '7' => '凌晨1点',
            '8' => '凌晨3点'
        ];

        $BiRule['statistical_frequency'] = $frequency[$BiRule['statistical_frequency']];
        $BiRule['fields_json'] = json_decode($BiRule['fields_json'], true);
        $return_data['data'] = $BiRule;

        return response()->json($return_data);
    }

    //搜索统计规则
    public function search(Request $request)
    {
        $name = $request->input('name');

        $return_data = array(
            'code' => 200,
            'message' => 'ok',
            'data' => array()
        );

        $map_data = BiRule::select('table_id', 'rule_group_id')
            ->where('table_name', 'like', "%" . $name . "%")
            ->orWhere('description', 'like', "%" . $name . "%")
            ->orderBy('table_id', 'ASC')
            ->first();
        if (!$map_data) {
            return response()->json(['code' => 10003, 'message' => "不存在数据集！"]);
        }

        $return_data['data'] = $map_data;

        return response()->json($return_data);
    }

}