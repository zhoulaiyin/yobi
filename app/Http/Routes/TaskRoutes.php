<?php
/**
 * 任务路由
 * Created by zhoulaiyin.
 * Date: 2018/6/7
 * Time: 14:09
 */
namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class TaskRoutes {

    public function map(Registrar $router) {

        $router->group(['middleware' => ['web']], function ($router) {

        });

    }



}