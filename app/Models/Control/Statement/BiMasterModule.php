<?php

/**
 * BI建模表.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:24
 */

namespace App\Models\Control\Statement;

use DB;
use Moloquent;

class BiMasterModule extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_master_module';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'module_id', 'uid', 'bi_id', 'view_id',
        'bi_json', 'db_json', 'attribute_json', 'chart_json',
        'bi_structure','db_structure','attribute_structure','chart_structure'
    ];
}