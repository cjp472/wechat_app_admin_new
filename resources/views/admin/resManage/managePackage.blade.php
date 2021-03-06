<?php
$pageData = [];
$pageData['sideActive'] = '_packagePart';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    {{-- 扁平化框架 --}}
    <link href="../css/external/materialize.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
    {{--依赖start--}}
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
    {{--文件选择框美化--}}
    <link href="../css/external/jquery.nice-file-input.css" rel="stylesheet" type="text/css"/>
    {{--弹窗--}}
    <link href="../css/external/xcConfirm.css" rel="stylesheet" type="text/css"/>
    {{--依赖end--}}

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
    {{--依赖start--}}
    {{--时间选择器--}}
    <script src="../js/external/bootstrap-datetimepicker.min.js" type="text/javascript">
    </script>
    {{--文件选择框美化--}}
    <script src="../js/external/jquery.nice-file-input.js" type="text/javascript">
    </script>
    {{--腾讯云上传js--}}
    <script src="sdk/cos-js-sdk-v4.js" type="text/javascript">
    </script>
    <script src="js/admin/utils/v4QcloudUpload.js" type="text/javascript">
    </script>
    {{--获取文件MD5--}}
    <script src="../js/external/browser-md5-file.js" type="text/javascript">
    </script>
    {{--弹窗--}}
    <script src="../js/external/xcConfirm.js" type="text/javascript">
    </script>
    {{--依赖end--}}
    {{--上传工具类--}}
    <script src="../js/admin/utils/upload.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--表单检查工具类--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script>
        secretId = "{{env('SecretId')}}";
        sigUrl = "{{ env('SignUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        transcodeNotifyUrl = "{{env('TransNotifyUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";

        globalUrl =
        @if($type)
                    '/goods_edit_package'
        @else
            '/goods_upload_package'
        @endif;
    </script>
    {{--materializeUI--}}
    <script src="../js/external/materialize.js?{{env('timestamp')}}" type="text/javascript">
    </script>
    {{--页面逻辑--}}
    <script src="../js/admin/resManage/resAdd.js?{{env('timestamp')}}" type="text/javascript">
    </script>
    <script src="../js/admin/resManage/managePackage.js?{{env('timestamp')}}" type="text/javascript"></script>

   <script>
       @if($type)
           is_distribute ="{{ $package_info->is_distribute}}";
           state="{{$package_info->state}}";
           columnState="{{$package_info->state}}";
           is_show_resourcecount="{{$package_info->is_show_resourcecount}}"
       @endif
   </script>
@endsection

@section('base_mainContent')
    <div class="pageTopTitle">
        @if($type)
        <a href="/package_detail_page?id={{$package_info->id}}" style="color: #353535;font-size: 16px;">专栏详情</a>
        @else
        <a href="/package_list_page" style="color: #353535;font-size: 16px;">专栏列表</a>
        @endif
        &nbsp;&nbsp;>&nbsp;&nbsp; @if($type) 编辑专栏 @else 新增专栏 @endif
    </div>
    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
            <div class="resAddPartTitle">
                <div class="titleLine"></div>
                <div class="AddPartTitleWords">
                    基本信息
                </div>
            </div>
            {{--专栏名称--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    专栏名称
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <input @if($type)@if($package_info->is_distribute) disabled="disabled" style="background-color: rgb(235, 235, 228)" @endif @endif class="resName inputDefault " name="resName" placeholder="请输入专栏名称(建议字数在14字以内)"
                           type="text"
                           @if($type)
                           value="{{$package_info->name}}"
                           @endif

                    />
                </div>
            </div>
            {{--专栏简介--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    专栏简介
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <textarea @if($type)@if($package_info->is_distribute) disabled="disabled" style="background-color: rgb(235, 235, 228)" @endif @endif name="" class="packageAbstract" id="packageAbstract" cols="30" rows="10" placeholder="请输入专栏简介">@if($type){{$package_info->summary}}@endif</textarea>
                </div>
            </div>
            {{-- 专栏封面 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    专栏封面
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                    <div class="previewPic previewPicImage1">
                        <img src="
                        @if($type)
                        {{$package_info->img_url}}
                        @else
                        {{ '../images/admin/resManage/pic_addfengmian.png' }}
                        @endif
                                "/>
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault coverUpbtn">
                        <span>
                            选择文件
                        </span>
                        <input @if($type) @if($package_info->is_distribute) disabled="disabled" @endif @endif accept="image/jpeg,image/png,image/gif,image/bmp"
                               class="upLoadImage upLoadImage1"
                               type="file"
                               />
                        <input  class="form-control" type="hidden" id="Image1Url" type="text"
                               @if($type)
                               value="{{$package_info->img_url}}"
                                @endif
                        />
                    </div>
                    <div class="coverUpTip">
                        建议尺寸750*560px或4：3，JPG、PNG格式， 图片小于5M。
                    </div>
                </div>
            </div>
            {{--专栏详情--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    专栏详情
                </div>
                <div class="resAddSectionC">
                    {{--临时存储--}}
                    @if($type)
                        <input id="rubbish" type="hidden" value="{{$package_info->org_content}}" />
                    @endif
                    <div class="resDescribe">
                        <script id="resource_desc" type="text/plain"></script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('resource_desc', ueditor_config);
                            @if($type)
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
        </div>
        <div class="resAddPart resAddPart2">
            <div class="resAddPartTitle">
                <div class="titleLine"></div>
                <div class="AddPartTitleWords">
                    上架信息
                </div>
            </div>
            {{--付费形式--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    付费形式
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <div class="radioGroup">
                        <div class="radioBtn1">
                            <input @if($type)@if($package_info->is_distribute) disabled="disabled" @endif @endif  class="with-gap notFreeSelect" id="rBtn1" name="group1" type="radio"
                                   @if($type && $package_info->price>0)
                                   checked
                                   @elseif(!$type)
                                   checked
                                    @endif
                            />
                            <label for="rBtn1" class="notFreeSelect active">
                                付费
                            </label>
                            <input  @if($type)@if($package_info->is_distribute) disabled="disabled" @endif @endif class="resPrize inputDefault" name="resName" placeholder="请输入价格"
                                   type="text"
                                   onkeyup="clearNoNum(this.value, this)"
                                   @if($type && $package_info->price>0)
                                   value="{{$package_info->price/100}}"
                                   @endif
                                   @if($type&&$package_info->price<=0) readonly="readonly"
                                   disabled="disabled" @endif/>
                            <span>元</span>
                        </div>
                        <div class="radioBtn2">
                            <input @if($type)@if($package_info->is_distribute) disabled="disabled" @endif @endif class="with-gap FreeSelect" id="rBtn2" name="group1" type="radio"
                                   @if($type && $package_info->price<=0)
                                   checked
                                    @endif
                            />
                            <label for="rBtn2" class="FreeSelect">
                                免费
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            {{--判断所属分类是否打开显示--}}
            @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category", "version_type") &&
                \App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category", "app_module"))
                <div class="resAddSection">
                    <div class="resAddSectionT">
                        所属分类
                    </div>
                    <div class="resAddSectionC">
                        <div class="clumnClass">
                            @foreach($category_info as $key => $val)
                                <input type="checkbox" name="columnClass" class="filled-in" id="{{$key}}"
                                       @if($type && in_array($key, $package_category))
                                       checked
                                       @endif
                                       value="{{$val}}"/>
                                <label for="{{$key}}">{{$val}}</label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            {{--服务号通知--}}
            {{--
            <div class="resAddSection">
                <div class="resAddSectionT">
                    服务号通知
                </div>
                <div class="resAddSectionC">
                    <div class="radioGroup">
                        <div class="radioBtn1">
                            <input class="with-gap" id="fBtn1" name="group2" type="radio"
                                   checked/>
                            <label for="fBtn1" class="active">
                                开启
                            </label>
                        </div>
                        <div class="radioBtn2">
                            <input class="with-gap FreeSelect" id="fBtn2" name="group2" type="radio"/>
                            <label for="fBtn2" class="">
                                关闭
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            --}}
            {{--是否上架--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    是否上架
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <div class="radioGroup">
                        <div class="radioBtn1">
                            <input class="with-gap" id="zBtn1" name="group3" type="radio"
                                   @if($type && !$package_info->state)
                                   checked
                                    @endif
                            />
                            <label for="zBtn1" class="active columnShow">
                                立即上架 <span class="grayWord">&nbsp;（对外显示）</span>
                            </label>
                        </div>
                        <div class="radioBtn2">
                            <input class="with-gap" id="zBtn2" name="group3" type="radio"
                                   @if($type && $package_info->state)
                                   checked
                                   @elseif(!$type)
                                   checked
                                    @endif
                            />
                            <label for="zBtn2" class="columnHide">
                                暂不上架 <span class="grayWord">&nbsp;（不对外显示）&nbsp;注：暂存于已下架列表</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{--<div class="resAddSection ">--}}
                {{--<div class="resAddSectionT">--}}
                    {{--是否显示期数--}}
                    {{--<span class="startKey">--}}
                        {{--*--}}
                    {{--</span>--}}
                {{--</div>--}}
                {{--<div class="resAddSectionC">--}}
                    {{--<div class="radioGroup">--}}
                        {{--<div class="radioBtn1">--}}
                            {{--<input class="with-gap" id="cBtn1" name="group4" type="radio"--}}
                                   {{--@if($type && $package_info->is_show_resourcecount)--}}
                                   {{--checked--}}
                                   {{--@elseif(!$type)--}}
                                   {{--checked--}}
                                    {{--@endif--}}
                            {{--/>--}}
                            {{--<label for="cBtn1" class="active countShow">--}}
                                {{--显示 <span class="grayWord">&nbsp;（对外显示更新期数）</span>--}}
                            {{--</label>--}}
                        {{--</div>--}}
                        {{--<div class="radioBtn2">--}}
                            {{--<input class="with-gap" id="cBtn2" name="group4" type="radio"--}}
                                   {{--@if($type && !$package_info->is_show_resourcecount)--}}
                                   {{--checked--}}
                                    {{--@endif--}}
                            {{--/>--}}
                            {{--<label for="cBtn2" class="countHide">--}}
                                {{--暂不显示 <span class="grayWord">&nbsp;（不对外显示期数）</span>--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="boxLine">
            </div>
        </div>
        @if($type)
            <div class="waves-effect btnMid xeBtnDefault lastStepBtn">
                取消
            </div>
        @endif
        <div class="waves-effect waves-light btnMid btnBlue completeBtn"
             @if(!$type) style="margin-left: 122px" @endif>
            @if($type)
                保存
            @else
                创建专栏
            @endif
        </div>
    </div>
    <input type="hidden" id="xcx_app_id" value="@if($type){{$package_info->app_id}}@else{{$app_id}}@endif">
@stop


@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')

@stop