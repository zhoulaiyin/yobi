<?php
/**
 * 官网路由
 */
namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;


class ControlRoutes
{

    public function map(Registrar $router)
    {
        $router->group(['middleware' => ['web']], function ($router) {
            $router->get('/', 'Control\AdminController@main'); //首页
            $router->get('/index', 'Control\AdminController@index'); //首页
        });
    }
}

