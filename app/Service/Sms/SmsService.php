<?php
namespace App\Service\Sms;

use DB;

use Carbon\Carbon;

use App\Models\Sms\SmsSendLog;

class SmsService
{

    private $server;

    private $config;

    public function __construct($server, $config)
    {
        $this->server = $server;
        $this->config = $config;
    }

    /**
     * 发送短信
     * @param int $account_id 短信账户id
     * @param array $mobile 手机号码数组
     * @param string $message 消息
     * @return mixed
     */
    public function send($account_id, Array $mobile, $message)
    {

        try {

            DB::beginTransaction();

            //检查短信账户信息
            $account = DB::table('sms_account')->where('id', $account_id)->lockForUpdate()->first();
            if (!$account) {
                throw new \Exception('短信账户不存在', 10001);
            }

            //计算数量
            $num = count($mobile);
            if ($account['quantitative_restriction']) {
                $sms_number = $account['sms_number'] - $num;
            } else {
                $sms_number = $account['sms_number'];
            }
            $sum_use_number = $account['sum_use_number'] + $num;

            //短信数量不足，且有数量限制
            if ($sms_number <= 0 && $account['quantitative_restriction']) {
                throw new \Exception('短信数量不足', 10002);
            }

            //根据业务类型加载不同配置
            if ($account['sms_type'] == 1) {
                $config = [
                    'key' => $this->config['notice_key'],
                    'session_key' => $this->config['notice_session_key'],
                    'pwd' => $this->config['notice_pwd'],
                ];
            } else {
                $config = [
                    'key' => $this->config['marketing_key'],
                    'session_key' => $this->config['marketing_session_key'],
                    'pwd' => $this->config['marketing_pwd'],
                ];
            }
            $config['gateway'] = $this->config['gateway'];

            //根据配置实例不同服务商的短信类
            $class_name = __NAMESPACE__ . '\Agent\\' . ucfirst($this->server);
            $agent = new $class_name($config);

            //发送短信
            $send_result = $agent->send($mobile, '【' . $account['signature'] .'】' . $message);
            $send_status = 2;
            if ($send_result['code'] == 200) {

                //短信发送成功，根据短信数量及累计使用数量
                DB::table('sms_account')->where('id', $account_id)->update(['sms_number' => $sms_number, 'sum_use_number' => $sum_use_number]);

                //扣减短信总数
                DB::table('sms_setting')->where('id', 1)->decrement('sms_number', $num);

                $send_status = 1;

            }

            //保存发送日志
            $insert = [];
            foreach ($mobile as &$m) {
                $insert[] = [
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'account_id' => $account_id,
                    'mobile' => $m,
                    'content' => $message,
                    'send_status' => $send_status,
                    'message' => $send_result['message'],
                ];
            }
            DB::table('sms_send_log')->insert($insert);

            DB::commit();

            return $send_result;

        } catch (\Exception $e) {
            DB::rollback();
            return ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }

    }

}