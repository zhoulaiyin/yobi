/**
 *  属性操作对象
 *
 *  操作函数集合
 */

var _We_CHART = {};
var previous = '';//修改前的option值

//chart_json series 结构
_We_CHART.series = {
    'table': '',
    'alias': '',
    'label': '0',
    'dataLine': '0,0,0',
    'dataLineColor': '#000000'
};
_We_CHART.toolbox = {
    'dataZoom': '0',
    'dataView': '0',
    'magicType': '0',
    'restore': '0',
    'saveAsImage': '0'
};

layui.use(['form','element'], function () {
    var form = layui.form;

    //关联-select多选
    form.on('select(multi)', function (data) { // 打开控制台查看效果
        //当前操作的数据集uuid
        uuid = data.elem.dataset.id;
        var dimension_key = $('.chart-selectd option:selected').val();//当前选中option

        var demission = [];
        for (var i=0,len = (data.value).length; i<len; i++) {
            demission.push(data.value[i]);
        }

        WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].different[uuid] = demission;
    });

    //属性开启关闭
    form.on('switch(chart-detial-filter)', function(data){
        var property = $(this).attr('data-attribute').split('.');
        var parm = $(this).attr('data-parm');

        _We_CHART.switchAttr(property,parm);
    });

    /*
     *  联动设置弹层
     *  全选 / 取消全选
     * */

    //全选-同数据集
    form.on('checkbox(same_select_all)', function (data) {
        var item = $('.same-chart input[name="chart"]'); //子选项

        if (data.elem.checked) {
            item.each(function () {
                $(this).prop("checked", true);
            });
            var al_total = item.length;

            _We_CHART.chartEdit(1, null);
        } else {
            item.each(function () {
                $(this).prop("checked", false);
            });
            var al_total = 0;

            _We_CHART.chartEdit(2, null);
        }

        form.render('checkbox');

        //统计关联个数
        $('.item-container-right .same-chart-num').text(al_total);
    });

    //有一个未选中 取消全选 -- 同
    form.on('checkbox(c_one)', function (data) {
        var item = $('.same-chart input[name="chart"]');

        for (var i = 0; i < item.length; i++) {
            if (item[i].checked == false) {
                $("#select_all").prop("checked", false);
                form.render('checkbox');

                _We_CHART.chartEdit(4,data.value);
                break;
            } else {
                _We_CHART.chartEdit(3,data.value);
            }
        }

        //如果都勾选了  勾上全选
        var all = item.length;
        var al_total = 0; //选中个数
        for (var i = 0; i < item.length; i++) {
            if (item[i].checked == true) {
                all--;
                ++al_total;
            }
        }

        if (all == 0) {
            $("#same_select_all").prop("checked", true);
            form.render('checkbox');
        }

        //统计关联个数
        $('.item-container-right .same-chart-num').text(al_total);
    });

    //全选-非同数据集
    form.on('checkbox(diff_select_all)', function (data) {
        var item = $('.diff-chart input[name="diff-chart"]'); //子选项

        if (data.elem.checked) {
            item.each(function () {
                $(this).prop("checked", true);
            });

            $('.diff-chart .cube-box').fadeIn(500);//展示待关联数据集
            var al_total = item.length;

            _We_CHART.chartEdit(1, null);
        } else {
            item.each(function () {
                $(this).prop("checked", false);
            });
            $('.diff-chart .cube-box').fadeOut(500);//隐藏待关联数据集
            var al_total = 0;

            _We_CHART.chartEdit(2, null);
        }

        form.render('checkbox');

        //统计关联个数
        $('.item-container-right .diff-chart-num').text(al_total);
    });

    //有一个未选中 取消全选 -- 非同
    form.on('checkbox(d_one)', function (data) {
        var item = $('.diff-chart input[name="diff-chart"]');

        //当前组件设置的显示隐藏
        if (data.elem.checked) {
            $(this).siblings().fadeIn(500);
            _We_CHART.chartEdit(3,data.value);
        } else {
            $(this).siblings().fadeOut(500);
            _We_CHART.chartEdit(4,data.value);
        }

        //如果都勾选了  勾上全选
        var all = item.length;
        var al_total = 0; //选中个数
        for (var i = 0; i < item.length; i++) {
            if (item[i].checked == true) {
                all--;
                ++al_total;
            }
        }

        if (all == 0) {
            $("#diff_select_all").prop("checked", true);
            _We_CHART.chartEdit(1,null);
        } else {
            $("#diff_select_all").prop("checked", false);
        }

        form.render('checkbox');

        //统计关联个数
        $('.item-container-right .diff-chart-num').text(al_total);
    });

});

_We_CHART.attrKeyUp = function(ev){
    var event = ev || event;

    if( event.target.nodeName == 'SELECT') {  //select框点击
        return false;
    }

    layer.load();

    var data_attribute = event.target.getAttribute('data-attribute').split('.');
    var parm = event.target.getAttribute('data-parm');
    switch (data_attribute[0]){
        default:

            if( data_attribute[0] == 'axis' ){
                var attribute_type = $('.button-checked-bottom').attr('data-attribute');
                data_attribute[0] = attribute_type;
            }

            if( parm > 100 ){
                WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] = event.target.value;
            }else{
                //多键值保存
                var data_parm =  WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]].split(',');
                data_parm[parm] = event.target.value;
                if( parm != 0){ //修改input框 switch改为开启
                    data_parm[0] = 1;
                }
                WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] =  data_parm.join(",");
            }

            break;
    }

    _We_CHART.save();
};

F.onEvent(document.getElementById('chart-setting'),'input',_We_CHART.attrKeyUp); //输入框变更事件

$(document).on('click','#chart-button',function () { //chart属性设置模块显示
    _We_CHART.showModule();
}).on('click','#attr_chart_icon',function () { //chart属性设置模块隐藏
    _We_CHART.hideModule();
}).on('click','#chart-main .layer-chart-title',function () { //点击单一属性设置菜单,贴上数据
    var type = $(this).parent().attr('data-genre');
    _We_CHART.clearUp();//清空属性设置
    _We_CHART.showAttr(type);//展示数据
}).on('change','#series-alias',function () { //更换列

    _We_CHART.clearUp.series();

    var obj = document.getElementById("series-alias"); //定位id
    var index = obj.selectedIndex; // 选中索引
    var chart_fields = obj.options[index].getAttribute('data-field'); //选中的列字段
    var webi_dt = WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'];

    if( F.isEmptyObject(webi_dt) || F.isEmptyObject(webi_dt[chart_fields]) ) {
        return false;
    }

    if ( !F.isEmptyObject(webi_dt) ){
        _We_CHART.seriesAttr(chart_fields);
    }

}).on('click','#chart-setting .chart-aligned',function () { //DIV 对齐 打开方式
    $(this).addClass('select-div').siblings().removeClass('select-div');
    var legend = $(this).attr('data-code');
    var attribute = $(this).attr('data-attribute').split('.');

    if(_We_M.uid != ""){
        WeBI.webi_dt['module'][_We_M.uid]['chart_json'][attribute[0]][attribute[1]] = legend;
        _We_CHART.save();
    }
}).on('click','.axis-title',function () { //切换坐标轴
    $(this).siblings().children('p').removeClass('button-checked-bottom');
    $(this).children('p').addClass('button-checked-bottom');
    _We_CHART.clearUp.axis();//清空属性设置
    _We_CHART.axisAttr();//展示属性设置
}).on('click','#linkage-button',function () { //打开联动设置弹层
    _We_CHART.linkSetting();
}).on('click','.set-del',function () { //联动设置清空
    _We_CHART.linkageDel();
}).on('click','.dimen-item .item-content',function (e) { //联动-维度选中
    $(this).addClass('chart-selectd').siblings().removeClass('chart-selectd');
    _We_CHART.chartSelected();
}).on('click','.icon-add',function (e) { //联动-维度增加
    _We_CHART.dimensionAdd(e);
}).on('click','.icon-delete',function (e) { //联动-维度删除
    delete WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[$(this).siblings().find('option:selected').val()];

    $(this).closest('.item-content').remove();
    _We_CHART.dimensionDel(e);
}).on('focus', '.selected-item select', function () { //联动-维度聚焦（修改前的option）
    previous = $(this).value;
}).on('change','.selected-item select',function (e) { //联动-维度修改
    _We_CHART.dimensionChange(e);
});


//打开联动设置弹层
_We_CHART.linkSetting = function() {

    var select_title = WeBI.webi_dt['module'][_We_M.uid].attribute_json.title.value;//当前选中组件标题
    select_title = select_title != '' ? select_title : '未设置';
    var select_chart = '';

    var view_table = WeBI.webi_dt['module'][_We_M.uid].db_json.view_table;
    if (!BI.isEmptyObject(view_table)) {
        for(var v in view_table) {
            select_chart = view_table[v].desc;
        }
    }

    var title = "<h3 class='pop-title'>图表联动设置 </h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;已选择组件：<em>"
        + select_title + '</em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数据集：<em>'+ select_chart + '</em>';

    layer.open({
        title: title,
        offset: 'auto',
        type: 1,
        area: ['88%', '80%'],
        scrollbar: false,
        shade:'#000',
        shadeClose:true,
        closeBtn: 1,
        content: $('#linkage-box'),
        btn: ['确认','取消'],
        success: function() {
            //页面初始化
            _We_CHART.chartInitial();
        },
        yes: function() {
            //判断页面编辑数据完整性
           var linkage = WeBI.webi_dt.module[_We_M.uid].chart_json.linkage;
           if (!BI.isEmptyObject(linkage)) {
               for (var s in linkage) {
                   if (BI.isEmptyObject(linkage[s])) {
                       layer.msg('请选择需要关联的图表',{icon: 2, time: 2000});
                       break;
                   }

                   if (s == 'different') {
                       for (var i in linkage[s]) {
                           if (BI.isEmptyObject(linkage[s][i])) {
                               layer.msg('请选择关联的图表维度',{icon: 2, time: 2000});
                               break;
                            }
                       }
                   }
               }
           }

            //保存数据
            _We_CHART.save();
           //联动属性页面重置
            _We_CHART.clearUp.linkage();
        }
    });

};
//联动设置清空
_We_CHART.linkageDel = function () {
    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage = {};
    _We_CHART.save(); //保存数据
    _We_CHART.clearUp.linkage(); //页面重置
};
//联动-判断是否存在联动设置键，并修改联动结构
_We_CHART.linkageExist = function(){
    //不存在联动设置键
    if (BI.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid].chart_json.linkage)) {
         WeBI.webi_dt['module'][_We_M.uid].chart_json['linkage'] = {};
    }
};
//联动-维度增加   s_option:选中的option选项
_We_CHART.dimensionAdd = function(s_option) {

    //维度字段
    var row = WeBI.webi_dt['module'][_We_M.uid].db_json.row;

    if (typeof row != "undefined" || row != null || row != "") {
        var dimension = row.split(',');
        var options = '<option value="" disabled selected></option>';
        var already_op = [];

        $(".item-container-left .dimen-item select").each(function(i){
            already_op.push($(this)[0].value);
        });

        if (already_op.length == dimension.length) {
            return false;
        }

        for(var x = 0,len = dimension.length; x < len; x++){

            //维度已经存在，跳过
            if (!already_op.indexOf(dimension[x].split(':')[0])) {
                continue;
            }

            if ((s_option !=''|| s_option !=null) && dimension[x].split(':')[0] == s_option) {
                options += '<option value="'+ dimension[x].split(':')[0]+ '" selected>'+ dimension[x].split(':')[1] +'</option>';
            } else {
                options += '<option value="'+ dimension[x].split(':')[0]+ '">'+ dimension[x].split(':')[1] +'</option>';
            }

        }

        $('.item-container-left .dimen-item').append($('.chart-link-select').html());
        $(".item-container-left .selected-item select:last").append(options);

        $(".layer-linkage input[type=checkbox]").prop("checked", false);
        $('.layer-linkage input[type=checkbox]').prop('disabled',false);

        layui.use('form', function () {
            var form = layui.form;
            form.render();
        });
    }

};
//联动-维度删除
_We_CHART.dimensionDel = function(e) {

    //页面关联维度个数
   var len = $(".item-container-left .dimen-item select").size();

   if (!len) {

       $(".layer-linkage input[type=checkbox]").prop("checked", false);
       $('.layer-linkage input[type=checkbox]').prop('disabled',true);

       layui.use('form', function () {
           var form = layui.form;
           form.render();
       });

       $('.item-container-right .same-chart-num').text(0);
       $('.item-container-right .diff-chart-num').text(0);
   }
};
//联动-维度修改
_We_CHART.dimensionChange = function(e) {
    _We_CHART.linkageExist();//查询联动结构

    var selectd_demmison = e.target.value;  //获取当前选中维度

    //删除修改前的键值
    delete WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[previous];

    //初始化当前维度关联结构
    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[selectd_demmison] ={
        "same": {},
        "different": {}
    };

    //取消页面选择属性
    $(".item-container-right input").prop("checked", false);
    $(".item-container-right select").find("option").each(function() {
        $(this).removeAttr("selected");
    });

    layui.use('form', function () {
        var form = layui.form;
        form.render();
    });
};
//联动-数据集,维度初始化
_We_CHART.chartInitial = function() {

    $('.item-container-left .dimen-item').empty();//清空维度
    $('.item-container-right .layui-row').empty();//清空数据集

    var module = WeBI.webi_dt['module'];//页面所有BI组件
    var bi = WeBI.webi_dt['module'][_We_M.uid];
    var view_id = bi.db_json.view_id;//当前BI组件view_id

    var same_num = 0; //关联个数
    var diff_num = 0;
    var same_num_total = 0;//数据集总数
    var diff_num_total = 0;
    var same_chart = [];
    var diff_chart = [];

    //遍历BI-展示筛选同、非同数据集
    for (var x in module) {
        //如果为当前选中BI，跳过
        if (x == 'undefined' || x == _We_M.uid) {
            continue;
        }

        //组件标题
        var title = module[x].attribute_json.title.value;
        if (
            typeof title == "undefined"
            || title == null
            || title == ""
        ) {
            title = '未设置';
        }

        if (parseInt(module[x].db_json.view_id) == view_id) {
            var li_html = $('.chart-link-same-li').html();
            li_html = li_html.replace(/[\$]uuid/g, x);
            li_html = li_html.replace(/[\$]title/g, title);

            $('.item-container-right .same-chart').append(li_html);

            ++same_num_total;
            same_chart.push(x);
        } else {

            if (typeof module[x].db_json.view_table == 'ubdefined') {
                var view_table = '';
            } else {
                var options = '';
                var r_table = '';
                var table_name = '';
                var view_table = '';
                for(var v in module[x].db_json.view_table) {
                    view_table = module[x].db_json.view_table[v];
                    r_table = v;
                    table_name = view_table.table_name;

                    for(var f = 0,len = (view_table.field).length; f < len; f++){

                        options += ' <option value="'+ v+'.'+view_table.field[f].field_name
                                        +'" name="chart-options">'+ view_table.field[f].field_remark
                                    +'</option>';
                    }

                }
            }


            var li_html = $('.chart-link-different-li').html();
            li_html = li_html.replace(/[\$]uuid/g, x);
            li_html = li_html.replace(/[\$]title/g, title);
            li_html = li_html.replace(/[\$]charts/g, r_table);
            li_html = li_html.replace(/[\$]ds/g, table_name);

            $('.item-container-right .diff-chart').append(li_html);
            $('.item-container-right .diff-chart select:last').append(options);

            ++diff_num_total;
            diff_chart.push(x);
        }

    }

    //未设置关联属性
    if (typeof bi.chart_json.linkage == 'undefined' || BI.isEmptyObject(bi.chart_json.linkage)) {
        return false;
    }

    //左侧维度初始化
    for (var dimension_key in  bi.chart_json.linkage) {
        _We_CHART.dimensionAdd(dimension_key);
    }

    //初始化选中
    if (typeof bi.chart_json.linkage != 'undefined' && !BI.isEmptyObject(bi.chart_json.linkage)) {
        //左侧维度选中
        $(".item-container-left .item-content:first").addClass('chart-selectd');

        //右侧关联选中
        _We_CHART.chartSelected();
    }

    //可关联个数
    $('.item-container-right .same-chart-total').text(same_num_total);
    $('.item-container-right .diff-chart-total').text(diff_num_total);

    //全选按钮
    if (same_num_total && same_num == same_num_total) {
        $('#same_select_all').prop("checked", "checked");
    }

    if (diff_num_total && diff_num == diff_num_total) {
        $('#diff_select_all').prop("checked", "checked");
    }

    layui.use('form', function () {
        var form = layui.form;
        form.render();
    });
};
/**
*联动-动态修改页面操作后结构
* @param type  选择类型（1：全选 2：反选 3：单选-选择 4：单选-取消）
* @param uuid  当前选中uuid（非必传）
* */
_We_CHART.chartEdit = function(type, uuid){
    _We_CHART.linkageExist();

    var dimension_key = $('.chart-selectd option:selected').val();//当前选中option
    var tab_key = $(".layui-tab-title .layui-this").attr("lay-id"); //当前所在选中key（1:same, 2:diff）

    if (!(WeBI.webi_dt.module[_We_M.uid].chart_json.linkage).hasOwnProperty(dimension_key)) {
        WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key] = {};
    }

    switch (type) {
        case 1:
                if (BI.isEmptyObject(WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key])) {
                    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key] = {};
                }

                WeBI.webi_dt['module'][_We_M.uid].chart_json.linkage[dimension_key][tab_key] = {};

                if (tab_key == 1) {
                    var len = Object.getOwnPropertyNames(WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same).length;

                    $('.item-container-right input[name="chart"]').each(function(){
                        WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same[len] = $(this)[0].value;
                        ++len;
                    });
                } else {
                    $('.item-container-right input[name="diff-chart"]').each(function(){
                        WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].different[$(this)[0].value] = {};
                    });
                }
            break;

        case 2:
                if (tab_key == 1) {
                    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same = {};
                } else {
                    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].different = {};
                }
            break;

        case 3:

                if (tab_key == 2) {
                    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].different[uuid] = {};
                } else {
                    var same_ar = WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same;

                    var len = Object.getOwnPropertyNames(same_ar).length || 0;
                    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same[len] = uuid;
                }
            break;

        case 4:
                if (tab_key == 1) {
                    var same_ar = WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same;
                    for (var i=0,len=same_ar.length;i<len;i++) {

                        if (same_ar[i] != uuid) {
                            continue;
                        }
                         same_ar.splice(i,1);
                    }
                     WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].same = same_ar;
                } else {
                    WeBI.webi_dt.module[_We_M.uid].chart_json.linkage[dimension_key].different[uuid] = {};
                }
            break;
    }

};
//联动-右侧关联数据集选中
_We_CHART.chartSelected = function () {
    var dimension_key = $('.chart-selectd option:selected').val();//当前选中option
    var linkage = WeBI.webi_dt['module'][_We_M.uid].chart_json.linkage;//当前BI关联BI
    var same_num = 0;
    var diff_num = 0;

    //遍历BI关联的组件，进行选中操作
    if (!BI.isEmptyObject(linkage[dimension_key])) {
        for (var g in linkage[dimension_key]){

            if (g == 'undefined') {
                continue;
            }

            if (g == 'same') {
                var same_len = (linkage[dimension_key].same).length;

                //右侧数据集选中,同数据集
                for(var s = 0,len=same_len; s < len; s++) {

                    if ($('.same-chart input[value="'+ linkage[dimension_key].same[s] +'"]').length > 0) {
                        $('.same-chart input[value="'+ linkage[dimension_key].same[s] +'"]').prop("checked", "checked");

                        ++same_num;
                    }

                }

            }

            if (g == 'different') {
                //右侧数据集选中,非同数据集

                var diff_len = Object.getOwnPropertyNames(linkage[dimension_key].different).length;

                if (!diff_len) {
                    continue;
                }
                for (var d in linkage[dimension_key].different){

                    if ($('.diff-chart input[value="'+ d +'"]').length > 0) {

                        $('.diff-chart input[value="'+ d +'"]').prop("checked", "checked");
                        $('.element_'+ d).show();

                        //数据集显示,关联维度option选中
                        $('.element_'+ d).show();

                        for(var i=0,lend=(linkage[dimension_key].different[d]).length; i < lend; i++){

                            var options = linkage[dimension_key].different[d][i];
                            $('.element_'+ d + ' option[value="'+ options +'"]').prop("selected",true);

                            ++diff_num;
                        }

                    }
                }
            }

        }

    }

    $('.item-container-right .same-chart-num').text(same_num);
    $('.item-container-right .diff-chart-num').text(diff_num);
};

//显示模块
_We_CHART.showModule = function() {
    $('#chart-main').show();
};
//隐藏模块
_We_CHART.hideModule = function() {
    $('#chart-main').hide();
};
//展示属性
_We_CHART.showAttr = function(type) {

    if( F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json']) ){
        return false;
    }

    if( type == 'axis'){ //坐标轴属性
        _We_CHART.axisAttr();
        return false;
    }
    if( type == 'series' ){ //数据属性
        _We_CHART.seriesAttr();
        return false;
    }

    if( F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json'][type]) ) {
        return false;
    }

    if (type == 'linkage') {
        $('.chart_linkage_attribute button:first').addClass('set-add');
        $('.chart_linkage_attribute button:last').show();//删除按钮显示
    }

    var chart_json = {};
    chart_json = WeBI.webi_dt['module'][_We_M.uid]['chart_json'][type];

    var elems_obj = document.querySelectorAll("#chart-"+ type +" .attr-child");

    //循环处理元素赋值
    for( var i=0; i<elems_obj.length; i++ ) {
        var elemt_obj = elems_obj[i];
        var attribute = elemt_obj.getAttribute('data-attribute').split('.');

        if (!chart_json[attribute[1]]) {
            continue;
        }

        var elem_value = chart_json[attribute[1]];

        switch( elemt_obj.nodeName ){
            case 'INPUT':
            case 'SELECT':

                if( elem_value.indexOf(',') != -1 ){ //多键值参数
                    var parm = elem_value.split(',');
                    elemt_obj.value = parm[elemt_obj.getAttribute('data-parm')];
                }else {
                    elemt_obj.value = elem_value;
                }

                if( ['color','backgroundColor','borderColor'].indexOf( attribute[1] ) != -1 ){
                    elemt_obj.style.backgroundColor = elem_value;
                }

                switch( elemt_obj.type ){
                    case 'checkbox':

                        if( elem_value.indexOf(',') != -1 ){ //多键值参数
                            var parm = elem_value.split(',');
                            elem_value = parm[0];
                        }

                        if( elem_value == 1){
                            elemt_obj.checked =  true;
                        }

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                elemt_obj.setAttribute('src',elem_value);
                break;
            case 'DIV':

                var cur_target_val = elemt_obj.getAttribute('data-code');
                F.toggleClass(elemt_obj,'select-div',false);
                if( cur_target_val == elem_value ){
                    F.toggleClass(elemt_obj,'select-div',true);
                }
                break;
        }
    }
};
//数据属性展示
_We_CHART.seriesAttr = function(chartFields){
    var chart_fields = chartFields || 0;

    //贴上列字段
    if (!$.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'])) {
        _We_CHART.fieldsCol(WeBI.webi_dt['module'][_We_M.uid]['db_json']['column'].split(","));
    }

    //不存在列数据设置
    if( F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series']) ){
        return false;
    }

    if( chart_fields ) {

        var chart_json = WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields];

        //更改选中
        $("#series-alias").find("option[value="+ chart_fields +"]").attr("selected",true);

    } else {

        var obj = document.getElementById("series-alias"); //定位id
        var index = obj.selectedIndex; // 选中索引
        chart_fields = obj.options[index].getAttribute('data-field'); //选中的列字段

        var chart_json = WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields];
    }

    var elems_obj = document.querySelectorAll("#chart_parents_series  .attr-child");

    //循环处理元素赋值
    for( var i=0; i<elems_obj.length; i++ ) {
        var elemt_obj = elems_obj[i];
        var attribute = elemt_obj.getAttribute('data-attribute').split('.');
        var elem_value = chart_json[attribute[1]];

        if( typeof chart_json  == 'undefined' || typeof chart_json[attribute[1]] == 'undefined' ){
            continue;
        }

        switch( elemt_obj.nodeName ){
            case 'INPUT':
            case 'SELECT':

                if( elem_value.indexOf(',') != -1 ){ //多键值参数
                    var parm = elem_value.split(',');
                    elemt_obj.value = parm[elemt_obj.getAttribute('data-parm')];
                }else {
                    elemt_obj.value = elem_value;
                }

                if( ['color','backgroundColor','dataLineColor'].indexOf( attribute[1] ) != -1 ){
                    elemt_obj.style.backgroundColor = elem_value;
                }

                switch( elemt_obj.type ){
                    case 'checkbox':

                        if( elem_value.indexOf(',') != -1 ){ //多键值参数
                            var parm = elem_value.split(',');
                            elem_value = parm[elemt_obj.getAttribute('data-parm')];
                        }

                        if( elem_value == 1){
                            elemt_obj.checked = true;
                        }

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                elemt_obj.setAttribute('src',elem_value);
                break;
            case 'DIV':

                var cur_target_val = elemt_obj.getAttribute('data-code');
                if( cur_target_val == elem_value ){
                    F.toggleClass(elemt_obj,'select-div',true);
                }
                break;
        }
    }

};
//数据属性列信息
_We_CHART.fieldsCol = function (data) {

    $("#chart_parents_series #series-alias").empty();

    var option_html = '';
    $.each(data,function (k,v) {

        var v_a = v.split(":");
        var table = v_a[0].split(".")[0];
        var field = v_a[0].split(".")[1];

        option_html += '<option value="'+ field +'" data-table="'+ table +'" data-remarks="'+ v_a[1] +'" data-field="'+ field +'"> '+ v_a[1] +'</option>';
    });

    $("#chart_parents_series #series-alias").append(option_html);

};
//坐标轴属性展示
_We_CHART.axisAttr = function(){
    var attribute_type = $('.button-checked-bottom').attr('data-attribute');

    if( F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json'][attribute_type]) ){
        return false;
    }

    var chart_json = {};
    chart_json = WeBI.webi_dt['module'][_We_M.uid]['chart_json'][attribute_type];

    var elems_obj = document.querySelectorAll("#chart-axis .attr-child");
    //循环处理元素赋值
    for( var i=0; i<elems_obj.length; i++ ) {
        var elemt_obj = elems_obj[i];
        var attribute = elemt_obj.getAttribute('data-attribute').split('.');

        if( typeof chart_json[attribute[1]] == 'undefined' ){
            continue;
        }

        var elem_value = chart_json[attribute[1]];
        switch( elemt_obj.nodeName ){
            case 'INPUT':
            case 'SELECT':

                if( ['color','backgroundColor'].indexOf( attribute[1] ) != -1 ){
                    elemt_obj.style.backgroundColor = elem_value;
                }

                if( elem_value.indexOf(',') != -1 ){ //多键值参数
                    var parm = elem_value.split(',');
                    elemt_obj.value = parm[elemt_obj.getAttribute('data-parm')];

                    elem_value = parm[0];//是否缩放
                }else {
                    elemt_obj.value = elem_value;
                }

                if( elemt_obj.type == 'checkbox'){

                    if( elem_value == 1){
                        elemt_obj.checked =  true;
                    }

                    layui.use(['form'], function () {
                        var form = layui.form;
                        form.render('checkbox');
                    });
                }//存在switch按钮

                break;
            case 'IMG':

                elemt_obj.setAttribute('src',elem_value);
                break;
        }
    }

};
//属性重置
_We_CHART.clearUp = function() {
    _We_CHART.clearUp.series();
    _We_CHART.clearUp.legend();
    _We_CHART.clearUp.title();
    _We_CHART.clearUp.axis();
    _We_CHART.clearUp.tooltip();
    _We_CHART.clearUp.toolbox();
    _We_CHART.clearUp.linkage();
};
//数据属性重置
_We_CHART.clearUp.series = function(){
    var lis = document.getElementById('chart_parents_series').querySelectorAll('.attr-child');

    for (var i = 0; i < lis.length; i++) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');

        switch (ele_type.nodeName) {

            case 'INPUT':
                ele_type.value = '';

                if( ['color','backgroundColor','dataLineColor'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }

                switch( ele_type.type ){
                    case 'checkbox':

                        ele_type.checked =  false;

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;


            case 'IMG':
                ele_type.setAttribute('src','');
                break;

            case 'DIV':
                break;

            case 'SELECT':
                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//图例属性重置
_We_CHART.clearUp.legend = function(){

    if(!document.getElementById('chart_parents_legend')){
        return false;
    }
    var lis = document.getElementById('chart_parents_legend').querySelectorAll('.attr-child');

    $('#parents_legend input').val("");

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');

        switch (ele_type.nodeName) {

            case 'DIV':

                var cur_target_val = ele_type.getAttribute('data-default');
                var before_target_val = ele_type.getAttribute('data-code');
                F.toggleClass(ele_type,'select-div',false);//移除之前选中的
                if( cur_target_val == before_target_val ){
                    F.toggleClass(ele_type,'select-div',true);
                }
                break;
            case 'INPUT':

                ele_type.value = '';
                if( ['color','backgroundColor'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }

                switch( ele_type.type ){
                    case 'checkbox':

                        ele_type.checked =  false;
                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                ele_type.setAttribute('src','');
                break;
            case 'SELECT':

                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//标题属性重置
_We_CHART.clearUp.title = function(){
    if(!document.getElementById('chart_parents_title')){
        return false;
    }
    var lis = document.getElementById('chart_parents_title').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');

        switch (ele_type.nodeName) {

            case 'DIV':

                var cur_target_val = ele_type.getAttribute('data-default');
                var before_target_val = ele_type.getAttribute('data-code');
                F.toggleClass(ele_type,'select-div',false);//移除之前选中的
                if( cur_target_val == before_target_val ){
                    F.toggleClass(ele_type,'select-div',true);
                }
                break;
            case 'INPUT':

                ele_type.value = '';
                if( ['color','backgroundColor'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }

                switch( ele_type.type ){
                    case 'checkbox':

                        ele_type.checked =  false;

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                ele_type.setAttribute('src','');
                break;
            case 'SELECT':

                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//坐标轴属性重置
_We_CHART.clearUp.axis = function(){
    if(!document.getElementById('chart_parents_Axis')){
        return false;
    }
    var lis = document.getElementById('chart_parents_Axis').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');

        switch (ele_type.nodeName) {

            case 'DIV':

                var cur_target_val = ele_type.getAttribute('data-default');
                var before_target_val = ele_type.getAttribute('data-code');
                F.toggleClass(ele_type,'select-div',false);//移除之前选中的
                if( cur_target_val == before_target_val ){
                    F.toggleClass(ele_type,'select-div',true);
                }
                break;
            case 'INPUT':

                ele_type.value = '';
                if( ['color','backgroundColor'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }

                switch( ele_type.type ){
                    case 'checkbox':
                        ele_type.checked =  false;

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                ele_type.setAttribute('src','');
                break;
            case 'SELECT':

                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//提示属性重置
_We_CHART.clearUp.tooltip = function(){
    if(!document.getElementById('chart_parents_tooltip')){
        return false;
    }
    var lis = document.getElementById('chart_parents_tooltip').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');

        switch (ele_type.nodeName) {

            case 'DIV':

                var cur_target_val = ele_type.getAttribute('data-default');
                var before_target_val = ele_type.getAttribute('data-code');
                F.toggleClass(ele_type,'select-div',false);//移除之前选中的
                if( cur_target_val == before_target_val ){
                    F.toggleClass(ele_type,'select-div',true);
                }
                break;
            case 'INPUT':

                ele_type.value = '';
                if( ['color','backgroundColor'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }

                switch( ele_type.type ){
                    case 'checkbox':

                        ele_type.checked =  false;

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                ele_type.setAttribute('src','');
                break;
            case 'SELECT':

                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//工具属性重置
_We_CHART.clearUp.toolbox = function(){
    if(!document.getElementById('chart_parents_toolbox')){
        return false;
    }
    var lis = document.getElementById('chart_parents_toolbox').querySelectorAll('.attr-child');

    for( var i=0; i<lis.length; i++ ) {

        var ele_type = lis[i];
        var attribute = ele_type.getAttribute('data-attribute').split('.');

        switch (ele_type.nodeName) {

            case 'DIV':

                var cur_target_val = ele_type.getAttribute('data-default');
                var before_target_val = ele_type.getAttribute('data-code');
                F.toggleClass(ele_type,'select-div',false);//移除之前选中的
                if( cur_target_val == before_target_val ){
                    F.toggleClass(ele_type,'select-div',true);
                }
                break;
            case 'INPUT':

                ele_type.value = '';
                if( ['color','backgroundColor','dataLineColor'].indexOf( attribute[1] ) != -1 ){
                    ele_type.style.backgroundColor = '#fff';
                }

                switch( ele_type.type ){
                    case 'checkbox':

                        ele_type.checked =  false;

                        layui.use(['form'], function () {
                            var form = layui.form;
                            form.render('checkbox');
                        });
                        break;
                }
                break;
            case 'IMG':

                ele_type.setAttribute('src','');
                break;
            case 'SELECT':

                var cur_target_val = ele_type.getAttribute('data-default');
                ele_type.value = cur_target_val;
                break;
        }
    }
};
//联动属性重置
_We_CHART.clearUp.linkage = function(){
    if (_We_M.uid == "" || !BI.isEmptyObject(WeBI.webi_dt.module[_We_M.uid].chart_json.linkage)) {
       return false;
    }

    $('.chart_linkage_attribute button:first').removeClass('set-add');
    $('.chart_linkage_attribute button:last').hide();//删除按钮隐藏
};

//属性数据保存
_We_CHART.save = function() {

    //组装保存数据结构
    var save_dt = {};
    save_dt.bi_id = _We_G.bi_id;
    save_dt.uid = _We_M.uid;
    save_dt.data = [];

    save_dt.data = JSON.stringify(WeBI.webi_dt.module[_We_M.uid].chart_json);

    E.ajax({
        type: 'post',
        url: _V.chart_url[1],
        dataType:'json',
        data:save_dt,
        success:function (obj) {
            layer.closeAll();

            if (obj.code != 200) {
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }
            _We_M.show_bi(_We_M.uid);
        }
    });
};
//switch开关
_We_CHART.switchAttr = function(data_attribute,parm){

    switch (data_attribute[0]){

        case 'series':
                    var obj = document.getElementById("series-alias"); //定位id
                    var index = obj.selectedIndex; // 选中索引
                    var chart_fields = obj.options[index].getAttribute('data-field'); //选中的列字段

                    if ( F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series']) ){//不存在 series
                        WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'] = {};
                    }

                    if ( F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields]) ){//不存在此列
                        WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields] = F.deepClone( _We_CHART.series);

                        var table = obj.options[index].getAttribute('data-table');//表名
                        var alias = obj.options[index].getAttribute('data-remarks');//表备注

                        WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields].table = table;
                        WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields].alias = alias;
                    }

                    if( parm > 100 ){
                        var onswitch =  WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields][data_attribute[1]];
                        WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields][data_attribute[1]] = parseInt(onswitch) ? "0" : "1";
                    }else{
                        var data_parm =  WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields][data_attribute[1]];
                             data_parm = data_parm.split(',');

                        data_parm[parm] =  parseInt(data_parm[parm]) ? 0 : 1;
                        WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields][data_attribute[1]] =  data_parm.join(",");

                    }

            break;

        default:

            if( data_attribute[0] == 'axis'){
                var attribute_type = $('.button-checked-bottom').attr('data-attribute');
                data_attribute[0] = attribute_type;
            }
            switch ( data_attribute[0] ){

                case 'axis':
                        var attribute_type = $('.button-checked-bottom').attr('data-attribute');
                        data_attribute[0] = attribute_type;
                    break;
                case 'toolbox':
                    WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]] = F.deepClone(_We_CHART.toolbox);
                    break;
            }

            if( parm > 100 ){

                var onswitch = WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]];//获取原本的设置
                WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] = parseInt(onswitch) ? 0 : 1;
            }else{
                //多键值保存
                var data_parm =  WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]].split(',');
                data_parm[parm] =  parseInt(data_parm[parm]) ? 0 : 1;
                WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] =  data_parm.join(",");
            }

    }

    _We_CHART.save();
};
//修改颜色
_We_CHART.color_change = function(obj) {
    var data_attribute = obj.getAttribute('data-attribute').split('.');
    var dom_id = obj.getAttribute('id');
    var color = $('#'+dom_id).val();

    switch (data_attribute[0]) {
        case 'series':

            var chart_fields = document.getElementById("series-alias").value;//选中的列字段
            var webi_dt = WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'];

            if ( F.isEmptyObject(webi_dt) ) {//不存在此列
                WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'] = {};
                WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields] = F.deepClone(_We_CHART.series);
            }

            if(  F.isEmptyObject(WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields]) ){
                WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields] = F.deepClone(_We_CHART.series);
            }

            if( obj.getAttribute('data-parm') > 100 ){
                WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields]['dataLineColor'] = color;
            }else{
                var data_parm =  WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields]['dataLineColor'].split(',');
                data_parm[obj.getAttribute('data-parm')] =  color;

                WeBI.webi_dt['module'][_We_M.uid]['chart_json']['series'][chart_fields]['dataLineColor'] =  data_parm.join(",");
            }
            break;
        default:

            if ( data_attribute[0] == 'axis'){
                var attribute_type = $('.button-checked-bottom').attr('data-attribute');
                data_attribute[0] = attribute_type;
            }

            if( obj.getAttribute('data-parm') > 100 ){
                WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] = color;
            }else{
                //多键值保存
                var data_parm =  WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]].split(',');
                data_parm[obj.getAttribute('data-parm')] = color;
                WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] =  data_parm.join(",");
            }
            break;
    }
    _We_CHART.save();
};
//清空颜色
_We_CHART.clear_color = function(obj) {
    var data_attribute = obj.getAttribute('data-attribute').split('.');

    switch (data_attribute[0]){
        case 'axis':

            var attribute_type = $('.button-checked-bottom').attr('data-attribute');
            data_attribute[0] = attribute_type;
            break;
    }
    if( obj.getAttribute('data-parm') > 100 ){
        WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] = '';
    }else{
        //多键值保存
        var data_parm =  WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]].split(',');
        data_parm[event.target.getAttribute('data-parm')] = '';
        WeBI.webi_dt['module'][_We_M.uid]['chart_json'][data_attribute[0]][data_attribute[1]] =  data_parm.join(",");
    }

    _We_CHART.save();
};