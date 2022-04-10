@extends('webi.tvindex')

@section('css')
<style>
    .select-text{
        height: 35px;
        width: 100%;
        background-color: #1A8BE8;
        border-radius: 50px;
        font-size: 17px;
        border: 2px solid white;
    }
    .selected-lable{
        box-shadow: 0 0 0 3px rgba(81,130,227,.15),inset 0 0 0 3px white !important;
    }
</style>
@endsection

@section('content')
    <input class="group_id" type="hidden" value="{{$group_id}}">

    {{--左侧--}}
    <div class="left-list">
        <ul>
            @if(isset($group))
                @foreach($group as $g)
                    <li class="li_{{$g['group_id']}}">
                        <div class="left-list-text" data-id="{{$g['group_id']}}" id="group_{{$g['group_id']}}">
                            <p>{{$g['group_name']}}</p>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    {{--右侧--}}
    <div class="right-list">
        <ul class="ul-master">
        </ul>
    </div>

    {{--报表li结构--}}
    <div id="master-li" style="display: none">
        <li id="master_uid" class="master">
            <div>
                <img src="/images/webi/bi/icon-order.png" alt="" title="点击可查看报表">
                <p>bi_title</p>
            </div>
        </li>
    </div>
@endsection

@section('js')
<script>

    var _G = {};//分组操作对象

    //分组下报表
    _G.list = function(g_id){
        var group_id = g_id,
            bi_id = "";

        if( $.isArray(g_id) ){//当g_id是数组是搜索页面操作
            group_id = g_id[0];
            bi_id = g_id[1];
        }
        $(".li_" + group_id).eq($(this).index()).children().addClass("select-text").parents().siblings().find('.left-list-text').removeClass("select-text");

        $.ajax({
            type: 'get',
            url: '/webi/tv/group/bi/list?group_id='+group_id,
            success: function (res) {
                if (res.code == 200) {

                    $('.ul-master').empty();

                    if (!($.isEmptyObject(res.data['master']))) {
                        var creat = _G.create_master(group_id,res.data['master']);
                        $('.ul-master').append(creat);

                        //绑定报表点击事件
                        $.each($('.master'),function (k,v){

                            $(this).on('click',function(){
                                var bi_id = $(this).attr('id').substring(7);
                                self.location = "/webi/wap/show?uid=" + bi_id;
                            });
                            //$(this).on('mouseover',function(){
                            $(this).focus(function(){
                                $(this).addClass('selected-lable').siblings().removeClass('selected-lable');
                            });

                        });

                        if( bi_id != "" ){
                            //移除之前选中的报表背景色
                            $(".ul-master li").removeClass("selected-lable");
                            //查找到的报表添加背景色
                            $("#master_"+ bi_id).addClass("selected-lable");
                        }
                    }
                }
            }
        });
    };

    //生成报表（li）
    _G.create_master = function(group_id,data){
        var master_html="";
        $.each(data, function (k, v) {

            master_html+=$('#master-li').html();
            master_html = master_html.replace(/bi_title/g,v.bi_title);
            master_html = master_html.replace(/uid/g,v.uid);

        });
        return master_html;
    };

    $(document).ready(function() {

        //绑定分组查询事件
        $.each($('.left-list-text'),function (k,v){
            $(this).on('click',function(){
                var id = $(this).attr('data-id');
                _G.list(id);
            })
        });

        //触发默认分组
        var group_id = $(".group_id").val();
        if(!$.isEmptyObject(group_id)){
            $("#group_"+group_id).trigger("click");
        }

    });

</script>

@endsection
