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
    <script type="text/javascript" src="../js/admin/communityOperate/dynamicList.js?{{env('timestamp')}}"></script>

@stop

@section('base_mainContent')
    <input id="admin_data" type="hidden" data-community_id="{{$community_id}}"
           data-count_notices="{{$communityInfo->count_notices}}" >
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
            <li class="baseActiveTab">
                <a href="javascript:void(0)">动态列表</a>
            </li>
            <li>
                <a href="/smallCommunity/userList?community_id={{$community_id}}">成员列表</a>
            </li>
            {{--is_collection  1 个人模式 0企业模式--}}
            {{--@if(session("is_collection") == 0)--}}
            {{--<li>--}}
                {{--<a href="/smallCommunity/setting/{{$communityInfo->id}}">功能配置</a>--}}
            {{--</li>--}}
            {{--@endif--}}
        </ul>
        <div class="top_area">

            <div class="btnMid btnBlue" id="createDynamic">新建动态</div>

            <div class="right_operate">
            	<select class="selector" id="dynamicTypeSelector">
                    <option value="0" @if($state == 0) selected="selected" @endif >全部动态</option>
                    <option value="1" @if($state == 1) selected="selected" @endif >精选动态</option>
                    <option value="2" @if($state == 2) selected="selected" @endif >群主动态</option>
                </select>
                <input class="inputSearchAll inputDefault" id="dynamicNameInput" placeholder="请输入动态内容" value="{{$search_content}}">
                <div class="searchAllBtn btnSmall xeBtnDefault" id="searchDynamic">搜索</div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="td_left textContent">动态内容</th>
                    <th>成员</th>
                    <th>创建时间</th>
                    <th>动态类型</th>
                    <th>点赞数</th>
                    <th>评论数</th>
                    <th class="dynamicToolTh">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dynamicList as $key => $value)
                    <?php
                        if (!empty($value->content)) {
                            $dynamicContentArray = json_decode($value->content, true);
                        }
                    ?>
                    <tr class="dynamicItem" data-community_id="{{$value->community_id}}" data-dynamic_id="{{$value->id}}" data-send_type="{{$value->send_type}}">
                        <td class="td_left">
                            <p class="dynamicTextContent" title="{{$value->title}}"
                               style="display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;"
                            >   @if(empty($value->title))
                                    @if(empty($dynamicContentArray['text']))
                                        (图片动态)
                                    @else
                                        {{$dynamicContentArray['text']}}
                                    @endif
                                @else {{$value->title}}
                                @endif</p>
                        </td>
                        <td>
                            {{($value->nick_name == "") ? "--" : $value->nick_name}}
                        </td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            @if($value->is_chosen)
                                <span class="tips quality">精选</span>
                            @endif
                            @if($value->user_type)
                                <span class="tips quality">群主</span>
                            @endif
                            @if($value->feeds_type == 2)
                                <span class="tips quality">作业</span>
                            @endif
                            @if(!$value->is_chosen && !$value->user_type && $value->feeds_type != 2)
                                <span>--</span>
                            @endif
                        </td>
                        <td>{{$value->zan_num}}</td>
                        <td>{{$value->comment_count}}</td>
                        {{-- 查看 / 更多-移入精选（移出精选） 设为群公告（取消群公告）删除 --}}
                        <td class="td_right">
                            <div class="toolBox">
                                <ul>
                                    <li class="operate"><a href="/smallCommunity/dynamicDetail?id={{$value->id}}">查看</a></li>
                                    <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                                    <li class="operate moreBtn">更多<span class="caret"></span>
                                        <ul class="moreTool downshow">
                                            @if($value->send_type == 1)
                                                <li data-type="edit_dynamic">编辑</li>
                                            @endif

                                            @if($value->is_chosen)
                                                <li data-type="move_out">移出精选</li>
                                            @else
                                                <li data-type="move_in">移入精选</li>
                                            @endif
                                            @if($value->user_type)
                                                @if($value->is_notice)
                                                    <li data-type="cancel_group_notice">取消群公告</li>
                                                @else
                                                    <li data-type="set_group_notice">设为群公告</li>
                                                @endif
                                            @endif
                                            <li data-type="delete_dynamic">删除</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(count($dynamicList) == 0)
            <div class="contentNoneTip">暂无动态，赶紧发表第一条动态吧！</div>
        @endif

        <div class="list-page">
            @if(empty($search_content))
                <?php echo $dynamicList->appends(['state' => $state, 'community_id' => $community_id])->render(); ?>
            @else
                <?php echo $dynamicList->appends(['state' => $state, 'search_content' => $search_content, 'community_id' => $community_id])->render(); ?>
            @endif
        </div>


    </div>
@stop

@section('base_modal')

@stop