<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:40
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiTemplateDatabase extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_template_database';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'template_id', 'template_title',
        'group_id', 'template_icon', 'template_pic', 'project_id', 'bi_id',
        'bi_uid', 'template_status', 'attribute_json', 'price', 'total_download',
        'total_collect','attribute_structure'
    ];  //设置 字段 白名单
}