@extends('webi.bishop')

@section('title')
    {{ $bi_master['bi_title'] }}
@endsection

@section('js')
    <script>

        (function(){

            $.ajax({
                type: 'get',
                url: '/webi/list/master/get?uid={{ $uid }}',
                dataType: 'JSON',
                data: {},
                timeout: 60000,
                success: function( o ) {
                    WeBI.op.a('parent_main', o.data, document.getElementById('bi_content_html').innerHTML,2);
                }
            });

        })();

    </script>
@endsection