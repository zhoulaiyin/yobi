<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;

use Illuminate\Console\Command;

use App\Models\TaskSchedule\FxProject;

use App\Models\TaskSchedule\FxProjectTaskSchedule;

class FxOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fx:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计微电汇10天内订单任务到微分销';

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

                //统计订单
                $url = $value['domain_name'].'/task/order';
                $request = $client->get($url);
                $return_data = json_decode($request->getBody(),true);
                if ( $return_data['code'] == 200 ) {
                    $stat = 1 ;
                } else {
                    $stat = 0 ;
                }

                //保存任务日志
                $project_task_schedule = new FxProjectTaskSchedule;
                $project_task_schedule->project_id = $value['id'];
                $project_task_schedule->schedule = 'fx:order'; //任务调度名称
                $project_task_schedule->stat = $stat;
                $project_task_schedule->msg = $return_data['message'];
                $project_task_schedule->save();

            }

        }

        return true;
    }

}
