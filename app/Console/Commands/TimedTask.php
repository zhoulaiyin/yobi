<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Service\MessageQueueService;

use Illuminate\Support\Facades\Redis as Redis;

use Illuminate\Support\Facades\DB;

class TimedTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timed:task {frequency}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '任务系统调度任务';

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


        $messageQueueService = new MessageQueueService();

        $frequency = $this->argument('frequency');//接受id

        //取得任务列表
        $timed_task_list = $this->getTimedTaskCache($frequency);


        foreach($timed_task_list as $v){
            //push进消息队列
            $a = $messageQueueService->push([
                'call_url' => $v
            ]);
            // var_dump($a);exit;
        }
        

        return true;

    }


    //取得任务列表 $frequency为频率id
    private function getTimedTaskCache($frequency)
    {

        //取redis里的任务列表，取到了则返回
        $redis_key = 'tt:list:' . $frequency;
        $timed_task_list = Redis::get($redis_key);
        if ($timed_task_list) {
            return json_decode($timed_task_list, true);
        }

        //没取到则要查询数据库，并将结果存储于redis，并返回任务列表
        //TODO 根据频率id查询该频率下的任务，并保存到redis里，redis有效期1个星期
        
        $timed_task_row = DB::table('timed_task')
                ->where('frequency_id', '=', $frequency)
                ->first();
        
        $product_id = $timed_task_row['product_id'];//产品id
        $project_id = $timed_task_row['project_id'];//项目id
        $url = $timed_task_row['url'];//任务url
        $arr = array();
        if($project_id == 0){//有子项目则循环
            //根据product_id查询其所有子项目，在timed_task_project表中
            $project_rows = DB::table('timed_task_project')->where('product_id','=',$product_id)->get()->toArray();
            foreach($project_rows as $v){
                $arr[] = $v->domain_name.$url;//domain_name+url
            }

        }else{
            $project_row = DB::table('timed_task_project')->where('id','=',$project_id)->first();
            $arr[] = $project_row['domain_name'].$url;//domain_name+url
        }

        //将数据存储到redis
        $json_arr = json_encode($arr);
        $r_key = 'tt:list'.$frequency;
        $expire_time = 7*24*60*60;//过期时间
        Redis::setex($r_key,$expire_time,$json_arr);
        return $arr;//返回domain_name+url列表

    }

}