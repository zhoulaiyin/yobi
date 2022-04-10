    /**
     *  属性操作对象
     *
     *  操作函数集合
     */

    var _We_ATTR = {};
        _We_ATTR.pop = {};//表格宽度弹层控制
        _We_ATTR.pattern ={};//重置页面样式
        _We_ATTR.rim_obj = {};//当前修改边框对象归属
        _We_ATTR.rim_orient = {};//当前点击边框线方位
        _We_ATTR.rim = {//边框设置临时对象
            'top' : '',
            'right' : '',
            'bottom' :'',
            'left' :''
        };
        _We_ATTR.series = {
            'val_format': 1,
            'unit_format': 0,
            'decimal_num': 0
        };//序列初始化

    _We_ATTR.theme_url = '';
    var limit = 6;
    var page =1;
    var total =0;

    layui.use(['form','element','layer'], function () {
        var layer = layui.layer;
        var form = layui.form;

        //图例\标题  switch开关  0关闭 1开启
        form.on('switch(filter)', function(data){
            var property = $(this).attr('name');
            _We_ATTR.switch(property);
        });

    });

    layui.use(['layedit','upload','laydate','element','layer'], function(){

        layui.layedit.set({
            uploadImage: {
                url: '/upload?action=webi', //接口url
                type: 'post' //默认post
            }
        });

        //年月日选择器
        layui.laydate.render({
            elem: '#date_start',
            done:function(){
                _We_DTS.where.editChange();
            }
        });

        //年月日选择器
        layui.laydate.render({
            elem: '#date_end',
            done:function(){
                _We_DTS.where.editChange();
            }
        });

        var $ = layui.jquery;

        //上传背景图片
        layui.upload.render({
            elem: '#b_img_upload',
            url: '/upload?action=webi',
            done: function(res){
                $('.b-img-add').hide();
                $('#b_img_upload').parent().css('padding-top', '0');
                $('#b_img').attr('src', res.data.url)
                    .css('width', '70px')
                    .css('height', '70px');
                $('.b_img').show();

                if ( _We_G.save_type == 2 ) {
                    if( WeBI.webi_dt['module'][_We_M.uid]['bi_json'].type == 'bi_text' ){
                        var dom = WeBI.content.o[_We_M.uid].boxpic;
                    } else {
                        var dom = document.getElementById('chart_'+_We_M.uid);
                    }
                    WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['general']['backgroundImage'] = res.data.url;
                } else {
                    var dom = document.getElementById(WeBI.p_id);
                    WeBI.webi_dt['master']['general'].backgroundImage = res.data.url;
                }

                dom.style.backgroundImage = 'url('+res.data.url+')';
                dom.style.backgroundRepeat = 'no-repeat';
                dom.style.backgroundSize = '100% 100%';

                _We_ATTR.save();
            }
        });

        //上传边框图片
        layui.upload.render({
            elem: '#border_upload',
            url: '/upload?action=webi',
            done: function(res){
                $('.border-add').hide();
                $('#border_upload').parent().css('padding-top', '0');
                $('#border_img').attr('src', res.data.url)
                    .css('width', '70px')
                    .css('height', '70px');
                $('.border_img').show();

                if ( _We_G.save_type == 2 ) {
                    var dom_id = 'grid_'+_We_M.uid;
                    WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['general']['border_image_source'] = res.data.url;
                } else {
                    var dom_id = WeBI.p_id;
                    WeBI.webi_dt['master']['general'].border_image_source = res.data.url;
                    WeBI.webi_dt['master']['general'].border = 5;
                    WeBI.webi_dt['master']['general'].border_image_slice = 15;
                    WeBI.webi_dt['master']['general'].border_image_repeat = 'repeat';
                }

                var dom = document.getElementById(dom_id);
                dom.style.borderImageSource = 'url('+res.data.url+')';
                dom.style.borderImageWidth = 5;
                dom.style.borderImageSlice = 15;
                dom.style.borderImageRepeat = 'repeat';
                dom.style.borderStyle = 'solid';

                _We_ATTR.save();
            }
        });

        $('.layui-upload-file').hide();

    });

function attrKeyUp(ev){

    var event = ev || event;
    if( event.target.id == 'chart-title' ){
        if( _We_M.uid != '' ){
            document.getElementById('touch_'+_We_M.uid).innerText = event.target.value;
        }
    }

    //序列  select框点击
    if( event.target.id == 'series-col' ){
        return false;
    }

    var data_attribute = event.target.getAttribute('data-attribute').split('.');

    if( _We_G.save_type !=1 ){//保存BI属性设置

        switch (data_attribute[0]){

            case 'series':
                //序列
                var obj = document.getElementById("series-col"); //定位id
                var index = obj.selectedIndex; // 选中索引
                var series_fields = obj.options[index].getAttribute('data-keys'); //选中的字段

                if ( BI.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series']) ){//不存在 series
                    WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series'] = {};
                }

                if ( BI.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series'][series_fields]) ){//不存在此列
                    var series_mod = _We_ATTR.series;
                    WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series'][series_fields] = series_mod;
                }

                WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series'][series_fields]['decimal_num'] = event.target.value;
                break;

            case 'text':
                var attribute_style = event.target.getAttribute('data-style');
                WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]][attribute_style] = event.target.value;
                break;

            default:

                WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]] = event.target.value;

                if( data_attribute[0] == 'general'){//常规
                    switch (data_attribute[1]) {
                        case 'border_image_repeat':
                            $('#bi_border_image_repeat').val(event.target.value);
                            break;
                    }
                }

                if( data_attribute[0] == 'title' ){
                    var dom_id = 'move_'+ _We_M.uid +'  .chart-touch';
                    switch (data_attribute[1]) {
                        case 'value':
                            $('#'+dom_id ).text(event.target.value);
                            break;
                        case 'link':
                            if((event.target.value).substring(7) != 'http://'){
                                WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]] = 'http://' + event.target.value;
                            }
                            break;
                    }
                }

        }

    }else{
        WeBI.webi_dt['master']['general'][data_attribute[1]] = event.target.value;
    }
    _We_ATTR.save();
}

BI.event.bind(document.getElementById('g_attr_setting'),'input',attrKeyUp); //输入框变更事件

$(document).on('click','#attr_design_icon',function () { //属性设置模块隐藏
    _We_ATTR.hideModule();
}).on('click','#right-set-show',function () { //属性设置模块显示
    _We_ATTR.showModule();
}).on('click','.layer-module-title',function () { //判断属性设置模块显示与隐藏
    _We_ATTR.setModuleType($(this).parent().attr('data-genre'));
}).on('click','#g_attr_main .layer-module-title',function () { //点击单一属性设置菜单,贴上数据
    var type = $(this).parent().attr('data-genre');

    _We_ATTR.clearUp();//清空属性设置
    if(_We_G.save_type == 2){//BI属性设置
        var btn_switch = WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][type];

        if (  typeof  (btn_switch) != 'undefined' && typeof  (btn_switch['switch']) != 'undefined'  &&  btn_switch['switch']  == 0 )
        {//关闭 移除展开
            $(this).siblings('.layui-colla-content').removeClass('layui-show');
            $(this).children('.layui-colla-icon').html('&#xe602;')//向右箭头
         }
    }

    if($(this).siblings().hasClass('layui-show')){

        if(_We_G.save_type == 1){//全局属性
            $('.general-title').css('display','block');//显示标题设置
            $('.general-font-way').css('display','none');//去除字体设置
        }
        if(_We_G.save_type == 2){//BI属性

            if( type == 'general'){//常规设置
                $('.general-title').css('display','none');//去除标题设置
                $('.general-font-way').css('display','block');//显示字体设置
            }
        }
        if( type != 'theme'){
            _We_ATTR.showAttr(type);
        }
    }

}).on('click','.upload-box .upload',function () { //上传图片框选中效果
    $(this).addClass('checked').siblings().removeClass('checked');
}).on('click','.b_img .img-del i',function () { //删除背景图片
    $('.b_img').hide();
    $('.b-img-add').show();
    $('#b_img_upload').parent().css('padding-top', '13px');
    $('#b_img').attr('src', '');

    if ( _We_G.save_type == 2 ) {
        WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['general']['backgroundImage'] = "";
    } else {
        WeBI.webi_dt['master']['general'].backgroundImage = "";
    }

    _We_ATTR.save();

}).on('click','.border_img .img-del i',function () {//删除边框图片
   
    $('.border_img').hide();
    $('.border-add').show();
    $('#border_upload').parent().css('padding-top', '13px');
    $('#border_img').attr('src', '');

    if ( _We_G.save_type == 2 ) {
        WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['general']['border_image_source'] = "";
    } else {
        WeBI.webi_dt['master']['general'].border_image_source = "";
    }
    _We_ATTR.save();

}).on('click','.align-legend',function () { //图例 水平对齐
    $(this).addClass('select-div').siblings().removeClass('select-div');
    var position = $(this).attr('data-code');
    if(_We_M.uid != ""){
        WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['legend']['position'] = position;
        _We_ATTR.save();
    }

    $(this).addClass('select-div');

    _We_M.show_bi(_We_M.uid);
}).on('click','.title-target-way',function () { //标题 打开方式
    $(this).addClass('select-div').siblings().removeClass('select-div');
    var target = $(this).attr('data-code');
    if(_We_M.uid != ""){
        WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['title']['target'] = target;
        _We_ATTR.save();
    }

    $(this).addClass('select-div');

}).on('click','#title .title-legend',function () { //标题 标题样式:水平对齐
    $(this).addClass('select-div').siblings().removeClass('select-div');
    var legend = $(this).attr('data-code');

    if(_We_M.uid != ""){
        WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['title']['legend'] = legend;
        _We_ATTR.save();
    }

    $(this).addClass('select-div');

}).on('click','.row-wid-button',function (e) { //打开表格宽度设置pop
    _We_ATTR.pop.showWidth(e);
    _We_ATTR.pop.hideLegend();//隐藏表格列对齐设置pop
}).on('click','.row-info-close',function () { //关闭表格宽度设置pop
    _We_ATTR.pop.hideWidth();
}).on('click','.row-legend-button',function (e) { //打开表格列对齐设置pop
    _We_ATTR.pop.hideWidth();//隐藏表格宽度设置pop
    _We_ATTR.pop.showLegend(e);
}).on('click','.row-legend-close',function () { //关闭表格列对齐设置pop
    _We_ATTR.pop.hideLegend();
}).on('click','.table-border-style',function (e){//展开表格边框线选项弹窗
        _We_ATTR.showSetBorder(e);
}).on('click','.title-border-button',function (e){//打开边框设置弹层
    _We_ATTR.rim_obj = $(this).data('type');//当前打开的边框设置属性
    _We_ATTR.pop.showBorder(e);
}).on('click','.border-style-close',function (){//关闭边框设置弹层
    _We_ATTR.pop.hideBorder();
}) .on('click','.round-boder',function (e){//点击边框弹窗中的边框线
    $(this).addClass('border-select').parent().siblings().children().removeClass('border-select');
    _We_ATTR.pop.line(e);
}).on('click','.second-border-style',function (e){//打开边框线设置弹层
    _We_ATTR.pop.showBorderStyle(e);
}).on('click','.set-border .layui-unselect',function (){//边框线显示选择
    if( JSON.stringify(_We_ATTR.rim_orient) != '{}' ){
        var rim_orient = _We_ATTR.rim[_We_ATTR.rim_orient].split(',');

        if($(this).hasClass('layui-form-checked')){
            var border_width = typeof rim_orient[2] != 'undefined' && rim_orient[2] != '0' ? rim_orient[2]:'2px'
            switch ( _We_ATTR.rim_orient){
                case 'top':
                    $(' .border-' +  _We_ATTR.rim_orient ).css({
                        "border-bottom-style": typeof rim_orient[1]  != 'undefined' ? rim_orient[1]:'solid',
                        "border-bottom-color": rim_orient[3],
                        "border-bottom-width": border_width
                    });
                    break;
                case 'left':
                    $('.border-box .border-' +  _We_ATTR.rim_orient ).css({
                        "border-right-style": typeof rim_orient[1] != 'undefined' ? rim_orient[1]:'solid',
                        "border-right-color": rim_orient[3],
                        "border-right-width": border_width
                    });
                    break;
                case 'right':
                    $('.border-box .border-' +  _We_ATTR.rim_orient ).css({
                        "border-left-style": typeof rim_orient[1] != 'undefined' ? rim_orient[1]:'solid',
                        "border-left-color": rim_orient[3],
                        "border-left-width": border_width
                    });
                    break;
                case 'bottom':
                    $('.border-box .border-' +  _We_ATTR.rim_orient ).css({
                        "border-top-style": typeof rim_orient[1] != 'undefined' ? rim_orient[1] : 'solid',
                        "border-top-color": rim_orient[3],
                        "border-top-width": border_width
                    });
                    break;
            }
            _We_ATTR.rim[_We_ATTR.rim_orient] = '1,'+rim_orient[1]+','+border_width+','+rim_orient[3];
        }else{
            $('.border-' +  _We_ATTR.rim_orient ).css(
                'border-width',0
            );
            _We_ATTR.rim[_We_ATTR.rim_orient] = '0,'+rim_orient[1]+','+rim_orient[2]+','+rim_orient[3];
        }
    }else{
        layer.msg('请选择您要操作的边框', {icon: 2, offset: '70px', time: 1500});
    }
}).on('click','.theme-button',function () { //打开模板页面
    _We_ATTR.showTheme();
}).on('click','.template-box',function () { //选择模板
    _We_ATTR.chooseTheme($(this).data('id'));
}).on('change','#search-option',function () { //关键词模板搜索
    _We_ATTR.themeKeyDown($(this).val());
}).on('change','#series-col',function () { //序列 更换列

    _We_ATTR.clearUp.series();

    var obj = document.getElementById("series-col"); //定位id
    var index = obj.selectedIndex; // 选中索引
    var series_fields = obj.options[index].getAttribute('data-keys'); //选中的列

    _We_ATTR.change_col( series_fields );

 }).on('click','#parents_series .val-format',function () { //序列 值格式

    $(this).addClass('select-div').siblings().removeClass('select-div');
    var attributes = $(this).attr('data-code');

    _We_ATTR.getSeries(attributes,'val_format');

}).on('click','#parents_series .unit-format',function () { //序列 单位格式

    $(this).addClass('select-div').siblings().removeClass('select-div');
    var attributes = $(this).attr('data-code');

    _We_ATTR.getSeries(attributes,'unit_format');

});

//修改颜色
_We_ATTR.color_change = function(obj) {
    var data_attribute = obj.getAttribute('data-attribute').split('.');
    var dom_id = obj.getAttribute('id');
    var color = $('#'+dom_id).val();

    if ( _We_G.save_type == 2 ) {

        switch( data_attribute[0] ){

            case 'text':
                    var attribute_style = obj.getAttribute('data-style');
                    WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]][attribute_style] = color;
                break;

            default:
                WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]] = color;
        }

    } else {
        WeBI.webi_dt['master']['general'].backgroundColor = color;
    }

    _We_ATTR.save();
};
//清空颜色
_We_ATTR.clear_color = function(obj) {
    var data_attribute = obj.getAttribute('data-attribute').split('.');

    if ( _We_G.save_type == 2 ) {

        switch( data_attribute[0] ){

            case 'text':
                var attribute_style = obj.getAttribute('data-style');
                WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]][attribute_style] = '';
                break;

            default:
                WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][data_attribute[0]][data_attribute[1]] = '';
        }

    } else {
        WeBI.webi_dt['master']['general'].backgroundColor = "";
    }

    _We_ATTR.save();
};

//显示模块
_We_ATTR.showModule = function() {
    $('#right-set-show').css('opacity','0');

    setTimeout(function(){
        _We_M.BISelfSuite();
    }, 500);

    $('#g_attr_main').addClass('slient');
    $('.main-cont').addClass('pdright');

};
//隐藏模块
_We_ATTR.hideModule = function() {
    $('#right-set-show').css('opacity','0');

    setTimeout(function(){
        _We_M.BISelfSuite();
    }, 500);

    $('#g_attr_main').removeClass('slient');
    $('.main-cont').removeClass('pdright');

};

//展示属性
_We_ATTR.showAttr = function(type) {

    var attribute_json = {};
    if ( _We_G.save_type == 2 ) {

        attribute_json = WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][type];

        switch(type){

            case 'series':
                _We_ATTR.seriesAttr();
                return false;
                break;

            case 'text':
                _We_ATTR.textAttr();
                return false;
                break;
        }

    }else{
        attribute_json = WeBI.webi_dt['master']['general'];
    }

    if(type){
        var elems_obj = document.querySelectorAll("#"+ type +" .attr-child");

        //循环处理元素赋值
        for( var i=0; i<elems_obj.length; i++ ) {
            var elemt_obj = elems_obj[i];
            var attribute = elemt_obj.getAttribute('data-attribute').split('.');

            if( typeof attribute_json[attribute[1]] == 'undefined' ){
                continue;
            }
            var elem_value = attribute_json[attribute[1]];
            switch( elemt_obj.nodeName ){
                case 'INPUT':
                case 'SELECT':

                    elemt_obj.value = elem_value;

                    if( ['color','backgroundColor','even_color','borderColor'].indexOf( attribute[1] ) != -1 ){
                        elemt_obj.style.backgroundColor = elem_value;
                    }

                    switch( elemt_obj.type ){

                        case 'checkbox':
                            var cur_target_val = elemt_obj.getAttribute('data-code');
                            if( cur_target_val == elem_value ){
                                elemt_obj.setAttribute('checked','false');
                            }
                            break;
                    }
                    break;
                case 'IMG':

                    elemt_obj.setAttribute('src',elem_value);
                    break;
                case 'DIV':

                    var cur_target_val = elemt_obj.getAttribute('data-code');
                    if( cur_target_val == elem_value ){
                        BI.toggleClass(elemt_obj,'select-div',true);
                    }
                    break;

            }
        }

        if( typeof  attribute_json != 'undefined'
            &&
            typeof  attribute_json['backgroundImage'] != 'undefined'
            &&
            attribute_json['backgroundImage']
        ){
            $('.b-img-add').hide();
            $('#b_img_upload').parent().css('padding-top', '0');
            $('#b_img').css('width', '70px');
            $('#b_img').css('height', '70px');
            $('.b_img').show();
        }
        if( typeof  attribute_json != 'undefined'
            &&
            typeof  attribute_json['border_image_source'] != 'undefined'
            &&
            attribute_json['border_image_source']
        ){
            $('.border-add').hide();
            $('#border_upload').parent().css('padding-top', '0');
            $('#border_img').css('width', '70px');
            $('#border_img').css('height', '70px');
            $('.border_img').show();
        }

    }

};
//属性数据保存
_We_ATTR.save = function() {

    //组装保存数据结构
    var save_dt = {};
    save_dt.bi_id = _We_G.bi_id;
    save_dt.uid = _We_M.uid;
    save_dt.data = [];

    if( _We_G.save_type == 1 ){ //保存主表属性设置
        var save_url = _V.attr_url[1];
        save_dt.data = WeBI.webi_dt['master'];
    } else { //保存BI属性设置
        var save_url = _V.attr_url[2];
        save_dt.data = WeBI.webi_dt['module'][_We_M.uid]['attribute_json'];
    }

    E.ajax({
        type: 'post',
        url: save_url,
        dataType:'json',
        data:save_dt,
        success:function (obj) {
            if ( obj.code != 200 ) {
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }
            if(_We_G.save_type == 1){
                WeBI.op.a_a();
            }else{
                _We_M.show_bi(_We_M.uid);
            }

        }
    })

};
//文本属性信息展示
_We_ATTR.textAttr = function(){

        var attribute_json = WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['text'];
        var elems_obj = document.querySelectorAll("#text .attr-child");

        //循环处理元素赋值
        for( var i=0; i<elems_obj.length; i++ ) {
            var elemt_obj = elems_obj[i];
            var attribute = elemt_obj.getAttribute('data-attribute').split('.');
            var attribute_style = elemt_obj.getAttribute('data-style');

            if( typeof attribute_json[attribute[1]] == 'undefined' ){
                continue;
            }

            var elem_value = attribute_json[attribute[1]][attribute_style];
            switch( elemt_obj.nodeName ){

                case 'INPUT':
                case 'SELECT':
                    elemt_obj.value = elem_value;

                    if( ['color'].indexOf( attribute_style ) != -1 ) {
                        elemt_obj.style.backgroundColor = elem_value;
                    }
                    break;

            }
        }
};


//序列属性信息展示
_We_ATTR.seriesAttr = function(seriesFields){
    var series_fields = seriesFields || 0;
    var series = WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series'];

    //贴上所有设置字段
    var row = WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'];
    var column = WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'];
    var sum = WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'];
    var all_fields = [];

    if ( !$.isEmptyObject(column) ) {
        var column_data = column.split(",");
        for(var j = 0,len = column_data.length; j < len; j++) {
            all_fields.push( column_data[j] );
        }
    }
    if( !$.isEmptyObject(sum) ){
        var sum_data = sum.split(",");
        for(var j = 0,len = sum_data.length; j < len; j++) {
            all_fields.push( sum_data[j] );
        }
    }

    if ( JSON.stringify(all_fields) != '[]' ) {
        _We_ATTR.fieldsCol( all_fields );
    }

    //不存在数据设置
    if( BI.isEmptyObject(series) ){
        return false;
    }

    //获取选中字段
    if( series_fields ) {

        series_fields = (( (series_fields.split(':'))[0] ).split('.'))[1];
        //更改选中
        $("#series-col").find("option[value="+ series_fields +"]").attr("selected",true);

    } else {

        var obj = document.getElementById("series-col"); //定位id
        var index = obj.selectedIndex; // 选中索引
        series_fields = obj.options[index].getAttribute('data-field'); //选中的列字段

    }

    //遍历获取当前选中字段序列属性
    var series_json = '';
    for (var i in series) {

        var data = i.split(':');

        if( (data[0].split('.'))[1] == series_fields ){
            series_json = series[i];
            break;
        }
    }
    if( !series_json ){
        return false;
    }

    var elems_obj = document.querySelectorAll("#parents_series .attr-child");
    //循环处理元素赋值
    for( var i=0; i<elems_obj.length; i++ ) {
        var elemt_obj = elems_obj[i];
        var attribute = elemt_obj.getAttribute('data-attribute').split('.');

        var elem_value = series_json[attribute[1]];

        if (typeof series_json == 'undefined' || typeof series_json[attribute[1]] == 'undefined') {
            continue;
        }

        switch( elemt_obj.nodeName ){
            case 'INPUT':
            case 'SELECT':
                elemt_obj.value = elem_value;
                break;

            case 'DIV':
                var cur_target_val = elemt_obj.getAttribute('data-code');
                if( cur_target_val == elem_value ){
                    BI.toggleClass(elemt_obj,'select-div',true);
                }
                break;
        }
    }

};
//数据列信息
_We_ATTR.fieldsCol = function (data) {

    $("#parents_series #series-col").empty();

    var option_html = '';
    $.each( data ,function (k,v) {

        var v_a = v.split(":");
        var table = v_a[0].split(".")[0];
        var field = v_a[0].split(".")[1];

        option_html +=
            '<option value="'+ field +'" data-table="'+ table +'" data-remarks="'+ v_a[1] +'" data-field="'+ field +'" data-keys="'+ v +'"> '+
                  v_a[1] +
            '</option>';
    });

    $("#parents_series #series-col").append(option_html);
};
//更改列
_We_ATTR.change_col = function(series_fields){

    var webi_dt = WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series'];

    if( BI.isEmptyObject( webi_dt ) || BI.isEmptyObject( webi_dt[series_fields] ) ) {
        _We_ATTR.clearUp.series();
        return false;
    }
    _We_ATTR.seriesAttr(series_fields);
};
//序列属性修改
_We_ATTR.getSeries  =function(val,attributes){

    var obj = document.getElementById("series-col"); //定位id
    var index = obj.selectedIndex; // 选中索引
    var series_fields = obj.options[index].getAttribute('data-keys'); //选中的字段

    if ( BI.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['series']) ){//不存在 series
        WeBI.webi_dt.module[_We_M.uid]['attribute_json']['series'] = {};
    }

    if ( BI.isEmptyObject(WeBI.webi_dt.module[_We_M.uid]['attribute_json']['series'][series_fields]) ){//不存在此列
        WeBI.webi_dt.module[_We_M.uid]['attribute_json']['series'][series_fields] = F.deepClone(_We_ATTR.series);
    }

    if(_We_M.uid != ""){
        WeBI.webi_dt.module[_We_M.uid]['attribute_json']['series'][series_fields][attributes] = val;
        _We_ATTR.save();
    }
};

//属性重置
_We_ATTR.clearUp = function() {
    _We_ATTR.clearUp.general();
    _We_ATTR.clearUp.table();
    _We_ATTR.clearUp.title();
    _We_ATTR.clearUp.text();
    _We_ATTR.clearUp.series();
};
//常规属性重置
_We_ATTR.clearUp.general = function(){
    var lis = document.getElementById('parents_general').querySelectorAll('.attr-child');

    for (var i = 0; i < lis.length; i++) {

        var ele_type = lis[i];

        switch (ele_type.nodeName) {

            case 'INPUT':
                ele_type.value = '';
                ele_type.style.backgroundColor = '#fff';
                $('#parents_general input').val("");
                break;

            case 'IMG':
                ele_type.setAttribute('src','');
                break;

            case 'DIV':
                break;

            case 'SELECT':
                $('#parents_general select').val('repeat');
                break;
        }
    }

    $('#general_backgroundColor').val('#FFFFFF');
    $('#general_backgroundColor').css('background-color','#FFFFFF');
    $('.b-img-add').show();
    $('#b_img_upload').parent().css('padding-top', '10px');
    $('.border-add').show();
    $('#border_upload').parent().css('padding-top', '10px');

};
//表格属性重置
_We_ATTR.clearUp.table = function(){
    if(!document.getElementById('parents_table')){
        return false;
    }
    var lis = document.getElementById('parents_table').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];

        switch (ele_type.nodeName) {

            case 'INPUT':
                ele_type.value = '';
                ele_type.style.backgroundColor = '#fff';
                break;

            case 'SELECT':
                ele_type.value = '微软雅黑';
                break;

            case 'IMG':
                ele_type.setAttribute('src','');
                break;

            case 'DIV':
                ele_type.removeAttribute('select-div');
                break;

        }
    }
};
//标题属性重置
_We_ATTR.clearUp.title = function(){
    if(!document.getElementById('parents_title')){
        return false;
    }
    var lis = document.getElementById('parents_title').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];

        switch (ele_type.nodeName) {

            case 'INPUT':
                ele_type.value = '';
                ele_type.style.backgroundColor = '#fff';
                break;

            case 'DIV':
                ele_type.removeAttribute('select-div');
                break;

            case 'SELECT':
                break;

            case 'IMG':
                ele_type.setAttribute('src','');
                break;
        }
    }

    $('.title .font-family').val('微软雅黑');
    $('.title_fontWeight').val('normal');
    $('.title_fontStyle').val('normal');
};
//文本属性重置
_We_ATTR.clearUp.text = function(){
    var lis = document.getElementById('parents_text').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');//当前属性
        var attribute_style = ele_type.getAttribute('data-style');//当前属性对象

        switch (ele_type.nodeName) {

            case 'INPUT':

                ele_type.value = '';
                if( ['color'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }
                break;

            case 'SELECT':

                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//序列属性重置
_We_ATTR.clearUp.series = function(){
    if(!document.getElementById('parents_series')){
        return false;
    }

    var lis = document.getElementById('parents_series').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];

        switch (ele_type.nodeName) {

            case 'INPUT':
            case 'SELECT':
                ele_type.value = '';
                break;

            case 'DIV':
                BI.toggleClass(ele_type,'select-div',false);
                break;

            case 'IMG':
                ele_type.setAttribute('src','');
                break;
        }
    }
};

//打开表格宽度设置弹窗
_We_ATTR.pop.showWidth = function(e){
    if( $('.width-html').css('display') != 'block') {
        $('#row_info_pop').fadeIn(500).css('top', 300);
        $('.row-field').empty();

        //按钮绑定保存事件
        $('#row_info_pop .row-inf-btn button').attr("onclick", '_We_ATTR.pop.widthSave();');

        var td_width = WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['table']['td_width'];

        //遍历保存的宽度设置，并赋值到弹窗  汇总、行、列
        if (JSON.stringify(td_width) != '{}' && JSON.stringify(td_width) != '[]' && td_width != "") {

            //生成li
            for (var i in td_width) {
                var data = i.split(':');

                var li_list_html = $('#attr_width_li_html').html();
                li_list_html = li_list_html.replace(/[\$]widthType/g, i);
                li_list_html = li_list_html.replace(/[\$]width_name/g, data[1]);
                li_list_html = li_list_html.replace(/[\$]widthPercent/g, td_width[i]);

                $('#attr-row-field').append(li_list_html);
            }

        } else {

            //遍历汇总、行、列，并赋值到弹窗 （列宽取平均值）
            if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'] != "") {
                var sum = "";
                if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'].indexOf(',') != -1) {
                    var sum_array = WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'].split(',');
                    $.each(sum_array, function (k, v) {
                        sum[k] = v.split(':');
                    });
                } else {
                    sum[0] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'].split(':');
                }

                var sum_width = (1 / sum.length) * 100;
                $.each(sum, function (k, v) {

                    var sum_li_list_html = $('#attr_width_li_html').html();
                    sum_li_list_html = sum_li_list_html.replace(/[\$]widthType/g, v[0] + ':' + v[1]);
                    sum_li_list_html = sum_li_list_html.replace(/[\$]width_name/g, v[1]);
                    sum_li_list_html = sum_li_list_html.replace(/[\$]widthPercent/g, sum_width);
                    $('#attr-row-field').append(sum_li_list_html);
                });
            }

            if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'] != "") {
                var row = [];
                if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'].indexOf(',') != -1) {
                    var row_array = WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'].split(',');
                    $.each(row_array, function (k, v) {
                        row[k] = v.split(':');
                    });
                } else {
                    row[0] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'].split(':');
                }
                var row_width = (1 / row.length) * 100;
                $.each(row, function (k, v) {
                    var row_li_list_html = $('#attr_width_li_html').html();
                    row_li_list_html = row_li_list_html.replace(/[\$]widthType/g, v[0] + ':' + v[1]);
                    row_li_list_html = row_li_list_html.replace(/[\$]width_name/g, v[1]);
                    row_li_list_html = row_li_list_html.replace(/[\$]widthPercent/g, row_width);
                    $('#attr-row-field').append(row_li_list_html);
                });
            }

            if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'] != "") {
                var column = [];
                if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].indexOf(',') != -1) {
                    var column_array = WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].split(',');

                    $.each(column_array, function (k, v) {
                        column[k] = v.split(':');
                    });
                } else {
                    column[0] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].split(':');
                }

                var column_width = (1 / column.length) * 100;
                $.each(column, function (k, v) {
                    var column_li_list_html = $('#attr_width_li_html').html();
                    column_li_list_html = column_li_list_html.replace(/[\$]widthType/g, v[0] + ':' + v[1]);
                    column_li_list_html = column_li_list_html.replace(/[\$]width_name/g, v[1]);
                    column_li_list_html = column_li_list_html.replace(/[\$]widthPercent/g, column_width);
                    $('#attr-row-field').append(column_li_list_html);
                });
            }
        }
    }else{
        _We_ATTR.pop.hideWidth();
    }
};
//隐藏表格宽度设置弹窗
_We_ATTR.pop.hideWidth = function(){
    $('#row_info_pop').fadeOut(500);
};
//打开表格列对齐设置弹窗
_We_ATTR.pop.showLegend = function(e){

    if( $('.col-html').css('display') != 'block') {
        $('#col_info_pop').fadeIn(500).css('top', 300);
        $('#legend-region').empty();

        //按钮绑定保存事件
        $('#col_info_pop button').attr("onclick", '_We_ATTR.pop.legendSave();');

        var legend = WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['table']['legend'];

        //遍历保存的宽度设置，并赋值到弹窗  汇总、行、列
        if (JSON.stringify(legend) != '{}' && JSON.stringify(legend) != '[]' && legend != "") {

            var j = 0;
            //生成select div
            for (var i in legend) {
                var data = i.split(':');

                var li_list_html = $('#attr_legend_div_html').html();
                li_list_html = li_list_html.replace(/[\$]legendType/g, i);
                li_list_html = li_list_html.replace(/[\$]legend_name/g, data[1]);
                li_list_html = li_list_html.replace(/[\$]num/g, j);

                $('#legend-region').append(li_list_html);
                $('#legend-region .length-' + j).val(legend[i]);
                ++j;
            }

        } else {

            //遍历汇总、行、列，并赋值到弹窗 （列宽取平均值）
            if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'] != "") {
                var sum = "";
                if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'].indexOf(',') != -1) {
                    var sum_array = WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'].split(',');
                    $.each(sum_array, function (k, v) {
                        sum[k] = v.split(':');
                    });
                } else {
                    sum[0] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'].split(':');
                }

                $.each(sum, function (k, v) {
                    var sum_li_list_html = $('#attr_legend_div_html').html();
                    sum_li_list_html = sum_li_list_html.replace(/[\$]legendType/g, v[0] + ':' + v[1]);
                    sum_li_list_html = sum_li_list_html.replace(/[\$]legend_name/g, v[1]);

                    $('#legend-region').append(sum_li_list_html);
                });
            }

            if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'] != "") {
                var row = [];
                if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'].indexOf(',') != -1) {
                    var row_array = WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'].split(',');
                    $.each(row_array, function (k, v) {
                        row[k] = v.split(':');
                    });
                } else {
                    row[0] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'].split(':');
                }

                $.each(row, function (k, v) {
                    var row_li_list_html = $('#attr_legend_div_html').html();
                    row_li_list_html = row_li_list_html.replace(/[\$]legendType/g, v[0] + ':' + v[1]);
                    row_li_list_html = row_li_list_html.replace(/[\$]legend_name/g, v[1]);

                    $('#legend-region').append(row_li_list_html);
                });

            }

            if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'] != "") {
                var column = [];
                if (WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].indexOf(',') != -1) {
                    var column_array = WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].split(',');

                    $.each(column_array, function (k, v) {
                        column[k] = v.split(':');
                    });
                } else {
                    column[0] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].split(':');
                }

                $.each(column, function (k, v) {
                    var column_li_list_html = $('#attr_legend_div_html').html();
                    column_li_list_html = column_li_list_html.replace(/[\$]legendType/g, v[0] + ':' + v[1]);
                    column_li_list_html = column_li_list_html.replace(/[\$]legend_name/g, v[1]);

                    $('#legend-region').append(column_li_list_html);
                });
            }
        }
    }else{
        _We_ATTR.pop.hideLegend();
    }
};
//隐藏表格列文本对齐设置
_We_ATTR.pop.hideLegend = function(){
    $('#col_info_pop').fadeOut(500);
};
//保存表格列宽度设置
_We_ATTR.pop.widthSave = function(){

    _We_ATTR.pop.hideWidth();//隐藏表格宽度设置弹窗

    var temp_params_str = {};
    $('#attr-row-field input').each(function(){
        var data_type = $(this).attr('data-type');
        var percent =$(this).val();

        //拼装结构
        temp_params_str[data_type] = percent;

    });
    WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['table']['td_width'] =temp_params_str;

    _We_ATTR.save();

    _We_M.show_bi(_We_M.uid);
};
//保存表格列文本对齐设置
_We_ATTR.pop.legendSave = function(){
    _We_ATTR.pop.hideLegend();//隐藏表格列文本对齐设置

    var temp_params_str = {};
    $('#legend-region select').each(function(){
        var data_type = $(this).attr('data-type');
        var legend =$(this).val();

        //拼装结构
        temp_params_str[data_type] = legend;
    });
    WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['table']['legend'] = temp_params_str;

    _We_ATTR.save();

    _We_M.show_bi(_We_M.uid);
};
//单一属性switch的禁止与允许
_We_ATTR.switch = function(property){
    var onswitch= WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][property]['switch'];

    if(onswitch == 1){
        $("input[name='"+ property +"']").siblings().removeAttr("checked");//switch开关  显示关闭状态
        $('#parents_' + property ).removeClass('layui-show');//隐藏展开的设置

        WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][property]['switch'] = 0;
    }else{
        $("input[name='"+ property +"']").siblings().attr("checked","checked");//switch开关  显示开启状态

        WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][property]['switch'] = 1;
    }

    _We_ATTR.save();
};
//属性设置是否允许展开
_We_ATTR.setModuleType = function (type){

    if(type == 'legend' || type == 'title' ){
        var onswitch = WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][type]['switch'];
        if( onswitch == 0){
            $('#'+ type + '.layui-colla-content').removeClass('layui-show');
        }
    }
};

//打开表格边框设置弹窗
_We_ATTR.showSetBorder = function (e){
    var edit_obj = 0;//边框线
    if( $('#table_border_select_html').css('display') != 'block'){
        if( JSON.stringify(_We_ATTR.rim) != '{}' && $('#rim-info-html').css('display') != 'none' ){
            if( JSON.stringify(_We_ATTR.rim_orient) == '{}'){
                layer.msg('请选择您要操作的边框', {icon: 2, offset: '70px', time: 1500});
                return false;
            }
            edit_obj = 1;
            var borderStyle_obj = _We_ATTR.rim[_We_ATTR.rim_orient].split(',');
            var borderStyle = borderStyle_obj['1'];
        }else{
            var borderStyle = WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['table']['borderStyle'];
        }
        $('.border-' + borderStyle).addClass('select-border-li');//选中设置的边框样式

        $('#table_border_select_html').fadeIn(500).css({
            "right": '248px',
            "top":e.pageY-260
        });

        $('.table_border_select li').each(function(){
            $(this).on('click',function(){
                var classify =  $(this).data('classify');
                _We_ATTR.SetBorder(classify,edit_obj);
            })
        });

    }else{
        _We_ATTR.hideSetBorder();
    }
};
//关闭表格边框设置弹窗
_We_ATTR.hideSetBorder = function (){
    $('#table_border_select_html li').removeClass('select-border-li');
    $('#table_border_select_html').fadeOut(500);
};
//保存表格表格边框设置
_We_ATTR.SetBorder = function(classify,edit_obj){

    _We_ATTR.hideSetBorder();//隐藏选项
    if(edit_obj == 0){//边框----单独设置边框线
        var table_td =  '#chart_'+_We_M.uid + ' td';
        var table_th =  '#chart_'+_We_M.uid + ' th';
        $( table_td +','+ table_th).css('border-style',classify);

        WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['table']['borderStyle'] = classify;
        _We_ATTR.save();
    }else{//边框的所有样式中边框线设置

        var rim_orient = _We_ATTR.rim[_We_ATTR.rim_orient].split(',');
        _We_ATTR.rim[_We_ATTR.rim_orient] = rim_orient[0]+ ','+ classify + ','+ rim_orient[2]+ ',' + rim_orient[3];

        switch ( _We_ATTR.rim_orient){

            case 'top':
                $(' .border-' +  _We_ATTR.rim_orient ).css(
                    "border-bottom-style",classify
                );
                break;
            case 'left':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-right-style",classify
                );
                break;
            case 'right':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-left-style",classify
                );
                break;
            case 'bottom':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-top-style",classify
                );
                break;
        }

    }

};
//打开边框设置
_We_ATTR.pop.showBorder = function (e) {

    BI.event.bind(document.getElementById('rim-info-html'),'input',attrBorderSet); //输入框变更事件

    if( $('#rim-info-html').css('display') != 'block'){
        $('#rim-info-html').fadeIn(500).css({
            "right": '248px',
            "top":e.pageY-320
        });
        //按钮绑定保存事件
        $('#rim-info-html button').attr("onclick", '_We_ATTR.pop.borderSave();');

    }else{
        _We_ATTR.pop.hideBorder();
    }

};
//隐藏边框设置弹层
_We_ATTR.pop.hideBorder = function(){
    _We_ATTR.rim = {};
    _We_ATTR.rim_obj = {};
    _We_ATTR.rim_orient = {};
    $('#rim-info-html').fadeOut(500);

    //移除边框选中样式
    $('.round-boder').removeClass('border-select');

    //为预览设置修改样式
    $('.border-box .border-top').css({
        'border-bottom-width':2,
        'border-bottom-color':'#000',
        'border-bottom-style':'solid'
    });
    $('.border-box .border-left').css({
        'border-right-width':2,
        'border-right-color':'#000',
        'border-right-style':'solid'
    });
    $('.border-box .border-right').css({
        'border-left-width':2,
        'border-left-color':'#000',
        'border-left-style':'solid'
    });
    $('.border-box .border-bottom').css({
        'border-top-width':2,
        'border-top-color':'#000',
        'border-top-style':'solid'
    });
     /*  初始化input设置   */
    _We_ATTR.pop.clearUp();

};
//点击边框线
_We_ATTR.pop.line = function(e){

    _We_ATTR.pop.clearUp();//清空设置

    var rim_obj = WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][_We_ATTR.rim_obj]['rim'];

    if( typeof rim_obj != 'undefined' && rim_obj != "" ){
        _We_ATTR.rim = rim_obj;
    }

    _We_ATTR.rim_orient = e.target.getAttribute('data-position');

    if( _We_ATTR.rim[ _We_ATTR.rim_orient] != 'undefined' && _We_ATTR.rim[ _We_ATTR.rim_orient] != "" ){

        var parm = _We_ATTR.rim[ _We_ATTR.rim_orient].split(',');
        //为设置的边框样式赋值

        if( parm[0] == '1'){
            $('.set-border .layui-form-checkbox').addClass('layui-form-checked');
            $(".set-box input[name='borderWidth']").val(parm[2]);
            $(".set-box input[name='borderColor']").val(parm[3]);
            $(".set-box input[name='borderColor']").css('background-color',parm[3]);

        }else{
            $('.set-border .layui-form-checkbox').removeClass('layui-form-checked');
        }

        //为预览设置修改样式
        $('.border-box .border-' +  _We_ATTR.rim_orient ).css('border',parm[0]);
        switch ( _We_ATTR.rim_orient){
            case 'top':
                $(' .border-' +  _We_ATTR.rim_orient ).css(
                    "border-bottom",parm[2] +' '+ parm[1] +' '+ parm[3]
                );
                break;
            case 'left':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    'border-right',parm[2] +' '+ parm[1] +' '+ parm[3]
                );
                break;
            case 'right':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-left",parm[2] +' '+ parm[1] +' '+ parm[3]
                );
                break;
            case 'bottom':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-top",parm[2] +' '+ parm[1] +' '+ parm[3]
                );
                break;
        }
    }

};
//保存边框设置
_We_ATTR.pop.borderSave = function(){

    WeBI.webi_dt['module'][_We_M.uid]['attribute_json'][_We_ATTR.rim_obj]['rim'] = _We_ATTR.rim;

    _We_ATTR.pop.hideBorder();//隐藏弹窗
    _We_ATTR.save();

};
//修改边框颜色
_We_ATTR.borderColor_change = function(obj) {
    var data_attribute = obj.getAttribute('data-attribute');
    var dom_id = obj.getAttribute('id');
    var color = $('#'+dom_id).val();

    if( JSON.stringify(_We_ATTR.rim) != '{}' ) {

        var rim_orient = _We_ATTR.rim[_We_ATTR.rim_orient].split(',');
        _We_ATTR.rim[_We_ATTR.rim_orient] = rim_orient[0]+ ','+ rim_orient[1] + ','+ rim_orient[2]+ ',' + color;

        $(".set-box input[name='borderColor']").val(color);
        $(".set-box input[name='borderColor']").css('background-color',color);

        switch ( _We_ATTR.rim_orient){

            case 'top':
                $(' .border-' +  _We_ATTR.rim_orient ).css(
                    "border-bottom-color",color
                );
                break;
            case 'left':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-right-color",color
                );
                break;
            case 'right':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-left-color",color
                );
                break;
            case 'bottom':
                $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                    "border-top-color",color
                );
                break;
        }
    }else{
        layer.msg('请选择您要操作的边框', {icon: 2, offset: '70px', time: 1500});
    }

};
//初始化边框设置框
_We_ATTR.pop.clearUp = function(){

    //移除边框显示复选框
    $('.set-border .layui-form-checkbox').removeClass('layui-form-checked');

    $(".set-box input[name='borderWidth']").val('');
    $(".set-box input[name='borderColor']").val('');
    $(".set-box input[name='borderColor']").css('background-color','#fff');

};
//修改边框弹窗中相应边框样式
function  attrBorderSet(ev){
    var event = ev || event;

    var data_attribute = event.target.getAttribute('data-attribute');
    if( JSON.stringify(_We_ATTR.rim_orient) != '{}' ){
        var rim_orient = _We_ATTR.rim[_We_ATTR.rim_orient].split(',');
        if( data_attribute == 'borderWidth'){
            _We_ATTR.rim[_We_ATTR.rim_orient] = rim_orient[0]+ ','+ rim_orient[1] + ','+event.target.value+ ',' + rim_orient[3];

            switch ( _We_ATTR.rim_orient){

                case 'top':
                    $(' .border-' +  _We_ATTR.rim_orient ).css(
                        "border-bottom-width",event.target.value
                    );
                    break;
                case 'left':
                    $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                        "border-right-width",event.target.value
                    );
                    break;
                case 'right':
                    $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                        "border-left-width",event.target.value
                    );
                    break;
                case 'bottom':
                    $('.border-box .border-' +  _We_ATTR.rim_orient ).css(
                        "border-top-width",event.target.value
                    );
                    break;
            }
        }
    }else{
        layer.msg('请选择您要操作的边框', {icon: 2, offset: '70px', time: 1500});
    }

};

function toPage(){
        layui.use('laypage', function(){
            var laypage = layui.laypage;

            laypage.render({
                elem: 'page',
                count: total,
                curr: page,
                limit:limit,
                theme:"#0099ff",
                jump:function(obj, first) {//点击页数按钮触发的函数
                    page = obj.curr;//得到点击的页数
                    $('#currPage').val(page);
                    var limts = obj.limit;
                    $('#limit').val(limts);
                    if(!first){ //一定要加此判断，否则初始时会无限刷新
                        _We_ATTR.pageTheme(_We_ATTR.theme_url +'&page='+page+'&limit='+limit);
                    }
                }
            });
        });
    }
//打开模板页面
_We_ATTR.showTheme = function () {

    layer.open({
        title: '模板库',
        offset: 'auto',
        type: 1,
        area: ['98%', '98%'],
        scrollbar: false,
        shade:'#000',
        shadeClose:true,
        closeBtn: 1,
        content: $('#theme-box'),
        btn: ['确认','取消'],
        yes: function () {

            if($('.theme-content li').hasClass('select-theme-li')){//判断是否选中模板

                if( JSON.stringify(WeBI.webi_dt['module']) != '{}' || JSON.stringify(WeBI.webi_dt['module']) != '[]'){
                    layer.confirm('覆盖现有报表，是否继续？',{icon:3},function (index) {
                        layer.close(index);
                        _We_ATTR.saveChooseTheme(1);//保存选择的模板BI
                    });
                }else{
                    _We_ATTR.saveChooseTheme(0);//保存选择的模板BI
                }
            }else{
                layer.msg('您未选择BI模板！', {icon: 2, offset: '70px', time: 1500});
                return false;
            }
        }
    });

    //搜索绑定保存事件
    $('.search-theme i').attr("onclick", '_We_ATTR.themeKeyDown(0);');

    $.each($('.radio-button-wrapper'),function (k,v){
        $(this).on('click',function(){
            var id = $(this).attr('data-index');
            _We_ATTR.themeList(id);
        })
    });

    //触发默认模板分组
    $('.radio-button-1').trigger("click");
};
//选择模板,添加选中样式
_We_ATTR.chooseTheme = function (id) {
    $('.template-' + id).addClass('select-theme-li').siblings().removeClass('select-theme-li');
};
//模板列表
_We_ATTR.themeList = function(id){

    $('.radio-button-'+ id).eq($(this).index()).addClass('select-header-nav').siblings().removeClass('select-header-nav');
    $('.theme-content').empty();//清空主体
    $('#group-type-id').val(id);

    _We_ATTR.theme_url ='/webi/design/theme/list?id='+id+'&keyword=1';
    page = 1;//重置页码
    _We_ATTR.pageTheme(_We_ATTR.theme_url+'&page='+page+'&limit='+limit);//分页展示数据


};
//搜索模板模板
_We_ATTR.themeKeyDown = function(key){
    if( key == 0){
        var inputValue = document.getElementById('theme-search').value;
        if(inputValue == ""){
            layer.msg('请输入模板名称', {icon: 2, offset: '70px', time: 1500});
            return false;
        }
        var word = 'theme_title='+inputValue+ '&id=' +$('#group-type-id').val();
    }else{
        var word = 'keyword='+key + '&id=' +$('#group-type-id').val();
    }
    _We_ATTR.theme_url ='/webi/design/theme/list?'+word;
    page = 1;//重置页码
    _We_ATTR.pageTheme(_We_ATTR.theme_url+'&page='+page+'&limit='+limit);//分页展示数据

};
//保存选中的模板
_We_ATTR.saveChooseTheme = function(module_id){

    var id = $('.select-theme-li').data('id');//选中的模板id
    $.ajax({
        type:'post',
        url:'/webi/design/theme/choose',
        data:{
            module_id:module_id,
            template_id:id,
            bi_id: _We_G.bi_id
        },
        success:function (o) {
            if ( o.code == 200 ) {
                layer.closeAll();
                location.reload();
            } else {
                layer.alert(o.message,{icon:2,offset:'70px'});
            }
        }
    })
};
//生成模板（li）
_We_ATTR.create_ul = function(data){

    var temp = $('#template-li').html();
    var li_html="";

    $.each(data, function (k, v) {
        li_html+= temp;
        li_html = li_html.replace(/[\$]id/g,v._id);
        li_html = li_html.replace(/#/g,v.template_pic);
        li_html = li_html.replace(/template_title/g,v.template_title);
    });
    return li_html;
};
//分页显示
_We_ATTR.pageTheme = function(url){
    var index_loading = layer.load(2);

    $.ajax({
        type: 'GET',
        url: url,
        success:function (res) {

            layer.close(index_loading);

            if (res.code == 200) {

                $('#theme-search').val('');
                if (res.data) {
                    total = res.data.count;
                    $('.theme-content').empty();//清空主体
                    var creat = _We_ATTR.create_ul(res.data.template);
                    $('.theme-content').append(creat);
                    toPage();//调用分页插件
                }else{
                    layer.msg('暂无匹配信息', {icon: 1, offset: '70px',time: 1500});
                    return false;
                }
            }
        }
    });

};

