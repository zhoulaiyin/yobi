<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:05
 */

namespace App\Models\Classes\Control\DataSource;

use App\Models\Control\DataSource\DataSource;

class DataSourceClass
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

        $total_num = DataSource::where($where)
            ->count();

        $results = DataSource::where($where)
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
        $DataSource = DataSource::find($id);
        if (empty($DataSource)) {
            return null;
        }

        return $DataSource->toArray();
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
            $result = DataSource::find($id);
        } else {
            //新增
            $result = new DataSource();
            $result->creator = UserName();
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

        $res = DataSource::find($_id);
        if (!$res) {
            return ['code' => 500, 'message' => '接口分类不存在'];
        }

        if (!$res->delete()) {
            return ['code' => 500, 'message' => '操作失败'];
        }

        return ['code' => 200, 'message' => '操作成功'];
    }

    public static function conn_query($source_id, $sql)
    {
        if (empty($source_id) || empty($sql)) {
            return [
                'code' => '400',
                'message' => '缺失参数',
                'data' => []
            ];
        }

        $data_source = DataSource::find($source_id)->toArray();
        if (empty($data_source)) {
            return [
                'code' => '400',
                'message' => '没有找到数据源配置',
                'data' => []
            ];
        }

        $conn = mysqli_connect($data_source['db_host'], $data_source['db_user'], $data_source['db_pwd'], $data_source['db_database'], $data_source['db_port']);
        if (!$conn) {
            return [
                'code' => '400',
                'message' => mysqli_connect_error(),
                'data' => []
            ];
        }

        mysqli_query($conn, 'set names utf8');

        $sql_data = [];

        $result_data = mysqli_query($conn, $sql);

        if (!empty($result_data)) {
            while ($dt = mysqli_fetch_assoc($result_data)) {
                $sql_data[] = $dt;
            }
        }

        if (!empty($conn->error)) {
            error_log('执行SQL《' . $sql . '》报错：' . $conn->error);
        }

        //关闭数据库链接
        mysqli_close($conn);

        return [
            'code' => '200',
            'message' => 'ok',
            'data' => $sql_data
        ];

    }
}