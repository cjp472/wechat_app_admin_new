<?php
$pageData = [];
$pageData['sideActive'] = 'create_content';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    {{-- 扁平化框架 --}}
    <link href="../css/external/materialize.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
    {{--依赖start--}}
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}" rel="stylesheet" type="text/css">
    {{--文件选择框美化--}}
    <link href="../css/external/jquery.nice-file-input.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
    {{--弹窗--}}
    <link href="../css/external/xcConfirm.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
    {{--依赖end--}}

    {{--页面样式--}}
    <link href="../css/admin/resManage/resAdd.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('page_js')
    {{--
        <script src="../js/admin/acitvity/activeBaseLayout.js?201703091023" type="text/javascript">
        </script>
        --}}
    {{--依赖start--}}
    {{--时间选择器--}}
    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}" type="text/javascript">
    </script>
    {{--文件选择框美化--}}
    <script src="../js/external/jquery.nice-file-input.js?{{env('timestamp')}}" type="text/javascript">
    </script>
    {{--腾讯云上传js--}}
    {{--
    <script src="sdk/swfobject.js" type="text/javascript">
    </script>
    --}}
    {{--
    <script src="sdk/qcloud_sdk.js" type="text/javascript">
    </script>
    --}}
    <script src="sdk/cos-js-sdk-v4.js" type="text/javascript">
    </script>
    <script src="js/admin/utils/v4QcloudUpload.js" type="text/javascript">
    </script>
    {{--云点播视频上传--}}
    <script charset="utf-8" src="https://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js">
    </script>
    {{--<script src="https://imgcache.qq.com/open/qcloud/js/vod/sdk/uploaderh5V3.js" charset="utf-8"></script>--}}
    {{--
    <script charset="utf-8" src="http://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js">
    </script>
    --}}
    {{--获取文件MD5--}}
    <script src="../js/external/browser-md5-file.js" type="text/javascript">
    </script>
    {{--弹窗--}}
    <script src="../js/external/xcConfirm.js" type="text/javascript">
    </script>
    {{--文本编辑器--}}
    <script src="../ueditor/ueditor.config.js" type="text/javascript">
    </script>
    <script src="../ueditor/ueditor.all.js" type="text/javascript">
    </script>
    {{--依赖end--}}
    <script src="../js/admin/config/config.js" type="text/javascript">
    </script>
    {{--上传工具函数--}}
    <script>
        secretId = "{{env('SecretId')}}";
        sigUrl = "{{ env('SignUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        transcodeNotifyUrl = "{{env('TransNotifyUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
    </script>
    {{--<script src="../js/admin/resManage/videoUploadTest.js" type="text/javascript">--}}
        <script src="../js/admin/resManage/videoUploadOld.js" type="text/javascript">
    </script>
    <script src="../js/external/materialize.js" type="text/javascript">
    </script>
    {{--页面逻辑--}}
    {{--
        <script src="../js/admin/resManage/resAdd.js" type="text/javascript">
        </script>
        --}}

    {{--
    <script>
        --}}
    {{--$(document).ready(function () {--}}
    {{--$.cookie('content_create')? setTopUrlInfo('content_create') : setTopUrlInfo('audio_listop');--}}
    {{--});--}}
    {{--
    </script>
    --}}
@endsection


@section('base_mainContent')
    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
            <div class="resAddPartTitle">
                <div class="AddPartTitleWords">
                    填写个人信息
                </div>
            </div>
            <div class="resAddSection">
                <div class="resAddSectionT">
                    音频名称
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <input class="resName inputDefault form-control" name="resName" placeholder="请输入音频名称(建议字数在14字以内)" type="text"/>
                </div>
            </div>
            {{-- 图片上传 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    图片上传
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <div class="fileUploadTip">
                        格式支持mp3，文件大小不超过50M，语音时长不超过60分钟
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault resUploadBtn">
                        图片上传
                        <input accept="image/jpeg,image/png,image/gif,image/bmp" class="upLoadImage" type="file">
                        </input>
                    </div>
                </div>
            </div>
            {{-- 音频上传 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    音频上传
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <div class="fileUploadTip">
                        格式支持mp3，文件大小不超过50M，语音时长不超过60分钟
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault resUploadBtn">
                        音频上传
                        <input accept="audio/*" class="upLoadAudio" type="file">
                        </input>
                    </div>
                    <div class="uploadBox">
                        <div class="uploadProgress">
                            <div class="uploadPTitle uploadaudioName">
                            </div>
                            <div class="uploadPSize uploadaudioSize">
                                (
                                <span>
                                </span>
                                M)
                            </div>
                            <div class="uploadPLine">
                                <div class="uploadPLineActive uploadPLineActiveA">
                                </div>
                            </div>
                            <div class="uploadPersent uploadPersentA">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- 视频上传 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    视频上传
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <div class="fileUploadTip">
                        格式支持mp3，文件大小不超过50M，语音时长不超过60分钟
                    </div>
                    <div class="uploadBox">
                        {{--<button class="waves-effect btnSmall xeBtnDefault" id="video_file" style="margin-left: 0">--}}
                            {{--选择文件--}}
                        {{--</button>--}}
                        <button id="video_file" class="btn btn-default" style="margin-left: 0">选择文件</button>
                    </div>
                    {{--
                    <div class="waves-effect btnSmall xeBtnDefault resUploadBtn" id="upLoadVideo">
                        视频上传
                        <input accept="video/*" class="uploadVideo" type="file">
                        </input>
                    </div>
                    --}}
                    <div class="uploadProgress">
                        <div class="uploadPTitle uploadPTitleV">
                        </div>
                        <div class="uploadPSize uploadPSizev">
                            (
                            <span>
                            </span>
                            M)
                        </div>
                        <div class="uploadPLine">
                            <div class="uploadPLineActive uploadPLineActiveV">
                            </div>
                        </div>
                        <div class="uploadPersent uploadPersentV">
                        </div>
                    </div>
                </div>
            </div>
            {{-- 音频封面 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    音频封面1
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                    <div class="previewPic">
                        <img src="">
                        {{--
                        <div class="noPicPreview">
                            <input accept="image/*" class="activityPic upLoadImage previewUpload" type="file">
                            </input>
                        </div>
                        <div class="picWrapper">
                            <img class="pic_close" id="deletePic" src="../images/icon_close.png">
                                <img class="picPreview" id="img_url">
                                    <input accept="image/jpeg,image/png,image/gif,image/bmp" class="uploadResPic previewUpload" type="file">
                                    </input>
                                </img>
                            </img>
                        </div>
                        --}}
                        </img>
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault coverUpbtn">
                        <span>
                            上传
                        </span>
                        <input accept="image/jpeg,image/png,image/gif,image/bmp" class="upLoadImage" type="file"/>
                    </div>
                    <div class="coverUpTip">
                        建议尺寸750*560px或4：3，JPG、PNG格式， 图片小于100K。
                    </div>
                </div>
            </div>
            <div class="resAddSection">
                <div class="resAddSectionT">
                    音频详情
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <textarea class="resDetail" cols="30" id="resDetail" name="resDetail" placeholder="请输出资源详情" rows="10">
                    </textarea>
                </div>
            </div>
        </div>
        <div class="resAddPart resAddPart2">
            <div class="resAddPartTitle">
                <div class="AddPartTitleWords">
                    设置上架信息
                </div>
            </div>
            <div class="resAddSection">
                <div class="resAddSectionT">
                    所属专栏
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <select class="browser-default">
                        <option value="1">
                            选项 1
                        </option>
                        <option value="2">
                            选项 2
                        </option>
                        <option value="3">
                            选项 3
                        </option>
                    </select>
                    <div class="waves-effect btnMid xeBtnDefault addClumnBtn">
                        +创建专栏
                    </div>
                </div>
            </div>
            <div class="resAddSection">
                <div class="resAddSectionT">
                    上架时间
                </div>
                <div class="resAddSectionC">
                    <div class="dateBox">
                        <input class="inputDefault dateInput" id="dateInput" name="upDate" type="text"/>
                        <div class="dateUpIcon">
                            <img src="../images/admin/resManage/icon_riqi.png"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resAddSection">
                <div class="resAddSectionT">
                    服务号通知
                </div>
                <div class="resAddSectionC">
                    <div class="radioGroup">
                        <div class="radioBtn1">
                            <input class="with-gap" id="test1" name="group1" type="radio"/>
                            <label for="test1">
                                开启
                            </label>
                        </div>
                        <div class="radioBtn2">
                            <input class="with-gap" id="test2" name="group1" type="radio"/>
                            <label for="test2">
                                关闭
                            </label>
                        </div>
                    </div>
                    <div class="noticeTip">
                        开启服务号通知后，内容上架时会向已订阅专栏的用户发送服务号模板消息
                    </div>
                </div>
            </div>
            <div class="boxLine">
            </div>
        </div>
        <div class="waves-effect btnMid xeBtnDefault lastStepBtn">
            上一步
        </div>
        <div class="waves-effect waves-light btnMid btnBlue completeBtn">
            完成
        </div>
    </div>
    @stop


    @section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
    @stop
    </link>