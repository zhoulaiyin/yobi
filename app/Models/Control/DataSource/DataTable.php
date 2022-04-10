<?php
/**
 * 数据源规则信息.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:15
 */

namespace App\Models\Control\DataSource;

use DB;
use Moloquent;

class DataTable extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'data_table';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'table_id', 'table_name', 'bi_user_id',
        'source_id', 'description', 'fields_json', 'is_default', 'fields_structure'
    ];
}