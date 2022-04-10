<?php

/**
 * 用户模板库.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:45
 */

namespace App\Models\Control\Template;

use DB;
use Moloquent;

class BiUserTemplateDatabase extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_user_template_database';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'template_id', 'template_title', 'bi_user_id',
        'template_icon', 'template_pic', 'bi_id', 'bi_uid', 'attribute_json',
        'attribute_structure'
    ];
}