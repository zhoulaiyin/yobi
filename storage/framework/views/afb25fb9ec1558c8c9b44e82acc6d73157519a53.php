<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/logo.ico" type="img/x-ico" />
    <title>WeBI数据云 - 控制中心</title>
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/css/control/index.css?v=2017030316" type="text/css">
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">

    <style>
        .red {
            color: #ff0000;
        }
        .form-horizontal .form-group {
            margin-right: 0;
            margin-left: 0;
        }
    </style>
</head>

<body>

<!-- 内容 -->
<div class="global-content">
    <!-- 左主导航 -->

        <!-- 左导航 -->
        <div class="app-first-sidebar">
            <div>

                <a class="team-logo-wrap" href="javascript:void (0);">
                    <div class="team-logo">
                    </div>
                </a>
            </div>
        </div>

        <!-- 右导航 -->
            <div class="app-second-sidebar">
                <div>
                    <nav id="js-app-second-sidebar">

                        <ul class="layui-nav layui-bg-white nav-left"  lay-filter="">
                            <li class="layui-nav-item layui-this"><a lay-href="/index">首页</a></li>
                            <li class="layui-nav-item"><a href="">帮助中心</a></li>
                        </ul>
                        <ul class="layui-bg-white nav-right"  lay-filter="">
                            <button type="button"  class="layui-btn logoColor" onclick="skipUrl('/webi/shop/login')">
                                <span>登录</span>
                            </button>
                            <button type="button" class="layui-btn borderLogo" onclick="skipUrl('/webi/shop/register')">
                                <span>注册</span>
                            </button>
                            <button type="button" class="layui-btn layui-btn-primary">
                                <span>CN</span>
                            </button>
                        </ul>
                    </nav>
                </div>
            </div>

    </aside>

    <!-- 详情内容 -->
    <div class="container">
        <iframe frameborder="0"  id="main-frame" name="main-frame" src="" width="100%" height="100%" ></iframe>
    </div>

</div>

</body>
<script src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script src="/libs/layer/layer.js"></script>
<script src="/libs/ebsig/base.js?v=20170206"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>

<script>

    $(function() {
        $('.container').css('height', $(window).height()-$(".app-first-sidebar").height() + 'px');

        var dashboard = '<?php echo e(isset($dashboard) ? $dashboard : "index"); ?>';
        $('#WeBI-' + dashboard).addClass('active');

        $('.nav-left a').on('click',function(event){
            event.preventDefault();
            var $this = $(this);
            var url = $this.attr('lay-href');
            if( url && url!=='javascript:;' ){
                $('#main-frame').attr('src', url);
            }
        });
        $('.nav-left a').trigger('click');
    });
    function skipUrl(url){
        self.location = url
    };

    layui.use('element', function(){
        var element = layui.element;

    });
</script>

</html>