@extends('backend')
@section('css')
    <style>
        .nav-tabs li{
            display: inline-block;
        }
    </style>
@endsection

@section('content')

    <div class="app-third-sidebar">
        <nav class="ui-nav " style="display: block;">
            <ul>
                <li>
                    <a href="/admin/task"><span>任务管理</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">

        <div class="layui-row">

            <div class="layui-col-lg12">
                <div id="toolbar">
                    <div class="ebsig_container" style="padding-top:30px;">
                        <div class="comm_right">
                            <div class="comm_content" style="width: 100%;margin:0px auto;">

                                @if(isset($task))
                                <ul class="nav nav-tabs layui-tab-title" >
                                    @foreach($task as $key => $tt)
                                    @if($key == 1)
                                    <li id="active_{{$key}}" class="active layui-this"><a onclick="sysTask.changeTask({{$key}});">{{$tt}}</a></li>
                                    @else
                                    <li id="active_{{$key}}" ><a href="javaScript:void(0);" onclick="sysTask.changeTask({{$key}});">{{$tt}}</a></li>
                                    @endif
                                    @endforeach
                                </ul>
                                @endif
                                <br>

                                <div class="layui-col-md2">
                                    <input class="layui-btn btn-primary" style="margin-top:5px;" type="button" value="添加任务" onclick="sysTask.edit();">
                                </div>
                                <div class="layui-col-md10">
                                    <form name="task_form" id="task_form" method="post" class="layui-form select_content fr" onsubmit="return false;">
                                        <div class="layui-form-item">
                                            <input type="text" name="taskName" value="" id="taskName" class="layui-input w150 inline" placeholder="请输入任务名称">
                                            <input type="text" name="taskLink" value="" id ="taskLink"  class="layui-input inline" style="width:300px" placeholder="请输入任务链接">
                                            <span style="margin-left:10px;">
                                                <input type="button" value="查询" onclick="sysTask.search()" class="layui-btn btn-primary" onfocus="this.blur();" />
                                                <input type="button" value="重置" onclick="sysTask.clear()" class="layui-btn btn-warning" onfocus="this.blur();" style="margin-left:10px;"/>
                                            </span>
                                            <input type="hidden" name="task_type" value="1" id="task_type">
                                        </div>
                                    </form>

                                </div>

                                <div class="layui-form-item" style="margin-top:5px;">

                                    <div class="layui-row">
                                        <div class="layui-col-lg12">
                                            <table id="table" lay-filter="table"></table>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="pop" style="display: none;">

        <form id="addForm" onsubmit="return false;" class="layui-form" role="form" style="margin-top: 10px">
            <div class="layui-form-item"  >
                <label class="layui-form-label" for="task_name"><span class="red pr5">*</span>任务名称：</label>
                <div class="layui-input-block">
                    <input class="layui-input w300"  type="text" id="task_name" name="task_name"  placeholder="请输入任务名称" />
                </div>
            </div>

            <div class="layui-form-item"  >
                <label class="layui-form-label" for="task_link" ><span class="red pr5">*</span>任务链接：</label>
                <div class="layui-input-block">
                    <input class="layui-input w300"  type="text" id="task_link" name="task_link"  placeholder="请输入任务链接" />
                </div>
            </div>

            <div class="layui-form-item"  >
                <label class="layui-form-label" for="task_act_value"><span class="red pr5">*</span>act值：</label>
                <div class="layui-input-block">
                    <input class="layui-input w300"  type="text" id="task_act_value" name="task_act_value"  placeholder="请输入act值" />
                </div>
            </div>
            <input type="hidden" id="_id" name="_id">
            <input type="hidden" id="task_id" name="task_id">
        </form>

    </div>

    <div id="openLog" style="display: none;">
        <table id="openProductTable" lay-filter="openProductTable"></table>
    </div>

@endsection

@section('js')
    <script type="text/javascript">

        $(document).ready(function(){
            sysTask.changeTask(1);
        });

        var tdom = "";
        var sysTask = {
            _id:'',
            task_type:'',
            task_id:'',
            status_task_id:'',
            status_task_status:'',
            del_task_id:'',
            show_task_id:'',
            load_index :'',
            //任务名称切换
            changeTask:function( task_type ){
                sysTask.task_type = task_type;
                sysTask.load_index = layer.load();
                $("li#active_"+task_type).siblings().removeClass("active");
                $("#active_"+task_type).addClass("active");
                $("#task_type").val( task_type );
                $("#taskName").val("");
                $("#taskLink").val("");

                sysTask.result({task_type: task_type});
            },

            //任务名称切换回调
            result: function(where) {
                layer.close(sysTask.load_index);

                layui.use(['table','form'], function(){
                    var table = layui.table;
                    var form = layui.form;

                    tdom= table.render({
                        elem: '#table',
                        height: 400,
                        url: "/admin/task/search",
                        where: where,
                        page: true, //开启分页
                        cols: [[
                            {field: 'operation',fixed: 'left', width:'18%' ,title:'操作',align:'left'},
                            {field: 'task_name', title: '任务名称', width:'20%',align:'left'},
                            {field: 'task_link', title: '任务地址', align:'left'},
                            {field: 'task_act_value', title: 'act值', align:'left'},
                            {field: 'task_status', title: '状态', align:'left',width:'6%'}
                        ]]
                    });
                });
            },

            //添加or编辑任务
            edit: function( task_id,_id ) {

                sysTask.reset();

                var name = "";

                if ( _id ) {
                    name = "修改任务";
                } else {
                    name = "添加任务";
                }

                layer.open({
                    type:1,
                    title: name,
                    content: $("#pop"),
                    offset:'50px',
                    area:'550px',
                    btn: ['确定', '取消'],
                    yes: function(){
                        sysTask.check();
                    },
                    btn2: function(){
                        sysTask.reset();
                    }
                });

                if ( _id ) {
                    sysTask.task_id = task_id;
                    sysTask._id = _id;

                    E.ajax({
                        type: 'get',
                        url: "/admin/task/get",
                        data: {
                            task_id: task_id,
                            _id: _id
                        },
                        success: function ( o ) {

                            if( o.code == 200 ) {
                                $('#_id').val(o.data._id);
                                $('#task_id').val(o.data.task_id);
                                $('#task_name').val(o.data.task_name);
                                $('#task_link').val(o.data.task_link);
                                $('#task_act_value').val(o.data.task_act_value);
                            }

                        }
                    });

                }
            },

            //编辑任务成功后 重置弹出层input框
            reset: function(){
                $('#task_name').val("");
                $('#task_id').val("");
                $('#task_link').val("");
                $('#task_act_value').val("");
                layer.closeAll();
            },

            //校验数据
            check: function (){
                this.dt = E.getFormValues("addForm");
                this.dt.task_type = sysTask.task_type;
                var error_msg = "";

                if (this.dt.task_name == '') {
                    error_msg += "任务名不能为空<br>";
                }
                if (E.isEmpty(this.dt.task_link)) {
                    error_msg += "任务链接不能为空<br>";
                }
                if (E.isEmpty(this.dt.task_act_value)) {
                    error_msg += "act值不能为空<br>";
                }

                if (error_msg != "") {
                    layer.alert(error_msg,{icon:2});
                    return false;
                } else {
                    if( sysTask._id != "" ){
                        var name = "修改";
                    }else{
                        var name = "添加";
                    }

                    var dt = E.getFormValues("addForm");
                    dt.task_type = sysTask.task_type;

                    layer.confirm('您确认'+name+'该任务吗?',{icon:3}, function () {

                        E.ajax({
                            type: 'post',
                            url: "/admin/task/edit",
                            data: dt,
                            success:function( o ) {
                                if( o.code == 200 ) {
                                    sysTask.changeTask( sysTask.task_type );
                                    layer.alert(o.message,{icon:1,time:1000}, function () {
                                        sysTask.reset();
                                    });
                                    setTimeout('sysTask.reset();' ,1000 );
                                }else{
                                    layer.alert(o.message,{icon:2});
                                }
                            }
                        });
                    });
                }
            },

            //运行or暂停任务确认
            changeStatus:function( task_status, task_id,_id ){
                sysTask.status_task_id = task_id;
                sysTask.status_task_status = task_status;
                sysTask._id = _id;

                if( task_status == 1 ){
                    var name = "暂停";
                } else {
                    var name = "运行";
                }

                layer.confirm('您确认'+name+'该任务吗?', {icon:3}, function () {
                    E.ajax({
                        type: 'get',
                        url: "/admin/task/status",
                        data: {
                            _id:sysTask._id,
                            task_id:sysTask.status_task_id,
                            task_status:sysTask.status_task_status
                        },
                        success:function( o ) {
                            if( o.code == 200 ) {
                                sysTask.changeTask( sysTask.task_type );
                                layer.alert(o.message,{icon:1,time:1000}, function () {
                                    sysTask.reset();
                                })
                                setTimeout('sysTask.reset();' ,1000 );
                            }else{
                                layer.alert(o.message,{icon:2});
                            }
                        }
                    });
                });
            },
            //删除任务确认
            delConfirm:function( task_id ,_id){
                sysTask.del_task_id = task_id;

                layer.confirm('您确认删除该任务吗?',{icon:3}, function () {
                    E.ajax({
                        type: 'get',
                        url: "/admin/task/del",
                        data: {
                            task_id:sysTask.del_task_id,
                            _id:_id
                        },
                        success: function ( o ) {
                            if( o.code == 200 ) {
                                sysTask.changeTask( sysTask.task_type );
                                layer.alert(o.message,{icon:1,time:1000}, function () {
                                    sysTask.reset();
                                });
                                setTimeout('sysTask.reset();' ,1000 );
                            }else{
                                layer.alert(o.message,{icon:2});
                            }
                        }
                    });
                });

            },
            
            //查询日志信息
            searchLog: function( page, task_id ,_id) {
                if (task_id) {
                    sysTask.show_task_id = task_id
                }
                if (_id) {
                    sysTask._id = _id
                }

                layer.open({
                    type: 1,
                    title: '任务日志列表',
                    area: ['800px','450px'], //宽高
                    content: $('#openLog'),
                    success: function() {
                        layui.use(['table','form'], function(){
                            var table = layui.table;

                            tdom= table.render({
                                elem: '#openProductTable',
                                height: ['400px', '280px'],
                                url: "/admin/task/log",
                                where: {
                                    _id: sysTask._id,
                                    task_id: sysTask.show_task_id,
                                    sortname: 'start_time',
                                    sortorder: 'DESC',
                                    rp: 8
                                },
                                page: true, //开启分页
                                cols: [[
                                    {field: 'start_time', title: '任务执行时间', width:'30%',align:'center'},
                                    {field: 'end_time', title: '任务结束时间', align:'center', width:'30%'},
                                    {field: 'total_time', title: '总耗时时间', align:'left', width:'20%'},
                                    {field: 'result', title: '任务执行结果', align:'left',width:'20%'}
                                ]]
                            });
                        });
                    }
                });
            },
            search: function ( ) {
                var task_name = $("#taskName").val();
                var task_type = $("#task_type").val();
                var task_link = $("#taskLink").val();

                tdom.reload({
                    where: {
                        task_type: task_type,
                        task_name: task_name,
                        task_link: task_link
                    },
                    page: {
                        curr: 1
                    }
                });
            },
            clear: function () {
                $("#taskName").val("");
                $("#taskLink").val("");
                this.search();
            }
        };

        layui.use('element', function(){
            var element = layui.element;

        });
    </script>

@endsection