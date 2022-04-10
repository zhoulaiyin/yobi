<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/css/webi/bi.wap.comm.css">
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <style>
        .scan{
            background: url("/images/webi/scan.png") no-repeat center center;
            width: 30px;
            height: 30px;
            background-size:100% 100%;
        }
    </style>
    @yield('css')
</head>
<body>
<div class="content">
    <div class="top-box">
        <div class="logo">
            <a href="/webi/wap/group/list"><img src="/images/webi/webi_logo.jpg" alt="WeBI"></a>
        </div>
        <div class="top-icon">
            <div class="code-button"><i class="scan" style="display: block;"></i></div>
            <div class="login">
                <div class="name" class="login-user"><a href="javascript:(0)">{{$userName}}</a>
                    <img src="/images/webi/biedit/icon-arr3.png">
                </div>
                <div class="quite-webi" style="display:none;"><a href="/webi/wap/logout" class="quit">退出</a></div>
            </div>
            <div class="home">
                <a href="/webi/wap/group/list"><img src="/images/webi/homepage.png"></a>
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

    (function(){

       //手机终端  显示扫一扫
        $('.code-button').on('click',function () {
            window.external.getScan();
        });


        //用户名下拉框
        $(".login").on('click',function () {
            $(".quite-webi").toggle();
        });
    })();

    /*
     * app端调用  传设备编码
     */
    function receive_device(device_id){
        BI.cookie.set("machine_num",device_id,60 * 60 * 24);//设置设备号cookie
    }
</script>
@yield('js')

</html>