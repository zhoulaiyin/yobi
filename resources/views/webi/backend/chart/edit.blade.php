@extends('backend')

@section('title')
    {{$title or ''}}
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav " style="display: block;">
            <ul>
                <li>
                    <a href="javascript: void(0);"><span>{{$title or ''}}</span></a>
                </li>
            </ul>
            <div class="top-back" style="top:6px;">
                <button class="layui-btn layui-btn-primary layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper" style="padding-top: 0px;">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">
                    <form class="layui-form" id="chart-form" onsubmit="return false;" style=" padding-top: 30px;">

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="table_name" style="padding-left: 0px;"><span class="red">*</span> 标题：</label>
                            <div class="layui-input-block">
                                <input class="layui-input" type="text" id="table_name" name="table_name" placeholder="请输入标题" value="{{$chart_title or ''}}">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="table_name" style="padding-left: 0px;"><span class="red">*</span> 展示图：</label>
                            <div class="layui-input-block" id="uploadPhoto">
                                <div id="photoPicker">上传展示缩略图</div>
                                @if(isset($photo_link))
                                    <div class="upload_photo"><img style="width:100px;height:100px;border:1px solid #e2e2e2;" src="{{$photo_link}}"/>
                                        <a href="javascript: void(0);" onclick="record.del_photo(this , 'uploadPhoto')" style="margin-left: 9px;">删除</a>
                                    </div>
                                @endif
                                <input type="hidden" name="photo_link" id="photo_link" value="{{$photo_link  or ''}}">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="description" ><span class="red">*</span> JSON包：</label>
                            <div class="layui-input-block">
                                <textarea class="layui-textarea config" id="chart_json" name="chart_json" rows="20" cols="100" style="border-radius: 4px;height: 400px;width: 590px;" placeholder=""> {{$chart_json or ''}}</textarea>
                            </div>
                        </div>
                        <input type="hidden" id="g_id" name="g_id" value="{{$id}}"/>
                        <input type="hidden" id="type" name="type" value="{{$type}}"/>
                        <input type="hidden" id="c_id" name="c_id" value="{{$c_id}}"/>

                        <div style="text-align: center;margin-bottom: 50px;" class="edit_save">
                            <button type="button" class="layui-btn btn-primary layui-btn-lg" onclick="record.save()">保存</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript" src="/libs/webuploader/webuploader.js"></script>
    <script>
        layui.use('form', function(){
            var form = layui.form;
        });

        var uploader = WebUploader.create({
            auto: true,
            swf: '/libs/webuploader/Uploader.swf',
            server: '/upload?action=chart/photo',
            pick: '#filePicker',
            resize: false,
            duplicate: true
        });
        uploader.on( 'uploadSuccess', function( file, res ) {

            console.log(res);

            if (res.code == 200) {
                record.create_upload(res.data.url, res.data.name);
            } else {
                layer.alert(res.message,{icon:2});
            }

        });

        var photouploader = WebUploader.create({
            auto: true,
            swf: '/libs/webuploader/Uploader.swf',
            server: '/upload?action=chart/photo',
            pick: '#photoPicker',
            resize: false
        });

        photouploader.on( 'uploadSuccess', function( file, res ) {

            if (res.code == 200) {
                record.create_photo(res.data.url);
            } else {
                layer.alert(res.message,{icon:2});
            }

        });

        var record = {
            create_photo: function (url) {
                $(".upload_photo").remove();
                $("#photo_link").val(url);

                //显示上传图片
                var html = '';
                html += '<div class="upload_photo"><img style="width:100px;height:100px;border:1px solid #e2e2e2;" src="' + url + '"/>';
                html += '<a href="javascript: void(0);" onclick="record.del_photo(this , \'uploadPhoto\')" style="margin-left: 9px;">删除</a></div>';

                $("#uploadPhoto").append(html);
            },

            del_photo: function (obj, tagId) {
                $(obj).parent().remove();
                $("#photo_link").val('');
            },

            save:function() {
                var msg ='';
                var dt = E.getFormValues('chart-form');

                if(dt.table_name==""){
                    msg += '标题不可为空<br>';
                }
                if(dt.photo_link==""){
                    msg += '展示图不可为空<br>';
                }
                if(dt.chart_json==""){
                    msg += 'JSON包不可为空<br>';
                }
                if ( msg != '' ) {
                    layer.alert(msg,{icon:2,offset:'50px'});
                    return false;
                }

                dt.chartJson = JSON.stringify( eval( '('+dt.chart_json+')' ) );

                layer.confirm('你确定要保存图表类型维护吗？',{icon:3,offset:'50px'},function (index) {

                    E.ajax({
                        type:'post',
                        url: '/webi/backend/bi/chart/save',
                        data : dt ,
                        success:function ( o ) {

                            if ( o.code == 200 ) {
                                layer.alert(o.message,{icon:1,offset:'50px',time:1500});
console.log(dt)
                                if(dt.type==0){//增加操作
                                    parent.group.list(dt.g_id);
                                    $("#table_name").val('');
                                    $("#chart_json").val('');
                                    $("#photo_link").val('');
                                    $(".upload_photo").remove();

                                }else{//编辑操作返回上一页面
                                    parent.group.list(dt.c_id);
                                    setTimeout('parent.layer.closeAll();' ,1500 );
                                }


                            } else {
                                layer.alert(o.message,{icon:2,offset:'50px'});
                                return false;
                            }

                        }

                    });

                })

            },
        }
    </script>
@endsection