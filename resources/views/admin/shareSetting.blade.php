<?php
$pageData = [];
$pageData['sideActive'] = 'knowledgeShop';
$pageData['barTitle'] = '店铺设置';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/shareSetting.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    <script type="text/javascript" src="../js/admin/shareSetting.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')

    @include("admin.knowledgeShop.baseTab", ["tabTitle" => "shareSetting"])

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
        <div class="inputArea">
            <label for="wx_share_title" class="inputLabel">分享链接标题 <span class="necess">*</span></label>
            <input type="text" class="inputDefault long" id="wx_share_title" value="{{$share->wx_share_title}}"
            placeholder="请输入分享链接标题"/>
        </div>
        <div class="inputArea">
            <label for="wx_share_content" class="inputLabel">分享链接描述 <span class="necess">*</span></label>
            <input type="text" class="inputDefault long" id="wx_share_content" value="{{$share->wx_share_content}}"
            placeholder="请输入分享链接描述"/>
        </div>
        <div class="inputArea" style="height: 120px;">
            <div class="inputLabel">分享链接配图 <span class="necess">*</span></div>
            <div class="inputValue" style="position: relative;">
                {{-- @if(empty($share->wx_share_image))
                <input type="file" id="wx_share_image" accept="image/jpeg,image/png,image/gif,image/bmp" />
                <img class="picClose hide" src="../images/icon_close.png"/>
                <img src="../images/share_upload.png" class="picAdd"/>
                <img class="picShow hide"/>
                @else
                <input type="file" id="wx_share_image" accept="image/jpeg,image/png,image/gif,image/bmp" />
                <img class="picClose" src="../images/icon_close.png"/>
                <img src="../images/share_upload.png" class="picAdd hide"/>
                <img class="picShow" src="{{$share->wx_share_image}}"/>
                @endif --}}

                {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                <label class="previewPic" for="uploadImage" style="background-image: url(../images/icon-addpic.png)">
                    <img id="reBackImg" src="{{$share->wx_share_image_compressed or $share->wx_share_image}}" />
                </label>
                <div class="previewInfo">
                    <div class="btnSmall xeBtnDefault coverUpbtn">
                        <span>
                            选择文件
                        </span>
                        <input id="uploadImage" accept="image/jpeg,image/png,image/gif,image/bmp" class="upLoadImage upLoadImage1" type="file"/>
                    </div>
                    <div class="coverUpTip">

                    </div>
                </div>
            </div>
        </div>


        <div class="bottomLine"></div>

        <button type="button" id="save" class="btnMid btnBlue">保存</button>

        <div class="previewArea">
            <div class="previewH4">分享到微信好友/群示例</div>
            <img src="../images/share1.png" class="previewImg" alt="好友分享示例" title="好友分享示例" />
            <div class="previewH4">分享到朋友圈示例</div>
            <img src="../images/share2.png" class="previewImg" alt="朋友圈分享示例" title="朋友圈分享示例" />
        </div>
    </div>

    <input type="hidden" id="xcx_app_id" value="{{session("app_id")}}" />

@stop

