<?php

namespace App\Service\Sms\Agent;

abstract class Agent
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 发送短信
     * @param string $mobile 手机号码
     * @param string $message 消息
     * @return mixed
     */
    abstract public function send($mobile, $message);

}