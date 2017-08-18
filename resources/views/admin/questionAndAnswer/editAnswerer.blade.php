<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link type="text/css" rel="stylesheet" href="../css/admin/questionAndAnswer/editAnswerer.css?{{env('timestamp')}}">
@stop

@section('page_js')
    {{-- 腾讯云上传V4 --}}
    <script type="text/javascript" src="../sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="../js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--上传工具类--}}
    <script type="text/javascript" src="../js/admin/utils/upload.js"></script>
    {{--表单检查--}}
    <script type="text/javascript" src="../js/admin/utils/formCheck.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/questionAndAnswer/editAnswerer.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    <div class="pageTopTitle">
        <a>社群运营</a>
        &gt;
        <a href="/QA/questionAndAnswerDetail?page_type=1">付费问答</a>
        &gt;
        答主编辑
    </div>

    <div class="baseItemWrapper baseItemAvatar">
        <div class="baseTitleLeft">
            头像
        </div>
        <div class="baseContentRight">
            <img class="avatarIcon" src="{{$answerer_info->answerer_avatar}}">
        </div>
        <div class="previewInfo">
            <label for="uploadImage" class="btnSmall xeBtnDefault upLoadImageBtn">上传头像</label>
            <span class="coverUpTip">（建议尺寸1：1，图片大小不超过2M）</span>

            <input id="uploadImage" class="hide" type="file" accept="image/jpeg,image/png,image/gif,image/bmp" />
            <input type="hidden" id="imgUrl" type="text" value="{{$answerer_info->answerer_avatar}}"/>

        </div>
        {{--<div class="btnBlue btnMid saveAnswererAvatar" id="saveAnswererAvatar">保存</div>--}}
    </div>

    <div class="baseItemWrapper baseItemName">
        <div class="baseTitleLeft">
            姓名<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault" id="responderName" placeholder="请输入答主姓名" value="{{$answerer_info->answerer_name}}">
        </div>
    </div>

    <div class="baseItemWrapper baseItemPhoneNum">
        <div class="baseTitleLeft">
            手机号码<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault" id="responderPhone" placeholder="请输入答主手机号码" value="{{$answerer_info->phone}}">
        </div>
    </div>

    <div class="baseItemWrapper baseItemPosition">
        <div class="baseTitleLeft">
            职位/头衔<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault" id="responderPosition" placeholder="请输入答主职位/头衔" value="{{$answerer_info->position}}">
        </div>
    </div>

    <div class="baseItemWrapper baseItemSummary">
        <div class="baseTitleLeft">
            答主简介<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <textarea id="responderSummary" class="responderSummaryTextArea" maxlength="128" placeholder="请输入答主简介">{{$answerer_info->summary}}</textarea>
        </div>
    </div>

    <div class="baseItemWrapper baseItemPrice">
        <div class="baseTitleLeft">
            提问价格<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <input class="inputDefault responderPriceInput" id="responderPrice" placeholder="提问价格，如88.00" value="{{$answerer_info->price/100}}" onkeyup="clearNoNum(this.value, this)">
            元
        </div>
    </div>

    <div class="baseItemWrapper baseItemQuestionRatio">
        <div class="baseTitleLeft">
            提问分成<span class="starIcon">*</span>
        </div>
        <div class="baseContentRight">
            <div class="sharer">
                <span>商家</span>
                <input class="inputDefault" id="sharerTrader" placeholder="30" value="{{$answerer_info->profit_business}}" onkeyup="clearNoNum(this.value, this)"> %
            </div>
            <div class="sharer ">
                <span>答主</span>
                <input class="inputDefault" id="sharerResponder" placeholder="40" value="{{$answerer_info->profit_answer}}" onkeyup="clearNoNum(this.value, this)"> %
            </div>
            <div class="questionRatioTip">商家、答主，二者分成总和必须等于100%</div>
        </div>
    </div>

    <div class="horizontalDivideLine"></div>

    <div class="btnBlue btnMid saveAnswererInfoBtn" id="saveAnswererInfo">保存</div>

@stop













