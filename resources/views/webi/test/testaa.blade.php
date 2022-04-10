@extends('webi.layout')

@section('css')
    <link rel="stylesheet" href="/css/webi/index.css?v=201803220950">
    <style>

    </style>
@endsection

@section('content')
    <div class="content">

        <!--头部-->
        <div class="top-title over">
            <h6 class="fl">WeBI</h6>
            <div class="fr right-inner">
                <a href="javascript:;" class="preview">预览</a>
                <a href="javascript:;" class="top-btn overview">数据集总览</a>
                <a href="javascript:;" class="top-btn addBi" onclick="bi_obj.choose_bi();">添加BI</a>
                <a href="javascript:;" class="top-btn global-attr">全局属性</a>
            </div>
        </div>

        <div class="main">
            <div class="left-list slient side-part">
                <div class="left-side-show side-show" id="left-side-show"></div>
                <div class="part-title over">
                    <span class="fl">BI设计</span>
                    <i class="fr icon side-icon"  data-type="left"><img src="/images/webi/biedit/icon-arr1.png" alt=""></i>
                </div>
                <div class="list-cont">
                    <div class="list-item">
                        <div class="title-box">
                            <div class="title">
                                <span class="fl">数据集</span>
                                <i class="fr icon down-arrow"></i>
                            </div>
                        </div>
                        <div class="select-box">
                            <select>
                                <option value="0">请选择数据集</option>
                                @if ( isset($rule) && !empty($rule) )
                                    @foreach( $rule as $k=>$v)
                                        <option onclick="edit.get_rule({{$v['table_id']}})" value="{{$v['table_id']}}">{{$v['table_name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="title-box">
                            <div class="title">
                                <span class="fl">汇总</span>
                                <i class="fr add-show" data-id="1"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                            </div>
                        </div>
                        <div class="item-inner" id="item-inner-1">
                            <ul class="ul-1">
                                <li>
                                    <div class="sub-item re-input" id="re-input-cal_date" style="display: none;padding: 0;">
                                        <input type="text" class="form-control" value="cal_date">
                                    </div>
                                    <div class="sub-item" id="re-show-cal_date">
                                        <span data-id="cal_date">cal_date</span>
                                        <i class="fr">...</i>
                                    </div>
                                    <div class="operation" style="display: none">
                                        <div class="operation-1">
                                            <p class="m-o rename" data-id="cal_date">重命名</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="m-o delete">删除</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">排序</p>
                                            <div class="operation-2 order order-show" style="display: none">
                                                <p data-id="ASC">升序</p>
                                                <p data-id="DESC">降序</p>
                                            </div>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">显示</p>
                                            <div class="operation-2 appear-show" style="display: none">
                                                <p>1</p>
                                                <p>2</p>
                                                <p>10</p>
                                                <p>自定义</p>
                                                <div class="operation-3" style="display: none">
                                                    <input type="text" name="" id="">
                                                    <div class="operation-3-btn">
                                                        <button onclick="">确定</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="title-box">
                            <div class="title">
                                <span class="fl">行设置</span>
                                <i class="fr add-show" data-id="2"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                            </div>
                        </div>
                        <div class="item-inner" id="item-inner-2">
                            <ul class="ul-2">
                                <li>
                                    <div class="sub-item">
                                        <span>cal_date</span>
                                        <i class="fr">...</i>
                                    </div>
                                    <div class="operation" style="display: none">
                                        <div class="operation-1">
                                            <p class="m-o rename">重命名</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="m-o delete">删除</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">排序</p>
                                            <div class="operation-2 order order-show" style="display: none">
                                                <p class="check" data-id="ASC">升序</p>
                                                <p data-id="DESC">降序</p>
                                            </div>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">显示</p>
                                            <div class="operation-2 appear-show" style="display: none">
                                                <p>1</p>
                                                <p>2</p>
                                                <p>10</p>
                                                <p>自定义</p>
                                                <div class="operation-3" style="display: none">
                                                    <input type="text" name="" id="">
                                                    <div class="operation-3-btn">
                                                        <button onclick="">确定</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="title-box">
                            <div class="title">
                                <span class="fl">列设计</span>
                                <i class="fr add-show" data-id="3"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                            </div>
                        </div>
                        <div class="item-inner" id="item-inner-3">
                            <ul class="ul-3">
                                <li>
                                    <div class="sub-item">
                                        <span>cal_date</span>
                                        <i class="fr">...</i>
                                    </div>
                                    <div class="operation" style="display: none">
                                        <div class="operation-1">
                                            <p class="m-o rename">重命名</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="m-o delete">删除</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">排序</p>
                                            <div class="operation-2 order order-show" style="display: none">
                                                <p class="check" data-id="ASC">升序</p>
                                                <p data-id="DESC">降序</p>
                                            </div>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">显示</p>
                                            <div class="operation-2 appear-show" style="display: none">
                                                <p>1</p>
                                                <p>2</p>
                                                <p>10</p>
                                                <p>自定义</p>
                                                <div class="operation-3" style="display: none">
                                                    <input type="text" name="" id="">
                                                    <div class="operation-3-btn">
                                                        <button onclick="">确定</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="title-box">
                            <div class="title">
                                <span class="fl">排序设置</span>
                                <i class="fr add-show" data-id="4"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                            </div>
                        </div>
                        <div class="item-inner" id="item-inner-4">
                            <ul class="ul-4">
                                <li>
                                    <div class="sub-item">
                                        <span>cal_date</span>
                                        <i class="fr">...</i>
                                    </div>
                                    <div class="operation" style="display: none">
                                        <div class="operation-1">
                                            <p class="m-o rename">重命名</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="m-o delete">删除</p>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">排序</p>
                                            <div class="operation-2 order order-show" style="display: none">
                                                <p data-id="ASC">升序</p>
                                                <p data-id="DESC">降序</p>
                                            </div>
                                        </div>
                                        <div class="operation-1">
                                            <p class="oper-arr m-o">显示</p>
                                            <div class="operation-2 appear-show" style="display: none">
                                                <p>1</p>
                                                <p>2</p>
                                                <p>10</p>
                                                <p>自定义</p>
                                                <div class="operation-3" style="display: none">
                                                    <input type="text" name="" id="">
                                                    <div class="operation-3-btn">
                                                        <button onclick="">确定</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!--添加字段弹层-->
                <div class="field-info" id="field-info-1" style="display: none">
                    <div class="field-info-title">
                        <p>添加字段</p>
                        <i class="field-info-close">×</i>
                    </div>
                    <div class="field-info-inner">
                        <ul>
                        </ul>
                    </div>
                    <div class="field-inf-btn">
                        <button onclick="edit.get_field(1)">确定</button>
                    </div>
                </div>

                <!--底部删除-->
                <div class="bottom-oper">
                    <div class="change fr">
                        <a href="javascript:;">行列互换</a>
                    </div>
                    <div class="delete fr">
                        <a href="javascript:;">清空所有设置</a>
                    </div>
                </div>
            </div>

            <!--主内容-->
            <div class="main-cont pdleft pdright parent" id="parent_main">

                {{--<div class="list-inner top-grid" id="grid_5">--}}
                {{--<div class="list-oper" id="move_5">--}}
                {{--<span class="oper-icon chart-operation"></span>--}}
                {{--<div class="oper-box" style="display: none;">--}}
                {{--<a href="javascript:;" class="chart-delete" data-id="5">删除</a>--}}
                {{--<a href="javascript:;" class="chart-copy" data-id="5">复制</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="chart" id="chart_5">--}}
                {{--<img src="/images/webi/biedit/img-2.jpg" alt="">--}}
                {{--</div>--}}
                {{--<i class="icon-drag" data-id="5"></i>--}}
                {{--</div>--}}

                {{--<div class="list-inner top-grid" id="grid_$uid">--}}
                {{--<div class="list-oper" id="move_$uid">--}}
                {{--<span class="oper-icon chart-operation"></span>--}}
                {{--<div class="oper-box" style="display: none;">--}}
                {{--<a href="javascript:;" class="chart-delete" data-id="$uid">删除</a>--}}
                {{--<a href="javascript:;" class="chart-copy" data-id="$uid">复制</a>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="chart" id="chart_$uid">--}}
                {{--<img src="/images/webi/biedit/img-2.jpg" alt="">--}}
                {{--</div>--}}
                {{--<i class="icon-drag" data-id="$uid"></i>--}}
                {{--</div>--}}

            </div>

            <!--右侧属性-->
            <div class="right-set side-part slient">
                <div class="right-set-show side-show" id="right-set-show"></div>
                <div class="part-title over">
                    <span class="fl">属性</span>
                    <i class="fr icon side-icon" data-type="right"><img src="/images/webi/biedit/icon-arr2.png" alt=""></i>
                </div>
                <div class="setting-cont">
                    <div class="set-inner name">
                        <p>名称</p>
                        <input type="text">
                    </div>
                    <div class="set-inner">
                        <p>背景色</p>
                        <div class="color">
                            <i></i>
                        </div>
                    </div>
                    <div class="set-inner">
                        <p>背景图片</p>
                        <div class="upload-box">
                            <div class="upload fl checked">
                                <p>背景照片</p>
                                <div class="bg-box">
                                    <div class="add-img">
                                        <span>+</span>
                                        <p>上传</p>
                                    </div>
                                </div>
                            </div>
                            <div class="upload fr">
                                <p>边框照片</p>
                                <div class="bg-box">
                                    <div class="add-img">
                                        <span>+</span>
                                        <p>上传</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="set-inner">
                        <p>边框样式</p>
                        <div class="border-style">
                            <div class="style">
                                <p>粗细</p>
                                <input type="text" value="0">
                            </div>
                            <div class="style">
                                <p>偏移</p>
                                <input type="text" value="0">
                            </div>
                            <div class="style">
                                <p>重复</p>
                                <select name="" id="">
                                    <option value="">拉伸</option>
                                    <option value="">收缩</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript" src="/js/webi/webi.comm.js"></script>
    <script type="text/javascript" src="/js/webi/webi.min.js"></script>

    <script>

        var fields_json = '';
        var domain_name = {!! $domain_name !!};   //域名
        var group_id = {!! $group_id !!};   //主表分组id
        var bi_id = {!! $bi_id !!}; //主表id
        var webi_dt = {!! $webi_dt !!} //调用报表接口数组
        var bi_content = {!! $bi_content !!}  //操作对象

            $(".select-box").hide();

        $(document).on('click','.side-icon',function(){     //左右模块隐藏
            var type = $(this).attr('data-type');
            console.log(type);
            if($(this).parents('.side-part').hasClass('slient')){
                if(type == 'left'){
                    $('.main-cont').removeClass('pdleft');
                    setTimeout(function(){
                        $('#left-side-show').show();
                    },500)
                } else {
                    $('.main-cont').removeClass('pdright');
                    setTimeout(function(){
                        $('#right-set-show').show();
                    },500)
                }
                $(this).parents('.side-part').removeClass('slient')
            }
            tiaozheng();
        }).on('click','.side-show',function(){     //左右模块展示
            $(this).hide();
            $(this).parents('.side-part').addClass('slient');
            if($(this).hasClass('left-side-show')){
                $('.main-cont').addClass('pdleft');
            } else {
                $('.main-cont').addClass('pdright');
            }
            tiaozheng();
        }).on('click','.up-arrow',function () {  //隐藏数据集
            $(".select-box").hide();
            $(this).removeClass('up-arrow').addClass('down-arrow');
        }).on('click','.down-arrow',function () {  // 展示数据集
            $(".select-box").show();
            $(this).removeClass('down-arrow').addClass('up-arrow');
        }).on('click','.add-show',function () { //显示字段列表
            $('.field-info').find('li').find('input').iCheck('uncheck');
            $(".operation").hide();
            if ( $.isEmptyObject(fields_json) ) {
                layer.alert('请选择数据集',{icon:3,offset:'50px',time:1500});
                return false;
            }
            $(".field-info").toggle();
            var id = $(this).attr('data-id');
            $(".field-info").attr("id",'field-info-'+id);
            $(".field-info .field-inf-btn button").attr("onclick",'edit.get_field('+ id +');');
        }).on('click','.field-info-close',function () { //关闭字段列表
            $(this).parent().parent().hide();
        }).on('click','.sub-item .fr',function (e) { //显示一级操作
            $(".operation").hide();

            var pageY = e.pageY; //获取鼠标点击位置高度
            var p_class = $(this).parent().parent().parent().attr('class');

            if ( p_class == 'ul-4' ){
                $(".item-inner ul li .operation").css('top',pageY-75);
            } else {
                $(".item-inner ul li .operation").css('top',pageY-110);
            }

            $(this).parent().next().toggle();
        }).on('mouseover','.operation-1',function () { //显示二级操作
            $('.m-o').removeClass('oper-chos');
            $(this).find('.m-o').addClass('oper-chos');
            $(".operation-2").hide();
            $(this).find('.operation-2').show();
        }).on('mouseover','.operation-1 p',function () { //显示三级操作
            $(".operation-3").hide();
            $(this).addClass('check').siblings().removeClass('check');
            if ( $(this).index() == 3 ) {
                $(".operation-3").show();
            }
        }).on('click','.operation-1 .delete',function () {
            var $class = $(this).parent().parent().parent().parent().attr('class');
            $(this).parent().parent().parent().remove();
            //ul中没有li时，改变父级样式
            if ( $('.'+$class+' li').length == 0 ) {
                $('.'+$class).parent().css('padding','0 7px');
            }
        }).on('click','.operation-1 .rename',function () {
            var id = $(this).attr('data-id');
            $('#re-show-'+id).hide();
            $('#re-input-'+id).show();
            $('#re-input-'+id+' input').focus();
            $(".option").hide();
        }).on('click','.chart-operation',function () { //单一报表操作列表
            $(this).next('.oper-box').toggle();
        }).on('click','.chart-delete',function () { //报表删除
            var uid = $(this).attr('data-id');
            console.log(uid);
            $("#grid_"+uid).remove();
        }).on('click','.chart-copy',function () { //报表复制
            var uid = $(this).attr('data-id');
            console.log(uid);
        }).on('keydown','.re-input input',function () {//给输入框绑定按键事件
            if(event.keyCode == "13") {//判断如果按下的是回车键则执行下面的代码
                $(this).parent().hide();
                $(this).parent().next().find('span').html($(this).val());
                $(this).parent().next().show();
            }
        }).on('click','.operation-1 .order p',function () {
            var order_type = $(this).attr('data-id');

        });

        //根据所选数据集获取字段信息
        $(document).ready(function () {
            $(".select-box").change(function () {
                var id = $(".select-box").find("option:selected").val();
                if ( id>0) {
                    edit.get_rule(id);
                }
            });
        });

        var  field_sum = []; //汇总字段
        var  field_row = []; //行字段
        var  field_col = []; //列字段
        var  field_order = []; //排序字段

        var edit = {

            create_order:function (order) {

            },

            create_field:function ( data ) {
                var html = '';
                $.each(data,function (k,v) {
                    html += '<li style="margin-bottom: 5px;">';
                    html += '<input type="checkbox" class="square-radio redrio" name="fields" data-id="'+ k+1 +'" value="'+ v['field_name'] +'" data-mark="'+v['field_remark']+'">   '+v['field_remark'];
                    html += '</li>';
                });
                $(".field-info").find('ul').empty();
                $(".field-info").find('ul').append(html);

                init();
            },

            get_field:function(type) {

                var field_obj = $('#field-info-'+ type +' input[name="fields"]:checked');

                var fields = [];
                if ( field_obj.length > 0 ) {
                    $.each(field_obj, function () {
                        fields.push($(this).val());

                        var msg = '';

                        if ( type == 1 && $.inArray($(this).val(), field_sum) != -1 ) {
                            msg = '存在重名字段';
                        } else if ( type == 2 && $.inArray($(this).val(), field_row) != -1 ) {
                            msg = '存在重名字段';
                        } else if ( type == 3 && $.inArray($(this).val(), field_col) != -1 ) {
                            msg = '存在重名字段';
                        } else if ( type == 4 && $.inArray($(this).val(), field_order) != -1 ) {
                            msg = '存在重名字段';
                        }

                        if ( msg ) {
                            layer.alert(msg,{icon:2,offset:'50px'});
                            return false;
                        }

                        if ( type == 1 ) {
                            field_sum.push($(this).val());
                        } else if ( type == 2 ) {
                            field_row.push($(this).val());
                        } else if ( type == 3 ) {
                            field_col.push($(this).val());
                        } else if ( type == 4 ) {
                            field_order.push($(this).val());
                        }

                        var html = '';
                        html +='<li>';
                        if ( $.inArray(type, [1,2,3]) != -1 ) {
                            html +='<div class="sub-item re-input" id="re-input-'+ $(this).val() +'" style="display: none;padding: 0;">';
                            html +='<input class="form-control" value="'+ $(this).attr('data-mark') +'">';
                            html +='</div>';
                        }
                        html +='<div class="sub-item" id="re-show-'+ $(this).val() +'">';
                        html +='<span data-id="'+ $(this).val() +'">'+ $(this).attr('data-mark') +'</span>';
                        html +='<i class="fr">...</i>';
                        html +='</div>';
                        html +='<div class="operation" style="display: none">';
                        if ( $.inArray(type, [1,2,3]) != -1 ) {
                            html +='<div class="operation-1">';
                            html +='<p class="m-o rename" data-id="'+ $(this).val() +'">重命名</p>';
                            html +='</div>';
                        }
                        html +=' <div class="operation-1">';
                        html +='<p class="m-o delete">删除</p>';
                        html +='</div>';
                        html +='<div class="operation-1">';
                        html +='<p class="oper-arr m-o">排序</p>';
                        html +='<div class="operation-2 order order-show" style="display: none">';
                        html +='<p class="check" data-id="ASC">升序</p>';
                        html +='<p data-id="DESC">降序</p>';
                        html +='</div>';
                        html +='</div>';
                        html +='<div class="operation-1">';
                        html +='<p class="oper-arr m-o">显示</p>';
                        html +='<div class="operation-2 appear-show" style="display: none">';
                        html +='<p>1</p>';
                        html +='<p>2</p>';
                        html +='<p>10</p>';
                        html +='<p>自定义</p>';
                        html +='<div class="operation-3" style="display: none">';
                        html +='<input type="text" name="" id="">';
                        html +='<div class="operation-3-btn">';
                        html +='<button onclick="">确定</button>';
                        html +='</div>';
                        html +='</div>';
                        html +='</div>';
                        html +='</div>';
                        html +='</div>';
                        html +='</li>';

                        $("#item-inner-"+type).find('ul').append(html);

                        //ul中有li时，改变父级样式
                        if ( $('.ul-'+type+' li').length>0 ) {
                            $('.ul-'+type).parent().css('padding','5px 7px');
                        }

                    });
                }

                $(".field-info-close").click();

            },

            get_rule:function (id) {

                if ( id == '' ) {
                    layer.alert('请选择数据集',{icon:2,offset:'70px'});
                    return false;
                }

                $.ajax({
                    type:'get',
                    url:'/webi/get/rule/'+id,
                    success:function(obj){

                        if (obj.code == 200) {
                            if ( obj.data ) {
                                fields_json = obj.data;
                                edit.create_field(obj.data);
                            }
                        }else{
                            layer.alert(obj.message,{icon:2});
                        }
                    }

                })

            },

            show:function () {
                layer.open({
                    title:'添加字段',
                    type:1,
                    content: $('#filed_info'),
                    shadeClose: true, //点击遮罩关闭
                    area: ['500px', '300px'],
                    offset: ['100px', '50px'],
                })
            },

            save:function (  ) {

            }

        };

        //添加BI弹层
        var choose_tip = '';

        var bi_obj = {

            choose_bi:function () {
                choose_tip =  layer.open( {
                    title: false ,
                    type: 2 ,
                    area: ['100%', '100%'] ,
                    scrollbar: false ,
                    offset: '0px' ,
                    closeBtn: 0,
                    content: domain_name+'/webi/edit/choose'
                } );
            },

//            delete_bi:function ( uid ) {
//
//            },

            //复制
            copy_bi:function ( uid ) {

            },

            //新建BI子模板
            module:function ( chart_id ) {

                if ( !chart_id ) {
                    layer.alert('请选择模板',{icon:2,offset:'70px'});
                    return false;
                }

                $.ajax({
                    type:'post',
                    url:'/webi/create/module',
                    data:{
                        chart_id:chart_id,
                        bi_id:bi_id
                    },
                    success:function(obj){

                        if (obj.code == 200) {
                            if ( obj.data ) {
                                fields_json = obj.data;
                                edit.create_field(obj.data);
                            }
                        }else{
                            layer.alert(obj.message,{icon:2});
                        }
                    }

                })

            }

        };

        function init() {
            //  单选复选框
            $('.square-radio').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        }

        init();

    </script>

    <script type="text/javascript">

        var uid_html = '';
        uid_html += '<div class="list-inner top-grid" id="grid_$uid">';
        uid_html += '<div class="list-oper" id="move_$uid">';
        uid_html += '<span class="oper-icon chart-operation"></span>';
        uid_html += '<div class="oper-box" style="display: none;">';
        uid_html += '<a href="javascript:;" class="chart-delete" data-id="$uid">删除</a>';
        uid_html += '<a href="javascript:;" class="chart-copy" data-id="$uid">复制</a>';
        uid_html += '</div>';
        uid_html += '</div>';
        uid_html += '<div class="chart" id="chart_$uid">';
        uid_html += '</div>';
        uid_html += '<i class="icon-drag" data-id="$uid"></i>';
        uid_html += '</div>';

        //操作对象
        bi_content = {
            "grid_1": {
                "id": "grid_1",
                "height": 100,
                "top": 10,
                "width_percent": "0.4500",
                "left_percent": "0.012500"
            },
            "grid_3": {
                "id": "grid_3",
                "height": 100,
                "top": 200,
                "width_percent": "0.3125",
                "left_percent": "0.012500"
            },
            "grid_2": {
                "id": "grid_2",
                "height": 100,
                "top": 200,
                "width_percent": "0.4000",
                "left_percent": "0.500000"
            },
            "grid_4": {
                "id": "grid_4",
                "height": 100,
                "top": 10,
                "width_percent": "0.2500",
                "left_percent": "0.500000"
            },
            "grid_5": {
                "id": "grid_5",
                "height": 100,
                "top": 10,
                "width_percent": "0.2500",
                "left_percent": "0.500000"
            }

        };

        webi_dt = {
            "master": {
                'project_id':'10387',
                'domain':'www.weishop.com',
                "title": "会员每日报表",
                "border": "1px",
                "backgroundColor": "#2EFEF7",
                "backgroundImage": "http://120.236.240.186:8080/uploads/img/6.jpg",
            },
            "module": {
                "748E4F6292E00573EB7FDE83BF4C37A1": {
                    "bi_json": {
                        "group_id": "group_id",
                        "chart_id": "chart_id",
                        "type": "bar",
                        "chart_json": {
                            xAxis: {
                                type: 'category'
                            },
                            yAxis: {
                                type: 'value'
                            },
                            series:{
                                type: 'bar'
                            }
                        }
                    },
                    "db_json": {
                        "row": "date",
                        "column": 'newMessageNum:新消息总数,totalCustNum,newCustNum',
                        "sum": "totalCustNum",
                        "table": "stat_date_cust",
                        "limit": "10",
                        "sort": "date DESC"
                    },
                    "attribute_json": {
                        "title": "会员每日报表",
                        "height": "200",
                        "top": "10",
                        "border": "1px",
                        "backgroundColor": "#2EFEF7",
                        "border_image_source": "http://120.236.240.186:8080/uploads/img/biankuang%20(51).png",
                        "width_percent": "0.3100",
                        "left_percent": "0.012500"
                    }
                },
                "F72EBC3CDBC971CBBB1A8773FE9F9A9B": {
                    "bi_json": {
                        "group_id": "group_id",
                        "chart_id": "chart_id",
                        "type": "line",
                        "chart_json": {
                            xAxis: {
                                type: 'category'
                            },
                            yAxis: {
                                type: 'value'
                            },
                            series:{
                                type: 'line'
                            }
                        }
                    },
                    "db_json": {
                        "row": "date",
                        "column": 'newMessageNum:总数,newCustNum:新会员',
                        "sum": "totalCustNum",
                        "table": "stat_date_cust",
                        "limit": "10",
                        "sort": "date DESC"
                    },
                    "attribute_json": {
                        "title": "折线图",
                        "height": "200",
                        "top": "10",
                        "border": "1px",
                        "backgroundColor": "#2EFEF7",
                        "border_image_source": "http://120.236.240.186:8080/uploads/img/biankuang%20(51).png",
                        "width_percent": "0.2800",
                        "left_percent": "0.350000"
                    }
                },
                "2C74791C198EA828C30DC3B56DDDED6F": {
                    "bi_json": {
                        "group_id": "group_id",
                        "chart_id": "chart_id",
                        "type": "bar",
                        "chart_json": {
                            tooltip : {
                                trigger: 'axis'
                            },
                            toolbox: {
                                show : true,
                                feature : {
                                    dataView : {show: true, readOnly: false},
                                    magicType : {show: true, type: ['line', 'bar']},
                                    restore : {show: true},
                                    saveAsImage : {show: true}
                                }
                            },
                            calculable : true,
                            xAxis : [
                                {
                                    type : 'category',
                                }
                            ],
                            yAxis : [
                                {
                                    type : 'value'
                                }
                            ],
                            series : {
                                name:'',
                                type:'bar',
                                markPoint : {
                                    data : [
                                        {type : 'max', name: '最大值'},
                                        {type : 'min', name: '最小值'}
                                    ]
                                },
                                markLine : {
                                    data : [
                                        {type : 'average', name: '平均值'}
                                    ]
                                }
                            }
                        }
                    },
                    "db_json": {
                        "row": "date",
                        "column": 'newMessageNum:总数,newCustNum:新会员',
                        "sum": "totalCustNum",
                        "table": "stat_date_cust",
                        "limit": "10",
                        "sort": "date DESC"
                    },
                    "attribute_json": {
                        "title": "柱状&折线",
                        "height": "200",
                        "top": "10",
                        "border": "1px",
                        "backgroundColor": "#2EFEF7",
                        "border_image_source": "http://120.236.240.186:8080/uploads/img/biankuang%20(51).png",
                        "width_percent": "0.3400",
                        "left_percent": "0.650000"
                    }
                },
                "748E4F6292E00573EB7FDEAKD82N32": {
                    "bi_json": {
                        "group_id": "group_id",
                        "chart_id": "chart_id",
                        "type": "map",
                        "chart_json": {
                            title: {
                                left: 'center'
                            },
                            tooltip: {
                                trigger: 'item'
                            },
                            legend: {
                                orient: 'vertical',
                                left: 'left'
                            },
                            visualMap: {
                                min: 0,
                                max: 20000,
                                left: 'left',
                                top: 'bottom',
                                text: ['高','低'],
                                calculable: true
                            },
                            toolbox: {
                                show: true,
                                orient: 'vertical',
                                left: 'right',
                                top: 'center',
                                feature: {
                                    dataView: {readOnly: false},
                                    restore: {},
                                    saveAsImage: {}
                                }
                            },
                            series: {
                                name: '',
                                type: 'map',
                                mapType: 'china',
                                roam: false,
                                label: {
                                    normal: {
                                        show: true
                                    },
                                    emphasis: {
                                        show: true
                                    }
                                }
                            }
                        }
                    },
                    "db_json": {
                        "row": "province:省份",
                        "column": 'cust_num',
                        "sum": "cust_num",
                        "table": "stat_region_analyse",
                        "limit": "10",
                        "sort": ""
                    },
                    "attribute_json": {
                        "title": "区域统计",
                        "height": "450",
                        "top": "230",
                        "border": "1px",
                        "backgroundColor": "#2EFEF7",
                        "border_image_source": "http://120.236.240.186:8080/uploads/img/biankuang%20(51).png",
                        "width_percent": "0.9800",
                        "left_percent": "0.012500"
                    }
                }
            }
        };

        WeBI.op.a('parent_main', webi_dt, uid_html);

        function tiaozheng(){
            setTimeout(function () {
                var main_width = document.getElementById('parent_main').offsetWidth;
                console.log(main_width);

                for( k in bi_content ){
                    document.getElementById(k).style.width = (bi_content[k].width_percent * main_width) + 'px';
                    document.getElementById(k).style.left = (bi_content[k].left_percent * main_width) + 'px';
                    var chart_id = k.replace('grid_','chart_');
                    document.getElementById(chart_id).style.width = document.getElementById(k).style.width;
                    console.log(document.getElementById(chart_id).style.width);
                };
                WeBI.chart.resize();
            },500)
        }

        function getNew(){
            var main_width = document.getElementById('parent_main').offsetWidth;
            var lis = document.querySelectorAll('.top-grid');
            var temp_obj = {};
            for(var i=0; i<lis.length; i++){
                temp_obj[lis[i].id] = {
                    id:lis[i].id,
                    top:lis[i].offsetTop,
                    height:lis[i].offsetHeight,
                    width_percent:(lis[i].offsetWidth/main_width).toFixed(4),
                    left_percent:(lis[i].offsetLeft/main_width).toFixed(6)
                }
            }
            bi_content = temp_obj;
//                console.log(temp_obj);
        }

        getNew();

        //触发鼠标的操作对象信息
        var op = {
            obj:'',
            maxLeft:0,
            maxTop:0,
            disx:0,
            disy:0
        };

        function mousedown(ev){

            var event=ev||event;

            var id = 'grid_'+event.target.id.substring(5);

            if ( !bi_content[id] ) {
                return false;
            }

            op.obj = $("#"+id);

            if( op.obj[0].className.search("top-grid") != -1 ){
                var main_offsetLeft = document.getElementById('parent_main').offsetLeft;
                var main_offsetTop = document.getElementById('parent_main').offsetTop;
                var main_offsetWidth = document.getElementById('parent_main').offsetWidth;
                var main_offsetHeight = document.getElementById('parent_main').offsetHeight;
                op.maxLeft = main_offsetWidth - op.obj[0].offsetWidth;
                op.maxTop = main_offsetHeight - op.obj[0].offsetHeight;
            } else {
                op.maxLeft = op.obj[0].offsetLeft;
                op.maxTop = op.obj[0].offsetTop;
            }

            op.disx = event.clientX-op.obj[0].offsetLeft;
            op.disy = event.clientY-op.obj[0].offsetTop;

            document.onmousemove = mousemove;
            document.onmouseup = mouseup;

        }

        function mousemove(ev){

            var oEvent = ev || event;

            if( op.obj == '' ){
                return false;
            }

            var sildLeft = oEvent.clientX-op.disx;
            var slidTop = oEvent.clientY-op.disy;

            if( sildLeft <= 0 ){
                sildLeft = 0;
            }
            if( sildLeft >= op.maxLeft ){
                sildLeft = op.maxLeft;
            }
            if( slidTop <= 0 ){
                slidTop = 0;
            }
            if( slidTop >= op.maxTop ){
                slidTop = op.maxTop;
            }

            op.obj[0].style.left = sildLeft+"px";
            op.obj[0].style.top = slidTop+"px";

        }

        function mouseup(){

            var main_width = document.getElementById('parent_main').offsetWidth;

            var new_position = {
                id:op.obj[0].id,
                top:op.obj[0].offsetTop,
                height:op.obj[0].offsetHeight,
                width_percent:(op.obj[0].offsetWidth/main_width).toFixed(4),
                left_percent:(op.obj[0].offsetLeft/main_width).toFixed(6)
            };
            bi_content[op.obj[0].id].top = new_position.top;
            bi_content[op.obj[0].id].left_percent = new_position.left_percent;

//            console.log(new_position);

            document.onmousemove=null;
            document.onmouseup=null;
        }

        //批量绑定事件
        var lis = document.querySelectorAll('.top-grid');
        for(var i=0; i<lis.length; i++){
            BI.event.bind(lis[i],'mousedown',mousedown);
        }

    </script>

@endsection