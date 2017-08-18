<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
$tabData = ['tabTitle'=>'accountList'];
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" href="../css/admin/accountView.css?{{env('timestamp')}}"/>     {{--css--}}
    <link rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/accountView.js?{{env('timestamp')}}"></script>               {{--js--}}
@endsection

@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{-- 块：编号一 --}}
    <div class="content first">
        <div class="content_part1_wrapper">
            <div class="content_part1_border">
                <div class="content_part1_up">
                    <div class="content_part1_up_desc">
                        <div class="user_version_out">
                            <div class="user_version_wrapper">
                                <div class="user_version_icon_wrapper">
                                    @if(!empty(session("version_type")) && session("version_type") == 1)
                                        <img class="user_version_icon" src="../images/account_base_version_pre.svg">
                                    @else
                                        <img class="user_version_icon" src="../images/account_base_version.svg">
                                    @endif
                                </div>
                                <div class="user_version_name_base">基础版</div>
                            </div>
                            <div class="horizontal_line"></div>

                            <div class="user_version_wrapper">
                                <div class="user_version_icon_wrapper">
                                    @if(!empty(session("version_type")) && session("version_type") == 2)
                                        <img class="user_version_icon" src="../images/account_grow_up_pre.svg">
                                    @elseif(!empty(session("version_type")) && session("version_type") == 1)
                                        <a class="account_grow_up" href="/upgrade_account">
                                            <img class="user_version_icon" src="../images/account_grow_up.svg">
                                        </a>
                                    @else
                                        <img class="user_version_icon" src="../images/account_grow_up.svg">
                                    @endif
                                </div>
                                <div class="user_version_name_grow_up">成长版</div>
                            </div>
                            <div class="horizontal_line"></div>

                            <div class="user_version_wrapper">
                                <div class="user_version_icon_wrapper">
                                    @if(!empty(session("version_type")) && session("version_type") == 3)
                                        <img class="user_version_icon" src="../images/account_vip_version_pre.svg">
                                    @else
                                        <a class="account_vip_version" href="/upgrade_account">
                                            <img class="user_version_icon" src="../images/account_vip_version.svg">
                                        </a>
                                    @endif
                                </div>
                                <div class="user_version_name_vip">专业版</div>
                            </div>
                        </div>
                        <div class="user_version_desc_wrapper">
                            <div class="user_version_desc left">
                                @if(!empty(session("version_type")) && session("version_type")==1)
                                    当前用户版本
                                @else
                                    永久免费
                                @endif
                            </div>
                            <div class="user_version_desc center">
                                @if(!empty(session("version_type")) && session("version_type")==2)
                                    当前用户版本
                                @else
                                    成长版年费：实际营收总额*1%/年（不超过4500元/年）
                                    {{--收取订单流水1%（封顶4500元/年）--}}
                                @endif
                            </div>
                            <div class="user_version_desc right">
                                @if(!empty(session("version_type")) && session("version_type")==3)
                                    当前用户版本
                                @else
                                    4800元/年
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content_part1_middle">
                    @if(!empty(session("version_type")) && session("version_type") == 3)
                        <div class="word_desc_1">当前为小鹅通专业版，可享受以下高级功能</div>
                    @else
                        <div class="word_desc_1">升级小鹅通账户版本，享受以下更多高级功能</div>
                    @endif
                    <div class="word_wrapper_1">
                        <div class="word_wrapper_2">
                            @if(!empty(session("version_type")) && session("version_type") == 1)
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">小程序</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">活动管理</div>
                                </div>
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">请好友看</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">用户定向推送</div>
                                </div>
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">视频+语音直播</div>
                                    <div class="indicator_dot">·</div><div class="look_up_senior_function">查看所有高级功能></div>
                                </div>
                            @elseif(!empty(session("version_type")) && session("version_type") == 2)
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">显示/隐藏订阅数、用户评论</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">首页名称自定义</div>
                                </div>
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">视频+语音直播</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">日签</div>
                                </div>
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">新功能内测试用</div>
                                    <div class="indicator_dot">·</div><div class="look_up_senior_function">查看所有高级功能></div>
                                </div>
                            @else
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">推广员</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">活动管理</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">付费问答</div>
                                </div>
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">小社群</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">小程序</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">用户定向推送</div>
                                </div>
                                <div class="word_wrapper_3">
                                    <div class="indicator_dot">·</div><div class="word_desc_2">首页分类导航</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">首页名称自定义</div>
                                    <div class="indicator_dot">·</div><div class="word_desc_2">视频+语音直播</div>
                                </div>
                            @endif

                        </div>
                        @if(empty(session("version_type")) || session("version_type") != 3)
                            <div class="update_atOnce_btn">立即升级</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content_part1_down">
                <div class="word_desc_3">流量账户余额：</div>
                <div class="remaining_desc">
                    <div class="remaining_wrapper">
                        <div class="remaining_number">{{$app_balance}}</div>
                        <div class="remaining_unit">元</div>
                    </div>
                    <div class="charge_btn">充值</div>
                </div>
                {{--<div class="data_statistics">--}}
                    {{--<div class="data_statistics_consume">本月消费金额：--}}
                        {{--@if(empty($total_month_expense))--}}
                            {{--0元--}}
                        {{--@else--}}
                            {{--{{$total_month_expense}}元--}}
                        {{--@endif--}}
                    {{--</div>--}}
                    {{--<div class="data_statistics_storage">本月存储使用量：--}}
                        {{--@if(empty($total_month_space))--}}
                            {{--0G--}}
                        {{--@else--}}
                            {{--{{$total_month_space}}G--}}
                        {{--@endif--}}
                    {{--</div>--}}
                    {{--<div class="data_statistics_flow">本月累计播放流量：--}}
                        {{--@if(empty($total_month_flow))--}}
                            {{--0G--}}
                        {{--@else--}}
                            {{--{{$total_month_flow}}G--}}
                        {{--@endif</div>--}}
                    {{--<div class="data_statistics_message">本月短信发送量：--}}
                        {{--@if(empty($total_month_sms))--}}
                            {{--0条--}}
                        {{--@else--}}
                            {{--{{$total_month_sms}}条--}}
                        {{--@endif--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>

    {{-- 块：编号二 --}}
    <div class="content">
        <div class="contentTitle">结算记录</div>    <div class="record_num">({{$record_nums}}条)</div>   <div class="cost_explain">资费说明</div>
        <table class="account_record_table">
            <thead>
            <tr class="tr_head">
                <th class="th_account_record left">结算时间</th>
                <th class="th_type_content">结算类型</th>
                <th class="th_details">相关描述</th>
                <th class="th_account_amount">结算金额（元）</th>
                <th class="th_available_balance">账户余额（元）</th>
                <th class="th_operation">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($record_list as $key => $record)
                @if($record->charge_type != 101 || ($record->fee / 100.00) != 4800)
                    <tr class="tr_body">
                        <td class="td_account_record left">{{$record->charge_time}}</td>

                        {{--结算类型 + 相关描述 - start--}}
                        @if($record->charge_type == 101)
                            <td class="td_type_content">开通成长版</td>
                            <td class="td_details">预充100元流量费</td>

                        @elseif($record->charge_type == 102)
                            <td class="td_type_content">赠送</td>
                            <td class="td_details">开通基础版赠送50元流量包</td>

                        @elseif($record->charge_type == 104)
                            <td class="td_type_content">赠送</td>
                            <td class="td_details">升级成长版赠送50元流量包</td>

                        @elseif($record->charge_type == 105)
                            <td class="td_type_content">赠送</td>
                            <td class="td_details">升级专业版赠送{{$record->fee / 100.00}}元流量包</td>

                        @elseif($record->charge_type == 103)
                            <td class="td_type_content">充值</td>
                            <td class="td_details">账户充值</td>

                        @elseif($record->charge_type == 201)
                            <td class="td_type_content">开通扣费</td>
                            <td class="td_details">开通专业版扣费</td>

                        @elseif($record->charge_type == 202)
                            <td class="td_type_content">代运营商收取（流量费）</td>
                            <td class="td_details">
                                {{explode("-", $record->charge_at)[2]}}日流量结算-
                                @if(!empty($record->extra) && $record->extra > 1024)
                                    {{number_format($record->extra / 1024.00, 2)}}G
                                @elseif(!empty($record->extra) && $record->extra > 0)
                                    {{number_format($record->extra, 2)}}M
                                @else
                                    0M
                                @endif
                            </td>

                        @elseif($record->charge_type == 203)
                            <td class="td_type_content">代运营商收取（存储费）</td>
                            <td class="td_details">
                                {{explode('-', $record->charge_at)[2]}}日存储结算-
                                @if(!empty($record->extra) && $record->extra > 1024)
                                    {{number_format($record->extra / 1024.00, 2)}}G
                                @elseif(!empty($record->extra) && $record->extra > 0)
                                    {{number_format($record->extra, 2)}}M
                                @else
                                    0M
                                @endif
                            </td>

                        @elseif($record->charge_type == 204)
                            <td class="td_type_content">代运营商收取（短信费）</td>
                            <td class="td_details">
                                {{explode('-', $record->charge_at)[2]}}日短信结算-
                                @if(!empty($record->extra) && $record->extra != 0)
                                    {{$record->extra}}条
                                @else
                                    0条
                                @endif
                            </td>

                        @elseif($record->charge_type == 205)
                            <td class="td_type_content">订单流水提成(1%)</td>
                            <td class="td_details">
                                {{explode('-', $record->charge_at)[2]}}日订单流水-{{$record->extra / 100.00}}元</td>

                        @elseif($record->charge_type == 301)
                            <td class="td_type_content">小鹅通补贴</td>
                            <td class="td_details">存储费、流量费和短信费</td>

                        @else
                            <td class="td_type_content">--</td>
                            <td class="td_details">--</td>

                        @endif
                        {{--结算类型 + 相关描述 - end--}}

                        <td class="td_account_amount">
                            @if($record->charge_type == 101
                                    || $record->charge_type == 102
                                    || $record->charge_type == 103
                                    || $record->charge_type == 104
                                    || $record->charge_type == 105
                                    || $record->charge_type == 301)
                                {{$record->fee / 100.00}}
                            @elseif($record->charge_type == 201
                                        || $record->charge_type == 202
                                        || $record->charge_type == 203
                                        || $record->charge_type == 204
                                        || $record->charge_type == 205)
                                -{{abs($record->fee / 100.00)}}
                            @else
                                -{{abs($record->fee / 100.00)}}
                            @endif
                        </td>

                        <td class="td_available_balance">{{$record->account_balance / 100.00}}</td>

                        @if($record->charge_type == 202)
                            <td class="td_operation flow_detail"
                                onclick="getFlowDetails('{{$record->charge_at}}', '{{$record->id}}','{{abs($record->fee / 100.00)}}')">详情</td>
                        @elseif($record->charge_type == 203)
                            <td class="td_operation storage_detail"
                                onclick="getStorageDetails('{{$record->charge_at}}', '{{$record->id}}','{{abs($record->fee / 100.00)}}')">详情</td>
                        @elseif($record->charge_type == 204)
                            <td class="td_operation sms_detail"
                                onclick="getSmsDetails('{{$record->charge_at}}', '{{$record->id}}','{{abs($record->fee / 100.00)}}')">详情</td>
                        @else
                            <td class="td_operation_no">-</td>
                        @endif
                    </tr>
                @endif
            @endforeach

            </tbody>
        </table>
        <div class="list-page">
            <?php echo $record_list->render(); ?>
        </div>
    </div>

@stop

@section('base_modal')
    <div class="cost_explain_prompt">
        <div class="cost_explain_bg"></div>
        <div class="cost_explain_wrapper">
            <div class="cost_explain_title">第三方运营商资源费用代收说明</div>
            <div class="cost_explain_desc paragraph_one">
                1、存储费：0.03元/G/天
            </div>
            <div class="cost_explain_desc paragraph_two">
                2、流量费：用户访问文件大小*当日新增访问用户数*综合流量单价（0.90元/G）
            </div>
            <div class="cost_explain_desc paragraph_three">
                <div class="order_number">1）</div>    <div class="order_num_content">备注：单个用户访问多次时只计费一次</div>
            </div>
            <div class="cost_explain_desc paragraph_four">
                <div class="order_number">2）</div>
                <div class="order_num_content">
                    示例：原始上传音频10M，经小鹅通优化后为5M，当天有20个用户访问，其中有10个用户为第一次访问，那么当天的流量费计算：<br>5M*10*0.90元/G = 0.045元
                </div>
            </div>
            <div class="cost_explain_desc paragraph_five">
                3、短信费：0.05元/条（用于用户手机验证及通知下发等）
            </div>
            <div class="cost_explain_desc paragraph_six">
                4、按天计费，第二天完成结算
            </div>
            <div class="i_know_btn">我知道了</div>
        </div>
    </div>

@stop



