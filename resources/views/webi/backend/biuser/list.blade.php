@extends('backend')

@section('title')
    用户管理
@endsection

@section('css')
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/webi/backend/biuser/list"><span>用户管理</span></a>
                </li>

            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        <button class="layui-btn btn-primary" type="button" onclick="stat.edit(0);">添加用户</button>
                    </div>

                    <div class="layui-col-md10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">
                            <div class="layui-form-item" style="margin-left: 5px;width: 200px;">
                                <select class="layui-select reset-bootstrap-select" style="width: 200px;margin-left: 10px;" id="project_id" name="p_id">
                                    <option value="">请选择项目</option>
                                </select>
                            </div>

                            <div class="layui-form-item fr" style="margin-left:10px;">
                                <input type="text" class="layui-input" id="trueName" name="trueName" placeholder="请输入用户名称">
                                <span class="input-group-btn">
                                        <button class="layui-btn btn-primary" onclick="stat.search();"  type="button">查询</button>
                                            <button class="layui-btn layui-btn-primary" name="to-reset" onclick="stat.reset();" type="button">重置</button>
                                </span>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-lg-12">
                <table id="table" lay-filter="table"></table>
                <script type="text/html" id="barDemo">
                    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                    <a class="layui-btn btn-success layui-btn-xs" lay-event="update">修改密码</a>
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                </script>
            </div>
        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">
        var project_data = {!! $project_data !!}
        var tdom = '';
        if ( project_data ) {

            $.each( project_data , function(i,n){
                var  html = '<option value="'+ n.project_id + '" >'+ n.project_name + '</option>';
                $('#project_id').append(html);
            });

        }

        var stat = {
            search:function(){
                tdom.reload({
                    where: {
                        trueName: $("#trueName").val(),
                        project_id: $("#project_id").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            //重置
            reset:function () {
                $("#project_id").val('');
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
                    content: '/webi/backend/biuser/edit/' + user_id ,
                    end: function () {
                        stat.search();
                    }
                } );

            },
            update: function (user_id) {

                var html = '<form id="user-form" onsubmit="return false;" class="layui-form" role="form">';

                html += '<input type="hidden" name="user_id" id="user_id" value="' + user_id + '">';

                html += '<div class="layui-form-item">';
                html += '<label class="layui-form-label" for="userID"><span class="red pr5">*</span> 原密码：</label>';
                html += '<div class="layui-input-block">';
                html += '<input class="layui-input" type="password" id="pwd" name="pwd" maxlength="20" placeholder="请输入原密码">';
                html += '</div>';
                html += '</div>';

                html += '<div class="layui-form-item">';
                html += '<label class="layui-form-label" for="trueName"><span class="red pr5">*</span> 新密码：</label>';
                html += '<div class="layui-input-block">';
                html += '<input class="layui-input" type="password" id="user_pwd" name="user_pwd" maxlength="20" placeholder="请输入新密码">';
                html += '</div>';
                html += '</div>';

                html += '<div class="layui-form-item">';
                html += '<label class="layui-form-label" for="email"><span class="red pr5">*</span> 确认密码：</label>';
                html += '<div class="layui-input-block">';
                html += '<input class="layui-input" type="password" id="user_pwds" name="user_pwds" maxlength="30" value="" placeholder="请确认密码">';
                html += '</div>';
                html += '</div>';

                html += '</form>';

                layer.open({
                    title: '修改密码',
                    type: 1,
                    offset: '50px',
                    area: ['450px', '410px'],
                    scrollbar: false,
                    content: html,
                    btn: ['保存', '取消'],
                    yes: function (index) {
                        var dt = E.getFormValues('user-form');

                        //验证单
                        if (E.isEmpty(dt.pwd)) {
                            layer.alert('请输入原密码', {icon: 2, offset: '70px'});
                            return false;
                        }
                        if (E.isEmpty(dt.user_pwd)) {
                            layer.alert('请输入密码', {icon: 2, offset: '70px'});
                            return false;
                        }

                        if (E.isEmpty(dt.user_pwds)) {
                            layer.alert('请确认密码', {icon: 2, offset: '70px'});
                            return false;
                        }

                        if (dt.user_pwd!=dt.user_pwds) {
                            layer.alert('两次密码输入不一致', {icon: 2, offset: '70px'});
                            return false;
                        }
                        E.ajax({
                            type: 'POST',
                            url: '/webi/backend/biuser/edit/editPwd',
                            data: dt,
                            success: function (res) {
                                if (res.code == 200) {
                                    layer.alert('密码修改成功', {icon: 1, offset: '70px', time: 1500});
                                    if (user_id) {
                                        layer.close(index);
                                    } else {
                                        $('#pwd').val('');
                                        $('#user_pwd').val('');
                                        $('#user_pwds').val('');
                                    }
                                    stat.search();
                                } else {
                                    layer.alert(res.message, {icon: 2, offset: '70px'});
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
                        url: '/webi/backend/biuser/del/' + user_id ,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.alert('用户删除成功', {icon: 1, offset: '70px', time: 1000});
                                $('#table').bootstrapTable('refresh');
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px'});
                            }
                        }
                    });
                });
            },
        };

        layui.use(['table','form'], function(){
            var table = layui.table;
            var form = layui.form;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: '/webi/backend/biuser/list/search',
                page: true, //开启分页
                cols: [[
                    {fixed: 'left', width:'20%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'project_name', title: '项目名称', width:'15%',align:'left'},
                    {field: 'user_id', title: '用户名',align:'left', width:'12%',align:'left'},
                    {field: 'true_name', title: '姓名',align:'left', width:'12%',align:'left'},
                    {field: 'email', title: '邮箱', width:'25%',align:'left'},
                    {field: 'mobile', title: '手机', width:'17%',align:'center'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'del'){
                    stat.del(data._id);
                } else if(layEvent === 'edit'){
                    stat.edit(data._id);
                } else if(layEvent === 'update'){
                    stat.update(data._id);
                }
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

