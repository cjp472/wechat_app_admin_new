<?php
$pageData = [];
$pageData['sideActive'] = 'aliveList';
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
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
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
    {{--云点播视频上传--}}
    {{--<script charset="utf-8" src="https://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js"></script>--}}
    {{--<script src="https://imgcache.qq.com/open/qcloud/js/vod/sdk/uploaderh5V3.js?{{env('timestamp')}}" charset="utf-8"></script>--}}
    <script src="https://imgcache.qq.com/open/qcloud/js/vod/sdk/ugcUploader.js?{{env('timestamp')}}"></script>
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
        transcodeNotifyUrl = "{{env('TransNotifyUrl1').'?url_app_id='.\App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        golbalUrl =
                @if($page_type)
                    '/edit_resource'
        @else
            '/goods_upload_resource'
        @endif;
    </script>
    {{--materializeUI--}}
    <script src="../js/external/materialize.js?{{env('timestamp')}}" type="text/javascript">
    </script>
    {{--页面逻辑--}}
    <script src="../js/admin/resManage/resAdd.js?{{env('timestamp')}}" type="text/javascript">
    </script>
    <script src="../js/admin/resManage/manageAlive.js?{{env('timestamp')}}" type="text/javascript"></script>


    {{--<script type="text/javascript" src="../js/admin/resManage/videoUploadOld.js?{{env('timestamp')}}"></script>--}}
    {{--<script type="text/javascript" src="../js/admin/resManage/videoUploadTest.js?{{env('timestamp')}}"></script>--}}
    <script type="text/javascript" src="../js/admin/resManage/videoUploadUgc.js?{{env('timestamp')}}"></script>

    <script type="text/javascript">
        @if($page_type)
            //直播类型
            aliveT={{$resource_info->alive_type}};
            console.log(aliveT);

            aliveType={{$resource_info->alive_type}};

            //直播点播id
            fileId="";

            //直播大小
            videoGsize={{$resource_info->video_size}};

            //是否付费（1表示免费，2表示单笔）
            resourceFree={{$resource_info->payment_type}};

            //直播状态（0可见，1下架，2删除）
            aliveState={{$resource_info->state}};

            //直播的播放状态（0未开始，1、4已开始，2,3结束）
            playState={{$resource_info->alive_state}};
        @endif;

        golbalUrl =
                @if($page_type)
                    '/edit_resource'
        @else
            '/goods_upload_resource'
        @endif;
    </script>
@endsection

@section('base_mainContent')

    <input type="hidden" id="_manageAlive" value="{{$page_type}}">

    <div class="pageTopTitle">
        <a href="#" id="getBack">
            @if($upload_channel_type==1)
                单品列表
            @elseif($upload_channel_type==2)
                专栏详情
            @elseif($upload_channel_type==3)
                会员详情
            @endif
        </a>&nbsp;>&nbsp;@if($page_type==0)新增直播@else编辑直播@endif
    </div>
    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
            <div class="resAddPartTitle">
                <div class="titleLine"></div>
                <div class="AddPartTitleWords">
                    基本信息
                </div>
            </div>
            {{--直播名称--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    直播名称<span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    <input class="resName inputDefault" name="resName" placeholder="请输入直播名称(建议字数在14字以内)"
                           type="text" @if($page_type) value="{{$resource_info->title}}" @endif/>
                </div>
            </div>
            {{--直播简介--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    直播简介<span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    <textarea name="" id="aliveAbstract" cols="30" rows="10"
                              placeholder="请输入直播简介">@if($page_type) {{$resource_info->summary}} @endif</textarea>
                </div>
            </div>
            {{-- 直播形式 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    直播形式<span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    <div class="radioBtn1 @if($page_type&&$resource_info->alive_type==2) hide @endif ">
                        <input class="with-gap" id="aBtn1" name="group2" type="radio"
                                @if($page_type&&$resource_info->alive_type==0||!$page_type) checked @endif/>
                        <label for="aBtn1" class="voiceSelect">
                            语音图文直播<span>适用于通过大量的语音、文字、图片进行的直播</span>
                        </label>
                    </div>

                @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual('live_video', 'version_type'))
                    <div class="radioBtn2 @if($page_type&&$resource_info->alive_type==2) hide @endif ">
                        <input class="with-gap" id="aBtn2" name="group2" type="radio"
                                @if($page_type && $resource_info->alive_type==1) checked @endif/>
                        <label for="aBtn2" class="videosSelect active">
                            视频录播<span>适用于提前准备好录播视频形式的直播</span>
                        </label>
                    </div>
                    <div class="aliveVideoBox"
                         @if(($page_type&&$resource_info->alive_type!=1) || !$page_type) style="display: none" @endif
                    >
                        <div class="fileUploadTip">格式支持MP4</div>
                        <button id="video_file" class="waves-effect btnSmall xeBtnDefault resUploadBtn"style="margin-left: 0">选择文件</button>
                        <input id="uploadVideoNow-file" type="file" style="display:none;"/>
                        @if($page_type&&($resource_info->file_name||$resource_info->alive_video_url)&&$resource_info->alive_type==1)
                            <div id="videoName" class="hasUpNameContent" >
                                <p class="hasUpName" title="{{$resource_info->file_name or $resource_info->alive_video_url}}">{{$resource_info->file_name or $resource_info->alive_video_url}}</p>
                                {{--<span>({{$resource_info->video_size}})M</span>--}}
                            </div>
                        @endif
                        {{--<div class="waves-effect btnSmall xeBtnDefault resUploadBtn">--}}
                        {{--选择文件--}}
                        {{--<input accept="audio/*" class="upLoadAudio1" type="file">--}}
                        {{--</input>--}}
                        {{--<input type="hidden" id="Audio1Url" type="text"/>--}}
                        {{--</div>--}}

                        <div class="videoUploadBox" style="display: none">
                            <div class="videoBoxPart1">
                                <div class="videoBoxTitle"></div>
                                <div class="videoSize"></div>
                                <div class="videoPercent">0%</div>
                            </div>
                            <div class="videoBoxPart2">
                                <div class="videoUploadLine">
                                    <div class="videoUploadLineA"></div>
                                </div>
                            </div>
                            <div class="videoBoxPart3">
                                <div class="uploadRatio">已上传：<span></span>/<span></span></div>
                                <div class="videoUploadCancle" data-videoid="">取消</div>
                            </div>
                        </div>
                    </div>

                    <div class="radioBtn3 @if($page_type&&$resource_info->alive_type==2) hide @endif ">
                        <input class="with-gap" id="aBtn4" name="group2" type="radio"
                               @if($page_type && $resource_info->alive_type==3) checked @endif/>
                        <label for="aBtn4" class="pptAliveSelect">
                            PPT直播
                            <span class="text1">适用于通过演示文档（图片在顶部区域显示）等授课形式进行的直播</span>
                            <a class="text2" href="/helpCenter/problem?document_id=d_5979e53110cb7_cHcZ7ZVo" target="_blank">了解新功能</a>
                        </label>
                    </div>

                    @if(\App\Http\Controllers\Tools\AppUtils::isOursApp())
                        <div class="radioBtn3 @if($page_type&&$resource_info->alive_type!=2) hide @endif ">{{--在线直播 与 非在线直播 一经设置 不能切换--}}
                            <input class="with-gap" id="aBtn3" name="group2" type="radio"
                                    @if($page_type && $resource_info->alive_type==2) checked @endif/>
                            <label for="aBtn3" class="aliveSelect">
                                在线直播
                                <span>一经设置，不可修改</span>
                            </label>
                        </div>
                    @endif
                @endif
                </div>
            </div>
            {{--直播时间--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    直播时间<span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    <div class="dataMidWord">开始时间</div>
                    <div class="dateBox">
                        <input class="inputDefault dateInput dateSetInput" id="zb_start_at" name="upDate" type="text"
                                @if($page_type) value="{{$resource_info->zb_start_at}}" @endif/>
                        <div class="dateUpIcon">
                            <img src="../images/admin/resManage/icon_riqi.png"/>
                        </div>
                    </div>
                    <div class="dataMidWord">直播时长</div>
                    <div class="dateBox">
                        <select class="aliveDuration browser-default" id="_aliveDuration">
                            <option value="3600" @if($page_type && $resource_info->zb_stop_at == 3600) selected @endif >1小时</option>
                            <option value="5400" @if($page_type && $resource_info->zb_stop_at == 5400) selected @endif >1.5小时</option>
                            <option value="7200" @if($page_type && $resource_info->zb_stop_at == 7200) selected @endif >2小时</option>
                            <option value="9000" @if($page_type && $resource_info->zb_stop_at == 9000) selected @endif >2.5小时</option>
                            <option value="10800" @if($page_type && $resource_info->zb_stop_at == 10800) selected @endif >3小时</option>
                            <option value="86400" @if($page_type && $resource_info->zb_stop_at == 86400) selected @endif >1天</option>
                            <option value="172800" @if($page_type && $resource_info->zb_stop_at == 172800) selected @endif >2天</option>
                            <option value="315360000" @if($page_type && $resource_info->zb_stop_at == 315360000) selected @endif >永久</option>
                        </select>
                    </div>
                    <div class="dataMidWord2">*嘉宾（原讲师）可以在 直播间-操作 主动结束直播</div>
                </div>
            </div>
            {{-- 详情封面 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    详情封面<span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                    <div class="previewPic previewPicImage1">
                        <img src="@if($page_type) {{$resource_info->img_url}} @else ../images/admin/resManage/pic_addfengmian.png @endif"/>
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault coverUpbtn">
                        <span>选择文件</span>
                        <input accept="image/jpeg,image/png,image/gif,image/bmp"
                               class="upLoadImage upLoadImage1"type="file"/>
                        <input type="hidden" id="Image1Url" type="text"
                               @if($page_type) value="{{$resource_info->img_url}}" @endif/>
                    </div>
                    <div class="coverUpTip">
                        建议尺寸750*560px或4：3，JPG、PNG格式， 图片小于5M。
                    </div>
                </div>
            </div>
            {{-- 直播宣传封面 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    直播宣传封面
                </div>
                <div class="resAddSectionC">
                    {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                    <div class="previewPicAliveC previewPicImage3">
                        <img src="@if($page_type&&$resource_info->alive_img_url!=null) {{$resource_info->alive_img_url}} @elseif($page_type&&$resource_info->alive_img_url==null) ../images/admin/resManage/pic_home_live_2-2.jpg @else ../images/admin/resManage/pic_home_live_2-2.jpg @endif"/>
                    </div>
                    <div class="waves-effect btnSmall xeBtnDefault coverUpbtn">
                        <span>
                            选择文件
                        </span>
                        <input accept="image/jpeg,image/png,image/gif,image/bmp"
                               class="upLoadImage upLoadImage3"
                               type="file"/>
                        <input type="hidden" id="Image3Url" type="text"
                               @if($page_type) value="{{$resource_info->alive_img_url}}"
                               @else value="" @endif/>
                    </div>
                    <div class="coverUpTip">
                        建议尺寸750*240px，JPG、PNG格式，图片小于1M。不上传则为默认图。
                    </div>
                </div>
            </div>
            {{--直播详情--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    直播详情
                </div>
                <div class="resAddSectionC">
                    {{--临时存储--}}
                    @if($page_type)
                        <input id="rubbish" type="hidden" value="{{$resource_info->org_content}}" />
                    @endif
                    <div class="resDescribe">
                        <script id="resource_desc" type="text/plain"></script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('resource_desc', ueditor_config);

                            @if($page_type)
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

            <div class="boxLine"></div>
        </div>
        <div class="resAddPart resAddPart2">
            <div classs="resAddPartTitle">
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
            @endif
            {{--上架时间--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    上架时间<span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    <div class="dateBox">
                        <input class="inputDefault dateInput dateSetInputBottom" id="dateInput" name="upDate" type="text"
                               @if($page_type) value="{{$resource_info->start_at}}" @endif/>
                        <div class="dateUpIcon">
                            <img src="../images/admin/resManage/icon_riqi.png"/>
                        </div>
                    </div>
                </div>
            </div>

            {{--开课提醒--}}
            {{--dump($resource_info)--}}
            @if( \App\Http\Controllers\Tools\AppUtils::IsPageVisual('message_push', 'version_type') &&
            ( ($upload_channel_type != 1 && Illuminate\Support\Facades\Input::get('price', 0)>0) || $upload_channel_type==1 ) )
            <div class="resAddSection" @if($page_type && $resource_info->payment_type==1) style="display: none;" @endif id="courseRemind">
                <div class="resAddSectionT">
                    开课提醒
                </div>
                @if(!$page_type || ($page_type&&$resource_info->if_push<2))
                <div class="resAddSectionC">
                    <select id="push_ahead" class="browser-default pull-left" data-setting="{{$is_set_temp}}" style="width: 200px;">
                        <option value="-1" @if($page_type && $resource_info->push_ahead==-1) selected @endif>不提醒</option>
                        <option value="0" @if($page_type && $resource_info->push_ahead==0) selected @endif>直播开始时</option>
                        <option value="5" @if($page_type && $resource_info->push_ahead==5) selected @endif>5分钟前</option>
                        <option value="15" @if($page_type && $resource_info->push_ahead==15) selected @endif>15分钟前</option>
                        <option value="30" @if($page_type && $resource_info->push_ahead==30) selected @endif>30分钟前</option>
                        <option value="60" @if($page_type && $resource_info->push_ahead==60) selected @endif>1小时前</option>
                        <option value="1440" @if($page_type && $resource_info->push_ahead==1440) selected @endif>1天前</option>
                    </select>
                    <div class="pull-left alivetips" style="margin-left: 20px;line-height: 34px; font-size:14px;">
                        <a target="_black" href="/helpCenter/problem?document_id=d_58f0bdbc5e504_LKZ3ouHf">什么是开课提醒？</a>
                    </div>
                </div>
                @elseif($page_type&&$resource_info->if_push==2)
                <div class="upload_input_msg" style="font-size: 14px; margin-left: 0;">
                    <span>消息已推送</span>
                    <input id="if_push" type="hidden" value="{{$resource_info->if_push}}" />
                </div>
                @elseif($page_type&&$resource_info->if_push==3)
                <div class="upload_input_msg" style="font-size: 14px; margin-left: 0;">
                    <span>消息推送失败</span>
                    <input id="if_push" type="hidden" value="{{$resource_info->if_push}}" />
                </div>
                @endif
            </div>
            @endif

            <div class="boxLine"></div>
        </div>

        <div class="friendlyTip">*友情提示：您可以在直播创建完成之后进行嘉宾（原讲师）设置</div>
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






















