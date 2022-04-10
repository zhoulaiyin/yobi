<?php

namespace App\Service;

use App\Contracts\PushContract;
use JPush\Client as JPush;

class PushService implements PushContract
{
    public function  sendPushMessage($message,$registrationId) {
        // 账号设置
        $client = new JPush('25b9c94476c9629306a2033e', '68818062ca4386ffe3628028');

        $client->push()
        ->setPlatform('all')
        ->setNotificationAlert($message)
        ->addRegistrationId($registrationId)
        ->setOptions(100000, 3600, null, true)
        ->send();

    }
}