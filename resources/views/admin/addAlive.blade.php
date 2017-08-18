<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '新增直播';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    {{--弹窗插件--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link rel="stylesheet" type="text/css" href="../css/admin/addAlive.css?{{env('timestamp')}}"/>    {{--css--}}
    {{--时间选择器--}}
    <link rel="stylesheet" type="text/css" href="../css/external/bootstrap-datetimepicker.min.css"  />
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js"></script>
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--腾讯云点播--}}
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/js/vod/sdk/uploaderh5.js" charset="utf-8"></script>
    {{--文本编辑器--}}
    <script type="text/javascript" src="../ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="../ueditor/ueditor.all.js"></script>
    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    {{--弹窗插件--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/addAlive.js?{{env('timestamp')}}"></script>   {{--js--}}
    <script>
        url_app_id = "{{ \App\Http\Controllers\Tools\AppUtils::getAppID() }}";
    </script>
@endsection

@section('base_mainContent')
    <div class="header"><h3>新增直播</h3></div>

    <div class="content">

        <div class="aliveInputArea">
            <div class="aliveLabel">直播名称</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="title" placeholder="请输入直播名称" />
            </div>
            <div class="aliveTip">请设置直播的名称</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">直播简介</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="summary" placeholder="请输入直播简介" />
            </div>
            <div class="aliveTip">请设置直播的简介,建议不要太长,控制在14个汉字以内。</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">直播宣传封面</div>
            <div class="aliveInput" style="width:200px;height:64px;position: relative;">
                <input type="file" class="homePicUpload" id="alive_img_url" accept="image/jpeg,image/png,image/gif,image/bmp" />
                <img src="../images/icon_close.png" class="homePicClose hide" />
                <img src="../images/home_pic_add.png" class="homePicAdd" />
                <img class="homePicShow hide" />
            </div>
            <div class="aliveTip">(注：像素750*240或者宽高比与此相同)</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">详情封面</div>
            <div class="aliveInput" style="width:130px;height:100px;position: relative;">
                <input type="file" class="picUpload" id="img_url" accept="image/jpeg,image/png,image/gif,image/bmp" />
                <img src="../images/icon_close.png" class="picClose hide" />
                <img src="../images/pic_add.png" class="picAdd" />
                <img class="picShow hide" />
            </div>
            <div class="aliveTip">(注：像素750*560或者宽高比与此相同)</div>
        </div>

        {{--3种收费形式--}}
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
                        <input type="text" class="form-control long" style="margin-right: 5px;"
                        placeholder="付费金额,例如100.00" id="piece_price" />元
                    </div>
                    {{--专栏时的展示--}}
                    <div class="packageShow hide" style="width: 300px;">
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
                            <input id="single_price" class="form-control single_long" placeholder="付费金额,例如100.00" />
                            <span >元</span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="aliveTip">请选择你的产品类型,目前支持:专栏、单卖、免费</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">直播时间</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="zb_start_at" placeholder="请输入直播开始时间" />
                <div class="zhi">至</div>
                <input type="text" class="form-control long" id="zb_stop_at" placeholder="请输入直播结束时间"/>
            </div>
            <div class="aliveTip">注意与直播开始时间之差要大于视频时长</div>
        </div>

        <div class="aliveInputArea">
            <div class="aliveLabel">上架时间</div>
            <div class="aliveInput">
                <input type="text" class="form-control long" id="start_at" placeholder="请输入直播上架时间"/>
            </div>
            <div class="aliveTip">请设置直播上架时间</div>
        </div>

        {{-- dump( $appModuleInfo ) --}}
        {{--开课提醒的权限控制--}}
        @if( \App\Http\Controllers\Tools\AppUtils::IsPageVisual('message_push', 'version_type') )
                <div class="aliveInputArea" id="courseRemind" style="display:none;margin-bottom: 30px;">
                    <div class="aliveLabel">开课提醒</div>
                    <div class="searchSelectArea" style="width: 90px;float: left;margin-right: 20px;">
                        <select class="form-control" id="push_ahead">
                            <option value="-1" selected="selected">不提醒</option>
                            <option value="0">直播开始时</option>
                            <option value="5">5分钟前</option>
                            <option value="15">15分钟前</option>
                            <option value="30">30分钟前</option>
                            <option value="60">1小时前</option>
                            <option value="1440">1天前</option>
                        </select>
                    </div>

                    <div class="fleft alivetips" style="line-height: 34px; font-size:14px;">
                        <a target="_black" href="/help/instructions#hp5_alive_prompt">什么是开课提醒？</a>
                    </div>

                </div>
        @else
            <input type="hidden" id="push_ahead" value="-1" />
        @endif

        <div class="aliveInputArea">
            <div class="aliveLabel">直播类型</div>
            <div class="aliveInput" style="position:relative;width: 800px;height: 100px;">
                {{--直播类型选择区域--}}
                <div class="aliveTypeArea">
                    <div class="aliveTypeSelect aliveBlue">语音</div>
                    @if($appModuleInfo)
                        @if($appModuleInfo[0]->alive_by_video)
                            <div class="aliveTypeSelect">视频</div>
                            <button class="btn btn-default hide" id="alive_video_url">选择文件</button>
                        @endif
                    @endif
                </div>

                <div class="aliveTip" style="padding-left: 0;">语音直播：讲师是可以发语音和文字和图片进行直播，不需要上传录制好的视频</div>

                {{--上传文件的信息展示--}}
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

        <div class="aliveInputArea">
            <div class="aliveLabel">直播描述</div>
            <div class="aliveInput">
                <script id="descrb"  type="text/plain"></script>
                <script type="text/javascript">
                    var ue = UE.getEditor('descrb',ueditor_config);
                </script>
            </div>
        </div>

        {{--直播人员管理--}}
        <div class="aliveInputArea">
            <div class="firstLine">
                <div class="aliveLabel">直播人员管理</div>
                <div class="firstLineLeft">添加人员</div>
                <div class="firstLineRight">标签（例如讲师、管理员）</div>
            </div>

            <div class="eachLine">
                <div class="user_name">点击添加</div>
                <div class="roleNameArea">
                    <input type="text" class="form-control center" value="讲师" maxlength="16" />
                </div>
                <div class="clear">清空</div>
                <input type="hidden" class="user_id" />
            </div>

            <div class="eachLine">
                <div class="user_name">点击添加</div>
                <div class="roleNameArea">
                    <input type="text" class="form-control center" value="讲师" maxlength="16" />
                </div>
                <div class="clear">清空</div>
                <input type="hidden" class="user_id" />
            </div>

            <div class="eachLine">
                <div class="user_name">点击添加</div>
                <div class="roleNameArea">
                    <input type="text" class="form-control center" value="讲师" maxlength="16"/>
                </div>
                <div class="clear">清空</div>
                <input type="hidden" class="user_id" />
            </div>

            <div class="addLine">+</div>
        </div>

        {{--分类导航栏的选择----同时判断 version_type + app_module--}}
        @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category","version_type") &&
            \App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category","app_module"))
            <div class="upload_item_div">
                <div class="upload_item_title">所属分类</div>
                @foreach($category_info as $key => $value)
                    <div class="checkBoxWrapper">
                        {{--编辑页面 需要初始化checkbox状态--}}
                        <input id="{{$key}}" class="radio_category" name="category" type="checkbox" value="{{$key}}"/>
                        <label for="{{$key}}" class="checkboxLabel"></label>
                        <label for="{{$key}}" class="checkboxText">{{$value}}</label>
                    </div>
                @endforeach
            </div>
        @endif

        <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
    </div>

    <div class="footer">
        <button id="finish">新增直播</button>
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


