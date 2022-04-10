<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WePaaS应用平台</title>
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">
    <style>

        @font-face
        {
            font-family: logoFont;
            src: url('../../font/Architecture.ttf');
        }

        *{
            margin: 0;
            padding: 0;
        }
        body{
            position: absolute;
            top:0;
            left:0;
            bottom:0;
            right:0;
            background-color: #23262E!important;
        }
        ::-webkit-scrollbar{width:0px;}
        #particles-js{
            width:100%;
            z-index:0;
            /*position: absolute;*/
            padding: 0px;
            margin: 0px;

        }
        /*星座特效*/
        canvas {
            width: 100%;
            height: 100%;
            z-index:-1;
            /*position: fixed;*/
            /*top: 0px;*/
            /*left: 0px;*/
        }


        img{
            border:0;
        }
        .background-bg img{
            height:100%;
            width:100%;
            overflow: hidden;
            position:absolute;
            bottom:0;
            right:0;
            left:0;
            top:0;
        }
        .main-box{
            position: absolute;
            width:100%;
            height:100%;
        }
        .logo{
            padding: 27px;
            text-align: left;
            font-family: "logoFont";
            color: #0077D0;
            font-size: 42px;
        }
        .logo_btn{
            float: right;
            padding: 0 10px;
            background: #fff;
            border: none;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            color: #8A8A8A;
            display: block;
            cursor: pointer;
            line-height: 30px;
            font-size: 15px;
            margin: 15px;
            cursor:pointer;
        }
        .logo-content{
            position: absolute;
            top:0;
            left:0;
            right:0;
            bottom:0;
            overflow:auto;
        }
        .log-main{
            width: 355px;
            top: 40%;
            left: 50%;
            padding-bottom:15px;
            margin-bottom:25px;
            margin-top: -200px;
            margin-left: -170px;
            background-color: #fff;
            border-radius: 8px;
            -webkit-border-radius: 8px;
        }
        .log-main .title{
            text-align: center;
            margin-bottom: 10px;
            line-height: 60px;
        }
        .title img{
            width: 162px;
            height: 71px;
            margin: 0 auto;
            display: block;
            line-height: 77px;
        }
        .log-list{
            position: relative;
        }
        .log-list label{
            position: absolute;
            left: 11px;
            top:14px;
        }
        .input-text {
            display: block;
            width: 100%;
            height: 40px;
            line-height: 40px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            text-indent: 34px;
            border: none;
            border:1px solid #eee;
            margin-bottom:10px;
            padding-top: 3px;
        }
        input:-webkit-autofill { box-shadow: 0 0 0px 1000px white inset !important;}

        .sm-control{
            width:58%;
        }
        /*忘记密码*/
        .layui-form-checked, .layui-form-checked:hover{
            color: #1e9eff !important;
        }
        .layui-form-checked[lay-skin=primary] i {
            border-color:  #1e9eff !important;
            background-color:  #1e9eff !important;
            color: #fff;
        }
        .layui-form-checked>span{
            font-size:13px;
        }
        .yzm{
            position: absolute;
            right:0;
            top:0;
            height:44px;
            overflow: hidden;
        }
        .yzm>img{
            height:100%;
        }
        .btn-log{
            width:100%;
            height:40px;
            background: #6AABFA;
            border: none;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            color:#fff;
            display: block;
            cursor: pointer;
        }
        .forgetlink{
            color: #1E9EFF!important;
            font-size: 14px;
            text-decoration: none;
            margin-bottom: 10px;
            margin-right: 0;
            display: inline-block;
            right: 0;
            float: right;
            line-height:18px;
        }
        .error-text{
            color:#f75404;
            font-size:12px;
            text-align: center;
        }
        .wechatIcon{
            text-align: center;
            padding-bottom:15px;
        }

        .border{
            color: #A1A3A2;
            display: block;
            padding: 10px 0;
        }
        .wechatIcon .layui-icon{
            font-size:50px!important;
            color:#3CB035;
        }
        .log-cont .invalid-msg {
            display: none;
            margin: 15px 0;
            background-color: #FFEEEC;
            color: #E64340;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            font-size: 14px;
            padding: 8px 12px;
        }
        .text-primary{
            color: #0077D0!important;
            cursor:pointer;
        }

        .msg_btn{
            padding: 5px;
            height: 44px;
            border: none;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            display: block;
            cursor: pointer;
            color: #909399;
        }
        .layui-form-checked[lay-skin=primary] i{
            background: #6AABFA!important;
            border-color:#6AABFA!important;
        }
        .text-logo{
            color:#6AABFA!important;
            margin: 0!important;
            cursor: pointer;
        }
        /*media*/
        @media only screen and (min-width:1201px){
            .log-main{
                position: absolute;
            }
            .log-cont{
                margin: 0 40px 10px;
            }
        }
        @media screen and (max-width: 768px) {
            #particles-js{
                height:100%;
                position:absolute;
            }
            .log-main{
                width: 80%!important;
                margin: 0 auto;
                left: 50%;
            }
            .log-cont {
                margin: 0 18px 10px;
            }
            .log-cont .log-list{
                margin-bottom:20px;
            }
            .log-cont .layui-form{
                margin-bottom:0;
            }
        }
    </style>
</head>


<body  style="overflow: hidden;">
<div id="particles-js">
    <div class="main-box">
        <div class="logo-content">
            <div></div>
            <div class="log-main">
                <div class="title">
                    <img src="/images/webi/webi_logo.jpg" alt="WeBI">
                </div>
                <div class="log-cont">
                    <div class="invalid-msg" id="error"></div>
                    <div class="log-list">
                        <input type="text" class="input-text" id="username" maxlength="20" placeholder="设置用户名，4到20位字母或数字">
                        <label><img src="/images/m/common/username.png"></label>
                    </div>
                    <div class="log-list">
                        <input type="text" class="input-text" id="true_name" placeholder="设置姓名">
                        <label><img src="/images/m/common/username.png"></label>
                    </div>
                    <div class="log-list">
                        <input type="text" class="input-text" id="mobile" placeholder="手机号码">
                        <label><img src="/images/m/common/shoujihao.png"></label>
                    </div>
                    <div class="log-list">
                        <input type="text" class="sm-control input-text" id="sms_yzm" placeholder="短信验证码">
                        <label><img src="/images/m/common/msg.png"></label>
                        <button class="yzm msg_btn">获取短信验证码</button>
                    </div>
                    <div class="log-list">
                        <input type="text" class="sm-control input-text" id="yzm" maxlength="5" placeholder="验证码">
                        <label><img src="/images/m/common/yzm.png"></label>
                        <a href="javascript:;" class="yzm" id="change-yzm"><img src="/captcha"></a>
                        <p class="error-text" id="error"></p>
                    </div>
                    <div class="log-list">
                        <input type="password" autocomplete="off" class="input-text" id="user_pwd" placeholder="6到24位字母、数字，区分大小写">
                        <label><img src="/images/m/common/password.png"></label>
                    </div>
                    <div class="log-list">
                        <input type="password" autocomplete="off" class="input-text" id="user_pwds" placeholder="再次确认密码">
                        <label><img src="/images/m/common/password.png"></label>
                    </div>

                    <div class="log-list layui-form">
                        <input type="checkbox"  id="remember" name="" lay-skin="primary"  checked>
                        <span style="font-size:12px;">我已阅读并同意<a class="text-primary">服务协议</a>和<a class="text-primary">隐私政策</a></span>
                    </div>

                    <br>
                    <button class="btn-log" id="login">注册</button>
                </div>
                <span style="float:right; margin-right: 10px;">已有账号，<a  class="text-primary" style="float:right" onclick="skipUrl('/webi/shop/login')">去登录 ></a></span>
            </div>
        </div>
    </div>
</div>

<script src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script src="/libs/layer-v3.0.3/layer.js"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>
<script src="/js/func.js"></script>
<script src="/js/global.js"></script>
<script type="text/javascript" src="/libs/snowbg/js/particles.min.js"></script>
<script type="text/javascript" src="/libs/snowbg/js/app.js"></script>
<script src="/js/application/app.js?v=201908261750"></script>

<script>
    layui.use('form', function(){
        var form = layui.form;
    });

    $(document).on('click', '#change-yzm', function(){
        P.changeYzm();
    }).on('keydown', '#yzm', function(e){
        if (e.keyCode == 13) {
            login();
        }
    }).on('click', '#login', function(){
        login();
    });

    function login() {
        var error = $('#error');
        var mobile = $.trim($('#mobile').val());
        var username = $.trim($('#username').val());
        var true_name = $.trim($('#true_name').val());
        var user_pwd = $.trim($('#user_pwd').val());
        var user_pwds = $.trim($('#user_pwds').val());
        var yzm = $.trim($('#yzm').val());
        var sms_yzm = $.trim($('#sms_yzm').val());

        if (username == '') {
            P.error.open('用户名不能为空');
            return false;
        }
        if (username.length < 4 || username.length > 20) {
            P.error.open('请输入正确长度的用户名');
            return false;
        }
        if (true_name == '') {
            P.error.open('姓名不能为空');
            return false;
        }

        if (mobile == '') {
            P.error.open('手机号码不能为空');
            return false;
        }

        if (!E.isMobile(mobile)) {
            P.error.open('手机号格式有误');
            return false;
        }
//        if (F.isEmpty(sms_yzm)) {
//            P.error.open('短信验证码不能为空');
//            return false;
//        }
        if (yzm == '') {
            P.error.open('验证码不能为空');
            return false;
        }
        if (yzm.length != 5) {
            P.error.open('请输入5位验证码');
            return false;
        }
        if (user_pwd == '') {
            P.error.open('密码不能为空');
            return false;
        }
        if (user_pwds == '') {
            P.error.open('确认密码不能为空');
            return false;
        }
        if (user_pwds != user_pwd) {
            P.error.open('密码输入不一致');
            return false;
        }


        P.error.close();

        E.ajax({
            type: 'get',
            url: '/webi/shop/register/do',
            data: {
                mobile: mobile,
                username: username,
                true_name: true_name,
                user_pwd: user_pwd,
                again_pwd: user_pwds,
                yzm: yzm,
                sms_yzm: sms_yzm
            },
            success: function (res) {
                if (res.code != 200) {
                    P.error.open(res.message);
                    P.changeYzm();
                    $('#yzm').val('');
                    return false;
                }

                self.location = '/webi/shop/login';
            }
        });
    };

    //获取短信验证码
//    function msg_btn(){
//
//    };

    function skipUrl(url){
        self.location = url
    };

    var P = {
        error: {
            open: function(err_msg) {
                $('#error').show().text(err_msg);
            },
            close: function() {
                $('#error').hide().text('');
            }
        },
        changeYzm: function () {
            $('#change-yzm').find('img').attr('src', '/captcha?t=' + Math.floor(Math.random() * 1000000));
        },

        init: function() {
            if (F.getCookie('MDEL_REMEMBER_USERNAME') && F.getCookie('MDEL_REMEMBER_TRUENAME') && F.getCookie('MDEL_REMEMBER_PWD')) {
                $('#username').val(F.getCookie('MDEL_REMEMBER_USERNAME'));
                $('#true_name').val(F.getCookie('MDEL_REMEMBER_TRUENAME'));
                $('#user_pwd').val(F.getCookie('MDEL_REMEMBER_USERPWD'));
            }

        },
    };

    $(document).ready(function(){
        P.init();
    });

</script>

</body>
</html>

