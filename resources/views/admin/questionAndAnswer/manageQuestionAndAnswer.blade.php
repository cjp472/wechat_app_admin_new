<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/admin/questionAndAnswer/manageQuestionAndAnswer.css?{{env('timestamp')}}">
@stop

@section('page_js')
    {{-- 腾讯云上传V4 --}}
    <script type="text/javascript" src="../sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="../js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--上传工具类--}}
    <script type="text/javascript" src="../js/admin/utils/upload.js?{{env('timestamp')}}"></script>
    {{--表单检查--}}
    <script type="text/javascript" src="../js/admin/utils/formCheck.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/questionAndAnswer/manageQuestionAndAnswer.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    <input id="admin_data" type="hidden" data-page_type="{{$page_type}}" >

    <div class="pageTopTitle">
        <a>社群运营</a>
        &gt;
        <a href="/QA/questionAndAnswerDetail">付费问答</a>
        &gt;
        @if($page_type) 编辑问答专区 @else 创建问答专区 @endif
    </div>

    <div class="baseItemWrapper baseItemTitle">
        <div class="baseTitleLeft">
            问答区名称<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault" id="QA_title" placeholder="请输入问答区名称（建议字数在24字以内）" @if($page_type) value="{{$data->title}}" @endif/>
        </div>
    </div>

    <div class="baseItemWrapper baseItemSummary">
        <div class="baseTitleLeft">
            问答区简介<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <textarea class="contentSummaryArea" id="QA_summary" placeholder="请输入问答区简介（建议字数在24字以内）" cols="69" rows="3">@if($page_type){{$data->desc}}@endif</textarea>
        </div>
    </div>

    <div class="baseItemWrapper baseItemCover">
        <div class="baseTitleLeft">
            问答封面<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">

            {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
            <label for="uploadImage" class="previewPic">

                <img id="previewCoverImg" src="@if($page_type) {{$data->img_url}} @else ../images/admin/resManage/pic_addfengmian.png @endif"/>
            </label>
            <div class="previewInfo">
                <label for="uploadImage" class="btnSmall xeBtnDefault upLoadImageBtn">上传</label>

                <input id="uploadImage" class="hide" type="file" accept="image/jpeg,image/png,image/gif,image/bmp" />
                <input type="hidden" id="imgUrl" type="text" value="@if($page_type) {{$data->img_url}} @else '' @endif"/>

                <div class="coverUpTip">建议尺寸750*560px，JPG、PNG格式，小于100K。</div>
            </div>

        </div>
    </div>

    <div class="baseItemWrapper baseItemEavesdrop">
        <div class="baseTitleLeft">
            偷听价格<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault EavesdropPriceInput" id="eavesdropPrice" placeholder="偷听价格，如1.00元" onkeyup="clearNoNum(this.value, this)"  @if($page_type) value="{{$data->price/100}}" @endif />元
        </div>
    </div>

    <div class="baseItemWrapper baseItemShareRatio">
        <div class="baseTitleLeft">
            偷听分成<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <div class="sharer">
                <span>商家</span>
                <input class="inputDefault" id="sharerTrader" placeholder="30" onkeyup="clearNoNum(this.value, this)" @if($page_type) value="{{$data->listen_for_business}}" @endif> %
            </div>
            <div class="sharer ">
                <span>答主</span>
                <input class="inputDefault" id="sharerResponder" placeholder="40" onkeyup="clearNoNum(this.value, this)" @if($page_type) value="{{$data->listen_for_answer}}" @endif> %
            </div>
            <div class="sharer ">
                <span>提问者</span>
                <input class="inputDefault" id="sharerAskPerson" placeholder="30" onkeyup="clearNoNum(this.value, this)" @if($page_type) value="{{$data->listen_for_questioner}}" @endif> %
            </div>
            <div class="shareRatioTip">商家、答主、提问者三者分成总和必须等于100%</div>
        </div>
    </div>

    <div class="baseItemWrapper baseItemResShow">
        <div class="baseTitleLeft">
            上下架设置<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <div class="radioGroup">
                <div class="radioBtn1">
                    <input class="with-gap" id="showQA" name="isQAShow" value="0" type="radio" @if($page_type == 0) checked @endif @if($page_type) @if($data->state == 0) checked @endif @endif>
                    <label for="showQA" class="isQAShowLabel">上架</label>
                </div>
                <div class="radioBtn2">
                    <input class="with-gap" id="hideQA" name="isQAShow" value="1" type="radio" @if($page_type) @if($data->state == 1) checked @endif @endif >
                    <label for="hideQA" class="isQAShowLabel">暂不上架</label>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="page_type" value="{{$page_type}}">

    <div class="horizontalDivideLine"></div>

    <div class="btnBlue btnMid createResource" id="createQA">
        @if($page_type) 保存 @else 创建 @endif
    </div>


@stop



