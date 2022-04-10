<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/css/webi/bi.tv.comm.css">
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <style>
        .qr-code{
            width: 35px;
            height: 35px;
            background: url("/images/webi/qrcode.png") no-repeat center center;
            background-size:100% 100%;
        }
    </style>
    @yield('css')
</head>
<body>
<div class="content">
    <div class="top-box">
        <div class="logo">
            <a href="/webi/tv/group/list"><img src="/images/webi/logo.png" alt="WeBI"></a>
        </div>
        <div class="top-icon">
            <div class="search-box">
                <div class="search">
                    <input type="text" class="form-control" name="version_number" placeholder="搜索报表" id="search-text">
                    <i class="glyphicon glyphicon-search" onclick="_G_BI.search();"></i>
                </div>
            </div>
            <div class="code-button"><i class="qr-code" style="display: block;"></i></div>
            <div class="login">
                <div class="name" class="login-user">
                    <a href="javascript:(0)">{{$userName}}
                      <i class="glyphicon glyphicon-chevron-down"></i>
                    </a>
                </div>
                <div class="quite-webi" style="display:none;"><a href="/webi/tv/logout" class="quit">退出</a></div>
            </div>
        </div>
    </div>

    <div class="code" style="display:none;">
        <div class="close-QR"><i class="glyphicon glyphicon-remove" style="font-size: 20px;"></i></div>
        <div id="qrcode" class="qrcode">
        </div>
    </div>

    <div class="mian">
        @yield('content')
    </div>
</div>
</body>
<script type="text/javascript" src="/libs/jquery/jquery-2.2.2.min.js" language="JavaScript"></script>
<script type="text/javascript" src="/js/webi/webi.comm.js"></script>
<script type="text/javascript" src="/js/webi/app.webi.js"></script>
<script type="text/javascript" src="/libs/jquery-qrcode/jquery.qrcode.min.js" language="JavaScript"></script>
<script type="text/javascript">

    var PROJECTION = {};//扫码投影对象
    //生成ticket
    PROJECTION.ticket = function () {

        var IME = _G_BI.getMEI();//获取设备号

        //生成二维码
        $('#qrcode').html('').qrcode({
            text: IME,
            render   : "canvas",//设置渲染方式
            width    : 256,     //设置宽度
            height   : 256     //设置高度
        });
        $('.code').css('display','block');
        $('.qrcode').find('canvas').css({width:'40%'});
    };

    (function(){

        //TV终端  显示二维码
        $('.code-button').on('click',function () {

            PROJECTION.ticket();//获取ticket

            //二维码关闭按钮事件
            $('.close-QR').on('click',function(){
                $('.code').css('display','none');
                $('.mian').css('display','block');
            });

            $('.mian').css('display','none');//隐藏页面报表数据

        });

        //用户名下拉框
        $(".login").on('click',function () {
            $(".quite-webi").toggle();
        });

        setTimeout($('.form-control').focus(),50);
    })();
</script>
@yield('js')

</html>