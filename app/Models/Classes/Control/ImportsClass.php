<?php

/**
 * Created by PhpStorm.
 * User: 豆贤静
 * Date: 2020/7/1
 * Time: 15:10
 */

namespace App\Models\Classes\Control;

use Maatwebsite\Excel\Concerns\ToModel;
//新增
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportsClass implements ToModel
{
    public function model(array $row)
    {
        return $row;
    }
}