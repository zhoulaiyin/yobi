<?php

namespace App\Http\Controllers\Admin\Permission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Classes\Console\System\PermissionClass;
use App\Models\Classes\Console\System\PermissionGroupClass;

class PermissionController extends Controller
{

    /**
     * 权限列表页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $permission_group = [];

        $where = [['parent_id', '=', '0']];
        $column = '_id, group_name';
        $parent_permission_group = PermissionGroupClass::getList($where, [], $column);


        if ($parent_permission_group['count']) {
            foreach ($parent_permission_group['data'] as $key => $group) {
                $where = [['parent_id', '=', $group['_id']]];
                $temp_permission_group = PermissionGroupClass::getList($where, [], $column);
                $permission_group[$group['_id']] = empty($temp_permission_group['data']) ? [] : $temp_permission_group['data'];
            }
        }

        $data = [
            'parent_permission_group' => json_encode($parent_permission_group['data']),
            'permission_group' => json_encode($permission_group, JSON_UNESCAPED_SLASHES),
        ];

        return view('admin/permission/permission', $data);

    }

    /**
     * 查询权限列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {

        $id = $request->input('id');
        $permission_id = $request->input('permission_id');
        $parent_group_id = $request->input('parent_group_id');
        $permission_name = $request->input('permission_name');
        $permission_type = (int)$request->input('permission_type');
        $where = [];

        if (ebsig_is_int($permission_id)) {
            $where[] = ['permission_id', $permission_id];
        }
        if (ebsig_is_int($id)) {
            $where[] = ['id', $id];
        }
        if (ebsig_is_int($parent_group_id)) {
            $where[] = ['parent_group_id', $parent_group_id];
        }
        if (in_array($permission_type, [1, 2])) {
            $where[] = ['permission_type', $permission_type];
        }
        if (!empty($permission_name)) {
            $where[] = ['permission_name', 'like', '%' . $permission_name . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'id',
            'sort' => 'DESC'
        ];
        $permission = PermissionClass::getList($where, $limit);

        //返回数组
        $return_data = [
            'code' => 0,
            'msg' => '',
            'count' => isset($permission['count']) ? $permission['count'] : 0,
            'data' => array()
        ];

        if ($permission['count'] > 0) {

            foreach ($permission['data'] as $row) {

                $id = $row['id'];

                $group_name = '';

                $permission_group = PermissionGroupClass::fetch($row['parent_group_id']);

                if ($row['id'] > 0) {
                    if ($permission_group) {
                        $group_name = $permission_group['group_name'];
                    }
                }

                $return_data['data'][] = [
                    'operation' => '<a href="javascript:Permission.edit(' . $id . ');">修改</a>&nbsp;&nbsp;<a href="javascript:Permission.del(' . $id . ');">删除</a>',
                    'parent_group_id' => $permission_group['_id'],
                    'permission_id' => $row['permission_id'],
                    'permission_name' => $row['permission_name'],
                    'group_name' => $group_name,
                    'permission_type' => (string)$row['permission_type'],
                    'permission_type_name' => $row['permission_type'] == 1 ? '功能页面' : '功能编辑',
                    'permission_url' => $row['permission_url'],
                    '_id' => $row['_id'],
                    'id' => $id
                ];
            }
        }
        return response()->json($return_data);
    }

    /**
     * 查询权限信息
     * @param int $id 权限id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {

        //正常
        $permission = PermissionClass::fetch($id);

        if (!$permission) {
            return response()->json(['code' => 10001, 'message' => '权限不存在']);
        } else {
            return response()->json(['code' => 200, 'message' => 'ok', 'data' => $permission]);
        }

    }

    /**
     * 保存权限数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $_id = $request->input('_id', '');
        $id = $request->input('id');
        $group_name = $request->input('group_name');
        $permission_id = $request->input('permission_id');
        $permission_url = $request->input('permission_url');
        $permission_type = $request->input('permission_type');
        $permission_name = $request->input('permission_name');
        $parent_group_id = $request->input('parent_group_id');

        if (!ebsig_is_int($id)) {
            $id = 0;
        }
        if (!ebsig_is_int($permission_id)) {
            return response()->json(['code' => 10000, 'message' => '权限ID不能为空']);
        }

        if (!ebsig_is_int($id)) {
            return response()->json(['code' => 10001, 'message' => '请选择权限组']);
        }

        if (empty($permission_name)) {
            return response()->json(['code' => 10002, 'message' => '权限名称不能为空']);
        }

        if (empty($permission_url)) {
            return response()->json(['code' => 10003, 'message' => '权限URL不能为空']);
        }

        if (!in_array($permission_type, [1, 2])) {
            $permission_type = 1;
        }

        //检查权限组/
        $parent_permission_group = PermissionGroupClass::fetch($parent_group_id);

        if (!$parent_permission_group || $parent_permission_group['parent_id'] > 0) {
            return response()->json(['code' => 10004, 'message' => '一级权限组不存在']);
        }

        $permission_group = PermissionClass::fetch($_id);
        if (!$permission_group || $permission_group['parent_id'] != $parent_group_id) {
            return response()->json(['code' => 10005, 'message' => '二级权限组不存在']);
        }

        //权限id是否在允许的范围
        $start_id = intval($permission_group['permission_prefix'] . '000');
        $end_id = intval($permission_group['permission_prefix'] . '999');
        if ($permission_id < $start_id || $permission_id > $end_id) {
            return response()->json(['code' => 10006, 'message' => '权限组【' . $permission_group['group_name'] . '】下的权限ID必须在' . $start_id . '~' . $end_id . '之间']);
        }

        //权限类型为“功能页面”，权限id必须能整除5
        if ($permission_type == 1 && $permission_id % 5 != 0) {
            return response()->json(['code' => 10007, 'message' => '权限类型为功能页面，权限ID必须能整除5']);
        }

        //判断权限id是否已使用
        $where = ['permission_id' => $permission_id];
        $column = 'id';
        $check_permission = PermissionClass::getList($where, [], $column);
        if ($check_permission['data'] && $check_permission['data'][0]['id'] != $id) {
            return response()->json(['code' => 10008, 'message' => '权限ID已被其他权限使用']);
        }

        $permission = PermissionClass::fetch($_id);
        if ($id > 0) {
            if (!$permission) {
                return response()->json(['code' => 10009, 'message' => '权限不存在']);
            }
        }

        if (!$permission) {
            //新增
            PermissionClass::save([
                'group_name' => $group_name,
                'permission_id' => $permission_id,
                'permission_name' => $permission_name,
                'permission_url' => $permission_url,
                'parent_group_id' => $parent_group_id,
                'id' => $id,
                'permission_type' => $permission_type
            ]);
        } else {
            //修改
            PermissionClass::save([
                'group_name' => $group_name,
                'permission_id' => $permission_id,
                'permission_name' => $permission_name,
                'permission_url' => $permission_url,
                'parent_group_id' => $parent_group_id,
                'id' => $id,
                'permission_type' => $permission_type
            ], $_id);
        }

        return response()->json(['code' => 200, 'message' => 'OK']);

    }

    /**
     * 删除权限
     * @param int $id 权限id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {

        PermissionClass::del($id);

        return response()->json(['code' => 200, 'message' => 'OK']);

    }

}
