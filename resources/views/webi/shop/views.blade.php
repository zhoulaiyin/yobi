@extends('webi.layout')

@section('css')
    <link rel="stylesheet" href="/css/webi/list.css?v=201803221330">
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
    <style>
        .cur-list{
            padding-left: 18px;
            color: #fff;
            background: #70a7ff;
        }
        .webuploader-pick{
            background: #fff;
            height:100%;
            margin: 0 auto;
            width: 100%;
        }
        .dropdown-menu{
            min-width: 60px;
            margin-top: -10px;
        }
        .linked-content{
            position: relative;
            width:100%;
            float: left;
            display: block;
            overflow: hidden;
            height: 100%;
        }
        .linked-list {
            display:none;
            float: right;
            width:25%;
            right: -15px;
            /*position: absolute;*/
            overflow-y: auto;
            background-color: #F7F7F7;
            height: 100%;
            min-height: 550px;
        }
        .linked-list li{
            margin: 0px 18px 0px;
            height:40px;
            line-height: 40px;
            text-align: center;
            margin-bottom: 7px;
            border-radius: 5px;
            background: #d9e2f2;
        }
        .linked-list li a{
            line-height:40px;
            height:40px;
        }
        .input-search{
            border-bottom: 1px solid #ddd;
            width: 97%;
            float: left;
            position: relative;
            padding-top: 5px;
            margin-left: 1%;
        }
        .input-search i{
            position: absolute;
            right: 0;
            bottom: 0;
            width: 40px;
            height: 34px;
            background: url(/images/webi/bi/icon-search.png) no-repeat center center;
            cursor: pointer;
        }

        .ul-list{
            padding-top: 10px;
        }
        .add-list a:hover{
            text-decoration:none;
        }
        /*right-cont 变成100%*/
        .right-cont-w{
            position: relative;
            width: 98.4%;
            float: left;
            /*left: 50px;*/
            height: 550px;
            overflow-x: scroll;
            white-space: nowrap;
        }
        .right-cont{
            top: 58px;
            position: fixed;
            width: 63%;
            overflow-x: scroll;
            cursor: pointer;
            height: 550px;
            white-space: nowrap;
        }
        .test-t{
            border: solid 1px #ddd;
            text-align: center;
            width: 240px;
            height:36px;
            padding: 10px;
            margin: 10px 0 ;
            -webkit-border-radius: 15px;
            -moz-border-radius: 15px;
            -ms-border-radius:15px;
            -o-border-radius:15px;
        }
        .parallel{
            display:inline-block
        }
        /*group-nav li 点击的效果*/
        .liclick {
            border-left: 2px solid #70a7ff;
            background: #fff;
            border-bottom: 1px solid #fff;
        }
        .liclick>div >a{
            color: #70a7ff !important;
        }
        .input-search .form-control{
            border:none;
            -webkit-box-shadow:none;
        }
        .ul-list li span{
            width: 100%;
            display: block;
            padding: 0 20px;
            overflow: hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
        }
        .fl {
            float: left;
        }
        .fr {
            float: left !important;
        }
        .right-cont-fl{
            width: 200px;
            padding-top: 55px;
            position: relative;
            display:inline-block;
            float: left;
        }
        .right-cont-fl span ,.right-cont-fr-text span{
            height: 30px;
            line-height: 30px;
            border: 1px solid #ddd;
            border-radius: 15px;
            -moz-border-radius:15px;
            -ms-border-radius:15px;
            -o-border-radius:15px;
            padding: 0 15px;
            width: 85%;
            display: block;
            margin-left: 10%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .right-cont-fr-text span {
            margin-left: 0;
            width: 100%;
        }
        .right-cont-fl .glyphicon,.right-cont-fr-text .glyphicon{
            position: absolute;
            right: 5%;
            padding-right: 15px;
            top:56.4px;
            color: #999999;
            width: 10px;
            height: 10px;
            margin: 10px;
            display: block;
            cursor: pointer;
            background:  url("/images/webi/jointype1.png") 0    -147px  no-repeat;
        }
        .right-cont-fr-text .glyphicon{
            position: absolute;
            right: 5px;
            top:-12px;
        }
        .right-cont-fl em{
            height: 2px;
            background: #ddd;
            width: 5%;
            display: block;
            margin-top: 15px;
        }
        .right-cont-fr{
            width: 350px;
            padding: 0px;
            /*padding-top: 70px;*/
            display:inline-block;
            float: none;
            position: absolute;
        }
        .right-cont-fr ul,.right-cont-fr li{
            display: block;
        }
        .right-cont-fr li {
            padding-bottom: 62.7px;
            position: relative;
        }
        .right-cont-fr li:first-child .fight-cont-fl-left {
            border-top-right-radius: 15px;
        }
        .right-cont-fr li:first-child em{
            border-bottom-left-radius:0;
            margin-top:-5px;
            border-left: none;
        }
        .right-cont-fr li:last-child .fight-cont-fl-left {
            border-right:  none;
        }
        .right-cont-fr-img{
            width: 40%;
            position: relative;
            margin-top: -13px;
        }
        .right-cont-fr li:first-child .imgbox{
            top:-3px !important;
        }
        .right-cont-fr li .imgbox{
            width: 44px;
            height: 28px;
            display: block;
            position: absolute;
            top:4px;
            left: 50%;
            margin-left: -14px;
        }
        .right-cont-fr-img span{
            width: 44px;
            height: 28px;
            display: block;
            position: absolute;
            top:-4px;
            left: 50%;
            margin-left: -17px;
            cursor: pointer;
        }
        .right-cont-fr-img .left-img{
            background: #fff url("/images/webi/jointype1.png") 0   -28px no-repeat;
        }
        .right-cont-fr-img .right-img{
            background: #fff url("/images/webi/jointype1.png") 0    -56px  no-repeat;
        }
        .right-cont-fr-img .inner-img{
            background: #fff url("/images/webi/jointype1.png") no-repeat;
        }
        .right-cont-fr-img em{
            height: 19px;
            border-bottom: 2px solid #ddd;
            display: block;
            width: 100%;
            border-left: 2px solid #ddd;
            border-bottom-left-radius: 15px;
        }
        .right-cont-fr-text{
            width: 60%;
            margin-left: 0;
            margin-top: -12px;
        }
        .fight-cont-fl-left{
            /*height: 60px;*/
            height: 56.8px;
            position: absolute;
            left: -3px;
            width: 5px;
            border-right: 2px solid #ddd;
        }
        .bomb-box{
            width: 500px;
            overflow: hidden;
            padding: 5px 10px;
        }
        .bomb-box  li{
            width: 33.33%;
            float: left;
            height: 80px;
            margin: 0 auto;
            text-align: center;
            cursor: pointer;
        }
        .bomb-box li span{
            margin: 20px auto 0;
            width: 44px;
            height: 28px;
            display: block;
        }
        .bomb-box li .inner-img{
            background: #fff url("/images/webi/jointype1.png") no-repeat;
        }
        .bomb-box li .left-img{
            background: #fff url("/images/webi/jointype1.png") 0   -28px no-repeat;
        }
        .bomb-box li .right-img{
            background: #fff url("/images/webi/jointype1.png") 0    -56px  no-repeat;
        }
        /*.bomb-box ul li点击以后的背景颜色*/
        .clickli{
            background: #f1f1f1;
        }
        .clickli  .inner-img{
            background: #f1f1f1 url("/images/webi/jointype1.png") no-repeat !important;
        }
        .clickli  .left-img{
            background: #f1f1f1 url("/images/webi/jointype1.png") 0   -28px no-repeat !important;
        }
        .clickli  .right-img{
            background: #f1f1f1 url("/images/webi/jointype1.png") 0    -56px  no-repeat !important;
        }
        .bomb-box li p{
            line-height: 35px;
            height: 35px;
        }
        .bomb-box-option{
            width: 100%;
        }
        .bomb-box-option ul{
            overflow: hidden;
        }
        .bomb-box-option ul li{
            margin: 2px 0;
            padding: 0 10px;
            height: 30px;
            line-height: 30px;
            display: block;
            width: 100%;
            background: #fff;
        }
        .bomb-box-option ul li:first-child{
            background: #f1f1f1;
        }
        /*.bomb-box-option ul li:first-child .bomb-box-equal{*/
            /*font-size: 0;*/
        /*}*/
        .bomb-box-option ul li:first-child .bomb-box-delete i{
            display: none;
        }
        .bomb-box-option select{
            width: 100%;
            border:none;
            padding-left: 10px;
            height: 30px;
            line-height: 30px;
            background: url("/images/webi/incon-down.png") no-repeat scroll right 10px center transparent;
        }
        .bomb-box-select{
            width: 42%;
            position: relative;
        }
        .bomb-box-select select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        .bomb-box-option p{
            text-align: center;
            color: #b9b9b9;
            height: 30px;
            line-height: 30px;
        }
        /*bomb-box-select 下select 点击效果*/
        .hover-option{
            color: #f94c43;
            border:1px solid #f94c43 !important; ;
            background: url("/images/webi/incon-down-h.png") no-repeat scroll right 10px center transparent !important;
        }
        .bomb-box-equal{
            width: 8%;
            text-align: center;
        }
        .bomb-box-but{
            border-top: 1px solid #ddd;
            height: 55px;
            padding-top: 10px;
        }
        .bomb-box-but button{
            height: 35px;
            line-height: 35px;
            width: 75px;
            text-align: center;
            margin-left: 10px;
            background: #fff;
            border-radius:5px;
            -moz-border-radius:5px;
            -ms-border-radius:5px;
            -o-border-radius:5px;
        }
        .bomb-box-but .confirm{
            background: #70a7ff;
            border-color: #70a7ff;
            color: #ffff;
            margin-left: 300px;
        }
        .bomb-box-delete{
            width: 8%;
        }
        .glyphicon-trash{
            cursor: pointer;
        }
        .top-title2{
            padding: 0 20px;
            width: 100%;
            height: 58px;
            box-shadow: 0 0 6px 6px rgba(0,0,0,.05);
            line-height: 58px;
            z-index: 22;
            background: #fff;
        }
        .left-list2{
            height: 100%;
            width: 109%;
            z-index: 2;
            float: left;
            overflow-y: scroll;
            margin-right: -17px;
        }
        .left-list2::-webkit-scrollbar {
            display: none;
        }
        .left-list2 .add-list {
            padding: 14px 30px 14px 12px;
            height: 66px;
            cursor: pointer;
        }
        .left-list2 ul li {
            height: 48px;
            line-height: 48px;
            padding-left: 20px;
            cursor: pointer;
        }
        .left-list2 ul li a{
            color: #fff;
        }
        .layui-icon-spread-left{
            position: absolute;
            top: 7px;
            width: 26.5%;
            right: 0;
            font-size: 18px !important;
        }
        .layui-icon-shrink-right{
            position: relative;
            top: 7px;
            font-size: 18px !important;
        }
        .li-lable{
            box-shadow: 0 0 0 2px rgba(81,130,227,.15),inset 0 0 0 2px #f88407 !important;
        }
        .left-button{
            float: left;
            text-align: center;
            color: #fff;
            font-size: 14px;
        }
        .button-group{
            text-align: center;
            max-width: 270px;
            height: 33px;
            padding: 10px;
            margin-left: 26%;
            font-size: 14px;
            float: right;
        }
        .left-button a{
            color: #fff;
        }
        .overview {
            padding: 0 7px;
            background: #5cd489;
            margin-top: 12px;
            width: 140px;
            height: 32px;
            text-align: center;
            line-height: 32px;
            color: #fff;
            border-radius: 20px;
            display: block;
            float: left;
            margin-right: 20px;
        }
        #upload-source div:last-child,.upload-source-div div:last-child{
            height: 100% !important;
            width: 100% !important;
        }

    </style>
@endsection

@section('content')
    <div id="wrapper">
        <div class="content" style="padding-top:0;width:100%;overflow: hidden;">

            <div class="left-sider">
                <div class="left-logo">
                    <a href="/webi/list/index"><img src="/images/webi/logo.png" alt="WeBI" width="179px;" height="59px;"></a>
                </div>
                <!--左侧列表-->
                <div class="left-list2">
                        <div class="add-list">
                            <div onclick="_GROUP.add()">
                                <span>数据集 <i>+ </i></span>
                            </div>
                        </div>
                        <ul id="group-nav">

                        </ul>
                </div>
            </div>

            <div class="right-sider">
                <div class="top-title2" >
                    <div class="left-button">
                        <span>数据源类型:&nbsp;&nbsp;</span>
                        <div class="data-source">
                            <select class="data-source-option" id="data-source-option">
                                <option value="0">请选择数据源</option>
                                <option value="1">微电汇数据源</option>
                                <option value="2">Excel数据源</option>
                                <option value="3">MYSQL数据源</option>
                            </select>
                        </div>
                        <div class="source-name-remind" data-source="0"></div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-default" role="button" onclick="JavaScript:history.go(-1)" style="float: right;margin-top: 3px;">返回</button>
                    </div>
                </div>
                <!--主内容-->
                <div class="linked-content" id="linked-content">
                    <input type="hidden" id="groupId" value=""/>
                    {{--关联列表--}}
                    <div class="right-cont-w" id="right-content"></div>

                    {{--链表区域--}}
                    <div>
                        <div>
                            <div class="layui-icon layui-icon-spread-left" id="left-side-hide" style="display: none;;cursor:pointer;"  onclick="_G.hideTable();"></div>
                            <div class="layui-icon layui-icon-shrink-right" id="left-side-show" style="display: inline-block;;cursor:pointer;"  onclick="_G.showTable(1);"></div>
                        </div>
                        <div class="linked-list" >
                            <div style="width: 100%;cursor: pointer; height: 50px">
                                <div class="input-search">
                                    <input type="text" id="search" class="form-control" placeholder="搜索表" />
                                    <i class="search" onclick="_G.searchTableList()"></i>
                                </div>
                            </div>
                            <div>
                                <ul class="ul-list">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{--数据源上传及列表页面--}}
                <div id="layer-source-box" class="layer-source-box" style="display: none">
                    <div class="source-header">
                        <div class="source-header-name"><h3>数据源</h3></div>
                    </div>
                    <div id="source-list-box">
                        {{--excel--}}
                        <div id="layer-source-excel" style="display: none;">
                            {{--数据源列表--}}
                            <div class="left-source">
                                <table class="layui-table source-table" lay-skin="line">
                                    <tbody class="source-layui-table-header">
                                    <tr>
                                        <td class="operation-source">操作</td>
                                        <td>数据源名称</td>
                                        <td>最近修改时间</td>
                                        <td>文件大小</td>
                                        <td>行数</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{--上传数据源\连接数据库--}}
                            <div class="right-source">
                                {{--上传数据源--}}
                                <div  id="ant-right-excel" class="ant-right-upload">
                                    <form class="layui-form layui-form-pane source-content source-excel">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">数据源名称</label>
                                            <div class="layui-input-block">
                                                <input value="" class="layui-input source-name" id="source-name">
                                            </div>
                                        </div>
                                        <div class="layui-form-item" id="upload-content-button">
                                            <div class="upload-source" id="upload-source" style="width: 100%;height:300px;">
                                                <div id="upload-rate"></div>
                                                <div class="table-name" id="table-name"><p></p></div>
                                                <div class="upload-button">
                                                    <i class="layui-icon layui-icon-upload-drag"></i>
                                                </div>
                                                <p>点击或者拖拽文件至此区域上传文件</p>
                                                <p class="text-one">只支持*.xls,*.xlsx文件</p>
                                            </div>
                                        </div>
                                    </form>
                                    <div><button class="btn btn-success" id="source-button-upload">保存</button></div>
                                </div>
                            </div>
                        </div>
                        {{--SQL--}}
                        <div id="layer-source-sql" style="display: none;">
                            {{--数据源列表--}}
                            <div class="left-source">
                                <table class="layui-table source-table" lay-skin="line">
                                    <tbody class="source-layui-table-header">
                                    <tr>
                                        <td class="operation-source">操作</td>
                                        <td>数据源名称</td>
                                        <td>数据库主机地址</td>
                                        <td>数据库名称</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{--上传数据源\连接数据库--}}
                            <div class="right-source">
                                {{--连接数据库--}}
                                <div id="ant-right-sql" class="ant-right-upload">
                                    <form class="layui-form layui-form-pane source-content source-sql" id="source-sql">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">数据库地址</label>
                                            <div class="layui-input-block">
                                                <input value="" type="text" name="db_host" class="layui-input source-name" placeholder="主机名">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">数据库</label>
                                            <div class="layui-input-block">
                                                <input value="" type="text" name="db_database" class="layui-input source-name" placeholder="数据库名称">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">用户名</label>
                                            <div class="layui-input-block">
                                                <input value="" type="text" name="db_user" class="layui-input source-name" placeholder="用户名">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">密码</label>
                                            <div class="layui-input-block">
                                                <input value="" type="password" name="db_pwd" class="layui-input source-name" placeholder="密码">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">端口</label>
                                            <div class="layui-input-block">
                                                <input value="3306" type="text" name="db_port" class="layui-input source-name" placeholder="端口号">
                                            </div>
                                        </div>
                                    </form>
                                    <div class="source-button-test">
                                        <button class="btn btn-info" id="source-button-test" onclick="_SOURCE.commitSqlSource(0,1);">测试连接</button>
                                    </div>
                                    <div class="source-button-connect">
                                        <button class="btn btn-success" id="source-button-connect" onclick="_SOURCE.commitSqlSource(0,4);">保存</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{--分组列表--}}
            <div id="group-nav-each" style="display: none">
                <li id="view_g_view_id" data-id="g_view_id">
                    <div style="float:left; width:82%;" class="group-each" id="g_g_view_id">
                        <a id="group_g_view_id">g_group_title</a>
                    </div>
                    <div type="button" style="height: 48px;float:left;"  class="dropdown" id="group_g_view_id">
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="glyphicon glyphicon-option-vertical text-style" ></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right" >
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick=" _GROUP.edit('g_view_id')";>编辑</a></li>
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick=" _GROUP.del('g_view_id');">删除</a></li>
                        </ul>
                    </div>
                </li>
            </div>
            {{--链表列表--}}
            <div id="tableList" style="display: none">
                <li id="t_TableId" class="link_content_list" >
                    <div class="source-table-name" data-id="TableId" data-name="TableName"><span class="fr">Description </span></div>
                    <div class="source-table-detail layui-icon layui-icon-about" data-id="TableId" data-name="TableName" data-remarks="Description" title="详情"></div>
                    <div class="source-table-choose layui-icon layui-icon-ok" data-id="TableId" data-name="TableName" title="选择"></div>
                </li>
            </div>
            {{--添加分组弹窗--}}
            <div id="layer_add_group" style="display: none">
               <form id="title-form-header" onsubmit="return false;" class="form-horizontal" role="form" style="margin-top: 20px;">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="title-header"><span class="red pr5">*</span>&ensp;名称：</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="title-header" id="title-header" placeholder="请输入名称" value="">
                           </div>
                        </div>
                  </form>
            </div>
            {{--修改关联表信息--}}
            <div id="layer-linked-edit" style="display: none">

                    <ul class="bomb-box" id="layer-edit">
                        <li data-code="1"><span class="left-img"></span>
                            <p>左联</p>
                        </li>
                        <li data-code="2" id="li-2" ><span class="inner-img" ></span>
                            <p>内联</p>
                        </li>
                        <li data-code="3"><span class="right-img"></span>
                            <p>右联</p>
                        </li>
                    </ul>
                    <div class="bomb-box-option">
                        <ul id="select_link">

                            <li>
                                <div class="bomb-box-select fl">
                                    <select id="table_l_l">
                                    </select>
                                </div>
                                <div class="bomb-box-equal fl" style="font-size: 0;">=</div>
                                <div class="bomb-box-select fl">
                                    <span id="table_rs" data-name="tableName-select">selectTable</span>
                                </div>
                            </li>

                            <div id="table_field_hidden">
                                <li >
                                    <div class="bomb-box-select fl">
                                        <select class="fields_l_l">
                                        </select>
                                    </div>
                                    <div class="bomb-box-equal fl">=</div>
                                    <div class="bomb-box-select fl">
                                        <select  class="fields_rr">
                                        </select>
                                    </div>

                                    <div class="bomb-box-delete fl">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    </div>
                                </li>

                            </div>
                        </ul>
                        <p onclick="_POP.add_row()" style="cursor: pointer">添加新的链接字段</p>
                    </div>

            </div>
            {{--链表主表--}}
            <div id="link_primarTabe" style="display: none">
                <div class="f1 right-cont-fl" id="li_parentId" data-parent="parentId">
                    <span class="fl" id="primarName">primarName</span>
                    <i class="glyphicon"></i>
                    <em class="fr"></em>
                </div>
            </div>
            {{--追加关联div表名--}}
            <div id="append-div-ul" style="display:none;">
                <div style="left:lf_valpx;top:t_valpx" class="right-div">
                    <ul>
                        li_list
                    </ul>
                </div>
            </div>
            {{--链表关联子表--}}
            <div id="link_table_div" style="display: none">
                <li class="li_uuId" id="li_uuId" data-parent="parentId">
                    <div class="fight-cont-fl-left"></div>
                    <div class="right-cont-fr-img fl">
                        <span class="join_class"  title="点击可编辑"></span>
                        <em></em>
                    </div>
                    <div class="right-cont-fr-text fl">
                        <span id="sublistNmae" >sublistNmae</span>
                        <i class="glyphicon" ></i>
                    </div>
                </li>
            </div>
            {{--链表字段--}}
            <div id="link-layer-hidden" style="display: none">
                <li>
                    <div class="bomb-box-select fl">
                        <select class="fieldsLs">
                        </select>
                    </div>
                    <div class="bomb-box-equal fl">=</div>
                    <div class="bomb-box-select fl">
                        <select class="fieldsRs">
                        </select>
                    </div>
                    <div class="bomb-box-delete fl">
                    <i class="glyphicon glyphicon-trash" ></i>
                    </div>
                </li>
            </div>
            {{--数据集分组--}}
            <div id="group-li-html" style="display: none;">
                <li id="view_$view_id" data-id="$view_id">
                    <div style="float:left; width:82%;" class="group-each" id="g_$view_id">
                        <a id="group_$view_id">$view_name</a>
                    </div>

                    <div type="button" style="height: 48px;float:left;"  class="dropdown"  id="group_$view_id">
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                             <i class="glyphicon glyphicon-option-vertical text-style" ></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right" >
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;" onclick=" _GROUP.edit('$view_id')";>编辑</a></li>
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick="_GROUP.del('$view_id');">删除</a></li>
                        </ul>
                    </div>
                </li>
            </div>
            {{--excel数据源表格tr--}}
            <div id="source-tr-html" style="display: none;">
                <table id="source-table-html">
                    <tr class="table-tr-$source_id">
                        <td class="operation">
                            <div class="layui-icon  layui-icon-ok" title="选择该数据源" onclick="_SOURCE.selectSource('$source_id')"></div>
                            <div class="layui-icon layui-icon-table" data-toggle="modal" data-target="#myModal" title="表格数据" onclick="_SOURCE.pop.tableList('$source_id')"></div>
                            <div class="layui-icon layui-icon-edit"  title="编辑数据源名称" onclick="_SOURCE.edit('$source_id')"></div>
                            <div class="layui-icon layui-icon-delete"  title="删除数据源" onclick="_SOURCE.delete('$source_id')"></div>
                            <div class="layui-icon layui-icon-upload-circle" id="updata-$source_id" onclick="_SOURCE.update('$source_id')" data-id="$source_id" title="更新数据源"></div>
                            <div class="layui-icon layui-icon-about"  title="查看数据源表" onclick="_SOURCE.tableList('$source_id','$group_id')"></div>
                        </td>
                        <td class="source-name-$source_id">$source_name</td>
                        <td>$updated_at</td>
                        <td>$file_size <i class="layui-icon layui-icon-download-circle" style="padding-left: 3px;" title="下载数据源文件" onclick="_SOURCE.download('$source_id')"></i></td>
                        <td>$line_num</td>
                    </tr>
                </table>
            </div>
            {{--sql数据源表格tr--}}
            <div id="source-sql-html" style="display: none;">
                <table id="source-table-html">
                    <tr class="table-tr-$source_id">
                        <td class="operation">
                            <div class="layui-icon  layui-icon-ok" title="选择该数据源" onclick="_SOURCE.selectSource('$source_id')"></div>
                            <div class="layui-icon layui-icon-edit"  title="编辑数据源名称" onclick="_SOURCE.edit('$source_id')"></div>
                            <div class="layui-icon layui-icon-delete"  title="删除数据源" onclick="_SOURCE.delete('$source_id')"></div>
                            <div class="layui-icon layui-icon-upload-circle" id="updata-$source_id" onclick="_SOURCE.update('$source_id')" data-id="$source_id" data-name="$source_name" data-host="$db_host" data-database="$db_database" data-user="$db_user"  data-port="$db_port" title="更新数据源"></div>
                            <div class="layui-icon layui-icon-about"  title="查看数据源表" onclick="_SOURCE.tableList('$source_id','$group_id')"></div>
                        </td>
                        <td class="source-name-$source_id">$source_name</td>
                        <td>$db_host</td>
                        <td>$db_database</td>
                    </tr>
                </table>
            </div>
            {{--数据源表弹窗--}}
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" style="width: 800px;height: 480px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                {{--来源名--}}
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="modal-table-box">
                                <table class="layer-table">
                                    {{--表数据--}}
                                </table>
                            </div>
                            <div class="modal-page">
                                <div id="page" class="source-table-page"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--更新Excel数据源弹层--}}
            <div id="layer-excel-source-update" class="layer-excel-source-update" style="display: none;">
                <form>
                    <div class="upload-source-div" id="upload-source-div" style="width: 400px;height:200px;">
                        <div class="remind-box">
                            <div id="layer-upload-rate"></div>
                            <div class="upload-button">
                                <i class="layui-icon layui-icon-upload-drag"></i>
                            </div>
                            <p>点击或者拖拽文件至此区域上传文件</p>
                            <p class="text-one">只支持*.xls,*.xlsx文件（不超过1M）</p>
                        </div>
                    </div>
                </form>
            </div>
            {{--更新SQL数据源弹层--}}
            <div id="layer-sql-source-update" class="layer-sql-source-update">
                <form class="layui-form layui-form-pane source-content" id="source-content-sql">
                    <div class="layui-form-item">
                        <label class="layui-form-label">数据库地址</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_host" value="" class="layui-input db_host" id="db_host" placeholder="主机名">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">数据库</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_database" value="" class="layui-input db_database" id="db_database" placeholder="数据库名称">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">用户名</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_user" value="" class="layui-input db_user" id="db_user" placeholder="用户名">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">密码</label>
                        <div class="layui-input-block">
                            <input value="" type="password" name="db_pwd" class="layui-input source-name" id="db_pwd" placeholder="密码">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">端口</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_port" value="" class="layui-input db_port" id="db_port" placeholder="端口号">
                        </div>
                    </div>
                    <div class="test-button" onclick="_SOURCE.commitSqlSource(0,3);">测试连接</div>
                </form>
            </div>
            {{--查看数据源下所有表弹层--}}
            <div id="layer-source-table" class="layer-source-table" style="display: none;">
                <div>
                    <table class="layer-source-table-box layui-table" lay-skin="line">
                    </table>
                </div>

                <div class="modal-page">
                    <div id="layer-page" class="source-table-page" style="text-align: center;"></div>
                </div>
            </div>
            {{--查看数据源下所有表弹层tr--}}
            <div id="layer-source-table-tr">
                <table id="layer-source-tr-html">
                    <tr class="table-tr">
                        <td class="table-list-name">$table_name</td>
                        <td class="source-name-$source_id">$table_remarks</td>
                        <td><div class="operation layui-icon layui-icon-about" data-name="$table_name" data-remarks="$table_remarks" title="详情"></div></td>
                    </tr>
                </table>
            </div>
            {{--查看数据源表字段信息--}}
            <div id="layer-source-field" class="layer-source-field" style="display: none">
                <div class="layer-source-field-comment">
                    <div> <lable>表名称：</lable><span class="field-table-name"></span> </div>
                    <div style="margin-top:10px;"><lable>表描述：</lable><span class="field-table-remarks"></span></div>
                </div>
                <div>
                    <table class="layer-source-field-box layui-table" lay-skin="line">
                        <tr>
                            <td class="table-list-name">字段名称</td>
                            <td>字段类型</td>
                            <td>字段描述</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript" src="/libs/webuploader/webuploader.js"></script>
    <script>

        //点击修改选中字段关联类型
        $(document).on('click','.bomb-box li',function (){//更改连接方式
            $(this).addClass("clickli").siblings().removeClass("clickli");
        }).on('click','.source-name-remind',function (){//点击数据源名展开弹层
            _SOURCE.showlayerSource();
        }).on('change','#data-source-option',function () { //select更改数据源
            $('.source-name-remind').empty();//清空头部数据源名称

            $('#right-content').empty();//清空右侧主关联表信息
            _GROUP.list();//更新分组

            switch($(this).val()) {

                case '0': //请选择
                    _G.source_id = 0;
                    _SOURCE.hidelayerSource();//隐藏数据源列表弹层
                    $('.linked-list').fadeOut(1500);//隐藏数据源列表
                    break;

                case '1':   //微电汇数据源
                        _G.source_id = 1;
                        if($('#left-side-show').hasClass('layui-icon-shrink-right')){
                            _SOURCE.hidelayerSource();//隐藏数据源列表弹层
                        }
                    break;

                default:   //Excel、SQL数据源
                    _SOURCE.showlayerSource();

            }

        }).on('blur','.pop-title',function () {//修改pop数据源名称
            _SOURCE.pop.save_name($(this).attr('data-id'));
        }).on('blur','.fieldRemarks',function () {//修改pop数据源字段备注
            var data ={};
            data.table_id = $(this).attr('data-table');
            data.field_name =  $(this).attr('data-id');
            data.field_remark = $(this).val();
            data.edit_field = 2;

            _SOURCE.pop.remarks(data);
        });

        $('#data-source-option').val('0');//页面数据源初始化

        $(document).ready(function () {

            _GROUP.list();//展示左侧数据集

            //添加分组弹窗
            layer_add_group=$('#layer_add_group').html();
            layer_add_group = layer_add_group.replace(/title-form-header/g, "title-form");
            layer_add_group = layer_add_group.replace(/title-header/g, "title");


            //关联信息弹窗
            layer_linked_edit=$("#layer-linked-edit").html();
            layer_linked_edit = layer_linked_edit.replace(/layer-edit/g, "edit-link");
            layer_linked_edit = layer_linked_edit.replace(/table_l_l/g, "table_l");
            layer_linked_edit = layer_linked_edit.replace(/fields_rr/g, "fields_r");
            layer_linked_edit = layer_linked_edit.replace(/fields_l_l/g, "fields_l");
            layer_linked_edit = layer_linked_edit.replace(/li-2/g, "li_select");
            layer_linked_edit = layer_linked_edit.replace(/table_field_hidden/g, "table-field");
            layer_linked_edit = layer_linked_edit.replace(/table_rs/g, "table_r");

        });
    </script>

    <script>

            /**
             *   _G  全局操作对象
             *   _GROUP  分组操作对象
             *   _POP  链接弹层操作对象
             *   G_handle  链表节点操作对象
             */
            var _G = {},
                _GROUP = {},
                _POP = {},
                _SOURCE = {},
                G_handle = { };


            _G.globalJosn = {};  //存储当前选中分组的链表信息
            _G.source_id = 1;   //SQL、 Excel数据源ID

            _POP.l_options = [];  //存储字段设置弹层里面的左侧表字段信息
            _POP.r_options = [];  //存储字段设置弹层里面的右侧表字段信息
            _POP.error_msg = "";  //存储字段更改重复报错信息

            _SOURCE.pop = {};//数据源表格弹层

            var parent = document.getElementById("right-content");//链表节点的父级--删除时使用


            layui.use('upload', function () {
                var upload = layui.upload;

                //上传数据源
                upload.render({
                    elem: '.upload-source',
                    accept: 'file',
                    acceptMime: 'xlsx|xls',
                    auto: false,
                    url: '/webi/views/upload?action=webi',
                    bindAction: '#source-button-upload',
                    choose: function(obj){
                        var $li = $( '#upload-rate'),
                            $percent = $li.find('.progress .progress-bar');

                        // 避免重复创建
                        if ( !$percent.length ) {

                            $('<div class="progress progress-striped active">' +
                                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                                '<p>' +
                                '</p>' +
                                '</div>' +
                                '</div>').appendTo( $li ).find('.progress-bar');

                            obj.preview(function(index, file, result){
                                $li.find('p').text(file.name);
                            });

                        }

                    },
                    done: function(res, index, upload){ //上传后的回调
                        if (res.code == 200) {
                            _SOURCE.commitExcelSource();
                        } else {
                            layer.msg(res.message,{icon:2,time:1500});
                        }
                    }
                });

            });


            //绑定更新数据源
            var updatePloader = WebUploader.create({
                auto: false,
                swf: '/libs/webuploader/Uploader.swf',
                server: '/webi/views/upload?action=webi',
                pick: '#upload-source-div',
                resize: false
            });
            updatePloader.on( 'fileQueued', function( file ) {
                var remind = $( '#layer-upload-rate');
                    remind.append( '<div id="' + file.id + '" class="item">' +
                    '<h4 class="info" style="color: green;">' + file.name + '</h4>' +
                    '<p class="state" style="color: green;">等待上传...</p>' +
                    '</div>' );
            });
            updatePloader.on( 'uploadSuccess', function( file, res ) {
                $('#layer-upload-rate').find('.item').remove();//移除上传提示
                if(res.code != 200){
                    layer.msg(res.message,{icon:2,time:3000});
                    return false;
                }
                layer.msg('更新成功',{icon:1,time:1500});
                layer.closeAll();
                _SOURCE.sourceList();//更新列表
            });


            //获取当前选中数据源来源
            _G.groupID = function(){
                var options=$("#data-source-option option:selected"); //获取选中的项
                var group_id = options.val();//不同类型数据源
                return group_id;
            };


            //展开数据源列表、新建页面
            _SOURCE.showlayerSource = function(){
                $('#linked-content').fadeOut(100);
                $('#layer-source-box').fadeIn(1000);

                _SOURCE.sourceList(); //数据源列表

                if( _G.groupID() == 2 ){ //Excel
                    $('#source-list-box #layer-source-excel').show().siblings().hide();//显示excel
                }else{     //SQL
                    $('#source-list-box #layer-source-sql').show().siblings().hide();  //显示sql
                }
            };
            //隐藏数据源列表、新建页面
            _SOURCE.hidelayerSource = function(){
                $('#layer-source-box').fadeOut(500);
                $('#linked-content').fadeIn(1000);
                $('.source-layui-table-header').nextAll().empty();
            };
            //获取数据源列表
            _SOURCE.sourceList = function(){
                var group_id = _G.groupID();//获取数据集来源ID
                $('.source-layui-table-header').nextAll().empty();//清空数据源列表
                $.ajax({
                    type: 'get',
                    data:{
                        group_id: group_id
                    },
                    url: '/webi/views/source/list',
                    success: function (res) {
                        if (res.code != 200) {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                        } else {
                            var source_tr_html = _SOURCE.creatTr(res.data['source']);
                             $('.source-table').append(source_tr_html);
                        }
                    }
                });

            };
            //生成数据源列表tr
            _SOURCE.creatTr = function(data){
                var tr_html="";
                if( _G.groupID() == 2){  //Excel
                    $.each(data, function (k, v) {

                        tr_html += $('#source-tr-html #source-table-html').html();

                        tr_html = tr_html.replace(/[\$]source_id/g,v._id);
                        tr_html = tr_html.replace(/[\$]group_id/g,v.group_id);
                        tr_html = tr_html.replace(/[\$]source_name/g,v.source_name);
                        tr_html = tr_html.replace(/[\$]file_size/g,v.file_size);
                        tr_html = tr_html.replace(/[\$]line_num/g,v.line_num);
                        tr_html = tr_html.replace(/[\$]updated_at/g,v.updated_at);

                    });
                }else{     //SQL

                    $.each(data, function (k, v) {

                        tr_html += $('#source-sql-html #source-table-html').html();

                        tr_html = tr_html.replace(/[\$]source_id/g,v._id);
                        tr_html = tr_html.replace(/[\$]group_id/g,v.group_id);
                        tr_html = tr_html.replace(/[\$]source_name/g,v.source_name);
                        tr_html = tr_html.replace(/[\$]db_host/g,v.db_host);
                        tr_html = tr_html.replace(/[\$]db_database/g,v.db_database);
                        tr_html = tr_html.replace(/[\$]db_user/g,v.db_user);
                        tr_html = tr_html.replace(/[\$]db_port/g,v.db_port);
                    });
                }

                return tr_html;
            };
            //下载数据源excel
            _SOURCE.download = function(source_id){
                $.ajax({
                    type:'get',
                    data:{
                        source_id: source_id
                    },
                    url: '/webi/views/download/excel',
                    // 返回值判断
                    success: function (obj) {
                        if ( obj.code == 200 ) {
                            window.location= obj.data.file_path;
                        } else {
                            layer.msg(obj.message,{icon:2,time:1500});
                        }
                    }
                });
            };
            //查看数据源下所有表
            _SOURCE.tableList = function(source_id,group_id,url_suffix){
                layer.open({
                    title:'数据表',
                    type: 1,
                    content: $('#layer-source-table'),
                    area:['700px','500px'],
                    success:function(){
                        _SOURCE.refreashTable(source_id,group_id,url_suffix);//刷新页面
                    }
                });
            };
            //刷新页面
            _SOURCE.refreashTable = function(source_id,group_id,url_suffix){
                var indexPage = layer.load();
                $('.layer-source-table-box').empty();//置空页面数据

                var tr_thml = '<tr>';
                tr_thml += '<td class="table-list-name">名称</td>';
                tr_thml += '<td>备注</td>';
                tr_thml += '<td>操作</td>';
                tr_thml += ' </tr>';
                $('.layer-source-table-box').append(tr_thml);

                var page  = url_suffix || 1;
                $.ajax({
                    type:'get',
                    url: '/webi/design/views/table/search',
                    data: {
                        group_id: group_id,
                        source_id: source_id,
                        page: page,
                        limit: 10
                    },
                    success: function (res) {
                        layer.close(indexPage);

                        if( res.code==200 ){
                            if(!($.isEmptyObject(res.data))){

                                var source_tr_html = _SOURCE.tableCombine(res.data.data);//组装数据表显示列表
                                $('.layer-source-table-box').append(source_tr_html);

                                $.each($('.layer-source-table-box .operation'), function (k, v) {
                                    $(this).on('click',function(){
                                        _G.tableStructure(
                                            $(this).attr('data-name'),
                                            $(this).attr('data-remarks'),
                                            source_id
                                        );
                                    });
                                });

                                if(typeof  url_suffix == 'undefined'){//首次展开弹层
                                    _SOURCE.toPage(source_id,group_id,res.data.count);//分页
                                }
                            }
                        }
                    }
                });
            };
            //表列表分页
            _SOURCE.toPage = function(source_id,group_id,count){
                var limit = 10;
                layui.use('laypage', function(){
                    var laypage = layui.laypage;
                    laypage.render({
                        elem: 'layer-page',
                        count: count,
                        curr: 1,
                        limit: limit,
                        theme:"#0099ff",
                        jump:function(obj, first) {//点击页数按钮触发的函数
                            page = obj.curr;//得到点击的页数
                            $("#currPage").val(page);
                            if(!first){ //一定要加此判断，否则初始时会无限刷新 
                                _SOURCE.refreashTable(source_id,group_id, page);
                            }
                        }
                    });
                });
            };
            //组装--查看数据源下所有表
            _SOURCE.tableCombine = function(data) {
                var tr_html = '';
                $.each(data, function (k, v) {

                    tr_html += $('#layer-source-table-tr #layer-source-tr-html').html();

                    tr_html = tr_html.replace(/[\$]source_id/g,v.source_id);
                    tr_html = tr_html.replace(/[\$]table_name/g,v.table_name);
                    tr_html = tr_html.replace(/[\$]table_remarks/g,v.description);

                });
                return tr_html;
            };
            //组装--查看表字段信息
            _SOURCE.tableField = function(data) {
                var tr_demo_html = '<tr>';
                    tr_demo_html += '<td class="table-list-name">$column_name</td>';
                    tr_demo_html +='<td class="source-name-$source_id">$data_type</td>';
                    tr_demo_html +='<td class="source-name-$source_id">$column_comment</td>';
                    tr_demo_html +='</tr>';

                var tr_html = '';
                var column_name = "";
                var column_comment = "";

                $.each(data.field, function (k, v) {
                    if(_G.groupID() == 2){
                        column_name = v.field_name;
                        column_comment = v.field_remark;

                    }else if(_G.groupID() == 3){
                        column_name = v.column_name;
                        column_comment = v.column_comment;
                    }

                    tr_html += tr_demo_html;

                    tr_html = tr_html.replace(/[\$]column_name/g,column_name);
                    tr_html = tr_html.replace(/[\$]data_type/g,v.data_type);
                    tr_html = tr_html.replace(/[\$]column_comment/g,column_comment);

                });
                return tr_html;

            };
            //选择数据源,展示表
            _SOURCE.selectSource = function(source_id){
                _G.source_id = source_id;//数据源ID,用于新建数据集参数
                $('.source-name-remind').html();//先清空
                $('.source-name-remind').attr('data-source',0);//数据源id
                $('.source-name-remind').html($('.source-name-'+source_id).eq(0).text());//选择的数据源提醒
                $('.source-name-remind').attr('data-source',source_id);//数据源id

                _SOURCE.hidelayerSource();//关闭数据源列表页面
                _GROUP.list(source_id);//
                //_G.showTable(source_id);//显示数据表
            };
            //显示数据表数据
            _SOURCE.pop.tableList = function(source_id,url_suffix){
                var url  = url_suffix || '&page=1&limit=10';
                $('.layer-table').empty();//清空页面表格数据
                if(typeof  url_suffix == 'undefined'){//首次展开弹层
                    $('#myModalLabel').empty();//重置表名
                }

                $.ajax({
                    type: 'get',
                    url: '/webi/views/source/table/list?source_id='+source_id + url,
                    success: function (obj) {
                        if (obj.code != 200) {
                            layer.msg(obj.msg, {icon: 2, offset: '70px',time:1500});
                        } else {
                            var res = [];
                            res.name = obj.data['table_name'];
                            res.source_id = source_id;
                            res.table_id = obj.data['table_id'];

                            var count = obj.data['count'];//总条数
                            var tr_html = _SOURCE.pop.creatTable(obj.data['table'],res);//生成表tr

                            //表格
                            $('.layer-table').append( tr_html.html);
                            if(typeof  url_suffix == 'undefined'){//首次展开弹层
                                //标题
                                $('#myModalLabel').append( tr_html.name_html);
                                _SOURCE.pop.toPage(count,source_id);//分页
                            }
                        }


                    }
                });
            };
            //生成表格
            _SOURCE.pop.creatTable = function(data,table){
                var  html = "";
                var tr_html = "";
                var th_html = "";
                var td_html = "";
                var th = "";
                var td = "";
                var table_th_html = '<th><div><input type="text" value="$source_remarks" class="fieldRemarks remarks-$field" data-id="$field" data-table="$table_id"></div></th>';
                var table_td_html = '<td>$parm</td>';

                $.each(data[0],function(dk,dv){//标题

                    th_html = table_th_html;

                    th_html = th_html.replace(/[\$]field/g, dk);
                    th_html = th_html.replace(/[\$]table_id/g, table['table_id']);
                    th += th_html.replace(/[\$]source_remarks/g, dv);

                });

                $.each(data[1], function (k, v) {
                    $.each(v, function (field, parm) {
                        td_html = table_td_html;

                        td_html = td_html.replace(/[\$]parm/g,parm);
                        td += td_html;
                    });
                    if( td != "" ){
                        tr_html += '<tr>'+ td + '</tr>';
                        td = '';
                    }
                });

                html = '<tr class="table-header">'+ th + '<tr>' + tr_html;

                var name_html = '<input type="text" value="'
                    + table['name'] +'" data-title="'+  table['name']
                    +'" class="pop-title" name="pop-title" data-table="'+ table['table_id'] +'" data-id="'+ table['source_id']+'" >';

                var data_array = {
                    'html':html,
                    'name_html':name_html
                };
                return data_array;
            };
            //表格分页
            _SOURCE.pop.toPage = function(count,source_id){
                var limit = 10;
                layui.use('laypage', function(){

                    var laypage = layui.laypage;
                    laypage.render({
                        elem: 'page',
                        count: count,
                        curr: 1,
                        limit: limit,
                        theme:"#0099ff",
                        jump:function(obj, first) {//点击页数按钮触发的函数
                            page = obj.curr;//得到点击的页数
                            $("#currPage").val(page);
                            if(!first){ //一定要加此判断，否则初始时会无限刷新
                                _SOURCE.pop.tableList(source_id,'&page='+ page+ '&limit='+ limit);
                            }
                        }
                    });
                });
            };
            //弹窗表格修改表名
            _SOURCE.pop.save_name = function (source_id) {

                if( $('.pop-title').val() == ""){
                    layer.msg('数据源名称不能为空',{icon:2,time:1500});
                    return false;
                }
                var table_name = $('.pop-title').val();
                $.ajax({
                    type:'post',
                    url: '/webi/views/source/table/save',
                    data: {
                        edit_field:1,
                        table_name: table_name,
                        table_id: $('.pop-title').attr('data-table')
                    },
                    dataType:'json',
                    // 返回值判断
                    success: function (obj) {
                        layer.closeAll();
                        if ( obj.code == 200 ) {
                            $('.pop-title').val( table_name );
                            $('.pop-title').attr('data-title',table_name);
                        } else {
                            layer.msg(obj.message,{icon:2,time:1500});
                        }
                    }
                });
            };
            //修改字段备注
            _SOURCE.pop.remarks = function (data) {

                $.ajax({
                    type:'post',
                    url: '/webi/views/source/table/save',
                    data: data,
                    dataType:'json',
                    // 返回值判断
                    success: function (obj) {
                        if ( obj.code == 200 ) {
                            $('.remarks-'+ data.field_name).val(data.field_remark);
                        } else {
                            layer.msg(obj.message,{icon:2,time:1500});
                        }
                    }
                });
            };
            //编辑数据源名称
            _SOURCE.edit = function(source_id){ 
                var html = '<form id="pop_form" onsubmit="return false;" class="form-horizontal" role="form" style="margin-top: 15px">';
                html+='<div class="form-group">';
                html+='<label class="col-sm-3 control-label lable-name-option"><span class="red mr5">*&nbsp;</span>数据源名称：</label>';
                html+='<div class="col-sm-8">';
                html+='<input type="text" class="form-control"  id="table-source-name" name="table_source_name"  placeholder="请输入数据源名称" value="">';
                html+='</div>';
                html+='</div>';
                html+='</form>';

                layer.open({
                    title: '编辑数据源名称',
                    type: 1,
                    offset:'100px',
                    area:['500px','180px'],
                    scrollbar: false,
                    content: html,
                    btn: ['确定', '取消'],
                    success:function(){
                        $('#table-source-name').val($('.source-name-'+ source_id).html());
                    },
                    yes:function(){
                        _SOURCE.save_name(source_id);
                    }
                });
            };
            //保存数据源名称
            _SOURCE.save_name = function (source_id) {
                var data = E.getFormValues('pop_form');
                var message ='';
                if (E.isEmpty(data.table_source_name) ) {
                    message += '请输入数据源名称</br>';
                }
                if(message){  //存在错误，返回不进行提交
                    layer.msg(message,{icon:2,time:1500});
                    return false;
                }

                layer.confirm('您确认要修改数据源名称吗？',{icon:3},function(){

                    E.ajax({
                        type:'post',
                        url: '/webi/views/source/save',
                        data: {
                            source_name: data.table_source_name,
                            source_id: source_id
                        },
                        dataType:'json',
                        // 返回值判断
                        success: function (obj) {
                            layer.closeAll();
                            if ( obj.code == 200 ) {
                                layer.msg( obj.message, {icon:1,time:1500});
                               $('.source-name-'+ source_id).text(data.table_source_name);//修改成功后修改 页面名称
                            } else {
                                layer.msg(obj.message,{icon:2,time:1500});
                            }
                        }
                    });
                });
            };
            //删除数据源
            _SOURCE.delete = function(source_id){
                layer.confirm('您确认要删除数据源吗？',{icon:3},function(index){
                    layer.closeAll();
                    $.ajax({
                        type:'get',
                        data:{
                            source_id: source_id
                        },
                        url: '/webi/views/source/del',
                        // 返回值判断
                        success: function (obj) {
                            if ( obj.code == 200 ) {
                               $('.table-tr-'+ source_id).parent().remove();//移除数据源行
                            } else {
                                layer.msg(obj.message,{icon:2,time:1500});
                            }
                        }
                    });
                });
            };
            //SQL数据源弹窗数据展示
            _SOURCE.update = function(source_id){
                var group_id = _G.groupID();//获取当前数据源分组ID

                layer.open({
                    title: '更新数据源',
                    type: 1,
                    offset:'100px',
                    area:['500px','auto'],
                    scrollbar: false,
                    content: group_id == 2 ? $('#layer-excel-source-update') : $('#layer-sql-source-update'),
                    btn: ['确定', '取消'],
                    success:function(){

                       if( group_id == 3 ){ //为弹窗input赋值
                           document.getElementById("db_host").value = $('#updata-'+source_id).attr('data-host');
                           document.getElementById("db_database").value = $('#updata-'+source_id).attr('data-database');
                           document.getElementById("db_user").value = $('#updata-'+source_id).attr('data-user');
                           document.getElementById("db_pwd").value = '';
                           document.getElementById("db_port").value = $('#updata-'+source_id).attr('data-port');
                       }
                    },
                    yes:function(){
                        if( group_id == 3 ){
                            _SOURCE.commitSqlSource(source_id,5);
                        }else{
                            updatePloader.options.formData = {
                                source_id:source_id
                            };
                            updatePloader.upload();
                        }
                    },
                    btn2:function(){
                        if(group_id == 2){
                            $('#layer-upload-rate').find('.item').remove();
                        }
                    }
                });
            };
            //提交新建Excel数据源
            _SOURCE.commitExcelSource = function(source_id){
                var sourceId = source_id || 0;

                var url = '/webi/views/import';

                if($('.table-name').html() == ''){
                    layer.msg('请上传数据源文件', {icon: 2, offset: '70px',time:1500});
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url: url,
                    enctype: 'multipart/form-data',
                    data: {
                        source_id: sourceId,
                        source_name:$('#upload-rate p').text(),
                        action: 'webi'
                    },
                    success: function (res) {
                        if (res.code == 200) {
                            $('#upload-rate').find('.progress').remove();//移除进度条
                            $('#source-name').val('');
                            $('#table-name p').empty();
                            _SOURCE.sourceList();//更新列表
                        } else {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                        }

                        $('#upload-rate').empty();
                    }
                });
            };
            //提交SQL数据源
            _SOURCE.commitSqlSource = function(source_id,type){
                var operation_type =  type || 0;
                var msg = '';

                if( type == 3 || type == 5){
                    var dt = E.getFormValues('source-content-sql');
                }else{
                    var dt = E.getFormValues('source-sql');
                }

                if( dt.db_host == ''){
                    msg += '请输入数据库主机地址<br/>';
                }
                if(dt.db_database == ''){
                    msg += '请输入数据库名称<br/>';
                }
                if(dt.db_user == ''){
                    msg += '请输入用户名<br/>';
                }
                if(dt.db_pwd == ''){
                    msg += '请输入密码<br/>';
                }
                if(dt.db_port == ''){
                    msg += '请输入数据库端口号<br/>';
                }
                if( msg != ''){
                    layer.msg(msg, {icon: 2, offset: '70px',time:2000});
                    return false;
                }

                $.ajax({
                    type: 'post',
                    url: '/webi/views/source/sql',
                    data: {
                        db_host:dt.db_host,
                        db_port:dt.db_port,
                        db_database:dt.db_database,
                        db_user:dt.db_user,
                        db_pwd:dt.db_pwd,
                        source_id:source_id,
                        operation_type:operation_type
                    },
                    success: function (res) {
                        if (res.code == 200) {
                            layer.msg('操作成功！', {icon: 1, offset: '70px',time:1500});
                            if( operation_type > 3){ // 非测试  关闭弹窗
                                $('#source-content-sql input').val('');
                                $('#source-sql input').val('');
                                _SOURCE.sourceList();//更新列表
                                layer.closeAll();
                            }

                        } else {
                            layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                        }
                    }
                });
            };


            //获取指定来源数据集
            _GROUP.list = function(source_id){
                var source_id = source_id || 0;
               var group_id = _G.groupID();//获取数据集来源ID

                $('#group-nav').empty();//清空分组列表
                if( group_id != 1 &&source_id == 0){//Excel、SQL  未选择数据源
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '/webi/design/views/group/list/'+ group_id +'/'+source_id,
                    success: function (res) {
                        if (res.code != 200) {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                        } else {

                            if( res.data['view'] != '' ){
                                var group_html = _GROUP.creat(res.data['view']);//生成数据集li
                                $('#group-nav').append(group_html);

                                //绑定视图查询事件
                                $.each($('.group-each'), function (k, v) {
                                    $(this).on('click', function () {
                                        _G.search( $(this).attr('id').substring(2) );
                                    })
                                });
                            }
                        }
                    }
                });
            };
            //生成数据集li
            _GROUP.creat = function(data) {

                var groupList_html="";
                $.each(data, function (k, v) {

                    groupList_html += $('#group-li-html').html();

                    groupList_html = groupList_html.replace(/[\$]view_name/g,v.view_name);
                    groupList_html = groupList_html.replace(/[\$]view_id/g,v._id);

                });
                return groupList_html;

            };
            //添加分组
            _GROUP.add = function() {
                if(  _G.groupID() != 1 && _G.source_id == 1){
                    layer.msg( '请选择数据源后添加数据集！' , { icon: 2,offset:'70px;',time:1500} ) ;
                    return false;
                }

                layer.open({
                    title: '添加数据集',
                    offset: '70px',
                    type: 1,
                    area: ['540px', '170px'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: layer_add_group,
                    btn: ['确认', '取消'],
                    yes: function () {
                            var dt = E.getFormValues('title-form');
                            dt.view_id = 0;

                            if(dt.title ==""){
                                layer.msg( '请输入数据集名称！' , { icon: 2,offset:'70px;',time:1500} ) ;
                                return false;
                            }

                            dt.source_id = _G.source_id;//数据源ID
                            dt.group_id = _G.groupID();//获取数据集来源ID
                            E.ajax({
                                type: 'post',
                                url: '/webi/design/views/group/edit',
                                data: dt,
                                success: function (res) {
                                    if (res.code == 200) {

                                        $("#title").val("");

                                        //追加数据集分组
                                        var nav_html=$("#group-nav-each").html();
                                        nav_html=nav_html.replace(/g_view_id/g,res.data.id);
                                        nav_html=nav_html.replace(/g_group_title/g,dt.title);
                                        $("#group-nav").append(nav_html);

                                        //展示新建分组下关联表
                                        $("#g_" + res.data.id).on('click',function(){
                                            _G.search(res.data.id);
                                        });

                                        layer.msg('保存成功', {icon: 1, offset: '70px', time: 1500});

                                        $("#g_"+ res.data.id).trigger("click");//触发选中新建的分组

                                    } else {
                                        layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                                    }
                                }
                            });
                        }
                    });

            };
            //修改分组
            _GROUP.edit = function(view_id) {

                var groupTitle=$("#group_"+view_id).text();
                layer.open({
                    title: '编辑数据集',
                    offset: '70px',
                    type: 1,
                    area: ['500px', '180px'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: layer_add_group,
                    btn: ['确认','取消'],
                    success: function() {
                        $("#title").val(groupTitle);
                        $("#title").focus();
                    },
                    yes: function () {
                        var dt = E.getFormValues('title-form');
                        dt.view_id = view_id;

                        if(dt.title==""){
                            layer.msg("请输入数据集名!", {icon: 2, offset: '70px',time:1500});
                            return false;
                        }
                        dt.group_id = _G.groupID();//数据源ID

                        E.ajax({
                            type: 'post',
                            url: '/webi/design/views/group/edit',
                            data:dt,
                            success: function (res) {
                                if (res.code == 200) {
                                    layer.closeAll();

                                    $("#group_"+view_id).html(dt.title);
                                    layer.msg('编辑成功', {icon: 1, offset: '70px', time: 1500});

                                } else {
                                    layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                                }
                            }
                        });
                    }
                });
            };
            //删除分组
            _GROUP.del = function(view_id) {
                layer.confirm('删除数据集后该数据集下链表将全部删除，您确认要删除吗？',{  icon: 3 ,offset: '100px'}, function (index) {
                    layer.close(index);

                    $.ajax({
                        type: 'get',
                        url: '/webi/design/views/group/del/' + view_id,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.msg("删除成功", {icon: 1, offset: '70px',time:1500});

                                if($("#view_"+view_id).hasClass("cur-list")){
                                    $('#right-content').empty();
                                }
                                $("#view_"+view_id).remove();
                            }else{
                                layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                            }
                        }
                    })
                })

            };


            //查询页面数据
            _G.search = function(view_id){
                //切换选中效果
                $("#g_"+view_id).eq($(this).index()).parent().addClass("cur-list").siblings().removeClass("cur-list");

                //清空右侧页面数据
                $("#right-content").empty( );
                _G.globalJosn = {};//清空json_table数据

                $.ajax({
                    type: 'get',
                    data:{
                        view_id: view_id,
                        group_id: _G.groupID()
                    },
                    url: '/webi/design/views/table/linked/list',
                    success: function (res) {
                        if (res.code == 200) {

                            $("#groupId").val(view_id);//重新设置当前分组id

                            //填充展示数据
                            if(!($.isEmptyObject(res.data['table']))){
                                _G.paste(res.data['table']);
                            }
                        }
                    }
                });

            };
            //贴上页面数据
            _G.paste = function(data){
                _G.globalJosn=data;//存储当前选中分组的链表信息

                var i=0;

                var join_class = {
                    1:'left-img imgbox',
                    2:'inner-img imgbox',
                    3:'right-img imgbox'
                };

                //关联字段的循环li
                var temp_link_html = $('#link_table_div').html();

                $.each(data, function (k, v) {
                    ++i;
                    if( i==1 ){

                        var primar_html=$('#link_primarTabe').html();
                        primar_html=primar_html.replace(/primarName/g,v.table_name);
                        primar_html=primar_html.replace(/parentId/g,k);

                        //插入主表html
                        $('#right-content').append(primar_html);

                        //绑定点击删除事件
                        $(".right-cont-fl" ).on('click','.glyphicon',function(){
                            _G.del(k);//删除uuid数据
                        });

                    }

                    var  link_li_html = "";  //关联子表HTML

                    $.each(v.join, function (uid, join_table) {

                        var  temp_li_html = temp_link_html;
                        temp_li_html = temp_li_html.replace(/join_class/g,join_class[join_table[1]]);
                        temp_li_html = temp_li_html.replace(/uuId/g,uid);
                        temp_li_html = temp_li_html.replace(/sublistNmae/g,data[uid]['table_name']);
                        temp_li_html = temp_li_html.replace(/parentId/g,k);

                        link_li_html += temp_li_html;

                    });

                    //页面只有一个主表，去除尾部的线条
                    if( i==1 && link_li_html== "" ){
                        $('.right-cont-w .fr').remove();
                    }

                    if(link_li_html!=""){
                        //关联的子元素div绑定视图查询事件
                        var p_left = document.getElementById(v.table_name).parentElement.offsetLeft;

                        var x_soller = !p_left ? 200 : p_left + 410;
                        var y_soller = document.getElementById(v.table_name).parentElement.parentElement.offsetTop + 71;

                        var sub_div = $('#append-div-ul').html().replace(/right-div/g,"right-cont-fr").replace(/li_list/g,link_li_html);
                        sub_div = sub_div.replace(/lf_val/g,( x_soller));
                        sub_div = sub_div.replace(/t_val/g,( y_soller));

                        $('#right-content').append( sub_div );
                    }


                });

                setTimeout(function(){
                    //绑定点击事件-编辑、删除
                    $.each($('.linked-content  li'), function (k, v) {

                        //编辑
                        var id =$(this).attr('id').substring(3);
                        $(".li_"+ id +" .right-cont-fr-img").on('click','span',function(){
                            _POP.edit_show(id);
                        });

                        //删除
                        $(".li_"+ id +" .right-cont-fr-text").on('click','.glyphicon',function(){
                            _G.del(id);
                        });

                    });
                },500);

            };
            //删除链表
            _G.del = function(uuid){
                layer.confirm('将要清空所有关联，您确认要删除吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);

                    var viewId =$("#groupId").val();

                    var parent_id=$("#li_"+uuid).data('parent');//父级id
                    edit_link_contents=G_handle.del_node(uuid,parent_id);//移除数组中要删除的

                    $.ajax({
                        type:'post',
                        url: '/webi/design/views/group/link/del',
                        data:{
                            view_id:viewId,
                            table_json:edit_link_contents//转成json字符串直接保存
                        },
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.code == 200) {
                                //清空右侧页面数据
                                $("#right-content").empty( );

                                layer.msg("删除成功", {icon: 1, offset: '70px',time:1500});

                                //页面重新HTML
                                setTimeout(_G.paste(res.data['table']),1500);

                                if($.isEmptyObject(edit_link_contents)){
                                    _G.globalJosn = {};  //存储当前选中分组的链表信息
                                }
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                            }
                        }
                    })
                })
            };
            //生成数据源列表
            _G.showTableList = function(data){

                var tableList_html="";
                $.each(data, function (k, v) {

                    tableList_html+=$('#tableList').html();

                    tableList_html = tableList_html.replace(/Description/g,v.description);
                    tableList_html = tableList_html.replace(/TableId/g,v._id);
                    tableList_html = tableList_html.replace(/TableName/g,v.table_name);

                });
                return tableList_html;

            };
            //隐藏数据集
            _G.hideTable = function(){

                <!--隐藏数据集-->
                $('.linked-list').toggle(1800, '', false);

                $('#left-side-hide').toggle(1800);
                $('#left-side-show').toggle(1800);

//                $("#left-side-show").toggle(1000,function(){
//                        $('#left-side-show').removeClass('layui-icon-spread-left').addClass('layui-icon-shrink-right');
//                        $('#left-side-show').toggle(800, '', true);
//                },false);

                setTimeout(function(){
                    $("#right-content").removeClass("right-cont");
                },1300);//移除链表列表

                //移除点击事件
                $(".ul-list li").prop("onclick",null).off("click");//jQuery1.7+
                $(".ul-list li").attr('onclick','').unbind('click');//jQuery-1.7

              //  $('#left-side-show').attr('onclick','_G.showTable('+ $('.source-name-remind').data('source') +')');

            };
            //显示数据集
            _G.showTable = function(source_id){
                var sourceID =$('.source-name-remind').data('source');
               
                source_id = sourceID || source_id;
          
                _G.tableList(source_id);

                $('.linked-list').toggle(1800, '', true);

                $('#left-side-hide').toggle(1800);
                $('#left-side-show').toggle(1800);

                $("#right-content").addClass("right-cont");

                //$('#left-side-show').attr('onclick','_G.hideTable()');

            };
            //数据源下数据集
            _G.tableList = function(source_id){
                var source_id = source_id; //数据源id
                var group_id = _G.groupID();//获取数据集来源ID

                if( group_id != 1 && source_id == 0){
                    return false;
                }//未选择数据源，停止查询

                $('.ul-list').empty();

                var page = layer.load();
                $.ajax({
                    type: 'GET',
                    data:{
                        group_id: group_id,
                        source_id: source_id
                    },
                    url: '/webi/design/views/table/search',
                    success: function (res) {
                        layer.close(page);
                        if( res.code==200 ){

                            if(!($.isEmptyObject(res.data))){

                                //生成数据源列表
                                var html=  _G.showTableList(res.data.data);
                                $('.ul-list').append(html);

                                //绑定点击事件
                                $.each($('.ul-list li .source-table-choose'), function (k, v) {

                                    //选择数据集
                                    $(this).on('click',function(){
                                        _G.chooseTableList($(this).attr('data-name'));
                                    });

                                });
                                $.each($('.ul-list li .source-table-detail'), function (k, v) {

                                    //查看数据表结构
                                    $(this).on('click',function(){
                                        _G.tableStructure(
                                            $(this).attr('data-name'),
                                            $(this).attr('data-remarks'),
                                            source_id
                                        );
                                    });

                                });

                            }
                        }
                    }
                });
            };
            //搜索数据集
            _G.searchTableList = function(){
                var inputValue = document.getElementById("search").value;

                if(inputValue == ""){
                    layer.msg("搜索内容不能为空", {icon: 2, offset: '70px',time:1500});
                    return false;
                }
                var group_id = _G.groupID();//获取数据集来源ID

                $.ajax({
                    type: 'GET',
                    data:{
                        source_id: 0,
                        table_name: inputValue,
                        group_id: group_id
                    },
                    url: '/webi/design/views/table/search',
                    dataType: 'JSON',
                    success: function (res) {
                        $("#search").val("");
                        if( res.code==200 ){
                            if($.isEmptyObject(res.data)){

                                layer.msg("暂无匹配信息", {icon: 1, offset: '70px',time:1500});
                                return false;

                            }else{
                                //清空页面
                                $(".ul-list").empty();

                                //展示数据集
                                var html=  _G.showTableList(res.data.data);
                                $('.ul-list').append(html);

                                //绑定点击事件
                                $.each($('.ul-list li .source-table-choose'), function (k, v) {

                                    //选择数据集
                                    $(this).on('click',function(){
                                        _G.chooseTableList($(this).attr('data-name'));
                                    });

                                });
                                $.each($('.ul-list li .source-table-detail'), function (k, v) {

                                    //查看数据表结构
                                    $(this).on('click',function(){
                                        _G.tableStructure(
                                            $(this).attr('data-name'),
                                            $(this).attr('data-remarks'),
                                            $('.source-name-remind').attr('data-source')
                                        );
                                    });

                                });

                            }
                        }else{
                            layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }

                    }
                });
            };
            //选择数据集
            _G.chooseTableList = function(name){

                var table_name = name; //使用表名查询
                var view_id = $('.cur-list').attr('data-id');

                if( typeof view_id == 'undefined' || view_id == ''){
                    layer.msg("请先选择操作的数据集~", {icon: 2, offset: '70px',time:2000});
                    return false;
                }

                var index  = layer.load() ;
                $.ajax({
                    type: 'get',
                    data:{
                        view_id: view_id,
                        group_id: _G.groupID(),
                        table_name: table_name,
                        source_id: _G.source_id
                    },
                    url: '/webi/design/views/make/table/structure',
                    success: function (res) {

                        layer.close(index);

                        if (res.code != 200) {//页面存在选择的关联表
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }

                        if ( $.isEmptyObject( res.data['tableL']['table']) ){  //分组下为空，在页面添加主表
                            _G.addTable(view_id,res.data['tableR']['table'][0], res.data['tableR']['table'][1]);
                        } else {   //该分组下存在关联表

                            //存左右表 字段
                            _POP.l_options = res.data['tableL']['fields'];
                            _POP.r_options = res.data['tableR']['fields'];

                            layer_linked_edit = layer_linked_edit.replace(/tableName-select/g, res.data['tableR']['table'][0]);
                            layer_linked_edit = layer_linked_edit.replace(/selectTable/g, res.data['tableR']['table'][1]);

                           //追加关联表
                            _POP.append_table(view_id,res.data);
                        }

                    }
                });
            };
            //查看数据表字段信息
            _G.tableStructure = function(name,remarks,source_id) {
                layer.open({
                    title: '表详情',
                    type: 1,
                    content: $('#layer-source-field'),
                    area: ['700px', '500px'],
                    success: function () {

                        var indexPage = layer.load();

                        $('.layer-source-field-box').empty();//置空页面数据

                        var tr_thml = '<tr>';
                        tr_thml += '<td class="table-list-name">字段名称</td>';
                        tr_thml += '<td>字段类型</td>';
                        tr_thml += '<td>字段描述</td>';
                        tr_thml += ' </tr>';
                        $('.layer-source-field-box').append(tr_thml);

                        $.ajax({
                            type:'get',
                            url: '/webi/views/table/field/list',
                            data: {
                                table_name: name,
                                source_id: source_id
                            },
                            success: function (res) {
                                layer.close(indexPage);

                                if( res.code==200 ){
                                    if(!($.isEmptyObject(res.data))){
                                        var source_tr_html = _SOURCE.tableField(res.data);//组装数据表字段信息列表
                                        $('.layer-source-field-comment .field-table-name').html(name);
                                        $('.layer-source-field-comment .field-table-remarks').html(remarks);
                                        $('.layer-source-field-box').append(source_tr_html);
                                    }
                                }
                            }
                        });
                    }
                });
            };
            //页面追加一个table
            _G.addTable= function(view_id,table,table_name){

                //递归修改table_json字符串 左表名，连接类型，右表名，右表描述，链接字段,uuid
                G_handle.addJson( "", "", table, table_name, "","" );

                $.ajax({
                    type: 'post',
                    url: '/webi/design/views/group/link/add',
                    data: {
                        table_json : _G.globalJosn,
                        view_id : view_id
                    },
                    success: function (res) {
                        if (res.code == 200) {

                            //清空右侧页面数据
                            $("#right-content").empty();

                            //刷新页面
                            setTimeout(function(){
                                _G.paste(res.data['table']);
                            },2000);


                        } else {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }
                    }
                })
            };

            //在已有表下进行关联表
            _POP.append_table = function(view_id,data){
                layer.open({
                    title: '添加表关联信息',
                    offset: '70px',
                    type: 1,
                    area: ['540px', '270px'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: layer_linked_edit,
                    btn: ['确认', '取消'],
                    success:function(){
                        _POP.add_show(data['tableL'], data['tableR']);//对弹窗进行赋值
                        $('#li_select').addClass('clickli');//触发默认表关联类型
                    },
                    yes: function () {

                        var checkTableL = $("option:selected", $('#table_l')).val();
                        var checkTableR = data['tableR']['table'][0].toString();
                        var description= data['tableR']['table'][1].toString();
                        var innerType = $(".bomb-box .clickli").attr("data-code");

                        var leftField = "";
                        var rightField = "";
                        var field = [];
                        var error_msg = '';

                        $.each($('#table-field li'), function () {
                            leftField = $(this).find(".fields_l option:selected").val();
                            rightField = $(this).find(".fields_r option:selected").val();

                            if($.inArray(leftField + ":" + rightField , field) != "-1"){
                                $(this).addClass("li-lable");//重复提示框
                                error_msg = "关联字段已重复";
                                return false;
                            }
                            field.push(leftField + ":" + rightField);
                        });

                        if(error_msg != ""){
                            layer.msg(error_msg, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }

                        //递归添加table_json字符串   左表名，连接类型，右表名，右表描述，链接字段,uuid
                        G_handle.addJson(checkTableL,innerType,checkTableR,description,field,"");

                        _POP.submit( view_id, _G.globalJosn,1);//保存

                        //初始化左右侧表字段
                        _POP.l_options = [];
                        _POP.r_options = [];
                    }
                });
            };
            //展示字段设置弹层
            _POP.add_show = function(l_data,r_data){
                var table_l = "";
                var option_l = "";
                var option_r = "";
                var fieldL = [];
                var fieldR = [];

                //左表--表名
                $.each(l_data['table'], function (kt, vt) {
                    if( l_data['selected'] && kt== l_data['selected']){
                        table_l+='<option name="table_name_l" value="'+kt+'"  selected>'+vt+'</option>';
                    }else{
                        table_l+='<option name="table_name_l" value="'+kt+'">'+vt+'</option>';
                    }
                });

                if(r_data['innerType']){

                    $.each(l_data['fields'], function (k, v) {
                        fieldL.push(v['field_remark']);
                    })
                    $.each(r_data['fields'], function (kk, vk) {
                        fieldR.push(vk['field_remark']);
                    })

                    for (var i=0;i<l_data['fields'].length;i++) {

                        //左表-字段
                        $.each(l_data['allFields'], function (kl, vl) {
                            if( vl['field_name'] == l_data['fields'][i] ){
                                option_l = '<option name="fieldOne" class="left-table" value="' + vl['field_name'] + '" selected>' + vl['field_remark'] + '</option>';
                            }else{
                                option_l = '<option name="fieldOne" class="left-table" value="' + vl['field_name'] + '" >' + vl['field_remark'] + '</option>';
                            }
                            $('#table-field li:last-child .fields_l').append(option_l);
                        });

                        //右表--字段
                        $.each(r_data['allFields'], function (kf, vf) {
                            if( vf['field_name'] == r_data['fields'][i] ){
                                option_r = '<option name="fieldTwo" value="' + vf['field_name'] + '" selected>' +  vf['field_remark'] + '</option>';
                            }else{
                                option_r = '<option name="fieldTwo" value="' +vf['field_name'] + '">' +  vf['field_remark'] + '</option>';
                            }
                            $('#table-field li:last-child .fields_r').append(option_r);
                        });

                        //多个关联字段
                        if(i+1<l_data['fields'].length){
                            var li = $('#link-layer-hidden').html();
                            li = li.replace(/fieldsLs/g, "fields_r");
                            li = li.replace(/fieldsRs/g, "fields_l");
                            $('#table-field').append(li);
                        }

                    }

                }else{
                    //左表-字段
                    $.each(l_data['fields'], function (k, v) {
                        option_l+= '<option name="fieldOne" class="left-table" value="'+v['field_name']+'">'+v['field_remark']+'</option>';
                    });

                    //右表--字段
                    $.each(r_data['fields'], function (kf, vf) {
                        option_r+= '<option name="fieldTwo" value="'+vf['field_name']+'">'+ vf['field_remark'] +'</option>';
                    });
                    $('.fields_l').append(option_l);
                    $('.fields_r').append(option_r);
                }

                $('#table_l').append(table_l);

                //绑定表名下拉框变更事件
                $('#table_l').on('change',function () {
                    _POP.select_change();
                });

                _POP.filed_changes();//字段下拉框绑定更改事件

            };
            //展示编辑字段设置弹层
            _POP.edit_show = function(uuid){
                var view_id = $("#groupId").val();//获取当前视图id
                $.ajax({
                    type: 'get',
                    url: '/webi/design/views/group/search/' + view_id + '/' + uuid,
                    success: function (res) {
                        if (res.code == 200) {
                            if (res.data['tableL']){

                                _POP.l_options = res.data['tableL']['allFields'];//存左侧表字段
                                _POP.r_options = res.data['tableR']['allFields'];//存右侧表字段

                                var  layer_content = layer_linked_edit;

                                layer_content = layer_content.replace(/tableName-select/g, res.data['tableR']['table'][0]);
                                layer_content = layer_content.replace(/selectTable/g,res.data['tableR']['table'][1]);

                                layer.open({
                                    title: '修改表关联信息',
                                    offset: '70px',
                                    type: 1,
                                    area: ['540px', '270px'],
                                    scrollbar: false,
                                    closeBtn: 0,
                                    content: layer_content,
                                    btn: ['确认', '取消'],
                                    yes: function () {

                                        var  checkTableL = $("option:selected", $('#table_l')).val();
                                        var checkTableR = res.data['tableR']['table'][1].toString();
                                        var innerType = $(".bomb-box .clickli").attr("data-code");

                                        var leftField = "";
                                        var rightField = "";
                                        var field = [];
                                        var msg = "";
                                        var pop_msg = "";

                                        $.each( $('#table-field li') , function(){
                                            leftField = $(this).find(".fields_l option:selected").val();
                                            rightField = $(this).find(".fields_r option:selected").val();

                                            if($.inArray(leftField + ":" + rightField , field) != "-1"){
                                                $(this).addClass("li-lable");//重复提示框
                                                pop_msg = "关联字段已重复";
                                                return false;
                                            }
                                            field.push(leftField + ":" + rightField);
                                        });
                                        if(pop_msg != ""){
                                            layer.msg( pop_msg, {icon: 2, offset: '70px',time:1500});
                                            return false;
                                        }

                                        //递归修改table_json字符串   左表名，连接类型，右表名，右表描述，链接字段,uuid
                                        G_handle.addJson(checkTableL,innerType,checkTableR,"",field,uuid);

                                        if(msg == ""){
                                            _POP.submit( view_id, _G.globalJosn,2);//保存

                                            //初始化左右侧表字段
                                            _POP.l_options = [];
                                            _POP.r_options = [];
                                        }

                                    }

                                })

                                _POP.add_show(res.data['tableL'], res.data['tableR']);//对弹窗进行赋值

                                if(res.data['tableR']['innerType']) {

                                    $.each($('#edit-link li'), function (k, v) {
                                        var inner=$(this).attr("data-code");
                                        if(inner==res.data['tableR']['innerType']){
                                            $(this).attr('class', 'clickli');
                                        }
                                    });

                                }

                            }else{
                                layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                            }
                        }
                    }
                 })

            };
            //表名下拉框变更
            _POP.select_change = function(){
                var checkText = $("option:selected",$('#table_l')).val();
                var group_id = _G.groupID();//获取数据集来源ID
                $.ajax({
                    type: 'get',
                    data:{
                        table_name: checkText,
                        group_id: group_id
                    },
                    url: '/webi/design/views/table/search/rule',
                    success: function (res) {
                        if( res.code == 200 ){

                            _POP.l_options = res.data['fields'];//修改存储的左表字段
                            _POP.select_options(res.data['fields']);//贴上选中表的字段信息（option）

                        }
                    }
                })
            };
            //字段下拉框绑定更改事件
            _POP.filed_changes = function(){

                $('.fields_l').on('change',function () {
                    _POP.option_change();
                });

                $('.fields_r').on('change',function () {
                    _POP.option_change();
                });
            };
            //字段值下拉框变更
            _POP.option_change = function(){

                var field = [];
                _POP.error_msg = "";
                $('#table-field li').removeClass("li-lable");

                $.each($('#table-field li'), function () {
                    leftField = $(this).find(".fields_l option:selected").val();
                    rightField = $(this).find(".fields_r option:selected").val();

                    if($.inArray(leftField + ":" + rightField , field) != "-1"){
                        $(this).addClass("li-lable");
                        _POP.error_msg = "关联字段已重复";
                        layer.msg( _POP.error_msg, {icon: 2, offset: '70px',time:1500});
                        return false;
                    }
                    field.push(leftField + ":" + rightField);
                });


            };
            //贴上选中表的字段信息（option）
            _POP.select_options = function(data){

                $('#table-field .fields_l').empty();
                var option_l="";
                $.each( data, function (k, v) {
                    option_l+='<option name="fieldOne" class="left-table" value="'+v['field_name']+'">'+v['field_remark']+'</option>';
                });
                $('#table-field .fields_l').append(option_l);

            };
            //添加一行
            _POP.add_row = function(){

                var table_l = $("option:selected", $('#table_l')).val();
                var table_r = $('#table_r').data('name');
                var optionsR = "";
                var optionsL = "";

                var li = $('#link-layer-hidden').html();
                    li = li.replace(/fieldsLs/g, "fields_l");
                    li = li.replace(/fieldsRs/g, "fields_r");
                    $('#table-field').append(li);

                //左侧字段
                $.each( _POP.l_options, function (kl, vl) {
                    optionsL += '<option name="fieldOne" class="left-table" value="'+vl['field_name']+'">'+vl['field_remark']+'</option>';
                });
                $('#table-field li:last-child .fields_l').append(optionsL);

                //右侧字段
                $.each(_POP.r_options, function (kr, vr) {
                    optionsR += '<option name="fieldTwo" value="'+vr['field_name']+'" >'+vr['field_remark']+'</option>';
                });
                $('#table-field li:last-child .fields_r').append(optionsR);

                _POP.filed_changes();//字段下拉框绑定更改事件

                //删除一行（option）
                $('.bomb-box-delete').on('click', function () {
                    $(this).closest('li').remove();
                })

            };
            //提交页面数据，保存JSON
            _POP.submit = function(view_id,table_json,type){

                <!--保存添加，隐藏数据集-->
                if( type == 1 ){
                    _G.hideTable();
                }

                $.ajax({
                    type: 'post',
                    url: '/webi/design/views/group/link/add',
                    data: {
                        view_id: view_id,
                        table_json:table_json,
                        type:type
                    },
                    success: function (result) {
                        if (result.code == 200) {
                            layer.closeAll();

                            //清空右侧页面数据
                            $("#right-content").empty();

                            //刷新页面
                            setTimeout($("#g_" + view_id).trigger("click"),2000);


                        } else {
                            layer.msg(result.msg, {icon: 2, offset: '70px',time:1500});
                        }
                    }
                });

            };


            //删除节点
            G_handle.del_node = function(del_uid,parent_uid) {

                if( typeof _G.globalJosn[del_uid] == 'undefined'){
                    alert('数据不存在');
                    return false;
                }

                //删除本元素下面所有关联表数据
                G_handle.diguiDel( _G.globalJosn[del_uid].join );

                delete _G.globalJosn[del_uid];

                if(!$.isEmptyObject(_G.globalJosn[parent_uid])){
                    delete _G.globalJosn[parent_uid]['join'][del_uid];
                }
                if( _G.globalJosn == {}){
                    _G.globalJosn = "";
                }

                return _G.globalJosn;
            };
            //递归删除所有子节点
            G_handle.diguiDel = function(obj) {
                if( obj.length == 0 ){
                    return false;
                }
                for( k in obj ){

                    delete _G.globalJosn[k];

                    if( obj[k].join.length > 0 ){
                        G_handle.diguiDel(obj[k].join);
                    }

                }
            };
            //递归添加修改所有子节点
            G_handle.addJson = function(checkTableL,innerType,checkTableR,description,field,uid) {

                    if( checkTableL == checkTableR ){
                        layer.msg("左右关联表不可相同", {icon: 2, offset: '70px',time:1500});
                        return false;
                    }

                    if( uid != "" ){//修改

                        for( k in _G.globalJosn ){

                            if(  _G.globalJosn[k].table == checkTableL ) {

                                delete _G.globalJosn[k]['join'][uid];//删除原关联
                                _G.globalJosn[k]['join'][uid] = [ checkTableL,innerType,field];
                                _G.globalJosn[uid]['parent'] = k;//修改关联父级uuid
                            }

                        }

                    }else{//添加

                        var uuid = BI.guid();//生成uuid

                        if( !$.isEmptyObject(_G.globalJosn) ){

                            for( k in  _G.globalJosn ){

                                //查找主表数组并在数组中添加关联子表数组
                                if( _G.globalJosn[k].table == checkTableL ){

                                    if (_G.globalJosn[k]['join'] == '[]') {
                                        _G.globalJosn[k]['join'] = {};
                                    }

                                    _G.globalJosn[k]['join'][uuid]=[checkTableR,innerType,field];

                                    //生成子表为主表的数组
                                    _G.globalJosn[uuid] = {
                                        uid : uuid,
                                        parent : k,
                                        table : checkTableR,
                                        table_name : description,//当前表中文名
                                        join : {}
                                    }

                                    return false;
                                }
                            }
                        }else{

                            _G.globalJosn[uuid] = {
                                uid : uuid,
                                parent : "",
                                table : checkTableR,
                                table_name : description,
                                join : {}
                            };

                        }

                    }

                };

    </script>
@endsection

