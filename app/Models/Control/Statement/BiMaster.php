<?php
/**
 * BI主表
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:29
 */

namespace App\Models\Control\Statement;

use DB;
use Moloquent;

class BiMaster extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_master';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'bi_id', 'group_id', 'bi_user_id', 'project_id',
        'uid', 'bi_title', 'attribute_json', 'attribute_structure'
    ];
}