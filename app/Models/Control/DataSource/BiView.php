<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:11
 */

namespace App\Models\Control\DataSource;

use DB;
use Moloquent;

class BiView extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_view';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'view_id', 'view_name', 'bi_user_id',
        'group_id', 'source_id', 'project_id', 'table_json', 'table_structure'
    ];
}