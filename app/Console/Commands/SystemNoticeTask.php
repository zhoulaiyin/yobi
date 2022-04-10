<?php

namespace App\Console\Commands;

use Mail;

use Illuminate\Console\Command;

use App\Service\TaskScheduleService;

use App\Http\Models\Project\Project;

use App\Http\Models\Notice\SystemNotice;

use App\Http\Models\Notice\SystemNoticeEmail;

use Illuminate\Support\Facades\Redis as Redis;

class SystemNoticeTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system_notice:stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送系统通知任务';

    /**
     * 任务id
     *
     * @var int
     */
    protected $id = 6;


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

        //检查任务
        if (false === TaskScheduleService::check($this->id)) {
            return false;
        }

        //获取凌晨定时脚本redis
        $res = Redis::get('TIMED_TASK_SYSTEM_NOTICE');

        if ( !$res ) {
            $current_time = time();
            $second_time = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
            $expire = $second_time - $current_time;
            Redis::setex('TIMED_TASK_SYSTEM_NOTICE', $expire, 1);
            $this->dayNotice();
        }

        //查询收件人信息
        $notifier = SystemNoticeEmail::all();
        if ( !$notifier ) {
            return response()->json(['code' => 10002, 'message' => '收件人信息为空']);
        }

        $notifier = $notifier->toArray();

        //查询系统通知信息 send_status 0 .未发送
        $notice = SystemNotice::where('send_status', 0)->get()->toArray();

        if ( empty( $notice ) ) {
            return response()->json(['code' => 10003, 'message' => '无可处理的系统通知']);
        }

        foreach ( $notice as $item ) {

            //获取标题
            $config = Project::find($item['project_id']);

            $config_value =  '微电汇';
            if ( $config ) {
                $config_value = $config->project_name;
            }

            $notice_data['title'] = $item['title'];  //邮件标题
            $notice_data['content'] = $item['content']; //邮件内容
            $notice_data['config_value'] = $config_value; //项目名称

            foreach ( $notifier as $email ) {

                $notice_data['email'] = $email['email']; //邮件地址

                if ( $email['project_id'] == $item['project_id'] ) {

                    $this->notice($notice_data);
                }
            }

            //更新发送状态
            SystemNotice::where(['id'=>$item['id']])->update(['send_status'=>1]);

            //保存日志
            TaskScheduleService::addLog( $this->id ,1 , '系统通知邮件发送成功');
        }

        return response()->json(['code' => 200 , 'message' => 'ok' ]);

    }


    /**
     * 系统通知邮件
     * @param $notice
     */
    private function notice( $notice )
    {
        //保存日志
        Mail::send('emails.demand.noticeMessage', ['notice' => $notice], function ($m) use ($notice) {
            $m->to($notice['email'])->subject('【' . $notice['config_value'] . '】' . $notice['title']);
        });

    }


    /**
     * 系统通知邮件凌晨定时任务
     * @param
     */
    private function dayNotice( )
    {

        $notifier_email = SystemNoticeEmail::all();

        if ( $notifier_email ) {

            $notifier = [];

            foreach ( $notifier_email as $email ) {

                //获取标题
                $config = Project::find($email['project_id']);

                $config_value =  '微电汇';
                if ( $config ) {
                    $config_value = $config->project_name;
                }
                $notifier['email'] = $email['email'];
                $notifier['title'] = '每天定时消息通知';
                $notifier['content'] = '每天定时消息通知内容';
                $notifier['config_value'] = $config_value;
                $notifier['title'] = '定时消息';

                //保存日志
                Mail::send('emails.demand.noticeMessage', ['notice' => $notifier], function ($m) use ($notifier) {
                    $m->to($notifier['email'])->subject('【'.$notifier['config_value'].'】'.$notifier['title'] );
                });

                //保存日志
                TaskScheduleService::addLog( $this->id ,1 , '系统通知凌晨定时邮件发送成功');

            }
        }


    }


}
