<?php
$pageData = [];
$pageData['sideActive'] = 'create_content';
$pageData['barTitle'] = '新增内容';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}" >
    <link type=text/css rel="stylesheet" href="../css/external/jquery.nice-file-input.css?{{env('timestamp')}}">
    <link href="../css/admin/createContent.css?{{env('timestamp')}}" rel="stylesheet">
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/jquery.nice-file-input.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/createContent.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    <div class="content_title">新增知识内容</div>
    <div class="content">
        <div class="content_item right_margin" onclick="window.location.href = '/audio_create'">
            <div class="border_div">
                <div><img src="../images/icon_music.png"/></div>
                <div class="item_title">音频</div>
                {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button">立即新增</button>--}}
                {{--</div>--}}
            </div>
        </div>
       <div class="content_item right_margin"
            @if($video_upload<=$uploadmax) onclick="window.location.href  = '/video_create'"
            @else onclick="showErrorToast('限量每天新增{{$uploadmax}}个视频，敬请明天再传！')"
               @endif>
            <div class="border_div">
                <div><img src="../images/icon_video.png"/></div>
                <div class="item_title">视频</div>
                {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button">立即新增</button>--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="content_item right_margin" onclick="window.location.href = '/article_create' ">
            <div class="border_div">
                <div><img src="../images/icon_tuwen.png"/></div>
                <div class="item_title">图文</div>
                {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button" >立即新增</button>--}}
                {{--</div>--}}
            </div>
        </div>
        @if(\App\Http\Controllers\Tools\AppUtils::isWhiltList(\App\Http\Controllers\Tools\AppUtils::getAppID()))
        <div class="content_item right_margin" onclick="window.location.href  = '/addalive'">
            <div class="border_div">
                <div><img src="../images/icon_live.png" width="20px" height="20px"/></div>
                <div class="item_title">直播</div>
                {{--<div style="margin-top: 20px">--}}
                {{--<button class="create_button">立即新增</button>--}}
                {{--</div>--}}
            </div>
        </div>
        @endif
        @if(\App\Http\Controllers\Tools\AppUtils::isWhiltList(\App\Http\Controllers\Tools\AppUtils::getAppID()))
            <div class="content_item right_margin mrgtop" onclick="window.location.href = '/package_create'">
                <div class="border_div">
                    <div><img src="../images/icon_zhuanlan.png"/></div>
                    <div class="item_title">专栏</div>
                    {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button">立即新增</button>--}}
                    {{--</div>--}}
                </div>
            </div>
        @else
            <div class="content_item right_margin" onclick="window.location.href = '/package_create'">
                <div class="border_div">
                    <div><img src="../images/icon_zhuanlan.png"/></div>
                    <div class="item_title">专栏</div>
                    {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button">立即新增</button>--}}
                    {{--</div>--}}
                </div>
            </div>
        @endif
        <div style="float:left;width:100%;height:2px;"></div>
    </div>
    <div style="float:left;width:100%;height:2px;"></div>
    <div style="clear: both"></div>
    <div class="other_title">新增运营内容</div>
    <div class="content">
        <div class="content_item right_margin" onclick="window.location.href = '/banner_create'">
            <div class="border_div">
                <div><img src="../images/icon_lunbotu.png"/></div>
                <div class="item_title">轮播图</div>
                {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button">立即新增</button>--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="content_item" onclick="window.location.href = '/messageadd'">
            <div class="border_div">
                <div><img src="../images/icon_message.png"/></div>
                <div class="item_title">消息</div>
                {{--<div style="margin-top: 20px">--}}
                    {{--<button class="create_button">立即新增</button>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
@stop

