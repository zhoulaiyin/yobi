<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/libs/bootstrap/css/bootstrap.min.css" type="text/css">
    <link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">
    <link href="/libs/editor.md-master/css/editormd.preview.min.css" rel="stylesheet">

    <style>
        body {
            font-size: 14px;
            font-family: Helvetica, STHeiti, "Microsoft YaHei", Verdana, Arial, Tahoma, sans-serif;
            background: #fff;
            position: relative;
            overflow-y: auto;
        }
        a{
            cursor: pointer;text-decoration:none!important;
        }
        ::-webkit-scrollbar{width:0px;}

        .layui-bg-white{
            background:#fff;
        }
        .layui-bg-white a{
            color: #585858!important;
        }
        .layui-bg-white .layui-nav-bar{
            background-color: #6DA9FD!important;
        }
        .layui-bg-white .layui-nav-child{
            background-color:#f9f9f9!important;
        }
        .layui-bg-white .layui-this a,.layui-nav-tree .layui-this,.layui-bg-white .layui-this a:hover{
            background:#fff!important;
            color:#6DA9FD!important;
        }
        .layui-nav-item a:hover{
            color:#6DA9FD!important;
            background: transparent!important;
        }
        .layui-bg-white .layui-nav-itemed>a .layui-nav-more{
            border-color: transparent transparent #438EB9!important;
        }
        .layui-bg-white  .layui-nav-more{
            border-color:#438EB9 transparent transparent !important;
        }

        .main{
            position: relative;
        }
        .content-body{
            margin-top: 3px;
            height: 95%;
            position: relative;
            overflow-x: hidden;
        }

        /*左侧子导航*/
        .side-nav {
            position: fixed;
            float: left;
            width: 15%;
            left: 5%;
            overflow-y: auto;
            overflow-x: hidden;
            bottom: 0;
            top: 0;
        }
        .side-nav h5 {
            margin:0;
            width:200px;
            height: 46px;
            padding-left: 20px;
            box-sizing: border-box;
            color: #fff;
            line-height: 46px;
            background-color: #6DA9FD;
            font-weight: bold;
        }
        .side-nav ul {
            margin: 0;
            position: relative;
            padding-left: 0;
        }
        .side-nav ul li {
            list-style-type:none;
            margin-top: 1px;
            background: #f9f9f9;
            color: #585858;
            box-sizing: border-box;
            cursor: pointer;
        }
        .side-nav ul li i {
            position: absolute;
            right: 10px;
        }
        .side-nav .cur-side-nav a,.side-nav .cur-side-nav a:hover{
            background: #fff!important;
            color:#6DA9FD!important;
        }
        .panel-default {
            float: left;
            margin-left:20%;
            width:80%;
            height:100%;
            border: none;
        }

        .icon-top{
            width:50px;
            height: 50px;
            right: 30px;
            bottom: 50px;
            position: fixed;
            display: none;
            z-index: 2;
        }
        .icon-top i{
            cursor: pointer;
            font-size: 50px;
        }
    </style>

    @yield('css')

</head>

<body>

<div id="icon_top" class="icon-top" onclick="goTop()" ><i class="layui-icon layui-icon-top"></i></div>
@yield('content')

</body>

<script src="/libs/jquery/jquery-2.2.2.min.js"></script>
<script src="/libs/layer/layer.js"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>
<script src="/libs/bootstrap/js/bootstrap.min.js"></script>
<script src="/libs/ebsig/base.js?v=20170206"></script>
<script type="text/javascript">

   //点击分组菜单
   $('li.menu_group').click(function () {

       $(".menu_group").find('i').removeClass('layui-icon-up').addClass('layui-icon-down');
       $('.menu_doc').hide();
       $(this).siblings().removeClass('active');

       if ($(this).hasClass('active')) {
           $(this).removeClass('active').find('i').removeClass('layui-icon-up').addClass('layui-icon-down');
           $(this).next('.menu_doc').hide();
       } else {
           $(this).addClass('active').find('i').removeClass('layui-icon-down').addClass('layui-icon-up');
           $(this).next('.menu_doc').show();
       }

   });

   window.onscroll= function(){
       //变量t是滚动条滚动时，距离顶部的距离
       var scrollTop = document.documentElement.scrollTop||document.body.scrollTop;
       if(scrollTop > 200){
           $('#icon_top').fadeIn(500);
       }else{
           $('#icon_top').fadeOut(1000);
       }
   }

   function goTop(){
       document.body.scrollTop = document.documentElement.scrollTop = 0;
   };
</script>
@yield('js')

</html>