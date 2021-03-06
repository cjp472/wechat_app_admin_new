<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    {{-- 扁平化框架 --}}
    <link href="../css/external/materialize.css" rel="stylesheet" type="text/css"/>
    {{--弹窗--}}
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css" />
    {{--页面样式--}}
    <link href="../css/admin/resManage/resAdd.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('ahead_js')
    {{--文本编辑器--}}
    <script src="../ueditor/ueditor.config.js" type="text/javascript"></script>
    <script src="../ueditor/ueditor.all.min.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--秀米sdk--}}
    <script src="../ueditor/xiumi-ue-dialog-v5.js" type="text/javascript"></script>
@endsection

@section('page_js')
    {{--腾讯云上传js--}}
    <script src="../sdk/cos-js-sdk-v4.js" type="text/javascript">
    </script>
    <script src="../js/admin/utils/v4QcloudUpload.js" type="text/javascript">
    </script>
    {{--获取文件MD5--}}
    <script src="../js/external/browser-md5-file.js" type="text/javascript">
    </script>
    {{--弹窗--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js"></script>
    {{--依赖end--}}
    {{--上传工具类--}}
    <script src="../js/admin/utils/upload.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--表单检查工具类--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script>
        secretId = "{{env('SecretId')}}";
        sigUrl = "{{ env('SignUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        transcodeNotifyUrl = "{{env('TransNotifyUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
    </script>
    {{--materializeUI--}}
    <script src="../js/external/materialize.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--逻辑--}}
    <script src="../js/admin/communityOperate/addDynamic.js?{{env('timestamp')}}" type="text/javascript"></script>
@endsection



@section('base_mainContent')
    <div class="pageTopTitle">
        <a href="#" id="getBack">
              社群运营
        </a>
        &nbsp;&nbsp;>&nbsp;&nbsp;
        <a href="#" id="getBack">
              小社群
        </a>
        &nbsp;&nbsp;>&nbsp;&nbsp;
        <a href="#" id="getBack">
              发布动态
        </a>
    </div>
    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
          {{--动态标题--}}
          <div class="resAddSection">
              <div class="resAddSectionT">
                  动态标题
                  <span class="startKey">
                      *
                  </span>
              </div>
              <div class="resAddSectionC">
                <input class="dynamicName resName inputDefault" name="resName" placeholder="请输入动态名称(建议字数在14字以内)" type="text"/>
              </div>
          </div>
            {{--动态内容--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    动态内容
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    {{--临时存储--}}
                    {{--@if($page_type)--}}
                        {{--<input id="rubbish" type="hidden" value="{{$resource_info->org_content}}" />--}}
                    {{--@endif--}}
                    <div class="resDescribe">
                        <script id="dynamicDescirb"  type="text/plain"></script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('dynamicDescirb',ueditor_config);
                            {{--@if($page_type)--}}
                                {{--ue.ready(function()--}}
                            {{--{--}}
                                {{--ue.setContent($("#rubbish").val());--}}
                            {{--});--}}
                            {{--@endif--}}
                        </script>
                    </div>
                </div>

            </div>
        </div>
        <div class="boxLine">

        </div>
        <div class="waves-effect waves-light btnMid btnBlue completeBtn" style="margin-left: 122px" >
            发布动态
        </div>
    </div>
@endsection

@section('base_modal')

@endsection
