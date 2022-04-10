<?php
/**
 * 图标类型组别.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 14:45
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiChartGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_chart_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'group_id', 'group_name', 'group_code', 'icon'
    ];
}