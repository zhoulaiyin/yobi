<?php
/**
 * 权限组.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:34
 */

namespace App\Models\Console\System;

use DB;
use Moloquent;

class PermissionGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'permission_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'id', 'group_name', 'parent_id',
        'permission_prefix', 'icon', 'url'
    ];

    public $timestamps = true;
}