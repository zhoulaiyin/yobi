<?php
return [

    /**
     * 短信服务商
     */
    'server' => env('SMS_SERVER', 'emay'),

    /**
     * 各服务商的参数配置
     */
    'connections' => [

        'emay' => [
            'gateway' => env('SMS_GATEWAY'),
            'notice_key' => env('SMS_NOTICE_SERIALNUMBER'),
            'notice_pwd' => env('SMS_NOTICE_PASSWORD'),
            'notice_session_key' => env('SMS_NOTICE_SESSIONKEY'),
            'marketing_key' => env('SMS_MARKETING_SERIALNUMBER'),
            'marketing_pwd' => env('SMS_MARKETING_PASSWORD'),
            'marketing_session_key' => env('SMS_MARKETING_SESSIONKEY'),
        ],

    ]

];