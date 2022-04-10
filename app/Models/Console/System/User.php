<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 17:32
 */

namespace App\Models\Console\System;

use DB;
use Moloquent;

class User extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'user';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'user_id', 'true_name', 'user_pwd',
        'useFlg', 'mobile', 'phone'
    ];
}