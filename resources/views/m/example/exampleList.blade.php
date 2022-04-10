@extends('m.layout')

@section('css')
    <link rel="stylesheet" href="/css/m/example.css?v=2017032103">
@endsection

@section('content')
    <div class="content">
        <ul class="example">

        </ul>
        <div id="examine"></div>
        <div id="not-result"></div>
        <div class="go-top hide" id="go-top">
            <img src="/images/m/common/top.png" alt="">
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/js/m/common/global.js?v=2017032203"></script>
    <script type="text/javascript">
    var example ={
        sub: 0,
        page:1,
        rp:5,
        totalPages: 0,
        industry_type: '{{$industry_type}}',
        example_list:function () {
            example.sub = 1;
            if ( example.page == 1 ) {
                Shop.wait_layer(1);
            }
            $.ajax({
                type: 'GET',
                url: '/mobile/example/'+ example.industry_type, //所属行业类型 industry_type
                data: {
                    page: example.page, //page 开始默认为1
                    rp: example.rp
                },
                success:function ( res ) {
                    if ( example.page == 1 ) {
                        $('.example').html('');
                        $('#examine').html('');
                        $('#not-result').html('');
                        Shop.close_wait_layer();
                    }
                    if ( res.data.length > 0 ){
                        var html = '';
                        $.each( res.data, function (k,v) {
                            html += '<li class="example-list">';
                            html += '<div class="example-header">';
                            html += '<img src="'+ v.company_logo +'" alt="">';
                            html += '<span class="cust-name">'+ v.company_name +'</span>';
                            html += '<a href="'+ v.official_website +'"><span class="icon ion-ios-arrow-right"></span></a>';
                            html += '</div>';
                            html += '<div class="example-info">';

                            if ( v.product ){
                            html += '<div class="example-info-list">';
                            html += '<span class="name-list">E-commerce</span>';
                                html += '<span class="sub-list">';
                                $.each(v.product,function (i,j) {
                                    if ( i<v.product.length && v.product.length > 1 && i != (v.product.length-1) ) {
                                        html += j+'、'
                                    } else {
                                        html += j
                                    }

                                });
                                html += '</span>';
                            html += '</div>';
                            }

                            html += '<div class="example-info-list">';
                            html += '<span class="name-list">Online</span>';
                            html += '<span class="sub-list">'+ v.online_date +'</span>';
                            html += '</div>';
                            html += '<div class="example-info-list">';
                            html += '<span class="name-list">Website</span>';
                            html += '<span class="sub-list">'+ v.show_website_url +'</span>';
                            html += '</div>';
                            html += '</div>';
                            html += '<div class="clear"></div>';
                            html += '<p class="example-description">'+ v.remark +'</p>';
                            html += '<div class="two-code">';
                            html += '<img src="'+ v.wx_qr_code +'" alt="">';
                            html += '</div>';
                            html += '</li>';
                        });
                        example.page++;
                        example.totalPages = Math.ceil(res.total/example.rp);
                        Shop.loadding(example.page, example.totalPages);
                        $('#not-result').html('');
                        $('.example').append(html);

                    } else {
                        $('.example').html( Shop.noContent('抱歉，暂无客户案例') )
                    }
                    example.sub = 0;
                }
            })
        }
    };

    example.example_list();

    $(window).scroll(function() {
        //当内容滚动到底部时加载新的内容
        if ($(this).scrollTop() + $(window).height() >= $(document).height() && example.page <= example.totalPages && example.sub == 0 ) {
            example.example_list();
            example.sub = 1;
        }
    });

    </script>
@endsection