<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:30
 */

namespace App\Models\Classes\Control\Statement;

use App\Models\Control\Statement\BiMaster;

class BiMasterClass
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

        $total_num = BiMaster::where($where)
            ->count();

        $results = BiMaster::where($where)
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
        $BiMaster = BiMaster::find($id);
        if (empty($BiMaster)) {
            return null;
        }

        return $BiMaster->toArray();
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
            $BiMaster = BiMaster::find($id);
        } else {
            //新增
            $BiMaster = new BiMaster();
            $BiMaster->creator = UserName();
            $BiMaster->project_id = $WS->projectID;
            $BiMaster->bi_user_id = $WS->mainUserID;
        }

        foreach ($args as $k => $v) {
            $BiMaster->$k = $v;
        }

        $effect_rows = $BiMaster->save();

        if ($effect_rows <= 0) {
            return ['code' => 500, 'message' => '操作失败'];
        }

        return ['code' => 200, 'message' => 'ok', 'data' => $BiMaster->_id];
    }

    /**
     * 删除接口文档
     * @param $_id
     */
    public static function del($_id)
    {
        $res = BiMaster::find($_id);
        if (!$res) {
            return ['code' => 500, 'message' => '报表不存在'];
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
        $result = BiMaster::where($where)->get()->first();

        if (empty($result)) {
            return null;
        }

        return $result->toArray();
    }

}