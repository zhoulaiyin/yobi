<?php
// +----------------------------------------------------------------------
// | ebSIG
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.ebsig.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: liudaojian <liudaojian@ebsig.com>
// +----------------------------------------------------------------------

/**
 * 短信提醒任务
 * @package  app/Console/Commands
 * @author   liudaojian <liudaojian@ebsig.com>
 * @version 1.0
 */

namespace App\Console\Commands;

use Sms;

use Illuminate\Console\Command;

use App\Models\Sms\SmsSetting;

use App\Service\TaskScheduleService;

class SmsRemind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每小时检查一次剩余短信总数，数量不足时提醒';

    /**
     * 任务id
     *
     * @var int
     */
    protected $id = 1;

    /**
     * Create a new command instance.
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
        
        //检查任务
        if (false === TaskScheduleService::check($this->id)) {
            return false;
        }

        //查询短信设置
        $setting = SmsSetting::find(1);
        if (!$setting) {
            TaskScheduleService::addLog($this->id, 2, '短信设置不存在');
            return false;
        }

        //预警提醒数量不为空
        if (!empty($setting->warning_reminder_number)) {
            //预警提醒数量大于等于可用短信数量
            if ($setting->warning_reminder_number >= $setting->sms_number) {
                $message = '短信平台短信总数不足，当前短信总数：'  . $setting->sms_number;
                $res = Sms::send(10005, explode(',', $setting->reminder_mobile), $message);
                if ($res['code'] != 200) {
                    TaskScheduleService::addLog($this->id, 2, '发送提醒短信失败，错误信息：'  . $res['message']);
                    return false;
                }
            }

        }

        //任务执行成功
        TaskScheduleService::addLog($this->id, 1, '短信平台短信总数：' . $setting->sms_number);

        return true;

    }
}
