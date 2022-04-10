@extends('webi.index')

@section('css')
    <link rel="stylesheet" href="/css/webi/bi.wap.comm.css">
@endsection

@section('content')

    <div class="mian">
        <ul>
            @if(isset($group))
                @foreach($group as $g)
                    <li id="group_{{$g['group_id']}}">
                        <div class="mian-img">
                            <img src="/images/webi/bi/icon-order.png" alt="">
                        </div>
                        <div class="main-text">
                            <p>{{$g['group_name']}}</p>
                            <p class="text">{{$g['group_name']}}</p>
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
        //绑定分组查询事件
        $.each($('.mian ul li'), function (k, v) {
            $(this).on('click', function () {
                var group_id = $(this).attr('id').substring(6);
                self.location = "/webi/wap/group/bi/list?group_id=" + group_id;
            })
        });
    })();
</script>
@endsection
