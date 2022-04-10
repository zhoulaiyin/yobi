<?php

namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class wapRoutes {

    public function map(Registrar $router) {

        $router->group(['middleware' => ['web']], function ($router) {
            $router->get('webi/wap/login','WeBI\shop\LoginController@wapLogin');  //wap登录页
            $router->get('webi/wap/logout', 'WeBI\shop\LoginController@wapLogout');//wap退出
        });

        $router->group(['middleware' => ['web','wap.service']], function ($router) {
            $this->wap( $router );
        });

    }

    /**
     * WeBI——wap端
     * @param $router
     */
    private function wap($router){
        $router->get('webi/wap/group/list','WeBI\wap\GroupListController@group_list');  //分组列表页
        $router->get('webi/wap/group/bi/list','WeBI\wap\GroupListController@bi_list');  //分组下的BI列表页
        $router->get('webi/wap/show','WeBI\wap\GroupListController@show');  //分组下的报表
    }

}