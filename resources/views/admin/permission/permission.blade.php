@extends('backend')

@section('title')
    权限列表
@endsection

@section('css')
    <style>
        .line .layui-form-select {
            display: inline-block;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/webi/backend/biuser/list"><span>权限列表</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-col-md1">
                    <button class="layui-btn btn-primary" type="button" onclick="Permission.edit(0);">添加</button>
                </div>
                <div class="layui-col-md11">
                    <form class="layui-form search_content" id="search-form" onsubmit="return false;"
                          style="padding:0;">

                        <div class="layui-form-item inline">
                            <select class="" name="parent_group_id" id="search_parent_group_id"
                                    lay-filter="searchParent">
                                <option value="">请选择分组</option>
                            </select>
                        </div>
                        <div class="layui-form-item inline">
                            <select class="" name="id" id="search_group_id" lay-filter="parentGroup">
                                <option value="">二级权限组</option>
                            </select>
                        </div>
                        <div class="layui-form-item inline">
                            <select class="" name="permission_type" id="permission_type">
                                <option value="">权限类型</option>
                                <option value="1">功能页面</option>
                                <option value="2">功能编辑</option>
                            </select>
                        </div>

                        <input type="text" class="layui-input inline" name="permission_id" id="permission_id"
                               placeholder="请输入权限ID">
                        <input type="text" class="layui-input inline" name="permission_name" id="permission_name"
                               placeholder="请输入权限名称">
                        <span class="input-group-btn fr">
                            <button class="layui-btn btn-primary" onclick="Permission.search();"
                                    type="button">查询</button>
                            <button class="layui-btn btn-warning" name="to-reset" onclick="Permission.reset();"
                                    type="button">重置</button>
                        </span>
                    </form>
                </div>
            </div>
        </div>
        <br>

        <div class="layui-row">
            <div class="layui-col-lg12">
                <table id="table" lay-filter="table"></table>
                <script type="text/html" id="barDemo">
                    <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                </script>
            </div>
        </div>

        <div id="layer-editTemplate" style="display: none;">
            <form class="layui-form" id="group-form" style="padding: 10px;">

                <div class="layui-form-item">
                    <label for="parent_id" class="layui-form-label">权限组：</label>
                    <div class="layui-input-block line">
                        <select id="pop_parent_group_id" name="parent_group_id" lay-filter="popParent">
                            <option value="0">请选择分组</option>
                        </select>
                        <select id="pop_group_id" name="group_id">
                            <option value="0">请选择分组</option>
                        </select>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="group_name" class="layui-form-label"><span class="red">*</span> 权限ID：</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input" id="pop_permission_id" name="permission_id"
                               placeholder="请输入权限ID">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="group_name" class="layui-form-label"><span class="red">*</span> 权限名称：</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input" id="pop_permission_name" name="permission_name"
                               placeholder="请输入权限名称">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="group_name" class="layui-form-label"><span class="red">*</span> 权限URL：</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input" id="pop_permission_url" name="permission_url"
                               placeholder="请输入权限URL">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="group_name" class="layui-form-label"><span class="red">*</span> 权限类型：</label>
                    <div class="layui-input-block">
                        <select class="" id="pop_permission_type" name="permission_type">
                            <option value="1">功能页面</option>
                            <option value="2">功能编辑</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" class="layui-input" id="_id" name="_id" value="0">
            </form>
        </div>

    </div>

@endsection

@section('js')
    <script>
        var permission_group = {!! $permission_group !!};
        var parent_permission_group = {!! $parent_permission_group !!};
        if (parent_permission_group) {
            $.each(parent_permission_group, function (i, n) {
                var html = '<option value="' + n._id + '" >' + n.group_name + '</option>';
                $('#search_parent_group_id').append(html);
            });
        }

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/admin/permission/search';

        layui.use(['table', 'form'], function () {
            var table = layui.table;
            var form = layui.form;

            tdom = table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                cols: [[
                    {fixed: 'operation', width: '12%', title: '操作', align: 'left', toolbar: '#barDemo'},
                    {field: 'permission_id', title: '权限ID', width: '10%', align: 'right'},
                    {field: 'permission_name', title: '权限名称', align: 'left', width: '15%'},
                    {field: 'group_name', title: '权限组名称', width: '15%', align: 'left'},
                    {field: 'permission_type_name', title: '权限类型', align: 'center', width: '10%'},
                    {field: 'permission_url', title: '权限URL', align: 'left', width: '40%'}
                ]]
            });
            table.on('tool(table)', function (obj) {
                var data = obj.data;
                var layEvent = obj.event;

                if (layEvent === 'del') {
                    Permission.del(data._id);
                } else if (layEvent === 'edit') {
                    Permission.edit(data._id);
                }
            });

            form.on('select(searchParent)', function (data) {
                var id = data.value;
                console.log(id);

                var html = '<option value="">二级权限组</option>';
                if (id != "" && permission_group[id]) {
                    console.log(permission_group[id]);
                    $.each(permission_group[id], function (k, v) {
                        html += '<option value="' + v._id + '">' + v.group_name + '</option>';
                    });
                }

                $('#search_group_id').html(html);

                form.render();
            });

            form.on('select(parentGroup)', function (data) {
                var id = data.value;
                var html = '<option value="">请选择分组</option>';

                if (id > 0 && permission_group[id]) {
                    $.each(permission_group[id], function (k, v) {
                        html += '<option value="' + v._id + '">' + v.group_name + '</option>';
                    });
                }

//                if (!group_id) {
//                    group_id = 0;
//                }

                $('#group_id').html(html);
//                $('#group_id').html(html).val(group_id);

                form.render();
            });

            form.on('select(popParent)', function (data) {
                var id = data.value;
                var html = '<option value="">请选择分组</option>';
                if (id > 0 && permission_group[id]) {
                    $.each(permission_group[id], function (k, v) {
                        html += '<option value="' + v._id + '">' + v.group_name + '</option>';
                    });
                }

//                if (!group_id) {
//                    group_id = 0;
//                }

//                $('#pop_group_id').html(html).val(group_id);
                $('#pop_group_id').html(html)

                form.render();
            });
        });

        var Permission = {
            search: function () {
                tdom.reload({
                    where: {
                        parent_group_id: $("#search_parent_group_id").val(),
                        id: $("#search_group_id").val(),
                        permission_type: $("#permission_type").val(),
                        permission_id: $("#permission_id").val(),
                        permission_name: $("#permission_name").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            reset: function () {
                $("#search_parent_group_id").val('');
                $("#search_group_id").val('');
                $("#permission_type").val('');
                $("#permission_id").val('');
                $("#permission_name").val('');
                Permission.search();
            },
            edit: function (id) {
                $('#pop_parent_group_id').empty();
                if (parent_permission_group) {
                    $.each(parent_permission_group, function (i, n) {
                        var html = '<option value="' + n._id + '" >' + n.group_name + '</option>';

                        $('#pop_parent_group_id').append(html);
                    });
                }
                layui.use('form', function () {
                    var form = layui.form;
                    form.render('select');
                });

                this.layer_index = layer.open({
                    title: id == 0 ? '添加权限' : '修改权限',
                    type: 1,
                    offset: '50px',
                    area: ['660px', '400px'],
                    scrollbar: false,
                    content: $("#layer-editTemplate"),
                    btn: ['保存', '取消'],
                    success: function () {
                        if (id != 0) {
                            E.ajax({
                                type: 'GET',
                                url: '/admin/permission/get/' + id,
                                success: function (res) {
                                    if (res.code == 200) {
                                        $('#_id').val(res.data._id);
                                        $('#pop_permission_id').val(res.data.permission_id);
                                        $('#pop_parent_group_id').val(res.data.parent_group_id).trigger('change', res.data.group_id);
                                        $('#pop_permission_name').val(res.data.permission_name);
                                        $('#pop_permission_url').val(res.data.permission_url);
                                        $('#pop_permission_type').val(res.data.permission_type);

                                        layui.use('form', function () {
                                            var form = layui.form;
                                            form.render('select');
                                        });
                                    } else {
                                        layer.close();
                                        layer.msg(res.message, {icon: 2});
                                    }
                                }
                            });
                        }
                    },
                    yes: function () {

                        var dt = E.getFormValues('group-form');

                        if (!E.isDigital(dt.permission_id)) {
                            layer.alert('请选择权限ID', {icon: 2});
                            return false;
                        } else if (dt.parent_group_id == 0 || dt.group_id == 0) {
                            layer.alert('请选择权限组', {icon: 2});
                            return false;
                        } else if (E.isEmpty(dt.permission_name)) {
                            layer.alert('请输入权限名称', {icon: 2});
                            return false;
                        } else if (E.isEmpty(dt.permission_url)) {
                            layer.alert('请输入权限URL', {icon: 2});
                            return false;
                        }

                        var load_index = layer.load();
                        E.ajax({
                            type: 'POST',
                            url: '/admin/permission/store',
                            data: dt,
                            success: function (res) {
                                layer.close(load_index);
                                if (res.code == 200) {
                                    layer.alert('权限保存成功', {icon: 1});
                                    if (id == 0) {
                                        $('#pop_permission_id').val('');
                                        $('#pop_permission_name').val('');
                                        $('#pop_permission_url').val('');
                                    } else {
                                        layer.close();
                                    }
                                    Permission.search();
                                } else {
                                    layer.msg(res.message, {icon: 2});
                                }
                            }
                        });

                    }
                });
            },
            del: function (id) {

                layer.confirm('您确认要删该权限吗？', {icon: 3}, function (index) {
                    layer.close(index);
                    var load_index = layer.load();
                    E.ajax({
                        type: 'GET',
                        url: '/admin/permission/delete/' + id,
                        success: function (res) {
                            layer.close(load_index);
                            if (res.code == 200) {
                                layer.alert('权限删除成功', {icon: 1});
                                Permission.search();
                            } else {
                                layer.alert(res.message, {icon: 2});
                            }
                        }
                    });
                });

            }
        };
    </script>
@endsection