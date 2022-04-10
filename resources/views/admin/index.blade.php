@extends('admin.layout')

@section('css')
    <link rel="stylesheet" href="/css/index/iconfont.css">
    <link rel="stylesheet" href="/css/index/iconfont.woff">
    <style>
        .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
            padding-right: 7px;
            padding-left: 7px;
        }
        .new_info{
            color:red;
            height:10px;
            line-height:10px;
            display:inline-block;
        }
        .model-title{
            display:inline-block;
            width:83%;
        }
        .main-info{
            height:22px;
            line-height:22px;
        }
        .table thead tr th{
            vertical-align:middle;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/eoa/index"><span>后台首页</span></a>
                </li>
                <li>
                    <a href="/eoa/zaza/app"><span>扎扎APP</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">
        <div class="row">
            <div class="col-md-9">

                @if(isset($demands) && !empty($demands))

                <div class="col-md-6">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title model-title">待我处理的需求</h3>
                            <a href="" target="_top" id="pending_demand">查看更多>></a>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:15%">需求编号</th>
                                <th style="width:15%">创建人</th>
                                <th>需求名称</th>
                                <th style="width:15%">需求状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($demands as $demand)
                                <tr>
                                    <td>{{$demand['demand_id']}}</td>
                                    <td>{{$demand['trueName']}}</td>
                                    <td><a title="{{$demand['demand_name']}}" href="javascript:;" onclick="Need.detail({{$demand['demand_id']}})">{{$demand['demand_name_short']}}</a></td>
                                    <td>{{$demand_status[ $demand['demand_status'] ]}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if(isset($demand_puts) && !empty($demand_puts))
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title model-title">由我提出的需求</h3>
                            <a href="" target="_top" id="propose_demand">查看更多>></a>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:15%">需求编号</th>
                                <th style="width:15%">创建人</th>
                                <th>需求名称</th>
                                <th style="width:15%">需求状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($demand_puts as $demand)
                                <tr>
                                    <td>{{$demand['demand_id']}}</td>
                                    <td>{{$demand['trueName']}}</td>
                                    <td><a title="{{$demand['demand_name']}}" href="javascript:;" onclick="Need.detail({{$demand['demand_id']}})">{{$demand['demand_name_short']}}</a></td>
                                    <td>{{$demand_status[ $demand['demand_status'] ]}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if(isset($tasks) && !empty($tasks))
                <div class="col-md-6">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h3 class="panel-title  model-title">待我处理的任务</h3>
                            <a href="" target="_top" id="pending_task">查看更多>></a>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:15%">任务编号</th>
                                <th style="width:15%">创建人</th>
                                <th>任务名称</th>
                                <th style="width:15%">任务状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td>{{$task['id']}}</td>
                                    <td>{{$task['trueName']}}</td>
                                    <td><a title="{{$task['task_name']}}" href="javascript:;" onclick="Task.detail({{$task['id']}})">{{$task['task_name_short']}}</a></td>
                                    <td>{{$task_status[$task['task_status']]}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if(isset($task_puts) && !empty($task_puts))
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title model-title">由我提出的任务</h3>
                            <a href="" target="_top" id="propose_task">查看更多>></a>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:15%">任务编号</th>
                                <th style="width:15%">创建人</th>
                                <th>任务名称</th>
                                <th style="width:15%">任务状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($task_puts as $task)
                                <tr>
                                    <td>{{$task['id'] or ''}}</td>
                                    <td>{{$task['trueName']}}</td>
                                    <td><a title="{{$task['task_name']}}" href="javascript:;" onclick="Task.detail({{$task['id'] or ''}})">{{$task['task_name_short']}}</a></td>
                                    <td>{{$task_status[$task['task_status']]}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
               @endif

                @if(isset($my_problems) && !empty($my_problems))
                <div class="col-md-6">
                    <div class="panel panel-danger">
                        <div class="panel-heading"><h3 class="panel-title model-title">我的任务BUG</h3></div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:15%">任务编号</th>
                                <th style="width:15%">创建人</th>
                                <th>任务名称</th>
                                <th style="width:15%">任务状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($my_problems as $problem)
                                <tr>
                                    <td>{{$problem['id'] or ''}}</td>
                                    <td>{{$problem['trueName']}}</td>
                                    <td><a title="{{$problem['task_name']}}" href="javascript:;" onclick="Task.detail({{$problem['id'] or '' }},9)">{{$problem['task_name_short']}}</a></td>
                                    <td>{{$task_status[$problem['task_status']]}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif







            </div>
            <div class="col-md-3">

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title main-info" style="width:73%;display:inline-block;">重要通知</h3>
                        <a href="javascript:;" onclick="Notice.more();" style="color:white;">查看更多</a>
                    </div>
                    <ul class="list-group">
                        @foreach($notices as $notice)
                        <li class="list-group-item">
                            <a href="javascript:;" onclick="Notice.detail({{$notice['id']}})">{{$notice['title']}}</a>
                            @if($notice['visited'] == 0)
                            <i class="iconfont new_info" id="new{{$notice['id']}}">&#xe601;</i>
                                @endif
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="/libs/layer/layer.js"></script>
    <script>
        $(function(){
            $('#pending_demand').attr('href','/eoa/dashboard/8?sidebar='+encodeURIComponent('/eoa/demand/1?act=search_1')+'');
            $('#propose_demand').attr('href','/eoa/dashboard/8?sidebar='+encodeURIComponent('/eoa/demand/1?act=search_2')+'');
            $('#pending_task').attr('href','/eoa/dashboard/7?sidebar='+encodeURIComponent('/eoa/task/0?act=search_1')+'');
            $('#propose_task').attr('href','/eoa/dashboard/7?sidebar='+encodeURIComponent('/eoa/task/0?act=search_2')+'');
        });

        var Notice = {
            //  查看消息详情
            detail:function( id ){
                $.ajax({
                    url:"/eoa/notice/detail/" + id ,
                    type:"GET",
                    success:function(res){
                        if(res.code == 200){
                            layer.open({
                                title:'通知详情',
                                type: 1,      //  完全打开类型是2
                                offset: '30px',
                                area: ['80%','80%'] ,   // ['600px','450px']
                                scrollbar: true,
                                content: res.data.content ,
                                btn: false ,
                                end: function (index) {
                                    layer.close(index);
                                    $('#new'+id).remove();
                                }
                            });
                        }else{
                            layer.msg(res.message , {icon: 2, offset: '70px'});
                        }
                    }
                })
            },
            more:function(){
                layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: '/eoa/notice/more',
                    end:function() {
                        //$('#table').bootstrapTable('refresh') ;
                    }
                } );
            }
        };


        var Need = {
            detail:function( demand_id ) {
                layer.open({
                    title:false ,
                    type: 2,                   //  iframe类型
                    offset: '0px',
                    area: ['100%','100%'],   // 完全打开就是[100%,100%]
                    scrollbar: false ,
                    content: "/eoa/demand/detail/" + demand_id,
                    btn: false ,
                    closeBtn:false
                });
            }

        };

        var Task = {
            detail:function( task_id , flg ) {
                layer.open({
                    title:false ,
                    type: 2,                   //  iframe类型
                    offset: '0px',
                    area: ['100%','100%'],   // 完全打开就是[100%,100%]
                    scrollbar: false ,
                    content: "/eoa/task/detail/" + task_id+'?flg='+flg,
                    btn: false,
                    closeBtn:false
                });
            }
        };

    </script>

@endsection