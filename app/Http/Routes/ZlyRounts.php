<?php
/**
 * 微信授权路由
 * Created by zhoulaiyin.
 * Date: 2017/6/18
 * Time: 10:24
 */
namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class ZlyRounts
{

    public function map(Registrar $router)
    {

        $router->any('zly/free', 'ZlyController@freefunc'); //测试方法
        $router->any('zly/sdk/data', 'ZlyController@sdkdata'); //测试方法
        $router->any('zly/test/layui', 'ZlyController@layui'); //layui测试页面
        $router->any('zly/test/page', 'ZlyController@testPage'); //测试页面
        $router->any('zly/test/page/search', 'ZlyController@testListData'); //列表页数据
        $router->any('zly/webi/del/redis', 'ZlyController@delWebiRedis'); //删除webi中的一些redis
        $router->any('zly/webi/color', 'ZlyController@webiColor'); //webiColor插件
        $router->any('zly/vue/js', 'ZlyController@vueJs'); //vue.js练习

        $router->any('zly/self/suit', 'ZlyController@selfSuit'); //

        $router->any('zly/test/echarts', 'ZlyController@testEcharts');


        $router->any('test/bi/master/module/changeData', 'Test\TestController@changeData');
        $router->any('test/excel/export', 'Test\TestController@excelExport');
        $router->any('test/excel/import', 'Test\TestController@excelImport');
        $router->any('test/excel/data', 'Test\TestController@import');

    }

}