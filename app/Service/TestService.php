<?php

namespace App\Service;

use App\Contracts\TestContract;

class TestService implements TestContract
{
    public function callMe($controller)
    {
        error_log('123124'.$controller);
    }
}