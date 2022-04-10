<?php

namespace App\Http\Routes;


use Illuminate\Contracts\Routing\Registrar;

class commRoutes {

    public function map(Registrar $router) {

        $router->group(['middleware' => ['web']], function ($router) {

            //任务调度
            $router->any('webi/task/exec/{type}', 'Common\execController@exec');

            $router->get('release', 'Common\ReleaseController@release');

            //错误页
            $router->get('error', 'ErrorController@error');
            $router->get('error/{act}', 'ErrorController@error');

            //上传文件
            $router->get('up', 'Common\UploadController@index');

            //上传文件
            $router->any('upload', 'Common\UploadController@upload');

            $router->get('excel/export/{op}','Common\ExcelController@export');
            $router->get('excel/import','Common\ExcelController@import');

            //生成图片验证码
            $router->get('yzm', 'Common\KitController@captcha');

            $router->any('uploadLayui', 'Common\UploadController@uploadLayui'); //layui文本上传图片

            $router->get('webi/color/get','WeBI\shop\webiColor@get');  //webiColor拾取器获取颜色

            $router->get('webi/show','WeBI\shop\ShowController@index');  //前台展示
            $router->get('webi/csql', 'WeBI\shop\ShowController@sql');//前台获取单个模块数据
            $router->get('webi/list/master/get','WeBI\WeBIGlobalController@series');  //获取某个BI的全部相关信息

            $router->get('webi/design/get/rule/{id}', 'WeBI\shop\BIEditController@get_rule');//获取BI数据集
            $router->post('webi/design/edit/bi/dts/save', 'WeBI\shop\BIEditController@save_bi_dts');//保存BI数据集设置

            $router->get('webi/design/edit/choose', 'WeBI\shop\BIEditController@choose');//选择报表类型页面
            $router->get('webi/design/choose/get/{group_id}', 'WeBI\shop\BIEditController@get');//获取指定类型报表模板

            $router->get('webi/design/edit/font/get', 'WeBI\shop\BIEditController@font_get');//获取维护的字体数组
            $router->post('webi/design/edit/bi/position/save', 'WeBI\shop\BIEditController@save_position');//保存BI位置

            $router->get('webi/design/edit/dts/list', 'WeBI\shop\BIEditController@dts_list');//WeBI数据集列表


        });

    }

}