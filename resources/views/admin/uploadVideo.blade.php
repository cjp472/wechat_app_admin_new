<?php
$pageData = [];
$pageData['sideActive'] = 'create_content';
$pageData['barTitle'] = '内容创建';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet">
    {{--文件选择框美化--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery.nice-file-input.css">
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
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

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>

    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="../ueditor/ueditor.all.js"></script>

    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    <script type="text/javascript" src="../js/admin/videoUpload.js?{{env('timestamp')}}"></script>


    {{--云点播视频上传--}}
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js?{{env('timestamp')}}" charset="utf-8"></script>

    <script>
        secretId = "{{env('SecretId')}}";
        sigUrl = "{{ env('SignUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        transcodeNotifyUrl = "{{env('TransNotifyUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";

    </script>

@endsection


@section('base_mainContent')
    <div class="upload_title">
        <span>新增视频</span>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">视频名称</div>
        <div class="upload_input_div">
            <input class="form-control long" placeholder="请输入视频名称" id="resource_title" />
            <div class="upload_input_msg">
                <span>请设置视频的名称,建议不要太长,控制在14个汉字以内。</span>
            </div>
        </div>
    </div>


    <div class="upload_item_div">
        <div class="upload_item_title">收费形式</div>
        <div class="upload_input_div">
            <div class="upload_type" id="package_select">专栏</div>
            <div class="upload_type border_blue" id="single_select">单卖</div>
            <div class="upload_type" id="free_select">免费</div>

            <div id="price_div"><input id="resource_price" class="form-control long"
                                       placeholder="付费金额,例如100.00"/><span class="price_unit">元</span></div>
            <div class="searchSelectArea hide" style="width: 300px">
                @if( count($package_list) > 0)
                <select class="form-control long hide" id="resource_package">
                    @foreach($package_list as $key=>$package)
                        <option value="{{$package->id}}" about="{{ $package->price }}" state="{{ $package->state }}">
                            {{$package->name}}</option>
                    @endforeach
                </select>
                @else
                <button type='button' class="btn btn-default long hide" style="margin-left: 0px;"
                id="resource_package" onclick="toAddPackage()">您还没有专栏，点击新增专栏</button>
                @endif
            </div>
            <div class="package_side_pay hide">
                {{--<input id="single_pay" class="single_pay" name="single_pay" type="radio" value="1" />--}}
                <div id="checkbox-img" class="checkbox-img" checked-state="unchecked" style="background-position: -78px 0px;"></div>
                <span style="margin-left: 4px">专栏外单卖</span>
                <div id="single_price_div" class="single_price_div hide">
                    <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" >
                    <span >元</span>
                </div>
            </div>

            <div class="upload_input_msg">
                <span>请选择你的产品类型,目前支持:专栏、单卖、免费。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">视频封面</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div">
                    <input type="file" class="pic_input" id="resource_pic" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                    <img class="pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/audio_cover_750_560.png" class="pic_add"/>
                    <img class="pic_show hide"/>
                </div>
            </div>
            <div class="upload_input_msg">
                <span>(注：像素750*560或者宽高比与此相同)</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">视频贴片</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div">
                    <input type="file" class="pic_input" id="patch_pic" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                    <img class="pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/audio_cover_750_560.png" class="pic_add"/>
                    <img class="pic_show hide"/>
                </div>
            </div>
            <div class="upload_input_msg">
                <span>(注：可不填,不填则用视频封面作为贴片,像素尺寸750*420)</span>
            </div>
        </div>
    </div>


    <div class="upload_item_div">
        <div class="upload_item_title">视频上传</div>
        <div class="upload_input_div">
        @if($video_upload<=$uploadmax)
            {{--<input type="file" class="nicefile" name="audio_file" id="public_audio"/>--}}
            <button id="video_file" class="btn btn-default" style="margin-left: 0">选择文件</button>
            <div id="progress_show"></div>
        @else
            <input type="text" class="form-control" value="限量每天新增{{$uploadmax}}个视频，敬请明天再传！" readonly style="width: 396px;font-weight: bold; border: 0;">
        @endif
        </div>

    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">视频描述</div>
        <div class="upload_input_div">
            <script id="resource_desc"  type="text/plain"></script>
            <script type="text/javascript">
                var ue = UE.getEditor('resource_desc',ueditor_config);
            </script>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">上架时间</div>
        <div class="upload_input_div">
            <input class="form-control long" id="start_time" readonly />
            <div class="upload_input_msg">
                <span>请设置上架时间</span>
            </div>
        </div>
    </div>

    @if( \App\Http\Controllers\Tools\AppUtils::IsPageVisual('message_push', 'version_type') )
    <div class="upload_item_div" style="display:none;"  id="serviceContent" data-state="0" data-setting="{{$isHadSetTemp}}">
        <div class="upload_item_title">服务号通知</div>
        <div class="upload_input_div">
            <div class="rdoBox">
                <input id="openService" class="rdo" type="radio" name="toggle" value="1" />
                <label for="openService" class="rdoSelf"></label>
                <label for="openService">开启</label>
            </div>
            <div class="rdoBox">
                <input id="closeService" class="rdo" type="radio" name="toggle" value="0" checked />
                <label for="closeService" class="rdoSelf"></label>
                <label for="closeService">关闭</label>
            </div>

            <div class="upload_input_msg">
                <span>开启服务号通知后，内容上架后会向已订阅专栏的用户发送服务号模板消息。</span><a target="_blank" href="/help/instructions#hp5_wx_service">什么是模板消息？</a>
            </div>
        </div>
    </div>
    @endif

    <div class="upload_button_div">
        @if($video_upload<=$uploadmax)
            <button class="btn btn-blue" onclick="uploadResource('video')">确认
            </button>
        @else
            <button class="btn btn-gray" onclick="showErrorToast('限量每天新增{{$uploadmax}}个视频，敬请明天再传！')">确认
            </button>
        @endif

    </div>

    <video style="display:none;" controls="controls" id='resource_time'
           oncanplaythrough="getResourceDuration(this)"></video>
    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
@stop
