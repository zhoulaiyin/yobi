<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WeBI数据云 - 控制中心</title>
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">
    <link href="/css/admin/main1.css?v=201807191514" rel="stylesheet">
</head>

<body>

<div class="layui-layout layui-layout-admin">

    <!-- 头部导航 -->
    <div class="layui-header header header-top" >
        <div class="layui-main">
            <a class="logo" href="#">
                <img src="/images/webi/logo.png" style="width:180px;height:72px">
            </a>

            <ul class="topGroup layui-nav" style="left:200px;">
                @foreach($first_group as $id => $group_name)
                    @if ( $select_group_id == $id )
                        <li class="layui-nav-item layui-this"><a href="/admin/dashboard/{{$id}}">{{$group_name}}</a></li>
                    @else
                        <li class="layui-nav-item"><a href="/admin/dashboard/{{$id}}">{{$group_name}}</a></li>
                    @endif
                @endforeach
            </ul>

            <ul class="layui-nav">
                <li class="layui-nav-item" lay-unselect="">
                    <a href="javascript:;">{{$user_name}}</a>
                    <dl class="layui-nav-child">
                        <dd><a href="/admin/logout?redirect_url=/admin">退了</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>

    <!-- 左侧菜单 -->
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul id="nav" class="layui-nav layui-nav-tree" lay-filter="menu">
                @foreach($menu as $m)
                    <li class="layui-nav-item">

                        @if ( !empty($m['url']) )
                            <a href="{{$m['url']}}" target="mainFrame">{{$m['name']}}</a>
                        @else
                            @if ( !empty($m['list']) )
                                <a href="javascript:;">{{$m['name']}}</a>
                                <dl class="layui-nav-child">
                                    @foreach($m['list'] as $list)
                                        <dd><a href="{{$list['url']}}" target="mainFrame">{{$list['name']}}</a></dd>
                                    @endforeach
                                </dl>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- 页面内容 -->
    <div class="layui-body layui-tab-content site-content site--body">
        <iframe frameborder="0" id="mainFrame" name="mainFrame" width="100%" height="100%" src="/admin/index"></iframe>
    </div>

</div>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>
<script>

    layui.use('element', function(){
        var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
        //监听导航点击
        element.on('nav(menu)', function(elem){
            elem.parent().siblings().removeClass('layui-nav-itemed');
        });
    });

    $('.app-user-info').mouseenter(function() {
        $(this).find('.user-dropdown').fadeIn(500);
    }).mouseleave(function() {
        $(this).find('.user-dropdown').fadeOut(500);
    });

</script>
</body>
</html>
