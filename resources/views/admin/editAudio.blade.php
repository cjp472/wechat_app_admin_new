<?php
$pageData = [];
$pageData['sideActive'] = 'create_content';
$pageData['barTitle'] = '内容创建';
?>
@extends('admin.baseLayout',$pageData)



@section('page_css')
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}" rel="stylesheet">
    {{--文件选择框美化--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery.nice-file-input.css?{{env('timestamp')}}">
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/upload.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/external/jquery.nice-file-input.js?{{env('timestamp')}}"></script>
    {{--右键菜单插件--}}
    <script type="text/javascript" src="../js/external/bootstrapMenu.min.js?{{env('timestamp')}}"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js?{{env('timestamp')}}"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js?{{env('timestamp')}}"></script>


    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../ueditor/ueditor.config.js?{{env('timestamp')}}"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="../ueditor/ueditor.all.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/editResource.js?{{env('timestamp')}}"></script>

    <script>
        pay_type = '{{$audio->payment_type}}';
        $(document).ready(function () {
            setTopUrlInfo('audio_listop');
        });
    </script>
@endsection


@section('base_mainContent')

    <div class="upload_title">
        <span>编辑音频</span>
    </div>

    <span style="display: none" id="data" data-id={{$audio->id}}></span>

    <div class="upload_item_div">
        <div class="upload_item_title">音频名称</div>
        <div class="upload_input_div">
            <input class="form-control long" placeholder="请输入音频名称" id="resource_title" value="{{$audio->title}}"/>
            <div class="upload_input_msg">
                <span>请设置音频的名称,建议不要太长,控制在14个汉字以内。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">收费形式</div>
        <div class="upload_input_div">

            @if($audio->payment_type!=3)

                @if($audio->payment_type==1)
                    <div class="upload_type" id="package_select">专栏</div>
                    <div class="upload_type" id="single_select">单卖</div>
                    <div class="upload_type border_blue" id="free_select">免费</div>


                    <div id="price_div" class="hide"><input id="resource_price" class="form-control long" readonly
                                placeholder="付费金额,例如100.00" value="{{$audio->piece_price/100}}"/>
                        <span class="price_unit">元</span>
                    </div>
                    <div class="searchSelectArea hide" style="width: 300px">
                        @if( count($package_list) > 0)
                        <select class="form-control long hide" id="resource_package">
                        @foreach($package_list as $key=>$package)
                            <option value="{{$package->id}}" about="{{ $package->price }}" >{{$package->name}}</option>
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
                @else
                    @if($single_sale == 0)
                    <div class="upload_type" id="package_select">专栏</div>
                    <div class="upload_type border_blue" id="single_select">单卖</div>
                    <div class="upload_type" id="free_select">免费</div>


                        <div id="price_div"><input id="resource_price" class="form-control long"
                                placeholder="付费金额,例如100.00" value="{{$audio->piece_price/100}}"/>
                        <span class="price_unit">元</span>
                    </div>
                    <div class="searchSelectArea hide" style="width: 300px">
                        @if( count($package_list) > 0)
                        <select class="form-control long hide" id="resource_package">
                        @foreach($package_list as $key=>$package)
                            <option value="{{$package->id}}" about="{{ $package->price }}">{{$package->name}}</option>
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
                    @else
                        <div class="upload_type border_blue" id="package_select">专栏</div>
                        <div class="upload_type" id="single_select">单卖</div>
                        <div class="upload_type" id="free_select">免费</div>


                        <div id="price_div" class="hide"><input id="resource_price" class="form-control long " type="text"  placeholder="付费金额,例如100.00"/>
                            <span class="price_unit">元</span>
                        </div>
                        <div>
                            <select class="form-control long" id="resource_package">
                                @foreach($package_list as $key=>$package)
                                    @if($package->id==$audio->product_id)
                                        <option value="{{$package->id}}" about="{{ $package->price }}" state="{{ $package->state }}"
                                                selected> {{$package->name}}</option>
                                    @else
                                        <option value="{{$package->id}}" about="{{ $package->price }}" state="{{ $package->state }}">
                                            {{$package->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="package_side_pay">
                            {{--<input id="single_pay" class="single_pay" name="single_pay" type="radio" value="1" />--}}
                            <div id="checkbox-img" class="checkbox-img" checked-state="checked" style="background-position: -104px 0px;"></div>
                            <span style="margin-left: 4px">专栏外单卖</span>
                            <div id="single_price_div" class="single_price_div" >
                                <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" value="{{$audio->piece_price/100}}">
                                <span >元</span>
                            </div>
                        </div>
                        <div class="upload_input_msg">
                            <span>请选择你的产品类型,目前支持:专栏、单卖、免费。</span>
                        </div>
                    @endif
                @endif



            @else
                <div class="upload_type border_blue" id="package_select">专栏</div>
                <div class="upload_type" id="single_select">单卖</div>
                <div class="upload_type" id="free_select">免费</div>


                <div id="price_div" class="hide"><input id="resource_price" class="form-control long " type="text"
                            placeholder="付费金额,例如100.00"/>
                    <span class="price_unit">元</span>
                </div>
                <div>
                    <select class="form-control long" id="resource_package">
                        @foreach($package_list as $key=>$package)
                        @if($package->id==$audio->product_id)
                            <option value="{{$package->id}}" about="{{ $package->price }}" state="{{ $package->state }}"
                            selected> {{$package->name}}</option>
                        @else
                            <option value="{{$package->id}}" about="{{ $package->price }}" state="{{ $package->state }}">
                            {{$package->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="package_side_pay">
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
            @endif
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">音频封面</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div">
                    <input type="file" class="pic_input" id="resource_pic" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                    <img class="pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/addbanner.png" class="pic_add hide"/>
                    <img class="pic_show" src="@if($audio->img_url_compressed){{$audio->img_url_compressed}}@else{{$audio->img_url}}@endif"/>
                </div>
                <script>
                    old_pic_url = '{{$audio->img_url}}';
                </script>
                <img id='icon_uploadPic' class="pic_upload hide" src="../images/icon_close.png"/>
                <img id='icon_uploadPic_success' class="pic_upload_success hide" src="../images/icon_success.png"/>
            </div>
            <div class="upload_input_msg">
                <span>(注：像素750*560或者宽高比与此相同)</span>
            </div>
        </div>
    </div>

    @if($result)
        @if($result[0]->daily_sign == 1)
            <div class="upload_item_div" style="height: 280px">
                <div class="upload_item_title">日签(选)</div>
                <div class="upload_input_div">
                    <div style="height: 260px">
                        <div class="sign_div">
                            <input type="file" class="pic_input" id="sign_pic" style="width: 180px;height: 240px" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                            <img class="sign_close hide" src="../images/icon_close.png"/>
                            <img src="../images/addbanner.png" class="sign_add hide"/>
                            <img class="sign_show" src="@if($audio->sign_url_compressed){{$audio->sign_url_compressed}}@else{{$audio->sign_url}}@endif"/>
                        </div>
                        <img id='icon_uploadSign' class="pic_upload hide" src="../images/icon_close.png"/>
                        <img id='icon_uploadSign_success' class="pic_upload_success hide" src="../images/icon_success.png"/>
                    </div>
                    <div class="upload_input_msg">
                        <span>(注：日签宽高比为3:4,宽高分别不小于600、800)</span>
                    </div>
                </div>
                <script>
                    old_sign_url = '{{$audio->sign_url}}';
                </script>

                {{--<div class="upload_item_title" style="width: 20px;margin-left: 40px">注:</div>--}}
                {{--<div class="upload_input_div" style="margin-left: 20px">--}}
                    {{--<div style="height: 280px">--}}
                        {{--<div class="pic_div">--}}
                            {{--<img src="../images/sign_example.png" class="sign_add"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
        @endif
    @endif

    @if($result)
        @if($result[0]->try_audio == 1)
            <script>
                try_url = '{{$audio->try_audio_url}}';
            </script>
            <div class="upload_item_div">
                <div class="upload_item_title">试听上传</div>
                <div class="upload_input_div">
                    <button class="btn_default btn_tryAudio">选择文件</button>
                    <input type="file" name="audio_file" id="public_try" style="opacity: 0;width: 82px;height: 34px;margin-top: -34px" />
                    <div class="audio_tryUrl">
                        <input type="text" class="tyr_Url tyr_Url_tryAudio">
                        <script>
                            old_try_url = '{{$audio->try_audio_url}}';
                        </script>
                        <div class='progress p_try' style='display: none;margin-left: 0px;width: 50%;height: 10px;vertical-align: top;margin-top: 12px;'>
                            <div id='progress_try' class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%;'>
                            </div>
                        </div>
                        <button class="btn_cancelTry hide">上传中</button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @if($result)
        @if($result[0]->sound_effect == 1)
            <div class="upload_item_div">
                <div class="upload_item_title">音频上传(无音效)</div>
                <div class="upload_input_div">
                    <button class="btn_default btn_tryAudio">选择文件</button>
                    <input type="file" name="audio_file" id="public_noEffect" style="opacity: 0;width: 82px;height: 34px;margin-top: -34px" />
                    <div class="audio_noEffectUrl">
                        <input type="text" class="tyr_Url tyr_Url_noEffect">
                        <div class='progress p_noEffect' style='display: none;margin-left: 0px;width: 50%;height: 10px;vertical-align: top;margin-top: 12px;'>
                            <div id='progress_noEffect' class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%;'>
                            </div>
                        </div>
                        <button class="btn_cancelNoEffect hide">上传中</button>
                    </div>
                    {{--<img id='icon_uploadTry_success' class="pic_upload_success hide" src="../images/icon_success.png"/>--}}
                @if($noEffect)
                    <script>
                        old_noEffect_url = '{{$noEffect->audio_url}}';
                    </script>
                @endif
                </div>
            </div>
        @endif
    @endif


    <div class="upload_item_div">
        <div class="upload_item_title">音频上传</div>
        <div class="upload_input_div" id="public_audio">
            <input type="file" class="nicefile" name="audio_file" />
            <script>
                resource_url = '{{$audio->audio_url}}';
            </script>
        </div>
    </div>

    <input type="hidden" id="rubbish" value="{{$audio->org_content}}"/>
    <div class="upload_item_div">
        <div class="upload_item_title">音频描述</div>
        <div class="upload_input_div">
            <script id="resource_desc"  type="text/plain"></script>
            <script type="text/javascript">
                var ue = UE.getEditor('resource_desc',ueditor_config);
                ue.ready(function()
                {
                    ue.setContent($("#rubbish").val());
                });
            </script>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">上架时间</div>
        <div class="upload_input_div">
            <input class="form-control long" id="start_time" readonly value="{{$audio->start_at}}"/>
            <div class="upload_input_msg">
                <span>请设置上架时间</span>
            </div>
        </div>
    </div>

    @if( \App\Http\Controllers\Tools\AppUtils::IsPageVisual('message_push', 'version_type') )
        <div class="upload_item_div" style="display:none;" id="serviceContent" data-state="{{$audio->push_state}}" data-setting="{{$isHadSetTemp}}">
            <div class="upload_item_title">服务号通知</div>
            @if($audio->push_state != 2)
                <div class="upload_input_div">
                    <div class="rdoBox">
                        <input id="openService" class="rdo" type="radio" name="toggle" value="1" {{$audio->push_state==1 ? 'checked' : ''}}/>
                        <label for="openService" class="rdoSelf"></label>
                        <label for="openService">开启</label>
                    </div>
                    <div class="rdoBox">
                        <input id="closeService" class="rdo" type="radio" name="toggle" value="0" {{$audio->push_state==0 ? 'checked' : ''}} />
                        <label for="closeService" class="rdoSelf"></label>
                        <label for="closeService">关闭</label>
                    </div>

                    <div class="upload_input_msg">
                        <span>开启服务号通知后，内容上架后会向已订阅专栏的用户发送服务号模板消息。</span><a target="_blank" href="/help/instructions#hp5_wx_service">什么是模板消息？</a>
                    </div>
                </div>
            @else
                <div class="upload_input_msg" style="display: inline-block;font-size: 14px;">
                    <span>通知已发送</span>
                </div>
            @endif
        </div>
    @endif


    <div class="upload_button_div">
        <button class="btn btn-blue" onclick="saveEditResource('audio')">保存
        </button>
    </div>

    <div class="cancel_button_div">
        <button class="btn btn-blue" onclick="relistUrl('/audio_list')">取消
        </button>
    </div>

    <audio style="display:none;" id='resource_time' oncanplaythrough="getResourceDuration(this)"></audio>
    <audio style="display:none;" id='try_time' oncanplaythrough="getTryDuration(this)"></audio>
    <audio style="display:none;" id='noEffect_time' oncanplaythrough="getNoEffectDuration(this)"></audio>

    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
@stop

