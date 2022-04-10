<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <title>WeBI</title>
    <link href="/libs/layui-2.1.2/css/layui.css" rel="stylesheet">
    <link href="/css/webi/tvLogin.css" rel="stylesheet">
</head>


<body>
<div class="wrapper">
    <div class="login-tv " id="login-box-tv">
        <div class="login-box-tv">
            <div class="login-content">
                <div class="logoin-box">
                    <div class="logoin">
                        <img src="/images/webi/webi_logo.jpg" alt="WeBI">
                        <div class="login-content" >
                            <div class="logoin-input">
                                <input name="login-name" id="login_name_wap" lay-verify="title" autocomplete="off" placeholder="账号" class="layui-input" type="text">
                            </div>
                            <div class="loginName" style="margin-top: 7px;"></div>
                            <div class="logoin-input">
                                <input name="password" id="password_wap" lay-verify="title" autocomplete="off" placeholder="密码" class="layui-input" type="password">
                            </div>
                            <div class="passWord" style="margin-top: 7px;"></div>
                            <button class="layui-btn layui-btn-normal">登录</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script type="text/javascript" src="/libs/layer/layer.js"></script>

<script type="text/javascript">
    $(function () {

        $('#login_name').focus();

        $('#login_name').keyup(function(){
            $('.loginName').html('');
        });

        $('#password').keyup(function(){
            $('.passWord').html('');
        });

        $('#login_name').focus(function(){
            $('input').removeAttr('class');
            $('#login_name').attr('class','in-edit');
        });

        $('#password').focus(function(){
            $('input').removeAttr('class');
            $('#password').attr('class','in-edit');
        });

    });

    $(function(){

        $('button').click(function(){

            var login_name = $.trim($('#login_name_wap').val());
            var password = $.trim($('#password_wap').val());

            if ( login_name == '' ) {
                $('.loginName').html('<p>请输入账号</p>');
                $('#login_name').focus();
                return false;
            }

            if (password == '') {
                $('.passWord').html('<p>请输入密码</p>');
                $('#password').focus();
                return false;
            }

            var layer_index = layer.load();
            $.ajax({
                type: 'GET',
                url:'/webi/shop/login/do',
                data:{
                    login_name : login_name,
                    password : password
                },
                dataType: 'json',
                success: function(obj) {
                    layer.close(layer_index);

                    if( obj.code != 200 ){
                        if( obj.code == 100001 || obj.code == 100004 ){
                            $('.loginName').html('<p>'+obj.message+'</p>');
                        } else if ( obj.code == 100002 || obj.code == 100005 ) {
                            $('.passWord').html('<p>'+obj.message+'</p>');
                        }
                        return false;
                    }
                    self.location = obj.data.redirect_url;
                }
            });

        });
    });
</script>

</body>
</html>
