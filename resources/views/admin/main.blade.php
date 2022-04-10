<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/logo.ico" type="img/x-ico" />
    <title>WeBI数据云 - 控制中心</title>
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin/index.css?v=2017030316" type="text/css">

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
    <aside class="app-sidebar"{{ isset($dashboard) ? '' : ' style=width:100px;' }}>

        <!-- 左导航 -->
        <div class="app-first-sidebar">
            <div>

                <a class="team-logo-wrap" href="javascript:void (0);">
                    <div class="team-logo">
                    </div>
                </a>

                <nav>
                    <ul class="clearfix">
                        @foreach($group as $group_id=>$g)
                            <li id="WeBI-{{$group_id}}">
                                <a href="/admin/dashboard/{{$group_id}}">
                                    <span>{{$g['name']}}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- 左底导航弹层 -->
        <div class="app-user-info">
            <span class="user-name">{{$user_name}}</span>
            <div class="user-dropdown">
                <div class="user-dropdown-meta">
                    <div>{{$user_name}}</div>
                    <div>{{$mobile}}</div>
                </div>
                <div class="user-dropdown-select">
                    <a href="/admin/logout?redirect_url=/admin">退出</a>
                </div>
            </div>
        </div>

    @if (isset($dashboard))
        <!-- 右导航 -->
            <div class="app-second-sidebar">
                <div>
                    <div class="second-sidebar-title">
                        {{$menu_name}}
                    </div>
                    <nav id="js-app-second-sidebar">
                        <ul>
                            @foreach($menus as $menu)
                                <li>
                                    <a href="{{ $menu['url'] }}" target="main-frame">{{ $menu['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
        @endif

    </aside>

    <!-- 详情内容 -->
    <div class="container"{{ isset($dashboard) ? '' : ' style=margin-left:100px;' }}>
        <iframe frameborder="0"  id="main-frame" name="main-frame" src="{{$default_url}}" width="100%" height="100%" ></iframe>
    </div>

</div>

</body>
<script src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script src="/libs/layer/layer.js"></script>
<script src="/libs/ebsig/base.js?v=20170206"></script>

<script>

    $(function() {

        $('.container').css('height', $(window).height() + 'px');

        var dashboard = '{{ isset($dashboard) ? $dashboard : "index" }}';
        $('#WeBI-' + dashboard).addClass('active');

        //二级菜单
        var second_sidebar_obj = $('#js-app-second-sidebar').find('li');
        if (second_sidebar_obj.length > 0) {
            second_sidebar_obj.eq(0).addClass('active');
            second_sidebar_obj.click(function () {
                second_sidebar_obj.removeClass('active').eq($(this).index()).addClass('active');
            });
        }

    });

</script>

</html>