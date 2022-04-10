<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/6
 * Time: 15:32
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiTemplateGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_template_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'group_id', 'group_name'
    ];
}