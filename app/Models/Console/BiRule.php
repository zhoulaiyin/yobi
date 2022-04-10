<?php


namespace App\Models\Console;

use DB;
use Moloquent;

/**
 * 系统统计规则
 * Class BiRule
 * @package App\Models\Console
 */
class BiRule extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_rule';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'table_id', 'rule_group_id', 'table_name',
        'description', 'statistical_frequency', 'fields_json', 'sound_code', 'is_default',
        'fields_structure'
    ];  //设置 字段 白名单
}