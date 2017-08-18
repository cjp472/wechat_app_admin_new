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
    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    <script type="text/javascript" src="../js/admin/wxaccountSetting.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')

    @include("admin.knowledgeShop.baseTab", ["tabTitle" => "wxAccountSetting"])

    <div class="content">
        <div class="inputArea">
            <label class="inputLabel" for="wx_app_name">公众号名称 <span class="necess">*</span></label>
            <input type="text" class="inputDefault long" id="wx_app_name" value="{{$name}}"
            placeholder="请输入公众号名称"/>
        </div>

        <div class="inputArea" style="height: 120px;">
            <div class="inputLabel">公众号二维码 <span class="necess">*</span></div>
            <div class="inputValue" style="position: relative;">
                {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                <label class="previewPic" for="uploadImage" style="background-image: url(../images/icon-addpic.png)">
                    <img id="reBackImg" src="{{$wxAccount->wx_qr_url_compressed or $wxAccount->wx_qr_url}}" />
                </label>
                <div class="previewInfo">
                    <div class="btnSmall xeBtnDefault coverUpbtn">
                        <span>
                            选择文件
                        </span>
                        <input id="uploadImage" accept="image/jpeg,image/png,image/gif,image/bmp" class="upLoadImage upLoadImage1" type="file"/>
                        <input type="hidden" id="wx_qr_url" type="text" />
                    </div>
                    <div class="coverUpTip">
                        图片格式为：bmp, jpeg, jpg, gif，尺寸1：1，不可大于2M。
                    </div>
                </div>
            </div>
        </div>
        <div class="bottomLine"></div>

        <button type="button" id="save" class="btnMid btnBlue">保存</button>

        <div class="previewArea">
            <div class="previewH4">常驻引导关注入口</div>
            <img src="../images/wx_app_info1.jpg" class="previewImg" alt="常驻引导关注入口" title="常驻引导关注入口" />
            <div class="previewH4">购买/接受赠送/邀请码开通引导关注</div>
            <img src="../images/wx_app_info2.jpg" class="previewImg" alt="购买/接受赠送/邀请码开通引导关注" title="购买/接受赠送/邀请码开通引导关注" />
        </div>
    </div>

    <input type="hidden" id="xcx_app_id" value="{{session("app_id")}}" />

@stop

