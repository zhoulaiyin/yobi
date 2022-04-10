<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Demand\Demand;

use App\Http\Models\Performance\AssessmentDeductPointsLog;

class AssessmentDeductPoints extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:deduct_points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计绩效-扣分任务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //获取当前日期时间戳
        $today_time = strtotime(date('Y-m-d'));

        $where = [
            ['demand_status','<',9], //需求状态小于已完成
            ['hang_up','<>',1],  //没有挂起
        ];

        $demand_master = Demand::select('demand_id','finish_date','stage_finish_date','creator','person_in_charge_id')->where($where)->get()->toArray();

        if ( $demand_master ) {

            foreach ( $demand_master as $item ) {

                if ( $item['finish_date'] ) {
                    //要求完成时间转化为时间戳
                    $finish_time = strtotime($item['finish_date']);

                    //判断要求完成时间是否超时
                    if ( $finish_time < $today_time ) {
                        $days = ($today_time-$finish_time)/86400; //相差多少天
                        $this->saveAssessment( [ 'user_id'=>$item['creator'] , 'deduct_points' =>$days , 'reason'=>'需求编号：'.$item['demand_id'].'要求完成时间已超时' ] );
                    }
                }

                //判断阶段要求完成时间是否超时
                if ( $item['stage_finish_date'] && $item['person_in_charge_id'] ) {

                    $stage_finish_time = strtotime($item['stage_finish_date']);   //阶段要求完成时间转为时间戳

                    if ( $stage_finish_time < $today_time ) {
                        $days = ($today_time-$stage_finish_time)/86400; //相差多少天
                        $this->saveAssessment( [ 'user_id'=>$item['person_in_charge_id'] , 'deduct_points' =>$days , 'reason'=>'需求编号：'.$item['demand_id'].'阶段要求完成时间已超时' ] );
                    }

                }

            }
        }

    }

    //保存绩效扣分记录
    private function saveAssessment($data)
    {

        $assessment_deduct_points = new  AssessmentDeductPointsLog();
        $assessment_deduct_points->user_id = $data['user_id'] ;
        $assessment_deduct_points->deduct_points = $data['deduct_points'] ;
        $assessment_deduct_points->reason = $data['reason'] ;
        $assessment_deduct_points->appeal_status = 1 ;
        $assessment_deduct_points->save();

    }


}