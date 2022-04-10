@extends('backend')

@section('title')
    微电汇数据源维护
@endsection

@section('css')

    <style>
        .nav-g {
            float: left;
            width: 18%;
            height: 700px;
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
            font-size: 16px;
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
            text-align: center;
        }
        .content{
            min-height: 500px;
            float: right;
            width: 82%;
            background-color: #fff;
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
        }
        .add-form{
            float: left;
            margin-top: 12px;
            margin-left: 9px;
            margin-right: 3.9px;
            width: 204px;
            height: 160px;
            box-shadow: 0 0 3px 3px rgba(0,0,0,.05);
            font-size: 12px;
        }
        a:hover{
            text-decoration:none;
        }
        .group_sider {
            height: 40px;
        }
        .table-name {
            width: 100%;
            text-align: center;
        }
        .btn-circle.btn-xl {
            width: 100px;
            height: 100px;
            padding: 30px 16px;
            margin: 10px 57px 10px;
            font-size: 34px;
            line-height: 1.33;
            border-radius: 50px;
        }
    </style>
@endsection

@section('content')

    {{--三级导航--}}
    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li id="active_1">
                    <a href="/webi/backend/dataset/index/1"><span>数据源维护</span></a>
                </li>
            </ul>
        </nav>
    </div>


    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        <button class="layui-btn btn-primary" type="button" onclick="master.edit(0);">添加数据集</button>
                    </div>

                    <div class="layui-col-md10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">

                            <div class="layui-form-item">
                                <input type="text" class="layui-input" id="description" name="description" placeholder="请输入数据集名称">
                                <span class="input-group-btn">
                                        <button class="layui-btn btn-primary" onclick="stat.search();"  type="button">查询</button>
                                            <button class="layui-btn btn-warning" name="to-reset" onclick="stat.reset();" type="button">重置</button>
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
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                </script>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>

        var stat = {

            search:function() {
                tdom.reload({
                    where: {
                        description: $("#description").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            reset:function () {
                $("#description").val('');
                stat.search();
            }
        };

        var bootstrap_table_ajax_url = '/webi/backend/dataset/get';
        layui.use('table', function(){
            var table = layui.table;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                cols: [[
                    {fixed: 'left', width:'12%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'description', title: '数据集',align:'left'},
                    {field: 'table_name', title: '统计表名称',align:'left'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'del'){
                    master.del(data._id);
                } else if(layEvent === 'edit'){
                    master.edit(data._id);
                }
            });
        });


        var master={

            //新增或编辑
            edit:function (id) {

                layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: '/webi/backend/dataset/edit/' + id
                } );
            },

            del:function (chart_id) {
                layer.confirm('是否确认删除?',{icon:3},function () {
                    $.ajax({
                        type: 'get',
                        url: '/webi/backend/dataset/del/'+chart_id,
                        success: function (res) {
                            stat.search();
                            if (res.code == 200) {
                                layer.msg('删除成功', {icon: 1, offset: '70px', time: 1500});
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                            }
                        }
                    });
                });

            }

        };

    </script>

@endsection