<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 12px;
            font-family: Helvetica, STHeiti, "Microsoft YaHei", Verdana, Arial, Tahoma, sans-serif;
            background: #fff;
            position:relative;
            overflow: hidden;
        }
    </style>

    @yield('css')

</head>

<body>

@yield('content')

</body>

<script src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script src="/libs/layer/layer.js"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>
<script src="/libs/bootstrap/js/bootstrap.min.js"></script>
<script src="/libs/ebsig/base.js?v=20170206"></script>
<script type="text/javascript">
   // 时间插件
    layui.use(['laydate']);
</script>
@yield('js')

</html>