
$( function (){

    //banner轮播
    //banner图片延迟
    setTimeout(function () {
        $('.banner-1 .phone').addClass('ban01-02').show();
    }, 300);
    setTimeout(function () {
        $('.banner-1 .right-img').addClass('ban01-03').show();
    }, 500);
    var mySwiper = new Swiper('.swiper-container', {
        onSetWrapperTransition: function(swiper){
            setTimeout(function(){
                if($('.banner-2').hasClass('swiper-slide-visible swiper-slide-active')){  //滚动到第二张图片
                    $('.banner-2 .phone').addClass('ban01-02');
                    $('.banner-2 .right-img').addClass('ban01-03');
                    $('.banner-1 .phone').removeClass('ban01-02');
                    $('.banner-1 .right-img').removeClass('ban01-03');

                } else if($('.banner-3').hasClass('swiper-slide-visible swiper-slide-active')){   //滚动到第三张图片

                    $('.banner-3 .phone').addClass('ban01-02');
                    $('.banner-3 .right-img').addClass('ban01-03');
                    $('.banner-2 .phone').removeClass('ban01-02');
                    $('.banner-2 .right-img').removeClass('ban01-03');

                }else if($('.banner-1').hasClass('swiper-slide-visible swiper-slide-active')){   //滚动到第一张图片
                    $('.banner-1 .phone').addClass('ban01-02').show();
                    $('.banner-1 .right-img').addClass('ban01-03').show();
                    $('.banner-3 .phone').removeClass('ban01-02');
                    $('.banner-3 .right-img').removeClass('ban01-03');
                }
            },10)
        },
        pagination: '.pagination',
        autoplay: 4000,
        grabCursor: true,
        paginationClickable: true,
        loop: true
    });



    var cases = {

        page: 1,

        totalPages: 0,

        rp: 15,

        id: 0,

        casesList: function(){

            $('#load-more').hide();
            if(cases.page == 1){
                $('#list-show').find('ul').html('').hide();
            }
            $.ajax({
                type: 'get',
                url: '/v2/cases/search',
                dataType: 'JSON',
                data: {
                    id: cases.id,
                    page: cases.page,
                    rp: cases.rp
                },
                success: function(res) {
                    var html = '';
                    $.each(res.data,function(k,v) {
                        html += '<li>';
                        html += '<a href="/v2/cases/detail?id='+ v.id+'">';
                        html += '<div class="logo-box">';
                        html += '<img class="default-logo" src="'+ v.logo +'" alt="">';
                        html += '<img class="highlight-logo" src="'+ v.highlight_logo +'" alt="">';
                        html += '</div>';
                        html += '</a>';
                        html += '</li>';
                    });

                    cases.totalPages = Math.ceil(res.total/cases.rp);
                    if(cases.page < cases.totalPages && res.data.length > 0){
                        $('#load-more').show();
                    } else {
                        $('#load-more').hide();
                    }
                    $('#list-show').find('ul').append(html).show();
                    $('#customer-box').show();

                }
            });
        }

    };
    cases.casesList();

    $(document).on('click','#load-more p',function(){
        self.location = '/v2/cases?id='+ cases.id;
    }).on('click','#list-nav li',function(){
        console.log(1);
        $(this).addClass('active').siblings().removeClass('active');
        cases.id = $(this).attr('data-id');
        cases.page = 1;
        cases.casesList();
    });

});
