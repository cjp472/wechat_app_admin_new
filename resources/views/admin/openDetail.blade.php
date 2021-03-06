<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/payAdmin.css?{{env('timestamp')}}">
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/selectTime.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    {{--时间选择器--}}
    <script type="text/javascript" src="../js/external/dateRange.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/openDetail.js?{{env('timestamp')}}"></script>
@endsection


@section('base_title')
    <span style="font-size: 18px">页面统计详情</span>
@stop

@section('base_mainContent')

    <div class="packageDetailHeader">
        <a href="/channel_admin">页面统计</a> &gt; 页面统计详情
    </div>

    <div class="searchArea">
        <form action="/open_detail" method="GET">
            <div style="float: right;margin-right: 20px;">
                <button class="btn btn-default" type="submit" style="margin-bottom: 3px" id="pay_search_btn">筛选</button>
            </div>
            <div id="SelectTime" class="pull-right time_group">
                <div id="dropdown-toggle" class="time_input dropdown-toggle" data-toggle="dropdown" >
                    <span id="SelectData">全部订单</span>
                    <span class="caret "></span>
                </div>
                <div id="SelectRange" class="time_option dropdown-menu">
                    <ul>
                        <li data-type='all'>全部订单</li>
                        <li data-type='nowMonth'>当月订单</li>
                    </ul>
                    <p id="optional" class="optional">自选时间</p>
                </div>
            </div>
            <input type="hidden" id="startTime" name="start_time" />
            <input type="hidden" id="endTime" name="end_time" />
            <input type="hidden" name="id" value="{{$id}}" />
            <input type="hidden" name="title" value="{{$title}}" />

        </form>
    </div>

    <div >
        <table class="table table-hover" style="margin-top: 10px;border: 1px solid #ddd;border-left: none; border-right: none">
            <thead>
            <tr>
                <th class="th_left">头像</th>
                <th>昵称</th>
                <th>订单类型</th>
                <th>订单内容</th>
                <th>订单总额</th>
                <th>订单时间</th>

            </tr>
            </thead>
            <tbody>
            @foreach($results as $key=>$value)
                <tr>
                    {{--头像/昵称--}}
                    <td>
                        <img src="{{$user_info[$key]->wx_avatar?$user_info[$key]->wx_avatar:'../images/default.png'}}"
                            style="cursor: pointer"
                            onclick="jumpDetail('{{$value->user_id}}')"/>
                    </td>
                    <td>
                        <span>{{$user_info[$key]->wx_nickname}}</span>
                    </td>

                    {{--内容类型--}}
                    @if($value->payment_type==3)
                        <td>专栏</td>
                    @elseif($value->payment_type == 4)
                        <td>团购</td>
                    @elseif($value->payment_type == 5)
                        <td>单笔购买赠送</td>
                    @elseif($value->payment_type == 6)
                        <td>产品包购买赠送</td>
                    @else
                        @if($value->resource_type==1)
                            <td>图文</td>
                        @elseif($value->resource_type==2)
                            <td>音频</td>
                        @elseif($value->resource_type==3)
                            <td>视频</td>
                        @elseif($value->resource_type==4)
                            <td>直播</td>
                        @endif
                    @endif
                    {{--消费内容--}}
                    <td>{{$value->purchase_name}}</td>

                    {{--消费总额--}}
                    <td>￥{{$value->price/100}}</td>
                    {{--订单时间--}}
                    <td>{{$value->created_at}}</td>

                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="list-page">
                <?php echo $results->appends(['id' => $id, 'title' => $title, 'start_time' => $start_time, 'end_time' => $end_time])->render(); ?>
        </div>
    </div>
@stop


