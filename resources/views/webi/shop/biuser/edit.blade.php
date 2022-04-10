@extends('webi.layout')

@section('title')
    用户管理
@endsection

@section('css')
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
    <style>
        
        .bs-searchbox{
            width:100% !important;
        }
        #wrapper {
            margin: 10px;
            padding: 15px;
            background: #fff;
            min-height: 700px;
        }
        .layui-form-switch{
            margin-top:0;
        }
        #bind {
            display: none;
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
                <button class="btn btn-default layer-go-back" role="button">返回</button>
            </div>
        </nav>
    </div>

    <div id="wrapper">
        <div class="row layui-form" style="margin-top: 20px;">
            <div id="time_line">
                <form id="start_form" onsubmit="return false;" class="form-horizontal" role="form">
                    <input type="hidden" name="uid" id="uid" value="{{$edit_data['id'] or ''}}">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="user_id"><span class="red">*</span>用户名：</label>
                        <div class="col-sm-3">
                            <input class="form-control elem-date" type="text" id="user_id" name="user_id" value="{{$edit_data['user_id'] or ''}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="true_name"><span class="red">*</span>姓名：</label>
                        <div class="col-sm-3">
                            <input class="form-control elem-date" type="text" id="true_name" name="true_name" value="{{$edit_data['true_name'] or ''}}">
                        </div>
                    </div>
                    @if (empty($edit_data['user_pwd']))
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_pwd"><span class="red">*</span>用户密码：</label>
                            <div class="col-sm-3">
                                <input class="form-control elem-date" type="password" id="user_pwd" name="user_pwd" value="{{$edit_data['user_pwd'] or ''}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_pwds"><span class="red">*</span>确认密码：</label>
                            <div class="col-sm-3">
                                <input class="form-control elem-date" type="password" id="user_pwds" name="user_pwds" value="{{$edit_data['user_pwd'] or ''}}">
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="mobile">手机号：</label>
                        <div class="col-sm-3">
                            <input class="form-control elem-date" type="text" maxlength="11" id="mobile" name="mobile" value="{{$edit_data['mobile'] or ''}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="email">email：</label>
                        <div class="col-sm-3">
                            <input class="form-control elem-date" type="text" id="email" name="email" value="{{$edit_data['email'] or ''}}">
                        </div>
                    </div>

                    <div class="form-group" >
                        <label class="col-sm-2 control-label" for="user_id"><span class="red">*</span>用户角色类型：</label>
                        <div class="col-sm-3">
                            <input type="radio"  name="role_id"  value="1" title="总部" @if(!empty($edit_data)&&$edit_data['role_id']==1) checked @endif lay-filter="template">
                            <input type="radio" name="role_id" value="2" title="分部" @if(!empty($edit_data)&&$edit_data['role_id']==2) checked @endif lay-filter="template">
                            <input type="radio"  name="role_id" value="3" title="门店" @if(!empty($edit_data)&&$edit_data['role_id']==3) checked @endif lay-filter="template">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="email">用户权限：</label>
                        <div class="col-sm-3" style="padding:5px;">
                            管理员&nbsp;&nbsp;<input type="checkbox" @if(!empty($user_permission)) @foreach($user_permission as $g) @if(in_array($g,array(1,'1')))  checked @endif @endforeach @endif value="1" name="user_permission" lay-skin="switch" lay-text="ON|OFF">&nbsp;&nbsp;&nbsp;
                            报表编辑者&nbsp;&nbsp;<input type="checkbox" @if(!empty($user_permission)) @foreach($user_permission as $g) @if(in_array($g,array(2,'2')))  checked @endif @endforeach @endif  id="bb_bj" value="2" name="user_permission" lay-skin="switch" lay-text="ON|OFF">
                        </div>
                    </div>

                    <div class="form-group" id="bind" @if(!empty($edit_data)&&$edit_data['role_id']>1) style="display: block;" @endif >
                        <label class="col-sm-2 control-label" for="role_bind"><span class="red">*</span>所属门店：</label>
                        <div class="col-sm-3">
                            <textarea class="form-control elem-date" style="height:120px;" type="text" id="role_bind" name="role_bind" placeholder="如果需要绑定多个门店号,请换行隔开"></textarea>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-3" style="margin-top: 50px;">
                        <input  type="button" class="btn btn-success"  onclick="line.save()" value="保存" style="margin-left:12px;"/>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('js')

    <script type="text/javascript" src="/libs/webuploader/webuploader.js"></script>
    <script type="text/javascript">
        var role_bind = {!! $role_bind !!}
//
        if(!E.isEmpty(role_bind)){
            var reg = new RegExp(",","g");

            $('#role_bind').html(role_bind.replace(reg,"\n"));
        }
        layui.use(['form'], function() {

            var form = layui.form;

            form.on('switch', function(data){

                if(data.value==1&&data.elem.checked){
                    $("#bb_bj").prop('checked',true);
                    $('#bb_bj').attr("disabled",true);

                    form.render('checkbox');
                }else{
                    $('#bb_bj').attr("disabled",false);

                    form.render('checkbox');
                }

            });

            form.on('radio', function(data){

                if(data.value==2||data.value==3){
                    $('#bind').show();
                }else{
                    $('#bind').hide();
                }

            });


        });

        var line = {
                //保存
                save:function () {
                    var data = E.getFormValues('start_form');

                    if (E.isEmpty(data.user_id)) {
                        layer.msg('请输入用户名', {icon: 2, offset: '70px'});
                        return false;
                    }

                    if (E.isEmpty(data.true_name)) {
                        layer.msg('请输入姓名', {icon: 2, offset: '70px'});
                        return false;
                    }
                    if (!E.isEmpty(data.mobile)) {
                        if (!E.isMobile(data.mobile)) {
                            layer.msg('请输入正确的手机号', {icon: 2, offset: '70px'});
                            return false;
                        }
                    }
                    if (!E.isEmpty(data.email)) {
                        if (!E.isEmail(data.email)) {
                            layer.msg('请输入正确的邮箱', {icon: 2, offset: '70px'});
                            return false;
                        }
                    }

                    if(E.isEmpty($('#uid').val())){
                        if (E.isEmpty(data.user_pwd)) {
                            layer.msg('请输入密码', {icon: 2, offset: '70px'});
                            return false;
                        }
                        if (E.isEmpty(data.user_pwds)) {
                            layer.msg('请确认密码', {icon: 2, offset: '70px'});
                            return false;
                        }
                        if (data.user_pwd!=data.user_pwds) {
                            layer.msg('两次密码输入不一致', {icon: 2, offset: '70px'});
                            return false;
                        }
                    }

                    if (E.isEmpty(data.role_id)) {
                        layer.msg('请选择用户角色类型', {icon: 2, offset: '70px'});
                        return false;
                    }

                    if( data.role_id>1 ){
                        if (E.isEmpty(data.role_bind)) {
                            layer.msg('请输入所属门店', {icon: 2, offset: '70px'});
                            return false;
                        }
                        var strContent = document.getElementById('role_bind').value;

                        data.role_bind = strContent.replace(/\n|\r\n/g, ',');
                    }

                    layer.confirm('你确定要保存用户信息吗？',{icon:3},function (index) {
                        layer.close(index);
                        E.ajax({
                            type:'post',
                            url:'/webi/design/biuser/edit/save',
                            data:data,
                            success:function (o) {

                                if ( o.code == 200 ) {

                                    if(E.isEmpty(data.uid)){

                                        $('#email').val('');
                                        $('#mobile').val('');
                                        $('#user_pwds').val('');
                                        $('#user_pwd').val('');
                                        $('#true_name').val('');
                                        $('#user_id').val('');
                                        $('#role_bind').val('');

                                    }

                                    layer.msg(o.message,{icon:1,offset:'70px',time:1500});

                                } else {
                                    layer.msg(o.message,{icon:2,offset:'70px'});
                                    return false;
                                }
                            }
                        })
                    })

                }
            };


    </script>
@endsection
