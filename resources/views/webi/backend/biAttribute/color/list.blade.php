@extends('backend')

@section('title')
    报表颜色
@endsection

@section('css')
    <link rel="stylesheet" href="/css/webi/list.css?v=201803221330">

    <style>
        .dropdown-menu{
            min-width: 60px;
            display: none;
        }
        .iradio-blue{
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            padding: 0;
            width: 22px;
            height: 22px;
            background-color:blue;
            border: none;
            cursor: pointer;
        }
        .add-list a:hover{
            text-decoration:none;
        }
        .test-t{
            text-align: center;
            max-width: 670px;
            height:33px;
            padding: 10px;
            margin-left:26%;
            font-size: 14px;
            float: right;
        }
        .user-dropdown{
            position: absolute;
            float: left;
            display: block;
            margin-left: 15px;
            text-decoration: none;
            cursor: pointer;
            outline: none;
            padding-right: 10px;
        }
        .user-dropdown{
            position: relative;
        }
        .user-dropdown .user{
            margin-top: -7px;
        }
        .user .username-control{
            font-size: 16px;
            color: #5bc0de;
            max-width:137px;
            height: 40px;
            padding: 0 12px;
        }
        .arrows{
            position: absolute;
            top: 5.3px;
            width: 25px;
            height: 34px;
            background: url(/images/webi/biedit/icon-arr3.png) no-repeat center center;
        }
        .user-dropdown-meta{
            height:25px;
            line-height: 18px;
        }
        .operation{
            padding-top: 55.4px;
        }
        .operation li{
            background-color: white;
            box-shadow: 3px 5px 4px rgba(0,0,0,.05);
            -moz-box-shadow:3px 5px 4px rgba(0,0,0,.05);
            -webkit-box-shadow:3px 5px 4px rgba(0,0,0,.05);
        }
        .test-t .top-btn {
            margin-top: 5px;
            width: 100px;
            height: 32px;
            text-align: center;
            line-height: 32px;
            color: #fff;
            border-radius: 20px;
        }
        .test-t .overview {
            background: #5cd489;
        }
        .test-t .addBi {
            background: #70a7ff;
        }
        .test-t a{
            float: left;
            display: block;
            margin-right: 20px;
            text-decoration: none;
            cursor: pointer;
            outline: none;
        }
        .user-dropdown-meta a{
            width:107px;
            margin-left: 0;
            text-align: center;
            height:25px;
            line-height: 22px;
        }
        .selected-lable{
            box-shadow: 0 0 0 2px rgba(81,130,227,.15),inset 0 0 0 2px #108ee9 !important;
            /*height:31px;*/
        }
        .layer-comfirm{
            margin-left: 400px;
            width: 244px;
            height:300px;
            border: 1px solid #d2d2d2;
            background-color: #fff;
            -webkit-background-clip: content;
            border-radius: 2px;
            box-shadow: 1px 1px 50px rgba(0,0,0,.3);
        }
        .layer-comfirm .layer-title{
            padding: 0 20px 0 20px;
            height: 42px;
            line-height: 42px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
            overflow: hidden;
            background-color: #F8F8F8;
            border-radius: 2px 2px 0 0;
        }
        .layer-wrap{
            line-height: 26px;
            margin-left: 20px;
            overflow: visible;
            list-style-type: disc;
        }
        .layer-wrap li{
            height:30px;
        }
        .layer-wrap li a{
            width: 244px;
            overflow-y:auto;
            overflow-x:hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
        }
        .glyphicon{
            font-size: 10px;
            top:14px;
            float:right;
            cursor: pointer;
        }
        .color{
            width: 194px;
            height: 30px;
            background-color: rgb(255, 255, 255);
            border: 1px solid;
            padding: 6px 12px;
        }
        .main-cont ul li{
            margin-bottom: 30px;
        }
    </style>
@endsection

@section('content')
    <div class="app-third-sidebar">
        <nav class="ui-nav" style="display: block;">
            <ul>
                <li class="active">
                    <a href="/webi/attribute/color/list"><span>图表颜色</span></a>
                </li>
                <li>
                    <a href="/webi/attribute/fontfamily/list"><span>字体维护</span></a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="wrapper">
        <div class="content" style="padding-top: 0;">

            <!--主内容-->
            <div class="main-cont" style="padding-left: 0;margin-left:20px">
                <div class="main-list">
                    <ul class="content-master">

                    </ul>
                </div>
            </div>

            {{--新建图表颜色li HTML--}}
            <div id="add-master"  style="display:none;" >
                <li class="add-form"  style="box-shadow: 0 0 3px 3px rgba(0,0,0,.05);" onclick=_M.addTable()>
                    <img src="/images/webi/bi/icon-ador.png" alt="">
                    <p>新建图表颜色</p>
                </li>
            </div>

            {{--颜色li HTML--}}
            <div id="master-content" style="display: none">
                <li style="box-shadow: 0 0 3px 3px rgba(0,0,0,.05);" id="master_bi_id"  >
                    <div type="button" style="height: 30px;float:right;padding-right: 8px;margin-top:-18px;cursor:  pointer;"  class="dropdown" >
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i><img src="/images/webi/bi/icon-line.png" style="width:10px;height:12px;"/></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right" style="padding:0;">
                            <li  ><a onclick="_M.edit('_id','bi_title')">编辑</a></li>
                            <li  ><a onclick="_M.del('_id');">删除</a></li>
                        </ul>
                    </div>

                    <div style="background-color:#ffffff;width: 86px;height: 86px;margin-left:40px;border-radius: 50%; " class="bkcolor">
                    </div>
                    <p>bi_title</p>
                </li>
            </div>

            {{--添加分组弹窗HTML--}}
            <div id="layer_add_group" style="display: none">
                <form id="title-form-header" onsubmit="return false;" class="layui-form" role="form" >
                    <input type="hidden" name="_id" id="_id" value="0">
                    <div class="layui-form-item">
                        <label class="col-sm-3 layui-form-label" for="title-header"><span class="red pr5">*</span>&ensp;颜色：</label>
                        <div class="color-pick layui-input-block">
                            <input type="text" class="color form-control" name="title-header" id="title-header" placeholder="请输入颜色代码" value="#FFFFFF">
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@section('js')

    <script type="text/javascript" src="/libs/colorpicker/jscolor.js"></script>

    <script>
        var _M = {};//报表操作对象

        var _G_Layer_Group = "";//添加分组弹窗HTML

        $(document).on('click','.dropdown',function(){
            $(this).children(".dropdown-menu").slideToggle(100);
        })

        _M.search = function(){
            $.ajax({
                type: 'get',
                url: '/webi/attribute/color/search/',
                success: function (res) {
                    if (res.code == 200) {

                        $('.content-master').empty();

                        var eHtml = $("#add-master").html();
                        $('.content-master').append(eHtml);

                        if (!($.isEmptyObject(res.master))) {
                            var creat = _M.create_master(res.master.data);
                            $('.content-master').append(creat);

                        }
                    }
                }
            });
        };

        //生成报表（li）
        _M.create_master = function(data){
            var master_html="";
            $.each(data, function (k, v) { 
                master_html+=$('#master-content').html();
                master_html = master_html.replace(/bi_title/g,"#"+v.color_code);
                master_html = master_html.replace(/#ffffff/g,"#"+v.color_code);
                master_html = master_html.replace(/_id/g,v._id);
            });
            return master_html;
        };
        //新建报表
        _M.addTable = function(){
            layer.open({
                title: '新增颜色',
                offset: '70px',
                type: 1,
                area: ['500px', '190px'],
                scrollbar: false,
                closeBtn: 0,
                zIndex:900,
                content: _G_Layer_Group,
                success: function(){
                    jscolor.bind();
                },
                btn: ['确认','取消'],
                yes: function () {
                    var dt = E.getFormValues('title-form');
                    if(dt.title ==""){
                        layer.msg( '请输入颜色代码！' , { icon: 2,offset:'70px;', time: 1500} ) ;
                        return false;
                    }

                    E.ajax({
                        type: 'post',
                        url: '/webi/attribute/color/add',
                        data: dt,
                        success: function (res) {
                            if (res.code == 200) {

                                layer.closeAll();
                                layer.msg('保存成功', {icon: 1, offset: '70px', time: 1500});

                                //页面追加新建报表
                                var creat = _M.create_master(res.message['master']);
                                $('.content-master').append(creat);

                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                            }
                        }
                    });
                }
            });
        };
        //编辑报表
        _M.edit = function(_id,bi_title){
            var _G_Layer_Group = "";
            _G_Layer_Group = $("#layer_add_group").html();
            _G_Layer_Group = _G_Layer_Group.replace(/title-form-header/g,"title-form");
            _G_Layer_Group = _G_Layer_Group.replace(/title-header/g,"title");
            _G_Layer_Group = _G_Layer_Group.replace(/#FFFFFF/g,bi_title);
            layer.open({
                title: '编辑颜色',
                offset: '70px',
                type: 1,
                area: ['500px', '190px'],
                scrollbar: false,
                closeBtn: 0,
                zIndex:900,
                content: _G_Layer_Group,
                success: function(){
                    jscolor.bind();
                },
                btn: ['确认','取消'],
                yes: function () {
                    var dt = E.getFormValues('title-form');
                    dt['_id'] = _id;
                    if(dt.title ==""){
                        layer.msg( '请输入颜色代码！' , { icon: 2,offset:'70px;', time: 1500} ) ;
                        return false;
                    }

                    E.ajax({
                        type: 'post',
                        url: '/webi/attribute/color/edit/'+ _id,
                        data: dt,
                        success: function (res) {
                            if (res.code == 200) {
                                
                                layer.closeAll();
                                layer.msg('修改成功', {icon: 1, offset: '70px', time: 1500});

                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                            }
                        }
                    });
                },
                end: function () {
                    _M.search();
                }
            });
        };
        //删除报表
        _M.del = function(_id){
            layer.confirm('是否确认删除?',{icon:3},function (index) {
                layer.close(index);
                $.ajax({
                    type: 'get',
                    url: '/webi/attribute/color/del/'+_id,
                    success: function (res) {
                        if (res.code == 200) {
                            $("#master_"+_id).remove();
                            layer.msg('删除成功', {icon: 1, offset: '70px', time: 1500});
                            _M.search();
                        } else {
                            layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                        }
                    }
                });
            })
        };

        //替换添加分组重要ID
        _G_Layer_Group = $("#layer_add_group").html();
        _G_Layer_Group = _G_Layer_Group.replace(/title-form-header/g,"title-form");
        _G_Layer_Group = _G_Layer_Group.replace(/title-header/g,"title");

        _M.search();
    </script>
@endsection

