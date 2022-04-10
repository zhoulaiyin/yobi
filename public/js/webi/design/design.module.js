    /**
     *  报表主体模块对象
     *
     *  操作函数集合
     */

    var _We_M = {};
    _We_M.uid = ''; //当前操作的报表uid
    _We_M.replace_uid = ''; //当前需要做替换操作的报表uid
    _We_M.refreshObj = {
        'module':{}
    }; //记录刷新对象
    _We_M.refreshObj.interval = {};//加入自动刷新的对象

    //module初始化结构
    _We_M.module_structure = {
        "bi_json":{
            'type': "",
            'chart_json' : '',
            'chart_id': ""
        },
        "db_json":{
            'view_id':0,
            'view_table': '',
            'auto_refresh' : '0',
            'row' : '',
            'column' : '',
            'sum' : '',
            'limit': 20,
            'sort' : '',
            'where': ""
        },
        "attribute_json":{
            "general":{
                'title': "",
                'backgroundColor' : '#FFFFFF',
                'backgroundImage': "",
                'border': "1" ,
                'border_image_slice': "0",
                'border_image_repeat': "repeat",
                'border_image_source': "",
                'refresh_frequency': "1",
                'fontFamily': "微软雅黑" ,
                'fontSize': "14",
                'color': "#000000",
                'fontStyle': "normal",
                'fontWeight': "normal",
                'rim': {
                    'top':'0',
                    'right':'0',
                    'bottom':'0',
                    'left':'0'
                }
            },
            "series":{},
            "text":{
                "content":{
                    'fontFamily': "微软雅黑" ,
                    'fontSize': "14",
                    'color': "#000000",
                    'fontStyle': "normal",
                    'fontWeight': "normal"
                },
                "prefix":{
                    "value": "",
                    'fontFamily': "微软雅黑" ,
                    'fontSize': "14",
                    'color': "#000000",
                    'fontStyle': "normal",
                    'fontWeight': "normal"
                },
                "suffix":{
                    "value": "",
                    'fontFamily': "微软雅黑" ,
                    'fontSize': "14",
                    'color': "#000000",
                    'fontStyle': "normal",
                    'fontWeight': "normal"
                }
            },
            "legend":{
                'switch': "1",
                'position': "center"
            },
            "table":{
                'height': "",
                'color' : '#000000',
                'fontFamily': "微软雅黑",
                'fontSize':"14",
                'fontStyle':"normal",
                'fontWeight': "normal" ,
                'backgroundColor': "#FFFFFF",
                'even_color': "",
                'td_width':"",
                'borderStyle':'solid',
                'borderColor': '#dddddd',
                'borderWidth': '1',
                'legend':''
            },
            "title":{
                'switch': "1",
                'value': "",
                'link' : '',
                'target': "_blank",
                'fontFamily': "微软雅黑" ,
                'fontSize': "14",
                'color': "#000000",
                'fontStyle': "normal",
                'fontWeight': "normal",
                'backgroundColor': "#FFFFFF",
                'legend': "left",
                'rim': {
                    'top':'0',
                    'right':'0',
                    'bottom':'0',
                    'left':'0'
                }
            },
            'height_percent' : "",
            'top_percent' : "",
            'height' : '200',
            'width' : '200',
            'top' : "",
            'width_percent' : '0.98',
            'left_percent' : '0.01'
        },
        'chart_json' : {
            "series": {},
            "legend": {
                "switch": "1",
                "x": "left",
                "y": "top"
            },
            "title": {
                "switch": "1",
                "mainTitle": "",
                "subTitle": "",
                "link": "",
                "target": "_blank",
                "fontFamily": "微软雅黑",
                "fontSize": "14",
                "color": "#000000",
                "fontStyle": "normal",
                "fontWeight": "normal",
                "backgroundColor": "#FFFFFF",
                "x": "left",
                "y": "top"
            },
            "xAxis": {
                "switch": "1",
                "text": "",
                "fontFamily": "微软雅黑",
                "fontSize": "14",
                "color": "#000000",
                "fontStyle": "normal",
                "fontWeight": "normal",
                "scale": "0,0,100"
            },
            "yAxis": {
                "switch": "1",
                "text": "",
                "fontFamily": "微软雅黑",
                "fontSize": "14",
                "color": "#000000",
                "fontStyle": "normal",
                "fontWeight": "normal",
                "scale": "0,0,100"
            },
            "tooltip": {
                "switch": "0",
                "backgroundColor": "0,#000000",
                "borderWidth": "0,1",
                "borderColor": "0,#000000",
                "fontFamily": "微软雅黑",
                "fontSize": "14",
                "color": "#FFFFFF",
                "fontWeight": "normal"
            },
            "toolbox":{
                "dataZoom": "0",
                "dataView": "0",
                "magicType": "0",
                "restore": "0",
                "saveAsImage": "0"
            },
            "linkage": {

            }
        }
    };

    $(document).on('click','.top-grid',function(){ //子模板选中效果
        _We_G.save_type = 2;
        _We_M.uid = $(this).attr('id').substring(5);

        _We_ATTR.clearUp();//清空所有属性设置
        _We_M.get_bi(_We_M.uid);
        $('.side-show').click();//左侧属性栏展开
        _We_M.right_attribute();//右侧属性栏模块显示隐藏
        _We_CHART.hideModule();//隐藏展开的图表属性

        //给正在展开的设置贴数据
        $.each($('.layui-colla-item'),function(){
            if($(this).children().hasClass('layui-show')){
                var type =$(this).data('genre');
                _We_ATTR.showAttr(type);
            }
        });

    }).on('mouseenter','.list-oper',function () { //鼠标滑过单一报表
        $(this).find('.chart-operation').css('opacity','1');
    }).on('mouseleave','.list-oper',function () { //鼠标离开单一报表
        $('.chart-operation').css('opacity','0');
    }).on('mouseleave','.list-oper .oper-box',function () { //鼠标离开单一报表操作列表
        $(this).hide();
    }).on('click','.chart-operation',function () { //单一报表操作列表
        if( $(this).next('.oper-box').css('display') == 'none' ){
            $(this).next('.oper-box').fadeIn(500);
        } else {
            $(this).next('.oper-box').fadeOut(500);
        }
    }).on('click','.chart-delete',function () { //报表删除
        _We_M.delete_bi( $(this).attr('data-id') );
    }).on('click','.chart-copy',function () { //报表复制
        _We_M.copy_bi( $(this).attr('data-id') );
    }).on('click','.chart-replace',function () { //报表更换
        _We_M.replace_bi( $(this).attr('data-id') );
    });

//新增一个BI模块
_We_M.addBI = function(chart_id) {
    var module_structure = F.deepClone(_We_M.module_structure);

    var top = 0;
    var top_percent = 0;
    var mainHeight = document.getElementById(WeBI.p_id).style.height;
    var mainWidth = document.getElementById(WeBI.p_id).style.width;
    var main_height = mainHeight.substring(0,mainHeight.length-2);
    var height_percent = (200 / main_height).toFixed(4);

    if ( !$.isEmptyObject(WeBI.bi_content) ) {

        var temp_top = WeBI.op.sort_module[WeBI.op.sort_module.length-1];

        $.each(WeBI.bi_content,function (k,v) {
            var temp_top_percent = parseFloat(v.top_percent) + parseFloat(v.height_percent) + 0.0005;
            if ( temp_top_percent >= top_percent ) {
                top = parseInt(temp_top.top) + parseInt(temp_top.height) + 15;
                top_percent = temp_top_percent;
            }
        });
    }

    //替换初始化结构中的参数
    module_structure.bi_json.chart_id = chart_id;
    module_structure.attribute_json.top = top;
    module_structure.attribute_json.top_percent = top_percent;
    module_structure.attribute_json.height_percent = height_percent;
    module_structure.attribute_json.width = mainWidth * module_structure.attribute_json.width_percent;

    $.ajax({
        type:'post',
        url: _V.module_url[1],
        data:{
            chart_id: chart_id,
            bi_id: _We_G.bi_id,
            modul_data: JSON.stringify(module_structure),
            callback_fun:'_We_M.show_bi'
        },
        success:function(obj){

            if( obj.code != 200 ){
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }
            //修改json
            module_structure.bi_json = obj.bi_json;
            module_structure.db_json = obj.db_json;

            //存储一个BI模块
            WeBI.webi_dt['module'][obj.uid] = module_structure;

            //设置当前新增模块为编辑对象
            _We_M.uid = obj.uid;

            if(obj.callback_fun != ""){
                eval(obj.callback_fun + "('"+obj.uid +"',0)");
            }

            _We_G.calContentHeight();
        }

    });
};
//复制BI
_We_M.copy_bi = function(uid) {
    if ( !uid ) {
        layer.msg("请选择要复制的BI组件", {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    $(".oper-box").hide();

    layer.confirm('您确认要复制该BI组件吗？',{icon: 3,offset:"70px"}, function (index) {
        layer.close(index);

        var top = 0;
        var height = 200;
        var width = 200;
        var top_percent = 0;
        var mainHeight = document.getElementById(WeBI.p_id).style.height;
        var main_height = mainHeight.substring(0,mainHeight.length-2);
        var height_percent = (200 / main_height).toFixed(4);

        if ( !$.isEmptyObject(WeBI.bi_content) ) {

            var temp_top = WeBI.op.sort_module[WeBI.op.sort_module.length-1];

            $.each(WeBI.bi_content,function (k,v) {
                var temp_top_percent = parseFloat(v.top_percent) + parseFloat(v.height_percent) + 0.0005;
                if ( temp_top_percent >= top_percent ) {
                    top = parseInt(temp_top.top) + parseInt(temp_top.height) + 15;
                    top_percent = temp_top_percent;
                }
            });
        }

        $.ajax({
            type:'get',
            url: _V.module_url[2],
            data:{
                uid:uid,
                top:top,
                top_percent:top_percent,
                height_percent:height_percent,
                callback_fun:'_We_M.show_bi'
            },
            success:function (obj) {
                if( obj.code != 200 ){
                    layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                    return false;
                }

                WeBI.webi_dt['module'][obj.uid] = obj.module;
                if(obj.callback_fun != ""){
                    eval(obj.callback_fun + "('"+obj.uid +"',0)");
                }

                $('#move_'+obj.uid).find('.oper-box').fadeOut(500);
                _We_G.calContentHeight();
            }
        });
    });
};
//更换BI
_We_M.replace_bi = function (uid) {
    _We_M.replace_uid = uid;
    _We_G.show_choose_bi();
};
//执行更换BI操作
_We_M.exec_replace_bi = function (chart_id) {
    var layer_index = layer.load();
    $.ajax({
        type:'post',
        url: _V.module_url[3],
        data:{
            chart_id:chart_id,
            uid:_We_M.replace_uid
        },
        success:function(obj){
            layer.close(layer_index);
            if( obj.code != 200 ){
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }

            $('#move_'+_We_M.replace_uid).find('.oper-box').fadeOut(500);
            WeBI.webi_dt['module'][_We_M.replace_uid]['bi_json'] = obj.data.bi_json; //更新结构
            WeBI.webi_dt['module'][_We_M.replace_uid]['attribute_json'] = obj.data.attribute_json; //更新结构

            //数据集被更新
            if(!$.isEmptyObject(obj.data.db_json) ){
                _We_DTS.clearUp();//清空所有属性设置
                _We_DTS.show_bi_fields(obj.data.db_json);//设置数据
                WeBI.webi_dt['module'][_We_M.replace_uid]['db_json'] = obj.data.db_json; //更新结构
            }

             $('#'+ WeBI.p_id).empty();//清空页面
             WeBI.op.a(WeBI.p_id, WeBI.webi_dt, $('#bi_content_html').html(),1);//重新加载页面数据

            _We_M.right_attribute();//右侧属性栏显示、隐藏
            _We_ATTR.clearUp();//重置右侧属性设置

            //清空编辑项
            _We_M.replace_uid = '';
        }

    });
};
//删除BI
_We_M.delete_bi = function(uid) {

    if( !uid ){
        layer.msg('请选择要删除的BI组件', {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    $(".oper-box").hide();

    layer.confirm('您确认要删除该BI组件吗？',{icon: 3,offset:"70px"}, function (index) {
        layer.close(index);
        E.ajax({
            type: 'get',
            url: _V.module_url[4] + "" + uid + "?callback_fun=WeBI.op.del",
            success: function (res) {
                if( res.code != 200 ){
                    layer.msg(res.message, {icon: 2, offset: '70px', time: 1500});
                    return false;
                }

                if( _We_M.uid == uid ){ //当前编辑的报表被删除
                    _We_G.save_type = '';
                    _We_M.uid = '';

                    _We_CHART.clearUp();//清空属性设置
                    _We_ATTR.clearUp();//清空属性设置
                    _We_DTS.clearUp();//清空数据集设置
                    _We_DTS.select.hide();//隐藏数据集设置
                }

                $("#grid_"+uid).remove();
                if(res.callback != ""){
                    eval(res.callback + "('"+uid +"')");
                }
            }
        });
    });
};
//保存的中转路由
_We_M.save_routes = function(flg,uid) {

    var index  = layer.load() ;

    switch (flg){

        case 1: //保存BI主体信息
            var save_url = _V.module_url[5];
            var save_data = WeBI.webi_dt['module'][uid]['attribute_json'];
            break;
        case 2: //保存BI数据集信息
            var save_url = _V.module_url[6];
            var save_data = WeBI.webi_dt['module'][_We_M.uid]['db_json'];
            break;
    }

    var save_uid = uid || _We_M.uid;

    $.ajax({
        type:'post',
        url:save_url,
        data:{
            uid:save_uid,
            data:save_data
        },
        success:function(obj){

            layer.close(index);//关闭等待层

            if( obj.code != 200 ){
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }
            _We_M.show_bi(save_uid);
        }

    });

};
//BI属性数据保存
_We_M.save_attr = function(uid) {
    _We_M.save_routes(1,uid);
};
//BI数据集数据保存
_We_M.save_dts = function() {
    _We_DTS.get_dts_json();
    _We_M.save_routes(2);
};
//获取报表信息
_We_M.get_bi = function(uid) {

    _We_DTS.clearUp(); //清空数据集设置信息
    _We_ATTR.clearUp(); //清空属性设置信息

    if ( !uid ) {
        layer.msg('请选择要操作的BI组件', {icon: 2, offset: '70px', time: 1500});
        return false;
    }

    $.ajax({
        type:'get',
        url:_V.module_url[7] + "" + uid + "?callback_fun=_We_M.showBIConfig",
        success:function (obj) {
            if( obj.code != 200 ){
                layer.msg(obj.message, {icon: 2, offset: '70px', time: 1500});
                return false;
            }

            if(obj.data.callback_fun != ""){
                eval(obj.data.callback_fun + "("+JSON.stringify(obj.data)+")");
            }

        }
    })

};
//展示BI设置信息
_We_M.showBIConfig = function(data) {
    //显示数据集设置
    _We_DTS.show_bi_fields(data['db_json']);
};
//BI模块信息的展示
_We_M.show_bi = function(uid,flg) {

    var handel_uid = uid || _We_M.uid;

    if( typeof flg == 'undefined' ){
        var create_flg = 1;
    } else {
        var create_flg = flg;
    }

    if(create_flg == 1){
        $("#grid_"+handel_uid).remove();
    }

    WeBI.op.create_module(handel_uid, WeBI.webi_dt.module[handel_uid]); //创建OR更新一个BI模块
    if(flg == 0){
        WeBI.op.create_sort_obj();//按坐标生成排序对象
    }

    if(create_flg == 0){
        $("#touch_"+handel_uid).click();
    }

    if(WeBI.webi_dt['module'][handel_uid]['db_json']['auto_refresh'] == 1){
        $("#refresh").val(1);
        _We_DTS.iCheck();//自动刷新选中
    }
    _We_M.right_attribute();

};
//BI模块宽度自适应
_We_M.BISelfSuite = function() {
    if( JSON.stringify(WeBI.bi_content) != '{}' && JSON.stringify(WeBI.bi_content) != '[]' && WeBI.bi_content != '' ){
        WeBI.op.resize();
    }
};
//BI模块自动刷新
_We_M.auto_refresh = function(){

    //存在组件刷新
    if (JSON.stringify(_We_M.refreshObj.module) != '{}') {

        for (var uid in _We_M.refreshObj.module) {

            //BI组价未设置刷新频率  使用全局频率
            if( $.isEmptyObject( _We_M.refreshObj.module[uid] ) || _We_M.refreshObj.module[uid] <= 0 ){
               return true;
            }

            _We_M.refreshObj.interval[uid] =
                setInterval("_We_M.show_bi('" + uid + "')", parseInt(_We_M.refreshObj.module[uid]) * 1000 * 60);//局部自动刷新BI
        }

    }

};
//保存module位置信息
_We_M.save_position = function(data){
    var index  = layer.load() ;
    $.ajax({
        type:'post',
        url: _V.module_url[8],
        data:{
            module: data
        },
        success:function(obj){
            layer.close(index);//关闭等待层
        }
    })

};
//右侧属性栏显示及隐藏的设置
_We_M.right_attribute = function(){

    if( _We_M.uid == '' ) {
        return false;
    }

    var webi_type = WeBI.webi_dt['module'][_We_M.uid]['bi_json'].type;

    $('.theme_attribute').hide();
    $('#parents_general .sole-input').css('display','block');//边框设置显示
    $('.general_attribute').nextAll().css('display','block');
    $('.general-title').css('display','none');//常规  标题设置隐藏
    $('.general-font-way').css('display','block');//显示字体设置

    if(webi_type == 'bi_text' || webi_type == 'bi_table'){//文本类型、表格 图例/图表属性设置隐藏
        $('.chart_attribute').css('display','none');
    }
    if(['wordCloud', 'bi_table'].indexOf(webi_type) != -1) { //词云图 隐藏序列设置
        $('.series_attribute').css('display','none');
    }
    if( webi_type != "bi_table" ){  //非表格  隐藏表格属性设置
        $('.table_attribute').css('display','none');
    }
    if( webi_type != "bi_text" ){  //非文本  隐藏文本属性设置
        $('.text_attribute').css('display','none');
    }

    //单一设置开关是否开启
    if(WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['legend']['switch'] == 1){
        $('#legend .layui-unselect').addClass('layui-form-onswitch');
    }else{
        $('#legend .layui-unselect').removeClass('layui-form-onswitch');
    }
    if(WeBI.webi_dt['module'][_We_M.uid]['attribute_json']['title']['switch'] == 1){
        $('#title .layui-unselect').addClass('layui-form-onswitch');
    }else{
        $('#title .layui-unselect').removeClass('layui-form-onswitch');
    }

    $("#grid_"+ _We_M.uid).addClass('grid-selected').siblings().removeClass('grid-selected'); //设置选中效果

};