@extends('webi.layout')

@section('css')
    <link rel="stylesheet" href="/css/webi/list.css?v=201803221330">

    <style>
        .dropdown-menu{
            min-width: 60px;
        }
        .iradio-blue{
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            padding: 0;
            width: 22px;
            height: 22px;
            background-color:blue;
            border: none;
            cursor: pointer;
        }
        .add-list a:hover{
            text-decoration:none;
        }
        .test-t{
            text-align: center;
            max-width: 670px;
            height:33px;
            padding: 10px;
            font-size: 14px;
            float: left;
        }
        .user-dropdown{
            position: absolute;
            float: right;
            display: block;
            text-decoration: none;
            cursor: pointer;
            outline: none;
            padding-right: 10px;
            min-width: 160px;
        }
        .user-dropdown{
            position: relative;
        }
        .user-dropdown .user{
            margin-top: -7px;
        }
        .user .username-control{
            font-size: 16px;
            color: #5bc0de;
            max-width:167px;
            padding: 0 12px;
        }
        .arrows{
            position: absolute;
            top: 5.3px;
            width: 25px;
            height: 34px;
            background: url(/images/webi/biedit/icon-arr3.png) no-repeat center center;
        }
        .user-dropdown-meta{
            height:25px;
            line-height: 18px;
        }
        .operation{
            text-align: center;
        }
        .operation li{
            background-color: white;
            box-shadow: 3px 5px 4px rgba(0,0,0,.05);
            -moz-box-shadow:3px 5px 4px rgba(0,0,0,.05);
            -webkit-box-shadow:3px 5px 4px rgba(0,0,0,.05);
        }
        .test-t .top-btn {
            margin-top: 5px;
            width: 100px;
            height: 32px;
            text-align: center;
            line-height: 32px;
            color: #fff;
            border-radius: 20px;
        }
        .test-t .overview {
            background: #5cd489;
        }
        .test-t .addBi {
            background: #70a7ff;
        }
        .test-t a{
            float: left;
            display: block;
            margin-right: 20px;
            text-decoration: none;
            cursor: pointer;
            outline: none;
        }
        .user-dropdown-meta a{
            width:107px;
            margin-left: 0;
            text-align: center;
            height:25px;
            line-height: 22px;
        }
        .selected-lable{
            box-shadow: 0 0 0 2px rgba(81,130,227,.15),inset 0 0 0 2px #108ee9 !important;
        }
        .test-r{
            margin-top: .7%;
            float: right;
        }
        .layer-comfirm{
            margin-left: 400px;
            width: 244px;
            height:300px;
            border: 1px solid #d2d2d2;
            background-color: #fff;
            -webkit-background-clip: content;
            border-radius: 2px;
            box-shadow: 1px 1px 50px rgba(0,0,0,.3);
        }
        .layer-comfirm .layer-title{
            padding: 0 20px 0 20px;
            height: 42px;
            line-height: 42px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
            overflow: hidden;
            background-color: #F8F8F8;
            border-radius: 2px 2px 0 0;
        }
        .layer-wrap{
            line-height: 26px;
            margin-left: 20px;
            overflow: visible;
            list-style-type: disc;
        }
        .layer-wrap li{
            height:30px;
        }
        .layer-wrap li a{
            width: 244px;
            overflow-y:auto;
            overflow-x:hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
        }
        .glyphicon{
            font-size: 10px;
            top:14px;
            float:right;
            cursor: pointer;
        }
        .layui-form-item{
            display: inline-block;
        }
        .layui-input-block{
            margin: 0;
        }
        .layui-form-radio{
            margin: 0;
            padding-right: 0;
        }
        .border5{
            border-radius: 5em!important;
        }
    </style>
@endsection

@section('content')
    <div id="wrapper">

        <div class="content">

            <div class="left-sider">
                <div class="left-logo">
                    <a href="/webi/list/index"><img src="/images/webi/logo.png" alt="WeBI" width="100%" height="59px;"></a>
                </div>
                <!--左侧列表-->
                <div class="left-list">
                    <div class="add-list">
                        <div onclick="_G.editGroup(0);">
                            <span>报表分组 <i>+ </i></span>
                        </div>
                        {{--<a  title="添加分组" style="cursor: pointer" onclick="_G.editGroup(0);">+</a>--}}
                    </div>
                    <ul id="group-nav" class="group-content" ondrop="_G.drop(event)" ondragover="_G.allowDrop(event)">
                        @if(isset($group))
                            @foreach($group as $g)
                                <li data-id="{{$g['_id']}}" id="group_sider_{{$g['_id']}}" >
                                    <div style="float:left; width:85%;" class="group-each" id="{{$g['_id']}}">
                                        <a id="group_{{$g['_id']}}">{{$g['group_name']}}</a>
                                    </div>

                                    <div type="button" style="height: 48px;float:left;padding-top: 5px;"  class="dropdown" id="group_{{$g['_id']}}">
                                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                             <i class="glyphicon glyphicon-option-vertical text-style" ></i>
                                        </span>
                                        <ul class="dropdown-menu dropdown-menu-right" >
                                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;" onclick="_G.editGroup('{{$g['_id']}}')";>编辑</a></li>
                                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick="_G.del('{{$g['_id']}}');">删除</a></li>
                                        </ul>
                                    </div>

                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            <div class="right-sider">
                <!--头部搜索-->
                <div class="top-title over" >

                    <div  class="test-t">
                        @if($btn)
                            <a href="/webi/design/views/list" id="head_chooseBI" class="top-btn addBi" >我的数据集</a>
                        @endif

                        <div class="fr input-box" style="display: inline; margin-top: 2px;">
                            <input type="text" id="search" class="layui-input border5" placeholder="搜索报表" />
                            <i class="search" onclick="_H.onKeyDown()"></i>
                        </div>
                    </div>
                    <div class="test-r">
                        <div class="thumb">
                            @if( isset($head_pic) && $head_pic != NULL )
                                 <img src="{{$head_pic}}">
                            @else
                                <img src="/images/m/common/headpic.png">
                            @endif
                        </div>
                        <div class="user-dropdown">
                            <ul class="user">
                                <li class="username-control"><a href="#" style="margin-left: 10px;height: 38px;">{{$userName}}@if(!empty($role))({{$role}}) @endif<i class="arrows"></i></a>
                                    <ul style="display: none" class="operation">
                                        @if($is_admin) <li class="user-dropdown-meta"><a target="_blank"  href="/webi/design/biuser/list" style="width: 100%;">用户管理</a></li> @endif

                                        <li class="user-dropdown-meta"><a href="/webi/shop/logout" style="width: 100%;">退出</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div id="search-layer" style="display: none;" >
                        <div class="layer-comfirm" style="margin-left: 11.5%;margin-top: 60px;">
                            <div class="layer-title">可选项<i class="glyphicon glyphicon-remove"  onclick="_H.close()"></i></div>
                            <div class="select-layer-content">
                                <ul class="layer-wrap" style="display: block;">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="" style="height: 58px"></div>
                <!--主内容-->
                <div class="main-cont">
                    <input type="hidden" id="group_id" value="{{$group_id}}"/>
                    <div class="main-list">
                        <ul class="content-master">
                        </ul>
                    </div>
                </div>
                <!--主内容 END -->
            </div>

            {{--新建报表li HTML--}}
            <div id="add-master" style="display:none;" >
                <li class="add-form"  style="box-shadow: 0 0 3px 3px rgba(0,0,0,.05);" onclick="_M.addBI('g_id')">
                   <img src="/images/webi/bi/icon-ador.png" alt="" draggable="false">
                    <p>新建报表</p>
                </li>
            </div>
            {{--报表li HTML--}}
            <div id="master-content" style="display: none">
               <li style="box-shadow: 0 0 3px 3px rgba(0,0,0,.05);margin-bottom: 16px;" id="master_bi_id">
                    <div type="button" style="height: 48px;float:right;padding-right: 8px;margin-top:-18px;cursor:  pointer;"  class="dropdown">
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i><img src="/images/webi/bi/icon-line.png" draggable="false" style="width:10px;height:12px;"/></i>
                        </span>
                       <ul class="dropdown-menu dropdown-menu-right" style="padding:0;">
                            <li  style="height:30px;padding-left:0;width: 20px;padding-top: 0;padding-bottom: 0;cursor:pointer"><a  style="width:57px;padding-left:15px" onclick="_M.edit('uuid')">编辑</a></li>
                            <li  style="height:30px;padding-left:0;width: 20px;padding-top: 0;padding-bottom: 0;cursor:pointer"><a  style="width:57px;padding-left:15px" onclick="_M.copy_master('bi_id')">复制</a></li>
                            <li  style="height:30px;padding-left:0;width: 20px;padding-top: 0;padding-bottom: 0;cursor:pointer"><a  style="width:57px;padding-left:15px" onclick="_M.remove('bi_id');">移动</a></li>
                            <li  style="height:30px;padding-left:0;width: 20px;padding-top: 0;padding-bottom: 0;cursor:pointer"><a  style="width:57px;padding-left:15px" onclick="_M.del('bi_id');">删除</a></li>
                       </ul>
                    </div>

                    <a onclick="window.open( '/webi/design/edit/uuid');">
                        <img src="/images/webi/bi/icon-order.png" alt="" data-group="gid" id="uid_bi_id" draggable="true" ondragstart="_G.drag(event)" title="点击可编辑报表" style="padding-left:20px">
                        <p>bi_title</p>
                    </a>
               </li>
            </div>
            {{--添加新分组HTML--}}
            <div id="group-nav-each" style="display: none">
               <li id="group_sider_g_group_id" data-id="g_group_id">
                   <div style="float:left; width:85%" class="group-each" id="g_group_id">
                       <a id="group_g_group_id">g_group_title</a>
                   </div>
                    <div type="button" style="height: 30px;float:left;padding-right: 3%;padding-top: 5px;"  class="dropdown"  id="group_g_group_id">
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="glyphicon glyphicon-option-vertical text-style" ></i>
                        </span>
                       <ul class="dropdown-menu dropdown-menu-right" >
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick='_G.editGroup("g_group_id")';>编辑</a></li>
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick="_G.del('g_group_id');">删除</a></li>
                       </ul>
                    </div>
               </li>
            </div>
            {{--添加分组弹窗HTML--}}
            <div id="layer_add_group" style="display: none">
               <form id="title-form-header" onsubmit="return false;" class="form-horizontal" role="form" style="margin-top: 20px;">
                    <input type="hidden" name="bi_id" id="bi_id" value="0">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="title-header"><span class="red pr5">*</span>&ensp;名称：</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="title-header" id="title-header" placeholder="请输入名称" value="">
                           </div>
                        </div>
                  </form>
            </div>
            {{--搜索下拉框li  HTML--}}
            <div id="search-li" style="display: none">
                <li data-group="groupId" id="li_biId" style="line-height: 26px;margin-left: 20px;overflow: visible;list-style-type: disc;"><a title="分组:tips-search" >bi_title</a></li>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/libs/jquery-ui/jquery-ui.js"></script>
    <script>

        var _H = {};//头部操作对象
            _G = {};//分组操作对象
            _M = {};//报表操作对象

        var _G_Layer_Group = "";//添加分组弹窗HTML
        var report_id = '',
            group = '';

        //点击搜索
        _H.onKeyDown = function(){
            var group_id = $("#group_id").val();
            var inputValue = $.trim( $("#search").val() );

            if( inputValue == '' ){
                layer.msg("请输入报表名称", {icon: 2, offset: '70px', time: 1500});
                return false;
            }

            $.ajax({
                type: 'GET',
                url: '/webi/design/report/search/'+inputValue,
                dataType: 'JSON',
                success: function (res) {
                    $("#search").val("");
                    if( res.code != 200 ){
                        layer.msg(res.msg, {icon: 2, offset: '70px', time: 1500});
                        return false;
                    }

                    if( res.data.length == 0 ){
                        layer.msg("暂无匹配信息", {icon: 1, offset: '70px',time: 1500});
                        return false;
                    }

                    if( res.data.length == 1 ){
                        _G.list(res.data[0]['group_id'], res.data[0]['_id']);//仅有一条匹配项，直接选中
                    } else {
                        _H.search(res.data);
                    }

                }
            });
        };
        //搜索选择框
        _H.search = function(data){
            $("#search-layer").css('display',"block");
            var li_html = '';

            $.each(data, function (k, v) {
                li_html += $("#search-li").html();
                li_html = li_html.replace(/groupId/g,v['group_id']);
                li_html = li_html.replace(/biId/g,v['_id']);
                li_html = li_html.replace(/tips-search/g,v['group_name']);
                li_html = li_html.replace(/bi_title/g,v['bi_title']);
            });

            $(".select-layer-content .layer-wrap").append(li_html);

            $.each($('.layer-wrap li'), function (k, v) {
                $(this).on('click', function () {
                    _G.list($(this).data('group') , $(this).attr('id').substring(3));//选中并查询对应分组数据
                })
            });
        };
        //搜索选择框关闭
        _H.close = function(){
            $("#search-layer").css("display","none");
            $(".select-layer-content .layer-wrap").empty();
        };


        _G.editGroup = function(group_id){
            var layer_title = group_id == 0 ?   '新增分组' : '编辑分组';
            layer.open({
                title: layer_title,
                offset: '70px',
                type: 1,
                area: ['500px', '180px'],
                scrollbar: false,
                content: _G_Layer_Group,
                btn: ['确认','取消'],
                success: function() {
                    $("#title").focus();

                    if( group_id != 0 ){
                        $("#title").val( $("#group_"+group_id).text() );
                    }
                },
                yes: function () {
                    var dt = E.getFormValues('title-form');

                    if( dt.title == '' ){
                        layer.msg("请输入分组名!", {icon: 2, offset: '70px',time: 1500});
                        return false;
                    }

                    dt.group_id = group_id;

                    E.ajax({
                        type: 'post',
                        url: '/webi/design/group/edit',
                        data:dt,
                        success: function (res) {
                            layer.closeAll();
                            if( res.code != 200 ){
                                layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                                return false;
                            }

                            if( group_id == 0 ){

                                $("#title").val("");
                                var nav_html = $("#group-nav-each").html();
                                nav_html = nav_html.replace(/g_group_id/g,res.data);
                                nav_html = nav_html.replace(/g_group_title/g,dt.title);

                                $("#group-nav").append(nav_html);
                                $("#"+res.data).on('click',function(){
                                    _G.list(res.data);
                                });
                                $("#"+res.data).trigger("click");//触发当前添加分组

                            } else {
                                $("#group_"+group_id).html(dt.title);
                            }

                            layer.msg('保存成功', {icon: 1, offset: '70px'});
                        }
                    });
                }
            });
        }
        _G.del = function(group_id){
            //删除分组（首先查询分组下是否存在报表）
            $.ajax({
                type: 'get',
                url: '/webi/design/group/search/global/'+group_id,
                success:function(o) {

                    if( o.code != 200 ){
                        layer.msg( '获取分组报表信息失败' , { icon: 2,offset:'70px;', time: 1500} ) ;
                        return false;
                    }

                    if( o.bi_num == 0 ){
                        _G.del_confirm(group_id);
                    } else {
                        _G.del_move(group_id,o);
                    }

                }
            });
        }
        _G.del_confirm = function(group_id) {
            layer.confirm('是否确认删除?',{icon:3},function (index) {
                layer.close(index);
                $.ajax({
                    type: 'post',
                    url: '/webi/design/group/del',
                    data:{
                        group_id:group_id,
                        target_id:0
                    },
                    success: function (o) {

                        if( o.code != 200 ){
                            layer.msg(o.message, {icon: 2, offset: '70px', time: 1500});
                            return false;
                        }

                        $("#group_sider_"+group_id).remove();
                        $(".content-master").empty();
                        layer.msg('删除成功', {icon: 1, offset: '70px', time: 1500});
                    }
                });
            });
        }
        _G.del_move = function(group_id, o) {
            if( o.group.length == 0 ){
                layer.msg("项目无可移动分组，请新建分组后再删除！", {icon: 2, OFFSET: '70px', TIME: 1500});
                return false;
            }

            //遍历查询出的分组并显示在html中
            var move_table = _M.move_form(o.group);

            layer.open({
                title: '转移报表至其他分组',
                offset: '70px',
                type: 1,
                area: ['500px', '200px'],
                scrollbar: false,
                closeBtn: 0,
                content: move_table,
                btn: ['确认','取消'],
                success:function() {
                    layui.use('form', function() {
                        var form = layui.form;
                        form.render();
                    });
                },
                yes: function () {
                    var dt = E.getFormValues('remove-form');

                    if($.isEmptyObject(dt.group_id)){
                        layer.msg( '请选择要移动的分组！' , { icon: 2,offset:'70px;', time: 1500} ) ;
                        return false;
                    }

                    E.ajax({
                        type: 'post',
                        url: '/webi/design/group/del',
                        data:{
                            group_id:group_id,
                            target_id:dt.group_id
                        },
                        success: function (res) {

                            layer.closeAll();

                            if (res.code != 200) {
                                layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                                return false;
                            }

                            $("#group_sider_"+group_id).remove();
                            _G.list(dt.group_id);
                            layer.msg('删除成功', {icon: 1, offset: '70px', time: 1500});
                        }
                    });
                }
            });

        }
        _G.list = function(group_id,bi_id){
            var bi_id = bi_id || 0;

            $("#"+group_id).eq($(this).index()).parent().addClass("cur-list").siblings().removeClass("cur-list");
            $('.content-master').empty();

            $.ajax({
                type: 'get',
                url: '/webi/design/report/list/'+group_id,
                success: function (res) {

                    if( res.code != 200 ){
                        return false;
                    }

                    $("#group_id").val(group_id);

                    var eHtml = $("#add-master").html();
                    eHtml= eHtml.replace(/g_id/g,group_id);
                    $('.content-master').append(eHtml);

                    if (!($.isEmptyObject(res.master))) {

                        $('.content-master').append( _M.create_master(group_id,res.master) );
console.log(bi_id)
                        if( bi_id != 0 ){
                            //移除之前选中的报表背景色
                            $("li").removeClass("selected-lable");
                            //查找到的报表添加背景色
                            $("#master_"+ bi_id).addClass("selected-lable");
                        }
                    }

                }
            });
        };


        //生成报表（li）
        _M.create_master = function(group_id,data){
            var master_html="";
            $.each(data, function (k, v) {

                master_html+=$('#master-content').html();
                master_html = master_html.replace(/bi_title/g,v.bi_title);
                master_html = master_html.replace(/bi_id/g,v._id);

                master_html = master_html.replace(/uuid/g,v.uid);
                master_html = master_html.replace(/gid/g,group_id);

            });
            return master_html;
        };
        //弹窗 移动单选框
        _M.move_form = function(data){
            var  move_html  = '<form id="remove-form" onsubmit="return false;" class="form-horizontal layui-form" role="form" style="margin-top: 20px;">';
            move_html += '<div class="form-group ">';

            $.each(data, function (k, v) {
                move_html += '<div class="layui-form-item" style="padding-left: 30px;">' ;
                move_html += '<div class="radio">';
                move_html += '<label class="layui-input-block">';
                move_html += '<input class="square-radio" id="group_type" name="group_id" type="radio" value="'+v._id+'">'+v.group_name+'&nbsp;&nbsp;';
                move_html += '</label>';
                move_html += '</div>';
                move_html += '</div>';
            });

            move_html += '</div>';
            move_html += '</form>';

            return  move_html;
        };
        //新建报表
        _M.addBI = function(group_id){
            layer.open({
                title: '新建报表',
                offset: '70px',
                type: 1,
                area: ['500px', '190px'],
                scrollbar: false,
                closeBtn: 0,
                content: _G_Layer_Group,
                btn: ['确认','取消'],
                yes: function () {
                    var dt = E.getFormValues('title-form');
                    if(dt.title ==""){
                        layer.msg( '请输入报表名称！' , { icon: 2,offset:'70px;', time: 1500} ) ;
                        return false;
                    }

                    dt.group_id = group_id;

                    E.ajax({
                        type: 'post',
                        url: '/webi/design/report/add',
                        data: dt,
                        success: function (res) {

                            if( res.code != 200 ){
                                layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                                return false;
                            }

                            layer.closeAll();
                            layer.msg('保存成功', {icon: 1, offset: '70px', time: 1500});

                            //页面追加新建报表
                            $('.content-master').append( _M.create_master(group_id,res.message['master']) );

                            //跳转到编辑页面
                            setTimeout('_M.edit("'+res.message.master[0]['uid']+'");' ,1500 );
                        }
                    });
                }
            });
        };
        //复制报表
        _M.copy_master = function(bi_id){
            E.ajax({
                type: 'get',
                url: '/webi/design/report/copy',
                data: {
                    'bi_id':bi_id
                },
                success: function (res) {

                    if( res.code != 200 ){
                        layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                        return false;
                    }

                    layer.closeAll();
                    layer.msg('复制成功', {icon: 1, offset: '70px', time: 1500});

                    //页面追加新建报表
                    var creat = _M.create_master(group_id,res.data['master']);
                    $('.content-master').append(creat);
                }
            });
        };
        //编辑报表
        _M.edit = function(uid){
            window.open( '/webi/design/edit/'+uid);
        };
        //移动报表
        _M.remove = function(bi_id){
            var group_id = $('#group-nav').find('.cur-list')[0].getAttribute('data-id');

            $.ajax({
                type: 'get',
                url: '/webi/design/group/search/global/'+group_id,
                success:function(o) {

                    if( o.code != 200 ){
                        layer.msg( '获取分组报表信息失败' , { icon: 2,offset:'70px;', time: 1500} ) ;
                        return false;
                    }

                    if( o.group.length == 0 ){
                        layer.msg("没有可移动分组", {icon: 2, offset: '70px', time: 1500});
                        return false;
                    }

                    var move_table = _M.move_form(o.group);

                    layer.open({
                        title: '移动报表',
                        offset: '70px',
                        type: 1,
                        area: ['500px', '300px'],
                        scrollbar: false,
                        closeBtn: 0,
                        content: move_table,
                        btn: ['确认','取消'],
                        success:function() {
                            layui.use('form', function() {
                                var form = layui.form;
                                form.render();
                            });
                        },
                        yes: function () {
                            var dt = E.getFormValues('remove-form');
                            if($.isEmptyObject(dt.group_id)){
                                layer.msg( '请选择要移动的分组' , { icon: 2,offset:'70px;', time: 1500} ) ;
                                return false;
                            }

                            E.ajax({
                                type: 'post',
                                url: '/webi/design/group/move/away',
                                data:{
                                    bi_id:bi_id,
                                    group_id: dt.group_id
                                },
                                success: function (res) {
                                    if( res.code != 200 ){
                                        layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                                        return false;
                                    }

                                    layer.closeAll();
                                    layer.msg('移动成功', {icon: 1, offset: '70px', time: 1500});
                                    _G.list(dt.group_id);
                                }
                            });
                        }
                    });

                }
            });

        };
        //删除报表
        _M.del = function(bi_id){ 
            layer.confirm('是否确认删除?',{icon:3},function (index) {
                layer.close(index);
                $.ajax({
                    type: 'get',
                    url: '/webi/design/report/del/'+bi_id,
                    success: function (res) {
                        if( res.code != 200 ){
                            layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                            return false;
                        }
                        $("#master_"+bi_id).remove();
                        layer.msg('删除成功', {icon: 1, offset: '70px', time: 1500});
                    }
                });
            })
        };


        //报表拖动
        _G.drag = function(ev){
            ev.dataTransfer.setData("moving_bi_id", (ev.target.id).substring(4));
            ev.dataTransfer.setData("moving_group_id", ev.target.getAttribute('data-group'));
        };
        //报表拖动移动分组
        _G.drop = function(ev) {

            ev.preventDefault();

            var title = ev.target.innerText;
            var g_id = "";

            var moving_bi_id = ev.dataTransfer.getData("moving_bi_id");
            var moving_group_id = ev.dataTransfer.getData("moving_group_id");

            if (ev.target.nodeName == 'DIV') {//dom对象是DIV
                g_id = ev.target.id;
            } else {
                g_id = (ev.target.id).substring(6);//dom对象是A链接
            }

            if (moving_group_id == g_id) {
                return false;
            }

            layer.confirm('确认移动至分组“' + title + '”吗?', {icon: 3}, function () {

                $.ajax({
                    type: 'post',
                    url: '/webi/design/group/move/away',
                    data: {
                        bi_id: moving_bi_id,
                        group_id: g_id
                    },
                    success: function (res) {

                        if (res.code != 200) {
                            layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                            return false;
                        }

                        layer.closeAll();
                        layer.msg('移动成功', {icon: 1, offset: '70px', time: 1500});
                        _G.list(g_id);
                        g_id = "";
                    }
                });

            });

        }
        //报表拖动禁用其他方法
        _G.allowDrop = function(ev){
            ev.preventDefault();
        }

        $(document).ready(function() {
            //绑定分组查询事件
            $.each($('.group-each'),function (k,v){
                $(this).on('click',function(){
                    _G.list( $(this).attr('id') );
                })
            });

            //触发默认分组
            var group_id = $("#group_id").val();
            if(!$.isEmptyObject(group_id)){
                $("#"+group_id).trigger("click");
            }

            //用户名下拉框
            $(".username-control").mouseenter(function(){
                $(".operation").css('display','block');
            });
            $(".username-control").mouseleave(function(){
                $(".operation").css('display','none');
            });

            //替换添加分组重要ID
            _G_Layer_Group = $("#layer_add_group").html();
            _G_Layer_Group = _G_Layer_Group.replace(/title-form-header/g,"title-form");
            _G_Layer_Group = _G_Layer_Group.replace(/title-header/g,"title");
        });
        
    </script>
@endsection

