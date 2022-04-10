<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:58
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiTemplateDatabaseModule extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_template_database_module';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'module_uid', 'template_id',
        'view_id', 'bi_json', 'db_json', 'attribute_json', 'chart_json',
        'bi_structure','db_structure','attribute_structure','chart_structure'
    ];
}