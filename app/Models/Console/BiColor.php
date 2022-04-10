<?php
/**
 * 色彩.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 14:55
 */

namespace App\Models\Console;

use DB;
use Moloquent;

class BiColor extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'bi_color';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'color_id', 'color_code'
    ];
}