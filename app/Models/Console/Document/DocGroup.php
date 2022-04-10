<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:22
 */

namespace App\Models\Console\Document;
use DB;
use Moloquent;

class DocGroup extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'doc_group';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'group_id', 'group_name', 'sort_order'
    ];
}