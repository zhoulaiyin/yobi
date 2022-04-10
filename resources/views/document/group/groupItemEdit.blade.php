@extends('backend')

@section('title')
    项目详情
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/layui/layui.css">
    <link rel="stylesheet" href="/libs/layui/css/modules/laydate/laydate.css">
    <style>
        .col-sm-2 {
            width:15%!important;
        }
        .col-sm-3 {
            width:32%!important;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav " style="display: block;">
            <ul>
                <li>
                    <a href="javascript: void(0);"><span>修改项目详情</span></a>
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
                    <input type="hidden" id="item_id" name="item_id" value="{{$edit_data['item_id'] or ''}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label" for="group_id">分组名称：</label>
                        <div class="layui-input-block">
                            <p style="margin-top:8px;font-size:14px;line-height:36px;">{{$name or ''}}</p>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="item_name"><span class="red">*</span>项目名称：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="item_name" name="item_name" value="{{$edit_data['item_name'] or ''}}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="item_link"><span class="red">*</span>项目链接：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="item_link" name="item_link"  value="{{$edit_data['item_link'] or ''}}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="sort_order"><span class="red">*</span>排序值：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="sort_order" style="width:100px;" name="sort_order" value="{{$edit_data['sort_order'] or ''}}">
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
        layui.use('form', function(){
            var form = layui.form;
        });
        var _id = {!! "'".$_id."'" !!}

        var line = {
            //保存
            save:function () {
                var data = E.getFormValues('start_form');

                var msg ='';

                if (!data.item_name) {
                    msg += '请输入项目名称<br/>';
                }

                if (!data.item_link) {
                    msg += '请选择项目链接<br/>';
                }

                if(msg){
                    layer.alert(msg,{icon:2,offset:'50px'});
                    return false;
                }
                var index  = layer.load() ;

                layer.confirm('你确定要修改项目信息吗？',{icon:3},function () {
                    var loading = layer.load();
                    E.ajax({
                        type:'post',
                        url:'/webi/doc/group/item/save',
                        data:{
                            "_id":data._id,
                            "item_id":data.item_id,
                            "item_name":data.item_name,
                            "item_link":data.item_link,
                            "sort_order":data.sort_order,
                        },
                        success:function (o) {
                            layer.close(index);
                            if ( o.code == 200 ) {
                                layer.alert(o.message,{icon:1,offset:'70px',time:1500});
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
