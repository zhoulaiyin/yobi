<?php

/**
 * 用户表.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:10
 */
namespace App\Models\Control;

use DB;
use Moloquent;

class BiUser extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_user';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'id', 'saas_client_id', 'saas_client_code',
        'project_id', 'project_name', 'user_id', 'true_name', 'user_pwd', 'group_id',
        'parent_user_id', 'user_permission', 'useFlg', 'mobile', 'email', 'user_type',
        'role_id', 'role_bind', 'template_group_id', 'head_pic'
    ];
}