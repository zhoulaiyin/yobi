@extends('shop')

@section('title')
    WeBI文档中心
@endsection

@section('css')
    <style>

        /**
         * 全局样式
         */
        ol, ul ,li {
            list-style: none;
        }
        ol, ul ,li, h4, h5, h6, span, div, p {
            padding: 0;
            margin: 0;
        }
        fieldset,img {
            border: 0;
        }
        table {
            border-collapse:collapse;
            border-spacing:0;
        }
        .layui-nav .layui-this:after{
            background-color:#669ADE!important;
        }
        /**
         * 头部样式
         */
        .doc-header{
            width: 100%;
            padding-left: 0 !important;
            height: 60px;
            border-bottom: 1px solid #404553;
            background-color: #393D49;
        }
        .doc-header-main{
            width: 100%;
        }
        .doc-header .logo {
            position: absolute;
            left: 65px;
            top: 0;
        }
        .header .first_menu {
            position: absolute;
            left: 220px;
            top: 0;
            padding: 0;
            background: none;
        }
        .doc_second_menu{
            position: relative;
            width: 100%;
            top: 70px;
            font-size: 16px;
            height:39px;
            line-height:43px;
            border-bottom:1px solid #e6e7ec;
            background-color: #fff;
        }
        .doc_second_menu .second-tabs{
            position: absolute;
            left: 80px;
            width: 100%;
        }
        .second-tabs li{
            margin-left: 20px;
            float: left;
        }
        .secondmenu span{
            padding-bottom: 5px;
        }
        .second-tabs .selected span{
            color: #669ADE;
            border-bottom: 2px solid #669ADE;
        }
        .second-tabs > li > span:hover {
            color: #669ADE;
            cursor: pointer;
        }

        /**
        * iframe框架样式
        */
        .doc-content {
            position: relative;
            width: 101%;
            height: 500px;
            margin-top: 60px;
        }
        .doc-content .doc-content-body{
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            margin-top:10px;
        }

        /**
        * 底部
        */
        .footer {
            display: none;
        }
        .icon-top{
            display: none;
        }

    </style>
@endsection

@section('content')
    <div>

        {{--  头部   --}}
        <div class="doc-header header ">
            <div class="layui-main doc-header-main">
                <a class="logo" href="/">
                    <img src="/images/webi/logo.png"  width="149px;" height="60px;">
                </a>
                <ul class="layui-nav first_menu">
                    @if ( isset($group) && !empty($group) )
                        @foreach( $group as $k=>$v)
                            @if ( !empty($group_id) && $group_id == $v['group_id'] )
                                <li class="layui-nav-item firstmenu layui-this" data-id="{{$v['group_id']}}"><a href="javascript:void(0);">{{$v['group_name']}}</a></li>
                            @else
                                <li class="layui-nav-item firstmenu" data-id="{{$v['group_id']}}"><a href="javascript:void(0);" target="_self">{{$v['group_name']}}</a></li>
                            @endif
                        @endforeach
                    @endif
                    <span class="layui-nav-bar"></span>
                </ul>
                {{--  二级菜单   --}}
                <div class="doc_second_menu">
                    <ul class="second-tabs">
                        @if ( isset($item) && !empty($item) )
                            @foreach( $item as $k=>$v)
                                <li class="tab_nav secondmenu" data-id="{{$v['item_id']}}" data-href="{{$v['item_link']}}">
                                    <span>{{$v['item_name']}}</span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="doc-content" id="content">
            <iframe frameborder="0" class="doc-content-body" id="mainFrame" name="mainFrame" src="/doc/blank" ></iframe>
        </div>

        {{--  脚部   --}}
        <div class="footer">
            Copyright © 2018-2020  上海志承软件有限公司
            <a href="http://www.ebsig.com" target="_blank"> http://www.ebsig.com </a>
            版权所有
        </div>

    </div>
@endsection

@section('js')
<script src="http://cdn.bootcss.com/jquery/2.2.3/jquery.min.js"></script>
<script src="/libs/layui-v2.5.6/layui.js" charset="utf-8"></script>
<script type="text/javascript">

    var item = {!! "'".$item_id."'" !!};//二级菜单默认选中
    window.onload = function() {
        if (window.innerHeight)
            winHeight = window.innerHeight;
        else if ((document.body) && (document.body.clientHeight))
            winHeight = document.body.clientHeight;
        if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth)
        {
            winHeight = document.documentElement.clientHeight;
        }
        document.getElementById('content').style.height = (parseInt(winHeight)-150)+'px';
    }

    $('.firstmenu').on('click',function(){
        self.location = "/doc/group/"+ $(this).attr('data-id');
    });

    $('.secondmenu').on('click',function(){
        $(this).addClass('selected').siblings().removeClass('selected');
        $("#mainFrame").show().attr('src', $(this).attr('data-href') );
    });

    (function () {
        if( item != ''){
            $('.secondmenu[data-id="'+ item + '"]').trigger('click');
        }
    })();

</script>
@endsection