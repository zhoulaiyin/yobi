@extends('webi.layout')

@section('css')
    <link rel="stylesheet" href="/css/webi/choose.css?v=201804081927">
@endsection

@section('content')
    <div class="content">

        <!--侧边栏-->
        <div class="left-list-choose">
            <div class="left-list-box">
                <ul id="chart_group">
                    @if ( isset($chart_group) && !empty($chart_group) )
                        @foreach( $chart_group as $k => $v )
                            <li class="{{$v['group_code']}} chart-c" data-id="{{$v['group_code']}}" id="chart-{{$v['_id']}}"><span class="icon-pic"><img src="{{$v['icon']}}" ></span><a href="javascript:;">{{$v['group_name']}}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <!---->
        <div class="main-choose">
            <ul class="chart-list">
            </ul>
        </div>
    </div>
@endsection

@section('js')

    <script type="text/javascript">

        var callback_fun =  {!!  '"'.$callback_fun.'"' !!} ;

        //操作对象
        var chart = {};

        //创建类型列表
        chart.create_list = function(data) {
            var html = '';
            $.each(data,function (k,v) {
                html += '<li onclick="chart.choose('+ "'" + k+"'" +');" title="'+v['chart_title']+'">';
                html += '<div class="chart-inner">';
                html += '<img src="'+v['photo_link']+'" alt="" style="width:100%;height:100%;">';
                html += '</div>';
                html += '</li>';
            });
            $('.chart-list').html(html);
        };

        //获取BI分组下面的所有类型
        chart.get_module = function(id) {

            if ( id == '' ) {
                layer.alert('请选择图表类型',{icon:2,offset:'50px'});
                return false;
            }

            var index = layer.load();

            $.ajax({
                type:"get",
                url:'/webi/design/choose/get/'+id,
                success:function (res) {
                    layer.close( index );
                    if( res.code != 200 ){
                        layer.alert(res.message,{icon:2,offset:'50px'});
                        return false;
                    }
                    if( $.isEmptyObject(res.data) || res.data == '' ){
                        return false;
                    }
                    chart.create_list(res.data);
                }

            });
        };

        //点击选择某一张BI
        chart.choose = function(id) {

            if ( !id ) {
                layer.alert('请选择报表',{icon:2,offset:'50px'});
                return false;
            }

            if( callback_fun != "" ){
                eval( 'parent.'+ callback_fun + "('"+id +"')" );
            }

            //关闭添加BI弹层
            parent._We_G.closeBI();
        };

        $(document).on('click','.chart-c',function(){     //查询图表类型
            $(this).addClass('cur-list').siblings().removeClass('cur-list');
            $('.chart-list').html('');
            chart.get_module( $(this).attr('id').substring(6) );
        });

        $("#chart_group li").eq(0).click();

    </script>
@endsection