@extends('webi.bishop')

@section('title')
    {{ $bi_master['bi_title'] }}
@endsection

@section('css')
    <style>
		.top-header{
			position:relative;
			top: 0;
			left: 0;
			padding: 0 20px;
			width: 100%;
			height: 58px;
			box-shadow: 0 0 6px 6px rgba(0,0,0,.05);
			line-height: 58px;
			z-index: 22;
			background: #fff;
		}
        .dropdown{
            padding-top: 1.5%;
            float: right;
            height: 30px;
        }
        .dropdown i,.dropdown-menu li{
            cursor: pointer;
        }

        .parent{
            width:100%;
            height:800px;
            position: relative;
        }

        .shop-grid {
            position: absolute;
            text-align:center;
        }
        .bi-title{
            width:100%;
            height:30px;
            line-height: 30px;
            font-size:18px;
            text-align:left;
            font-weight: 700;
            padding: 0 10px;
            overflow: hidden;
            background-color: #454E53;
            color: #F2F2F2;
        }
        .bi-title-l{
            width:100%;
            height:30px;
            line-height: 30px;
            font-size:20px;
            text-align:left;
            font-weight: 700;
            padding: 0 10px;
            overflow: hidden;
        }
        .bi-title-c{
            width:100%;
            height:30px;
            line-height: 30px;
            font-size:20px;
            font-weight: 700;
            padding: 0 10px;
            text-align:center;
            overflow: hidden;
        }
        .bi-title-r{
            width:100%;
            height:30px;
            line-height: 30px;
            font-size:20px;
            font-weight: 700;
            text-align:right;
            padding: 0 10px;
            overflow: hidden;
        }
        .bi-text{
            width:100%;
            height:30px;
            font-size:20px;
            font-weight: 700;
            text-align:center;
            overflow: hidden;
        }
        .bi-chart{
            width:100%;
            height:100%;
        }
        table tr:nth-child(even){
            background:#454E53;
            color:#F2F2F2;
        }
    </style>
@endsection

@section('content')
    {{--START 头部  --}}
    <div class="top-header" style="display: none;">
        <div type="button" class="dropdown" id="group_">
            <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <i class="glyphicon glyphicon-option-vertical text-style" ></i>
            </span>
            <ul class="dropdown-menu dropdown-menu-right" style=" min-width: 60px;margin-top: 55%;">
                <li  style="height:30px;padding-left:0;"><a style="color: black;" onclick="_G_BI.fullScreen()";>全屏投影</a></li>
            </ul>
        </div>
    </div>
    {{--END 头部--}}
    <div class='parent' id="parent_main" data-id="{{ $uid }}"></div>
    <div id="bi_content_html" style="display: none;">
        <div class="shop-grid" id="grid_$uid">
            <div class="bi-title" id="title_$uid"></div>
            <div class="bi-chart" data-id="$uid" id="chart_$uid"></div>
        </div>
    </div>
@endsection
<script type="text/javascript" src="/js/webi/app.webi.js"></script>
@section('js')
    <script>
        (function(){

            var type = _G_BI.iType();//终端类型
            var equipment_code = _G_BI.getCookie('machine_num');

            //手机端且已扫码连接 显示全屏投影
            if( type == 1 && equipment_code != null){
                $('.top-header').css('display','block');
                $(".glyphicon-button").on('click',function () {
                    $(".dropdown-menu").toggle();
                });
            }

            $.ajax({
                type: 'get',
                url: "/webi/list/master/get?uid={{ $uid }}",
                dataType: 'JSON',
                data: {},
                success: function( o ) {

                    //解析chart_json
                    if ( !$.isEmptyObject(o.data.module) ) {
                        $.each(o.data.module,function (k,v) {
                            o.data.module[k]['bi_json']['chart_json'] = o.data.module[k]['bi_json']['chart_json'];
                           // o.data.module[k]['bi_json']['chart_json'] = eval('(' + o.data.module[k]['bi_json']['chart_json'] + ')');
                        });
                    }
                    WeBI.op.a('parent_main', o.data, document.getElementById('bi_content_html').innerHTML,2);

                    //报表绑定点击事件
                    if( equipment_code != null && type == 1 ){
                        _G_BI.touch();
                    }
                }
            });

        })();

    </script>
@endsection