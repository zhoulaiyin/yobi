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


        if (total_page <= 1) {
            $('#paging').html('');
            return false;
        }

        var html = '';
        html += '<div class="paging">';
        html += '<div class="tc">';
        html += '<ul class="pagination over">';
        if ( page == 1 ) {
            html += '<li class="upper disable client-pc"><span>&lt;上一页</span></li>';
            html += '<li class="upper disable client-mobile"><span>&lt;</span></li>';
        } else {
            if ( type == 1 ) {
                html += '<li class="upper client-pc"><a href="javascript:;" onclick="'+ link_replace(link, page-1) +'">&lt;上一页</a></li>';
                html += '<li class="upper client-mobile"><a href="javascript:;" onclick="'+ link_replace(link, page-1) +'">&lt;</a></li>';
            } else {
                html += '<li class="upper client-pc"><a href="' + link_replace(link, page-1) +'">&lt;上一页</a></li>';
                html += '<li class="upper client-mobile"><a href="' + link_replace(link, page-1) +'">&lt;</a></li>';
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
                html += '<li class="ellipsis">...</li>';
                i = page - 3;
            } else if (i < total_page && page + 3 < i && total_page - 1 > i) {
                html += '<li class="ellipsis">...</li>';
                i = total_page - 1;
            }
            i++;
        }

        if ( page == total_page ) {
            html += '<li class="next disable client-pc"><span>下一页></span></li>';
            html += '<li class="next disable client-mobile"><span>></span></li>';
        } else {
            if ( type == 1 ) {
                html += '<li class="next client-pc"><a href="javascript:;" onclick="'+ link_replace(link, page+1) +'">下一页></a></li>';
                html += '<li class="next client-mobile"><a href="javascript:;" onclick="'+ link_replace(link, page+1) +'">></a></li>';
            } else {
                html += '<li class="next client-pc"><a href="' + link_replace(link, page+1) + '">下一页></a></li>';
                html += '<li class="next client-mobile"><a href="' + link_replace(link, page+1) + '">></a></li>';
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

    //头部
    $(document).on('click','.head-nav-left ul li',function(){

        if($(this).hasClass('hover')){
            $(this).removeClass('hover');
            if($(this).find('.down-arrow')){
                $(this).find('.down-arrow').removeClass('up-arrow-list');
            }
        } else {
            $(this).addClass('hover').siblings().removeClass('hover');
            $(this).find('.down-arrow').addClass('up-arrow-list');
            $(this).siblings().find('.down-arrow').removeClass('up-arrow-list');
        }

    }).on('mouseenter','.head-nav-left ul li',function(){

        $('.global-header-top').addClass('bs');
        $(this).addClass('hover');
        if($(this).find('down-arrow')){
            $(this).find('.down-arrow').addClass('up-arrow-list');
        }

    }).on('mouseleave','.head-nav-left ul li',function(){

        $('.global-header-top').removeClass('bs');
        $(this).removeClass('hover');
        if($(this).find('down-arrow')){
            $(this).find('.down-arrow').removeClass('up-arrow-list');
        }

    }).on('click','#m-nav-show',function () {
        $('#m-nav-box').show();
        $('html').css('overflow','hidden');
        $('body').css('overflow','hidden');
    }).on('click','#m-nav-hide',function () {
        $('#m-nav-box').hide();
        $('html').css('overflow','auto');
        $('body').css('overflow','auto');
    }).on('click','#m-nav-list .item-meun',function () {
        if($(this).find('.m-nav-arrow').hasClass('m-nav-arrow-up')){
            $(this).find('.m-nav-arrow').removeClass('m-nav-arrow-up');
            $(this).find('ul').hide();
        } else {
            $(this).find('.m-nav-arrow').addClass('m-nav-arrow-up');
            $(this).find('ul').show();
        }
    });

    //侧边栏
    $(document).on('click','#right-nav ul li.nav-item',function(){
        if($(this).hasClass('hover')){
            $(this).removeClass('hover');
        } else {
            $(this).addClass('hover');
        }
    }).on('click','#radio-show',function(){

        $('#right-nav ul li.nav-item').removeClass('hover');  //关闭二维码和电话号码弹框

        var html = '';
        html += '<span class="close-radio"></span>';
        html += '<video  src="/images/home/v2/common/gw-video.mp4"  controls="" autoplay="">';
        html += '<object data="/images/home/v2/common/gw-video.mp4" type="">';
        html += '<embed src="/images/home/v2/common/gw-video.mp4" type="">';
        html += '</object>';
        html += '</video>';
        $('#radio').html(html);
        var _height = $('#radio').height();
        $('#radio').addClass('show').css('margin-bottom',-(_height/2)+'px');

    }).on('click','#radio .close-radio',function (){

        $('#radio').removeClass('show');
        $('#nav-radio').removeClass('hover');
        setTimeout(function () {
            $('#radio').html('');
        },700);
    }).on('click','#btn-top',function(){
        $('body,html').animate({scrollTop: 0},500);
    });

    //底部二维码

    $(document).on('click','#bottom-info-pc .weichat-box',function () {
        if($(this).find('.ewm-box').css('display') == 'block'){
            $(this).find('.ewm-box').hide()
        } else {
            $(this).find('.ewm-box').show()
        }
    });

    //联系我们
    $(document).on('click','.send-information',function () {
        var contacts = $.trim($('.contacts').val());
        var mobile = $.trim($('.mobile').val());
        var info = $.trim($('.info').val());

        if ( mobile == '' ) {
            layer.msg('请输入您的手机号码/电话', {time: 2000});
            return false;
        } else if (!global.isMobile(mobile) && !global.isPhone(mobile)) {
            layer.msg('请输入正确的手机号码/电话', {time: 2000});
            return false;
        }

        var layer_index = layer.load();
        $.ajax({
            type: 'POST',
            url: "/v2/contact/add",
            data: {
                contacts: contacts,
                mobile: mobile,
                info: info
            },
            dataType: 'json',
            success: function(res){
                layer.close(layer_index);
                if (res.code == 200) {
                    layer.msg('提交成功, 我们的客<br>服人员会及时联系您 !', {icon: 1, time: 2000});
                    $('.contacts').val('');
                    $('.mobile').val('');
                    $('.info').val('');
                    if ($('#contact-infor-box').find('.infor-box').hasClass('show')) {
                        $('#contact-infor-box').find('.infor-box').removeClass('show');
                    }
                } else {
                    layer.msg(res.message);
                }
            }
        });
    }).on('click','#contact-us',function () {
        $('#contact-infor-box').find('.infor-box').addClass('show');
    }).on('click','#infor-close',function () {
        $('#contact-infor-box').find('.infor-box').removeClass('show');
    });
});


