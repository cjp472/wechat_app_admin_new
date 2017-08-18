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
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js"></script>
    <script type="text/javascript" src="../ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="../ueditor/ueditor.all.js"></script>
    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>

    <script type="text/javascript" src="../js/admin/ArticleUpload.js?{{env('timestamp')}}"></script>
    <script>
        $(document).ready(function () {
            $.cookie('content_create')? setTopUrlInfo('content_ceate') : setTopUrlInfo('article_listop');
        });
    </script>
@endsection


@section('base_mainContent')
    <div class="upload_title">
        <span>新增图文</span>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">图文名称</div>
        <div class="upload_input_div">
            <input class="form-control long" placeholder="请输入标题名称" id="resource_title"/>
            <div class="upload_input_msg">
                <span>请设置音频的名称,建议不要太长,控制在14个汉字以内。</span>
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
                <div id="single_price_div" class="single_price_div hide" >
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
        <div class="upload_item_title">图文封面</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div">
                    <input type="file" class="pic_input" id="resource_pic" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                    <img class="pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/audio_cover_750_560.png" class="pic_add"/>
                    <img class="pic_show hide"/>
                </div>
                <img id='icon_uploadPic' class="pic_upload hide" src="../images/icon_close.png"/>
                <img id='icon_uploadPic_success' class="pic_upload_success hide" src="../images/icon_success.png"/>
            </div>
            <div class="upload_input_msg">
                <span>(注：像素750*560或者宽高比与此相同)</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">内容</div>
        <div class="upload_input_div">
            <script id="container"  type="text/plain"></script>
            <script type="text/javascript">
                var ue = UE.getEditor('container',ueditor_config);
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
    <div class="upload_item_div" style="display:none;" id="serviceContent" data-state="0" data-setting="{{$isHadSetTemp}}">
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
        <button class="btn btn-blue" onclick="uploadArticle()">确认
        </button>
    </div>


    <audio style="display:none;" id='resource_time' oncanplaythrough="getResourceDuration(this)"></audio>

    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
@stop

