@extends('backend')

@section('title')
    权限组
@endsection

@section('content')

    <div class="app-content">

        <div class="app-third-sidebar">
            <nav class="ui-nav" style="display: block;">
                <ul>
                    <li class="active">
                        <a href="/admin/permission/group"><span>权限组列表</span></a>
                    </li>
                </ul>
            </nav>
        </div>

        <div id="wrapper">

            <div class="layui-row">
                <div class="layui-col-lg12">
                    <div class="layui-row">
                        <div class="layui-col-md3">
                            <button class="layui-btn btn-primary" type="button" onclick="PermissionGroup.edit(0);">添加</button>
                        </div>
                        <div class="layui-col-md9">
                            <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">

                                <div class="layui-form-item" style="margin-left: 5px;width: 200px;">
                                    <select class="inline" style="width: 200px;margin-left: 10px;" id="parent_group_id" name="parent_group_id">
                                        <option value="">请选择分组</option>
                                    </select>
                                </div>
                                <div class="input-group fr" style="margin-left: 10px;">
                                    <input type="text" class="layui-input" name="group_name" id="group_name" placeholder="请输入权限组名称">
                                    <span class="input-group-btn fr">
                                        <button class="layui-btn btn-primary" onclick="PermissionGroup.search();"  type="button">查询</button>
                                        <button class="layui-btn btn-warning" name="to-reset" onclick="PermissionGroup.reset();" type="button">重置</button>
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
                    <script type="text/html" id="barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                    </script>
                </div>
            </div>

        </div>


        {{--新增编辑弹窗--}}
        <div id="layer-editTemplate" style="display: none">

            <form class="layui-form" id="group-form" style="padding: 10px;">
                <div class="layui-form-item">
                    <label for="parent_id" class="layui-form-label">上级分组：</label>
                    <div class="layui-input-block">
                        <select class="" id="parent_id" name="parent_id" lay-filter="parentId">
                            <option value="0">请选择分组</option>
                        </select>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="group_name" class="layui-form-label"><span class="red">*</span> 分组名称：</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input" id="pop_group_name" name="group_name" placeholder="请输入分组名称">
                    </div>
                </div>

                <div class="layui-form-item" id="permission_prefix_box">
                    <label for="group_name" class="layui-form-label"><span class="red">*</span> 权限前缀：</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input" id="permission_prefix" name="permission_prefix" placeholder="请输入权限前缀">
                    </div>
                </div>

                <input type="hidden" class="layui-input" id="_id" name="_id" value="0">
            </form>

        </div>

    </div>
@endsection

@section('js')
    <script>
        var permission_group = {!! $permission_group !!}
        var tdom = '';
        var edit_template="";
        if ( permission_group ) {
            $.each( permission_group , function(i,n){
                var  html = '<option value="'+ n._id + '" >'+ n.group_name + '</option>';
                $('#parent_group_id').append(html);
            });

        }
        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/admin/permission/group/search';
        layui.use(['table','form'], function(){
            var table = layui.table;
            var form = layui.form;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                cols: [[
                    {fixed: 'operation', width:'11%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'id', title: '权限组ID', width:'20%',align:'right'},
                    {field: 'group_name', title: '权限组名称', width:'16%',align:'left'},
                    {field: 'parent_group_name', title: '上级权限组名称', width:'25%',align:'left'},
                    {field: 'permission_prefix', title: '权限前缀', width:'17%',align:'right'},
                    {field: 'permission_num', title: '权限数量', width:'12%',align:'right'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'del'){
                    PermissionGroup.del(data._id);
                } else if(layEvent === 'edit'){
                    PermissionGroup.edit(data._id);
                }
            });

            form.on('select(parentId)', function(data){
                if (data.value == 0) {
                    $('#permission_prefix_box').hide();
                } else {
                    $('#permission_prefix_box').show();
                }

                form.render();
            });
        });


        var PermissionGroup = {
            search:function(){
                tdom.reload({
                    where: {
                        group_name: $("#group_name").val(),
                        id: $("#parent_group_id").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            reset:function () {
                $("#parent_group_id").val('');
                $("#group_name").val('');
                PermissionGroup.search();
            },
            edit: function (id) {
                $('#parent_id').empty();
                if ( permission_group ) {
                    $.each( permission_group , function(i,n){
                        var html = '<option value="'+ n._id + '" >'+ n.group_name + '</option>';

                        $('#parent_id').append(html);
                    });
                }

                $("#parent_id").val('0');
                $("#pop_group_name").val('');
                $("#permission_prefix").val('');
                $("#_id").val('0');

                layui.use('form', function(){
                    var form = layui.form;
                    form.render('select');
                });

                this.layer_index = layer.open({
                    title: id == 0 ? '添加权限组' : '修改权限组',
                    type: 1,
                    offset: '50px',
                    area: ['600px', '400px'] ,
                    scrollbar: false,
                    content: $("#layer-editTemplate"),
                    btn: ['保存', '取消'],
                    success:function(){
                        if( id == 0 ){
                            return false;
                        }

                        E.ajax({
                            type: 'GET',
                            url: '/admin/permission/group/' + id,
                            success: function (res) {

                                if( res.code != 200 ){
                                    layer.close(PermissionGroup.search());
                                    layer.msg(res.message, {icon: 2});
                                    return false;
                                }

                                $('#_id').val(res.data._id);
                                $('#pop_group_name').val(res.data.group_name);
                                $('#parent_id').val(res.data.parent_id);
                                $('#permission_prefix').val(res.data.permission_prefix);

                                layui.use('form', function(){
                                    var form = layui.form;
                                    form.render('select');
                                });

                            }
                        });
                    },
                    yes: function () {

                        var dt = E.getFormValues('group-form');

                        if (E.isEmpty(dt.group_name)) {
                            layer.msg('请输入分组名称', {icon: 2});
                            return false;
                        } else if (!E.isDigital(dt.permission_prefix)) {
                            layer.msg('请输入权限前缀', {icon: 2});
                            return false;
                        }

                        var load_index = layer.load();
                        E.ajax({
                            type: 'POST',
                            url: '/admin/permission/group/store',
                            data: dt,
                            success: function (res) {
                                layer.close(load_index);

                                if( res.code != 200 ){
                                    layer.msg(res.message, {icon: 2});
                                    return false;
                                }
                                layer.close(PermissionGroup.layer_index);
                                layer.msg('保存成功', {icon: 1,time:2000}, PermissionGroup.search);
                            }
                        });

                    }
                });
            },
            del: function (id) {

                layer.confirm('您确认要删该权限组吗？', {icon: 3}, function (index) {
                    layer.close(index);
                    var load_index = layer.load();
                    E.ajax({
                        type: 'GET',
                        url: '/admin/permission/group/delete/' + id,
                        success: function (res) {
                            layer.close(load_index);
                            if( res.code != 200 ){
                                layer.alert(res.message, {icon: 2});
                                return false;
                            }
                            layer.msg('删除成功', {icon: 1, time:2000}, PermissionGroup.search);
                        }
                    });
                });

            }
        };

    </script>
@endsection