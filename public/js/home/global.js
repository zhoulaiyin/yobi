var Shop = {
    
    /**
     * 分页查询
     */
    paging: function ( page, rp, count, link ) {
        var total_page = Math.ceil( count/rp );

        var type = 0;
        if ( link.indexOf('javascript:') != -1 ) {  //传了方法而不是链接
            type = 1;
            link = link.replace('javascript:', '');
        }

        function link_replace(link, page) {
            return link.replace('%d', page);
        }


        if (total_page == 1) {
            $('#paging').html('');
            return false;
        }

        var html = '';
        html += '<div class="paging">';
        html += '<div class="tc">';
        html += '<ul class="pagination over">';
        if ( page == 1 ) {
            html += '<li class="upper disable"><span>&lt;上一页</span></li>';
        } else {
            if ( type == 1 ) {
                html += '<li class="upper"><a href="javascript:;" onclick="'+ link_replace(link, page-1) +'">&lt;上一页</a></li>';
            } else {
                html += '<li class="upper"><a href="' + link_replace(link, page-1) +'">&lt;上一页</a></li>';
            }

        }

        var i = 1;
        while (i <= total_page) {
            if (i == page) {
                if ( type == 1 ) {
                    html += '<li class="active"><a href="javascript:;" onclick="'+ link_replace(link, i) + '">' + i + '</a></li>';
                } else {
                    html += '<li class="active"><a href="'+ link_replace(link, i) + '">' + i + '</a></li>';
                }

            } else {
                if ( type == 1 ) {
                    html += '<li><a href="javascript:;" onclick="'+ link_replace(link, i) + '">' + i + '</a></li>';
                } else {
                    html += '<li><a href="'+ link_replace(link, i) + '">' + i + '</a></li>';
                }

            }

            if (page - 3 > i) {
                html += '<li class="active">...</li>';
                i = page - 3;
            } else if (i < total_page && page + 3 < i && total_page - 1 > i) {
                html += '<li class="active">...</li>';
                i = total_page - 1;
            }
            i++;
        }

        if ( page == total_page ) {
            html += '<li class="next disable"><span>下一页></span></li>';
        } else {
            if ( type == 1 ) {
                html += '<li class="next"><a href="javascript:;" onclick="'+ link_replace(link, page+1) +'">下一页></a></li>';
            } else {
                html += '<li class="next"><a href="' + link_replace(link, page+1) + '">下一页></a></li>';
            }

        }
        html += '</ul>';

        html += '</div>';
        html += '</div>';

        $('#paging').html(html);
    },

    /**
     * 加载中html
     */
    loadingHtml: function (words) {
        var html = '<div class="more-loading rel">';
        html += '<span class="more-loading-img abs"></span>';
        html += '<span class="more-loading-title">'+words+'</span>';
        html += '</div>';
        return html
    },

    noConHtml: function (words) {

        var html = '';
        html+= '<div class="no-content">';
        html+= '<span><img src="/images/home/common/none.png"></span>';
        html+= '<div class="result-name">'+words+'</div>';
        html+= '</div>';
        return html;

    }
};

(function () {

    window.global = {


        /**
         * 匹配手机号码
         * @param s
         * @returns {Array|{index: number, input: string}}
         */
        isMobile: function(s) {
            var reg = /^1[34578]\d{9}$/;
            return reg.exec(s);
        },

        /**
         * 匹配电话号码
         * @param s
         * @returns {Array|{index: number, input: string}}
         */
        isPhone: function(s) {
            var reg = /^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,6})?$/;
            return reg.exec(s);
        }

    };

})();

/**
 * 头部导航鼠标滑过显示下拉菜单
 */
$( function (){

    $(document).on('mouseenter','.head-nav-left ul .down',function(){

        $(this).addClass('hover');

    }).on('mouseleave','.head-nav-left ul .down',function(){

        $(this).removeClass('hover');

    }).on('mouseenter','.head-nav-left ul .product-li',function(){

        $('.menu-arrow').addClass('up-arrow');

    }).on('mouseleave','.head-nav-left ul .product-li',function(){

        $('.menu-arrow').removeClass('up-arrow');

    }).on('mouseenter','.head-nav-left ul .cases-li',function(){

        $('.down-arrow').addClass('up-arrow-list');

    }).on('mouseleave','.head-nav-left ul .cases-li',function(){

        $('.down-arrow').removeClass('up-arrow-list');

    }).on('mouseenter','.pull-menu ul li',function(){

        $(this).addClass('cut-menu');

    }).on('mouseleave','.pull-menu ul li',function(){

        $(this).removeClass('cut-menu');

    });

});

//QQ咨询
$( function (){

    $(document).on('click','.qq-top',function(){
        $(this).parent('.qq-consult').hide();
        $(this).parents().siblings().find('.qq-head').show();
    });

    $(document).on('click','.qq-head',function(){
        $(this).parents().siblings('.qq-consult').show();
    });
    $('#btn-top').click(function () {
        $('body,html').animate({scrollTop: 0},500);
    });

});

//解决方案
$( function (){

    $(document).on('click','.bnt-download',function(){
        $(this).parents().siblings('.download-layer').show();
    });

    $(document).on('click','.bnt-cancel',function(){
        $(this).parents('.download-nav').hide();
    });

    $(document).on('click','.bnt-cancel-pic',function(){
        $(this).parent('.download-layer').hide();
    });

});

//下载表单判断
$( function () {

    $('#confirm-refer').click(function () {

        var contacts = $.trim($('#contacts').val());
        var mobile = $.trim($('#mobile').val());
        var email = $.trim($('#email').val());

         //if ( contacts == '') {
         //layer.msg('请输入您的称呼', {icon: 2, time: 2000});
         //return false;
         //}

         if ( mobile == '' ) {
         layer.msg('请输入您的手机号码', {icon: 2, time: 2000});
         return false;
         } else if (!global.isMobile(mobile) && !global.isPhone(mobile)) {
         layer.msg('请输入正确的手机号码', {icon: 2, time: 2000});
         return false;
         }

         //if ( email == '') {
         //layer.msg('请输入邮箱', {icon: 2, time: 2000});
         //return false;
         //}


        var layer_index = layer.load();
        $.ajax({
            type: 'POST',
            url: "/contact/add",
            data: {
                contacts: contacts,
                mobile: mobile,
                email: email
            },
            dataType: 'json',
            success: function(res){

                layer.close(layer_index);
                if (res.code == 200) {
                    layer.msg('已收到您的申请，<br>我们将尽快与您联系！', {icon: 1, time: 2000});
                    $('#contacts').val('');
                    $('#mobile').val('');
                    $('#email').val('');
                } else {
                    layer.msg(res.message, {icon: 2});
                }

            }
        });


    });

});