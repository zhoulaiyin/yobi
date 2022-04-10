<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:34
 */

namespace App\Models\Console\System;

use DB;
use Moloquent;

class UserLogin extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'user_login';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'log_id', 'user_id', 'ip',
        'session_id', 'user_agent'
    ];
}