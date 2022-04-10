/**
 *  数据集操作对象
 *
 *  操作函数集合
 */
var _We_DTS = {};
_We_DTS.fields_json = ''; //数据集字段信息
_We_DTS.edit_k = 0; //待编辑where条件索引值
_We_DTS.select = {}; //数据集控制对象
_We_DTS.field = {}; //字段控制对象
_We_DTS.editObj = {}; //当前编辑的字段对象
_We_DTS.pop = {}; //字段修饰弹层控制
_We_DTS.where = {}; //设置筛选条件控制对象


$("#sums,#rows,#cols").sortable({//数据集ul绑定拖拽事件
    revert: true,
    cursor: "move",
    deactivate:function ( event, ui ) {
        _We_M.save_dts();
    }
});

$(document).on('click','#dts_design_icon',function () { //数据集设置模块隐藏
    _We_DTS.hideModule();
}).on('click','#left-side-show',function () { //数据集设置模块显示
    _We_DTS.showModule();
}).on('click','.bottom-oper .delete',function () { //清空所有设置
    _We_DTS.reset();
}).on('change','.bottom-oper .refresh input',function () { //自动刷新
    _We_DTS.refresh();
}) .on('click','.bottom-oper .change',function () { //行列互换
    _We_DTS.exchange();
}).on('click','.up-arrow',function () {  //隐藏数据集
    _We_DTS.select.hide();
}).on('click','.down-arrow',function () {  // 展示数据集
    _We_DTS.select.show();
}).on('change','#selectDts',function () {  //根据所选数据集获取字段信息
    _We_DTS.select.change();
}).on('change','#selectSour',function () {  //根据所选数据源获取数据集
    _We_DTS.select.sourceChange();
}).on('click','.add-show',function () { //显示字段列表
    _We_DTS.field.show($(this));
}).on('click','.field-info-close',function () { //关闭字段列表
    _We_DTS.field.hide();
}).on('click','.field-i-icon',function () { //数据集内部小图标显示和隐藏的切换
    _We_DTS.field.inner_handel($(this));
}).on('click','.sub-item .fr',function (e) { //显示一级操作
    _We_DTS.editObj = $(this).parent().find('.sub_field');
    _We_DTS.pop.show(e);
    _We_DTS.field.hide();
}).on('mouseover','.operation-1',function () { //显示二级操作
    _We_DTS.pop.showSecond($(this));
}).on('mouseover','.operation-1 p',function () { //显示三级操作
    _We_DTS.pop.showThird($(this));
}).on('click','.operation-1 .delete',function () { //删除字段
    _We_DTS.field.del();
}).on('click','.operation-1 .rename',function () { //显示重命名字段文本框
    _We_DTS.pop.rename();
}).on('click','.limit-num',function () { //limit设置
    _We_DTS.pop.limit( $(this).text() );
}).on('click','.limit-self',function () { //自定义limit显示
    _We_DTS.pop.limit( $(this).parent().parent().find('input').val() );
}).on('click','.operation-2 .order-type',function () { //排序
    _We_DTS.pop.sort( $(this).attr('data-order') );
}).on('click','.operation-1 .edit',function () { //展示where设置弹层
    _We_DTS.edit_k = _We_DTS.editObj.attr('data-uid');
    _We_DTS.where.show();
}).on('click','.oper-th .edit',function () {  //where弹层内点击编辑where条件
    _We_DTS.where.editShow($(this));
}).on('click','.oper-th .delete',function () { //where弹层内点击删除where条件
    _We_DTS.where.editDel( $(this).attr('data-id') );
}).on('change','#select_y_n',function () { //where弹层内改变是否关系
    _We_DTS.where.editChange();
}).on('change','.search-relation',function () { //where弹层内改变比较关系
    _We_DTS.where.editChange();
}).on('keyup','#text_start,#text_end',function () { //文本区间变更
    _We_DTS.where.editChange();
}).on('click','#confirm',function () { //where条件编辑完成
    _We_DTS.where.submit();
}).on('click','#cancel',function () { //取消编辑where条件
    $(".screen-layer").hide();
}).on('click','.layer-close',function () { //关闭where弹层
    _We_DTS.where.hide();
});

//显示数据集
_We_DTS.select.show = function() {
    if ( _We_G.save_type == 1 ) {
        layer.msg('请选择BI组件', {icon: 2, offset: '70px', time: 1500});
        return false;
    }
    $(".select-box").fadeIn(500);
    $(this).removeClass('down-arrow').addClass('up-arrow');
};
//隐藏数据集
_We_DTS.select.hide = function() {
    $('#selectBox').addClass('hide');
    $(this).removeClass('up-arrow').addClass('down-arrow');
};
//数据集下拉框变更事件
_We_DTS.select.change = function() {
    var id = $("#selectDts").find("option:selected").val();
    var table = $("#selectDts").find("option:selected").text();
    if ( id != 0) {
        _We_DTS.get_rule(id);
    }

    _We_DTS.clearUp(); //清空页面数据集设置信息
    $('#selectDts').selectpicker('val', id);//由于清空了选项，需要重新设置下拉框选择项
    WeBI.webi_dt['module'][_We_M.uid]['db_json'] = {
        "view_id": id,
        "view_table": _We_DTS.fields_json,
        "auto_refresh":0,
        "row": '',
        "column": '',
        "sum": '',
        "limit": 20,
        "sort": '',
        "where":''
    };
    _We_M.save_dts(); //保存数据集设置
};
_We_DTS.select.sourceChange = function(){
    var group_id = $("#selectSour").find("option:selected").val();
    _We_DTS.makeDts('',group_id);//生成数据集列表
};

//显示字段列表
_We_DTS.field.show = function(obj) {

    if (_We_M.uid == 0 || _We_M.uid == '') {
        layer.msg("请选择BI组件", {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    if ( $.isEmptyObject(_We_DTS.fields_json) ) {
        layer.msg('请选择数据集', {icon: 2, offset: '70px', time: 1500});
        return false;
    }
    $('#field_info_pop').find('li').find('input').iCheck('uncheck');
    $(".operation").hide();
    $("#field_info_pop").fadeIn(500);
    $("#field_info_pop .field-inf-btn button").attr("onclick",'_We_DTS.field.get_field('+ parseInt(obj.attr('data-id')) +');');
};
//关闭字段列表
_We_DTS.field.hide = function() {
    $('#field_info_pop').find('li').find('input').iCheck('uncheck');
    $('.field-info').fadeOut(500);
};
//删除字段
_We_DTS.field.del = function() {

    var data_type = parseInt( _We_DTS.editObj.attr('data-type') ); //获取当前编辑的字段所属分组类型
    var data_field = _We_DTS.editObj.attr('data-field'); //获取当前编辑的字段名称
    var data_uid = _We_DTS.editObj.attr('data-uid');

    _We_DTS.pop.hide();

    //删除当前的字段li
    _We_DTS.editObj.parent().remove();

    if ( $('.ul-'+data_type+' li').length == 0 ) { //ul中没有li时，改变父级样式
        $('#item-inner-'+data_type).css('padding','0 7px');
    }

    if ( data_type == 5 ) { //where条件删除
        delete WeBI.webi_dt['module'][_We_M.uid]['db_json']['where'][data_uid];
        _We_M.save_dts();
        return false;
    }

    _We_M.save_dts();

};
//获取选中字段列表信息
_We_DTS.field.get_field = function(type) {

    var field_obj_list = $('#field_info_pop input[name="fields"]:checked');
    if( field_obj_list.length == 0 ){
        layer.msg('请选择字段', {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    //获取字段信息
    var field_data = [];
    var field_alias = '';
    $.each( field_obj_list , function(k,v){
        field_alias += ','+$(this).attr('data-table')+'.'+$(this).attr('data-mark');
        field_data.push({
            table:$(this).attr('data-table'),
            field:$(this).val(),
            alias:$(this).attr('data-mark'),
            type:$(this).attr('data-type')
        });
    });

    //校验是否存在重名的字段
    var target_li_dt = $('.ul-'+type+' .sub_field');
    var error_msg = '';
    if ( target_li_dt.length > 0 ){
        $.each( $('.ul-'+type+' .sub_field') , function(k,v){
            var cur_field_alias = $(this).attr('data-table')+'.'+$(this).attr('data-alias');
            if( field_alias.indexOf(cur_field_alias) > -1 ){
                error_msg = '字段<'+cur_field_alias+'>重复';
                return false;
            }
        });
    }

    if( error_msg != '' ){
        layer.msg(error_msg, {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    //贴上选择的字段
    var total_html = '';
    $.each(field_data, function (k,v) {

        var uid = '';
        var sortOrder = '';

        switch (type){
            case 4:
                sortOrder = 'ASC';
                break;
            case 5:
                uid = BI.guid();

                if( typeof _We_DTS.where.addObj == 'undefined' ){
                    _We_DTS.where.addObj = {};
                }

                _We_DTS.edit_k = uid;

                _We_DTS.where.addObj[uid] = [v.table+'.'+v.field,v.alias,'Y',v.type,'=','',1,1].join(':');
                break;
        }

        var li_list_html = $('#dts_field_li_html').html();
        li_list_html = li_list_html.replace(/[\$]type/g,type);
        li_list_html = li_list_html.replace(/[\$]table/g,v.table);
        li_list_html = li_list_html.replace(/[\$]field/g,v.field);
        li_list_html = li_list_html.replace(/[\$]alias/g,v.alias);
        li_list_html = li_list_html.replace(/[\$]sort/g,sortOrder);
        li_list_html = li_list_html.replace(/[\$]uid/g,uid);

        total_html += li_list_html;
    });

    //贴上选择的字段
    $("#item-inner-"+type).find('ul').append(total_html);

    //ul中有li时，改变父级样式
    $('.ul-'+type).parent().css('padding','5px 7px');

    _We_DTS.field.hide();

    if( type == 5 ){ //打开where条件修改弹层
        _We_DTS.where.show();
        return false;
    }

    _We_M.save_dts();
};
//选择字段弹层内部显示和隐藏操作
_We_DTS.field.inner_handel = function(obj) {
    if( obj.hasClass('glyphicon-menu-down') ){
        obj.removeClass('glyphicon-menu-down').addClass('glyphicon-menu-up');
    } else {
        obj.removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');
    }
    obj.next().toggle(500);
    obj.parent().siblings().find('.field-inner-ul').fadeOut(500);
};


/**
 * 打开字段修饰弹层
 * filed_type 类型：1.汇总 2.行信息 3.列信息 4.排序信息 5.where信息
 */
_We_DTS.pop.show = function(e) {
    var filed_type = parseInt( _We_DTS.editObj.attr('data-type') );
    switch ( filed_type ){
        case 1:
        case 2:
        case 3:
            $('#dts_pop_html').find('.dts_pop_edit').hide();
            $('#dts_pop_html').find('.dts_pop_rename').show();
            $('#dts_pop_html').find('.dts_pop_sort').show();
            $('#dts_pop_html').fadeIn(500).css('top',e.pageY-110);
            break;
        case 4:
            $('#dts_pop_html').find('.dts_pop_edit').hide();
            $('#dts_pop_html').find('.dts_pop_rename').hide();
            $('#dts_pop_html').find('.dts_pop_sort').show();
            $('#dts_pop_html').fadeIn(500).css('top',e.pageY-75);
            break;
        case 5:
            $('#dts_pop_html').fadeIn(500).css('top',e.pageY-75);
            $('#dts_pop_html').find('.dts_pop_edit').show();
            $('#dts_pop_html').find('.dts_pop_rename').hide();
            $('#dts_pop_html').find('.dts_pop_sort').hide();
            break;
    }


};
//隐藏字段修饰弹层
_We_DTS.pop.hide = function() {
    $('#dts_pop_html').fadeOut(500);
    $('#dts_pop_html').find('.dts_pop_edit').show();
    $('#dts_pop_html').find('.dts_pop_rename').show();
    $('#dts_pop_html').find('.dts_pop_sort').show();
    _We_DTS.pop.hideSecond();
    _We_DTS.pop.hideThird();
};
//打开限制条件设置
_We_DTS.pop.showSecond = function(obj) {
    $('.m-o').removeClass('oper-chos');
    obj.find('.m-o').addClass('oper-chos');
    obj.find('.operation-2').fadeIn(500);
    obj.siblings().find('.operation-2').fadeOut(500);
};
//隐藏限制条件设置
_We_DTS.pop.hideSecond = function() {
    $(".operation-2").fadeOut(500);
};
//打开指定限制条件设置
_We_DTS.pop.showThird = function(obj) {
    $(".operation-3").hide();
    obj.addClass('check').siblings().removeClass('check');
    if ( obj.index() == 3 ) {
        $(".operation-3").fadeIn(500);
    }
};
//隐藏指定限制条件设置
_We_DTS.pop.hideThird = function() {
    $(".operation-3").fadeOut(500);
};
//展示重命名字段文本框
_We_DTS.pop.rename = function() {
    _We_DTS.pop.hide(); //隐藏设置框
    //创建一个文本框
    var oA = document.createElement('input');
    oA.className = 'edit_field_text';
    oA.style.height = _We_DTS.editObj.height();
    oA.style.width = _We_DTS.editObj.width();
    oA.value = _We_DTS.editObj.attr('data-alias');
    _We_DTS.editObj.html('');
    _We_DTS.editObj.append(oA);
    oA.focus();
    BI.event.bind(oA,'keydown',_We_DTS.pop.renameField);//绑定事件
};
//回车修改字段别名
_We_DTS.pop.renameField = function(e) {
    var event = e || event;
    if( event.keyCode != 13 ){
        return false;
    }
    var new_field_alias = $.trim( $(this).val() );
    if( new_field_alias == '' ){
        layer.msg('请输入字段名称', {icon: 2, offset: '70px', time: 1500});
        return false;
    }
    var old_alias = _We_DTS.editObj.attr('data-alias');
    _We_DTS.editObj.html(new_field_alias);
    _We_DTS.editObj.attr('data-alias',new_field_alias);
    if( old_alias== new_field_alias ){//没有更改字段名称
        console.log('没有修改字段');
        return false;
    }
    _We_M.save_dts();
};
//limit设置
_We_DTS.pop.limit = function(limit) {
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['limit'] = limit;
    $(".option").hide();
    _We_M.save_dts();
    _We_M.show_bi();
};
//排序设置
_We_DTS.pop.sort = function(sortOrder) {

    var data_table = _We_DTS.editObj.attr('data-table'); //获取当前编辑的字段所属表
    var data_type = parseInt( _We_DTS.editObj.attr('data-type') ); //获取当前编辑的字段所属分组类型
    var data_field = _We_DTS.editObj.attr('data-field'); //获取当前编辑的字段名称
    var data_alias = _We_DTS.editObj.attr('data-alias'); //获取当前编辑的字段别名
    var exist_flg = 0; //判断编辑的元素是否在排序字段里面

    //遍历页面字段
    $.each( $('#item-inner-' + data_type).find('.sub_field'),function(k,v){
        if( $(this).attr('data-alias') == data_alias ){
            $(this).attr('data-sort',sortOrder);
            $(this).text(data_alias);
            exist_flg = 1;
            return false;
        }
    });

    //排序字段
    if( data_type == 4){
        if( exist_flg == 0 ){ //添加一个新排序字段
            var li_list_html = $('#dts_field_li_html').html();
            li_list_html = li_list_html.replace(/[\$]type/g,data_type);
            li_list_html = li_list_html.replace(/[\$]table/g,data_table);
            li_list_html = li_list_html.replace(/[\$]field/g,data_field);
            li_list_html = li_list_html.replace(/[\$]alias/g,data_alias);
            li_list_html = li_list_html.replace(/[\$]sort/g,sortOrder);
            li_list_html = li_list_html.replace(/[\$]uid/g,'');
            $("#item-inner-4").find('ul').append(li_list_html);
        }
    }

    _We_M.save_dts();
};


/**
 * 展开where条件弹层
 * 循环字符串示例：'sale_money:销售额:Y:decimal:>:100:2:0'
 * 第1位：原始库表字段名称
 * 第2位：字段别名
 * 第3位：是非条件  是:Y 非:N
 * 第4位：字段类型
 * 第5位：比较条件（mysql表示）
 * 第6位：比较条件对应的数值或者范围
 * 第7位：比较条件对应的页面下拉框值 1:=, 2:>, 3:>=, 4:<, 5:<=, 6:IN, 7:!=, 8:LIKE, 9:BETWEEN
 * 第8位：表示连接条件为“AND”还是“OR” 0: or, 1: and
 */
_We_DTS.where.show = function() {

    _We_DTS.pop.hide();

    delete _We_DTS.where.jsonObj;

    _We_DTS.where.jsonObj = $.extend({}, WeBI.webi_dt['module'][_We_M.uid]['db_json']['where']);
    $('.search-conditions tbody').empty();

    if( $.isEmptyObject(_We_DTS.where.jsonObj) || _We_DTS.where.jsonObj == '' ){
        _We_DTS.where.jsonObj = {};
    }

    //存在添加元素，此时为选择新的where条件时触发
    if( !$.isEmptyObject(_We_DTS.where.addObj) ){
        _We_DTS.where.jsonObj = $.extend(_We_DTS.where.jsonObj, _We_DTS.where.addObj);
        delete _We_DTS.where.addObj;
    }

    var c_html = '';
    $.each(_We_DTS.where.jsonObj,function (k,v) {

        var w_a = v.split(':');
        _We_DTS.where.jsonObj[k] = w_a;
        var sql = '';
        var desc = '';

        switch(parseInt(w_a[6])){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 7:
                sql = [w_a[1],w_a[4],w_a[5]].join(' ');
                break;
            case 6:
                sql = [w_a[1],w_a[4],'('+w_a[5]+')'].join(' ');
                break;
            case 8:
                sql = [w_a[1],w_a[4],'%'+w_a[5]+'%'].join(' ');
                break;
            case 9:
                sql = [w_a[1],w_a[4],w_a[5].replace(',',' AND ')].join(' ');
                break;
        }

        if( w_a[2] == 'N' ){
            sql = 'not('+sql+')';
        }

        var temp_html = '<tr id="tr_$key" class="tr-$key">';
        temp_html += '<td title="$sql" class="title-th"><p>$sql</p></td>';
        temp_html += '<td title="$description" class="tc des-th"><p>$description</p></td>';
        temp_html += '<td class="tc check-th">';
        temp_html += '<div class="check-box">';
        temp_html += '<input type="checkbox" data-id="$key" id="checkbox_$key" class="square-radio redrio" $checked>';
        temp_html += '</div>';
        temp_html += '<span style="margin-left: 5px;">AND</span>';
        temp_html += '</th>';
        temp_html += '<td class="tc oper-th">';
        temp_html += '<i data-id="$key" class="layui-icon edit">&#xe642;</i>';
        temp_html += '<i data-id="$key" style="margin-left: 10px;" class="layui-icon delete">&#xe640;</i>';
        temp_html += '</td>';
        temp_html += '</tr>';

        var checked = '';
        if ( w_a[7] == 1 ) {
            checked = 'checked';
        }

        temp_html = temp_html.replace(/[\$]key/g,k);
        temp_html = temp_html.replace(/[\$]sql/g,sql);
        temp_html = temp_html.replace(/[\$]description/g,desc);
        temp_html = temp_html.replace(/[\$]checked/,checked);

        c_html += temp_html;

    });

    $('.search-conditions tbody').html(c_html);
    _We_G.eventBindInit();

    _We_DTS.where.expression();
    $('#tr_'+_We_DTS.edit_k).find('.edit').click();
    $(".screen-layer").fadeIn(500);

    $('[id*="checkbox_"]').on('ifChanged', function(){
        _We_DTS.where.editAnd( $(this).attr('data-id') );
    });

};
//隐藏where条件弹层
_We_DTS.where.hide = function() {
    delete _We_DTS.where.jsonObj;
    $(".screen-layer").fadeOut(500);
    $('.search-conditions tbody').empty();
};
//修改单条where条件
_We_DTS.where.editShow = function(obj) {

    _We_DTS.edit_k = obj.attr('data-id');
    if( _We_DTS.where.jsonObj[_We_DTS.edit_k][2] == 'Y' ){
        BI.selectChoose('select_y_n','是');
    } else {
        BI.selectChoose('select_y_n','否');
    }

    $('.edit-now').html(_We_DTS.where.jsonObj[_We_DTS.edit_k][1]);
    $('#select_relation').val( _We_DTS.where.jsonObj[_We_DTS.edit_k][6] );

    if( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ){
        $('.text-in').hide();
        $('.time-in').show();
        $(".time-include").hide();
    } else {
        $('.text-in').show();
        $('.time-in').hide();
        $(".text-include").hide();
    }

    //BETWEEN类型
    if( _We_DTS.where.jsonObj[_We_DTS.edit_k][6] == 9 ){
        var val_dt = _We_DTS.where.jsonObj[_We_DTS.edit_k][5].split(','); //将值按‘,’拆分成数组
        if( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ){
            $('#date_start').val(val_dt[0]);
            $('#date_end').val(val_dt[1]);
            $(".time-include").show();
            $(".text-include").hide();
        } else {
            $('#text_start').val(val_dt[0]);
            $('#text_end').val(val_dt[1]);
            $(".text-include").show();
            $(".time-include").hide();
        }
    } else {
        if ( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ) {
            $('#date_start').val(_We_DTS.where.jsonObj[_We_DTS.edit_k][5]);
        } else {
            $('#text_start').val(_We_DTS.where.jsonObj[_We_DTS.edit_k][5]);
        }
    }
};
//删除where条件
_We_DTS.where.editDel = function(uid) {
    delete _We_DTS.where.jsonObj[uid];
    $('#tr_'+uid).remove();
    if( _We_DTS.edit_k == uid ){
        _We_DTS.edit_k = '';
        $('.edit-now').html('');
        $('#select_y_n').html('Y');
        $('#select_relation').html('1');
        $('.text-in').show();
        $('.time-in').hide();
        $(".text-include").hide();
    }
    _We_DTS.where.expression();
};
//点击AND复选框
_We_DTS.where.editAnd = function(uid) {
    if( typeof _We_DTS.where.jsonObj[uid] == 'undefined' ){
        _We_DTS.where.editDel(uid);
        return false;
    }
    if( $('#checkbox_'+uid).is(':checked') ){
        _We_DTS.where.jsonObj[uid][7] = 1;
    } else {
        _We_DTS.where.jsonObj[uid][7] = 0;
    }
    _We_DTS.where.expression();
};
//改变比较条件
_We_DTS.where.editChange = function() {

    var y_n = $('#select_y_n').val();
    var relation = parseInt( $('#select_relation').val() );
    var relation_val = '';

    if( relation == 9 ){

        if( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ){
            $(".time-include").show();
            $(".text-include").hide();
        } else {
            $(".time-include").hide();
            $(".text-include").show();
        }

    } else {

        if( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ){
            $('.text-in').hide();
            $('.time-in').show();
            $(".time-include").hide();
        } else {
            $('.text-in').show();
            $('.time-in').hide();
            $(".text-include").hide();
        }

    }

    if( relation == 9 ){ //BETWEEN类型
        if( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ){
            relation_val = $('#date_start').val()+','+$('#date_end').val();
        } else {
            relation_val = $('#text_start').val()+','+$('#text_end').val();
        }
    } else {
        if( _We_DTS.where.jsonObj[_We_DTS.edit_k][3] == 'date' ){
            relation_val = $('#date_start').val();
        } else {
            relation_val = $('#text_start').val();
        }
    }

    _We_DTS.where.jsonObj[_We_DTS.edit_k][2] = y_n;
    _We_DTS.where.jsonObj[_We_DTS.edit_k][4] = relation_type[relation];
    _We_DTS.where.jsonObj[_We_DTS.edit_k][5] = relation_val;
    _We_DTS.where.jsonObj[_We_DTS.edit_k][6] = relation;

    _We_DTS.where.expression();

};
//提交where条件
_We_DTS.where.submit = function() {

    if( $.isEmptyObject(_We_DTS.where.jsonObj) ){
        WeBI.webi_dt['module'][_We_M.uid]['db_json']['where'] = '';
    } else {

        WeBI.webi_dt['module'][_We_M.uid]['db_json']['where'] = {};
        for ( uid in _We_DTS.where.jsonObj ){
            WeBI.webi_dt['module'][_We_M.uid]['db_json']['where'][uid] = _We_DTS.where.jsonObj[uid].join(':');
        }

    }

    $('.edit-now').html('');
    $('.search-relation').val('1');
    $('.edit-type').val('');
    $('.text-in').show();
    $('.time-in').hide();
    $(".text-include").hide();

    _We_DTS.where.hide();

    _We_M.save_dts();
};
//判断字段是否属于字符串
_We_DTS.where.field_is_string = function(field_type) {
    //字符串类型
    var str_arr = ['string','char','date','text'];
    var is_string = false;
    for(var i=0; i<4; i++){
        if( field_type.indexOf(str_arr[i]) != -1 ){
            is_string = true;
            break;
        }
    }
    return is_string;
};
//显示筛选表达式
_We_DTS.where.expression = function(){

    if( JSON.stringify(_We_DTS.where.jsonObj) == '{}' ){
        return false;
    }

    //连接or条件
    var or_where_string = '';

    //连接and条件
    var and_where_string = '';

    $.each(_We_DTS.where.jsonObj,function (k,v) {

        //例子：'cal_date:统计日期:Y:date:BETWEEN:2018-01-01,2018-03-02:9:1'
        switch ( parseInt(v[6]) ){

            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 7:

                if( _We_DTS.where.field_is_string(v[3]) ){
                    var val_str = '"'+v[5]+'"';
                } else {
                    var val_str = v[5];
                }

                var condition_string = [
                    v[1],
                    relation_text[v[6]],
                    val_str
                ].join(' ');
                break;

            case 6:

                var val_str = '';

                if( _We_DTS.where.field_is_string(v[3]) ){
                    var val_arr = v[5].split(',');
                    for( k in val_arr) {
                        val_arr += ',"'+$val+'"';
                    }
                    val_str = val_str.substr(1);
                } else {
                    val_str = v[5];
                }

                var condition_string = [
                    v[1],
                    relation_text[v[6]],
                    '('+val_str+')'
                ].join(' ');
                break;

            case 8:
                var condition_string = [
                    v[1],
                    relation_text[v[6]],
                    '%'+v[5]+'%'
                ].join(' ');
                break;

            case 9:

                var val_arr = v[5].split(',');
                if( _We_DTS.where.field_is_string(v[3]) ){
                    val_str = '"'.val_arr[0]+'" AND "'+val_arr[1]+'"';
                } else {
                    val_str = val_arr[0]+' AND '+val_arr[1];
                }

                var condition_string = [
                    v[1],
                    relation_text[v[6]],
                    val_str
                ].join(' ');
                break;

        }

        if( v[2] == 'N' ){
            condition_string = ' NOT('+condition_string+')';
        }

        if( v[7] == 0 ){
            or_where_string += ' OR  ' + condition_string;
        } else {
            and_where_string += ' AND ' + condition_string;
        }

    });

    var sql_string = '';
    if( and_where_string != '' ){
        sql_string = and_where_string.substr(4);
    }
    if( or_where_string != '' ){
        if( sql_string == '' ){
            sql_string = or_where_string.substr(4);
        } else {
            sql_string += or_where_string;
        }
    }

    $('#show_where_sql').val(sql_string);

};


//显示数据集设置模块
_We_DTS.showModule = function() {

    $('#left-side-show').css('opacity','0');
    setTimeout(function(){
        _We_M.BISelfSuite();
    }, 500);
    $('#g_dts_main').addClass('slient');
    $('.main-cont').addClass('pdleft');

};
//隐藏数据集设置模块
_We_DTS.hideModule = function() {

    $('#left-side-show').css('opacity','0');
    setTimeout(function(){
        _We_M.BISelfSuite();
    }, 500);

    $('#g_dts_main').removeClass('slient');
    $('.main-cont').removeClass('pdleft');

};
//清空所有设置
_We_DTS.reset = function() {
    if ( _We_M.uid == 0 || _We_M.uid == '' ) {
        layer.msg("请选择要操作的BI组件", {icon: 2, offset: '70px', time: 1500});
        return false;
    }
    layer.confirm('您确认需要清除所有设置吗？', {icon: 3, title:'清空设置'}, function(index){
        layer.close(index);

        _We_DTS.clearUp();
        WeBI.webi_dt['module'][_We_M.uid]['db_json'] = {
            "view_id": 0,
            "view_table": '',
            "auto_refresh": 0,
            "row": '',
            "column": '',
            "sum": '',
            "limit": 20,
            "sort": '',
            "where":''
        };
        $('#chart_'+_We_M.uid).html('');
        _We_M.save_dts(); //保存数据集设置
    });
};
//自动刷新
_We_DTS.refresh = function() {
    var auto_refresh = 0;
    if($('#refresh').is(':checked')) {
        auto_refresh = 1;
    }

    if (typeof WeBI.webi_dt['module'][_We_M.uid] == "undefined") {
        layer.msg("请选择要操作的BI组件", {icon: 2, offset: '70px', time: 1500});
        _We_DTS.iCheck();//移除选中
        return false;
    }

    $('#refresh').val(auto_refresh);//更改自动刷新页面属性
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['auto_refresh'] = auto_refresh;

    if(  parseInt( auto_refresh ) > 0){  //加入自动刷新
        _We_M.refreshObj.interval[_We_M.uid] =
            setInterval("_We_M.show_bi('" + _We_M.uid + "')", parseInt( auto_refresh ) * 1000 * 60);
    } else {
        clearInterval(_We_M.refreshObj.interval[_We_M.uid]); //停止局部计数器
    }
    _We_M.save_dts(2);

};
//自动刷新选中、移除
_We_DTS.iCheck = function() {
    if( $('#refresh').val() == 1) {
        $("#refresh").prop('checked',true);
    }else{
        $("#refresh").prop("checked",false);
    }
};
//行列互换
_We_DTS.exchange = function() {
    if ( _We_M.uid == 0 || _We_M.uid == '' ) {
        layer.msg("请选择要操作的BI组件", {icon: 2, offset: '70px', time: 1500});
        return false;
    }
    var temp = WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'];
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'] = WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'];
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'] = temp;

    _We_DTS.clearUp();//清空数据集设置
    _We_DTS.show_bi_fields(WeBI.webi_dt['module'][_We_M.uid]['db_json']);//更新数据集各个维度的字段列表

    _We_M.save_dts();

};
//清空数据集设置
_We_DTS.clearUp = function() {
    $(".top-grid").removeClass('grid-active');
    _We_DTS.edit_k = 0;
    _We_DTS.fields_json = ''; //重置数据集字段
    $("#selectBox").removeClass('hide');
    $(".field-info").find('ul').empty();
    $(".item-inner ul").empty();
    $(".item-inner").css('padding','0 7px');
    $("#refresh").val(0);

    $('#selectDts').selectpicker('val', '0');//重置数据集下拉框
};
//选择数据集，获取相应字段信息
_We_DTS.get_rule = function(view_id) {
    if (_We_M.uid == 0 || _We_M.uid == '') {
        layer.msg("请选择BI组件", {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    if ( view_id == '' || view_id == 0 ) {
        layer.msg("请选择数据集", {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    $.ajax({
        type:'get',
        url:'/webi/design/get/rule/'+view_id,
        success:function(obj){

            if( obj.code != 200 ){
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }

            _We_DTS.fields_json = obj.data;
            _We_DTS.makeField(obj.data);
        }

    });
};
//数据集列表
_We_DTS.makeDts = function(viewId,groupId){
    var group_id = groupId || 0;
    var view_id = viewId || 0;

    $("#selectBox").removeClass('hide');

    $.ajax({
        type:'get',
        url:'/webi/design/edit/dts/list',
        data:{
            view_id: view_id,
            group_id: group_id
        },
        success:function(obj){
            if( obj.code != 200 ){
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }


            $('#selectSour').selectpicker("val", obj.data.group);//重载配置
            $('#selectDts').empty();//清空数据集列表

            if( !$.isEmptyObject(obj.data.rule)){
                _We_DTS.dts_list(obj.data.rule);//生成数据集列
                if( viewId ){
                    $('#selectDts').val(view_id);//数据集
                }
            }

            //数据集下拉框插件
            $('#selectDts').selectpicker('refresh');//重载配置
            $('#selectDts').selectpicker({
                liveSearch: true,
                maxOptions: 1
            });

        }
    });

};
//生成数据集列表
_We_DTS.dts_list = function(data){
    var html =  '<option value="0">请选择数据集</option>';

    for(var k=0,len=data.length;k<len;k++){

        var temp_ul = '<option onclick="_We_DTS.get_rule('+"'"+ data[k]._id +"'"+')" value="'+ data[k]._id +'">'+data[k].view_name+'</option>';

        html += temp_ul;
    }

    $('#selectDts').append(html);

};
//展示库表/视图的字段信息
_We_DTS.makeField = function(data) {

    var html = '<ul class="field-ul">';

    for( k in data ){

        var temp_ul = '<li class="field-li">';

        temp_ul += '<span title="'+data[k].desc+'" style="margin-top:5px;overflow: hidden;">'+data[k].desc+'</span>';
        temp_ul += '<i class="fr field-i-icon glyphicon glyphicon-menu-down"></i>';
        temp_ul += '<ul  class="field-inner-ul" style="display:none;" data-tb="'+k+'">';

        for(var j=0,len=data[k]['field'].length;j<len;j++){
            temp_ul += '<li style="margin-bottom: 5px;">';
            temp_ul += '<input type="checkbox" class="square-radio redrio" name="fields" data-table="'+k+'" value="'+ data[k]['field'][j]['field_name'] +'" data-mark="'+data[k]['field'][j]['field_remark']+'" data-type="'+data[k]['field'][j]['field_type']+'"> '+data[k]['field'][j]['field_remark'];
            temp_ul += '</li>';
        }

        temp_ul += '</ul>';

        temp_ul += '</li>';

        html += temp_ul;

    }

    html += '</ul>';

    $('#field_info_inner').html(html);
    _We_G.eventBindInit();
};
//贴上数据集各个维度的字段列表
_We_DTS.show_bi_fields = function(db_json) {
    //选中数据集，获取数据集字段信息
    if( db_json['view_id'] != 0 ){
        _We_DTS.makeDts(db_json['view_id']);//数据集列表
        _We_DTS.get_rule(db_json['view_id']);
    } else {
        _We_DTS.makeDts('',0);//系统数据源
    }

    //汇总
    if (!$.isEmptyObject(db_json['sum'])) {
        _We_DTS.bi_fields(db_json['sum'].split(","), 1);
    }

    //行信息
    if (!$.isEmptyObject(db_json['row'])) {
        _We_DTS.bi_fields(db_json['row'].split(","), 2);
    }

    //列信息
    if (!$.isEmptyObject(db_json['column'])) {
        _We_DTS.bi_fields(db_json['column'].split(","), 3);
    }

    //排序信息
    if (!$.isEmptyObject(db_json['sort'])) {
        _We_DTS.bi_fields(db_json['sort'].split(","), 4);
    }

    //where条件
    if (!$.isEmptyObject(db_json['where'])) {
        _We_DTS.bi_fields(db_json['where'],5);
    }

    if ( db_json['auto_refresh'] == 1 && typeof db_json['auto_refresh'] != "undefined" ) {
        $("#refresh").val(1);
    }else{
        $("#refresh").val(0);
    }
    _We_DTS.iCheck();//自动刷新选中

};

/**
 * 展示数据集字段设置详细信息
 * @param table  表名
 * @param data 数据
 * @param type 类型：1.汇总 2.行信息 3.列信息 4.排序信息 5.where信息
 */
_We_DTS.bi_fields = function(data,type) {

    var total_html = '';

    $.each(data,function (k,v) {

        var v_a = v.split(":");
        var sort = '';
        var uid = '';
        var table = v_a[0].split(".")[0];
        var field = v_a[0].split(".")[1];

        switch(type){
            case 1:
            case 2:
            case 3:
            case 4:
                sort = v_a[2];
                break;
            case 5:
                uid = k;
                break;
        }
        var li_list_html = $('#dts_field_li_html').html();
        li_list_html = li_list_html.replace(/[\$]type/g,type);
        li_list_html = li_list_html.replace(/[\$]table/g,table);
        li_list_html = li_list_html.replace(/[\$]field/g,field);
        li_list_html = li_list_html.replace(/[\$]alias/g,v_a[1]);
        li_list_html = li_list_html.replace(/[\$]sort/g,sort);
        li_list_html = li_list_html.replace(/[\$]uid/g,uid);
        total_html += li_list_html;
    });

    $("#item-inner-"+type).find('ul').append(total_html);

    //ul中有li时，改变父级样式
    $('.ul-'+type).parent().css('padding','5px 7px');
};

//获取排除where之外的所有设置字段信息
_We_DTS.get_dts_json = function() {

    var sum_params_str = '';
    var row_params_str = '';
    var column_params_str = '';
    var sort_params_str = '';

    $.each( $('#g_dts_main').find('.sub_field') , function(k,v){

        var data_type = parseInt( $(this).attr('data-type') ); //所属分组类型

        if( data_type==5 ){ //循环到where条件时结束
            return false;
        }

        var data_table = $(this).attr('data-table'); //数据库字段原始名称
        var data_field = $(this).attr('data-field'); //数据库字段原始名称
        var data_alias = $(this).attr('data-alias'); //字段别名
        var data_sort = $(this).attr('data-sort'); //字段别名

		 //拼装结构
        var temp_params_str = ','+data_table+'.'+data_field+':'+data_alias;
        if(data_sort != ""){
             temp_params_str = ','+data_table+'.'+data_field+':'+data_alias+':'+data_sort;
        }

        switch ( data_type ){
            case 1:
                sum_params_str += temp_params_str;
                break;
            case 2:
                row_params_str += temp_params_str;
                break;
            case 3:
                column_params_str += temp_params_str;
                break;
            case 4:
                sort_params_str += temp_params_str;
                break;
        }

    } );

    WeBI.webi_dt['module'][_We_M.uid]['db_json']['auto_refresh'] = $('#refresh').val();
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['sum'] = sum_params_str.substr(1);
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['row'] = row_params_str.substr(1);
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'] = column_params_str.substr(1);
    WeBI.webi_dt['module'][_We_M.uid]['db_json']['sort'] = sort_params_str.substr(1);

    _We_CHART.fieldsCol(WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].split(","));//重新贴列数据设置字段
};
