<?php
$pageData = [];
$pageData['sideActive'] = 'memberList';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)

@section("page_css")
    <link type=text/css rel="stylesheet" href="../css/admin/resManage/memberList.css?{{env('timestamp')}}" />
@stop

@section("page_js")
    <script type="text/javascript" src="../js/admin/resManage/memberList.js?{{env('timestamp')}}"></script>
@stop

@section("base_mainContent")
    {{--公共tab--}}
    {{--@include('admin.resManage.baseTab', ["tabTitle" => "memberList"])--}}
    <div class="pageTopTitle">
        知识商品 &gt; 会员
    </div>

    <div class="content">
        <div class="createMemberBtn btnMid btnBlue">新建会员</div>

        <div class="right_operate">
            {{--<select class="selector_member_type" id="selector_distribute">--}}
                {{--<option value="-1">全部</option>--}}
                {{--<option value="0">自有</option>--}}
                {{--<option value="1">推广</option>--}}
            {{--</select>--}}
            <select class="selector_member_type" id="selector">
                <option value="-1">全部</option>
                <option value="0">已上架</option>
                <option value="1">已下架</option>
            </select>
            <input class="inputSearchAll inputDefault" placeholder="输入名称">
            <div class="searchAllBtn btnSmall xeBtnDefault">搜索</div>
        </div>

        <div class="member_list_content">
            @foreach($memberListInfo as $key => $value)
            <div class="member_list_item" data-member_id="{{$value->id}}">
                <div class="left_item_part">
                    <a href="/member_detail_page?id={{$value->id}}">
                        <img class="member_cover_img"
                        @if(!empty($value->img_url_compressed))
                             src="{{$value->img_url_compressed}}"
                        @else
                             src="{{$value->img_url}}"
                        @endif
                    >
                    </a>
                    <div class="member_list_item_desc">
                        <a href="/member_detail_page?id={{$value->id}}">
                            <div class="item_title" title="{{$value->name}}">{{$value->name}}
                                <img class="member_icon_img" src="/images/admin/resManage/icon_member_2.png">
                                @if($value->is_distribute == 1) <span class="distribute_target">推广</span> @endif
                            </div>
                        </a>
                        <div class="member_num" title="{{$value->summary}}">{{$value->summary}}</div>
                        <div class="member_price">
                            @if($value->period == 2592000)
                                ￥{{$value->price / 100.00}}元/月
                            @elseif($value->period == 7776000)
                                ￥{{$value->price / 100.00}}元/季度
                            @elseif($value->period == 15811200)
                                ￥{{$value->price / 100.00}}元/半年
                            @elseif($value->period == 31622400)
                                ￥{{$value->price / 100.00}}元/年
                            @else
                                ￥{{$value->price / 100.00}}元/年
                            @endif
                        </div>
                    </div>
                </div>
                <div class="right_item_part">
                    <div class="toolBox">
                        <ul>
                            {{--<li class="operate" data-type="edit">编辑</li>--}}
                            {{--<li class="divide_line">&nbsp;&nbsp;|&nbsp;&nbsp;</li>--}}

                            <li class="operate" data-type="to_up">上移</li>
                            <li class="divide_line">&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                            <li class="operate" data-type="to_down">下移</li>
                            <li class="divide_line">&nbsp;&nbsp;|&nbsp;&nbsp;</li>

                        {{--@if($value->state == 1)--}}
                            {{--<li class="operate" data-type="show_member">上架</li>--}}
                        {{--@else--}}
                            {{--<li class="operate" data-type="hide_member">下架</li>--}}
                        {{--@endif--}}
                        {{--<li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>--}}

                        <li data-type="edit"><a href="/member_detail_page?id={{$value->id}}">详情</a></li>
                        </ul>
                    </div>

                    @if($value->state == 1)
                    <div class="show_state">已下架</div>
                    @endif
                </div>
            </div>
            @endforeach

            @if(count($memberListInfo) == 0)
                <div class="contentNoneTip">没有相应的数据</div>
            @endif

        </div>

        <div class="list-page">
            @if(empty($search_content))
                <?php echo $memberListInfo->appends(['state' => $state])->render(); ?>
            @else
                <?php echo $memberListInfo->appends(['state' => $state, 'search_content' => $search_content])->render(); ?>
            @endif
        </div>


    </div>


@stop
