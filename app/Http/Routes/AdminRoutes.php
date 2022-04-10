<?php
/**
 * 接口路由
 *
 * @package  Laravel
 * @author   liudaojian <liudaojian@ebsig.com>
 */

namespace App\Http\Routes;

use function foo\func;
use Illuminate\Contracts\Routing\Registrar;

class AdminRoutes
{
    
    public function map(Registrar $router)
    {

        $router->group(['middleware' => ['web']], function ($router) {
            $router->get('admin/login', 'Admin\LoginController@index'); //登录页
            $router->get('admin/login/do', 'Admin\LoginController@login'); //登录操作
        });

        $router->group(['middleware' => ['web','admin.service']], function ($router) {

            $router->get('admin/logout', 'Admin\LoginController@logout');

            $router->get('admin','Admin\WeBIController@main');
            $router->get('admin/dashboard/{dashboard}', 'Admin\WeBIController@main');

            //空白页
            $router->get('admin/index', 'Admin\WeBIController@index');

            $this->user($router);
            $this->dataset($router);
            $this->chartType($router);
            $this->biUser($router);
            $this->biAttribute($router);
            $this->moduleDB($router);
            $this->document($router);
            $this->group($router);
            $this->operation($router);
            $this->task($router);
            $this->permission($router);
        });

    }

    private function user($router){
        $router->get('admin/user/list','Admin\UserController@index');  //列表页
        $router->get('admin/user/list/search','Admin\UserController@search');  //列表页查询
        $router->get('admin/user/edit/{user_id}','Admin\UserController@edit');  //编辑页
        $router->get('admin/user/del/{user_id}','Admin\UserController@delete');//删除
        $router->post('admin/user/edit/save','Admin\UserController@save');  //编辑页保存
        $router->post('admin/user/edit/editPwd','Admin\UserController@editPwd');  //修改密码
    }

    /**
     * 数据集维护
     */
    private function dataset($router){
        $router->any('webi/backend/dataset/index', 'WeBI\backend\DataBIController@index');
        $router->any('webi/backend/dataset/get', 'WeBI\backend\DataBIController@get');
        $router->get('webi/backend/dataset/edit/{id}', 'WeBI\backend\DataBIController@edit');
        $router->get('webi/backend/dataset/del/{id}', 'WeBI\backend\DataBIController@del');
        $router->get('webi/backend/dataset/gettable', 'WeBI\backend\DataBIController@getTable');//拉取表结构
        $router->post('webi/backend/dataset/save', 'WeBI\backend\DataBIController@save');
    }

    /**
     * 图表类型维护
     */
    private function chartType($router){
        $router->get('webi/backend/bi/chart/list','WeBI\backend\ChartController@index'); //图表类型维护列表
        $router->get('webi/backend/bi/chart/edit/{group_id}','WeBI\backend\ChartController@inquiry'); //图表类型分组名查询
        $router->get('webi/backend/bi/chart/movelist/{group_id}','WeBI\backend\ChartController@moveList'); //图表类型分组删除查询
        $router->post('webi/backend/bi/chart/groupDel','WeBI\backend\ChartController@moveSave'); //图表类型分组删除
        $router->post('webi/backend/bi/chart/group/save','WeBI\backend\ChartController@save'); //图表类型分组保存
        $router->get('webi/backend/bi/chart/edit/{id}/{type}','WeBI\backend\ChartController@edit'); //图表类型维护新增、编辑
        $router->post('webi/backend/bi/chart/save','WeBI\backend\ChartController@chartSave'); //图表类型维护保存
        $router->get('webi/backend/bi/chart/del/{chart_id}','WeBI\backend\ChartController@del'); //图表类型维护删除
        $router->get('webi/backend/bi/chart/get/{group_id}','WeBI\backend\ChartController@groupList'); //图表类型分组列表
    }

    /**
     * WEBI用户
     */
    private function biUser($router){
        $router->get('webi/backend/biuser/list','WeBI\backend\BiUserController@index');  //列表页
        $router->get('webi/backend/biuser/list/search','WeBI\backend\BiUserController@search');  //列表页查询
        $router->get('webi/backend/biuser/edit/{user_id}','WeBI\backend\BiUserController@edit');  //编辑页
        $router->get('webi/backend/biuser/del/{user_id}','WeBI\backend\BiUserController@delete');//删除
        $router->post('webi/backend/biuser/edit/save','WeBI\backend\BiUserController@save');  //编辑页保存
        $router->post('webi/backend/biuser/edit/editPwd','WeBI\backend\BiUserController@editPwd');  //修改密码
    }

    /**
     * 属性维护
     */
    private function biAttribute($router){

        /* 色彩维护 */
        $router->get('webi/attribute/color/list','WeBI\backend\ColorController@index');  //列表页
        $router->get('webi/attribute/color/search','WeBI\backend\ColorController@search');  //查询
        $router->post('webi/attribute/color/add','WeBI\backend\ColorController@add');  //新增颜色
        $router->post('webi/attribute/color/edit/{bi_id}','WeBI\backend\ColorController@edit');  //编辑颜色
        $router->get('webi/attribute/color/del/{bi_id}','WeBI\backend\ColorController@del');  //删除颜色

        /* 字体维护 */
        $router->get('webi/attribute/fontfamily/list','WeBI\backend\FontfamilyController@index');  //列表页
        $router->get('webi/attribute/fontfamily/search','WeBI\backend\FontfamilyController@search');  //查询
        $router->post('webi/attribute/fontfamily/sort/save','WeBI\backend\FontfamilyController@save');  //更新排序
        $router->post('webi/attribute/fontfamily/add','WeBI\backend\FontfamilyController@add');  //新增字体
        $router->post('webi/attribute/fontfamily/edit/{font_id}','WeBI\backend\FontfamilyController@edit');  //编辑字体
        $router->get('webi/attribute/fontfamily/del/{font_id}','WeBI\backend\FontfamilyController@del');  //删除字体
    }

    /**
     * 模板库
     */
    private function moduleDB($router){

        $router->get('webi/template/database/list','WeBI\backend\BiTemplateController@index');  //列表页
        $router->get('webi/template/database/grouping','WeBI\backend\BiTemplateController@grouping');  //模板分组页面
        $router->post('webi/template/database/s_grouping','WeBI\backend\BiTemplateController@s_grouping');  //新增模板分组
        $router->get('webi/template/database/del_grouping/{group_id}','WeBI\backend\BiTemplateController@del_grouping');  //删除模板分组
        $router->get('webi/template/database/g_search','WeBI\backend\BiTemplateController@g_search');  //搜索操作
        $router->get('webi/template/database/del/{template_id}/{type}','WeBI\backend\BiTemplateController@del');  //发布、下架操作
        $router->get('webi/template/database/search','WeBI\backend\BiTemplateController@search');  //搜索操作
        $router->get('webi/template/database/group/list','WeBI\backend\BiTemplateController@group_list');  //查询所有分组名
        $router->get('webi/template/database/edit/{template_id}','WeBI\backend\BiTemplateController@edit');  //添加、编辑模板
        $router->post('webi/template/database/save','WeBI\backend\BiTemplateController@save');  //保存操作
        $router->get('webi/template/database/design/{template_id}','WeBI\backend\BiTemplateController@design');  //设计
        $router->post('webi/template/database/save_master', 'WeBI\backend\BiTemplateController@save_master');//修改主表
        $router->post('webi/template/database/attr/save', 'WeBI\backend\BiTemplateController@save_bi_attr');//保存BI属性设置
        $router->post('webi/template/database/create/module', 'WeBI\backend\BiTemplateController@module');//新建BI
        $router->get('webi/template/database/choose/copy', 'WeBI\backend\BiTemplateController@copy');//复制BI
        $router->post('webi/template/replace/module', 'WeBI\backend\BiTemplateController@replace_module');//更换BI
        $router->get('webi/template/database/module/del/{uid}', 'WeBI\backend\BiTemplateController@del_module');//删除BI
        $router->get('webi/template/database/operation/get/{uid}', 'WeBI\backend\BiTemplateController@get_bi');//获取待操作BI信息
        $router->post('webi/template/database/module/save', 'WeBI\backend\BiTemplateController@save_all_module');//更新BI数据集模块信息
        $router->post('webi/template/edit/chart/save', 'WeBI\backend\BiTemplateController@chart_save');//保存BI_CHART属性设置

    }

    /**
     * 文档管理
     */
    private function document($router) {

        $router->get('webi/doc/changelog/list','Document\DocchangelogController@index');
        $router->get('webi/doc/changelog/list/search','Document\DocchangelogController@search');
        $router->get('webi/doc/changelog/edit/{flow_id}','Document\DocchangelogController@edit');  //变更日志列表页
        $router->get('webi/doc/changelog/get/{flow_id}', 'Document\DocchangelogController@get');
        $router->post('webi/doc/changelog/chart/save', 'Document\DocchangelogController@save');

    }
    /**
     * 分组管理
     */
    private function group ( $router ) {
        $router->get('webi/doc/group/list','Document\GroupController@index');  //列表页
        $router->get('webi/doc/group/list/search','Document\GroupController@search');  //列表页查询
        $router->get('webi/doc/group/del/{user_id}','Document\GroupController@del');  //删除
        $router->get('webi/doc/group/edit/{user_id}','Document\GroupController@edit');  //编辑页
        $router->post('webi/doc/group/save','Document\GroupController@save');  //编辑页保存

        $router->get('webi/doc/group/item/list/{user_id}','Document\GroupController@task');  //任务清单列表页
        $router->get('webi/doc/group/item/search','Document\GroupController@task_search');  //任务清单列表页查询
        $router->post('webi/doc/group/item/list/save','Document\GroupController@list_save');  //列表页保存
        $router->get('webi/doc/group/item/add','Document\GroupController@task_add');//任务清单新增页
        $router->get('webi/doc/group/item/edit/{user_id}','Document\GroupController@task_edit');//任务清单编辑页
        $router->post('webi/doc/group/item/save','Document\GroupController@task_save');  //编辑页保存
        $router->get('webi/doc/group/item/del/{task_id}','Document\GroupController@task_del');  //列表页删除
    }

    /**
     * 操作文档
     */
    private function operation ( $router ) {
        $router->get('webi/doc/operation/list','Document\OperationDocumentController@index');  //操作文档分组列表页
        $router->get('webi/doc/operation/list/search','Document\OperationDocumentController@search');  //列表页查询
        $router->get('webi/doc/operation/del/{user_id}','Document\OperationDocumentController@del');  //删除
        $router->get('webi/doc/operation/edit/{user_id}','Document\OperationDocumentController@edit');  //编辑页
        $router->post('webi/doc/operation/save','Document\OperationDocumentController@save');  //编辑页保存
        $router->post('webi/doc/operation/list/save','Document\OperationDocumentController@group_save');

        $router->get('webi/doc/operation/item/list','Document\OperationDocumentController@task');  //任务清单列表页
        $router->get('webi/doc/operation/item/search','Document\OperationDocumentController@task_search');  //任务清单列表页查询
        $router->post('webi/doc/operation/item/list/save','Document\OperationDocumentController@list_save');  //列表页保存
        $router->get('webi/doc/operation/item/get/{flow_id}', 'Document\OperationDocumentController@get');
        $router->get('webi/doc/operation/item/edit/{user_id}','Document\OperationDocumentController@task_edit');//任务清单编辑页
        $router->post('webi/doc/operation/item/save','Document\OperationDocumentController@task_save');  //编辑页保存
        $router->get('webi/doc/operation/item/del/{task_id}','Document\OperationDocumentController@task_del');  //列表页删除
    }

    /**
     * 任务管理
     */
    private function task($router) {

        $router->any('admin/task/manage', 'Admin\TaskManageController@index'); // 任务管理
        $router->any('admin/task/search', 'Admin\TaskManageController@search'); // 查询任务管理
        $router->any('admin/task/get', 'Admin\TaskManageController@get'); // 获取任务单条信息
        $router->any('admin/task/edit', 'Admin\TaskManageController@edit'); // 编辑or添加任务
        $router->any('admin/task/status', 'Admin\TaskManageController@status'); // 暂停或运行任务
        $router->any('admin/task/del', 'Admin\TaskManageController@del'); // 删除任务
        $router->any('admin/task/log', 'Admin\TaskManageController@log'); // 查询日志
    }

    /**
     * 权限管理
     */
    private function permission($router){

        $router->get('admin/permission', 'Admin\Permission\PermissionController@index');//权限列表
        $router->get('admin/permission/search', 'Admin\Permission\PermissionController@search');//权限信息查询
        $router->post('admin/permission/store', 'Admin\Permission\PermissionController@store');//权限信息保存
        $router->get('admin/permission/get/{id}', 'Admin\Permission\PermissionController@get');//权限新增修改页
        $router->get('admin/permission/delete/{id}', 'Admin\Permission\PermissionController@delete');//权限信息删除

        $router->get('admin/permission/group', 'Admin\Permission\PermissionGroupController@index');//权限组列表
        $router->get('admin/permission/group/search', 'Admin\Permission\PermissionGroupController@search');//权限组信息查询
        $router->post('admin/permission/group/store', 'Admin\Permission\PermissionGroupController@store');//权限组信息保存
        $router->get('admin/permission/group/{id}', 'Admin\Permission\PermissionGroupController@get');//权限组新增修改页
        $router->get('admin/permission/group/delete/{id}', 'Admin\Permission\PermissionGroupController@delete');//权限组信息删除
    }

}