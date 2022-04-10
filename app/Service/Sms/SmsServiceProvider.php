<?php

namespace App\Service\Sms;

use App\Service\Sms\SmsService;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sms', function ($app) {
            $server = $app['config']['sms.server'];
            $config = $app['config']['sms.connections'][$server];
            return new SmsService($server, $config);
        });
    }
}
