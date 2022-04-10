@extends('backend')

@section('title')
    BI模板
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li>
                    <a href="/webi/template/database/grouping"><span>模板分组</span></a>
                </li>
                <li class="active">
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
                        <button class="layui-btn btn-primary" type="button" onclick="EDIT_TEMPLATE.save(0);">添加模板</button>
                    </div>

                    <div class="layui-col-md10" >
                        <form class="layui-form search_content fr" id="search-form" onsubmit="return false;">
                            <div class="layui-form-item" style="margin-left: 10px;width: 200px;">
                                <select class="" style="width: 200px;margin-left: 10px;" id="project_id" name="project_id">
                                    <option value="">请选择来源</option>
                                </select>
                            </div>

                            <div class="input-group layui-form-item fr" style="margin-left: 5px;">
                                <select class="inline_b" style="width: 200px;margin-left: 10px;" id="template_status" name="template_status">
                                    <option value="">请选择模板状态</option>
                                    <option value="3">创建</option>
                                    <option value="1">发布</option>
                                    <option value="2">下架</option>
                                </select>
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
                    <a class="layui-btn layui-btn-xs" lay-event="desige">设计</a>
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="add">发布</a>
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">下架</a>
                </script>
            </div>
        </div>

        {{--新增编辑弹窗--}}
    <div id="layer-editTemplate" style="display: none">

            <form class="layui-form" id="e_chart-form"  style="padding-right: 15px;padding-left: 15px;">

                <div class="layui-form-item">
                    <label class="layui-form-label" for="template_title" style="padding-left: 0px;"><span class="red">*</span> 模板标题：</label>
                    <div class="layui-col-sm8">
                        <input class="layui-input" type="text" id="e_template_title" name="e_template_title" placeholder="请输入模板标题" value="g_template_title ">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label" for="template_group" style="padding-left: 0px;"><span class="red">*</span> 模板分组：</label>
                    <div class="layui-col-sm8">
                        <select class="" id="e_template_group" name="e_template_group">
                            <option value="">请选择分组</option>
                        </select>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label" for="template_title" style="padding-left: 0px;"><span class="red">*</span> 模板展示图：</label>
                    <div class="layui-col-sm8" id="e_uploadPhotoPic">
                        <div id="e_picPicker">上传模板展示图</div>
                            <div class="e_upload_photo_pic"><img style="width:100px;height:100px;border:1px solid #e2e2e2;" src="g_template_pic"/>
                                <a href="javascript: void(0);" onclick="record.del_photo_pic(this , 'e_upload_photo_pic')" style="margin-left: 9px;">删除</a>
                            </div>
                        <input type="hidden" name="e_template_pic" id="e_template_pic" value="g_template_pic">
                    </div>
                </div>
                <input type="hidden" id="type" name="type" value="g_types"/>

            </form>

    </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript" src="/libs/webuploader/webuploader.js"></script>

    {{--主页面--}}
    <script type="text/javascript">
        var templ_data = {!! $templ_list !!}
        var edit_template="";
        var tdom = '';
        var form = null;

        //搜索栏来源
        if ( templ_data ) {
            $.each( templ_data , function(i,n){
                var option = '<option value="' + n.project_id  + '">' + n.project_name + '</option>';

                $('select[name=project_id]').append(option);
            });

            layui.use('form', function () {
                layui.form.render('select');
            });
        }

        var stat = {
            search:function(){
                console.log('$("#project_id").val()',$("#project_id").val())
                tdom.reload({
                    where: {
                        project_id: $("#project_id").val(),
                        template_status: $("#template_status").val()
                    },
                    page: {
                        curr: 1
                    }
                });
            },

            //重置
            reset:function () {
                $('select[name=project_id] option[value=""]').prop('selected', true);
                $('select[name=template_status] option[value=""]').prop('selected', true);
                stat.search();
            },

            //发布、下架
            del:function (template_id,$type) {

                var title= {
                    1:'发布',
                    2:'下架'
                };

                layer.confirm('您确认要'+title[$type]+'该模板吗？',{icon: 3,offset:"70px"}, function (index) {
                    layer.close(index);

                    $.ajax({
                        type: 'get',
                        url: '/webi/template/database/del/'+template_id+"" +'/'+$type,
                        success: function (res) {
                            if(res.code==200){

                                layer.alert("操作成功", {icon: 1, offset: '70px'});

                                //刷新页面
                                stat.search();

                            }else{
                                layer.alert(res.msg, {icon: 2, offset: '70px'});
                            }
                        }
                    });

                });
            },

        };

        // 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
        var bootstrap_table_ajax_url = '/webi/template/database/search';
        //table插件//
        layui.use(['table','form'], function(){
            var table = layui.table;
            form = layui.form;

            tdom= table.render({
                elem: '#table',
                height: 460,
                url: bootstrap_table_ajax_url,
                page: true, //开启分页
                cols: [[
                    {fixed: 'left', width:'20%',title:'操作',align:'left', toolbar: '#barDemo'},
                    {field: 'creator', title: '创建人', width:'10%',align:'left'},
                    {field: 'project', title: '来源', width:'10%',align:'left'},
                    {field: 'template_title', title: '模板标题', width:'13%',align:'left'},
                    {field: 'template_id', title: '模板ID',align:'right', width: '21%', sort: true},
                    {field: 'template_pic', title: '模板展示图',align:'center', width: '15%'},
                    {field: 'status', title: '模板状态',align:'center', width: '12%'}
                ]]
            });
            table.on('tool(table)', function(obj){
                var data = obj.data;
                var layEvent = obj.event;
                if(layEvent === 'add'){
                    stat.del(data.template_id,1);
                } else if(layEvent === 'del'){
                    stat.del(data.template_id,2);
                }else if(layEvent === 'edit'){
                    EDIT_TEMPLATE.save(data.template_id);
                }else if(layEvent === 'desige'){
                    window.open('/webi/template/database/design/'+data.template_id);
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


        $(document).ready(function () {

            //编辑模板弹窗
            edit_template=$('#layer-editTemplate').html();
            edit_template=edit_template.replace(/e_chart-form/g,"chart-form");
            edit_template=edit_template.replace(/e_template_title/g,"template_title");
            edit_template=edit_template.replace(/e_template_group/g,"template_group");
            edit_template=edit_template.replace(/e_uploadPhotoIcon/g,"uploadPhotoIcon");
            edit_template=edit_template.replace(/e_iconPicker/g,"iconPicker");
            edit_template = edit_template.replace(/e_upload_photo_icon/g, "upload_photo_icon");
            edit_template = edit_template.replace(/e_template_icon/g, "template_icon");
            edit_template = edit_template.replace(/e_uploadPhotoPic/g, "uploadPhotoPic");
            edit_template = edit_template.replace(/e_picPicker/g, "picPicker");
            edit_template = edit_template.replace(/e_upload_photo_pic/g, "upload_photo_pic");
            edit_template = edit_template.replace(/e_template_pic/g, "template_pic");

        })

    </script>

    <script type="text/javascript">

        var EDIT_TEMPLATE={};//添加编辑模板集合

        //添加、编辑模板
        EDIT_TEMPLATE.save = function(type) {
            var temple_html=edit_template;

            if( type != 0 ){//编辑

                //查询模板需要编辑信息
                $.ajax({
                    type:'get',
                    url: '/webi/template/database/edit/' + type,
                    success:function ( o ) {

                        if ( o.code == 200 ) {
                            temple_html=temple_html.replace(/g_template_title/g,o.data['template_title']);
                            if(!$.isEmptyObject(o.data['template_pic'])){//存在展示图
                                temple_html=temple_html.replace(/g_template_pic/g,o.data['template_pic']);
                            }
                            temple_html=temple_html.replace(/g_types/g,type);
                            layer_openTemplate(type,temple_html,o.data['group_id']);//新增编辑弹层
                        }

                    }
                });

            }else{//新增
                layer_openTemplate(type,temple_html,'');//新增编辑弹层
            }

        }

        //弹层
        function layer_openTemplate(type,temple_html,group_id){

            E.ajax({
                type:'get',
                url: '/webi/template/database/group/list',
                success:function ( o ) {

                    if ( o.code == 200 ) {
                        if ( !$.isEmptyObject(o.data) ) {
                            $.each( o.data , function(i,n){

                                if( group_id != n.group_id ){
                                    var  html = '<option value="'+ n.group_id + '" >'+ n.group_name + '</option>';
                                }else{
                                    var  html = '<option value="'+ n.group_id + '" selected>'+ n.group_name + '</option>';
                                }

                                $('#template_group').append(html);
                            });

                        }

                        layui.use('form', function () {
                            layui.form.render('select');
                        });

                    }
                }

            });

            layer.open({
                title: type ? "编辑模板" : "新增模板",
                offset: '70px',
                type: 1,
                area: ['600px', '400px'],
                scrollbar: false,
                closeBtn: 0,
                content: temple_html,
                btn: ['确认','取消'],
                yes: function () {
                    var msg ='';
                    var dt = E.getFormValues('chart-form');

                    if(dt.template_title==""){
                        msg += '模板标题不可为空<br>';
                    }
                    if(dt.template_pic==""){
                        msg += '模板展示图不可为空<br>';
                    }

                    if ( msg != '' ) {
                        layer.alert(msg,{icon:2,offset:'50px'});
                        return false;
                    }

                    layer.confirm('您确定要保存模板吗？',{icon:3,offset:'50px'},function (index) {

                        E.ajax({
                            type:'post',
                            url: '/webi/template/database/save',
                            data : dt ,
                            success:function ( o ) {

                                if ( o.code == 200 ) {
                                    layer.alert(o.message,{icon:1,offset:'50px',time:1500});

                                    stat.search();
                                    $("#template_title").val('');
                                    $("#template_group").val('');
                                    $("#template_pic").val('');
                                    $(".upload_photo_pic").remove();

                                } else {
                                    layer.alert(o.message,{icon:2,offset:'50px'});
                                    return false;
                                }

                            }

                        });

                    })
                }

            });

            if(type==0){//新增 置空参数

                $("#template_title").val("");

                $(".upload_photo_pic").remove();
                $("#template_pic").val('');
            }


//            //展示图为空 移除img标签
            if(($(".upload_photo_pic img").attr("src"))=="g_template_pic"){
                $(".upload_photo_pic").remove();
                $("#template_pic").val('');
            }

            upload("picPicker");//按钮绑定上传事件
        }


        //上传图片
        function  upload(pickPic){
            //上传展示图
            var photouploader = WebUploader.create({
                auto: true,
                swf: '/libs/webuploader/Uploader.swf',
                server: '/upload?action=template/photo',
                pick: '#'+pickPic,
                resize: false
            });

            photouploader.on( 'uploadSuccess', function( file, res ) {

                if (res.code == 200) {
                    record.create_photo_pic(res.data.url);
                } else {
                    layer.alert(res.message,{icon:2});
                }

            });
        }

        var record = {

            //上传展示图
            create_photo_pic: function (url) {
                $(".uploadPhotoPic").remove();
                $("#template_pic").val(url);

                //显示上传图片
                var html = '';
                html += '<div class="upload_photo_pic"><img style="width:100px;height:100px;border:1px solid #e2e2e2;" src="' + url + '"/>';
                html += '<a href="javascript: void(0);" onclick="record.del_photo_pic(this , \'upload_photo_pic\')" style="margin-left: 9px;">删除</a></div>';

                $("#uploadPhotoPic").append(html);
            },

            //删除展示图
            del_photo_pic: function (obj, tagId) {
                $(obj).parent().remove();
                $("#template_pic").val('');
            }

        }

    </script>
@endsection

