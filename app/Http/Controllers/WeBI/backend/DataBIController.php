<?php
namespace App\Http\Controllers\WeBI\backend;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\BiRuleClass;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\EbsigHttp;
use Illuminate\Support\Facades\Redis as Redis;

class DataBIController extends Controller
{

    public function index() {
        return view('webi/backend/dataset/list');
    }

    public function get(Request $request) {

        $where = [];

        if ($request->input('description')) {
            $where[] = ['description', 'like', '%' . $request->input('description') . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'table_id',
            'sort' => 'ASC'
        ];
        $column = 'table_id, table_name, description, is_default';
        $map_data = BiRuleClass::getList($where, $limit, $column);

        $return_data = array(
            'code' => 0,
            'msg'  => '',
            'count' => isset($map_data['count']) ? $map_data['count'] : 0,
            'data' => array()
        );

        
        if ($map_data['count'] > 0) {
            foreach ($map_data['data'] as $data) {
                $return_data['data'][] = array(
                    '_id' => $data['_id'],
                    'table_id' => $data['_id'],
                    'table_name' => $data['table_name'],
                    'description' => $data['description']
                );
            }
        }

        return $return_data;
    }

    public function edit($id) {

        $data['id'] = $id;
        $title = '新增系统数据集';
        if ($id) {
            $title = '编辑系统数据集';
            $bi_rule = BiRuleClass::fetch($id);

            $bi_rule['fields_json'] = json_decode($bi_rule['fields_json'], true);
            $data['rule'] = $bi_rule;
            $data['rule_json'] = json_encode($bi_rule);
        }

        $data['title'] = $title;
        $data['_id'] = $id;
        return view('webi/backend/dataset/edit', $data);

    }

    public function save(Request $request) {

        $markdown = $request->input('sound_code');
        $html = $request->input('html');
        $dt = $request->input('dt');

        if ( empty($dt['table_name']) ) {
            return response()->json([ 'code' => 100002, 'message' => '请填写统计表名' ]);
        }

        if ( empty($dt['description']) ) {
            return response()->json([ 'code' => 100003, 'message' => '请填写描述' ]);
        }

        if ( $dt['statistical_frequency'] == '' ) {
            return response()->json([ 'code' => 100004, 'message' => '请选择统计频率' ]);
        }

        if ( empty($dt['request']) ) {
            return response()->json([ 'code' => 100005, 'message' => '请填写字段信息' ]);
        }

        if ( empty($markdown) || empty($html) ) {
            return response()->json([ 'code' => 100006, 'message' => '请填写规则说明' ]);
        }

        $ets_bi_tule = BiRuleClass::fetch($dt['_id']);

        if (!$ets_bi_tule) {
            BiRuleClass::save([
                'rule_group_id' => $dt['table_type'],
                'table_name' => $dt['table_name'],
                'description' => $dt['description'],
                'statistical_frequency' => $dt['statistical_frequency'],
                'fields_json' => $dt['request'],
                'sound_code' => htmlspecialchars($markdown),
                'doc_html' => '<div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">'.$html.'</div>',
                'is_default' => 1
            ]);
        }else {
            BiRuleClass::save([
                'rule_group_id' => $dt['table_type'],
                'table_name' => $dt['table_name'],
                'description' => $dt['description'],
                'statistical_frequency' => $dt['statistical_frequency'],
                'fields_json' => $dt['request'],
                'sound_code' => htmlspecialchars($markdown),
                'doc_html' => '<div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">'.$html.'</div>',
                'is_default' => 1
            ], $dt['_id']);
        }

        return response()->json([ 'code' => 200, 'message' => '保存成功' ]);

    }

    public function del($id) {

        BiRuleClass::del($id);

        return response()->json([ 'code' => 200 , 'message' => '删除成功' ]);

    }

    /**
     * 拉取表结构
     */
    public function getTable(Request $request) {

        $tablename = $request->input('tablename');

        set_time_limit(0);

        $post_data = array(
            'tablename' => $tablename
        );

        $http_opts = array(
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 60
        );

        $request_url = 'http://demo.ebsig.com/api/async/get.table.structure.php';

        $api_result = EbsigHttp::get($request_url.'?'.http_build_query($post_data), $http_opts);

        if ( $api_result['code'] != 200 ) {
            return response()->json(['code' => 404, 'message' => '没有拉取到表结构']);
        }

        if($api_result['data']['code'] != 200){
            return response()->json(['code' => 404, 'message' => '表不存在']);
        }

        return response()->json(['code' => 200, 'message' => '拉取微电汇数据源成功', 'data' => $api_result['data']['data']]);

    }
}