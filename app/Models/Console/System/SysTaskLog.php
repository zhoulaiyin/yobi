<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:24
 */

namespace App\Models\Console\System;

use DB;
use Moloquent;

class SysTaskLog extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'sys_task_log';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'task_log_id', 'task_id', 'start_time',
        'end_time', 'total_time', 'result'
    ];
}