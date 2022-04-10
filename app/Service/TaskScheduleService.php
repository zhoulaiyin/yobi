<?php
namespace App\Service;

use App\Models\TaskSchedule\TaskSchedule;

use App\Models\TaskSchedule\TaskScheduleLog;

class TaskScheduleService
{

    public static function check($task_id)
    {

        //检查任务
        $task_schedule = TaskSchedule::find($task_id);
        if (!$task_schedule) {
            self::addLog($task_id, 2, '任务调度没有找到');
            return false;
        }

        if (0 === $task_schedule->is_use) {
            self::addLog($task_id, 2, '任务调度已禁用');
            return false;
        }

        return true;

    }

    public static function addLog($task_id, $stat = 1, $msg = 'OK')
    {

        $task_schedule_log = new TaskScheduleLog;
        $task_schedule_log->task_id = $task_id;
        $task_schedule_log->stat = $stat;
        $task_schedule_log->msg = $msg;
        $task_schedule_log->save();

    }

}