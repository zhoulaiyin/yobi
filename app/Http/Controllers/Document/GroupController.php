<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Classes\Console\Document\DocGroupClass;
use App\Models\Classes\Console\Document\DocGroupItemClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis as Redis;
use DB;


class GroupController extends Controller
{
    public function index()
    {
        return view('document/group/groupList');
    }
//

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
            'pageSize' => $request->input('limit'),
            'orderBy' => 'sort_order',
            'sort' => 'ASC'
        ];
        $sdk_data = DocGroupClass::getList($where, $limit);

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
                    'group_name' => $data['group_name'],
                    'sort_order' => $data['sort_order']
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
    public function edit($user_id)
    {
        if ($user_id == 0) {
            $data['title'] = '新增分组';
        } elseif ($user_id > 0) {
            $data['title'] = '编辑分组';
        }
        $edit_data = [];
        if ($user_id > 0) {
            $edit_data = DocGroupClass::fetch($user_id);
        }
        $column = DB::raw('MAX(sort_order)');
        $sort_order = DocGroupClass::getList([], [], $column);

        $data['sort_order'] = json_encode($sort_order['data'][0]['sort_order'] + 1);
        $data['edit_data'] = $edit_data;
        $data['user_id'] = $user_id;
        $data['_id'] = $user_id;
        return view('document/group/groupEdit', $data);
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
        $column = 'sort_order';
        $order_data = DocGroupClass::getList($where, [], $column);
        if ($order_data['count']) {
            return response()->json(['code' => 10004, 'message' => '更新失败,此排序值已存在!']);
        }

        $Performance = DocGroupClass::fetch($args['_id']);

        if (!$Performance) {
            //新增
            DocGroupClass::save([
                'group_name' => $args['group_name'],
                'sort_order' => $args['sort_order']
            ]);
        } else {
            //修改
            DocGroupClass::save([
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
    public function del($user_id)
    {
        $plan_task = DocGroupClass::fetch($user_id);

        if (!$plan_task) {
            return response()->json(['code' => 100000, 'message' => '该分组不存在或已被删除，请重试']);
        }

        try {

            DocGroupClass::del($user_id);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'ok', 'code' => 200]);

    }

    /**
     * 开发计划清单列表页
     * @param $user_id
     * @param Request @return array|\Illuminate\Http\JsonResponse
     */
    public function task($user_id)
    {
        Redis::setex('WEBI_PLAN_ID' . session()->getId(), 86400, $user_id);

        return view('document/group/groupItemList');
    }

    /**
     * 开发计划清单列表删除
     * @param $task_id
     * @param Request @return array|\Illuminate\Http\JsonResponse
     */
    public function task_del($task_id)
    {
        $plan_task = DocGroupItemClass::fetch($task_id);

        if (!$plan_task) {
            return response()->json(['code' => 100000, 'message' => '此条项目详情不存在']);
        }

        try {

            DocGroupItemClass::del($task_id);

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
        $where['group_id'] = Redis::get('WEBI_PLAN_ID' . session()->getId());

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'sort_order',
            'sort' => 'DESC'
        ];
        $sdk_data = DocGroupItemClass::getList($where, $limit);

        $where = ['group_id' => Redis::get('WEBI_PLAN_ID' . session()->getId())];
        $column = 'group_name';

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
                    'user_id' => $data['_id'],
                    'item_id' => $data['_id'],
                    'sort_order' => '<a id="sort_order_' . $data['_id'] . '" href="javascript:" onclick="stat.openOrder(\'' . $data['sort_order'] . '\',' . $data['_id'] . ')">' . $data['sort_order'] . '</a>',
                    'item_name' => $data['item_name'],
                    'item_link' => $data['item_link']
                );

            }

        }
        return $return_data;
    }

    /**
     * 新增开发计划清单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function task_add()
    {
        $column = DB::raw('MAX(sort_order)');
        $sort_order = DocGroupItemClass::getList([], [], $column);

        $data['sort_order'] = json_encode($sort_order['data'][0]['sort_order'] + 1);
        return view('document/group/groupItemAdd', $data);
    }

    /**
     * 编辑开发计划清单
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function task_edit($user_id)
    {

        $edit_data = [];
        if ($user_id > 0) {
            $edit_data = DocGroupItemClass::fetch($user_id);

            $where = ['group_id' => Redis::get('WEBI_PLAN_ID' . session()->getId())];
            $column = 'group_name';
            $item = DocGroupClass::getList($where, [], $column);
        }

        $data['_id'] = $user_id;
        $data['edit_data'] = $edit_data;
//        $data['name'] = $item['data'][0]['group_name'];
        return view('document/group/groupItemEdit', $data);
    }

    /**
     * 开发计划清单保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function task_save(Request $request)
    {
        $args = $request->all();

        if (!$args['item_name']) {
            return response()->json(['code' => 10002, 'message' => '项目名称不能为空']);
        }
        if (!$args['item_link']) {
            return response()->json(['code' => 10003, 'message' => '项目链接不能为空']);
        }
        if (!$args['sort_order']) {
            return response()->json(['code' => 10004, 'message' => '排序值不能为空']);
        }
        if (!is_numeric($args['sort_order'])) {
            return response()->json(['code' => 10005, 'message' => '排序值必须为数字']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['item_id', '!=', $args['item_id']]];
        $column = 'sort_order';
        $order_data = DocGroupItemClass::getList($where, [], $column);

        if ($order_data['count']) {
            return response()->json(['code' => 10006, 'message' => '更新失败,此排序值已存在!']);
        }

        $Performance = DocGroupItemClass::fetch($args['item_id']);

        if (!$Performance) {
            //新增
            DocGroupItemClass::save([
                'item_name' => $args['item_name'],
                'item_link' => $args['item_link'],
                'sort_order' => $args['sort_order']
            ]);
        } else {
            //修改
            DocGroupItemClass::save([
                'item_name' => $args['item_name'],
                'item_link' => $args['item_link'],
                'sort_order' => $args['sort_order']
            ], $args['_id']);
        }

        return response()->json(['code' => 200, 'message' => '保存成功']);
    }

    /**
     * 项目详情列表页保存
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function list_save(Request $request)
    {
        $args = $request->all();
        $Performance = DocGroupItemClass::fetch($args['item_id']);
        if (!$Performance) {
            return response()->json(['code' => 10001, 'message' => '更新失败,此项项目详细不存在!']);
        }

        $where = [['sort_order', '=', $args['sort_order']], ['item_id', '!=', $args['item_id']]];
        $column = 'sort_order';
        $order_data = DocGroupItemClass::getList($where, [], $column);
        if ($order_data['count']) {
            return response()->json(['code' => 10002, 'message' => '更新失败,此排序值已存在!']);
        }


        if (!empty($args['sort_order'])) {
            $Performance['sort_order'] = $args['sort_order'];
        }

        DocGroupItemClass::save([
            'sort_order' => $args['sort_order']
        ]);

        return response()->json(['code' => 200, 'message' => '更新成功']);
    }

}