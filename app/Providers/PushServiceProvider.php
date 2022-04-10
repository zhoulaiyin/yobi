<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\PushService;

class PushServiceProvider extends ServiceProvider
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
     * @author LaravelAcademy.org
     */
    public function register()
    {
        //使用singleton绑定单例
        $this->app->singleton('push',function(){
            return new PushService();
        });

        //使用bind绑定实例到接口以便依赖注入
        $this->app->bind('App\Contracts\PushContract',function(){
            return new PushService();
        });
    }
}