@extends('backend')

@section('title')
    项目详情列表
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
                    <a href="javascript: void(0);"><span>项目详情列表</span></a>
                </li>
            </ul>
            <div class="top-back">
                <button class="layui-btn layui-btn-primary layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div class="layui-col-md2">
                        <button class="layui-btn btn-primary" type="button" onclick="stat.add();">添加项目详情</button>
                    </div>

                    <div class="layui-col-md10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">
                            <div class="layui-form-item">
                                <input type="text" class="layui-input" id="item_name" name="item_name" style="width: 150px;margin-left: 10px;" placeholder="请输入项目名称">
                            </div>

                            <div class="input-group fr">
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
            layer_index: 0,
            //查询
            search: function () {
                tdom.reload({
                    where: {
                        item_name: $("#item_name").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            //重置
            reset: function () {
                $("#item_name").val('');
                stat.search();
            },
            add: function () {
                var content = '/webi/doc/group/item/add';

                layer.open({
                    title: false,
                    type: 2,
                    area: ['100%', '100%'],
                    scrollbar: false,
                    offset: '0px',
                    closeBtn: 0,
                    content: content,
                    end: function () {
                        stat.search();
                    }
                });
            },
            edit: function (user_id) {
                var content = '/webi/doc/group/item/edit/' + user_id;

                layer.open({
                    title: false,
                    type: 2,
                    area: ['100%', '100%'],
                    scrollbar: false,
                    offset: '0px',
                    closeBtn: 0,
                    content: content,
                    end: function () {
                        stat.search();
                    }
                });
            },
            openOrder: function (percent,item_id) {

                this.item_id = item_id;

                var html = '<div class="layui-form" style="margin-top: 15px;">' ;

                html += '<div class="layui-form-item">';
                html += '<div class="layui-col-sm12">';
                html += '<input type="text" id="sort_order" class="layui-input" value='+percent+' >';
                html += '</div>';
                html += '</div>';

                html += '<div class="layui-form-item">';
                html += '<div class="layui-col-sm12">';
                html += '<input type="button" class="layui-btn btn-default"  onclick="stat.alterDate(0);" value="关闭"/>';
                html += '<input type="button" class="layui-btn btn-success"  onclick="stat.alterDate(1);" value="保存" style="margin-left:12px;"/>';
                html += '</div>';
                html += '</div>';

                html += '</div>';

                var id = '#sort_order_';

                this.layer_index = layer.tips(html, id + item_id, {
                    tips: [4, '#fff'],
                    time: 0,
                    success: function () {
                        $("#sort_order").keyup(function(){
                            var c=$(this);
                            if(/[^\d]/.test(c.val())){
                                var temp_amount=c.val().replace(/[^\d]/g,'');
                                $(this).val(temp_amount);
                            }
                        });
                    }
                });

            },
            del:function(user_id){
                layer.confirm('您确认要删除该项目详情吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);
                    E.ajax({
                        type: 'get',
                        url: '/webi/doc/group/item/del/' + user_id,
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
            },
            alterDate: function (action) {

                if (action) {
                    if($('#sort_order').val()){
                        var sort_order = $('#sort_order').val();
                    }

                    var load = layer.load();
                    E.ajax({
                        type: 'POST',
                        url: '/webi/doc/group/item/list/save',
                        data: {
                            item_id: this.item_id,
                            sort_order: sort_order
                        },
                        success: function (res) {
                            layer.close( load );
                            if ( res.code == 200 ) {
                                layer.alert( res.message, { icon: 1 ,time:1500 } );
                                layer.close( stat.layer_index );
                                stat.search();
                            } else {
                                layer.alert( res.message, { icon: 2 } );
                            }
                        }

                    });
                } else {
                    layer.close( this.layer_index );
                }
            }
        }
        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/doc/group/item/search';
        //table插件
        layui.use('table', function(){
            var table = layui.table;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页72
                cols: [[
                    {fixed: 'left', width:'18%' ,title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'sort_order', title: '排序值', width:'10%', sort: true,align:'right'},
                    {field: 'group_name', title: '分组名称', width:'20%',align:'left'},
                    {field: 'item_name', title: '项目名称', width:'20%',align:'left'},
                    {field: 'item_link', title: '项目链接',align:'left'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'edit'){
                    stat.edit(data._id);
                } else if(layEvent === 'del'){
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

