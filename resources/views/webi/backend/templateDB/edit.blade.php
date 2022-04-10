@extends('webi.layout')
@section('css')
    <link rel="stylesheet" href="/css/webi/index.css?v=201806191113">
    <link href="/css/webi/webi.clolor.css?v=20180507" rel="stylesheet">
@endsection

@section('content')
    @include('webi.webiDesign')
@endsection

@section('js')

    <script type="text/javascript">
        var _V = {};
        _V.attr_url = {
            1:"/webi/template/database/save_master",  //保存主表属性设置
            2:"/webi/template/database/attr/save"  //保存BI属性设置
        };
        _V.global_url = {
            1:"/webi/template/database/module/save",  //重新遍历保存module结构
            2:"/webi/design/edit/font/get"  //获取所有维护字体
        };
        _V.module_url = {
            1:"/webi/template/database/create/module",     //新增一个BI模块
            2:"/webi/template/database/choose/copy",       //复制BI
            3:"/webi/template/replace/module?callback_fun=_We_M.show_bi",    //更换BI
            4:"/webi/template/database/module/del/",    //删除BI
            5:"/webi/template/database/attr/save", //保存BI主体信息
            6:"/webi/design/edit/bi/dts/save?bi_module=1",  //保存BI数据集信息
            7:"/webi/template/database/operation/get/",     //获取报表信息
            8:"/webi/design/edit/bi/position/save?flg=2",//保存module位置数据
        };
        _V.chart_url = {
            1: "/webi/template/edit/chart/save"     //保存BI模块chart数据
        }
    </script>
    <script type="text/javascript" src="/js/webi/webi.color.js"></script>
    <script type="text/javascript" src="/libs/jquery-ui/jquery-ui.js"></script>
    {{--<script type="text/javascript" src="/js/webi/free.drag.js"></script>--}}
    <script type="text/javascript" src="/js/webi/freedrag.js"></script>
    <script type="text/javascript" src="/js/webi/design/design.module.js"></script>
    <script type="text/javascript" src="/js/webi/design/design.dataset.js"></script>
    <script type="text/javascript" src="/js/webi/design/design.attr.js"></script>
    <script type="text/javascript" src="/js/webi/design/design.chart.js"></script>
    <script type="text/javascript" src="/js/webi/design/design.global.js"></script>
    <script type="text/javascript" src="/js/webi/design/design.drag.js"></script>

    <script type="text/javascript">

        document.getElementById('logo-href').href = "javascript:void(0)";
		$('.preview').remove();//预览
        $('.glo_attribute').remove();//上传主图
        $('.overview').remove();//数据源总览

         _We_G.pid = 'parent_We_Main'; //整个设计页最外层的DIV元素ID
         _We_G.bi_id = {!! $bi_id !!}; //报表ID
         var webi_dt = JSON.parse(JSON.stringify({!! $webi_dt !!}) );

         var data = new Array();
         data['master'] = webi_dt.master;
         data['module'] = webi_dt.module;

         //加载全部报表
         WeBI.op.a(_We_G.pid, data, $('#bi_content_html').html(),1,2);

         //进入页面初始化绑定操作
         _We_G.init();
    </script>
@endsection