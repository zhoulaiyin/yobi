@extends('backend')

@section('title')
    用户管理
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
    <style>
        
        .bs-searchbox{
            width:100% !important;
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
                    <input class="layui-input" type="hidden" name="_id" id="_id" value="{{$edit_data['_id'] or ''}}">
                    <input class="layui-input" type="hidden" name="xb" id="xb" value="{{$edit_data['project_id'] or ''}}">
                    <input class="layui-input" type="hidden" name="project_name" id="project_name">
                    <div class="layui-form-item">
                        <label class="layui-form-label" for="user_id">项目名称：</label>
                        <div class="layui-input-block">
                        <select class="layui-select reset-bootstrap-select" id="project_id" name="project_id">
                            <option value="">请选择项目</option>
                        </select>
                        </div>
                    </div>

                    <div class="layui-form-item" >
                        <label class="layui-form-label" for="user_id"><span class="red">*</span>用户角色类型：</label>
                        <div class="layui-input-block">
                            <input type="radio"  name="role_id"  value="1" title="总部" @if(!empty($edit_data)&&$edit_data['role_id']==1) checked @endif lay-filter="template">
                            <input type="radio" name="role_id" value="2" title="分部" @if(!empty($edit_data)&&$edit_data['role_id']==2) checked @endif lay-filter="template">
                            <input type="radio"  name="role_id"  value="3" title="门店" @if(!empty($edit_data)&&$edit_data['role_id']==3) checked @endif lay-filter="template">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="role_bind">绑定内容：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="role_bind" name="role_bind" value="{{$edit_data['role_bind'] or ''}}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="user_id"><span class="red">*</span>用户名：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="user_id" name="user_id" value="{{$edit_data['user_id'] or ''}}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="true_name"><span class="red">*</span>姓名：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="true_name" name="true_name" value="{{$edit_data['true_name'] or ''}}">
                        </div>
                    </div>
                    @if (empty($edit_data['user_pwd']))
                        <div class="layui-form-item">
                            <label class="layui-form-label" for="user_pwd"><span class="red">*</span>用户密码：</label>
                            <div class="layui-input-block">
                                <input class="layui-input elem-date" type="password" id="user_pwd" name="user_pwd" value="{{$edit_data['user_pwd'] or ''}}">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label" for="user_pwds"><span class="red">*</span>确认密码：</label>
                            <div class="layui-input-block">
                                <input class="layui-input elem-date" type="password" id="user_pwds" name="user_pwds" value="{{$edit_data['user_pwd'] or ''}}">
                            </div>
                        </div>
                    @endif

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="mobile">手机号：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" maxlength="11" id="mobile" name="mobile" value="{{$edit_data['mobile'] or ''}}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label" for="email">email：</label>
                        <div class="layui-input-block">
                            <input class="layui-input elem-date" type="text" id="email" name="email" value="{{$edit_data['email'] or ''}}">
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
                        <input  type="button" class="layui-btn btn-primary"  onclick="line.save()" value="保存" style="margin-left:12px;"/>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('js')

    <script type="text/javascript" src="/libs/webuploader/webuploader.js"></script>
    <script type="text/javascript">
        layui.use(['form'], function() {
            var form = layui.form;

        });

        var Xb = {!! $xb !!}
        var project_data = {!! $project_data !!}

         $(document).ready(function () {
            if ( $('#xb').val() > 0 ) {  //修改默认选中
                $('select[name=project_id] option[value=' + $('#xb').val()+']').prop('selected', true);
                layui.use(['form'], function() {
                    layui.form.render('select');
                });
            }
        });

        if ( project_data ) {
            $.each( project_data , function(i,n){
                var  html = '<option value="'+ n.project_id + '" >'+ n.project_name + '</option>';
                $('#project_id').append(html);
            });
            layui.use(['form'], function() {
                layui.form.render('select');
            });
        }

        var line = {
                //保存
                save:function () {
                    var opts= $("#project_id").find("option:selected").text();
                    $('#project_name').val(opts);
                    var data = E.getFormValues('start_form');
                    if (E.isEmpty(data.role_id)) {
                        layer.alert('请选择用户角色类型', {icon: 2, offset: '70px'});
                        return false;
                    }
                    if (E.isEmpty(data.user_id)) {
                        layer.alert('请输入用户名', {icon: 2, offset: '70px'});
                        return false;
                    }
                    if (E.isEmpty(data.true_name)) {
                        layer.alert('请输入姓名', {icon: 2, offset: '70px'});
                        return false;
                    }
                    if(Xb==1){
                        if (E.isEmpty(data.user_pwd)) {
                            layer.alert('请输入密码', {icon: 2, offset: '70px'});
                            return false;
                        }
                        if (E.isEmpty(data.user_pwds)) {
                            layer.alert('请确认密码', {icon: 2, offset: '70px'});
                            return false;
                        }
                        if (data.user_pwd!=data.user_pwds) {
                            layer.alert('两次密码输入不一致', {icon: 2, offset: '70px'});
                            return false;
                        }
                    }

                    var index  = layer.load() ;
                    layer.confirm('你确定要保存用户信息吗？',{icon:3},function () {

                        var loading = layer.load();
                        E.ajax({
                            type:'post',
                            url:'/webi/backend/biuser/edit/save',
                            data:data,
                            success:function (o) {
                                layer.close(index);
                                if ( o.code == 200 ) {
                                    layer.alert(o.message,{icon:1,offset:'70px',time:1500});
                                    layer.close(loading);
                                    if(Xb==1){
                                        $('#project_id').val('');
                                        $('#email').val('');
                                        $('#mobile').val('');
                                        $('#user_pwds').val('');
                                        $('#user_pwd').val('');
                                        $('#true_name').val('');
                                        $('#user_id').val('');
                                        $('#role_bind').val('');
                                    }
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
