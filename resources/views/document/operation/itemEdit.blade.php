@extends('backend')

@section('title')
    业务流程图
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
    <link rel="stylesheet" href="/libs/editor.md-master/css/editormd.css" />
    <style>

        #layui-layer1 .layui-layer-setwin .layui-layer-close1{
            background-position: 1px -40px;
        }

        .pic {
            border: 1px solid #e5e5e5;
            border-bottom: none;
            padding-bottom: 5px;
        }

        .word {
            border: 1px solid #e5e5e5;
            border-top: none;
            border-bottom: none;
            padding-bottom: 5px;
        }

    </style>

@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li id="active_1">
                    <a href="javascript: void(0);"><span>项目详情</span></a>
                </li>
            </ul>
            <div class="top-back">
                <button class="layui-btn btn-parimary layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper" style="padding-top: 0px;">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row pic">
                    <form class="layui-form" style="margin-top: 10px;">

                        <div  class="layui-form-item">
                            <label class="layui-form-label">
                                <span style="color:red">*</span>分组:
                            </label>
                            <div class="layui-input-block" style="margin-bottom:10px;">
                                <select id="group_id" name="group_id">
                                    <option value="">请选择分组</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">
                                <span style="color:red">*</span>项目名称:
                            </label>
                            <div class="layui-input-block" style="margin-bottom:10px;">
                                <input type="text" class="layui-input" placeholder="项目名称" id="item_name">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label"><span style="color:red">*</span>排序值:</label>
                            <div class="layui-input-block" style="margin-bottom:10px;">
                                <input type="text" class="layui-input" style="width:80px;" placeholder="排序值" id="sort_order" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <div class="layui-form-label">
                                <a id="filePicker" class="option_img" >添加图片</a>
                            </div>
                            <div class="layui-input-block">
                                <input type="text" class="up_url layui-input" placeholder="上传图片后得到图片的存储路径" readonly>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="layui-row word" style="display: none;">
                    <div class="layui-form-item">
                        <div class="layui-form-label" style="width: 110px;">
                            <div id="wordPicker" class="option_img" >上传文件</div>
                        </div>
                        <div class="layui-input-block">
                            <input type="text" class="word_url layui-input" placeholder="上传文件后得到文件的下载路径" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">

                    <div id="edit_area"></div>

                </div>
            </div>
        </div>

    </div>

@endsection

@section('js')
    <script src="/libs/webuploader/webuploader.js"></script>
    <script src="/libs/editor.md-master/editormd.min.js"></script>
    <script type="text/javascript">

        var testEditor = '';
        var form = '';
        layui.use('form', function() {
            form = layui.form;

            form.render();
        });

        var flow_id = '{!! $flow_id or 0 !!}'
        var http_post = {!! $http_url or ''!!}
        var group = {!! $group !!};
        var sort_order = {!! $sort_order !!};

        if ( group ) {
            $.each( group , function(i,n){
                var  html = '<option value="'+ n._id + '" >'+ n.group_name + '</option>';
                $('#group_id').append(html);
            });
        }

        var flow = {

            //显示文档详情
            showDoc:function( flow_id ) {

                if (E.isEmpty( flow_id) ) {
                    layer.alert('参数错误',{icon:2,offset:'50px'});
                    return false;
                }

                testEditor = '';
                $('#edit_area').html('');       //文档编辑域重置
                $('.edit_save').remove();         //删除保存按钮

                E.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '/webi/doc/operation/item/get/'+flow_id,

                    success:function( res ) {

                        if ( res.code == 200 ) {
                            markdown.showArea( res.data );
                        } else {
                            layer.alert( res.message , {icon:2,offset:'50px'});
                        }
                    }
                })

            }

        };

        flow.showDoc(flow_id);

        $(document).ready(function () {
            var $class = $('#active_'+flow_id) ;
            $class.addClass('active') ; //添加
            $class.siblings().removeClass('active') ; //其他同胞移除
        });
    </script>

    <!--  markdown文档 -->
    <script type="text/javascript">

        var markdown = {

            showArea:function( data ) {

                var html = '';
                html +='<div id="doc_markdown" style="float: left;">';
                html +='<textarea style="display:none;float: left;">'+data.sound_code+'</textarea>';
                html +='</div>';

                $('#edit_area').html(html);
                $('#sort_order').val(data.sort_order);
                $('#item_name').val(data.item_name);
                $('#group_id').val(data.group_id);
                if(!data.sort_order){
                    $('#sort_order').val(sort_order);
                }
                form.render();
                testEditor = editormd({
                    id      : "doc_markdown",
                    width   : "100%",
                    height  : 640,
                    watch : true,
                    saveHTMLToTextarea : true,
                    path    : "/libs/editor.md-master/lib/"
                });

                var save_html = '<div style="text-align: center;margin-bottom: 50px;" class="edit_save">';
                save_html += '<button type="button" class="layui-btn layui-btn-lg layui-btn-normal" onclick="markdown.saveMarkdown('+ "'"+data.id +"'"+');">保存</button>';
                save_html += '</div>';
                $('#edit_area').after(save_html);
            },


            //保存markdown文档
            saveMarkdown:function( flow_id ) {

                if (E.isEmpty( flow_id ) ) {
                    layer.msg('参数错误',{icon:2,offset:'50px'});
                    return false;
                }

                var markdown = testEditor.getMarkdown();
                var html = testEditor.getPreviewedHTML();//获取预览html代码
                var sort_order = $('#sort_order').val();
                var item_name = $('#item_name').val();
                var group_id = $('#group_id').val();

                if ( E.isEmpty( markdown ) ) {
                    layer.msg('文档内容不能为空',{icon:2,offset:'50px'});
                    return false;
                }
                if ( E.isEmpty( group_id ) ) {
                    layer.msg('分组不能为空',{icon:2,offset:'50px'});
                    return false;
                }
                if ( E.isEmpty( item_name ) ) {
                    layer.msg('项目名称不能为空',{icon:2,offset:'50px'});
                    return false;
                }
                if ( E.isEmpty( sort_order ) ) {
                    layer.msg('排序值不能为空',{icon:2,offset:'50px'});
                    return false;
                }
                if ( !E.isInt( sort_order ) ) {
                    layer.msg('排序值格式错误',{icon:2,offset:'50px'});
                    return false;
                }

                layer.confirm('您确定保存该流程图吗？',{icon:3,offset:'50px'},function(index){

                    layer.close(index);

                    E.ajax({
                        type: 'post',
                        dataType: 'josn',
                        url: '/webi/doc/operation/item/save',
                        data:{
                            flow_id :flow_id,
                            markdown :markdown,
                            html :html,
                            sort_order:sort_order,
                            item_name:item_name,
                            group_id:group_id
                        },
                        success: function (res) {
                            if ( res.code == 200 ) {
                                layer.msg( res.message,{icon:1,offset:'50px',time:1500});
                            } else {
                                layer.msg( res.message,{icon:2,offset:'50px'});
                            }
                        }
                    });
                })
            }

        };

        var uploader = WebUploader.create({
            auto: true,
            swf: '/libs/webuploader/Uploader.swf',
            server: '/upload?action=document',
            pick: '#filePicker',
            resize: false,
            duplicate :true
        });

        uploader.on( 'uploadSuccess', function( file, res ) {

            if (res.code == 200) {
                var url = http_post + res.data.url;
                $('.up_url').val(url);
            } else {
                layer.alert(res.message,{icon:2});
            }

        });

        var uploaderw = WebUploader.create({
            auto: true,
            swf: '/libs/webuploader/Uploader.swf',
            server: '/upload?action=document',
            pick: '#wordPicker',
            resize: false,
            duplicate :true
        });

        uploaderw.on( 'uploadSuccess', function( file, res ) {

            if (res.code == 200) {
                $('.word_url').val(http_post+res.data.url);
            } else {
                layer.alert(res.message,{icon:2});
            }

        });
        //
    </script>
    <!--  markdown文档 -->

@endsection