<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;

use Illuminate\Console\Command;

use App\Models\TaskSchedule\FxProject;

use App\Models\TaskSchedule\FxProjectTaskSchedule;

class ShopOwnerStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop_owner:stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微店长统计任务';

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
        //获取分销项目信息
        $project_data = FxProject::select('id','domain_name')->get()->toArray();
        if ( $project_data ) {

            $client = new Client();

            foreach ( $project_data as $value ) {

                //批量初始化微店长当前小时段统计数据
                $create_url = $value['domain_name'].'/task/shopowner/stat/create';
                $request = $client->get($create_url);
                $create_data = json_decode($request->getBody(),true);
                if ( $create_data['code'] == 200 ) {
                    $stat = 1 ;
                } else {
                    $stat = 0 ;
                }

                //保存日志
                $this->addLog( $value['id'] , 'shop_owner:stat:create' ,$stat ,$create_data['message']);

                //统计上一个小时段微店长会员、订单等数据
                $stat_url = $value['domain_name'].'/task/shopowner/stat/statHour';
                $request = $client->get($stat_url);
                $stat_data = json_decode($request->getBody(),true);
                if ( $stat_data['code'] == 200 ) {
                    $stat = 1 ;
                } else {
                    $stat = 0 ;
                }

                //保存日志
                $this->addLog( $value['id'] , 'shop_owner:stat:hour' ,$stat ,$stat_data['message']);

            }

        }

        return true;

    }

    //保存项目任务调度日志
    public function addLog($project_id, $schedule, $stat = 1, $msg = 'ok')
    {
        $project_task_schedule = new FxProjectTaskSchedule;
        $project_task_schedule->project_id = $project_id;
        $project_task_schedule->schedule = $schedule; //任务调度名称
        $project_task_schedule->stat = $stat;
        $project_task_schedule->msg = $msg;
        $project_task_schedule->save();

    }

}
