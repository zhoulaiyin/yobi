<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\Document\DocOperateManualGroupClass;
use App\Models\Classes\Console\Document\DocOperateManualGroupItemClass;
use App\Models\Console\Document\DocOperateManualGroupItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;


class OperationDocumentController extends Controller
{
    public function index()
    {
        return view('document/operation/groupList');
    }


    /**
     * 查询
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $where = [];

        if ($request->input('group_name')) {
            $where[] = ['group_name', 'like', '%' . $request->input('group_name') . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'sort_order',
            'sort' => 'ASC'
        ];
        $sdk_data = DocOperateManualGroupClass::getList($where, $limit);

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
                    'group_id' => $data['_id'],
                    'sort_order' => $data['sort_order'],
                    'group_name' => $data['group_name'],
                    'sort_arr' => array(
                        'group_id' => $data['_id'],
                        'sort_order' => $data['sort_order']
                    )
                );

            }

        }
        return $return_data;

    }


    /**
     * 添加、编辑
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($_id)
    {
        if ($_id == 0) {
            $data['title'] = '新增分组';
        } elseif ($_id > 0) {
            $data['title'] = '编辑分组';
        }

        $edit_data = [];
        if ($_id > 0) {
            $edit_data = DocOperateManualGroupClass::fetch($_id);
        }

        $column = DB::raw('MAX(sort_order)');
        $sort_order = DocOperateManualGroupClass::getList([], [], $column);

        $data['sort_order'] = json_encode($sort_order['data'][0]['sort_order'] + 1);
        $data['edit_data'] = $edit_data;
        $data['group_id'] = $_id;
        $data['_id'] = $_id;

        return view('document/operation/groupEdit', $data);

    }


    /**
     * 保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $args = $request->all();

        if (!$args['group_name']) {
            return response()->json(['code' => 10001, 'message' => '分组名称不能为空']);
        }
        if (!$args['sort_order']) {
            return response()->json(['code' => 10002, 'message' => '排序值不能为空']);
        }
        if (!is_numeric($args['sort_order'])) {
            return response()->json(['code' => 10003, 'message' => '排序值必须为数字']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['group_id', '!=', $args['group_id']]];
        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'table_id',
            'sort' => 'ASC'
        ];
        $order_data = DocOperateManualGroupClass::getList($where, $limit);

        if ($order_data['data']) {
            return response()->json(['code' => 10004, 'message' => '更新失败,此排序值已存在!']);
        }

        $Performance = DocOperateManualGroupClass::fetch($args['_id']);

        if (!$Performance) {
            //新增
            $data = DocOperateManualGroupClass::save([
                'group_name' => $args['group_name'],
                'sort_order' => $args['sort_order']
            ]);
            DocOperateManualGroupClass::save([
                'group_id' => $args['_id']
            ], $data['data']);
        } else {
            //修改
            DocOperateManualGroupClass::save([
                'group_id' => $args['_id'],
                'group_name' => $args['group_name'],
                'sort_order' => $args['sort_order']
            ], $args['_id']);
        }

        return response()->json(['code' => 200, 'message' => '保存成功']);

    }


    /**
     * 开发计划清单列表删除
     * @param $task_id
     * @param Request @return array|\Illuminate\Http\JsonResponse
     */
    public function del($_id)
    {

        $plan_task = DocOperateManualGroupClass::fetch($_id);

        if (!$plan_task) {
            return response()->json(['code' => 100000, 'message' => '该分组不存在或已被删除，请重试']);
        }

        try {

            DocOperateManualGroupClass::del($_id);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'ok', 'code' => 200]);

    }


    /**
     * 项目详情列表页保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function group_save(Request $request)
    {
        $args = $request->all();

        $Performance = DocOperateManualGroupClass::fetch($args['_id']);
        if (!$Performance) {
            return response()->json(['code' => 10001, 'message' => '更新失败,此项项目详细不存在!']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['group_id', '!=', $args['group_id']]];
        $column = 'sort_order';
        $order_data = DocOperateManualGroupClass::getList($where, [], $column);

        if ($order_data['count']) {
            return response()->json(['code' => 10002, 'message' => '更新失败,此排序值已存在!']);
        }

        if (!empty($args['sort_order'])) {
            $Performance['sort_order'] = $args['sort_order'];
        }

        $Performance->save();

        return response()->json(['code' => 200, 'message' => '更新成功']);

    }

    /**
     * 开发计划清单列表页
     * @param Request @return array|\Illuminate\Http\JsonResponse
     */
    public function task()
    {

        $column = 'group_id，group_name';
        $Docoperategroup = DocOperateManualGroupClass::getList([], [], $column);

        return view('document/operation/itemList')->with('DocGroup', json_encode($Docoperategroup['data']));

    }


    /**
     * 开发计划清单列表删除
     * @param $task_id
     * @param Request @return array|\Illuminate\Http\JsonResponse
     */
    public function task_del($task_id)
    {

        $plan_task = DocOperateManualGroupItemClass::fetch($task_id);

        if (!$plan_task) {
            return response()->json(['code' => 100000, 'message' => '此条项目详情不存在']);
        }

        try {

            DocOperateManualGroupItemClass::del($task_id);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'ok', 'code' => 200]);

    }


    /**
     * 开发计划清单查询
     * @param Request $request
     * @return array
     */
    public function task_search(Request $request)
    {
        $where = [];

        if ($request->input('item_name')) {
            $where[] = ['item_name', 'like', '%' . $request->input('item_name') . '%'];
        }
        if ($request->input('group_id')) {
            $where[] = ['group_id', $request->input('group_id')];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'sort_order',
            'sort' => 'DESC'
        ];
        $sdk_data = DocOperateManualGroupItemClass::getList($where, $limit);

        $return_data = array(
            'code' => 0,
            'msg' => '',
            'count' => isset($sdk_data['count']) ? $sdk_data['count'] : 0,
            'data' => array()
        );

        if ($sdk_data['count'] > 0) {

            foreach ($sdk_data['data'] as $data) {

                $where = ['group_id' => $data['group_id']];
                $item = DocOperateManualGroupClass::getList($where, [], 'group_name');

                $return_data['data'][] = array(
                    '_id' => $data['_id'],
                    'item_id' => $data['_id'],
                    'sort_order' => $data['sort_order'],
                    'group_name' => $item['data'][0]['group_name'],
                    'item_name' => $data['item_name'],
                    'sort_arr' => array(
                        'item_id' => $data['_id'],
                        'sort_order' => $data['sort_order']
                    )
                );

            }

        }
        return $return_data;
    }


    /**
     * 编辑开发计划清单
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function task_edit($flow_id)
    {
        $http_url = $_SERVER['HTTP_HOST'];//获取域名

        $sort_order = DB::table('doc_operate_manual_group_item')->max('sort_order');

		$limit = [
			'pageSize' => 999,
            'orderBy' => 'group_id',
            'sort' => 'DESC'
        ];
        $column = 'group_id，group_name';
        $group = DocOperateManualGroupClass::getList([], $limit, $column);

        $data = [
            'flow_id' => $flow_id,
            'group' => json_encode($group['data']),
            'sort_order' => $sort_order+1,
            'http_url' => json_encode($http_url),
        ];

        return view('document/operation/itemEdit', $data);
    }

    public function get($flow_id)
    {
        $data['sound_code'] = '';
        $data['id'] = $flow_id;

        $flow = DocOperateManualGroupItemClass::fetch($flow_id);

        if ($flow) {
            $data['sound_code'] = $flow['sound_code'];
            $data['group_id'] = $flow['group_id'];
            $data['item_name'] = $flow['item_name'];
            $data['sort_order'] = $flow['sort_order'];
        }

        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $data]);

    }


    /**
     * 开发计划清单保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function task_save(Request $request)
    {
        $args = $request->all();

		//主键id获取失败
        $flow_id = $args['flow_id'];
        $markdown = $args['markdown'];
        $html = $args['html'];

        if (empty($markdown)) {
            return response()->json(['code' => 10001, 'message' => '缺少文档源码']);
        }
        if (!$args['group_id']) {
            return response()->json(['code' => 10002, 'message' => '分组ID不能为空']);
        }
        if (!$args['item_name']) {
            return response()->json(['code' => 10003, 'message' => '项目名称不能为空']);
        }
        if (!$args['sort_order']) {
            return response()->json(['code' => 10004, 'message' => '排序值不能为空']);
        }
        if (!is_numeric($args['sort_order'])) {
            return response()->json(['code' => 10005, 'message' => '排序值必须为数字']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['item_id', '!=', $flow_id]];
        $column = 'sort_order';
        $order_data = DocOperateManualGroupItemClass::getList($where, [], $column);

        if ($order_data['count']) {
            return response()->json(['code' => 10005, 'message' => '更新失败,此排序值已存在!']);
        }

        $_id = null;
        $flow_chart = DocOperateManualGroupItemClass::fetch($flow_id);
        if ($flow_chart) {
            $_id = $flow_id;
        }

        try {
            DocOperateManualGroupItemClass::save([
                'group_id' => $args['group_id'],
                'item_name' => $args['item_name'],
                'sort_order' => $args['sort_order'],
                'sound_code' => htmlspecialchars($markdown),
                'doc_html' => '<div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">' . $html . '</div>'
            ], $_id);
        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $e->getMessage()
            );
        }

        return response()->json(['code' => 200, 'message' => '流程图保存成功']);
    }


    /**
     * 项目详情列表页保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function list_save(Request $request)
    {
        $args = $request->all();
        $Performance = DocOperateManualGroupItemClass::fetch($args['item_id']);
        if (!$Performance) {
            return response()->json(['code' => 10001, 'message' => '更新失败,此项项目详细不存在!']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['item_id', '!=', $args['item_id']]];
        $column = 'sort_order';
        $order_data = DocOperateManualGroupItemClass::getList($where, [], $column);

        if ($order_data['count']) {
            return response()->json(['code' => 10002, 'message' => '更新失败,此排序值已存在!']);
        }

        if (!empty($args['sort_order'])) {
            $Performance['sort_order'] = $args['sort_order'];
        }

        $Performance->save();

        return response()->json(['code' => 200, 'message' => '更新成功']);
    }

}