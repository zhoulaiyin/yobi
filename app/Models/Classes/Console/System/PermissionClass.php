<?php

/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:31
 */

namespace App\Models\Classes\Console\System;

use App\Models\Console\System\Permission;

class PermissionClass
{
    /**
     * 查询数据
     * @param array $where //筛选条件
     * @param array $limit //限定条件
     * @param string $columns //查询的数据列
     * @return mixed
     */
    public static function getList($where = [], $limit = [], $columns = '*')
    {
        global $WS;

        $page = isset($limit['page']) ? $limit['page'] : 1;
        $pageSize = isset($limit['pageSize']) ? $limit['pageSize'] : 10;
        $orderBy = isset($limit['orderBy']) ? $limit['orderBy'] : '_id';
        $sort = isset($limit['sort']) ? $limit['sort'] : 'DESC';
        $offset = (int)(($page - 1) * $pageSize);

        $total_num = Permission::where($where)
            ->count();

        $results = Permission::where($where)
            ->orderBy($orderBy, $sort)
            ->offset($offset)
            ->limit((int)$pageSize)
            ->get()
            ->toArray();

        return [
            'count' => $total_num,
            'data' => $results
        ];
    }

    /**
     * 获取单一应用数据
     * @param $id
     * @return null
     */
    public static function fetch($id)
    {
        $Permission = Permission::find($id);
        if (empty($Permission)) {
            return null;
        }

        return $Permission->toArray();
    }

    /**
     * 保存
     * @param $args
     * @param $id
     */
    public static function save($args, $id = null)
    {
        global $WS;
        if (!empty($id)) {
            //编辑
            $Permission = Permission::find($id);
            if (!$Permission) {
                return ['code' => 500, 'message' => '接口文档未找到'];
            }
        } else {
            //新增
            $Permission = new Permission();
            $Permission->permission_name = $WS->ConsoleUserName;
            $Permission->permission_id = $WS->parentID;
        }

        foreach ($args as $k => $v) {
            $Permission->$k = $v;
        }

        $effect_rows = $Permission->save();

        if ($effect_rows <= 0) {
            return ['code' => 500, 'message' => '操作失败'];
        }
        return ['code' => 200, 'message' => 'ok'];
    }

    /**
     * 删除接口文档
     * @param $_id
     */
    public static function del($_id)
    {
        global $WS;

        $res = Permission::find($_id);
        if (!$res) {
            return ['code' => 500, 'message' => '接口分类不存在'];
        }

        if (!$res->delete()) {
            return ['code' => 500, 'message' => '操作失败'];
        }

        return ['code' => 200, 'message' => '操作成功'];
    }
}