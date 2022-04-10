(function(){

    var _G_BI = {};//终端操作对象

    /*
     * 获取设备redis数据
     */
    _G_BI.detection = function() {
        var equipment_code = _G_BI.getMEI();//获取设备编码
        $.ajax({
            type: 'get',
            url: '/webi/tv/group/bi/module/get?equipment_code=' + equipment_code,
            dataType: 'JSON',
            data: {},
            timeout: 60000,
            success: function (o) {
                if ( !$.isEmptyObject(o.data) ) {
                    if(o.data[0] == 1){//全局报表
                        self.location ='/webi/wap/show?uid='+ o.data[1];
                    }else if(o.data[0] == 2){//单一报表
                        self.location = '/webi/tv/show/' + o.data[1];
                    }
                }
            }
        });
    };

    /*
     * 报表点击事件
     */
    _G_BI.touch = function(){
        $.each($('.shop-grid'), function (k, v) {
            $(this).on('click', function () {
                var uid = $(this).attr('id').substring(5);
                var equipment_code = _G_BI.getCookie('machine_num');//获取设备编码
                $.ajax({
                    type: 'get',
                    url: '/webi/tv/group/bi/module/get?uid=' + uid + '&equipment_code=' + equipment_code +'&iType=2',
                    dataType: 'JSON',
                    data: {},
                });
            })
        });
    };

    /*
     * TV端 获取设备编码
     */
    _G_BI.getMEI = function () {
        var imei = window.external.getIMEI();
        return imei;
    };

    /*
     * 读取设备编码cookie
     */
    _G_BI.getCookie =  function (name){
        var cookieName = BI.cookie.get(name);
        return cookieName;
    }

    /*
     * 当前设备类型 1 手机   2 TV
     */
    _G_BI.iType = function (){
        var type = window.external.get_device();
        return type;
    };
	
	_G_BI.fullScreen = function (){
		var machine_num = _G_BI.getCookie("machine_num");
		var parents_id = $('.parent').data('id');
		
		$.ajax({
			type: 'get',
			url: '/webi/tv/group/bi/module/get?uid=' + parents_id + '&equipment_code=' + machine_num +'&iType=1',
			dataType: 'JSON',
			data: {},
		});
    }

    _G_BI.search = function (){
        var inputValue = document.getElementById("search-text").value;
        if(inputValue == ""){
            return false;
        }

        $.ajax({
            type: 'GET',
            url: '/webi/design/report/search/'+inputValue,
            dataType: 'JSON',
            success: function (res) {

                $("#search-text").val("");

                if( res.code == 200 ){

                    $(".group_id").val(res.group_id[0]['group_id']);

                    if(res.group_id){

                        var code_id = [res.group_id[0]['group_id'],res.group_id[0]['bi_id']];
                        _G.list(code_id);//选中并查询对应分组数据

                    }else{
                        layer.msg("暂无匹配信息", {icon: 1, offset: '70px',time: 1500});
                        return false;
                    }
                }else{
                    layer.msg(res.msg, {icon: 2, offset: '70px', time: 1500});
                    return false;
                }

            },
        });


    }
	//TV终端  监听手机端操作行为
    if(_G_BI.iType() == 2){
        setInterval('_G_BI.detection();',1000);
    }

    window._G_BI = _G_BI;
})();