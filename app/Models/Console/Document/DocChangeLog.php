<?php

/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:04
 */
namespace App\Models\Console\Document;

use DB;
use Moloquent;

class DocChangeLog extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'doc_change_log';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'log_id', 'v', 'change_date',
        'editor', 'sound_code', 'doc_html'
    ];
}