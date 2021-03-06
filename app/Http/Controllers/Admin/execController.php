<?php
/**
 * 任务调度控制器
 * Created by zhoulaiyin.
 * Date: 2017/6/19
 * Time: 12:59
 */

namespace App\Http\Controllers\Admin;

use App\Models\Classes\Console\System\SysTaskLogClass;
use DB;
use App\Http\Controllers\Controller;
use App\Http\Models\Task;
use App\Http\Controllers\Common\EbsigMsgPush;
use Illuminate\Http\Request;

class execController extends Controller

{

    public $task_type = array(
        '1' => '一分钟任务',
        '2' => '五分钟任务',
        '3' => '十分钟任务',
        '4' => '半小时任务',
        '5' => '一小时任务',
        '6' => '0点任务',
        '7' => '1点任务',
        '8' => '3点任务',
    );

    /**
     * 任务调度
     */
    public function exec(Request $request, $type = 1)
    {
        $EbsigMsgPush = new EbsigMsgPush();

        //查询任务列表信息
        $where = array(
            ['task_type', '=', $type],
            ['task_status', '=', 1]
        );
        $task_array = Task::where($where)->get()->toArray();

        //请求的任务类型
        $task_type_value = $this->task_type[$type];

        if (empty($task_array)) {
            die(json_encode(array('code' => 404, 'message' => '暂无运行中的' . $task_type_value . '任务')));
        }

        $today_date = date('Y-m-d');
        $yesterday_date = date('Y-m-d', strtotime('-1 days'));

        $task_time = array(
            '6' => array(
                'name' => '凌晨0点统计任务',
                's' => $yesterday_date . ' 23:50:00',
                'e' => $today_date . ' 00:30:00'
            ),
            '7' => array(
                'name' => '凌晨1点统计任务',
                's' => $today_date . ' 00:50:00',
                'e' => $today_date . ' 01:30:00'
            ),
            '8' => array(
                'name' => '凌晨3点统计任务',
                's' => $today_date . ' 02:50:00',
                'e' => $today_date . ' 03:30:00'
            )
        );

        foreach ($task_array as $val) {

            $task_log_id = SysTaskLogClass::getList()->insertGetId(
                [
                    'updated_at' => date('Y-m-d H:i:s'),
                    'creator' => 'system',
                    'created_at' => date('Y-m-d H:i:s'),
                    'task_id' => $val['task_id'],
                    'start_time' => date('Y-m-d H:i:s')
                ]
            );

            //执行任务
            $push_array = array(
                'call_url' => $request->getSchemeAndHttpHost() . $val['task_link'],//任务链接
                'debug' => true, //act值
                'act' => $val['task_act_value'], //act值
                'task_log_id' => strval($task_log_id)
            );
            error_log('推送给gomq的数组--->' . print_r($push_array, true));
            $re = $EbsigMsgPush->ebsigAsyncPush($push_array);
            error_log('mq返回结果：' . print_r($re, true));

        }
    }

}