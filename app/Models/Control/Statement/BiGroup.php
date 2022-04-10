<?php
/**
 * 报表分组.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:31
 */

namespace App\Models\Control\Statement;

use DB;
use Moloquent;

class BiGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'bi_id', 'group_id', 'group_name', 'bi_user_id',
        'project_id'
    ];
}