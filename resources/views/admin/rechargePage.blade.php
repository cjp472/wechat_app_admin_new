<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" xmlns="http://www.w3.org/1999/html" href="../css/admin/rechargePage.css?{{env('timestamp')}}"/>
@stop

@section('ahead_js')
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
    {{--公共支付js--}}
    <script type="text/javascript" src="../js/admin/utils/newWeiXinPay.js?{{env('timestamp')}}"></script>
@stop

@section('page_js')
    <script type="text/javascript" src="../js/admin/rechargePage.js?{{env('timestamp')}}"></script>
@stop


@section('base_mainContent')

    <div class="pageTopTitle">
        <a href="/accountview">账户一览</a>&nbsp;&gt;&nbsp;充值
    </div>

    <div class="mainContentPart">
        <div class="titleOne">当前流量账户余额：</div>

        <div class="titleTwo">
            <div class="remaining_number">{{$app_balance}}</div>
            <div class="remaining_unit">元</div>
        </div>

        <div class="paytype">
            <div class="paytype_title">付款方式</div>
            <div class="paytype_set">
                <div class="paytype_check" title="微信支付" >
                    <div class="wx_icon_wrapper">
                        <img class="wx_icon" src="/images/icon64_wx_logo.png">
                    </div>
                    <div class="wx_pay">微信支付</div>
                    <div class="usable_icon_wrapper">
                        <img class="usable_icon" src="/images/account_vip_version_pre.svg">
                    </div>
                </div>
            </div>
        </div>

        <div class="payin">
            <div class="paytype_title">充值金额</div>
            <div class="paymentInputArea">
                <input class="inputDefault" id="money" type="text" placeholder="请输入充值金额" value="100"/>
                <div class="mny">元</div>
                <div class="input_Tip" id="inputPriceTip">最低充值额度为100元</div>
            </div>
        </div>

        <div class="qrCodeScanArea" id="qrCodeScan">
            <div class="qrCodePicWrapper">
                <div class="qrCodePic"  id="qr_code">

                </div>
            </div>

            <div class="qrCodeScanTipWrapper" id="qrCodeScanTip">
                <div class="qrCodeScanTip" >
                    <div class="qrCodeScanTipPic">
                        <img src="../images/icon_scan_qr_code.png">
                    </div>
                    <div class="qrCodeScanTipText">微信扫码完成支付</div>
                </div>
            </div>
        </div>

    </div>

@stop

@section('base_modal')

    <div class="successWindow" style="display: none;">
        <div class="windowBg"></div>
        <div class="successWindowContent">
            <div class="paymentSuccessPic">
                <img src="../images/version_charge_success.png">
            </div>
            <div class="paymentSuccessTip">恭喜您，充值成功！</div>
            <div class="paymentSuccessReturnTip" id="paymentSuccessReturn"><span>3</span>秒后弹窗自动关闭...</div>
            <div class="returnAccountBtn" id="returnAccount">返回管理台</div>
            <div class="continueChargeBtn" id="continueCharge">继续充值</div>
        </div>
    </div>

    <div class="failureWindow" style="display: none;">
        <div class="windowBg"></div>
        <div class="failureWindowContent">
            <div class="paymentSuccessPic">
                <img src="../images/version_charge_fail.png">
            </div>
            <div class="paymentSuccessTip">支付遇到了错误，请重新支付</div>
            <div class="refreshCurrentWindowBtn" id="payAgain">重新支付</div>
        </div>
    </div>

    <div class="refreshWindow" style="display: none;">
        <div class="windowBg"></div>
        <div class="refreshWindowContent">
            <div class="paymentSuccessPic">
                <img src="../images/version_charge_fail.png">
            </div>
            <div class="paymentSuccessTip">支付超时，请刷新当前页面</div>
            <div class="refreshCurrentWindowBtn" id="refreshCurrentPage">刷新页面</div>
        </div>
    </div>

@stop


