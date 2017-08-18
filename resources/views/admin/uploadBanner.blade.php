<?php
$pageData = [];
$pageData['sideActive'] = 'knowledgeShop';
$pageData['barTitle'] = '店铺设置';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet">
    {{--文件选择框美化--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery.nice-file-input.css">
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/upload.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="../js/external/jquery.nice-file-input.js"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>


    <script type="text/javascript" src="../js/external/xcConfirm.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>

    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/upload.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/bannerAdmin.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="upload_title">
        <span>新增轮播图</span>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">上传图片</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div_banner">
                    <input type="file" class="pic_input_banner pic_input" id="resource_pic" name="audio_pic" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                    <img class="pic_close_banner pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/addbanner.png" class="pic_add"/>
                    <img class="pic_show hide"/>
                </div>
            </div>
            <div class="upload_input_msg">
                <span>(注：像素750*280或者宽高比与此相同)</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">图片名称</div>
        <div class="upload_input_div">
            <input class="form-control long" placeholder="请输入轮播图名称" id="resource_title"/>
            <div class="upload_input_msg">
                <span>请设置轮播图的名称,建议不要太长,控制在14个汉字以内。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">上架时间</div>
        <div class="upload_input_div">
            <input class="form-control long" id="start_time"/>
            <div class="upload_input_msg">
                <span>请设置上架时间。</span>
            </div>
        </div>
    </div>
    <div class="upload_item_div">
        <div class="upload_item_title">下架时间</div>
        <div class="upload_input_div">
            <input class="form-control long" id="stop_time"/>
            <div class="upload_input_msg">
                <span>可设置下架时间。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">跳转链接</div>
        <div class="upload_input_div">
            <div style="float: left">
                <select class="form-control"  id="type_selector">
                    <option value="audio">音频</option>
                    <option value="video">视频</option>
                    <option value="image_text">图文</option>
                    <option value="alive">直播</option>
                    <option value="package">专栏</option>
                    <option value="h5">外部链接</option>
                    <option value="no_jump">无跳转</option>
                </select>
            </div>
            <div style="float: left;margin-left: 15px">
                <select class="form-control"  id="sub_selector">
                    @foreach($audioList as $key=>$value)
                        <option value="{{ $value->id }}">{{ $value->title }}</option>
                    @endforeach
                </select>
                <input class="form-control hide" style="width: 200px" id="sub_input" />
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">显示顺序</div>
        <div class="upload_input_div">
            <select class="form-control" id="view_order">
                <option value="first">第一屏</option>
                <option value="second">第二屏</option>
                <option value="third">第三屏</option>
            </select>
        </div>
    </div>

    <div class="upload_button_div">
        <button class="btn btn-blue" onclick="uploadBanner()">确认新增
        </button>
    </div>
    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
@stop

