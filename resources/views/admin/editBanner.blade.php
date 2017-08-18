<?php
$pageData = [];
$pageData['sideActive'] = 'create_content';
$pageData['barTitle'] = '轮播图编辑';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}" rel="stylesheet">
    {{--文件选择框美化--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery.nice-file-input.css?{{env('timestamp')}}">
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/upload.css?{{env('timestamp')}}">
@endsection

@section('page_js')

    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/external/jquery.nice-file-input.js?{{env('timestamp')}}"></script>

    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/upload.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/editBannerAdmin.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="upload_title">
        <span>编辑轮播图</span>
    </div>

    <span style="display: none" id="data"  data-id ={{$banner_detail->id}}></span>

    <div class="upload_item_div">
        <div class="upload_item_title">上传图片</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div_banner">
                    <input type="file" class="pic_input_banner pic_input" id="resource_pic" name="audio_pic"/>
                    <img class="pic_close_banner pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/addbanner.png" class="pic_add hide"/>
                    <img class="pic_show " src="@if($banner_detail->img_url_compressed){{$banner_detail->img_url_compressed}}@else{{ $banner_detail->image_url }}@endif"/>
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
            <input class="form-control long" placeholder="请输入轮播图名称" id="resource_title" value="{{ $banner_detail->title }}" accept="image/jpeg,image/png,image/gif,image/bmp"/>
            <div class="upload_input_msg">
                <span>请设置轮播图的名称,建议不要太长,控制在14个汉字以内。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">上架时间</div>
        <div class="upload_input_div">
            <input class="form-control long" id="start_time" value="{{ $banner_detail->start_at }}"/>
            <div class="upload_input_msg">
                <span>请设置上架时间。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">下架时间</div>
        <div class="upload_input_div">
            <input class="form-control long" id="stop_time" value="{{ $banner_detail->stop_at }}"/>
            <div class="upload_input_msg">
                <span>可设置下架时间。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">跳转链接</div>
        <div class="upload_input_div">
            <div style="float: left">
                <select class="form-control" id="type_selector">

                        <option value="audio"@if($banner_detail->skip_type == 2) selected="selected"@endif>音频</option>
                        <option value="video"@if($banner_detail->skip_type == 3) selected="selected"@endif>视频</option>
                        <option value="image_text"@if($banner_detail->skip_type == 1) selected="selected"@endif>图文</option>
                        <option value="alive"@if($banner_detail->skip_type == 4) selected="selected"@endif>直播</option>
                        <option value="package"@if($banner_detail->skip_type == 6) selected="selected"@endif>专栏</option>
                        <option value="h5"@if($banner_detail->skip_type == 5) selected="selected"@endif>外部链接</option>
                        <option value="no_jump"@if($banner_detail->skip_type == 0) selected="selected"@endif>无跳转</option>
                </select>
            </div>
            <div style="float: left;margin-left: 15px">
                <select class="form-control" id="sub_selector">
                    @if($resource_list != "")
                        @foreach($resource_list as $key=>$value)
                            @if($banner_detail->skip_target == $value->id)
                                <option selected value="{{ $value->id }}">{{ $value->title }}</option>
                            @else
                                <option value="{{ $value->id }}">{{ $value->title }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <input class="form-control hide" style="width: 200px" id="sub_input" value="{{ $banner_detail->skip_target }}" >
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">显示顺序</div>
        <div class="upload_input_div">
            <select class="form-control" id="view_order">
                @if($banner_detail->weight == 10)
                    <option value="first" selected="selected">第一屏</option>
                    <option value="second">第二屏</option>
                    <option value="third">第三屏</option>
                @elseif($banner_detail->weight == 9)
                    <option value="first">第一屏</option>
                    <option value="second" selected="selected">第二屏</option>
                    <option value="third">第三屏</option>
                @elseif($banner_detail->weight == 8)
                    <option value="first">第一屏</option>
                    <option value="second">第二屏</option>
                    <option value="third" selected="selected">第三屏</option>
                @else
                    <option value="first">第一屏</option>
                    <option value="second">第二屏</option>
                    <option value="third">第三屏</option>
                @endif
            </select>
        </div>
    </div>

    {{--<div class="upload_button_div">--}}
        {{--<button class="btn btn-blue" onclick="uploadBanner()">确认新增--}}
        {{--</button>--}}
    {{--</div>--}}

    <div class="upload_button_div">
        <button class="btn btn-blue" onclick="saveBanner()">保存
        </button>
    </div>

    <div class="cancel_button_div">
        <button class="btn btn-blue" onclick="relistUrl('/getBannerList')">取消
        </button>
    </div>
    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
@stop

