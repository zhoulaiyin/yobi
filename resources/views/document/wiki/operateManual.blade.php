@extends('document.layout')

@section('css')
    <style>
    .layui-bg-white a:hover {
        background: #fff!important;
        color: #438eb9!important;
    }
    .side-nav ul li.menu_doc {
        padding-left: 0;
        display: none;
    }
    .menu_doc li:hover {
        color: #438eb9;
    }
</style>
@endsection

@section('content')
    <div class="main">
        <div id="content_body" class="content-body">
            <!--侧边栏-->
            <div class="side-nav">
                <h5>文档目录</h5>
                <ul class="layui-nav layui-nav-tree layui-nav-side layui-bg-white">
                    @foreach( $group as $g )
                    <li class="layui-nav-item"  data-id="{{$g['group_id']}}">
                        <a href="javascript:;">{{$g['group_name']}}</a>
                        <dl class="layui-nav-child" >
                            @foreach( $g['list'] as $d )
                            <dd>
                                <a onclick='skipUrl("{{$d['id']}}")'>{{$d['name']}}</a>
                            </dd>
                            @endforeach
                        </dl>
                    </li>
                    @endforeach
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
        $('.layui-nav-child a').click(function () {

            $('.layui-this dd').removeClass('layui-this');
            $(this).parent().addClass('layui-this');

        });
        function skipUrl(id){
            $.ajax({
                type: 'GET',
                url: '/doc/operate/manual/item/get?id='+ id,
                timeout: 30000,
                success: function(res) {
                    if( res.code != 200 ){
                        layer.alert(res.message, {icon: 2, offset: '50px'});
                        return false;
                    }
                    $('#doc-content').html(res.data);
                }
            });
        }

        $(function(){
            $('.layui-nav-item:first').addClass('layui-nav-itemed');
            $('.layui-nav-child a:first').trigger('click');
        });
        layui.use('element', function(){
            var element = layui.element;

        });
    </script>
@endsection