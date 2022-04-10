$(function () {
    /**
     * 如果页面有网站导航
     * 根据页面链接和导航链接显示导航高亮图片
     * 页面内容部分设置距离底部的距离，防止导航把页面底部内容遮盖
     */
    var nav_obj = $('#navigation');
    if (nav_obj.length>0) {

        var url_data = E.parseURL(location.href),
            path = url_data.path;
        nav_obj.find('a').each(function () {
            var _this = $(this);
            //获取高亮图标
            var on_img_src = _this.attr('on-img-src');
            if (!on_img_src) {
                return true;
            }
            //获取导航链接
            var nav_href = _this.attr('href');
            if (!nav_href) {
                return true;
            }
            if (nav_href == '/wap/index.html' && path == '/wap/') {
                _this.find('img').attr('src', on_img_src);
                return false;
            } else if (nav_href.indexOf(path) > -1) {
                _this.find('img').attr('src', on_img_src);
                return false;
            }
        });

        $(document).on('focus','input[type="text"],input[type="search"],input[type="password"]', function () {
            $('#navigation').hide();
        }).on('blur','input[type="text"],input[type="search"],input[type="password"]', function () {
            $('#navigation').show();
        });
    }

    /**
     * 公告内容移动
     * @param scroll_obj
     */
    function notice_move(scroll_obj,span_width,parent_width) {
        var speed = Math.ceil(span_width/parent_width);
        console.log(speed);
        scroll_obj.animate({marginLeft:-span_width}, speed * 8000,function(){
            scroll_obj.css({marginLeft:parent_width+10});
            notice_move(scroll_obj,span_width,parent_width);
        });
    }

    //网站公告内容滚动效果
    var custom_notice_obj = $('div.custom-notice');
    if (custom_notice_obj.length > 0) {
        custom_notice_obj.each(function (k, v) {
            //可见内容部分宽度
            var parent_width = $(this).find('.custom-notice-inner').width();
            //内容部分宽度
            var span_width = $(this).find('span').width() ;

            if (parent_width < span_width) {
                var scroll_obj = $(this).find('div.custom-notice-scroll');
                notice_move(scroll_obj,span_width,parent_width);
            }
        });
    }
    /**
     * swiper插件
     */
    var swiper_obj = $('div.swiper-container');
    if (swiper_obj.length > 0) {
        if ( swiper_obj.attr('data-loading') ) {    //如果有这个属性的话就不使用公共方法
            return false
        }
        swiper_obj.each(function (k, v) {

            var cl = 'swiper-container' + (k + 1);
            $(v).addClass(cl);

            var pl = 'swiper-pagination' + (k + 1);
            $(v).find('.swiper-pagination').addClass(pl);

            if ($(v).find('.swiper-slide').length > 1) {
                new Swiper('.' + cl, {
                    pagination: '.' + pl,
                    paginationClickable: true,
                    centeredSlides: true,
                    loop: true,
                    autoplay: 4000,
                    autoplayDisableOnInteraction: false
                });
            }else{
                new Swiper('.' + cl, {
                    //pagination: '.' + pl,
                    loop: false
                });
            }

        });
    }
    var swiper_little_obj = $('div.swiper-little-container');
    if (swiper_little_obj.length > 0) {
        swiper_little_obj.each(function (k, v) {

            var cl = 'swiper-little-container' + (k + 1);
            $(v).addClass(cl);

            var pl = 'swiper-little-pagination' + (k + 1);
            $(v).find('.swiper-pagination').addClass(pl);

            if ($(v).find('.swiper-slide').length > 0) {
                new Swiper('.' + cl, {
                    slidesPerView: 5
                });
            }
        });
    }

    //返回按钮

    $('#back-prev').click(function () {
        if( document.referrer!='' ){
            history.go(-1);
        }else{
            window.location.href = '/wap/index.html';
        }
    });
    var form_obj = $('form.global-search-form');
    if (form_obj.length > 0) {
        form_obj.each(function () {

            var _this = $(this);

            _this.submit(function () {
                var keyword = E.trim($(this).find('input').val());
                if (E.isEmpty(keyword) || keyword == '输入查找的商品名称') {
                    $(this).find('input').val('');
                    return false;
                }
                Shop.search(keyword);
            });

            var search_btn_obj = _this.find('.search-btn');
            if (search_btn_obj.length > 0) {
                search_btn_obj.click(function () {
                    var keyword = E.trim( $(this).parent('.global-search-form').find('input').val());
                    if (E.isEmpty(keyword) || keyword == '输入查找的商品名称') {
                        $(this).prev().val('');
                        return false;
                    }
                    Shop.search(keyword);
                });
            }
        });
    }

    //开启定位的页头显示门店名称
    var header_position = $('#header-position');
    if (header_position.length > 0) {
        var mall_name = E.getCookie('EBSIG_MALL_NAME');
        if (mall_name) {
            header_position.text(mall_name);
        }
    }

    //查询商品实时价格
    var goods_list_obj = $('ul.custom-goods-list');
    if (goods_list_obj.length > 0) {

        var goods_arr = [], goods_id;

        goods_list_obj.find('li').each(function () {
            goods_id = $(this).attr('data-target');
            if ($.inArray(goods_id, goods_arr) == -1) {
                goods_arr.push(goods_id);
            }
        });

        E.ajax_post({
            url: '/ajax-shop/goods/batchGoods.ajax?operFlg=1',
            data: {
                id: goods_arr.join(',')
            },
            call: function (res) {
                if (res.code == 200) {

                    var id, goods_data = res.data;
                    goods_list_obj.find('li').each(function () {
                        id = $(this).attr('data-target');
                        if (goods_data[id]) {
                            $(this).find('.public-saleprice').text('¥' + goods_data[id].price);
                            if(goods_data[id].price < goods_data[id].marketPrice){
                                $(this).find('.public-maketprice').text('¥' + goods_data[id].marketPrice).show();
                            } else{
                                $(this).find('.public-maketprice').hide();
                            }

                            if(goods_data[id].enableSaleAmount <= 0){
                                $(this).find('.cart-text').html('<span class="shortage word-color">补货中</span>');
                            }

                            var html='';
                            if(goods_data[id].tag){
                                $.each(goods_data[id].tag,function(k,v){
                                    if(v.show_type==1){
                                        html +='<span style="height: 22px;"><img src="'+ v.pic_url +'"></span>'
                                    } else if(v.show_type==2){
                                        html+='<span style="color:'+ v.word_color +';background:'+ v.bg_color +';border-radius: 5px; padding: 0 2px; font-size: 12px;">'+ v.tag_name+'</span>';
                                    }
                                });
                            }
                            $(this).find('.tag-box').append(html);

                        }
                    });

                }
            }
        })

    }

    //秒杀活动
    var custom_seckill_obj = $('.custom-seckill');
    if (custom_seckill_obj.length > 0){

        custom_seckill_obj.each(function(k, v){
            var seckill_style = $(v).find('input[name="seckill-style"]').val();
            var startTimeStamp = $(v).find('input[name="start-time"]').val()*1000;
            var endTimeStamp = $(v).find('input[name="end-time"]').val()*1000;

            if(seckill_style == 1){
                count_down(startTimeStamp, function( d, h, m, s ){
                    var html = '';
                    html +='距开始';
                    if(d > 0){
                        html +='<span class="word-color">'+ d +'</span>天 ';
                    }
                    html +='<span>'+ h +'</span>：';
                    html +='<span>'+ m +'</span>：';
                    html +='<span>'+ s +'</span>';
                    $(v).find($('.custom-seckill-time')).html(html);

                });
            } else if(seckill_style == 2){
                count_down(endTimeStamp, function( d, h, m, s ){
                    var html = '';
                    html +='还剩';
                    if(d > 0){
                        html +='<span class="word-color">'+ d +'</span>天 ';
                    }
                    html +='<span>'+ h +'</span>：';
                    html +='<span>'+ m +'</span>：';
                    html +='<span>'+ s +'</span>';
                    $(v).find($('.custom-seckill-time')).html(html);
                });
            }else if(seckill_style == 3){
                $(v).find($('.custom-seckill-time')).text('秒杀活动已结束');
            }
        })

    }

});

var shop = Shop = {

    layerIndex: 0,

    callFunc: '',

    search: function (keyword) {
        self.location = '/wap/search.html?keyword=' + keyword;
    },

    //微电汇技术支持
    showCopyright: function () {

        var nav_height = 0;
        if ($('#navigation').length > 0) {
            nav_height = $('#navigation').height();
        } else if ($('#buy_goods').length > 0) {
            nav_height = $('#buy_goods').height();
        }
        if ( $('body').height() + nav_height >= $(window).height()) {
            if (nav_height == 0) {
                nav_height = 10;
            }
            $('#wdh-copyright').css('margin-bottom', nav_height + 'px').show();
        }
    },

    /**
     * 购物车数量展示
     */
    get_cart_num: function () {

        var cart_num_obj = $('.cart-num');
        if (cart_num_obj.length > 0) {
            var cart_amount = E.getCookie('cart_amount');
            if (cart_amount == null) {
                cart_amount = 0;
            }
            cart_num_obj.each(function () {
                $(this).text(cart_amount);
            });
        }

    },

    //获取用户名
    getUserID: function () {
        return E.getCookie('WANSONSHOP_IDENTIFIER');
    },
    layer: {

        index: 0,

        open: function (url, call_func) {

            var window_height = $(window).height();
            var html = '<iframe scrolling="auto" allowtransparency="true" frameborder="0" src="' + url + '" style="width: 100%;height: ' + window_height + 'px;"></iframe>';

            this.index = layer.open({
                type: 1,
                content: html,
                style: 'position:fixed; left:0; top:0; width:100%; height:100%; border:none;',
                end: function () {
                    $('body').css('overflow-y', 'auto');
                    if (typeof(call_func) == 'function') {
                        call_func();
                    }
                },
                success: function () {
                    $('body').css('overflow-y', 'hidden');
                }
            });

        },

        close: function () {
            layer.close(this.index);
        }

    },

    goLogin: function (redirect_url) {
        if (!redirect_url || typeof(redirect_url) == 'function') {
            redirect_url = location.href;
        }
        self.location = '/wap/login.html?requrl=' + encodeURIComponent(redirect_url);
    },

    //layer加载等待层
    load: function (isshade) {
        if ( isshade ) {
            return layer.open({
                type: 2,
                shadeClose: false,
                style: ' background:#000; padding:25px; opacity: 0.3; border-radius: 8px;'
            });
        } else {
            return layer.open({
                type: 2,
                shade: false,
                style: ' background:#000; padding:25px; opacity: 0.3; border-radius: 8px;'
            });
        }

    },

    //公共等待层新方法
    wait_layer: function (type,words) {
        var html = '<div class="custom-layer-loading">';
        html += ' <div class="laymshade"></div>';
        html += '<div class="layermmain">';
        html += '<div class="section">';
        if ( type == 2 ) {
            html += '<div class="inner flex">';
        } else {
            html += '<div class="inner">';
        }
        html += '<img src="/images/m/common/loading4.gif">';
        if ( words ) {
            html += '<div class="title">'+ words +'</div>';
        } else {
            html += '<div class="title">正在加载</div>';
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('body').append(html);
    },
    close_wait_layer: function () {
        $('.custom-layer-loading').remove();
    },

    //滚动加载更多
    loadding: function ( page,totalPages,isfirst ) {
        var examineHtml ='';
        if( page <= totalPages){
            examineHtml += '<div class="loading hide rel">';
            examineHtml += '<span class="loading-img abs"></span>';
            examineHtml += '<span class="loading-title">正在加载更多...</span>';
            examineHtml += '</div>';
        } else {
            examineHtml += '<div class="loading hide rel">';
            examineHtml += '<p class="no-more">';
            examineHtml += '<span>没有更多了～</span>';
            examineHtml += '</p>';
            examineHtml += '</div>';
        }
        if ( isfirst == undefined ) {
            if ( totalPages>1 ) {
                $('#examine').html(examineHtml);
            } else {
                $('#examine').html('')
            }
        } else {
            $('#examine').html(examineHtml);
        }


    },

    noContent: function ( words,noneImg ) {
        var html = '';
        if ( noneImg ) {
            html+= '<span><img src="'+ noneImg +'"></span>';
        } else {
            html+= '<span><img src="/images/m/common/none.png"></span>';
        }
        html+= '<div class="result-name">'+words+'</div>';
        $('#not-result').html('').html(html);
    },

    //加载js文件
    loadScript: function (url, callback) {

        var script = document.createElement("script");
        script.type = "text/javascript";
        if (script.readyState){ //IE
            script.onreadystatechange = function(){
                if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else { //Others: Firefox, Safari, Chrome, and Opera
            script.onload = function(){
                callback();
            };
        }
        script.src = url;
        document.body.appendChild(script);

    },

    //生成条形码
    barcode: function (barcode, params) {

        if (!params) {
            params = {};
        }
        if (!params.selector_id) {
            params.selector_id = 'custom-barcode';
        }

        this.loadScript('/static/libs/jquery-barcode/jquery-barcode.min.js', function () {
            var settings = {
                output:'css',           //输出条形码方式
                btype:'code128',       //生码规则
                bgColor: '#FFFFFF',   //条形码背景颜色
                color: '#000000',     //条形码颜色
                barWidth: '2.5px',      //条形码宽度
                barHeight: '60px',  //条形码高度
                numSize: '20px',     //条形码下方数字的大小
                moduleSize: 5,
                addQuietZone: false,
                isNeedNumber:0  //是否需要显示条形码下方的数字
            };
            if (params.setting) {
                settings = E.concat(settings, params.setting);
            }
            if (params.isNeedNumber) {
                settings.isNeedNumber = params.isNeedNumber;
            }

            if ( settings.isNeedNumber == 0 ) {
                $('#' + params.selector_id).barcode(barcode, settings.btype,settings).children(":last").remove();
            } else {
                $('#' + params.selector_id).barcode(barcode, settings.btype,settings).children(":last").css('font-size',settings.numSize)
            }
        });

    },

    //点击获取卡券
    getCard: function (card_id){
        if (!Shop.getUserID()) {
            sessionStorage.setItem('login_success_call_back', 'function:Shop.getCard');
            sessionStorage.setItem('card_id', card_id);
            Shop.goLogin(location.href);
            return false;
        }

        var layer_load_index = Shop.load();
        E.ajax_post({
            action: 'display',
            operFlg: 1,
            data: {
                couponID: card_id
            },
            call: function (o) {
                layer.close(layer_load_index);
                if( o.code != 200 && o.code != 409 ){
                    layer.open({
                        content: o.message,
                        btn: ['确定']
                    });
                    return false;
                }

                var msg = '恭喜您，领取成功！';
                if ( o.code == 409 ) {
                    msg = o.message;
                }
                layer.open({
                    content: msg,
                    btn: ['点击查看'],
                    end: function() {
                        self.location = '/wap/member/card_coupon.html';
                    }
                });
            }
        });
    }

};

Shop.get_cart_num();

window.onload = function () {
    Shop.showCopyright();


    //返回顶部按钮事件
    $(window).scroll(function () {
        if ($(this).scrollTop() >= 500) {
            $('#go-top').fadeIn();
        } else {
            $('#go-top').fadeOut();
        }
    });

    $('#go-top').click(function () {
        $(window).scrollTop(0);
    });

};