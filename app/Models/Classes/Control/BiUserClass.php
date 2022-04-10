<?php

/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:10
 */


namespace App\Models\Classes\Control;

use App\Models\Control\BiUser;

class BiUserClass
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

        $total_num = BiUser::where($where)
            ->count();

        $results = BiUser::where($where)
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
        $BiUser = BiUser::find($id);
        if (empty($BiUser)) {
            return null;
        }

        return $BiUser->toArray();
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
            $result = BiUser::find($id);
            $result->creator = UserId();
        } else {
            //新增
            $result = new BiUser();
        }

        foreach ($args as $k => $v) {
            $result->$k = $v;
        }

        $effect_rows = $result->save();

        if ($effect_rows <= 0) {
            return ['code' => 500, 'message' => '操作失败'];
        }

        return ['code' => 200, 'message' => 'ok', 'data' => $result->_id];
    }

    /**
     * 删除接口文档
     * @param $_id
     */
    public static function del($_id)
    {
        global $WS;

        $res = BiUser::find($_id);
        if (!$res) {
            return ['code' => 500, 'message' => '用户不存在'];
        }

        if (!$res->delete()) {
            return ['code' => 500, 'message' => '操作失败'];
        }

        return ['code' => 200, 'message' => '操作成功'];
    }

    /**
     * 获取单一数据
     * @param $args
     * @return null
     */
    public static function get($where)
    {
        $result = BiUser::where($where)->get()->first();

        if (empty($result)) {
            return null;
        }

        return $result->toArray();
    }


    public static function isAdmin()
    {
        global $WS;

        $user = BiUser::find($WS->shopUserID);

        if (empty($user['user_permission'])) {
            return null;
        }

        $is_admin = null;

        if (strlen($user['user_permission']) > 1) {

            foreach (explode(',', $user['user_permission']) as $g) {
                if ($g == 1) {
                    $is_admin = true;
                    break;
                }
            }

        } elseif (strlen($user['user_permission']) == 1) {

            if ($user->user_permission == 1) {
                $is_admin = true;
            }

        }

        return $is_admin;

    }
}