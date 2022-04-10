<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:21
 */

namespace App\Models\Console\System;

use DB;
use Moloquent;

class SysSeqno extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'sys_seqno';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'name', 'seqno', 'remark'
    ];
}