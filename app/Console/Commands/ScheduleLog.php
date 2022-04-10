<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Service\TaskScheduleService;

use App\Models\TaskSchedule\TaskScheduleLog;

class ScheduleLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:log {op}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '{op}=remove 删除一个星期前的任务调度记录';

    /**
     * 任务id
     *
     * @var array
     */
    protected $id = [
        'remove' => 3
    ];

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

        //操作
        $op = $this->argument('op');

        if (!isset($this->id[$op])) {
            return false;
        }
        $id = $this->id[$op];

        //检查任务
        if (false === TaskScheduleService::check($id)) {
            return false;
        }

        //if ($op == 'remove') { //删除一个星期前的任务调度记录

            $res = $this->remove();

        //}

        //任务执行成功
        TaskScheduleService::addLog($id, $res['code'], $res['message']);

        return true;

    }

    /**
     * 删除一个星期前的任务调度记录
     */
    private function remove()
    {

        //七天前
        $datetime = \Carbon\Carbon::now()->subDays(7)->toDateString() . ' 23:59:59';

        TaskScheduleLog::where('created_at', '<=', $datetime)->delete();

        return ['code' => 1, 'message' => '已删除[' . $datetime . ']之前的任务调度记录'];

    }

}
