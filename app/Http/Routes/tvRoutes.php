<?php

namespace App\Http\Routes;


use Illuminate\Contracts\Routing\Registrar;

class tvRoutes {

    public function map(Registrar $router) {

        $router->group(['middleware' => ['web']], function ($router) {
            $router->get('webi/tv/login','WeBI\shop\LoginController@tvLogin');  //tv登录页
            $router->get('webi/tv/logout', 'WeBI\shop\LoginController@tvLogout');//tv退出
        });

        $router->group(['middleware' => ['web','tv.service']], function ($router) {//
            $this->tv( $router ); //wap端
        });

    }

    /**
     * WeBI——TV端
     * @param $router
     */
    private function tv($router){
        $router->get('webi/tv/group/list','WeBI\TV\TvListController@group_list');  //分组列表页
        $router->get('webi/tv/group/bi/list','WeBI\TV\TvListController@bi_list');  //分组下的BI列表页
        $router->get('webi/tv/show/{uid}','WeBI\TV\TvListController@show_bi');  //展示单一报表
        $router->get('webi/tv/group/bi/module/get','WeBI\TV\TvListController@get_redis');  //获取设备号 BI报表redis

    }

}