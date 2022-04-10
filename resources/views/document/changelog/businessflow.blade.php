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
        .up_url,.word_url{
            width: 500px!important;
        }
        .option_img{
            height:37px;
        }
    </style>

@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li id="active_1">
                    <a href="javascript: void(0);"><span>流程图详情</span></a>
                </li>
            </ul>
            <div class="top-back">
                <button class="layui-btn layui-btn-primary layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper" style="padding-top: 30px;">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row pic">
                    <div class="layui-form" style="margin-top: 10px;">
                        <div class="layui-form-item" style="width: 100%;height:45px;">
                            <label class="layui-form-label"><span style="color:red">*</span>版本号:</label>
                            <div class="layui-input-block" style="margin-bottom:10px;">
                                <input type="text" class="layui-input" style="width:130px;" placeholder="版本号" id="version">
                            </div>
                        </div>

                        <div class="layui-form-item" style="width: 100%;height:45px;">
                            <label class="layui-form-label"><span style="color:red">*</span>操作人:</label>
                            <div class="layui-input-block" style="margin-bottom:10px;">
                                <input type="text" class="layui-input" style="width:130px;" id="editor">
                            </div>
                        </div>

                        <div class="layui-form-item" style="width: 100%;height:45px;">
                            <label class="layui-form-label"><span style="color:red">*</span>更新日期:</label>
                            <div class="layui-input-block" style="margin-bottom:10px;">
                                <input type="text" class="layui-input" style="width:130px;" id="change_date" >
                            </div>
                        </div>

                        <div class="layui-form-item" style="width: 100%;height:45px;">
                            <div class="layui-form-label">
                            </div>
                            <div class="layui-input-block">
                                <button type="button" class="layui-btn btn-primary option_img" id="test1">上传图片</button>
                                <input type="text" class="up_url layui-input inline" placeholder="上传图片后得到图片的存储路径" readonly>
                            </div>
                        </div>
                    </div>
                </div>

              <div class="layui-row word" style="display: none;">
                  <div class="layui-form-item">
                      <div class="layui-form-label">
                      </div>
                      <div class="layui-input-block">
                          <button type="button" class="layui-btn btn-primary option_img" id="test2">上传文件</button>
                          <input type="text" class="word_url layui-input inline" placeholder="上传文件后得到文件的下载路径" readonly>
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

        var _id = {!! "'".$_id."'" !!}
        var flow_id = {{"'".$flow_id."'" or 0}};
        var http_post = {!! $http_url or ''!!};

        var flow = {

            //显示文档详情
            showDoc:function( _id ) {
                testEditor = '';
                $('#edit_area').html('');       //文档编辑域重置
                $('.edit_save').remove();         //删除保存按钮

                E.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '/webi/doc/changelog/get/'+_id,

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

        flow.showDoc(_id);
        
        $(document).ready(function () {
            var $class = $('#active_'+_id) ;
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
                $('#version').val(data.v);
                $('#change_date').val(data.change_date);
                $('#editor').val(data.editor);

                testEditor = editormd({
                    id      : "doc_markdown",
                    width   : "100%",
                    height  : 640,
                    watch : true,
                    saveHTMLToTextarea : true,
                    path    : "/libs/editor.md-master/lib/"
                });

                var save_html = '<div style="text-align: center;margin-bottom: 50px;" class="edit_save">';
                save_html += '<button type="button" class="layui-btn btn-primary btn-lg" onclick="markdown.saveMarkdown('+ "'"+data._id+ "'"+');">保存</button>';
                save_html += '</div>';
                $('#edit_area').after(save_html);

            },


            //保存markdown文档
            saveMarkdown:function( flow_id ) {

                var markdown = testEditor.getMarkdown();
                var html = testEditor.getPreviewedHTML();//获取预览html代码
                var version = $('#version').val();
                var editor = $('#editor').val();
                var change_date = $('#change_date').val();
                var msg = '';

                if (E.isEmpty( markdown )) {
                    msg += '文档内容不能为空<br/>';
                }
                if (E.isEmpty( version )) {
                    msg += '版本号不能为空<br/>';
                }
                if (E.isEmpty( editor )) {
                    msg += '操作人不能为空<br/>';
                }
                if (E.isEmpty( change_date )) {
                    msg += '更新时间不能为空<br/>';
                }

                if(msg){
                    layer.alert(msg,{icon:2,offset:'50px'});
                    return false;
                }

                layer.confirm( '您确定保存该流程图吗？',{icon:3,offset:'50px'},function(index){

                    layer.close(index);

                    E.ajax({
                        type: 'post',
                        dataType: 'josn',
                        url: '/webi/doc/changelog/chart/save',
                        data:{
                            _id :flow_id,
                            markdown :markdown,
                            html :html,
                            version:version,
                            editor:editor,
                            change_date:change_date
                        },
                        success: function (res) {
                            if ( res.code == 200 ) {
                                layer.alert( res.message,{icon:1,offset:'50px',time:1500});
                            } else {
                                layer.alert( res.message,{icon:2,offset:'50px'});
                            }
                        }
                    });


                })


            }

        };


        layui.use(['laydate','upload','form'], function() {
            var laydate = layui.laydate;
            var upload = layui.upload;
            var form = layui.form;

            //年月日选择器
            laydate.render({
                elem: '#change_date'
            });

            //上传图片
            var uploadImg = upload.render({
                elem: '#test1' //绑定元素
                ,url: '/upload?action=document' //上传接口
                ,done: function(res){
                    $('.up_url').val(http_post+res.data.url);
                }
                ,error: function(){
                    layer.alert(res.message,{icon:2});
                }
            });
            //上传文件
            var uploadWord = upload.render({
                elem: '#test2' //绑定元素
                ,url: '/upload?action=document' //上传接口
                ,done: function(res){
                    $('.word_url').val(http_post+res.data.url);
                }
                ,error: function(){
                    layer.alert(res.message,{icon:2});
                }
            });
        });


    </script>
    <!--  markdown文档 -->

@endsection