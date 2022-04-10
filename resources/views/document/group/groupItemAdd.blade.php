@extends('backend')

@section('title')
    项目详情
@endsection

@section('css')
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
                    <a href="javascript: void(0);"><span id="cs_title">新增项目详情</span></a>
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
                    <input type="hidden" id="item_id" name="item_id" value="0">

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="item_name"><span class="red">*</span>项目名称：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="item_name" name="item_name" value="">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="item_link"><span class="red">*</span>项目链接：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="item_link" name="item_link"  value="">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="sort_order"><span class="red">*</span>排序值：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="sort_order" style="width:100px;" name="sort_order" value="{{$sort_order or ''}}">
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <input  type="button" class="layui-btn btn-priamry"  onclick="line.save()" id="cs_save" value="保存" style="margin-left:12px;"/>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">

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

                layer.confirm('你确定要保存项目信息吗？',{icon:3},function () {

                    var loading = layer.load();
                    E.ajax({
                        type:'post',
                        url:'/webi/doc/group/item/save',
                        data:data,
                        success:function (o) {
                            layer.close(index);
                            if ( o.code == 200 ) {
                                layer.alert(o.message,{icon:1,offset:'70px',time:1500});
                                $("#item_name").val('');
                                $("#item_link").val('');
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
