
<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '内容分销';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--页面样式--}}
    <link href="../css/admin/chosen/manageContent.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
@stop

@section('ahead_js')
    {{--文本编辑器--}}
    <script type="text/javascript" src="../ueditor/ueditor.config.js" ></script>
    <script type="text/javascript" src="../ueditor/ueditor.all.min.js?{{env('timestamp')}}" ></script>
    {{--秀米sdk--}}
    <script src="../ueditor/xiumi-ue-dialog-v5.js" type="text/javascript"></script>
@stop

@section('page_js')
    <script src="../js/admin/chosen/manageContent.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--业务代码--}}
    <script src="../js/admin/base.js?{{env('timestamp')}}" type="text/javascript"></script>
@stop

@section("base_mainContent")

    <div class="pageTopTitle"><a href="/chosen/homepage">内容分销</a><span> > </span><a>上传文推广文案</a></div>

    <div class="baseItemWrapper baseItemTitle">
        <div class="baseTitleLeft">
            标题名称<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault inputDynamicTitle" id="dynamicTitle" placeholder="请输入标题名称"
                   @if($type) value="{{ $title }}" @endif >
        </div>
    </div>

    <div class="baseItemWrapper baseItemContent">
        <div class="baseTitleLeft">
            推广文案<span class="starIcon">*</span>
        </div>

        <div class="baseContentRight">
            @if($type)
                <input id="return_content" type="hidden" value="{{$content}}" />
                <input id="return_type" type="hidden" value="{{$type}}" />
            @endif
            <div class="resDescribe">
                <script id="container" type="text/plain"></script>
                <script type="text/javascript">
                    var ue = UE.getEditor('container', ueditor_config);
                    @if($type){{--判断是否需要初始化--}}
                        ue.ready(function () {
                        ue.setContent($("#return_content").val());
                    });
                    @endif
                </script>
            </div>

        </div>
        <div class="waves-effect btnSmall xeBtnDefault coverUpbtn" id="preview" style="margin-left: 20px;margin-right: 0;">
            预览
        </div>
    </div>

    <div class="horizontalDivideLine"></div>

    <div class="btnBlue btnMid releaseDynamicBtn" id="releaseDynamic">@if($type)保存编辑@else保存@endif</div>
    <input type="hidden" id="xcx_app_id" value="">
@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')

@stop