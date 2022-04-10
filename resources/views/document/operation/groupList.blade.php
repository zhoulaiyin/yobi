@extends('backend')

@section('title')
    操作文档列表
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
                    <a href="/webi/doc/operation/list"><span>分组管理</span></a>
                </li>
                <li>
                    <a href="/webi/doc/operation/item/list"><span>项目详情</span></a>
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

                    <div class="layui-col-md-10" >
                        <form class="form-inline search_content fr" id="search-form" onsubmit="return false;">

                            <div class="input-group">
                                <input type="text" class="layui-input" id="group_name" name="group_name" style="width: 150px;margin-left: 10px;" placeholder="请输入分组名称">
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

                var content = '/webi/doc/operation/edit/' + user_id ;

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
            openOrder: function (percent,group_id) {

                this.group_id = group_id;

                var html = '<div class="form-horizontal" style="margin-top: 15px;">' ;

                html += '<div class="form-group">';
                html += '<div class="col-sm-12">';
                html += '<input type="text" id="sort_order" class="form-control" value='+percent+' >';
                html += '</div>';
                html += '</div>';

                html += '<div class="form-group">';
                html += '<div class="col-sm-12">';
                html += '<input type="button" class="btn btn-default"  onclick="stat.alterDate(0);" value="关闭"/>';
                html += '<input type="button" class="btn btn-success"  onclick="stat.alterDate(1);" value="保存" style="margin-left:12px;"/>';
                html += '</div>';
                html += '</div>';

                html += '</div>';

                var id = '#sort_order_';

                this.layer_index = layer.tips(html, id + group_id, {
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
                layer.confirm('您确认要删除该分组吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);
                    E.ajax({
                        type: 'get',
                        url: '/webi/doc/operation/del/' + user_id,
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
                    }else{
                        layer.msg('排序值不能为空', {icon: 2, offset: '70px', time: 1500});
                        return false;
                    }

                    var load = layer.load();
                    E.ajax({
                        type: 'POST',
                        url: '/webi/doc/operation/list/save',
                        data: {
                            group_id: this.group_id,
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
        };

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/doc/operation/list/search';
        layui.use(['table','form'], function(){
            var table = layui.table;
            var form = layui.form;

            tdom= table.render({
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
                    {fixed: 'left', width:'12%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'sort_order', title: '排序值', width:'10%',align:'right', sort: true,templet: function(sort_arr){
                        return '<a id="sort_order_'+sort_arr.group_id+'" onclick=stat.openOrder('+sort_arr.sort_order+","+sort_arr.group_id+')>'+ sort_arr.sort_order +'</a>'
                    }},
                    {field: 'group_name', title: '分组名称', align:'left', width:'79%'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;

                if(layEvent === 'edit'){
                    stat.edit(data._id);
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

