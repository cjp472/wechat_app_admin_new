<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';

$tabData = ['tabTitle' => 'openApiSetting'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountSetting/openApiSetting.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/accountSetting/openApiSetting.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')

    @include("admin.accountSetting.baseTab", $tabData)

    {{--标题--}}
    {{--<div class="header" style="border-bottom: 1px solid #ECECEC;">--}}
        {{--<ul>--}}
            {{--<li><a href="/interfacesetting">手机预览</a></li>--}}
            {{--<li style="border-bottom:2px solid #2a75ed;"><a href="/sharesetting">分享配置</a></li>--}}
            {{--@if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category", "version_type"))--}}
                {{--<li><a href="/manage_function">功能管理</a></li>--}}
            {{--@endif--}}
        {{--</ul>--}}
    {{--</div>--}}

    <div class="content">

        <div class="inputArea clearfix">
            <label for="wx_share_title" class="inputLabel">小鹅PC链接 <span class="necess">*</span></label>
            <input type="text" class="inputDefault long" id="webUrl" value="{{$data->web_jump_url}}" placeholder="请输入小鹅PC链接"
                @if($data->need_jump==0) disabled @endif/>
        </div>
        <div class="radiobox clearfix">
            <div class="radioBtn radioBtn1">
                <input class="with-gap" id="openWebUrl" name="group1" type="radio" @if($data->need_jump==1) checked @endif>
                <label id="openWebUrlLabel" for="openWebUrl" class="columnShow">
                    开启
                </label>
            </div>
            <div class="radioBtn radioBtn1">
                <input class="with-gap" id="closeWebUrl" name="group1" type="radio" @if($data->need_jump==0) checked @endif>
                <label id="closeWebUrlLabel" for="closeWebUrl" class="columnShow">
                    关闭
                </label>
            </div>
        </div>

        <div class="inputArea clearfix">
            <label for="wx_share_title" class="inputLabel">消息推送链接 <span class="necess">*</span></label>
            <input type="text" class="inputDefault long" id="pushUrl" value="{{$data->msg_push_url}}" placeholder="请输入消息推送链接"
                @if($data->need_push==0) disabled @endif/>
        </div>
        <div class="radiobox clearfix">
            <div class="radioBtn radioBtn1">
                <input class="with-gap" id="openPushUrl" name="group2" type="radio" @if($data->need_push==1) checked @endif>
                <label id="openPushUrlLabel" for="openPushUrl" class="columnShow">
                    开启
                </label>
            </div>
            <div class="radioBtn radioBtn1">
                <input class="with-gap" id="closePushUrl" name="group2" type="radio" @if($data->need_push==0) checked @endif>
                <label id="closePushUrlLabel" for="closePushUrl" class="columnShow">
                    关闭
                </label>
            </div>
        </div>

        <div class="inputArea clearfix">
            <label for="wx_share_title" class="inputLabel">App_Secret <span class="necess">*</span></label>
            <input type="text" class="inputDefault long" id="appSecret" value="{{$data->app_secret}}" placeholder="请输入微信App_Secret"/>
        </div>

        <div class="bottomLine"></div>
        <button type="button" id="save" class="btnMid btnBlue btn-save">保存</button>
    </div>

    <input type="hidden" id="xcx_app_id" value="{{session("app_id")}}" />

@stop
