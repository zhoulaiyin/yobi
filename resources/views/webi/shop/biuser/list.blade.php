@extends('webi.layout')

@section('title')
    用户管理
@endsection

@section('css')
    <style>
        .layui-input,.input-group-btn{
            display: inline-block;
        }
        .layui-input{
            width:220px;
        }
        .input-group-btn,.layui-btn+.layui-btn{
            margin-left:5px!important;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/webi/design/biuser/list"><span>用户管理</span></a>
                </li>
            </ul>
        </nav>
    </div>
    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        @if($is_admin)
                            <button class="layui-btn layui-btn-normal" type="button" onclick="stat.edit(0);">添加用户</button>
                        @endif
                    </div>

                    <div class="layui-col-md10" style="float:right;">
                        <form class="layui-form" id="search-form" onsubmit="return false;" style="float:right;">

                            <div class="layui-form-item">
                                <input type="text" class="layui-input" id="trueName" name="trueName" placeholder="请输入用户名称">
                                <span class="input-group-btn">
                                    <button class="layui-btn layui-btn-normal" onclick="stat.search();"  type="button">查询</button>
                                    <button class="layui-btn layui-btn-primary" onclick="stat.reset();" type="button">重置</button>
                                </span>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="layui-row">
            <div class="layui-col-lg12">
                <table id="table" lay-filter="table"></table>
            </div>
        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">
        var tdom = '';

        var stat = {
            search:function(){
                tdom.reload({
                    where: {
                        trueName: $("#trueName").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            //重置
            reset:function () {
                $("#trueName").val('');
                stat.search();
            },
            edit: function ( user_id ) {

                layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: '/webi/design/biuser/edit/' + user_id ,
                    end: function () {
                        stat.search();
                    }
                } );

            },
            update: function (user_id) {

                var html = '<form id="user-form" onsubmit="return false;" class="form-horizontal" role="form">';

                html += '<input type="hidden" name="user_id" id="user_id" value="' + user_id + '">';

                html += '<div class="form-group">';
                html += '<label class="layui-col-sm3 control-label" for="userID"><span class="red pr5">*</span> 原密码：</label>';
                html += '<div class="layui-col-sm9">';
                html += '<input class="form-control" type="password" id="pwd" name="pwd" maxlength="20" placeholder="请输入原密码">';
                html += '</div>';
                html += '</div>';

                html += '<div class="form-group">';
                html += '<label class="layui-col-sm3 control-label" for="trueName"><span class="red pr5">*</span> 新密码：</label>';
                html += '<div class="layui-col-sm9">';
                html += '<input class="form-control" type="password" id="user_pwd" name="user_pwd" maxlength="20" placeholder="请输入新密码">';
                html += '</div>';
                html += '</div>';

                html += '<div class="form-group">';
                html += '<label class="layui-col-sm3 control-label" for="email"><span class="red pr5">*</span> 确认密码：</label>';
                html += '<div class="layui-col-sm9">';
                html += '<input class="form-control" type="password" id="user_pwds" name="user_pwds" maxlength="30" value="" placeholder="请确认密码">';
                html += '</div>';
                html += '</div>';

                html += '</form>';

                layer.open({
                    title: '修改密码',
                    type: 1,
                    offset: '50px',
                    area: ['450px', '300px'],
                    scrollbar: false,
                    content: html,
                    btn: ['保存', '取消'],
                    yes: function (index) {
                        var dt = E.getFormValues('user-form');

                        //验证单
                        if (E.isEmpty(dt.pwd)) {
                            layer.msg('请输入原密码', {icon: 2, offset: '70px'});
                            return false;
                        }
                        if (E.isEmpty(dt.user_pwd)) {
                            layer.msg('请输入密码', {icon: 2, offset: '70px'});
                            return false;
                        }

                        if (E.isEmpty(dt.user_pwds)) {
                            layer.msg('请确认密码', {icon: 2, offset: '70px'});
                            return false;
                        }

                        if (dt.user_pwd!=dt.user_pwds) {
                            layer.msg('两次密码输入不一致', {icon: 2, offset: '70px'});
                            return false;
                        }
                        E.ajax({
                            type: 'POST',
                            url: '/webi/design/biuser/edit/editPwd',
                            data: dt,
                            success: function (res) {
                                if (res.code == 200) {
                                    layer.msg('密码修改成功', {icon: 1, offset: '70px', time: 1500});
                                    if (user_id) {
                                        layer.close(index);
                                    } else {
                                        $('#pwd').val('');
                                        $('#user_pwd').val('');
                                        $('#user_pwds').val('');
                                    }
                                    stat.search();
                                } else {
                                    layer.msg(res.message, {icon: 2, offset: '70px'});
                                }
                            }
                        });

                    }
                });
            },
            del:function(user_id){
                layer.confirm('您确认要删除该用户吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);
                    E.ajax({
                        type: 'get',
                        url: '/webi/design/biuser/del/' + user_id ,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.msg('用户删除成功', {icon: 1, offset: '70px', time: 1000});
                                stat.search();
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px'});
                            }
                        }
                    });
                });
            }
        };

        layui.use(['table','form'], function(){
            var table = layui.table;
            var form = layui.form;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: '/webi/design/biuser/list/search',
                page: true, //开启分页
                cols: [[
                    {field: 'operation', width:'20%',title:'操作',align:'center'},
                    {field: 'user_id', title: '用户名',align:'left', width:'15%'},
                    {field: 'true_name', title: '姓名',align:'left', width:'15%'},
                    {field: 'role_id', title: '账号类型',align:'left', width:'10%'},
                    {field: 'role_bind', title: '绑定门店',align:'left', width:'15%'},
                    {field: 'email', title: '邮箱', width:'15%'},
                    {field: 'mobile', title: '手机', width:'15%'}
                ]]
            });

        });

        $(document).on('click', ".ui-nav li", function ( ) {

            var $this = $(this).attr('id') ;

            //添加属性class
            var $class = $('#'+$this) ;
            $class.addClass('active') ; //添加
            $class.siblings().removeClass('active') ; //其他同胞移除

        });

    </script>
@endsection

