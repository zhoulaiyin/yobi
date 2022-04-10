<?php

/**
 * 权限.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:30
 */
namespace App\Models\Console\System;

use DB;
use Moloquent;

class Permission extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'permission';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'id', 'permission_id', 'permission_name',
        'permission_url', 'parent_group_id', 'group_id', 'permission_type'
    ];
}