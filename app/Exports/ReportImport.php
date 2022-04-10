<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\ToModel;

class ReportImport implements ToModel{
    private $field = [];

    public function model(array $row)
    {
        //空文档
        if (!is_array($row[0])) {
            return false;
        }

        $this->field = $row;
        error_log(print_r($this->field, true));
    }
}