<?php

namespace App\Models\Console;

use DB;
use Moloquent;

/**
 * 字体库
 * Class BiFontFamily
 * @package App\Models\Console
 */
class BiFontFamily extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_font_family';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'font_id', 'val', 'sort_order'
    ];  //设置 字段 白名单
}