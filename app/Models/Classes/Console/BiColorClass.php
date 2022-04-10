<?php

namespace App\Models\Classes\Console;

use App\Models\Console\BiColor;

class BiColorClass
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

        $total_num = BiColor::where($where)
            ->count();

        $results = BiColor::where($where)
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
        $BiColor = BiColor::find($id);
        if (empty($BiColor)) {
            return null;
        }

        return $BiColor->toArray();
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
            $result = BiColor::find($id);
        } else {
            //新增
            $result = new BiColor();
            $result->creator = UserId();
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

        $res = BiColor::find($_id);
        if (!$res) {
            return ['code' => 500, 'message' => '接口分类不存在'];
        }

        if (!$res->delete()) {
            return ['code' => 500, 'message' => '操作失败'];
        }

        return ['code' => 200, 'message' => '操作成功'];
    }


}