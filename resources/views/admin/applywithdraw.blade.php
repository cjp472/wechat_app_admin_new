<?php
$pageData = [];
$pageData['sideActive'] = 'money_admin';
$pageData['barTitle'] = '财务管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/applywithdraw.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/applywithdraw.js?{{env('timestamp')}}"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
@endsection


@section('base_title')
    {{--<span style="font-size: 18px">财务管理</span>--}}
@stop

@section('base_mainContent')
    <div class="payTitle">
        <ul>
          <span class="sub_title"><a href="/withdraw_page" >提现记录</a></span>
            <span class="tubiao"> > </span>
            <span class="sub_title_2"><a href="/apply_withdraw_page">申请提现</a></span>
        </ul>
    </div>
    <hr>
    <div class="smscode_c">
        <div class="code_num">
            <span class="code_text">可提现余额(元)</span>
            <div class="code_desc">
                <span id="account_amount" class="amount_total">{{$account_amount_total}}</span>
                <a target="_blank" href="/help/explainDoc#hp0" class="shuoming">小鹅通不加收手续费说明</a>
            </div>
        </div>
        <div class="code_num" style="height: 95px">
            <span class="code_text">到账微信账号</span>
            <div class="code_desc" style="border: solid 1px #e1e6f0;height: 65px;width: 27%;">
                @if(count($bind_wx_account) > 0)
                    <img class="wx_img_bind" src="{{$bind_wx_account->wx_avatar?$bind_wx_account->wx_avatar:'../images/default.png'}}"/>
                    <div class="wx_nickname" id="wx_nickname" data-bind_acount_wx_id="{{$bind_wx_account->bind_account_wx_id}}">{{$bind_wx_account->wx_nickname}}</div>
                    {{--<a href="/bind_wx_account_page" class="chang_wx_account hide"> 更改</a>--}}
                @else
                    <a href="/bind_wx_account_page" class="bind_wx_account"> 绑定提现微信号</a>
                @endif
            </div>
        </div>
        <div class="code_num">
            <span class="code_text">提现金额</span>
            <div class="code_desc">
                <input type="text" class="form-control code_input inputDefault" placeholder="200.00" aria-label="..." id="cash_amount">元
                <span class="extra_cash" data-has-draw="{{$total_cash_money}}">最低提现限额为200元，今日已提现{{$total_cash_money}}元</span>
                <span class="has_withdraw extra_cash"></span>
            </div>
        </div>
        <div class="code_num" style="float: left;">
            <span class="code_text">提现说明</span>
            <div class="code_desc">
                <div class="extra_1">微信实名认证后每日能提现的上限为2万, 未认证每日提现的上限为2000元</div>
                <div class="extra_1">微信支付的结算周期为T+7, 提现申请后, 7天后款项会自动转至您的微信钱包;</div>
                <div class="extra_1 hide">咨询专线:0571-400 800 6600, 服务时间: 10:00-18:00</div>
            </div>
        </div>
    </div>

    <hr>
    <div class="bind_confirm">
        <div id="period_week" onclick="confirm_cash()" class="btn_confirm" >确认提现</div>
    </div>
@stop
