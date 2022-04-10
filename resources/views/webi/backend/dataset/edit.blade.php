@extends('backend')

@section('title')
    {{$title or ''}}
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
        .frequency_type{
            line-height:36px;
        }

        .fields input{
            width: 100%!important;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav " style="display: block;">
            <ul>
                <li>
                    <a href="javascript: void(0);"><span>{{$title or ''}}</span></a>
                </li>
            </ul>
            <div class="top-back">
                <button class="layui-btn layui-btn-primary layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper" style="padding-top: 0px;">

        <div class="layui-row">
            <div class="layui-col-lg12">
                <div class="layui-row">
                    <form class="layui-form" id="bi-form" onsubmit="return false;" style="padding-top:30px;">
                        <input type="hidden" id="_id" name="_id" value="{{$_id or 0}}" />
                        <input type="hidden" id="table_type" name="table_type" value="{{$type or 0}}" />

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="table_name"  ><span class="red">*</span>  统计源：</label>
                            <div class="layui-input-block">
                                <input class="layui-input inline" type="text" id="table_name" name="table_name" placeholder="请输入统计源" value="{{ $rule['table_name'] or '' }}">

                                <button class="layui-btn btn-primary" type="button" onclick="list.sync();" style="margin-left: 30px;height:37px;">拉取表结构</button>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="statistical_frequency"  ><span class="red">*</span>  统计频率：</label>
                            <input class="layui-input" type="hidden" id="statistical_frequency" name="statistical_frequency" value="{{ $rule['statistical_frequency'] or ''}}">
                            <div class="layui-input-block frequency_type">
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_0">无</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_1">一分钟</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_2">五分钟</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_3">十分钟</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_4">半小时</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_5">一小时</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_6">凌晨0点</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_7">凌晨1点</button>
                                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" id="frequency_8">凌晨3点</button>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="description" ><span class="red">*</span> 描述：</label>
                            <div class="layui-input-block">
                                <textarea class="layui-textarea config" id="description" name="description" rows="5" cols="100" style="border-radius: 4px;height: 100px;width: 590px;" placeholder="">@if(isset($rule)) {{$rule['description']}} @endif</textarea>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="args" ><span class="red">*</span> 字段信息：</label>
                            <div class="layui-input-block">
                                <table class="layui-table fields">
                                    <colgroup>
                                        <col width="150">
                                        <col width="200">
                                        <col>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th style="width:240px;"><span class="red">*</span>字段名称</th>
                                        <th style="width:200px;"><span class="red">*</span>字段类型</th>
                                        <th><span class="red"></span>示例值</th>
                                        <th style="width:240px;"><span class="red">*</span>字段说明</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if( isset( $rule ) && !empty( $rule['fields_json'] ))

                                        @foreach ($rule['fields_json'] as $item)
                                                <tr  class="request_message request_add">
                                                    <td><input type="text" class="layui-input input-sm"  name="field_name" value="{{$item['field_name'] or ''}}"></td>
                                                    <td><input type="text" class="layui-input input-sm"  name="field_type" value="{{$item['field_type'] or ''}}"></td>
                                                    <td><input type="text" id="field_sample" class="layui-input input-sm"  name="field_sample" value="{{$item['field_sample'] or ''}}"></td>
                                                    <td><input type="text" class="layui-input input-sm"  name="field_remark"  value="{{$item['field_remark'] or ''}}"></td>
                                                    <td><div><a class="layui-btn layui-btn-danger layui-btn-xs" href="javascript: void(0);" onclick="ebi.removeRequest(this)">删除</a></div></td>
                                                </tr>
                                        @endforeach

                                    @endif

                                    <tr id="request_tr" class="request_message">
                                        <td><input type="text" class="layui-input input-sm" id="request_field_name" name="field_name" value=""></td>
                                        <td><input type="text" class="layui-input input-sm" id="request_field_type" name="field_type" value=""></td>
                                        <td><input type="text" class="layui-input input-sm" id="request_field_sample" name="field_sample" value=""></td>
                                        <td><input type="text" class="layui-input input-sm" id="request_field_remark" name="field_remark"  value=""></td>
                                        <td ><div><a class="layui-btn btn-primary layui-btn-xs" href="javascript: void(0);" onclick="ebi.request()">添加</a></div></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="" ><span class="red">*</span> 规则说明：</label>
                            <div class="layui-input-block">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="row">

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
        layui.use('form', function(){
            var form = layui.form;
        });

        var testEditor = '';
        var table_id = {{ "'".$id ."'" or 0}};
        var rule_json = {!! $rule_json or 0  !!};

        var ebi = {

            request:function () {

                if( $("#request_field_name").val() == '' ) {
                    $("#request_field_name").focus();
                    return false;
                }

                if( $("#request_field_remark").val() == '' ) {
                    $("#request_field_remark").focus();
                    return false;
                }

                var html =  '<tr class="request_message request_add">' ;
                html +=  '<td><input type="text" class="layui-input input-sm" name="field_name"  value=" '+$("#request_field_name").val()+' "></td>' ;
                html += '<td><input type="text" class="layui-input input-sm" name="field_type" value="'+$("#request_field_type").val()+'"></td>' ;
                html += '<td><input type="text" id="field_sample" class="layui-input input-sm"  name="field_sample" value=" '+$("#request_field_sample").val()+'"></td>' ;
                html +=  '<td><input type="text" class="layui-input input-sm" name="field_remark"  value=" '+$("#request_field_remark").val()+' "></td>' ;
                html +=  '<td ><a href="javascript: void(0);" class="layui-btn layui-btn-danger layui-btn-xs" onclick="ebi.removeRequest(this)">删除</a></td>' ;
                html +=  '</tr>' ;

                //将新增请求参数添加到添加行之前
                $("#request_tr").before( html );

                //清空添加行数据
                $("#request_tr input").val( '' );
                
            },

            removeRequest:function (obj) {
                $(obj).parent().parent().remove() ;
            }

        };

        var list = {

            sync:function () {

                var loadIndex = layer.load();
                var tablename = $('#table_name').val();

                if (E.isEmpty(tablename)) {
                    layer.alert('请输入数据源表名称', {icon: 2, offset: '70px'});
                    return false;
                }

                E.ajax({
                    type: 'get',
                    url: '/webi/backend/dataset/gettable',
                    data:{
                        tablename : tablename
                    },
                    success: function( obj ) {
                        layer.close(loadIndex);
                        if (obj.code == 200) {
                            $('.request_add').remove();
                            layer.alert(obj.message,{icon:1,offset:'70px'});
                            for (var i=0;i<obj.data.length;i++){
                                var html =  '<tr class="request_message request_add">' ;
                                html +=  '<td><input type="text" class="layui-input input-sm" name="field_name" readonly="readonly" value='+obj.data[i]['column_name']+'></td>' ;
                                html += '<td><input type="text" class="layui-input input-sm" name="field_type" readonly="readonly" value='+obj.data[i]['data_type']+'></td>' ;
                                if(obj.data[i]['data_type'] == 'date'){
                                    html += '<td><input type="text" id="field_sample" class="layui-input input-sm" readonly="readonly" name="field_sample" value="2018-10-25"></td>' ;
                                }else if(obj.data[i]['data_type'] == 'time'){
                                    html += '<td><input type="text" id="field_sample" class="layui-input input-sm" readonly="readonly" name="field_sample" value="08:42:30"></td>' ;
                                }else if(obj.data[i]['data_type'] == 'datetime'){
                                    html += '<td><input type="text" id="field_sample" class="layui-input input-sm" readonly="readonly" name="field_sample" value="2018-10-25 08:42:30"></td>' ;
                                }else if(obj.data[i]['data_type'] == 'int'||obj.data[i]['data_type'] == 'tinyint'||obj.data[i]['data_type'] == 'bigint'||obj.data[i]['data_type'] == 'mediumint'||obj.data[i]['data_type'] == 'smallint'){
                                    html += '<td><input type="text" id="field_sample" class="layui-input input-sm" readonly="readonly" name="field_sample" value="10"></td>' ;
                                }else if(obj.data[i]['data_type'] == 'float'||obj.data[i]['data_type'] == 'double'||obj.data[i]['data_type'] == 'decimal'){
                                    html += '<td><input type="text" id="field_sample" class="layui-input input-sm" readonly="readonly" name="field_sample" value="10.34"></td>' ;
                                }else{
                                    html += '<td><input type="text" id="field_sample" class="layui-input input-sm" readonly="readonly" name="field_sample" value=""></td>' ;
                                }
                                html +=  '<td><input type="text" class="layui-input input-sm" name="field_remark"  value='+obj.data[i]['column_comment']+'></td>' ;
                                html +=  '<td ><a href="javascript: void(0);" onclick="ebi.removeRequest(this)">删除</a></td>' ;
                                html +=  '</tr>' ;

                                //将新增请求参数添加到添加行之前
                                $("#request_tr").before( html );
                            }

                        } else {

                            layer.alert(obj.message, {icon: 2,offset:'70px'});
                        }
                    }
                });

            }

        }

        $(document).on('click', ".frequency_type button", function ( ) {
                var button_id = $(this).attr('id') ;
                var statistical_frequency = Number(button_id.substr(button_id.length-1,1));
                $("#statistical_frequency").val(statistical_frequency);

                //添加属性class
                var $class = $('#'+button_id) ;
                $class.removeClass('btn-default').addClass('btn-primary') ; //添加
                $class.siblings().removeClass('btn-primary').addClass('btn-default') ; //其他同胞移除
        });

        $(document).ready(function () {
            if( rule_json ) {
                $("#frequency_"+rule_json['statistical_frequency']).removeClass('btn-default').addClass('btn-primary') ;
            }
        });

    </script>

    <!--  markdown文档 -->
    <script type="text/javascript">

        var markdown = {

            showArea:function( data ) {

                $('#edit_area').html('');       //文档编辑域重置
                $('.edit_save').remove();         //删除保存按钮

                var html = '';
                html +='<div id="doc_markdown" style="float: left;">';
                if( data ) {
                    html +='<textarea style="display:none;float: left;">'+data.sound_code+'</textarea>';
                } else {
                    html +='<textarea style="display:none;float: left;"></textarea>';
                }
                html +='</div>';

                $('#edit_area').html(html);

                testEditor = editormd({
                    id      : "doc_markdown",
                    width   : "100%",
                    height  : 640,
                    watch : true,
                    saveHTMLToTextarea : true,
                    path    : "/libs/editor.md-master/lib/"
                });

                var save_html = '<div style="text-align: center;margin-bottom: 50px;" class="edit_save">';
                save_html += '<button type="button" class="layui-btn btn-primary btn-lg" onclick="markdown.saveMarkdown();">保存</button>';
                save_html += '</div>';
                $('#edit_area').after(save_html);

            },

            //保存markdown文档
            saveMarkdown:function() {

                var dt = E.getFormValues('bi-form');
                var sound_code = testEditor.getMarkdown();
                var html = testEditor.getPreviewedHTML();//获取预览html代码
                var msg ='';

                if ( E.isEmpty(dt.table_name) ) {
                    msg += '统计源名称不能为空<br>';
                }

                if ( dt.statistical_frequency == '' ) {
                    msg += '请选择统计频率<br>';
                }

                if ( E.isEmpty(dt.description) ) {
                    msg += '统计表描述不能为空<br>';
                }

                if ( E.isEmpty( sound_code ) || E.isEmpty( html ) ) {
                    msg += '规则说明不能为空<br>';
                }

                //保存字段信息//
                dt.request = [];

                var is_error = false;

                $(".request_message").each( function () {
                    var request_data = { } ;

                    $(this).find("td").find("input").each( function () {
                        request_data[this.name] = this.value ;
                    });

                    if ( E.isEmpty( request_data.field_name ) &&  E.isEmpty( request_data.field_type ) &&  E.isEmpty( request_data.field_remark ) ) {
                        return true;
                    } else {
                        if ( E.isEmpty( request_data.field_name ) ) {
                            layer.alert('请输入字段名称',{icon:2,offset:'70px'});
                            $(this).find('input[name="field_name"]').focus();
                            is_error = true;
                            return false;
                        }
                        if ( E.isEmpty( request_data.field_type ) ) {
                            layer.alert('请输入字段类型',{icon:2,offset:'70px'});
                            $(this).find('input[name="field_type"]').focus();
                            is_error = true;
                            return false;
                        }
                        if ( E.isEmpty( request_data.field_remark ) ) {
                            layer.alert('请输入字段说明',{icon:2,offset:'70px'});
                            $(this).find('input[name="field_remark"]').focus();
                            is_error = true;
                            return false;
                        }
                    }

                    dt.request.push( request_data ) ;
                });

                if ( is_error ) {
                    return false;
                }

                dt.request = JSON.stringify(dt.request);

                if ( msg ) {
                    layer.alert(msg,{icon:2,offset:'70px'});
                    return false;
                }

                layer.confirm( '您确定保存该统计规则吗？',{icon:3,offset:'50px'},function(index){

                    layer.close(index);

                    E.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/webi/backend/dataset/save',
                        data:{
                            table_id :table_id,
                            dt:dt,
                            sound_code :sound_code,
                            html :html
                        },
                        success: function (res) {
                            if ( res.code == 200 ) {

                                window.parent.stat.search();

                                if( table_id >0 ){
                                    layer.alert( res.message,{icon:1,time:1500,offset:'50px'},function(){
                                        E.layerClose();
                                    } );
                                    setTimeout('E.layerClose();',1500);
                                }else{
                                    markdown.clearUp();
                                    layer.alert( res.message,{icon:1,time:1500,offset:'50px'});
                                }
                            } else {
                                layer.alert( res.message,{icon:2,offset:'50px'});
                            }
                        }
                    });


                })
                
            },
            
            clearUp:function () {
                $("#table_name").val('');
                $("#statistical_frequency").val('');
                $(".frequency_type").find('button').removeClass('btn-primary').addClass('btn-default');
                $("#description").val('');
                $(".fields").find('.request_add').remove();
                $("#request_tr input").val( '' );
                rule_json = 0;
                markdown.showArea(rule_json);
            }

        };

        //显示
        markdown.showArea(rule_json);

    </script>
    <!--  markdown文档 -->

@endsection