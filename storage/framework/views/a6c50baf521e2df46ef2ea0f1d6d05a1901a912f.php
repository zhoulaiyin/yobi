<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/logo.ico" type="img/x-ico" />
    <title>WeBI数据云</title>
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">
    <link href="/css/webi/login.css" rel="stylesheet">
</head>
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
    #particles-js{
        width:100%;
        background-color: #23262E!important;
        z-index:0;
        padding: 0px;
        margin: 0px;

    }
    /*星座特效*/
    canvas {
        width: 100%;
        height: 100%;
        z-index:-1;
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
        text-align: center;
        font-family: "logoFont";
        color: #0077D0;
        font-size: 57px;
    }
    .log-main{
        width: 355px;
        top: 50%;
        left: 50%;
        position: absolute;
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
        top:12px;
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
    .log-cont{
        margin: 0 40px 40px
    }
    .sm-control{
        width:50%;
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
        height:40px;
        overflow: hidden;
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
        margin-bottom:15px;
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
    .layui-form-checked[lay-skin=primary] i{
        background: #6AABFA!important;
        border-color:#6AABFA!important;
    }
    .text-logo{
        color:#6AABFA!important;
        margin: 0!important;
        cursor: pointer;
    }
</style>

<body  style="overflow: hidden;">
<div id="particles-js">
    <div class="main-box">
        <div class="log-main">
            <div class="title">
                <img src="/images/webi/webi_logo.jpg" alt="WeBI">
            </div>
            <div class="log-cont">
                <div class="invalid-msg" id="error"></div>
                <div class="log-list">
                    <input type="text" class="input-text" id="user_name" placeholder="账户">
                    <label><img src="/images/m/common/username.png"></label>
                </div>
                <div class="log-list">
                    <input type="password" class="input-text" id="pwd" placeholder="密码">
                    <label><img src="/images/m/common/password.png"></label>
                </div>

                <div class="log-list">
                    <input type="text" class="sm-control input-text" id="yzm" maxlength="5" placeholder="验证码">
                    <label><img src="/images/m/common/yzm.png"></label>
                    <a href="javascript:;" class="yzm" id="change-yzm"><img src="/yzm"></a>
                    <p class="error-text" id="error"></p>
                </div>
                <div class="log-list layui-form">
                    <input type="checkbox" id="remember" name="" title="记住密码" lay-skin="primary"  checked>
                    <span style="float:right; margin-right: 10px; margin-top: 7px;" class="text-logo" onclick="skipUrl('/webi/shop/register')">立即注册</span>
                </div>

                <br>
                <button class="btn-log" id="login">登录</button>
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
        var user_name = $.trim($('#user_name').val());
        var pwd = $.trim($('#pwd').val());
        var yzm = $.trim($('#yzm').val());

        if (user_name == '') {
            P.error.open('账户不能为空');
            return false;
        }
        if (pwd == '') {
            P.error.open('密码不能为空');
            return false;
        }

        if (yzm == '') {
            P.error.open('验证码不能为空');
            return false;
        }

        if (yzm.length != 5) {
            P.error.open('请输入5位验证码');
            return false;
        }

        P.error.close();

        $.ajax({
            type: 'GET',
            url: '/webi/shop/login/do',
            data: {
                login_name: user_name,
                password: pwd,
                yzm:yzm
            },
            dataType: 'json',
            success: function (res) {
                if (res.code != 200) {
                    P.error.open(res.message);
                    P.changeYzm();
                    $('#yzm').val('');
                    return false;
                }

                if ($('#remember').attr('checked')) {
                    P.setRem(user_name, pwd);
                } else {
                    P.delRem();
                }

                self.location = res.data.redirect_url;
            }
        });
    }

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
            $('#change-yzm').find('img').attr('src', '/yzm?t=' + Math.floor(Math.random() * 1000000));
        },
        init: function() {
            if (F.getCookie('MDEL_REMEMBER_NAME') && F.getCookie('MDEL_REMEMBER_PWD')) {
                $('#user_name').val(F.getCookie('MDEL_REMEMBER_NAME'));
                $('#pwd').val(F.getCookie('MDEL_REMEMBER_PWD'));
            }
        },
        setRem: function(name, pwd) {
            F.setCookie('MDEL_REMEMBER_NAME', name , 86400);
            F.setCookie('MDEL_REMEMBER_PWD', pwd , 86400);
        },
        delRem: function() {
            F.setCookie('MDEL_REMEMBER_NAME', null , -1);
            F.setCookie('MDEL_REMEMBER_PWD', null , -1);
        },
    }

    function skipUrl(url){
        self.location = url
    };

    $(document).ready(function(){
        P.init();
    });

</script>

</body>
</html>
