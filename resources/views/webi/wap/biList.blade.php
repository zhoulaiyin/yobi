@extends('webi.index')

@section('css')
    <link rel="stylesheet" href="/css/webi/bi.wap.comm.css">
@endsection

@section('content')

    <div class="mian">
        <ul>
            @if(isset($master))
                @foreach($master as $g)
                    <li id="master_{{$g['uid']}}">
                        <div class="mian-img">
                            <img src="/images/webi/bi/icon-order.png" alt="">
                        </div>
                        <div class="main-text">
                            <p>{{$g['bi_title']}}</p>
                            <p class="text">{{$g['bi_title']}}</p>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
    </div>
@endsection
@section('js')
    <script>
        (function(){
            //绑定报表点击查询事件
            $.each($('.mian ul li'), function (k, v) {
                $(this).on('click', function () {

                    var bi_id = $(this).attr('id').substring(7);
                    self.location = "/webi/wap/show?uid=" + bi_id;
                })
            });

        })();
    </script>
@endsection
