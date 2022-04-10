@extends('document.layout')

@section('css')
@endsection

@section('content')
    <div class="main">
        <div id="content_body" class="content-body">
            <!--侧边栏-->
            <div class="side-nav">
                <h5>变更日志</h5>
                <ul id="list_ul" class="layui-nav layui-nav-tree layui-nav-side layui-bg-white">
                    @if( !empty($log) )
                        @foreach( $log as $g => $v )
                            @if( $g == 1 )
                                <li class="layui-nav-item menu_doc active" data-id="{{$v['log_id']}}">
                                    <a href="javascript:;">{{$v['v']}}</a>
                                </li>
                            @else
                                <li class="layui-nav-item menu_doc" data-id="{{$v['log_id']}}">
                                    <a href="javascript:;">{{$v['v']}}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
            <!--内容-->
            <div class="panel panel-default">
                <div class="panel-body" id="doc-content">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

        //单击文档菜单
        $('li.menu_doc').click(function () {

            $(this).addClass('cur-side-nav').siblings().removeClass('cur-side-nav');

            $.ajax({
                type: 'GET',
                url: '/doc/change/log/get',
                dataType: 'JSON',
                data: {
                    log_id: $(this).attr('data-id')
                },
                timeout: 30000,
                success: function(res) {
                    if( res.code != 200 ){
                        layer.alert(res.message, {icon: 2, offset: '50px'});
                        return false;
                    }
                    $('#doc-content').html(res.data);
                }
            });

        });

        (function(){
            var list = document.getElementById('list_ul').querySelectorAll('li');
            if( typeof list[0] != 'undefined'){
                if(document.all) { //IE
                    list[0].click();
                } else { // 其它浏览器
                    var e = document.createEvent("MouseEvents");
                    e.initEvent("click", true, true);
                    list[0].dispatchEvent(e);
                }
            }
        })();
        layui.use('element', function(){
            var element = layui.element;

        });
    </script>
@endsection