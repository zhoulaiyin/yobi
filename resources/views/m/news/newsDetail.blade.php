@extends('m.layout')

@section('css')
    <link rel="stylesheet" href="/css/m/newsdetail.css?v=2017032108">
@endsection

@section('content')
    <div class="content">
        <div class="detail">
            <div class="detail-top">
                <div class="title">{!! $data['title'] !!}</div>
                <p>
                    <span>{!! $data['creator'] !!}</span>
                    <span>{!! $data['created_at'] !!}</span>
                </p>
            </div>
            <div class="detail-content">
                {!! $data['content'] !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript"></script>
@endsection