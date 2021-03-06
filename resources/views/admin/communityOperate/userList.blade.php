<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/communityOperate/communityDetail.css?{{env('timestamp')}}">

@stop

@section('page_js')
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/communityOperate/userList.js?{{env('timestamp')}}"></script>

@stop

@section('base_mainContent')
    <input id="admin_data" type="hidden" data-community_id="{{$communityInfo->id}}">
    <div class="pageTopTitle">
        <a>社群运营</a>
        &gt;
        <a href="/smallCommunity/communityList">小社群</a>
        &gt;
        社群详情
    </div>
    <div class="content">
        <div class="communityName">{{$communityInfo->title}}</div>
        <ul class="baseManageTab">
            <li>
                <a href="/smallCommunity/dynamicList?community_id={{$communityInfo->id}}">动态列表</a>
            </li>
            <li class="baseActiveTab">
                <a href="javascript:void(0)">成员列表</a>
            </li>
            {{--is_collection  1 个人模式 0企业模式--}}
            {{--@if(session("is_collection") == 0)--}}
            {{--<li>--}}
                {{--<a href="/smallCommunity/setting/{{$communityInfo->id}}">功能配置</a>--}}
            {{--</li>--}}
            {{--@endif--}}
        </ul>
        <div class="top_area">

            <div class="right_operate">
                <select class="selector" id="userTypeSelector">
                    <option value="0" @if($user_type == 0) selected="selected" @endif >全部用户</option>
                    <option value="1" @if($user_type == 1) selected="selected" @endif >黑名单</option>
                </select>
                <input class="inputSearchAll inputDefault" id="userNameInput" placeholder="请输入名称/手机号码" value="{{$search_content}}">
                <div class="searchAllBtn btnSmall xeBtnDefault" id="searchUser">搜索</div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="td_left">头像/昵称</th>
                    <th>身份</th>
                    <th>加入时间</th>
                    <th>手机号码</th>
                    <th>动态数</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userList as $key => $value)
                    <tr class="userListItem" data-user_id="{{$value->user_id}}">
                        <td class="td_left">
                            <img class="userImg" src={{$value->wx_avatar ? $value->wx_avatar : "../images/icon64_wx_logo.png"}} alt="头像">
                            <p class="username" title="{{$value->nick_name}}">{{$value->nick_name}}</p>
                        </td>
                        @if($value->type)
                            <td>群主</td>
                        @else
                            <td>成员</td>
                        @endif
                        <td>{{$value->created_at ? $value->created_at : "--"}}</td>
                        <td>{{$value->phone ? $value->phone : "--"}}</td>
                        <td>{{$value->dynamic_count ? $value->dynamic_count : "--"}}</td>
                        <td>
                            <div class="toolBox">
                                <ul>
                                    @if($value->type == 0)
                                        @if($value->state == 0)
                                            <li class="moveInBlackList">加入黑名单</li>
                                        @elseif($value->state == 2)
                                            <li class="moveOutBlackList">移出黑名单</li>
                                        @endif
                                    @else
                                        --
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(count($userList) == 0)
            <div class="contentNoneTip">暂无成员，赶紧分享链接邀请好友加入吧！</div>
        @endif

        <div class="list-page">
            @if(empty($search_content))
                <?php echo $userList->appends(['community_id' => $communityInfo->id, 'user_type' => $user_type])->render(); ?>
            @else
                <?php echo $userList->appends(['community_id' => $communityInfo->id, 'user_type' => $user_type, 'search_content' => $search_content])->render(); ?>
            @endif
        </div>

    </div>
@stop

@section('base_modal')

@stop