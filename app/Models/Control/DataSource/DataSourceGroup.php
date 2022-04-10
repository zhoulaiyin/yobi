<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:12
 */

namespace App\Models\Control\DataSource;

use DB;
use Moloquent;

class DataSourceGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'data_source_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'group_id', 'group_name', 'group_classify'
    ];
}