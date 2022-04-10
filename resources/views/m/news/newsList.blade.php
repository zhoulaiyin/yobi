@extends('m.layout')

@section('css')
    <link rel="stylesheet" href="/css/m/newslist.css?v=2017032107">
@endsection

@section('content')
    <div class="content">
        <div class="news-first">
        </div>
        <ul class="news-list">

        </ul>
        <div id="examine"></div>
        <div id="not-result"></div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/js/m/common/global.js?v=2017032203"></script>
    <script type="text/javascript">
        var news ={
            sub: 0,
            page:1,
            rp:5,
            totalPages: 0,
            typle:'{{$typle}}',
            news_list:function () {
                news.sub = 1;
                if ( news.page == 1 ) {
                    Shop.wait_layer(1);
                }
                $.ajax({
                    type: 'GET',
                    url: '/mobile/news/list/search',
                    data: {
                        rp: news.rp,
                        page: news.page,
                        typle: news.typle,
                    },
                    success:function ( res ) {
                        if ( news.page == 1 ) {
                            $('.news-list').html('');
                            $('#examine').html('');
                            $('#not-result').html('');
                            Shop.close_wait_layer();
                        }
                        if ( res.data.length > 0 ){

                            $.each( res.data, function (k,v) {
                                if ( v.isTop == 1){
                                    var firsthtml = '';
                                    firsthtml +='<a href="/mobile/news/detail/'+ v.id +'"><img width="100%" src="'+ v.icon +'" alt=""></a>';
                                    firsthtml += '<div class="news-first-content">';
                                    firsthtml += '<div class="news-first-title"><a href="/mobile/news/detail/'+ v.id +'">'+ v.title +'</a></div>';
                                    firsthtml += '<p>';
                                    firsthtml += '<span>'+ v.creator +'</span>';
                                    firsthtml += '<span>'+ v.created_at +'</span>';
                                    firsthtml += '</p>';
                                    firsthtml += '</div>';
                                    $('.news-first').append(firsthtml);
                                } else {
                                    var html = '';
                                    html += '<li>';
                                    html += '<div class="fl news-list-content">';
                                    html += '<div class="news-list-title">';
                                    html += '<a href="/mobile/news/detail/'+ v.id +'">'+ v.title +'</a>';
                                    html += '</div>';
                                    html += '<p>';
                                    html += '<span>'+ v.creator +'</span>';
                                    html += '<span>'+ v.created_at +'</span>';
                                    html += '</p>';
                                    html += '</div>';
                                    html += '<div class="fr news-img">';
                                    html += '<a href="/mobile/news/detail/'+ v.id +'"><img src="'+ v.icon +'" alt=""></a>';
                                    html += '</div>';
                                    html += '<div class="clear"></div>';
                                    html += '</li>';
                                    $('.news-list').append(html);
                                }

                            });
                            news.page++;
                            news.totalPages = Math.ceil(res.total/news.rp);
                            Shop.loadding(news.page, news.totalPages);
                        } else {
                            $('.news-first').html( Shop.noContent('抱歉，暂无文章') )
                        }
                        news.sub = 0;
                    }
                })

            }
        };
        news.news_list();

        $(window).scroll(function() {
            //当内容滚动到底部时加载新的内容
            if ($(this).scrollTop() + $(window).height() >= $(document).height() && news.page <= news.totalPages && news.sub == 0 ) {
                news.news_list();
                news.sub = 1;
            }
        });

    </script>
@endsection