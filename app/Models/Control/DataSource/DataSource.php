<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:03
 */

namespace App\Models\Control\DataSource;

use DB;
use Moloquent;

class DataSource extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'data_source';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'source_id', 'source_name', 'bi_user_id',
        'group_id', 'table_name', 'file_size', 'line_num', 'file_path', 'db_host',
        'db_database', 'db_user', 'db_pwd'
    ];
}