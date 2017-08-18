<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    <link rel="stylesheet" href="../css/admin/marketing/marketing1.css?{{env('timestamp')}}">
    {{--弹出提示--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
    {{--短链接样式--}}
    <link rel="stylesheet" href="../css/admin/marketing/shortLink.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    {{--弹出提示--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{--表单检查--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
    {{--剪贴板--}}
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/marketing/shortLink.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    <div class="pageTopTitle"><a href="/marketing">营销中心</a> > 短链接生成</div>
    <div class="shortVessel">
        <div class="">
            <div class="shortTitle">请输入长链接</div>
            <input class="shortInput" type="text" placeholder="请输入长链接">
            <button class="btnBlue btnMid compress">压缩一下</button>
            <div>
                <span class="shortTip">短链接生成功能可以帮您压缩小鹅通链接，以便制作二维码和传播分享</span>
            </div>
        </div>
        <div class="displayVessel">
            <div>
                {{--<span class="success">压缩成功</span>--}}
                {{--<div class="shortContent">生成的短链接：--}}
                    {{--<span class="shortUrl">http://xiaoe_tech.com</span>--}}
                    {{--<a data-clipboard-text="urltest" class="clip copyHref">复制链接</a>--}}
                {{--</div>--}}
                {{--<div class="shortContent">链接二维码：--}}
                    {{--<div class="frame" >--}}
                        {{--<div id="miniCode"></div>--}}
                    {{--</div>--}}
                    {{--<a class="clip">下载二维码</a>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
@stop


@section('base_modal')

@stop
