
var Index = {

    newSearch: function (category_id,objId) {
        $.ajax({
            type: 'GET',
            url: '/news/search/'+category_id,
            data: {
                page: 1,
                rp: 3
            },
            dataType: 'json',
            success: function(res) {
                if ( res.data.length >0 ) {
                    var html = '';
                    $.each( res.data, function (k,v) {
                        if ( k <= 2 ) {
                            if ( k == 2 ) {
                                html += '<dl class="delete-border">';
                            } else {
                                html += '<dl>';
                            }
                            html += '<a href="/news/detail/'+ v.article_id+'" target="_blank">';
                            html += '<dt>0'+ (k+1) +'</dt>';
                            html += '<dd>';
                            html += '<p>'+ v.article_title+'</p>';
                            html += '<span>'+ v.article_summary+'</span>';
                            html += '</dd>';
                            html += '</a>';
                            html += '</dl>';
                        }
                    });
                    $('#'+objId).html(html);
                } else {
                    $('#'+objId).html( Shop.noConHtml('抱歉，暂无相关新闻') )
                }

            }
        });
    },
    exampleSearch: function (type) {
        $('.case-content ul').html( Shop.loadingHtml('努力加载中') );
        $.ajax({
            type: 'GET',
            url: '/example/search/'+ type,
            data: {
                page: 1,
                rp: 8
            },
            dataType: 'json',
            success: function(res) {
                if ( res.data.length >0 ) {
                    var html = '';
                    $.each( res.data, function (k,v) {
                        var remarks = 'null';
                        if ( v.remark != null ) {

                            remarks = v.remark.substring(0,71);
                            var arr = ['，','。','？','：','；','、','…'];
                            if ( $.inArray(remarks[remarks.length-1],arr) >= 0 ) { //判断尾部是否存在标点符号
                                remarks = remarks.substring(0,remarks.length-1);
                            }

                        }
                        html += '<li>';
                        html += '<a href="/example/detail/'+ v.company_id +'" target="_blank">';
                        if ( v.company_logo ) {
                            html += '<img src="'+ v.company_logo +'">';
                        }
                        html += '<div class="profile">';
                        html += '<p>'+ v.company_full_name +'</p>';
                        html += '<span>'+ remarks +'…</span>';
                        html += '</div>';
                        html += '</a>';
                        html += '</li>';
                        //if (v.remark != null ) {
                        //    console.log( v.remark.length );
                        //}

                    });
                    $('.case-content ul').html(html);

                } else {
                    $('.case-content ul').html( Shop.noConHtml('抱歉，暂无相关案例') )
                }

            }
        });
    }
};




$( function (){


    //banner图片延迟
    setTimeout(function () {
        $('.banner-1 .title').addClass('ban01').show();
    }, 300);
    setTimeout(function () {
        $('.banner-1 .right-img').addClass('ban01-right').show();
    }, 500);

    //banner轮播
    var mySwiper = new Swiper('.swiper-container', {
        onSetWrapperTransition: function(swiper){
            setTimeout(function(){
                    if($('.banner-2').hasClass('swiper-slide-visible swiper-slide-active')){  //滚动到第二张图片

                        $('.banner-2 .title').addClass('ban02');
                        $('.banner-2 .right-img').addClass('ban02-right');
                        $('.banner-1 .title').removeClass('ban01');
                        $('.banner-1 .right-img').removeClass('ban01-right');
                        $('.banner-3 .title').removeClass('ban03');
                        $('.banner-3 .right-img-1').removeClass('r-img-1');
                        $('.banner-3 .right-img-2').removeClass('r-img-2');
                        $('.banner-3 .right-img-3').removeClass('r-img-3');
                        $('.banner-3 .right-img-4').removeClass('r-img-4');
                    } else if($('.banner-3').hasClass('swiper-slide-visible swiper-slide-active')){   //滚动到第三张图片

                        $('.banner-3 .title').addClass('ban03');
                        $('.banner-3 .right-img-1').addClass('r-img-1');
                        setTimeout(function () {
                            $('.banner-3 .right-img-2').addClass('r-img-2').show();
                        }, 500);
                        setTimeout(function () {
                            $('.banner-3 .right-img-3').addClass('r-img-3').show();
                        }, 1500);
                        setTimeout(function () {
                            $('.banner-3 .right-img-4').addClass('r-img-4').show();
                        }, 2000);
                        $('.banner-2 .title').removeClass('ban02');
                        $('.banner-2 .right-img').removeClass('ban02-right');
                    }else if($('.banner-1').hasClass('swiper-slide-visible swiper-slide-active')){   //滚动到第一张图片

                        $('.banner-1 .title').addClass('ban01');
                        $('.banner-1 .right-img').addClass('ban01-right');
                        $('.banner-3 .title').removeClass('ban03');
                        $('.banner-3 .right-img-1').removeClass('r-img-1');
                        $('.banner-3 .right-img-2').removeClass('r-img-2');
                        $('.banner-3 .right-img-3').removeClass('r-img-3');
                        $('.banner-3 .right-img-4').removeClass('r-img-4');
                        $('.banner-2 .title').removeClass('ban02');
                        $('.banner-2 .right-img').removeClass('ban02-right');
                    }
            },10)
        },
        pagination: '.pagination',
        autoplay: 4000,
        grabCursor: true,
        paginationClickable: true
    });

    //全渠道O2O场景鼠标滑过状态
    $(document).on('mouseenter','.scene-left li',function(){

        $(this).addClass('add-property').find('span').addClass('blue-dot');
        $(this).siblings().removeClass('add-property').find('span').removeClass('blue-dot');
        $(this).find('a').addClass('blue-color');
        $(this).siblings().find('a').removeClass('blue-color');

        var index = $('.scene-left li').index(this);
        $('.scene-right-cont').hide();
        $('.scene-right-cont:eq('+ index +')').show();
    });

    //我们的产品
    $(document).on('mouseenter','.our li',function(){
        $(this).find('.pic').css('background-position','center bottom');
    }).on('mouseleave','.our li',function(){
        $(this).find('.pic').css('background-position','center top');
    });

    //解决方案 鼠标滑过
    $(document).on('mouseenter','.module-scene li',function(){
        $(this).find('.icon').css('background-position','center top');
        $(this).find('.look-detail a').show();
    }).on('mouseleave','.module-scene li',function(){
        $(this).find('.icon').css('background-position','center bottom');
        $(this).find('.look-detail a').hide();
    });

    //客户案例筛选
    $(document).on('click','.case-nav li',function(){

        $(this).addClass('current').siblings().removeClass('current');
        var type = $(this).attr('data-type');
        Index.exampleSearch( type );
        $('.success-pic').find('.example-link').attr('href','example?industry_type='+type);

    });
    //客户案列鼠标滑过效果
    $(document).on('mouseenter','.case-content li',function(){
        $(this).find('.profile').show();
    }).on('mouseleave','.case-content li',function(){
        $(this).find('.profile').hide();
    });

    //新闻资讯切换
    $(document).on('click','.news-nav ul li', function () {
        $(this).addClass('add-style').siblings().removeClass('add-style');
        var num=$(this).index();
        $('.com-news-list .news-info ').eq(num).show().siblings().hide();
    });




});
