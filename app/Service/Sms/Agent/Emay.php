<?php

namespace App\Service\Sms\Agent;

use SoapClient;

class Emay extends Agent
{

    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * 发送短信
     * @param string $mobile 手机号码
     * @param string $message 消息
     * @return mixed
     */
    public function send($mobile, $message)
    {

        $mobile = implode(',', $mobile);

        $params = array(
            'arg0' => $this->config['key'],
            'arg1' => $this->config['session_key'],
            'arg2' => '',
            'arg3' => [$mobile],
            'arg4' => $message,
            'arg5' => '',
            'arg6' => 'GBK',
            'arg7' => 5,
            'arg8' => time()
        );

        try {
            $soap_client = new SoapClient($this->config['gateway']);
        } catch (\SoapFault $sf) {
            return array('code' => $sf->getCode(), 'data' => $sf->getMessage());
        }

        try {

            $result = $soap_client->sendSMS($params);
            if ($result->return === 0) {

                return ['code' => 200, 'message' => 'ok'];

            } else if ($result->return === -1105) {

                $reg_params = [
                    'arg0' => $this->config['key'],
                    'arg1' => $this->config['session_key'],
                    'arg2' => $this->config['pwd']
                ];
                $status = $soap_client->registEx($reg_params);
                if ($status->return === 0) {
                    $result = $status->sendSMS($params);
                    if ($result->return === 0) {
                        return ['code' => 200, 'message' => 'ok'];
                    } else {
                        return ['code' => 100003, 'message' => '短信发送失败，错误代码：' . $result->return];
                    }
                } else {
                    return ['code' => 100002, 'message' => '短信发送失败，错误代码：' . $result->return];
                }

            } else {
                return ['code' => 100001, 'message' => '短信发送失败，错误代码：' . $result->return];
            }

        } catch (\Exception $e) {
            return array('code' => $e->getCode(), 'data' => $e->getMessage());
        }
        
    }

}