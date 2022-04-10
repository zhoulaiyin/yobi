<?php
/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 16:19
 */

namespace App\Models\Console\Document;

use DB;
use Moloquent;
class DocOperateManualGroupItem extends Moloquent
{
    protected $connection = 'mongodb';  //库名

    protected $collection = 'doc_operate_manual_group_item';   //文档名

    protected $primaryKey = '_id';  //设置id

    protected $fillable = [
        'updated_at', 'created_at', 'creator', 'item_id', 'item_name', 'group_id',
        'sound_code', 'doc_html', 'sort_order'
    ];
}