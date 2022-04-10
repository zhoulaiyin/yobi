@extends('backend')

@section('title')
    文档分组列表
@endsection

@section('css')
    <style>
        #layui-layer1 .layui-layer-setwin .layui-layer-close1{
            background-position: 1px -40px;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/webi/doc/group/list"><span>分组管理</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        <button class="layui-btn btn-primary" type="button" onclick="stat.edit(0);">添加分组</button>
                    </div>

                    <div class="layui-col-md10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">

                            <div class="input-group layui-form-item">
                                <input type="text" class="layui-input" id="group_name" name="group_name" style="width: 150px;margin-left: 10px;" placeholder="请输入分组名称">
                                <span class="input-group-btn fr">
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
            <div class="layui-col-lg12">
                <table id="table" lay-filter="table"></table>
                <script type="text/html" id="barDemo">
                    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="task">项目</a>
                </script>
            </div>
        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">
        var tdom = '';
        var count = 1;
        var stat = {
            //查询
            search:function(){
                tdom.reload({
                    where: {
                        group_name: $("#group_name").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            //重置
            reset:function () {
                $("#group_name").val('');
                stat.search();
            },
            edit: function ( user_id ) {

                var content = '/webi/doc/group/edit/' + user_id ;

                layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: content,
                    end: function () {
                        stat.search();
                    }
                } );
            },
            task: function ( user_id ) {

                var content = '/webi/doc/group/item/list/' + user_id ;

                layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: content
                } );
            },
            del:function(user_id){
                layer.confirm('您确认要删除该分组吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);
                    E.ajax({
                        type: 'get',
                        url: '/webi/doc/group/del/' + user_id,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.alert('删除成功', {icon: 1, offset: '70px', time: 1000});
                                stat.search();
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px'});
                            }
                        }
                    });
                });
            }
        };

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/doc/group/list/search';
        layui.use(['table','form'], function(){
            var table = layui.table;
            var form = layui.form;

            tdom = table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                done: function(){
                    if(count==1){
                        count=0;
                        stat.search();
                    }
                },
                cols: [[
                    {fixed: 'left', width:'18%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'sort_order', title: '排序值', width:'10%', sort: true, align:'right'},
                    {field: 'group_name', title: '分组名称',align:'left'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'edit'){
                    stat.edit(data._id);
                } else if(layEvent === 'task'){
                    stat.task(data._id);
                }else if(layEvent === 'del'){
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

