/**
 * Created by wangqingqing on 14-10-13.
 */
KindEditor.plugin('couponCode', function(K) {
    var self = this, name = 'couponCode';
    // 点击图标时执行
    self.clickToolbar(name, function() {
        search();
    });

    function search(page){

        this.couponName = $("#editor_couponName").val();

        if (!this.couponName) {
            this.couponName = '';
        }
        this.couponType = $("#editor_couponType").val();
        if (!this.couponType) {
            this.couponType = '';
        }

        E.loadding.open('正在查询，请稍候...');

        $.ajax({
            type: "POST",
            url: "/ajax-backend/wechatPromotion/searchCouponsAndActivity.ajax?operFlg=4",
            data: {
                couponName: this.couponName,
                couponType:this.couponType,
                sortname: 'createTime',
                sortorder: 'desc',
                rp: 10,
                page: page
            },
            dataType: "json",
            success: function(o){
                showCoupon(o);
            }
        });
    }

    function showCoupon(obj){

        //关闭等待层
        E.loadding.close();
        if( obj.code != 200 ){
            E.alert(obj.message);
            return false;
        }
        var html_str = '<div style="padding: 5px;" class="form-inline">';
        html_str += '电子券名称： <input placeholder="" type="text" id="editor_couponName" class="form-control" name="editor_couponName" style="width: 120px;" value="' + this.couponName + '"/>&nbsp;&nbsp;';
        html_str += '电子券类型：<select name="editor_couponType" id="editor_couponType" class="form-control">';
        html_str += '<option value="" ';
        if ( this.couponType == "" ) {
            html_str += 'selected ';
        }
        html_str += '>请选择</option>';

        html_str += '<option value="1" ';
        if ( this.couponType == 1 ) {
            html_str += 'selected ';
        }
        html_str += '>现金券</option>';

        html_str += '<option value="2" ';
        if ( this.couponType == 2 ) {
            html_str += 'selected ';
        }
        html_str += '>折扣券</option>';

        html_str += '<option value="3" ';
        if ( this.couponType == 3 ) {
            html_str += 'selected ';
        }
        html_str += '>提货券</option>';
        html_str += '</select>';
        html_str += '&nbsp;&nbsp;<input type="button" class="editorSearchCoupon btn btn-primary" value="查询"  />';
        html_str += '&nbsp;&nbsp;<input type="button" class="editorClearUp btn btn-warning" value="重置"  />';
        html_str += '</div>';
        html_str += '<table class="table table-bordered">';
        html_str += '<tbody>';

        html_str += '<tr>';
        html_str += '<th width="80">操作</th>';
        html_str += '<th width="150">电子券名称</th>';
        html_str += '<th width="100">电子券类型</th>';
        html_str += '<th width="100">数量</th>';
        html_str += '<th width="100">有效期</th>';

        html_str += '</tr>';
        $.each(obj.rows, function(k, v) {

            html_str += '<tr>';
            html_str += '<td class="tac"><a href="javascript:void(0)" class="editorSelectCoupon"  val0='+v.id+' val1='+v.cell[0]+'>选择</a></td>';
            html_str += '<td class="tac" style="text-align:left">' + v.cell[0] + '</td>';
            html_str += '<td class="tac">' + v.cell[1] + '</td>';
            html_str += '<td>' + v.cell[5] + '</td>';
            html_str += '<td>' + v.cell[2] + '</td>';
            html_str +='<input type="hidden" class="editorCouponCode" name="editor_couponCode" value="' + k + '">';
            html_str += '</tr>';
        });
        html_str += '</tbody>';
        html_str += '</table>';

        if (obj.paging) {
            html_str += obj.paging;
        }

        E.popup.open({
            content: html_str,
            title: '电子券列表',
            css: 'width: 800px;'
        });

        $(".page_con a").attr("href","javascript:void(0);");

        //选择电子券活动码
        $(".editorSelectCoupon").click(function(){
            var index =   $(this).parent().parent().find('.editorCouponCode').val();
            var html = obj.rows[index]["cell"][3];
            var coupon = addHtmlTags( html, 'class=\"ebsig_couponGet\"', 'onclick="getOffLineCoupon(' + obj.rows[index]["id"]+ ');"') ;
            self.insertHtml(coupon);
            E.popup.close();
        });
        //追加点击事件
        function addHtmlTags ( str, flag, addStr ) {
            var regExp = new RegExp( flag , "g");
            return str.replace(regExp, flag + " " + addStr);
        }
        
        //查询
        $(".editorSearchCoupon").click(function(){
            search();
        })
       //重置
        $(".editorClearUp").click(function(){
               $('#editor_couponName').val("");
               $('#editor_couponType').val("");
        });
        //分页
       $(".page_con a").click(function(){
           var page = $(this).children("span").html();
           var curPage = parseInt($(this).siblings("span.page-cur").html());
           if(page == "上一页"){
               page = curPage - 1;
           }else if(page == "下一页"){
               page = curPage + 1;
           }else{
               page = parseInt(page);
           }
           search(page);
       });
    }
});





