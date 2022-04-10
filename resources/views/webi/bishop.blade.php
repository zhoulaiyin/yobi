<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/logo.ico" type="img/x-ico" />
    <title>WeBI数据云 @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <link href="/css/webi/shop.show.css" rel="stylesheet">
    <style>
        body { margin:0;font-size: 12px;font-family: Helvetica, STHeiti, "Microsoft YaHei", Verdana, Arial, Tahoma, sans-serif; background: #f2f2f2; position:relative;}
    </style>

    @yield('css')

</head>

<body>

<div class='WeBI_main' id="parent_main"></div>
<div id="bi_content_html" style="display: none;">
    <div class="shop-grid" id="grid_$uid">
        <div class="bi-title" id="title_$uid"><a></a></div>
        <div class="bi-chart" data-id="$uid" id="chart_$uid"></div>
    </div>
</div>

@yield('content')

</body>


<script src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>
<script language="JavaScript" src="/js/charts/echarts.js" type="text/javascript"></script>
<script language="JavaScript" src="/js/charts/china.js" type="text/javascript"></script>
<script language="JavaScript" src="/js/charts/world.js" type="text/javascript"></script>
<script src="/js/charts/echarts-wordcloud.min.js"></script>
<script type="text/javascript" src="/js/func.js"></script>
<script type="text/javascript" src="/js/webi/webi.comm.js"></script>
<script type="text/javascript" src="/js/webi/v1.0/webi.min.js"></script>
<script src="/libs/bootstrap/js/bootstrap.min.js"></script>

@yield('js')

</html>