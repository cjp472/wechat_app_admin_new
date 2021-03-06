<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="/css/external/jquery-alert.css?{{env('timestamp')}}" />
    {{--<link type="text/css" rel="stylesheet" href="/css/admin/base.css?{{env('timestamp')}}">--}}
    <link type="text/css" rel="stylesheet" href="/css/admin/communityOperate/communityDetail.css?{{env('timestamp')}}">

@stop

@section('page_js')
    <script type="text/javascript" src="/js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="/js/admin/communityOperate/functionSet.js?{{env('timestamp')}}"></script>

@stop

@section('base_mainContent')
    <input id="admin_data" type="hidden" data-id="{{$info->id}}">
    <div class="pageTopTitle">
        <a>社群运营</a>
        &gt;
        <a href="/smallCommunity/communityList">小社群</a>
        &gt;
        社群详情
    </div>
    <div class="content">
        <div class="communityName">{{$info->title}}</div>
        <ul class="baseManageTab">
            <li>
                <a href="/smallCommunity/dynamicList?community_id={{$info->id}}">动态列表</a>
            </li>
            <li>
                <a href="/smallCommunity/userList?community_id={{$info->id}}">成员列表</a>
            </li>
            <li class="baseActiveTab">
                <a href="javascript:void(0)">功能配置</a>
            </li>
        </ul>
        {{--{{dump($info)}}--}}
        <input id="industry" type="text" class="hide" data-hasIndustry="{{$info->is_industry}}">
        <div class="top_area">
            <div class="contentBox">
                <p class="pageTitle">社群通知</p>
                <div>
                    <div class="subTitle">群主动态提醒</div>
                    <div class="selectBox">
                        <p>
                            <input type="radio" class="with-gap" id="dynamic" name="dynamic"
                                   @if($info->is_feeds_push) checked @endif
                                   value="1">
                            <label for="dynamic">开启通知</label>
                            <span class="subTip">(群主发送新动态，群成员将收到服务号提醒)</span>
                            <a id="remindCheck" target="_blank" href="/helpCenter/problem?document_id=d_5954e90310557_MHBzvybc">查看 【群主动态提醒说明】</a>
                        </p>
                        <p>
                            <input type="radio" class="with-gap" id="dynamicClose" name="dynamic"
                                   @if($info->is_feeds_push==0) checked @endif value="0">
                            <label for="dynamicClose">关闭</label>
                        </p>
                    </div>
                </div>

                {{--<div>--}}
                    {{--<div class="subTitle">点赞和评论提醒</div>--}}
                    {{--<div class="selectBox">--}}
                        {{--<p>--}}
                            {{--<input type="radio" class="with-gap" id="remind" name="remind"--}}
                                   {{--@if($info->is_comment_push) checked @endif value="1">--}}
                            {{--<label for="remind">开通通知</label>--}}
                            {{--<span class="subTip">(群成员发表的动态有收到新的点赞或评论将收到服务提醒，每天一次，每晚20:00推送)</span>--}}
                        {{--</p>--}}
                        {{--<p>--}}
                            {{--<input type="radio" class="with-gap" id="remindClose" name="remind"--}}
                                   {{--@if($info->is_comment_push==0) checked @endif value="0">--}}
                            {{--<label for="remindClose">关闭</label>--}}
                        {{--</p>--}}
                    {{--</div>--}}
                {{--</div>--}}



            </div>
        </div>

    </div>
@stop

@section('base_modal')

@stop