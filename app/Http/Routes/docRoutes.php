<?php
/**
 * Created by zhoulaiyin.
 * User: Administrator
 * Date: 2018/5/29
 * Time: 9:31
 */
namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class docRoutes {

    public function map(Registrar $router) {

        $router->group(['middleware' => ['web']], function ($router) {

            $router->get('doc','Document\WikiController@index'); //文档首页
            $router->get('doc/index','Document\WikiController@index'); //文档首页

            $router->get('doc/blank','Document\WikiController@blank'); //文档首页

            $router->get('doc/group/{id}','Document\WikiController@select'); //展示分组数据项


            $this->changeLog($router);  //变更日志
            $this->operateManual($router); //操作文档
            $this->dataSource($router);   //数据源展示

        });

    }

    /**
     * 变更日志
     * @param $router
     */
    private function changeLog($router){
        $router->get('doc/change/log/index','Document\ChangeLogController@index');//变更日志页面
        $router->get('doc/change/log/get','Document\ChangeLogController@detail');//获取日志详情
    }

    private function operateManual($router){
        $router->get('doc/operate/manual/index','Document\OperateManualController@index');//操作文档页面
        $router->get('doc/operate/manual/item/get','Document\OperateManualController@detail');//子项详情
    }

    private function dataSource($router){
        $router->get('doc/data/source/index', 'Document\DataSourceController@index');  //数据源首页
        $router->get('doc/data/source/get/{id}', 'Document\DataSourceController@get');  //获取单条数据源
        $router->post('doc/data/source/search', 'Document\DataSourceController@search');  //数据源查询
    }
}