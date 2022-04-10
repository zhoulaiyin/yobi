<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportExport implements FromCollection, WithHeadings, WithEvents{

    private $title = []; //数据列标题
    private $keys = []; //取值字段名
    private $data = []; //数组数组

    /**
     *
     * ReportExport constructor.
     * @param $headings  //导出的标题设置
     * @param $data //数据数组
     */
    public function __construct($headings, $data)
    {
        foreach ($headings as $key => $title) {
            $this->keys[] = $key;
            $this->title[] = $title;
        }
        $this->data = $data;
    }

    // set the headings
    public function headings(): array
    {
        return $this->title;
    }

    // freeze the first row with headings
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->freezePane('A2', 'A2');
            },
        ];
    }

    public function collection()
    {
        $data = [];

        if (!empty($this->data)) {
            foreach ($this->data as $r) {
                $temp = [];
                foreach ($this->keys as $k) {
                    if(strpos($k,'|') === false){
                        if (array_key_exists($k, $r)) {
                            $temp[] = $r[$k];
                        } else {
                            $temp[] = '';
                        }
                    } else {
                        //包含外键字段
                        $params = explode('|', $k);

                        if (
                            !empty($r['forignKeys'])
                            && isset($r['forignKeys'][$params[0]])
                            && $r['forignKeys'][$params[0]][$params[1]]
                        ) {
                            $temp[] = $r['forignKeys'][$params[0]][$params[1]];
                        }
                    }

                }
                $data[] = $temp;
            }
        }

        return collect($data);
    }
}