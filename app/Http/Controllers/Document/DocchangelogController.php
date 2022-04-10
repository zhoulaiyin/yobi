<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

use App\Models\Classes\Console\Document\DocChangeLogClass;

class DocchangelogController extends Controller
{
    public function index()
    {
        return view('document/changelog/changeList');
    }

    /**
     * 查询
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $where = [];

        if ($request->input('version')) {
            $where[] = ['v', 'like', '%' . $request->input('v') . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit'),
            'orderBy' => 'log_id',
            'sort' => 'ASC'
        ];
        $sdk_data = DocChangeLogClass::getList($where, $limit);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => isset($sdk_data['count']) ? $sdk_data['count'] : 0,
            'data' => array()
        );

        if ($sdk_data['count'] > 0) {

            foreach ($sdk_data['data'] as $data) {

                $return_data['data'][] = array(
                    '_id' => $data['_id'],
                    'log_id' => $data['_id'],
                    'v' => $data['v'],
                    'editor' => $data['editor'],
                    'change_date' => json_decode($data['change_date'])
                );

            }

        }
        return $return_data;

    }

    public function edit($flow_id)
    {

        $http_url = $_SERVER['HTTP_HOST'];//获取域名
        $edit_data = DocChangeLogClass::fetch($flow_id);

        $data = [
            '_id' => $flow_id,
            'flow_id' => $flow_id,
            'http_url' => json_encode($http_url)
        ];
        $data['edit_data'] = $edit_data;

        return view('document/changelog/businessflow', $data);

    }

    public function get($flow_id)
    {

        $data['sound_code'] = '';

        $flow = DocChangeLogClass::fetch($flow_id);

        if ($flow) {
            $data['_id'] = $flow_id;
            $data['sound_code'] = $flow['sound_code'];
            $data['v'] = $flow['v'];
            $data['change_date'] = $flow['change_date'];
            $data['editor'] = $flow['editor'];
        }

        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $data]);

    }

    public function save(Request $request)
    {

        $_id = $request->input('_id');
        $version = $request->input('version');
        $markdown = $request->input('markdown');
        $html = $request->input('html');
        $change_date = $request->input('change_date');
        $editor = $request->input('editor');

        if (empty($version)) {
            return response()->json(['code' => 10001, 'message' => '缺少版本号']);
        }
        if (empty($markdown)) {
            return response()->json(['code' => 10002, 'message' => '缺少文档源码']);
        }
        if (empty($change_date)) {
            return response()->json(['code' => 10003, 'message' => '更新时间不能为空']);
        }
        if (empty($editor)) {
            return response()->json(['code' => 10004, 'message' => '操作人不能为空']);
        }

        try {

            $flow_chart = DocChangeLogClass::fetch($_id);

            if (!$flow_chart) {
                //新增
                $data = DocChangeLogClass::save([
                    'v' => $version,
                    'editor' => $editor,
                    'change_date' => $change_date,
                    'sound_code' => htmlspecialchars($markdown),
                    'doc_html' => '<div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">' . $html . '</div>'
                ]);

                DocChangeLogClass::save([
                    'log_id' => $data['data']
                ], $data['data']);
            } else {
                //修改
                DocChangeLogClass::save([
                    'v' => $version,
                    'editor' => $editor,
                    'change_date' => $change_date,
                    'sound_code' => htmlspecialchars($markdown),
                    'doc_html' => '<div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">' . $html . '</div>'
                ], $_id);
            }

        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $e->getMessage()
            );
        }

        return response()->json(['code' => 200, 'message' => '流程图保存成功']);

    }
}
