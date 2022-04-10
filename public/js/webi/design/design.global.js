/**
 *  全局对象
 *
 *  操作函数集合
 */

var relation_text = {
    "1": '等于',
    "2": '大于',
    "3": '大于等于',
    "4": '小于',
    "5": '小于等于',
    "6": '包含于',
    "7": '不等于',
    "8": '中包含',
    "9": '介于'
};
var relation_type = {
    "1": '=',
    "2": '>',
    "3": '>=',
    "4": '<',
    "5": '<=',
    "6": 'IN',
    "7": '!=',
    "8": 'LIKE',
    "9": 'BETWEEN'
};

var _We_G = {};

_We_G.bi_id = 0; //报表ID
_We_G.bi_uid = ''; //报表uuid
_We_G.save_type = 1; //保存类型：1、全局属性  2、报表
_We_G.auto_refresh_id = {}; //自动刷新对象
_We_G.font_amily = {};//字体对象数组

layui.use(['upload'], function(){
    //上传模板展示图
    layui.upload.render({
        elem: '#upload-template-pic',
        url: '/upload?action=webi',
        done: function(res){

            if(res.code == 200){
                $(".theme-template-pic").val(res.data.url);

                //显示上传图片
                var html = '';
                html += '<div class="upload_photo_pic"><img style="width:100px;height:100px;border:1px solid #e2e2e2;" src="' + res.data.url + '"/>';
                html += '<a href="javascript: void(0);" onclick="_We_G.del_pic(this , \'upload_photo_pic\')" style="margin-left: 9px;">删除</a></div>';

                $(".theme-upload-icon").append(html);
            }

        }
    });
});

$(document).on('click','#head_preview',function () { //预览
    _We_G.biview();
}).on('click','#upload_theme',function () { //上传模板
    _We_G.biTheme();
}).on('click','#head_chooseBI',function () { //添加BI
    _We_G.chooseBI();
}).on('click','#head_We_Global_We_ATTR',function () { //点击全局属性
    _We_G.right_attribute();
}).on('click','.top-title,.part-title,#parent_We_Main',function () { //隐藏选择字段弹层和限制条件弹层
    _We_DTS.field.hide();
    _We_DTS.pop.hide();
}).on('mouseover','.left-side-show',function () { //鼠标滑过左展开按钮
    if(!$('#g_dts_main').hasClass('slient')){
        $(this).css('opacity','1');
    }
}).on('mouseleave','.left-side-show',function () { //鼠标离开左展开按钮
    $(this).css('opacity','0');
}).on('mouseover','#right-set-show',function () { //鼠标滑过右展开按钮
    if(!$('#g_attr_main').hasClass('slient')){
        $(this).css('opacity','1');
    }
}).on('mouseleave','#right-set-show',function () { //鼠标离开右展开按钮
    $(this).css( 'opacity','0');
});

//预览报表
_We_G.biview = function() {
    window.open('/webi/show?uid='+_We_G.bi_uid);
};
//上传模板
_We_G.biTheme = function(){

    if( typeof WeBI.webi_dt['module'] == 'undefined' || WeBI.webi_dt['module'] == ''  || WeBI.webi_dt['module'] == '[]'){
        layer.msg('请先设计报表',{icon: 2, offset: '70px', time: 1500});
        return false;
    }

    layer.open({
        title: '上传模板',
        offset: 'auto',
        type: 1,
        area: ['600px', '400px'],
        scrollbar: false,
        shade: '#000',
        shadeClose: true,
        closeBtn: 1,
        content: $('#ulpoadTheme'),
        btn: ['确认', '取消'],
        yes: function () {

            var msg = '';
            var template_group = '';
            $(".theme-upload-group input:checkbox[name='group']:checked").each(function() {
                template_group =  $(this).val();
            });
            var template_title = $('.theme-template-title').val();//document.getElementsByClassName('theme-template-title').value;
            var template_pic = $('.theme-template-pic').val();//document.getElementsByClassName('theme-template-pic').value;

            if( template_title == "" ){
                msg += '模板名称不可为空<br>';
            }
            if( template_group == "" ){
                msg += '模板分组不可为空<br>';
            }
            if( template_pic == "" ){
                msg += '模板展示图不可为空<br>';
            }

            if ( msg != '' ) {
                layer.msg(msg,{icon: 2, offset: '70px', time: 1500});
                return false;
            }

            layer.confirm('您确定要上传该模板吗？',{icon:3,offset:'50px'},function (index) {

                $.ajax({
                    type: 'post',
                    url: '/webi/design/template/add',
                    data: {
                        template_title:template_title,
                        template_group:template_group,
                        template_pic:template_pic,
                        bi_id:_We_G.bi_id
                    },
                    success: function (o) {

                        if (o.code == 200) {
                            layer.alert(o.message, {icon: 1, offset: '50px', time: 1500});

                            $(".theme-template-title").val('');
                            $(".theme-template-pic").val('');
                            $(".upload_photo_pic").remove();
							layer.closeAll();
                        } else {
                            layer.alert(o.message, {icon: 2, offset: '50px'});
                            return false;
                        }

                    }
                })

            })
        }
    })
};
//删除展示图
_We_G.del_pic = function (obj, tagId) {
    $(obj).parent().remove();
    $(".theme-template-pic").val('');
};
//弹出添加BI层
_We_G.chooseBI = function() {
    if( $("#mainFrame").css('display') == 'none' ){
        _We_G.show_choose_bi();
    } else {
        _We_G.closeBI(1);//区别自动关闭、手动关闭  1 手动关闭
    }
};
//展示选择BI层
_We_G.show_choose_bi = function(){
    $('#head_chooseBI').text('关闭BI');
    var win_size = BI.browser.findDimensions();
    $(".main").fadeOut(500);
    $("#mainFrame").fadeIn(500).css('height',win_size.winHeight+'px').attr('src','/webi/design/edit/choose?callback=_We_G.selectBI');
};
//隐藏选择BI层
_We_G.closeBI = function(flg){
    $('#head_chooseBI').text('添加BI');
    $(".main").fadeIn(500);
    $("#mainFrame").fadeOut(500).attr('src','');

    if(typeof flg != 'undefined' && flg == 1){
        //由于页面BI组件宽高所有都为0，重新加载页面数据
        $('#'+ WeBI.p_id).empty();//清空页面
        WeBI.op.a(WeBI.p_id, WeBI.webi_dt, $('#bi_content_html').html(),1);
    }

};
//选择BI的回调函数
_We_G.selectBI = function(chart_id) {
    if ( !chart_id ) {
        layer.msg('请选择BI模板', {icon: 2, offset: '70px', time: 1500});
        return false;
    }
    if( _We_M.replace_uid == '' ){ //新增
        _We_M.addBI(chart_id);
    } else { //更改
        _We_M.exec_replace_bi(chart_id);
    }
};
//根据当前BI模块的数量计算出编辑体的高度
_We_G.calContentHeight = function() {

    if( JSON.stringify(WeBI.bi_content) != '{}' && JSON.stringify(WeBI.bi_content) != '[]' && WeBI.bi_content != '' ){

        var bottomObj = WeBI.op.sort_module[WeBI.op.sort_module.length-1];

        var win_size = BI.browser.findDimensions();

        //计算出新的高度
        var new_height = parseInt(bottomObj.top) + parseInt(bottomObj.height);

        //比较高度：页面浏览器高度 - 头部标题的高度
        var compare_height = parseInt(win_size.winHeight) - parseInt( document.getElementById('g_top_title').offsetHeight );

        //如果计算出的高度小于页面实际高度，则取页面实际高度
        if( new_height <= compare_height ){
            new_height = compare_height;
        }

        document.getElementById(WeBI.p_id).style.height = new_height + 'px';

        if( new_height > compare_height){
            WeBI.op.reCount("",_We_G.saveModuleFun);
        }
    }
};
//重新遍历保存module结构
_We_G.saveModuleFun = function(){
    $.ajax({
        type:'post',
        url: _V.global_url[1],
        data:{
            master_module: JSON.stringify(WeBI.webi_dt.module)
        },
     });
 };
//绑定事件初始化
_We_G.eventBindInit = function(){

    //单选复选框
    $('.square-radio').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });

};
//进入页面初始化操作
_We_G.init = function() {

    _We_G.eventBindInit();//绑定事件

    _We_G.calContentHeight();

    //字体初始化
    if( JSON.stringify(_We_G.font_amily) == '{}' || JSON.stringify(_We_G.font_amily) != '[]' || _We_G.font_amily != '' ){

        $.ajax({
            type:'get',
            url: _V.global_url[2],
            success:function(obj){

                if(obj.code == 200){

                    var total_option_html = '';
                    $.each(obj.data, function (k,v) {
                        var option_html = $('#side_option_html').html().replace(/[\$]font_family/g,v.val);
                        total_option_html += option_html;

                        _We_G.font_amily[k] = v.val;
                    });
                    //贴上维护的字体
                    $(".font-family").append(total_option_html);
                }
            }
        });
    }

    _We_DRAG.drag();//绑定拖拽事件
};
//右侧属性展示
_We_G.right_attribute = function () {
    _We_G.save_type = 1;

    _We_ATTR.clearUp();
    _We_DTS.clearUp();
    _We_CHART.hideModule();//隐藏chart属性设置
    _We_DTS.select.hide();//隐藏数据集设置

    $('.theme_attribute').show();
    $('#parents_general .sole-input').css('display','none');
    $('.general_attribute').nextAll().css('display','none');//常规设置后的所有设置隐藏
    $('.general-title').css('display','block');//标题设置显示
    $('.general-font-way').css('display','none');//字体设置隐藏

    _We_ATTR.showAttr('');//贴上字段信息

    $("div[id*='grid_']").removeClass('grid-selected');//取消页面编辑模块的选中效果
    $('.right-set-show').click();

};