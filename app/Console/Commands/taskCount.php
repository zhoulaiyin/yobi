<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Task\Task;
use App\Http\Models\User\User;
use App\Http\Models\Task\Bug;
use App\Http\Models\Task\BugSetting;
use App\Http\Models\Performance\PerformanceMaster;

class taskCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taskCount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly employee score';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->countScore();

    }

    //统计员工工时分和质量分
    protected function countScore()
    {

        //获取所有员工
        $user_data = User::all();

        //声明大数组，用于组装员工工时分、质量分等信息
        $performance_data = array();

        $month_begin = date('Y-m-01');//获取当月第一天
        $month_end = date('Y-m-d',strtotime("$month_begin +1 month -1 day"));//当月月末
        $evaluation_year = date('Y');//获取当前年份
        $evaluation_month = date('n');//获取当前月份

        //循环统计员工工时分和质量分
        if (!empty($user_data)) {
            foreach ( $user_data as $user ) {
                $where = [
                    ['task.created_at','>',$month_begin.' 00:00:00'],
                    ['task.created_at','<',$month_end.' 23:59:59'],
                    ['development_user_id',$user['userID']],
                ];

                //获取当月所有的状态下的任务
                $task_all_data = Task::select('working_hours')->where($where)->where('task_status','<',6)->get()->toArray();

                //获取当月审核，或已完成的任务，且对应的需求状态为已完成
                $task_completed_data = Task::select('task.working_hours','task.id')
                    ->whereIn('task.task_status',[4,5])
                    ->where($where)
                    ->join('demand_master_new as dmn',function($join)
                    {

                        $join->on('dmn.demand_id','=','task.demand_id')
                            ->where('dmn.demand_status','=',9);

                    })
                    ->get()
                    ->toArray();

                //如果没有任务或者没有完成的任务，则工时和质量分为0，
                if ( empty($task_all_data) || empty($task_completed_data) ) {
                    $performance_data[$user['userID']] = array(
                        'working_hours_score' => 0,
                        'quality_score' => 0
                    );
                    continue;
                }

                //该数组用于组装当前员工任务bug所扣之分
                $bug_score = array();
                //循环已完成的任务，判断是否存在bug，从而算出质量分
                foreach( $task_completed_data as $item ) {

                    $bug_data = Bug::select('bug_setting_id')->where('task_id',$item['task_id'])->get()->toArray();
                    if ( !empty($bug_data) ) {//说明有bug

                        foreach($bug_data as $value){
                            $bugSetting_data = BugSetting::select('deduct_points')->find($value['bug_setting_id']);
                            $bug_score[] = $bugSetting_data['deduct_points'];
                        }

                    }

                }
                //初始化扣分总数
                $sum_bug_collection = 0;
                //获取扣分总数
                if ( !empty($bug_score) ) {
                    $sum_bug_collection = collect($bug_score)->sum();
                }
                //当前员工质量分
                $quality_score = round((45-$sum_bug_collection),2);

                //获取所有状态下工时总和
                $sum_all_collection = collect($task_all_data)->sum('working_hours');
                //获取开发完成的任务工时总和
                $sum_completed_collection  = collect($task_completed_data)->sum('working_hours');

                //如果当前用户的质量分小于或者等于37，工时分扣除20%
                if ( $quality_score <= 37 ) {
                    $deduct = 0.8;
                } else {
                    $deduct = 1 ;
                }

                //判断工时，获取工时分
                if ( $sum_all_collection >= 176 ) {//总工时如果大于或等于176

                    if ( $sum_completed_collection >=176 ) {
                        $working_hours_score = round((50 + ($sum_completed_collection-176)*0.284 )*$deduct,2);
                    } else {
                        $working_hours_score = round(($sum_completed_collection*0.284)*$deduct,2);
                    }

                    $performance_data[$user['userID']] = array(
                        'working_hours_score' => $working_hours_score,
                        'quality_score' => $quality_score
                    );

                } else {//总工时小于176

                    if ( $sum_all_collection = $sum_completed_collection ) {//如果完成工时等于总工时

                        $performance_data[$user['userID']] = array(
                            'working_hours_score' => round(50*$deduct,2),
                            'quality_score' => $quality_score
                        );

                    } else {//如果完成工时小于总工时

                        $performance_data[$user['userID']] = array(
                            'working_hours_score' => round((50/$sum_all_collection)*$sum_completed_collection*$deduct,2),
                            'quality_score' => $quality_score
                        );

                    }

                }

            }
        }

        //批量往绩效主表插入数据
        if ( !empty($performance_data) ) {

            foreach ( $performance_data as $k=>$value ) {
                PerformanceMaster::create([
                    'uuid' => makeUuid(),
                    'timeStamp' =>Carbon::now(),
                    'creator' => 'system' ,
                    'createTime' => Carbon::now(),
                    'evaluation_year' => $evaluation_year,
                    'evaluation_month' => $evaluation_month,
                    'user_id' => $k,
                    'working_hours_score' =>$value['working_hours_score'] ,
                    'quality_score' => $value['quality_score'],
                ]);
            }

        }

    }

}
