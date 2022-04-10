<?php
/**
 * 微电汇数据源分组.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:38
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiRuleGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_rule_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'group_id', 'group_name'
    ];  //设置 字段 白名单
}