@extends('document.layout')

@section('title')
    WeBI—微电汇数据源
@endsection

@section('css')
    <link href="/libs/editor.md-master/css/editormd.preview.min.css" rel="stylesheet">
    <style>
        #doc-content{
            padding: 0;
            border: none;
        }

        /*左侧子导航*/

        .nav-ul{
            height: 420px;
            overflow-y: scroll;
        }
        .nav-ul li{
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .layui-form{
            float: right;
            margin-right: 25px;
        }
        .layui-input,.input-group-btn{
            display: inline-block;
        }
        .input-group-btn,.layui-btn+.layui-btn{
            margin-left:5px!important;
        }
    </style>
@endsection

@section('content')

    <!--主体内容-->
    <div class="main">
        <div id="content_body" class="content-body">
            <!--侧边栏-->
            <div class="side-nav">
                <h5>全部数据集</h5>
                <ul class="nav-ul layui-nav layui-nav-tree layui-nav-side layui-bg-white">
                @foreach( $group as $k=>$g )
                    @if($g['table'])
                        @foreach( $g['table'] as $t=>$d )
                            @if($k==0 && $t==0)
                                <li class="layui-nav-item menu_doc single_t_{{$d['table_id']}}" data-code="{{$d['table_id']}}" id="first-click">
                                    <a>{{$d['description']}}</a>
                                </li>
                            @else
                                <li class="layui-nav-item menu_doc single_t_{{$d['table_id']}}" data-code="{{$d['table_id']}}">
                                    <a>{{$d['description']}}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
            </div>

            <!--内容-->
            <div class="layui-row">
                <div class="layui-col-lg12">
                    <div class="layui-col-md4 layui-col-md-offset8">
                        <form class="layui-form" id="search-form" onsubmit="return false;">

                            <div class="layui-form-item">
                                <input type="text" class="layui-input" name="table_name" id="table_name" style="width: 180px;" placeholder="请输入数据集">
                                <span class="input-group-btn">
                                        <button class="layui-btn layui-btn-normal" onclick="stat.search()" type="button">查询</button>
                                        <button class="layui-btn layui-btn-primary" onblur="stat.reset()" type="button">重置</button>
                                    </span>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <br>

            <div class="panel panel-default">
            <div class="panel-body" id="doc-content">
                <div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;" id="main_content">

                    <div style="height:40px;">
                        <span id="t_name"; style="font-size: 27px;width:50%" ></span>
                        <span id="statistical_frequency" style="float:right;font-size: 20px;"></span>
                        <p  style="border-bottom: 1px solid #eee"></p>
                    </div>

                    <h4 id="h4-post">描述：</h4>
                    <pre id="description" style="height:100px;"> </pre>

                    <h4 id="h4-post">数据字典：</h4>
                    <table style="width:100%">
                        <thead >
                        <tr>
                            <th style="text-align: center; vertical-align: middle;border-bottom: none;width:250px">字段名称</th>
                            <th style="text-align: center; vertical-align: middle;border-bottom: none;width:140px">字段类型</th>
                            <th style="text-align: center; vertical-align: middle;border-bottom: none;width:140px">示例值</th>
                            <th style="text-align: center; vertical-align: middle;border-bottom: none;width:420px">字段说明</th>
                        </tr>
                        </thead>
                        <tbody id="api_content">
                        </tbody>
                    </table>

                    {{--<h4>规则说明：</h4><div id="doc_html"> </div>--}}

                </div>

            </div>

        </div>
        </div>
    </div>
@endsection

@section('js')
    <script>

        //单击文档菜单
        $('li.menu_doc').click(function () {

            $('li.menu_doc').removeClass('cur-side-nav');
            $(this).addClass('cur-side-nav');

            $.ajax({
                type: 'GET',
                url: '/doc/data/source/get/'+ $(this).attr('data-code'),
                dataType: 'JSON',
                success: function(res) {
                    var html = '';
                    if (res.code == 200) {

                        if ( res.data ) {

                            $('#t_name').html(res.data['description']);
                            if( res.data['statistical_frequency'] != ''){
                                $('#statistical_frequency').html("统计频率："+res.data['statistical_frequency']);
                            }
                            $('#description').html(res.data['description']);

                            if ( res.data['fields_json'] ) {
                                $.each(res.data['fields_json'],function (t,m) {
                                    html += '<tr>';
                                    html += '<td style="text-align:center;vertical-align: middle;">'+m.field_name+'</td>';
                                    html += '<td style="text-align:left;vertical-align: middle;">'+m.field_type+'</td>';
                                    html += '<td style="text-align:left;vertical-align: middle;">'+m.field_sample+'</td>';
                                    html += '<td style="text-align:center;vertical-align: middle;">'+m.field_remark+'</td>';
                                    html += '</tr>';
                                });
                            }

                            $('#api_content').empty();
                            $('#api_content').append(html);

                        }
                    } else {
                        $('#main_content').html('数据集不存在');
                    }
                },
            });

        });

        var stat = {

            //查询数据
            search:function(){
                var name = $("#table_name").val();
                $.ajax({
                    type: 'POST',
                    url: '/doc/data/source/search',
                    dataType: 'JSON',
                    data:{
                        name:name
                    },
                    success: function(res) {
                        if(res.code==200){
                            $("#first_"+res.data['rule_group_id']).trigger('click');
                            $(".single_t_"+res.data['table_id']).trigger('click');
                        }else{
                            layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                            return false;
                        }

                    }
                })

            },

            reset:function () {
                $("#table_name").val('');
                stat.search();
            },

        }
        $("#first-click").trigger('click');
        layui.use(['element','form'], function(){
            var element = layui.element;
            var form = layui.form;
        });
    </script>
@endsection
