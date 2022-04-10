@extends('backend')

@section('title')
    分组管理
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/layui/layui.css">
    <link rel="stylesheet" href="/libs/layui/css/modules/laydate/laydate.css">
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
    <style>
        .col-sm-2 {
            width:26%!important;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav " style="display: block;">
            <ul>
                <li>
                    <a href="javascript: void(0);"><span>{{$title}}</span></a>
                </li>
            </ul>
            <div class="top-back">
                <button class="layui-btn layui-btn-primary layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper">
        <div class="layui-row" style="margin-top: 20px;">
            <div id="time_line">
                <form id="start_form" onsubmit="return false;" class="layui-form" role="form">

                    <input type="hidden" name="group_id" id="group_id" value="{{$edit_data['group_id'] or 0}}">

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="group_name"><span class="red">*</span>分组名称：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="group_name" name="group_name" value="{{$edit_data['group_name'] or ''}}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="sort_order"><span class="red">*</span>排序值：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="sort_order" style="width:100px;" name="sort_order" value="{{$edit_data['sort_order'] or $sort_order}}">
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="layui-row">
            <div class="layui-col-lg12">

                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <input  type="button" class="layui-btn btn-primary"  onclick="line.save()" id="cs_save" value="保存" style="margin-left:12px;"/>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">
        var _id = {!! "'".$_id."'" !!}
        var line = {
            //保存
            save:function () {
                var data = E.getFormValues('start_form');

                var msg ='';

                if(!data.group_name ) {
                    msg += '请输入分组名称<br/>' ;
                }
                if(!data.sort_order ) {
                    msg += '请输入排序值<br/>' ;
                }
                if(!E.isNum(data.sort_order) ) {
                    msg += '请输入非负整数的排序值<br/>' ;
                }

                if(msg){
                    layer.alert(msg,{icon:2,offset:'50px'});
                    return false;
                }
                var index  = layer.load() ;

                layer.confirm('你确定要保存分组信息吗？',{icon:3},function () {
                    var loading = layer.load();
                    E.ajax({
                        type:'post',
                        url:'/webi/doc/group/save',
                        data:{
                            "_id":_id,
                            "group_id":data.group_id,
                            "group_name":data.group_name,
                            "sort_order":data.sort_order,
                        },
                        success:function (o) {
                            layer.close(index);
                            if ( o.code == 200 ) {
                                layer.alert(o.message,{icon:1,offset:'70px',time:1500});
                                if($("#group_id").val()==0){
                                    $("#group_name").val('');
                                    $("#sort_order").val('');
                                }
                                layer.close(loading);
                            } else {
                                layer.alert(o.message,{icon:2,offset:'70px'});
                                return false;
                            }
                        }
                    })
                })

            }
        };


    </script>
@endsection
