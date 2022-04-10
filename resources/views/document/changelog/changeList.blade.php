@extends('backend')

@section('title')
    变更日志列表
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
                    <a href="/webi/doc/changelog/list"><span>变更日志</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        <button class="layui-btn btn-primary" type="button" onclick="stat.edit(0);">添加记录</button>
                    </div>

                    <div class="layui-col-md10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">

                            <div class="layui-form-item">
                                <input type="text" class="layui-input" id="v" name="v" style="width: 150px;margin-left: 10px;" placeholder="请输入版本号">
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
            <div class="layui-col-lg12">
                <table id="table" lay-filter="table"></table>
                <script type="text/html" id="barDemo">
                    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                </script>
            </div>
        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">
        //
        var tdom = '';
        var count = 1;
        var stat = {
            //查询
            search:function(){
                tdom.reload({
                    where: {
                        v: $("#v").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            //重置
            reset:function () {
                $("#v").val('');
                stat.search();
            },
            edit: function ( user_id ) {

                var content = '/webi/doc/changelog/edit/' + user_id ;

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
            }
        };

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/doc/changelog/list/search';
        layui.use('table', function(){
            var table = layui.table;

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
                    {fixed: 'left', width:'7%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'log_id', title: 'ID', width:'28%', sort: true,align:'right'},
                    {field: 'v', title: '版本号', align:'right'},
                    {field: 'editor', title: '操作人', align:'left'},
                    {field: 'change_date', title: '更新时间', width:'25%',align:'center'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'edit'){
                    stat.edit(data._id);
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

