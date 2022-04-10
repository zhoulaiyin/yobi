@extends('backend')

@section('title')
    报表字体
@endsection

@section('css')
    <link rel="stylesheet" href="/css/webi/list.css?v=201803221330">

    <style>
        #wrapper{
            margin: 10px;
            padding: 15px;
            background: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li>
                    <a href="/webi/attribute/color/list"><span>图表颜色</span></a>
                </li>
                <li class="active">
                    <a href="/webi/attribute/fontfamily/list"><span>字体维护</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">
                    <div class="layui-col-md12">
                        <button class="layui-btn btn-primary" type="button" onclick="stat.edit(0,'','');">添加字体</button>
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

    <div id="add_layer" style="display:none;">
        <form id="title-form-header" onsubmit="return false;" class="layui-form" role="form" style="margin-top: 20px;">
            <div class="layui-form-item">
                <div>
                    <label class="col-sm-4 layui-form-label" for="title" style="padding-top: 8px;" ><span class="red pr5">*</span>&ensp;字体：</label>
                    <div class="col-sm-7" style="padding-left: 3px;">
                        <input type="text" class="layui-input" name="title" id="title-header" placeholder="请输入字体名称">
                    </div>
                </div>
                <div >
                    <label class="col-sm-4 layui-form-label" for="group_code" style="padding-top: 13px;"><span class="red pr5">*</span>&ensp;排序值：</label>
                    <div class="col-sm-7" style="padding-top: 5px;padding-left: 3px;">
                        <input type="text" class="layui-input" name="group_code" id="group_code-header" placeholder="请输入排序值">
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')

    <script type="text/javascript">
        var tdom = '';
        var count = 1;
        var add_layer="";

        var stat = {
            search:function(){
                tdom.reload({
                    page: {
                        curr: 1
                    }
                });
            },
            edit: function ( font_id ,fontfamily,sort_order) {

                var  url = "/webi/attribute/fontfamily/edit/" + font_id;
                var title = '编辑字体';
                if( font_id == 0){//新增
                    url = "/webi/attribute/fontfamily/add";
                    title = '新增字体';
                }

                layer.open({
                    title: title,
                    offset: '70px',
                    type: 1,
                    area: ['600px', '50%'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: add_layer,
                    btn: ['确认','取消'],
                    yes: function () {
                        var dt = E.getFormValues('title-form');
                        var msg = '';

                        if(dt.val ==""){
                            msg += '请输入字体名称<br>';
                        }

                        if(dt.sort_order==""){
                            msg += '请输入排序值!<br>';
                        }

                        if (msg) {
                            layer.msg(msg, {icon: 2, offset: '70px', time: 1500});
                            return false;
                        }

						dt.font_id = font_id;
                        E.ajax({
                            type: 'post',
                            url: url,
                            data: dt,
                            success: function (res) {
                                if (res.code == 200) {

                                    layer.msg('保存成功', {icon: 1, offset: '70px', time: 1500});
                                    stat.search();

                                    if( font_id == 0 ){
                                        //弹窗input清空
                                        $("#title-header-form").val("");
                                        $("#sort_order-header").val("");
                                    }

                                } else {
                                    layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                                }
                            }
                        });
                    }
                });

                if( font_id != 0 ){
                    $("#title-header-form").val(fontfamily);
                    $("#sort_order-header").val(sort_order);
                }
            },
            openOrder: function (init,font_id) {

                this.font_id = font_id;

                var html = '<div class="form-horizontal" style="margin-top: 15px;">' ;

                html += '<div class="form-group">';
                html += '<div class="col-sm-12">';
                html += '<input type="text" id="sort_order" class="layui-input" value='+init+' >';
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

                this.layer_index = layer.tips(html, id + font_id, {
                    tips: [4, '#fff'],
                    time: 0,
                    success: function () {
                        $("#sort_order").keyup(function(){
                            var c=$(this);
                            if(/[^\d]/.test(c.val())){
                                var sort=c.val().replace(/[^\d]/g,'');
                                $(this).val(sort);
                            }
                        });
                    }
                });

            },
            del:function(font_id){
                layer.confirm('您确认要删除该字体吗？', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);
                    E.ajax({
                        type: 'get',
                        url: '/webi/attribute/fontfamily/del/' + font_id,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.msg('删除成功', {icon: 1, offset: '70px', time: 1000});
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
                        url: '/webi/attribute/fontfamily/sort/save',
                        data: {
                            font_id: this.font_id,
                            sort_order: sort_order
                        },
                        success: function (res) {
                            layer.close( load );
                            if ( res.code == 200 ) {
                                layer.msg( res.message, { icon: 1 ,time:1500 } );
                                layer.close( stat.layer_index );
                                stat.search();
                            } else {
                                layer.msg( res.message, { icon: 2 ,time:1500  } );
                            }
                        }

                    });
                } else {
                    layer.close( this.layer_index );
                }
            }
        };

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/attribute/fontfamily/search';
        layui.use('table', function(){
            var table = layui.table;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                initSort: {
                    field: 'sort_order',
                    type: 'asc'
                },
                done: function(){
                    if(count==1){
                        count=0;
                        stat.search();
                    }
                },
                cols: [[
                    {fixed: 'left', width:'12%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'fontfamily', title: '字体',align:'left'},
                    {field: 'sort_order', title: '排序值', width:'15%', sort: true,align:'right',
							templet: function(sort_arr){
								return '<a id="sort_order_'+sort_arr.font_id+'" onclick=stat.openOrder('+sort_arr.sort_order+","+sort_arr.font_id+')>'+ sort_arr.sort_order +'</a>'
							}}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'edit'){
                    stat.edit(data.font_id,data.fontfamily,data.sort_order);
                }else if(layEvent === 'del'){
                    stat.del(data.font_id);
                }
            });
        });

        add_layer=$("#add_layer").html();
        add_layer=add_layer.replace(/title-form-header/g,"title-form");
        add_layer=add_layer.replace(/title-header/g,"title-header-form");
        add_layer=add_layer.replace(/group_code/g,"sort_order");

    </script>
@endsection

