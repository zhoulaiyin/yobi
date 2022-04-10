<?php

namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class shopRoutes {

    public function map(Registrar $router) {

        $router->group(['middleware' => ['web']], function ($router) {
            $router->get('webi/shop/login','WeBI\shop\LoginController@index');  //前台登录页
            $router->get('webi/shop/login/do', 'WeBI\shop\LoginController@login'); //登录操作
            $router->get('webi/shop/logout', 'WeBI\shop\LoginController@logout');//退出
            $router->get('webi/shop/register', 'WeBI\shop\RegisterController@index');//注册页
            $router->get('webi/shop/register/do', 'WeBI\shop\RegisterController@register');//注册操作

            $router->get('webi/user/delete', 'WeBI\shop\TaskController@delete');//删除3天前创建用户
        });

        $router->group(['middleware' => ['web','shop.service'], 'prefix'=>'webi'], function ($router) {
            $this->shop( $router ); //BI前台
        });
    }

    //WeBI前台
    private function shop($router) {

        $router->get('list/index','WeBI\shop\WeBIListController@index');  //首页


        $router->get('design/biuser/list','WeBI\shop\BiUserController@index');  //列表页
        $router->get('design/biuser/list/search','WeBI\shop\BiUserController@search');  //列表页查询
        $router->get('design/biuser/edit/{user_id}','WeBI\shop\BiUserController@edit');  //编辑页
        $router->get('design/biuser/del/{user_id}','WeBI\shop\BiUserController@delete');//删除
        $router->post('design/biuser/edit/save','WeBI\shop\BiUserController@save');  //编辑页保存
        $router->post('design/biuser/edit/editPwd','WeBI\shop\BiUserController@editPwd');  //修改密码

        /**
         * BI设计列表页
         */
        $router->post('design/group/edit','WeBI\shop\WeBIListController@editGroup');//新建&编辑分组
        $router->post('design/group/del','WeBI\shop\WeBIListController@delGroup');//删除分组
        $router->get('design/group/search/global/{group_id}','WeBI\shop\WeBIListController@globalGroup');
        $router->get('design/report/list/{group_id}','WeBI\shop\WeBIListController@BIMasterList');//查看分组下报表
        $router->post('design/report/add','WeBI\shop\WeBIListController@addTable');//新建报表
        $router->get('design/report/copy','WeBI\shop\WeBIListController@copy');//复制报表
        $router->get('design/report/del/{bi_id}','WeBI\shop\WeBIListController@del');//删除报表
        $router->get('design/report/search/{info}','WeBI\shop\WeBIListController@search');//搜索报表
        $router->post('design/group/move/away','WeBI\shop\WeBIListController@moveSave');//报表移动至别的分组

        /**
         * BI设计视图列表页
         */
        $router->get('design/views/list', 'WeBI\shop\BIViewsController@index');//WeBI视图库列表首页
        $router->get('design/views/group/list/{group_id}/{source_id}','WeBI\shop\BIViewsController@groupList');//数据集列表
        $router->post('design/views/group/edit','WeBI\shop\BIViewsController@editGroup');//新建、编辑数据集
        $router->get('design/views/group/del/{id}','WeBI\shop\BIViewsController@del');//删除数据集
        $router->get('design/views/table/search', 'WeBI\shop\BIViewsController@search');//数据源下表列表、搜索

        $router->get('design/views/table/linked/list', 'WeBI\shop\BIViewsController@linkList');//数据集下关联表
        $router->get('design/views/make/table/structure', 'WeBI\shop\BIViewsController@makeTableStructure');//将要关联表字段
        $router->get('design/views/group/search/{view_id}/{id}', 'WeBI\shop\BIViewsController@editLinked');//查询、保存关联表字段信息
        $router->post('design/views/group/link/add', 'WeBI\shop\BIViewsController@addLinked');//添加关联表信息
        $router->post('design/views/group/link/del', 'WeBI\shop\BIViewsController@delLinked');//删除关联表信息
        $router->get('design/views/table/search/rule', 'WeBI\shop\BIViewsController@searchFields');//查询表字段

        $router->get('views/source/list', 'WeBI\shop\BIViewsController@sourceList');//数据源列表
        $router->any('views/upload', 'WeBI\shop\BIViewsController@upload');//上传excel到临时路径
        $router->any('views/import', 'WeBI\shop\BIViewsController@import');//导入excel数据
        $router->get('views/download/excel', 'WeBI\shop\BIViewsController@download');//下载excel

        $router->post('views/source/save', 'WeBI\shop\BIViewsController@source_name_save');//数据源名称保存
        $router->get('views/source/table/list', 'WeBI\shop\BIViewsController@source_table_list');//excel数据源表数据信息
        $router->post('views/source/table/save', 'WeBI\shop\BIViewsController@source_table_save');//excel数据源表名称、字段备注保存

        $router->get('views/source/del', 'WeBI\shop\BIViewsController@source_del');//数据源删除
        $router->post('views/source/sql', 'WeBI\shop\BIViewsController@sqlSave');//测试、保存数据库连接

        $router->get('views/table/field/list', 'WeBI\shop\BIViewsController@tableStructure');//拉取表字段信息


        /**
         * BI设计编辑页
         */
        $router->get('design/edit/{uid}', 'WeBI\shop\BIEditController@index');//WeBI后台编辑页
        $router->get('design/get/data/{uid}', 'WeBI\shop\BIEditController@get_data');//WeBI后台查询报表接口数组
        $router->post('design/create/module', 'WeBI\shop\BIEditController@add_module');//新建BI
        $router->post('design/replace/module', 'WeBI\shop\BIEditController@replace_module');//更换BI
        $router->get('design/delete/module/{uid}', 'WeBI\shop\BIEditController@del_module');//删除BI
        $router->get('design/choose/copy', 'WeBI\shop\BIEditController@copy_module');//复制BI
        $router->get('design/operation/get/{uid}', 'WeBI\shop\BIEditController@get_bi');//获取待操作BI信息

        $router->post('design/edit/master/save', 'WeBI\shop\BIEditController@save_master');//修改主表

        $router->post('design/edit/bi/save', 'WeBI\shop\BIEditController@save_bi');//保存BI主体设置
        $router->post('design/edit/bi/attr/save', 'WeBI\shop\BIEditController@save_bi_attr');//保存BI_ATTRIBUTE属性设置
        $router->post('design/edit/chart/save', 'WeBI\shop\BIEditController@save_bi_chart');//保存BI_CHART属性设置
        $router->post('design/edit/bi/module/save', 'WeBI\shop\BIEditController@save_all_module');//更新BI数据集模块信息

        $router->get('design/theme/list', 'WeBI\shop\BIEditController@theme_list');//主题分组信息
        $router->post('design/theme/choose', 'WeBI\shop\BIEditController@theme_choose');//选择主题
        $router->post('design/template/add', 'WeBI\shop\BIEditController@template_add');//上传主题

    }
}