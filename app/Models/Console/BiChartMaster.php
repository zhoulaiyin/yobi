<?php
/**
 * 图标类型.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 14:49
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiChartMaster extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_chart_master';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'chart_id', 'group_id', 'chart_title',
        'photo_link', 'chart_json', 'stringify','chart_structure','stringify_structure'
    ];
}