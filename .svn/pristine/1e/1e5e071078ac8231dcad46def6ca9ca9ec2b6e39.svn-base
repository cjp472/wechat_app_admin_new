<?php
$pageData = [];
$pageData['sideActive'] = 'user_list';
$pageData['barTitle'] = '用户管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/admin/customerDetail.css?{{env('timestamp')}}" />
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/customerDetail.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    {{--头部--}}
    <div class="header"><h3>用户详情</h3>
        <button type="button" id="customerMsg" class="btn btn-default"
                data-target="#SmsModal">发消息</button>
    </div>

    {{--内容区--}}
    <div class="content">

        <div class="detailArea">

            <h3>会员资料</h3>
            <button type='button' id="editButton" class="btn btn-default">编辑</button>

            {{--头像--}}
            <div class="avaterArea">
                <img src="{{$avatar}}" />
                <div class="avatarText">
                    <div class="avatarTextHalf">
                        <span class="nickname">{{$nickname}}</span>
                        <span class="gender">{{$gender}}</span>
                    </div>
                    <div class="avatarTextHalf">
                        <span>手机号：</span>
                        <span style="margin-left: 6px;">{{$phone}}</span>
                    </div>
                </div>
            </div>

            <h3 style="margin-top: 12px;">详细信息</h3>

            {{--详细信息--}}
            <div class="info">
                @if($first_channelid)<div><span onclick="window.location.href='/open_detail?id={{$first_channelid}}&title={{$channel_name}}'" class="table-pointer">来源渠道：{{$channel_name}}</span></div>@endif
                <div>真实姓名：{{$name}}</div>
                <div>地　　址：{{$address}}</div>
                <div>公　　司：{{$company}}</div>
                <div>职　　位：{{$job}}</div>
                <div>行　　业：{{$industry}}</div>
            </div>


        </div>

        <table></table>

        {{--购买区--}}
        <div class="purchaseArea">
            <h3>开通记录</h3>

            {{--记录--}}
            @if(!empty($purchase))
            <div class="record">
                    <div class="eachRecord">
                        <div class="time">时间</div>
                            <div class="each_title ">产品</div>
                        <span>价格</span>
                        <span>类型</span>
                        <span>方式</span>
                    </div>
            </div>
            @endif
            <div class="purchaseList">
                <div class="record">
                    @if(!empty($purchase))
                        @foreach($purchase as $key => $value)
                            <div class="eachRecord">
                                <div class="time">{{$value[0]}}</div>
                                <div class="each_title " style="overflow: hidden; white-space: nowrap; text-overflow:ellipsis" title="{{$value[1]}}"> {{$value[1]}} </div>
                                <span>{{$value[4]}}</span>
                                <span>{{$value[2]}}</span>
                                <span>{{$value[3]}}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="footer">
        <div class="msgbox">
            <h3>系统回复</h3>
<div class="boxMessage">
    @if(!empty($message))
        <div class="adminMessage">
            @foreach($message as $key => $value)
                <p class="eachAdmin">
                    {{$value[2]}}&nbsp;&nbsp;{{$value[0]}}&nbsp;:&nbsp;{{$value[1]}}
                </p>
            @endforeach
        </div>
    @endif
</div>

        </div>
        <div class="msgbox" id="border_lt">
            <h3>用户评论</h3>
<div class="boxMessage">
    @if(!empty($comments))
        <div class="adminMessage">
            @foreach($comments as $key => $value)
                <p class="eachAdmin">
                    {{$value[5]}}&nbsp;&nbsp;
                    @if(empty($value[2]))
                        {{$value[4]}}
                    @else
                        {{$value[4].' || '.$value[3]}}
                    @endif
                    &nbsp;&nbsp;[{{$value[1]}}]
                    [{{$value[0]}}]
                </p>
            @endforeach
        </div>
    @endif
</div>

        </div>
        <div class="msgbox" id="border_lt">
            <h3>用户反馈</h3>
<div class="boxMessage">
    @if(!empty($feedbacks))
        <div class="adminMessage">
            @foreach($feedbacks as $key => $value)
                <p class="eachAdmin">
                    {{$value[1]}}&nbsp;&nbsp;{{$value[0]}}
                </p>
            @endforeach
        </div>
    @endif
</div>

        </div>
    </div>
@stop

@section('base_modal')
    {{--发消息--}}
    <div class="modal fade" id="SmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 640px;margin-top: 150px;">
            <div class="modal-content" style="height: 550px;width: 640px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">消息推送</span></div>
                    <div style="margin-top: 10px;">
                        @if(!empty($model)>0)
                            @foreach($model as $key=>$value)
                                <div class="model_type" id="model_{{$key+1}}" data-id="{{$value[0]}}" data-name="{{$value[1]}}" data-content="{{$value[2]}}">模板{{$key+1}}</div>
                            @endforeach
                            @for($i = 1 ; $i <= (5-count($model)) ; $i++)
                                <div class="model_type" id="model_{{$i+count($model)}}" data-id="" data-name="" data-content="">模板{{$i+count($model)}}</div>
                            @endfor
                        @else
                            @for($i = 1 ; $i <= 5 ; $i++)
                                <div class="model_type" id="model_{{$i}}" data-id="" data-name="" data-content="">模板{{$i}}</div>
                            @endfor
                        @endif
                        <div class="model_type" style="border: 0px;width: auto;height: auto" id="cancel_model" data-id="" data-name="" data-content="">×</div>
                    </div>
                </div>

                <div class="modal-body">
                    <input class="form-control" placeholder="在此输入发送人昵称，例如：小鹅通" id="sms_nickname"
                           style="margin-bottom: 20px; height: 36px; border-radius:1px;"/>
                    <textarea class="form-control" placeholder="在此输入消息内容" style="resize: none;
                            height: 111px; width: 100%; border-radius:1px;" id="sms_content"></textarea>

                    {{--私人消息新添加外链-start--}}
                    <div class="link-part-title">消息链接 (可不填)</div>
                    <input class="form-control" placeholder="链接名称，例如：戳我领取福利！" id="link_name"
                           style="margin-bottom: 20px; height: 36px; border-radius:1px;"/>

                    <div>
                        <div style="float: left; width: 100px;">
                            <select class="form-control"  id="link_type_selector" style="height: 36px; border-radius:1px;">
                                <option value="h5">外部链接</option>
                                <option value="audio">音频</option>
                                <option value="video">视频</option>
                                <option value="image_text">图文</option>
                                <option value="package">专栏</option>
                                <option value="no_jump">无跳转</option>
                            </select>
                        </div>
                        <div style="float: left;margin-left: 10px">
                            <select class="form-control"  id="skip_target_selector" style="height: 36px; border-radius:1px;">
                                @foreach($audioList as $key=>$value)
                                    <option value="{{ $value->id }}">{{ $value->title }}</option>
                                @endforeach
                            </select>
                            <input class="form-control hide" id="skip_target_input" style="width: 478px; height: 36px; border-radius:1px;" />
                        </div>
                    </div>

                    {{--检测网址的输入错误时，添加文字提醒--}}
                    <div class="http_error_tip hide">
                        您输入的网址不符合要求，输入网址请以 http:// 或 https:// 开头
                    </div>
                    {{--私人消息新添加外链-end--}}

                </div>

                <div class="modal-footer" style="margin: 45px 15px 0; padding: 30px 0px;">
                    <div class="edit_model hide" style="width: 100px; height: 36px; display: inline-block; margin-top: 0">
                        保存修改
                    </div>
                    <button type="button" class="btn btn-primary btn-blue" style="width: 100px; height: 36px;">
                        立即发送</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                            style="width: 100px; height: 36px; margin-left: 25px">取消</button>
                </div>
            </div>
        </div>
    </div>
@stop

{{--
@section('base_modal')
    --}}{{--发消息--}}{{--
    <div class="modal fade" id="SmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content" style="height: 460px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">消息推送</span></div>
                    <div style="margin-top: 10px;">
                        @if(!empty($model)>0)
                            @foreach($model as $key=>$value)
                                <div class="model_type" id="model_{{$key+1}}" data-id="{{$value[0]}}" data-name="{{$value[1]}}" data-content="{{$value[2]}}">模板{{$key+1}}</div>
                            @endforeach
                            @for($i = 1 ; $i <= (5-count($model)) ; $i++)
                                <div class="model_type" id="model_{{$i+count($model)}}" data-id="" data-name="" data-content="">模板{{$i+count($model)}}</div>
                            @endfor
                        @else
                            @for($i = 1 ; $i <= 5 ; $i++)
                                <div class="model_type" id="model_{{$i}}" data-id="" data-name="" data-content="">模板{{$i}}</div>
                            @endfor
                        @endif
                        <div class="model_type" style="border: 0px;width: auto;height: auto" id="cancel_model" data-id="" data-name="" data-content="">×</div>
                    </div>
                </div>

                <div class="modal-body" style="height: 200px">
                    <input class="form-control" placeholder="请输入发送人昵称" id="sms_nickname"
                           style="margin-bottom: 15px;"/>
                    <textarea class="form-control" placeholder="请输入您想发送的内容"
                              style="resize: none;height: 200px;border-radius:10px;width: 100%;" id="sms_content"></textarea>
                </div>

                <div class="modal-footer" style="margin-top: 75px;line-height: 50px">
                    --}}{{--<div style="float: left">--}}{{--
                        --}}{{--@if(!empty($model)>0)--}}{{--
                            --}}{{--@foreach($model as $key=>$value)--}}{{--
                                --}}{{--<div class="model_type" id="model_{{$key+1}}" data-id="{{$value[0]}}" data-name="{{$value[1]}}" data-content="{{$value[2]}}">模板{{$key+1}}</div>--}}{{--
                            --}}{{--@endforeach--}}{{--
                            --}}{{--@for($i = 1 ; $i <= (5-count($model)) ; $i++)--}}{{--
                                --}}{{--<div class="model_type" id="model_{{$i+count($model)}}" data-id="" data-name="" data-content="">模板{{$i+count($model)}}</div>--}}{{--
                            --}}{{--@endfor--}}{{--
                        --}}{{--@else--}}{{--
                            --}}{{--@for($i = 1 ; $i <= 5 ; $i++)--}}{{--
                                --}}{{--<div class="model_type" id="model_{{$i}}" data-id="" data-name="" data-content="">模板{{$i}}</div>--}}{{--
                            --}}{{--@endfor--}}{{--
                        --}}{{--@endif--}}{{--
                            --}}{{--<div class="model_type" style="border: 0px;width: auto;height: auto" id="cancel_model" data-id="" data-name="" data-content="">×</div>--}}{{--
                    --}}{{--</div>--}}{{--

                    <div class="edit_model hide" style="display: inline-block">保存修改</div>
                    <button type="button" class="btn btn-primary btn-blue">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@stop--}}
