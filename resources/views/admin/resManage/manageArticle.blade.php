<?php
$pageData = [];
$pageData['sideActive'] = 'resourceList';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    {{-- 扁平化框架 --}}
    <link href="../css/external/materialize.css" rel="stylesheet" type="text/css"/>
    {{--依赖start--}}
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
    {{--文件选择框美化--}}
    <link href="../css/external/jquery.nice-file-input.css" rel="stylesheet" type="text/css"/>
    {{--弹窗--}}
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    {{--依赖end--}}

    {{--页面样式--}}
    <link href="../css/admin/resManage/resAdd.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('ahead_js')

    {{--文本编辑器--}}
    <script src="../ueditor/ueditor.config.js" type="text/javascript"></script>
    <script src="../ueditor/ueditor.all.min.js" type="text/javascript"></script>
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
    {{--云点播视频上传--}}
    <script charset="utf-8" src="https://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js">
    </script>
    {{--获取文件MD5--}}
    <script src="../js/external/browser-md5-file.js" type="text/javascript">
    </script>
    {{--弹窗--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{--依赖end--}}
    {{--上传工具类--}}
    <script src="../js/admin/utils/upload.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--表单检查工具类--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script>
        secretId = "{{env('SecretId')}}";
        sigUrl = "{{ env('SignUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        transcodeNotifyUrl = "{{env('TransNotifyUrl').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";

        golbalUrl =
        @if($page_type)
            '/edit_resource'
        @else
            '/goods_upload_resource'
        @endif;

    </script>
    {{--materializeUI--}}
    <script src="../js/external/materialize.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--页面逻辑--}}
    <script src="../js/admin/resManage/resAdd.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script src="../js/admin/resManage/manageArticle.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script>
        $(function(){
            @if($page_type)
            //是否付费（1表示免费，2表示单笔）
            resourceFree={{$resource_info->payment_type}};
            @endif;
        })
    </script>

@endsection


@section('base_mainContent')

    <div class="pageTopTitle">
    <a href="#" id="getBack">
    @if($upload_channel_type==1)
        单品列表
    @elseif($upload_channel_type==2)
        专栏详情
    @elseif($upload_channel_type==3)
        会员详情
    @endif
    </a>
    &nbsp;&nbsp;>&nbsp;&nbsp;
    @if($page_type==0)
        新增图文
    @else
        编辑图文
    @endif
    </div>

    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
            <div class="resAddPartTitle">
                <div class="titleLine"></div>
                <div class="AddPartTitleWords">
                    基本信息
                </div>
            </div>
            {{--图文名称--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    图文名称
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <input class="resName inputDefault" name="resName" placeholder="请输入图文名称(建议字数在14字以内)"
                    @if($page_type)
                        value="{{$resource_info->title}}"
                    @endif
                           type="text"/>
                </div>
            </div>
            {{-- 图文封面 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    图文封面

                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                    <div class="previewPic previewPicImage1">
                        <img src="{!! $page_type ? $resource_info->img_url_compressed : '../images/admin/resManage/pic_addfengmian.png' !!}" />
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault coverUpbtn">
                        <span>
                            选择文件
                        </span>
                        <input accept="image/jpeg,image/png,image/gif,image/bmp" class="upLoadImage upLoadImage1"
                               type="file"/>
                        <input type="hidden" id="Image1Url" type="text"@if($page_type)
                            value="{{$resource_info->img_url}}"
                        @endif
                        />
                    </div>
                    <div class="coverUpTip">
                        建议尺寸750*560px或4：3，JPG、PNG格式， 图片小于5M。
                    </div>
                </div>
            </div>
            {{--图文详情--}}

            <div class="resAddSection">
                <div class="resAddSectionT">
                    图文详情
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    {{--临时存储--}}
                    @if($page_type)
                    <input id="rubbish" type="hidden" value="{{$resource_info->org_content}}" />
                    @endif
                    <div class="resDescribe">
                        <script id="resource_desc"  type="text/plain"></script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('resource_desc',ueditor_config);
                            @if($page_type)
                                ue.ready(function()
                                {
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

            {{--图文试看--}}

            <div class="resAddSection">
                
                <div class="resAddSectionT">
                    试看内容
                    @if($upload_channel_type==2||$upload_channel_type==3)
                    <div class="singleSale">(单卖后可见)</div>
                    @endif
                </div>
                <div class="resAddSectionC">
                    {{--临时存储--}}
                    @if($page_type)
                        <input id="rubbish1" type="hidden" value="{{$resource_info->try_org_content}}" />
                    @endif
                    <div class="resDescribe">
                        <script id="contentTry"  type="text/plain"></script>
                        <script type="text/javascript">
                            var ue1 = UE.getEditor('contentTry',ueditor_config);
                            @if($page_type)
                                ue1.ready(function()
                            {
                                ue1.setContent($("#rubbish1").val());
                            });
                            @endif

                        </script>
                    </div>
                </div>
                <div class="waves-effect btnSmall xeBtnDefault coverUpbtn" id="preview_try" style="margin-left:40px;margin-right: 0;">
                    预览
                </div>

            </div>

            <style>
                .singleSale{
                    line-height:12px;
                    font-size:12px;
                    color:#888;
                }
            </style>
        </div>
        <div class="resAddPart resAddPart2">
            <div class="resAddPartTitle">
                <div class="titleLine"></div>
                <div class="AddPartTitleWords">
                    上架信息
                </div>
            </div>
            {{--付费形式--}}
            @if($upload_channel_type==1)
            <div class="resAddSection">
                <div class="resAddSectionT">
                    付费形式
                </div>
                <div class="resAddSectionC">
                    <div class="radioGroup">
                        <div class="radioBtn1">
                            <input class="with-gap notFreeSelect" id="rBtn1" name="group1" type="radio"
                                   @if(($page_type==1&&$resource_info->payment_type==2)||!$page_type) checked @endif/>
                            <label for="rBtn1" class="notFreeSelect">
                                付费
                            </label>
                            <input class="resPrize inputDefault" name="resName" placeholder="请输入价格"
                                   type="text"  onkeyup="clearNoNum(this.value, this)"
                                   @if($page_type) value="{{$resource_info->piece_price/100}}" @endif  @if(($page_type==1&&$resource_info->payment_type==1)) readonly="readonly" disabled="disabled " @endif/>
                            <span>元</span>
                        </div>
                        <div class="radioBtn2">
                            <input class="with-gap FreeSelect" id="rBtn2" name="group1" type="radio" @if(($page_type==1&&$resource_info->payment_type==1)) checked @endif/>
                            <label for="rBtn2" class="FreeSelect">
                                免费
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            {{--所属分类--}}
            {{--判断所属分类是否打开显示--}}
            @if($upload_channel_type==1)
                @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category", "version_type") &&
                    \App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category", "app_module"))
                    <div class="resAddSection">
                        <div class="resAddSectionT">
                            所属分类
                        </div>
                        <div class="resAddSectionC">
                            <div class="clumnClass">
                                @foreach($category_info as $key => $val)
                                    <input type="checkbox" name="aliveClass" class="filled-in" id="{{$key}}"
                                           @if($page_type && in_array($key, $package_category))
                                           checked
                                           @endif
                                           value="{{$val}}"/>
                                    <label for="{{$key}}">{{$val}}</label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            {{--上架时间--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    上架时间
                    <span class="startKey">
                        *
                    </span>
                </div>
                <div class="resAddSectionC">
                    <div class="dateBox">
                        <input class="inputDefault dateInput dateSetInputBottom" id="dateInput" name="upDate" type="text"@if($page_type)
                            value="{{$resource_info->start_at}}"
                        @endif/>
                        <div class="dateUpIcon">
                            <img src="../images/admin/resManage/icon_riqi.png"/>
                        </div>
                    </div>
                </div>
            </div>
            {{--服务号通知--}}
            @if( session('version_type') > 1 && ($upload_channel_type != 1 && Illuminate\Support\Facades\Input::get('price', 0)>0) )
                <div class="resAddSection" id="subMsg">
                    <div class="resAddSectionT" >
                        服务号通知
                    </div>
                    <div class="resAddSectionC">

                        @if(!$page_type || ($page_type && $resource_info->push_state !=2))
                            <div class="radioGroup" id="serviceToggle" data-setting="{{$is_set_temp}}" data-collection="{{$use_collection}}">
                                <div class="radioBtn1">
                                    <input class="with-gap" id="fBtn1" name="group2" type="radio" value="1" {{$page_type && $resource_info->push_state==1 ? 'checked' : ''}} />
                                    <label for="fBtn1" class="active">
                                        开启
                                    </label>
                                    <div class="remindNum">(<span id="nextDay">
                                            @if($upload_channel_type == 2)本专栏@elseif($upload_channel_type == 3)本会员@endif今日</span>已发送<span id="has_push" class="starIcon">0</span>次，还可以发送<span id="valid_push" class="starIcon">0</span>次)
                                    </div>
                                    <div class="upload_input_msg">
                                        <span>开启后，内容上架后会向已订阅专栏的用户发送服务号模板消息(每天最多可发送3次)。</span><a target="_blank" href="/helpCenter/problem?document_id=d_58f0bdbc5e504_LKZ3ouHf">什么是模板消息？</a>
                                    </div>
                                </div>
                                <div class="radioBtn2">
                                    <input class="with-gap" id="fBtn2" name="group2" type="radio" value="0" {{$page_type ? ($resource_info->push_state==0 ? 'checked' : '') : 'checked'}} />
                                    <label for="fBtn2" class="">
                                        关闭
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="upload_input_msg" style="font-size: 14px; margin-left: 0;">
                                <span>通知已发送</span>
                            </div>
                        @endif

                    </div>

                </div>
            @endif
            <div class="boxLine">
            </div>
        </div>
        <div class="createResourceBtnWrapper">
            @if($page_type)
                <div class="waves-effect btnMid xeBtnDefault lastStepBtn">取消</div>
            @endif
            <div class="waves-effect waves-light btnMid btnBlue completeBtn">@if($page_type)保存@else立即创建@endif</div>
        </div>
    </div>


    <input type="hidden" id="xcx_app_id" value="@if($page_type){{$resource_info->app_id}}@else{{$app_id}}@endif">
@stop


@section('base_modal')
        {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')

@stop