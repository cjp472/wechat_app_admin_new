<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '内容管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    {{--弹窗插件--}}
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link type="text/css" rel="stylesheet" href="../css/admin/addAlive.css?{{env('timestamp')}}" />   {{--css--}}
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet" />
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js?{{env('timestamp')}}"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js?{{env('timestamp')}}"></script>
    {{--腾讯云点播--}}
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js?{{env('timestamp')}}" charset="utf-8"></script>
    {{--文本编辑器--}}
    <script type="text/javascript" src="../ueditor/ueditor.config.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../ueditor/ueditor.all.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    {{--弹窗插件--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/editAlive.js?{{env('timestamp')}}"></script>     {{--JavaScript--}}
    <script>
        $(document).ready(function () {
            setTopUrlInfo('alive_listop');
            url_app_id = "{{ \App\Http\Controllers\Tools\AppUtils::getAppID() }}";
        });
    </script>
@endsection


@section('base_mainContent')
    <input class="admin_data hide" data-alive_id="{{$alive->id}}">

    <div class="header"><h3>编辑直播</h3></div>

    <div class="content">

        <div class="aliveInputArea">
            <div class="aliveLabel">直播名称</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="title" placeholder="请输入直播名称"
                value="{{$alive->title}}"/>
            </div>
            <div class="aliveTip">请设置直播的名称</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">直播简介</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="summary" placeholder="请输入直播简介"
                value="{{$alive->summary}}"/>
            </div>
            <div class="aliveTip">请设置直播的简介,建议不要太长,控制在14个汉字以内。</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">直播宣传封面</div>
            <div class="aliveInput" style="width:200px;height:64px;position: relative;">
                <input type="file" class="homePicUpload" id="alive_img_url" accept="image/jpeg,image/png,image/gif,image/bmp" />
                <img src="../images/icon_close.png" class="homePicClose" />
                <img src="../images/home_pic_add.png" class="homePicAdd hide" />
                <img class="homePicShow" src="{{$alive->alive_img_url}}" />
            </div>
            <div class="aliveTip">(注：像素750*240或者宽高比与此相同)</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">直播封面</div>
            <div class="aliveInput" style="width:130px;height:100px;position: relative;">
                <input type="file" class="picUpload" id="img_url" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                <img src="../images/icon_close.png" class="picClose" />
                <img src="../images/pic_add.png" class="picAdd hide" />
                <img class="picShow" src="@if($alive->img_url_compressed){{$alive->img_url_compressed}}@else{{$alive->img_url}}@endif" />
            </div>
            <div class="aliveTip">(注：像素750*560或者宽高比与此相同)</div>
        </div>

        @if($alive->payment_type==1) {{--免费--}}
        <div class="aliveInputArea">
            <div class="aliveLabel">收费形式</div>
            <div class="aliveInput">
                <div class="typeSelect">专栏</div>
                <div class="typeSelect">单卖</div>
                <div class="typeSelect blue">免费</div>

                {{--对应收费形式的展示--}}
                <div class="productShow">
                    {{--付费时的展示--}}
                    <div class="payShow hide">
                        <input type="text" class='form-control long' style='margin-right: 5px;'
                               placeholder="付费金额,例如100.00" id="piece_price"/>元
                    </div>
                    {{--专栏时的展示--}}
                    <div class="packageShow hide" style="width: 300px">
                        <select class="form-control" id="packageInfo">
                            @foreach($packages as $key => $value)
                                <option value="{{$value->id}}" price="{{$value->price}}" state="{{$value->state}}">
                                    {{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="package_side_pay hide">
                        <div id="checkbox-img" class="checkbox-img" checked-state="unchecked" style="background-position: -78px 0px;"></div>
                        <span style="margin-left: 4px">专栏外单卖</span>
                        <div id="single_price_div" class="single_price_div hide" >
                            <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" >
                            <span >元</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="aliveTip">请选择你的产品类型,目前支持:专栏、单卖、免费</div>
        </div>
        @elseif($alive->payment_type==2){{--单个收费--}}
             @if($single_sale == 0)
                 <div class="aliveInputArea">
                    <div class="aliveLabel">收费形式</div>
                    <div class="aliveInput">
                        <div class="typeSelect">专栏</div>
                        <div class="typeSelect blue">单卖</div>
                        <div class="typeSelect">免费</div>


                        {{--对应收费形式的展示--}}
                        <div class="productShow">
                            {{--付费时的展示--}}
                            <div class="payShow">
                                <input type="text" class='form-control long' style='margin-right: 5px;'
                               placeholder="付费金额,例如100.00" id="piece_price" value="{{$alive->piece_price/100}}"/>元
                            </div>
                            {{--专栏时的展示--}}
                            <div class="packageShow hide" style="width: 300px">
                                <select class="form-control" id="packageInfo">
                                    @foreach($packages as $key => $value)
                                        <option value="{{$value->id}}" price="{{$value->price}}" state="{{$value->state}}">
                                            {{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="package_side_pay hide">
                                <div id="checkbox-img" class="checkbox-img" checked-state="unchecked" style="background-position: -78px 0px;"></div>
                                <span style="margin-left: 4px">专栏外单卖</span>
                                <div id="single_price_div" class="single_price_div hide" >
                                    <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" >
                                    <span >元</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="aliveTip">请选择你的产品类型,目前支持:专栏、单卖、免费</div>
                 </div>
             @else
                <div class="aliveInputArea">
                    <div class="aliveLabel">收费形式</div>
                    <div class="aliveInput">
                        <div class="typeSelect blue">专栏</div>
                        <div class="typeSelect">单卖</div>
                        <div class="typeSelect">免费</div>


                        {{--对应收费形式的展示--}}
                        <div class="productShow">
                            {{--付费时的展示--}}
                            <div class="payShow hide">
                                <input type="text" class='form-control long' style='margin-right: 5px;'
                                       placeholder="付费金额,例如100.00" id="piece_price" />元
                            </div>
                            {{--专栏时的展示--}}
                            <div class="packageShow " style="width: 300px">
                                <select class="form-control" id="packageInfo">
                                    @foreach($packages as $key => $value)
                                        <option value="{{$value->id}}" price="{{$value->price}}" state="{{$value->state}}"
                                                @if($value->id == $alive->product_id)selected="selected"@endif>{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="package_side_pay">
                                <div id="checkbox-img" class="checkbox-img" checked-state="checked" style="background-position: -104px 0px;"></div>
                                <span style="margin-left: 4px">专栏外单卖</span>
                                <div id="single_price_div" class="single_price_div" >
                                    <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" value="{{$alive->piece_price/100}}">
                                    <span >元</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="aliveTip">请选择你的产品类型,目前支持:专栏、单卖、免费</div>
                </div>
             @endif
        @else
            <div class="aliveInputArea">
                <div class="aliveLabel">收费形式</div>
                <div class="aliveInput">
                    <div class="typeSelect blue">专栏</div>
                    <div class="typeSelect">单卖</div>
                    <div class="typeSelect">免费</div>
                    {{--对应收费形式的展示--}}
                    <div class="productShow">
                        {{--付费时的展示--}}
                        <div class="payShow hide">
                            <input type="text" class='form-control long' style='margin-right: 5px;'
                            placeholder="付费金额,例如100.00" id="piece_price" />元
                        </div>
                        {{--专栏时的展示--}}
                        <div class="packageShow " style="width: 300px">
                            <select class="form-control" id="packageInfo">
                                @foreach($packages as $key => $value)
                                    <option value="{{$value->id}}" price="{{$value->price}}" state="{{$value->state}}"
                                    @if($value->id == $alive->product_id)selected="selected"@endif>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="package_side_pay">
                            <div id="checkbox-img" class="checkbox-img" checked-state="unchecked" style="background-position: -78px 0px;"></div>
                            <span style="margin-left: 4px">专栏外单卖</span>
                            <div id="single_price_div" class="single_price_div hide" >
                                <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" >
                                <span >元</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="aliveTip">请选择你的产品类型,目前支持:专栏、单卖、免费</div>
            </div>
        @endif

        <div class="aliveInputArea">
            <div class="aliveLabel">直播时间</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="zb_start_at" placeholder="请输入直播开始时间"
                value="{{$alive->zb_start_at}}" />
                <div class="zhi">至</div>
                <input type="text" class="form-control long" id="zb_stop_at" placeholder="请输入直播结束时间"
               value="{{$alive->zb_stop_at}}" />
            </div>
            <div class="aliveTip">注意与直播开始时间之差要大于视频时长</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">上架时间</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="start_at" value="{{$alive->start_at}}" />
            </div>
            <div class="aliveTip">请设置直播上架时间</div>
        </div>


        @if( \App\Http\Controllers\Tools\AppUtils::IsPageVisual('message_push', 'version_type') )
            <div class="aliveInputArea" id="courseRemind" style="display:none; margin-bottom: 30px;">
                <div class="aliveLabel">开课提醒</div>
                <div class="searchSelectArea" style="width: 90px;float:left;margin-right:20px;">
                    <select class="form-control" id="push_ahead">
                        <option value="-1" @if($alive->push_ahead == -1) selected="selected" @endif>不提醒</option>
                        <option value="0" @if($alive->push_ahead == 0) selected="selected" @endif>直播开始时</option>
                        <option value="5" @if($alive->push_ahead == 5) selected="selected" @endif>5分钟前</option>
                        <option value="15" @if($alive->push_ahead == 15) selected="selected" @endif>15分钟前</option>
                        <option value="30" @if($alive->push_ahead == 30) selected="selected" @endif>30分钟前</option>
                        <option value="60" @if($alive->push_ahead == 60) selected="selected" @endif>1小时前</option>
                        <option value="1440" @if($alive->push_ahead == 1440) selected="selected" @endif>1天前</option>
                    </select>
                </div>

                <div class="fleft alivetips" style="line-height: 34px; font-size:14px;">
                    <a target="_black" href="/help/instructions#hp5_alive_prompt">什么是开课提醒？</a>
                </div>
            </div>
        @else
            <input type="hidden" id="push_ahead" value="-1" />
        @endif


        {{--语音--}}
        @if($alive->alive_type==0)
            <div class="aliveInputArea">
                <div class="aliveLabel">直播类型</div>
                <div class="aliveInput" style="position:relative;width: 800px;height: 100px;">
                    <div class="aliveTypeArea">
                        <div class="aliveTypeSelect aliveBlue">语音</div>
                        @if($appModuleInfo)
                            @if($appModuleInfo[0]->alive_by_video)
                                <div class="aliveTypeSelect hide">视频</div>
                                <button class="btn btn-default hide" id="alive_video_url">选择文件</button>
                            @endif
                        @endif
                    </div>

                    <div class="aliveTip" style="padding-left: 0;">语音直播：讲师是可以发语音和文字和图片进行直播，不需要上传录制好的视频</div>

                    <div id="videoShow">
                        <div id="videoName"></div>
                        <div class="progress hide" style="width: 300px;height: 20px;float: left;">
                            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0"
                                 aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                        </div>
                        <span class="deleteVideo hide">删除</span>
                    </div>
                </div>
            </div>
        @else
            <div class="aliveInputArea">
                <div class="aliveLabel">直播类型</div>
                <div class="aliveInput" style="position:relative;width: 600px;height: 100px;">
                    <div class="aliveTypeArea">
                        <div class="aliveTypeSelect hide">语音</div>
                        <div class="aliveTypeSelect aliveBlue">视频</div>
                        <button class="btn btn-default" id="alive_video_url">选择文件</button>
                    </div>

                    <div class="aliveTip" style="padding-left: 0;">视频直播：需要上传录制好的视频，直播形式为直播视频时间+互动答疑时间，互动答疑时间为语音直播形式</div>

                    <div id="videoShow">
                        <div id="videoName"></div>
                        <div class="progress hide" style="width: 300px;height: 20px;float: left;">
                            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0"
                                 aria-valuemin="0" aria-valuemax="100" style="width: 0;"></div>
                        </div>
                        <span class="deleteVideo hide">删除</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="aliveInputArea">
            <input type="hidden" id="rubbish" value="{{$alive->org_content}}"/>
            <div class="aliveLabel">直播描述</div>
            <div class="aliveInput">
                <script id="descrb"  type="text/plain"></script>
                <script type="text/javascript">
                    var ue = UE.getEditor('descrb',ueditor_config);
                    ue.ready(function()
                    {
                        ue.setContent($("#rubbish").val());
                    });
                </script>
            </div>
        </div>

        {{--直播人员管理--}}
        @if(count($alive_roles)<=3)
        <div class="aliveInputArea">
            <div class="firstLine">
                <div class="aliveLabel">直播人员管理</div>
                <div class="firstLineLeft">添加人员</div>
                <div class="firstLineRight">标签</div>
            </div>

            @foreach($alive_roles as $key => $value)
            <div class="eachLine">
                <div class="user_name" style="color:#000;">{{$value->user_name}}</div>
                <div class="roleNameArea">
                    <input type="text" class="form-control center" style="color:#000;"
                    value="{{$value->role_name}}" />
                </div>
                <div class="clear">清空</div>
                <input type="hidden" class="user_id" value="{{$value->user_id}}"/>
            </div>
            @endforeach

            @for($i=0;$i<3-count($alive_roles);$i++)
            <div class="eachLine">
                <div class="user_name">点击添加</div>
                <div class="roleNameArea">
                    <input type="text" class="form-control center" value="讲师" maxlength="16" />
                </div>
                <div class="clear">清空</div>
                <input type="hidden" class="user_id" />
            </div>
            @endfor

            <div class="addLine">+</div>
        </div>
        @else
        <div class="aliveInputArea">
            <div class="firstLine">
                <div class="aliveLabel">直播人员管理</div>
                <div class="firstLineLeft">添加人员</div>
                <div class="firstLineRight">标签（例如讲师、管理员）</div>
            </div>

            @foreach($alive_roles as $key => $value)
                <div class="eachLine">
                    <div class="user_name" style="color:#000;">{{$value->user_name}}</div>
                    <div class="roleNameArea">
                        <input type="text" class="form-control center" style="color:#000;"
                        value="{{$value->role_name}}" />
                    </div>
                    <div class="clear">清空</div>
                    @if($key >= 3)
                        <div class="shuxian">|</div>
                        <div class="delete">删除</div>
                    @endif
                    <input type="hidden" class="user_id" value="{{$value->user_id}}"/>
                </div>
            @endforeach
            <div class="addLine">+</div>
        </div>
        @endif

        {{--分类导航栏的选择----同时判断 version_type + app_module--}}
        @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category","version_type") &&
            \App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category","app_module"))
            <div class="upload_item_div @if($alive->payment_type == 3) hide @endif">
                <div class="upload_item_title">所属分类</div>
                @foreach($category_info as $key => $value)
                <div class="checkBoxWrapper">
                    {{--编辑页面 需要初始化checkbox状态--}}
                    <input id="{{$key}}" class="radio_category" name="category" type="checkbox" value="{{$key}}"  @if(in_array($key,$package_category)) checked="checked" @endif />
                    <label for="{{$key}}" class="checkboxLabel"></label>
                    <label for="{{$key}}" class="checkboxText">{{$value}}</label>
                </div>
                @endforeach
            </div>
        @endif


        <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}" />
    </div>

    <div class="footer">
        <button id="finish">保存修改</button>
    </div>
@stop

@section('base_modal')
    {{--搜索讲师--}}
    <div class="modal fade" id="zbModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 640px;margin-top: 150px;">
            <div class="modal-content" style="height: 360px;width: 640px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">添加人员</span></div>
                </div>

                <div class="modal-body" style="height: 210px;overflow-y:scroll;overflow-x:hidden;">
                    <div id="searchArea">
                        <button class="btn btn-default" id="searchButton">搜索</button>
                        <input type="text" class="form-control long" id="search" placeholder="请输入讲师的昵称或者手机号"/>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 0px;">
                    <button type="button" class="btn btn-primary btn-blue">选择</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
@stop


