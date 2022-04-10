<?php

namespace App\Contracts;

interface PushContract
{
     public function sendPushMessage($message,$registrationId);
}