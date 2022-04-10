<?php

namespace App\Http\Controllers\Admin\Permission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Console\System\PermissionGroup;

use App\Models\Classes\Console\System\PermissionGroupClass;
use App\Models\Classes\Console\System\PermissionClass;

use DB;

class PermissionGroupController extends Controller
{

    /**
     * 权限组列表页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $where = [['parent_id', '=', '0']];
        $column = 'id, group_name';
        $permission_group = PermissionGroupClass::getList($where, [], $column);

        return view('admin/permission/permissionGroup')->with('permission_group', json_encode($permission_group['data']));

    }

    /**
     * 查询权限组列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {

        $id = $request->input('id');
        $group_name = $request->input('group_name');

        $where = [];

        if (ebsig_is_int($id)) {
            $where[] = ['id', $id];
        }
        if (!empty($group_name)) {
            $where[] = ['group_name', 'like', '%' . $group_name . '%'];
        }

        $limit = [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('limit', 10),
            'orderBy' => 'id',
            'sort' => 'DESC'
        ];
        $column = 'id, group_name';
        $permission_group = PermissionGroupClass::getList($where, $limit, $column);


        //返回数组
        $return = [
            'code' => 0,
            'msg' => '',
            'count' => isset($permission_group['count']) ? $permission_group['count'] : 0,
            'data' => array()
        ];

        if ($permission_group['count'] > 0) {

            foreach ($permission_group['data'] as $row) {

                $id = $row['_id'];

                //上级权限组名称
                $parent_group_name = '';
                if ($row['parent_id'] > 0) {
                    $temp_permission_group = PermissionGroupClass::fetch($row['_id']);
                    $where[] = ['id', $temp_permission_group['parent_id']];
                    $temp_permission_group1 = PermissionGroupClass::getList($where);
                    if ($temp_permission_group1['count']) {
                        $parent_group_name = $temp_permission_group1['data'][0]['group_name'];
                    }
                }

                //权限数量
                $where = [['parent_group_id', $id]];
                $permission = PermissionClass::getList($where);

                $return['data'][] = [
                    'operation' => '<a href="javascript:PermissionGroup.edit(' . $id . ');">修改</a>&nbsp;&nbsp;<a href="javascript:PermissionGroup.del(' . $id . ');">删除</a>',
//                    'id' => $id,
                    'id' => $row['id'],
                    'group_name' => $row['group_name'],
                    'parent_group_name' => $parent_group_name,
                    'permission_prefix' => $row['permission_prefix'],
                    'permission_num' => $permission['count'],
                    '_id' => $row['_id']
                ];
            }
        }

        return response()->json($return);

    }

    /**
     * 查询权限组信息
     * @param int $id 权限组id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {

        $permission_group = PermissionGroupClass::fetch($id);
        if (!$permission_group) {
            return response()->json(['code' => 10001, 'message' => '权限组不存在']);
        } else {
            return response()->json(['code' => 200, 'message' => 'ok', 'data' => $permission_group]);
        }

    }

    /**
     * 保存权限组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $_id = $request->input('_id', '');
        $id = $request->input('id');
        $parent_id = $request->input('parent_id');
        $group_name = $request->input('group_name');
        $permission_prefix = $request->input('permission_prefix');

        if (empty($group_name)) {
            return response()->json(['code' => 10001, 'message' => '权限组名称不能为空']);
        }

        if (!ebsig_is_int($permission_prefix)) {
            return response()->json(['code' => 10003, 'message' => '权限前缀不能为空']);
        }

        if (!ebsig_is_int($id)) {
            $id = 0;
        }

        if (!ebsig_is_int($parent_id)) {
            $parent_id = 0;
        }

        $permission_group = PermissionGroupClass::fetch($_id);

        //检查权限前缀是否已存在
        $where = ['permission_prefix' => $permission_prefix];
        $column = 'id';

        $check_permission_group = PermissionGroupClass::getList($where, [], $column);
        if ($check_permission_group['data'] && $check_permission_group['data'][0]['id'] != $id) {
            return response()->json(['code' => 10005, 'message' => '权限前缀已被其他权限组使用']);
        }

        if (!$permission_group) {
            //新增
            PermissionGroupClass::save([
                'id' => $id,
                'group_name' => $group_name,
                'parent_id' => $parent_id,
                'permission_prefix' => $permission_prefix
            ]);
        } else {
            //修改
            PermissionGroupClass::save([
                'id' => $id,
                'group_name' => $group_name,
                'parent_id' => $parent_id,
                'permission_prefix' => $permission_prefix
            ], $_id);
        }
        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $id]);

    }

    /**
     * 删除权限组
     * @param int $id 权限组id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {

        $permission_group = PermissionGroupClass::fetch($id);
        if (!$permission_group) {
            return response()->json(['code' => 10001, 'message' => '权限组不存在']);
        }
        PermissionGroupClass::del($id);

        $level = 2;
        if ($permission_group['parent_id'] == 0) {
            $level = 1;
            //删除下级权限组
            PermissionGroupClass::del($id);
        }

        return response()->json(['code' => 200, 'message' => 'ok', 'data' => ['level' => $level]]);

    }

}
