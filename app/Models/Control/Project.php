<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:41
 */

namespace App\Models\Control;

use DB;
use Moloquent;
class Project extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'project';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'project_id', 'project_name', 'project_domain_name'
    ];
}