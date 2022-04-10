@extends('webi.bishop')

@section('js')

<script type="text/javascript" src="/js/webi/app.webi.js"></script>

<script type="text/javascript">
    var master={};

    master.uid = {!! '"'.$uid.'"' !!};

    master.url = "/webi/design/operation/get/{{ $uid }}";

    master.module = function(){

            $.ajax({
                type: 'get',
                url: master.url,
                dataType: 'JSON',
                success: function( o ) {
                    //单一报表
                    var win = BI.browser.findDimensions();
                    o.data.attribute_json.top = 10;
                    o.data.attribute_json.height= parseInt(win.winHeight)-20;
                    o.data.attribute_json.top_percent = 0.0001;
                    o.data.attribute_json.height_percent = 0.70;
                    o.data.attribute_json.width_percent = 0.98;
                    o.data.attribute_json.left_percent = 0.01;
                    o.data.module = {};
                    o.data.module[master.uid] = {
                        "bi_json": o.data.bi_json,
                        "db_json": o.data.db_json,
                        "attribute_json": o.data.attribute_json,
                        "chart_json": o.data.chart_json
                    };

                    WeBI.op.a('parent_main', o.data, document.getElementById('bi_content_html').innerHTML,2);
                    WeBI.op.create_module(master.uid,o.data);
                }
            });

        };

    master.module();

</script>
@endsection