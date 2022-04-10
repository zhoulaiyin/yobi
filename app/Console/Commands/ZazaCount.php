<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use Mail;

use Maatwebsite\Excel\Facades\Excel;

use App\Http\Models\User\Department;

use App\Http\Models\User\User;

use App\Http\Models\Zaza\Zaza;

use App\Service\TaskScheduleService;

class ZazaCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zaza:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'use for count data of zaza';

    /**
     * 任务id
     *
     * @var int
     */
    protected $id = 2;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //检查任务
        if (false === TaskScheduleService::check($this->id)) {
            return false;
        }

        //本周时间（写表头）
        $w = date('w',time());
        $start_day = date('Ymd',time()-60*60*24*($w-1));
        $end_day = date('Ymd',strtotime($start_day)+60*60*24*6);

        //邮件正文
        $desc = "这是一份系统自动生成的扎扎统计，本统计收集了全公司以部门为单位形成的发扎，收扎和完扎情况，并最终计算了各部门的扎效率\n报告生成时间：".date('Y-m-d H:i:s',time())."\n";
        $desc .= '统计周期：'.$start_day.'~'.$end_day."\n";
        $desc .= "统计范围：公司全员工\n";
        $desc .= '统计单位：部门';

        //拿数据
        $departmentModel = new Department();
        //获取顶级部门信息
        $department_data = $departmentModel->getDepartments(0);
        //部门总表数据源，部门详细数据源
        $data_source = $this->getDepartmentData($department_data,$departmentModel,$start_day);
        $totalCount = $data_source['totalCount'];
        $department = $data_source['department'];
        $title = '报表'.$start_day.'-'.$end_day ;

        Excel::create('zazacount'.date('Ymd',time()),function($excel) use ($totalCount,$department,$title){
            //部门报表
            $excel->sheet('部门报表', function($sheet) use ($totalCount,$title){
                $sheet->setFontFamily('微软雅黑');

                //组合填充数据
                Array_unshift($totalCount,['部门','发扎次数','收扎数量','完扎数量','效率(完扎数量/收扎数量)']);
                Array_unshift($totalCount,[$title]);
                $sheet->rows($totalCount);

                //设置单元格宽度
                $sheet->setWidth(array(
                    'A'     =>  15,
                    'B'     =>  15 ,
                    'C'     =>  15 ,
                    'D'     =>  15,
                    'E'     =>  22
                ));
                //设置首行合并，显示标题
                $sheet->mergeCells('A1:E1');
                //设置居中
                foreach($totalCount as $k=>$a){
                    $sheet->cells('A'.($k+1).':'.'F'.($k+1),function($cells){
                        $cells->setAlignment('center');
                    });
                }
            });

            //部门内详细报表
            foreach($department as $kk=> &$d) {
                $excel->sheet($d['departmentName'], function ($sheet) use ($d, $title) {

                    $sheet->setFontFamily('微软雅黑');
                    //组合填充数据
                    Array_unshift($d['user'], ['姓名', '发扎次数', '收扎数量', '完扎数量', '效率(完扎数量/收扎数量)']);
                    Array_unshift($d['user'], [$title]);
                    $sheet->rows($d['user']);
                    //设置单元格宽度
                    $sheet->setWidth(array(
                        'A' => 15,
                        'B' => 15,
                        'C' => 15,
                        'D' => 15,
                        'E' => 22
                    ));
                    //设置首行合并，显示标题
                    $sheet->mergeCells('A1:E1');
                    //设置居中
                    foreach ($d['user'] as $k => $a) {
                        $sheet->cells('A' . ($k + 1) . ':' . 'F' . ($k + 1), function ($cells) {

                            $cells->setAlignment('center');
                        });
                    }
                });
            }

        })->store('xls',storage_path('excel/exports/company'));

        //成功返回1，失败返回0
        $flag = Mail::raw($desc, function ($message) use ($department, $title) {
            //发送excel
            $attachment = storage_path('excel/exports/company/' . 'zazacount'.date('Ymd',time()).'.xls');
            //在邮件中上传附件
            $message->attach($attachment, ['as' => "=?UTF-8?B?" . base64_encode('扎扎' . $title) . "?=.xls"]);
            $to = [];
            foreach($department as $d){
                foreach($d['email'] as $ee){
                    $to[] = $ee ;
                }
            }
            $message->to($to)->subject('扎扎周统计数据');

        });

        //任务执行成功
        TaskScheduleService::addLog($this->id);

    }

    public function getDepartmentData(&$department_data,$departmentModel,$start_day,&$totalCount=[],&$department=[],&$level=0){
        if($level == 2){
            return [
                'totalCount' => $totalCount ,
                'department'  => $department
            ];
        }
        $length = count($totalCount) ;

        foreach($department_data as $kk=> &$d) {
            $length ++ ;
            //单个部门统计
            $totalCount[$length] = ['departmentName'=> $d['departmentName'],'send'=>0,'get'=>0,"done"=>0,'rate'=>0];
            //顶级部门下所有部门
            $departmentIds = $departmentModel->getDepartmentsId($d['departmentID']);
            $departmentIds[] = $d['departmentID'];
            //本部门下所有用户
            $user = User::whereIn('departmentID', $departmentIds)->get(['userID','trueName']);
            $user = $user ? $user->toArray() : [];

            //负责人
            $email = User::whereIn('departmentID',$departmentIds)->where('personLiable',1)->pluck('email');
            //添加部门数据
            if(!empty($user)){
                foreach($user as &$u){
                    //发扎
                    $send = Zaza::where(function($query) use($u,$start_day) {
                        $query->where('fromId',$u['userID'])
                            ->where('createTime','>',date("Y-m-d H:i:s",strtotime($start_day)))
                            ->where('createTime','<',date("Y-m-d H:i:s",strtotime($start_day)+60*60*24*7));
                    })->count();
                    //收扎
                    $get = Zaza::where(function($query) use($u,$start_day) {
                        $query->where('fromId',$u['userID'])
                            ->where('createTime','>',date("Y-m-d H:i:s",strtotime($start_day)))
                            ->where('createTime','<',date("Y-m-d H:i:s",strtotime($start_day)+60*60*24*7));
                    })->count();
                    //完扎
                    $done = Zaza::where(function($query) use($u,$start_day) {
                        $query->where('toId',$u['userID'])
                            ->where('stat',2)
                            ->where('timeStamp','>',date("Y-m-d H:i:s",strtotime($start_day)))
                            ->where('timeStamp','<',date("Y-m-d H:i:s",strtotime($start_day)+60*60*24*7));
                    })->count();
                    $rate = 0 ;
                    if($get!=0){
                        $rate = round($done / $get,2) ;
                    }
                    $u = [
                        'trueName' => $u['trueName'],
                        'send' => $send ,
                        'get'  => $get ,
                        'done'  => $done,
                        'rate'  => $rate
                    ];
                    $totalCount[$length]['send'] += $send ;
                    $totalCount[$length]['get'] += $get ;
                    $totalCount[$length]['done'] += $done ;
                }
            }
            //效率
            if($totalCount[$length]['get']!=0){
                $totalCount[$length]['rate'] =  round($totalCount[$length]['done']/$totalCount[$length]['get'],2);
            }
            $department[$length]['departmentName'] = $d['departmentName'];
            $department[$length]['user'] = $user ;
            $email = $email ? $email->toArray() :[];
            $department[$length]['email'] = $email ;
            if($d['sub']){
                $level ++ ;
                $this->getDepartmentData($d['sub'],$departmentModel,$start_day,$totalCount,$department,$level);
            }

        }

        return [
            'totalCount' => $totalCount ,
            'department'  => $department
        ];
    }
    
}
