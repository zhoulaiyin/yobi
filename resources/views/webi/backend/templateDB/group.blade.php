@extends('backend')

@section('title')
    模板分组
@endsection

@section('css')
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/webi/template/database/grouping"><span>模板分组</span></a>
                </li>
                <li>
                    <a href="/webi/template/database/list"><span>模板库</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        <button class="layui-btn btn-primary" type="button" onclick="stat.edit();">添加分组</button>
                    </div>

                    <div class="layui-col-md-10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">
                            <div class="input-group">
                                <input type="text" class="layui-input inline_b" id="trueName" name="trueName" placeholder="请输入分组名称">
                                <span class="input-group-btn">
                                        <button class="layui-btn btn-primary" onclick="stat.search();"  type="button">查询</button>
                                            <button class="layui-btn btn-warning" onclick="stat.reset();" type="button">重置</button>
                                        </span>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="layui-row">
            <div class="layui-col-lg-12">
                <table id="table" lay-filter="table"></table>
                <script type="text/html" id="barDemo">
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                </script>
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
            edit: function () {
                var html = '<form id="group-form" onsubmit="return false;" class="layui-form search_content" role="form">';

                html += '<div class="layui-form-item">';
                html += '<label class="layui-col-sm-3 layui-form-label" for="grouping"><span class="red pr5">*</span> 分组名称：</label>';
                html += '<div class="layui-col-sm-9 layui-input-block">';
                html += '<input class="layui-input" type="text" id="grouping" name="grouping"  value="" placeholder="请输入分组名称">';
                html += '</div>';
                html += '</div>';

                html += '</form>';

                var index = layer.open({
                        title: '添加模板分组',
                        type: 1,
                        offset: '50px',
                        area: '450px',
                        scrollbar: false,
                        content: html,
                        btn: ['保存', '取消'],
                        yes: function (index) {

                            var dt = E.getFormValues('group-form');

                            if(E.isEmpty(dt.grouping)){
                                layer.alert('分组名称不能为空',{icon:2,offset:'50px'});
                                return false;
                            }

                            E.ajax({
                                type: 'POST',
                                url: '/webi/template/database/s_grouping',
                                data: dt,
                                success: function (res) {
                                    if (res.code == 200) {
                                        layer.alert(res.message, {icon: 1, offset: '70px', time: 1500});
                                        layer.close(index);
                                    } else {
                                        layer.alert(res.message, {icon: 2, offset: '70px'});
                                        layer.close(index);
                                    }
                                }
                            });

                        },
                        end: function () {
                        stat.search();
                    }
                    });

            },
            del:function(user_id){
                layer.confirm('您确认要删除该分组吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);
                    E.ajax({
                        type: 'get',
                        url: '/webi/template/database/del_grouping/' + user_id,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.alert('分组删除成功', {icon: 1, offset: '70px', time: 1000});
                                stat.search();
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px'});
                            }
                        }
                    });
                });
            },
        };

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/template/database/g_search';
        //table插件//
        layui.use(['table','form'], function(){
            var table = layui.table;
            var form = layui.form;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                cols: [[
                    {fixed: 'left', width:'7%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'group_name', title: '分组名称',align:'left', width:'94%'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'del'){
                    stat.del(data._id);
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

