<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" href="../css/admin/smsDetailList.css?{{env('timestamp')}}"/>     {{--css--}}
@endsection

@section('page_js')
    <script src="../js/admin/smsDetailList.js?{{env('timestamp')}}"></script>               {{--js--}}
@endsection

@section('base_mainContent')
    {{--标题 - header --}}
    <div class="header">
        <span class="header_level home_page">账户一览 ></span>
        <span class="header_level home_page">结算记录 ></span>
        <span class="header_level">短信详情 </span>
    </div>

    <div class="content">
        <div class="content_title">
            <span class="transaction identifier">结算单号：{{$id}}</span>
            <span class="transaction time">费用产生日期：{{$charge_at}}</span>
            <span class="transaction type">结算类型：短信费</span>
            <span class="transaction fee_sum">费用总计：{{$fee_sum}}元</span>
        </div>
        <table class="table_sms_detail">
            <thead>
                <tr class="tr_head" height="40px">
                    <th class="th_sms_type">短信类型</th>
                    <th class="th_avatar_nickname">头像/昵称</th>
                    <th class="th_phone">手机号码</th>
                    <th class="th_fee">费用(元)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result_list as $key => $result)
                    <tr class="tr_body">
                        <td class="td_sms_type">验证码</td>
                        <td class="td_avatar_nickname">
                            <div class="avatar_nickname_wrapper">
                                <div class="avatar_nickname" data-app_id="{{$result->app_id}}" data-user_id="{{$result->user_id}}">
                                    <div class="avatar_icon_wrapper">
                                        <img class="avatar_icon" src="{{$result->wx_avatar_wx?$result->wx_avatar_wx:'../images/default.png'}}" alt="头像" title="头像">
                                    </div>
                                    <span class="nick_name">{{$result->wx_nickname}}</span>
                                </div>
                            </div>
                        </td>
                        <td class="td_phone">{{$result->phone}}</td>
                        <td class="td_fee">0.05</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="list-page">
            <?php echo $web_sms_total->appends(['charge_at' => $charge_at, 'id' => $id, 'fee_sum' => $fee_sum])->render(); ?>
        </div>
    </div>
@stop













