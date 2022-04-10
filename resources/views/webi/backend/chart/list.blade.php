@extends('backend')

@section('title')
    图表类型维护
@endsection

@section('css')

    <style>
        .nav-g {
            float: left;
            width: 18%;
            height: 500px;
            background: #f1f1f1;
        }
        .add-list{
            padding: 14px 18px;
            height: 51px;
        }
        .add-list a{
            display: block;
            text-align: center;
            border: 1px dashed #70a7ff;
            background: #fff;
            font-size: 40px;
            line-height: 36px;
            color: #70a7ff;
        }
        .table-bi .table-name {
            width: 100%;
            text-align: center;
        }
        .table-list {
            padding: 5px;
        }
        .group-add {
            width:100%;
            height:40px;
            display: block;
            text-align: left;
            font-size: 40px;
            line-height: 42px;
        }
        .content{
            height: 500px;
            float: right;
            width: 82%;
        }
        .edit_button{
            width:100%;
        }
        .title{
            width:70%;
            float:left;
            font-size: 12px;
            padding-top: 6px;
            padding-left: 5px;
            height:25px;
        }
        .pull-right{
            cursor: pointer;
        }
        .color{
            background-color: #fff;
            border-left: 2px solid #70a7ff;
            color: #70a7ff;
        }
        .group-each{
            padding-left: 18px;
            font-size: 14px;
            cursor: pointer;
            position: relative;
        }
        .group-each>.dropdown{
            line-height:40px;
            margin-right:15px;
        }
        .group-each ul{
            position: absolute;
        }
        .dropdown{
            position:relative;
        }
        .dropdown-menu{
            position: absolute;
            top: 40px;
            right: -16px;
            z-index: 9;
            background: #fff;
            border: 1px solid #eeeeee;
            display: none;
        }

        .dropdown-menu li{
            width:auto;
            height: auto;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        .dropdownMenu3 .dropdown-toggle{
            float:right;
        }
        .dropdownMenu3 .dropdown-menu li{
            width: 60px;
            height: 30px;
            line-height: 30px;
        }

        .add-form{
            min-height: 170px;
            float: left;
            margin-top: 12px;
            margin-left: 9px;
            margin-right: 3.9px;
            width: 214px;
            height: 160px;
            box-shadow: 0 0 3px 3px rgba(0,0,0,.05);
            font-size: 12px;
            cursor: pointer;
        }
        a:hover{
            text-decoration:none;
        }
        .group_sider {
            height: 40px;
        }

    </style>
@endsection

@section('content')

    {{--三级导航--}}
    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li id="active_1">
                    <a href="/webi/backend/dataset/index/1"><span>图表类型维护</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper" style="padding: 0px;width: 100%;margin-left:0;margin-top: 0;">
        <input type="hidden" id="group_id" value="{{$group_id}}"/>

        <div class="nav-g" id="group">

            <li class="add-list" style="cursor: pointer;padding-top: 5px;" onclick="group.addGroup()" title="添加图表分组">
               <a>+</a>
            </li>

            {{--遍历分组--}}
            @if(isset($group))
                @foreach($group as $g)

                    <li class="group-add group-each"  id="group_sider_{{$g['_id']}}" >
                        <div class="group_sider" id="button_{{$g['_id']}}" style="width:150px;float:left;">
                             <span id="group_name_{{$g['_id']}}">{{$g['group_name']}}</span>
                        </div>
                        <div type="button" style="height: 30px;float:right;padding-right: 15px;"  class="dropdown dropdownMenu1" style="height: 15px;"  id="group_{{$g['_id']}}">
                                <span class="dropdown-toggle" type="button" >
                                     <i class="layui-icon layui-icon-more-vertical text-style" ></i>
                                </span>
                            <ul class="dropdown-menu dropdown-menu-right" style="min-width: 60px;padding:0;">
                                <li><a style="padding:0;" onclick="group.edit('{{$g['_id']}}')";>编辑</a></li>
                                <li><a style="padding:0;"  onclick="group.del('{{$g['_id']}}');">删除</a></li>
                            </ul>
                        </div>

                    </li>
                @endforeach
            @endif

        </div>

        <div class="content ">
            {{--新建类型表--}}
            <div class="table-list">
                <ul class="content-master"> </ul>
            </div>


        </div>

        <div id="hidden-group" style="display:none;" >
            <li class="group-add group-each"  id="group_sider_g_group_id_g">
                <div class="group_sider" id="button_g_group_id_g" style="width:150px;float:left;">
                    <span  id="group_name_g_group_id_g">g_group_name_g</span>
                </div>

                 <div type="button" style="height: 30px;float:right;padding-right: 15px;"  class="dropdown dropdownMenu2" style="height: 15px;"  id="group_g_group_id_g">
                       <span class="dropdown-toggle" type="button">
                           <i class="layui-icon layui-icon-more-vertical text-style" ></i>
                       </span>
                        <ul class="dropdown-menu dropdown-menu-right" style="min-width: 60px;padding:0;">
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;" onclick="group.edit(g_group_id_g)";>编辑</a></li>
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick="group.del(g_group_id_g);">删除</a></li>
                        </ul>
                   </div>
               </li>
        </div>

        <div id="add-master" style="display:none;" >
            <li class="add-form" id="add-master-header" style="text-align: center;">
                <img src="/images/webi/bi/icon-ador.png" alt="" style="padding-top: 30px;">
                <p style="padding-top: 7px;">新建图表</p>
            </li>
        </div>

        <div id="master-content" style="display:none;" >
            <li class="add-form" id="operationm_chart_id">
                <div type="button" class="edit_button">
                    <div class="title">m_chart_title</div>
                    <div class="dropdown pull-right dropdownMenu3">
                       <input type="hidden" id="bi_id" value="">
                       <span class="dropdown-toggle" type="button">
                            <i><img src="/images/webi/bi/icon-line.png" style="width:16px;height:13px;padding-right: 5px;padding-top: 6px;"></i>
                       </span>
                        <ul class="dropdown-menu">
                            <li><a onclick="master.edit('m_chart_id',1)";>编辑</a></li>
                            <li><a onclick="master.del('m_chart_id');">删除</a></li>
                        </ul>
                     </div>
               </div>

                <div class="master-img">
                    <a onclick="master.edit('m_chart_id',1)">
                      <img src="" title="点击可编辑报表" style="width:213.9px;height:135px;border-top: 1px #eee solid"/>
                    </a>
                </div>
            </li>
        </div>

        <div id="add_group_layer" style="display:none;">
           <form id="title-form-header" onsubmit="return false;" class="layui-form" role="form" >
                <div class="layui-form-item">
                        <label class="layui-form-label" for="title" style="padding-top: 8px;" ><span class="red pr5">*</span>&ensp;分组名称：</label>
                        <div class="layui-input-block" style="padding-left: 3px;">
                            <input type="text" class="layui-input" name="title" id="title-header" placeholder="请输入图表类型分组名称">
                            </div>
                       </div>
                    <div >
                    <div class="layui-form-item">
                        <label class="layui-form-label" for="group_code" style="padding-top: 13px;"><span class="red pr5">*</span>&ensp;类型代码：</label>
                        <div class="layui-input-block" style="padding-top: 5px;padding-left: 3px;">
                           <input type="text" class="layui-input" name="group_code" id="group_code-header" placeholder="请输入类型代码">
                            </div>
                        </div>
                    </div>
                </form>
        </div>

@endsection

@section('js')
    <script>
        var group_already = {!!$group_already!!};
        var code_already ={!!$code_already!!};
        var group_id =$("#group_id").val();
        var add_group_layer="";

        function create_table(group_id,data) {
            var master_html="";
            $.each(data, function (k, v) {
                master_html+=$('#master-content').html();
                master_html = master_html.replace(/m_chart_title/g,v.chart_title);
                master_html = master_html.replace(/m_chart_id/g,v._id);
                master_html = master_html.replace(/src=""/g,'src="'+v.photo_link+'"');

            });
            return master_html;
        }

        //移动单选框
        function create_move(data) {

            var  move_html  = '<form id="remove-form" onsubmit="return false;" class="layui-form" role="form" style="margin-top: 20px;">';
            move_html += '<div class="layui-form-item ">';
            $.each(data, function (k, v) {
                move_html +='<div class="layui-col-sm4" style="padding-left: 30px;">' ;
                move_html +='<div class="radio">';
                move_html += '<label>';
                move_html +='<input class="square-radio" id="group_type" name="group_id" type="radio" value="'+v._id+'">'+v.group_name+'&nbsp;&nbsp;';
                move_html += '</label>';
                move_html += '</div>';
                move_html += '</div>';
            })
            move_html += '</div>';
            move_html += '</form>';

            return  move_html;
        }

        var group={
            addGroup:function(){
                layer.open({
                    title: '新建分组',
                    offset: '70px',
                    type: 1,
                    area: ['600px', '45%'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: add_group_layer,
                    btn: ['确认','取消'],
                    yes: function () {
                        var dt = E.getFormValues('title-form');
                        var msg = '';

                        if(dt.title ==""){
                            msg += '请输入分组名称<br>';
                        }else {
                            if ($.inArray(dt.title, group_already) != -1) {
                                msg += '分组名称已存在<br>';
                            }
                        }

                        if(dt.group_code==""){
                            msg += '请输入类型代码!<br>';
                        }else {
                            if ($.inArray(dt.group_code, code_already) != -1) {
                                msg += '类型代码已存在<br>';
                            }
                        }

                        if (msg) {
                            layer.alert(msg, {icon: 2, offset: '70px'});
                            return false;
                        }

                        dt.type=1;
                        E.ajax({
                            type: 'post',
                            url: '/webi/backend/bi/chart/group/save',
                            data: dt,
                            success: function (res) {
                                if (res.code == 200) {

                                    layer.alert('保存成功', {icon: 1, offset: '70px', time: 1500});
                                        group_already.push(dt.title);
                                        code_already.push(dt.group_code);

                                        $("#title").val("");
                                        $("#group_code").val("");
                                        //清空input框，追加导航
                                        var nav=$("#hidden-group").html();
                                        nav= nav.replace(/g_group_id_g/g,res._id);
                                        nav= nav.replace(/g_group_name_g/g,dt.title);

                                        $("#group").append(nav);

                                        $('#button_'+res._id).on('click',function(){
                                            group.list(res._id);
                                        })

                                } else {
                                    layer.alert(res.message, {icon: 2, offset: '70px'});
                                }
                            }
                        });
                    }
                })
            },

            list:function(group_id){
                $("#group_sider_"+group_id).addClass("color").siblings().removeClass("color");
                $.ajax({
                    type: 'get',
                    url: '/webi/backend/bi/chart/get/'+group_id,
                    success: function (res) {
                        if(res.code==200){
                            $('.content-master').empty();
                            $("#group_id").val(group_id);

                            var eHtml=$("#add-master").html();
                            eHtml= eHtml.replace(/g_group_id_g/g,group_id);
                            $('.content-master').append( eHtml);

                            $('#add-master-header').on('click',function(){
                                master.edit(group_id,0);
                            });

                            if (!($.isEmptyObject(res.msg['master']))) {
                                var create =create_table(group_id,res.msg['master']);
                                $('.content-master').append(create);
                            }

                        }else{
                            layer.alert(res.msg, {icon: 2, offset: '70px'});
                        }

                    }
                });

            },

            //编辑分组名
            edit: function (group_id) {
                $.ajax({
                    type: 'get',
                    url: '/webi/backend/bi/chart/edit/'+group_id,
                    success: function (res) {
                        if (res.code == 200) {

                            layer.open({
                                title: '编辑分组',
                                offset: '70px',
                                type: 1,
                                area: ['500px', '45%'],
                                scrollbar: false,
                                closeBtn: 0,
                                content: add_group_layer,
                                btn: ['确认','取消'],
                                yes: function () {
                                    var dt = E.getFormValues('title-form');

                                    var msg = '';

                                    if(dt.title ==""){
                                        msg += '请输入分组名称<br>';
                                    }

                                    if(dt.group_code==""){
                                        msg += '请输入类型代码!<br>';
                                    }else{
                                        if ($.inArray(dt.group_code, code_already) != -1) {
                                            msg += '类型代码已存在<br>';
                                        }
                                    }

                                    if (msg) {
                                        layer.alert(msg, {icon: 2, offset: '70px'});
                                        return false;
                                    }

                                    dt.type=0;
                                    dt.s=group_id;
                                    E.ajax({
                                        type: 'post',
                                        url: '/webi/backend/bi/chart/group/save',
                                        data:dt,
                                        success: function (res) {
                                            if (res.code == 200) {
                                                //删除编辑前的分组名，追加编辑的分组名
                                                code_already.push(dt.group_code);
                                                group_already.push(dt.title);
                                                var title_name=$("#group_name_"+group_id).text();
                                                group_already.splice(jQuery.inArray(title_name, group_already),1);

                                                $("#group_name_"+group_id).html(dt.title);
                                                layer.alert('编辑成功', {icon: 1, offset: '70px', time: 1500});
                                                layer.closeAll();
                                            } else {
                                                layer.alert(res.message, {icon: 2, offset: '70px'});
                                            }
                                        }
                                    });
                                }
                            });
                            $("#title").val(res.message['group_name']);
                            $("#group_code").val(res.message['group_code']);
                            code_already.splice(jQuery.inArray(res.message['group_code'], code_already),1);
                        } else {
                            layer.alert(res.message, {icon: 2, offset: '70px'});
                        }
                    }
                });

            },

            //删除分组
            del: function (group_id) {
                //查询分组下是否有报表
                $.ajax({
                    type: 'get',
                    url: '/webi/backend/bi/chart/movelist/'+group_id,
                    success: function (res) {
                        if (res.code == 200) {

                            if(!($.isEmptyObject(res.message['group']))){
                                //遍历查询出的分组并显示在html中
                                var move_table=create_move(res.message['group']);

                                layer.open({
                                    title: '删除分组前移动图表',
                                    offset: '70px',
                                    type: 1,
                                    area: ['500px', '200px'],
                                    scrollbar: false,
                                    closeBtn: 0,
                                    content: move_table,
                                    btn: ['确认','取消'],
                                    yes: function () {
                                        var dt = E.getFormValues('remove-form');

                                        if($.isEmptyObject(dt.group_id)){
                                            layer.alert( '请选择要移动到的位置！' , { icon: 2,offset:'70px;'} ) ;
                                            return false;
                                        }

                                        E.ajax({
                                            type: 'post',
                                            url: '/webi/backend/bi/chart/groupDel',
                                            data:{
                                                group: dt.group_id,
                                                move_g:group_id
                                            },
                                            success: function (res) {
                                                if (res.code == 200) {

                                                    var title_name=$("#group_name_"+group_id).text();
                                                    group_already.splice(jQuery.inArray(title_name, group_already),1);
                                                    code_already.splice(jQuery.inArray(res.group_code, code_already),1);

                                                    $("#group_sider_"+group_id).remove();
                                                    layer.alert('删除成功', {icon: 1, offset: '70px', time: 1500});
                                                    layer.closeAll();
                                                    group.list(dt.group_id);
                                                } else {
                                                    layer.alert(res.message, {icon: 2, offset: '70px'});
                                                }
                                            }
                                        });
                                    }
                                });

                                layui.use('form', function() {
                                    var form = layui.form;
                                });
                            }
                        }else{//项目下无报表，执行删除
                            layer.confirm('是否确认删除?',{icon:3},function () {
                                $.ajax({
                                    type: 'post',
                                    url: '/webi/backend/bi/chart/groupDel',
                                    data:{
                                        group: "",
                                        move_g:group_id
                                    },
                                    success: function (res) {
                                        if (res.code == 200) {

                                            var title_name=$("#group_name_"+group_id).text();
                                            group_already.splice(jQuery.inArray(title_name, group_already),1);
                                            code_already.splice(jQuery.inArray(res.group_code, code_already),1);

                                            $("#group_sider_"+group_id).remove();
                                            $(".content-master").empty();

                                            layer.alert('删除成功', {icon: 1, offset: '70px', time: 1500});
                                            layer.closeAll();

                                        } else {
                                            layer.alert(res.message, {icon: 2, offset: '70px'});
                                        }
                                    }
                                });
                            });

                        }
                    }
                });


            }

        };

        var master={

            //新增或编辑
            edit:function (id,type) {

                layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: '/webi/backend/bi/chart/edit/' + id + '/'+type
                } );
            },

            del:function (chart_id) {
                layer.confirm('是否确认删除?',{icon:3},function () {
                    $.ajax({
                        type: 'get',
                        url: '/webi/backend/bi/chart/del/'+chart_id,
                        success: function (res) {
                            if (res.code == 200) {
                                $('#operation'+chart_id).remove();
                                layer.alert('删除成功', {icon: 1, offset: '70px', time: 1500});
                            } else {
                                layer.alert(res.message, {icon: 2, offset: '70px'});
                            }
                        }
                    });
                });

            }

        }

        //绑定分组查询事件
        $(document).ready(function() {
            $.each($('.group_sider'),function (k,v){
                $(this).on('click',function(){
                    var id=$(this).attr('id').substring(7);
                    group.list(id);
                })
            });

            $("#button_" + group_id).trigger("click");

            add_group_layer=$("#add_group_layer").html();
            add_group_layer=add_group_layer.replace(/title-form-header/g,"title-form");
            add_group_layer=add_group_layer.replace(/title-header/g,"title");
            add_group_layer=add_group_layer.replace(/group_code-header/g,"group_code");

        })

        $(document).on('click','.dropdownMenu1',function(){
            $(this).children(".dropdown-menu").slideToggle(100);
        }).on('click','.dropdownMenu2',function(){
            $(this).children(".dropdown-menu").slideToggle(100);
        }).on('click','.dropdownMenu3',function(){
            $(this).children(".dropdown-menu").slideToggle(100);
        });
    </script>

@endsection