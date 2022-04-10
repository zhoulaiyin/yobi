@extends('webi.layout')

@section('css')
    <link rel="stylesheet" href="/css/webi/index.css?v=2018062821">
    {{--<link rel="stylesheet" href="/css/webi/select.css?v=2019011516">--}}
    <link href="/css/webi/webi.clolor.css?v=20180507" rel="stylesheet">
@endsection

@section('content')

    @include('webi.webiDesign')

@endsection

@section('js')
    <script type="text/javascript">
        var _V = {};
        _V.attr_url = {
            1:"/webi/design/edit/master/save",  //保存主表属性设置
            2:"/webi/design/edit/bi/attr/save"  //保存BI属性设置
        };
        _V.global_url = {
            1:"/webi/template/database/module/save",  //重新遍历保存module结构
            2:"/webi/design/edit/font/get"  //获取所有维护字体
        };
        _V.module_url = {
            1:"/webi/design/create/module",     //新增一个BI模块
            2:"/webi/design/choose/copy",       //复制BI
            3:"/webi/design/replace/module",    //更换BI
            4:"/webi/design/delete/module/",    //删除BI
            5:"/webi/design/edit/bi/attr/save", //保存BI主体信息
            6:"/webi/design/edit/bi/dts/save",  //保存BI数据集信息
            7:"/webi/design/operation/get/",     //获取报表信息
            8:"/webi/design/edit/bi/position/save?flg=1",//保存module位置数据
        };
        _V.chart_url = {
            1: "/webi/design/edit/chart/save"     //保存BI模块chart数据
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

        (function(){
            _We_G.pid = 'parent_We_Main'; //整个设计页最外层的DIV元素ID

            $.ajax({
                type:'get',
                url:'/webi/design/get/data/{!! $uid !!}',
                success:function(obj){
                    if(obj.code == 200){

                        _We_G.bi_id = obj.data.bi_id; //报表ID
                        _We_G.bi_uid = {!!  '"'.$uid.'"' !!};

                        //加载全部报表
                        WeBI.op.a(_We_G.pid, obj.data, $('#bi_content_html').html(),1);

                        //局部刷新对象数组
                        for (var uid in obj.data.module){
                            if(typeof obj.data.module[uid].db_json.auto_refresh != "undefined" && obj.data.module[uid].db_json.auto_refresh == 1 ){
                                var refresh_frequency = obj.data.module[uid].attribute_json.general.refresh_frequency;//刷新频率
                                _We_M.refreshObj.module[uid] = refresh_frequency;
                            }
                        }
                        _We_M.auto_refresh();//自动刷新

                        //进入页面初始化绑定操作
                        _We_G.init();
                    }
                }
            });

        })();

    </script>

@endsection