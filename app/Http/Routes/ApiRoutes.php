<?php
/**
 * 接口路由
 *
 * @package  Laravel
 * @author   liudaojian <liudaojian@ebsig.com>
 */

namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class ApiRoutes
{

    public function map(Registrar $router)
    {

        $router->group(['middleware' => 'api'], function($router) {

            //webi开放API
            $this->webi($router);

        });

    }

    /**
     * webi开放API接口路由
     * @param $router
     */
    public function webi($router){
        $router->any('api/webi/user/register' , 'Api\WeBI\UserController@register'); //用户注册接口
    }

}