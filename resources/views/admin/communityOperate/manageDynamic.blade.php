<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)

@section("page_css")

    <link type="text/css" rel="stylesheet" href="../css/admin/communityOperate/manageDynamic.css?{{env('timestamp')}}">

@stop

@section('ahead_js')
    {{--文本编辑器--}}
    <script type="text/javascript" src="../ueditor/ueditor.config.js" ></script>
    <script type="text/javascript" src="../ueditor/ueditor.all.min.js?{{env('timestamp')}}" ></script>
    {{--秀米sdk--}}
    <script src="../ueditor/xiumi-ue-dialog-v5.js" type="text/javascript"></script>
@stop

@section("page_js")
    {{--腾讯云上传js--}}
    <script type="text/javascript" src="../sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="../js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--上传工具类--}}
    <script type="text/javascript" src="../js/admin/utils/upload.js?{{env('timestamp')}}"></script>
    {{--表单检查工具类--}}
    <script type="text/javascript" src="../js/admin/utils/formCheck.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/communityOperate/manageDynamic.js?{{env('timestamp')}}"></script>

@stop

@section("base_mainContent")

    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>     {{--用于 js 获取 appId--}}

    <input id="admin_data" type="hidden" data-type="{{$type}}"
           @if($type)
           data-community_id="{{$dynamic_info->community_id}}"
           @else
           data-community_id="{{$community_id}}"
            @endif
    >

    <div class="pageTopTitle">
        <a>社群运营</a>
        &gt;
        <a href="/smallCommunity/communityList">小社群</a>
        &gt;
        <a  @if($type)
            href="/smallCommunity/dynamicList?community_id={{$dynamic_info->community_id}}"
            @else
            href="/smallCommunity/dynamicList?community_id={{$community_id}}"
                @endif
        >社群详情
        </a>
        &gt;
        发布动态
    </div>

    <div class="baseItemWrapper baseItemTitle">
        <div class="baseTitleLeft">
            标题名称<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault inputDynamicTitle" id="dynamicTitle" placeholder="请输入标题名称"
                   @if($type) value="{{$dynamic_info->title}}" @endif >
        </div>
    </div>
    <div class="baseItemWrapper baseItemContent">
        <div class="baseTitleLeft">
            动态内容<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            @if($type)
                <input id="rubbish" type="hidden" value="{{$dynamic_info->org_content}}" />
            @endif
            <div class="resDescribe">
                <script id="container" type="text/plain"></script>
                <script type="text/javascript">
                    var ue = UE.getEditor('container', ueditor_config);
                    @if($type)                          {{--判断是否需要初始化--}}
                        ue.ready(function () {
                        ue.setContent($("#rubbish").val());
                    });
                    @endif
                </script>
            </div>
        </div>
        <div class="waves-effect btnSmall xeBtnDefault coverUpbtn" id="preview" style="margin-left: 40px;margin-right: 0;">
            预览
        </div>
    </div>

    <div class="baseItemWrapper baseItemFileUpload">
        <div class="baseTitleLeft">

        </div>
        <div class="baseContentRight">
            <div class="uploadFileBtnWrapper">
                <div class="xeBtnDefault btnMid uploadFileBtn" id="uploadFile">上传文件</div>
                <div class="uploadFileTip">仅支持pdf格式，大小限制为20Mb</div>

                <input class="selectPdfFileInput" id="selectPdfFile" type="file" accept="application/pdf"/>
                <input type="text" class="uploadedFileUrlInput hide" id="uploadedFileUrl"
                       @if($type && !empty($dynamic_info->file_url))
                       data-file_url="{{$dynamic_info->file_url}}"
                       data-file_name="{{$dynamic_info->file_name}}"
                       data-file_size="{{$dynamic_info->file_size}}"
                       @else
                       data-file_url=""
                       data-file_name=""
                       data-file_size=""
                        @endif
                />
            </div>

            <div class="uploadBox uploadBoxPdf" style="@if($type && !empty($dynamic_info->file_url)) display: block; @else display: none; @endif">
                <div class="pdfFileIconWrapper">
                    <img class="pdfFileIcon" src="/images/admin/communityOperate/pdf_icon.png">
                </div>
                <div class="uploadProgress">
                    <div class="uploadPTitle uploadPdfName">
                        @if($type && !empty($dynamic_info->file_url)) {{$dynamic_info->file_name}} @endif
                    </div>
                    <div class="uploadPSize uploadPdfSize hide">{{--暂时先隐藏--}}
                        (<span>@if($type && !empty($dynamic_info->file_url)){{$dynamic_info->file_size}}@endif</span>M)
                    </div>
                    <div class="uploadPLine uploadPLinePdf" style="@if($type && !empty($dynamic_info->file_url)) display: none; @else display: block; @endif">
                        <div class="uploadPLineActive uploadPLineActivePdf">
                        </div>
                    </div>
                </div>
                <div class="uploadPercent uploadPercentPdf" id="deleteUploadedPdfFile">
                    @if($type && !empty($dynamic_info->file_url))删除@endif
                </div>
            </div>


        </div>
    </div>
    @if($type == 0 && session('version_type') > 1 )
    <div class="baseItemWrapper baseItemServerInfo">
        <div class="baseTitleLeft">
            服务号通知<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight" id="service_radio" data-message-push="{{$is_message_push}}" data-set-temp="{{$is_set_temp}}" data-collection="{{session('is_collection')}}">
            <div class="selectContent">
                <div class="selectItem">
                    <input id="openItem" type="radio" class="with-gap" name="serverInfo" value="1"
                    @if($has_push == 3)
                        disabled
                    @endif
                    >
                    <label for="openItem" value="1"
                    @if($has_push == 3)
                        style="color: #dddddd;"
                    @endif
                    >开启通知</label>
                    <div class="remindNum">(今日已发送<span class="starIcon">{{$has_push}}</span>次,还可以发送<span class="starIcon">{{$valid_push}}</span>次)</div>
                    <div class="listMsg">开启服务号后，该动态会向已加入该小社群的用户发送服务号通知</div>
                    <a target="_blank" href="
                           @if(session('is_collection') == 0)/help/instructions#hp5_wx_service
                           @else/helpCenter/problem?first_id=44&second_id=45&document_id=doc_598dcf69a8367_8AjB9
                           @endif
                        ">什么是服务号通知？</a>
                </div>
                <div class="selectItem">
                    <input id="closeItem" type="radio" class="with-gap" name="serverInfo" value="0"
                           checked
                    >
                    <label for="closeItem" value="0">关闭</label>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="horizontalDivideLine"></div>

    <div class="btnBlue btnMid releaseDynamicBtn" id="releaseDynamic">@if($type)保存编辑@else发布动态@endif</div>

    <div class="baseItemTip">
        (管理台发布的动态将以群主身份发送)
    </div>
@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')

@stop