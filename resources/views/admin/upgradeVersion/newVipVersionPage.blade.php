<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    <link rel='icon' href='logo-64.ico' type='image/x-ico' />

    <link type="text/css" rel="stylesheet" href="../css/admin/upgradeVersion/newVipVersionPage.css?{{env('timestamp')}}">

    <script src="../js/external/jquery-1.11.3.js"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    {{--公共支付js--}}
    <script type="text/javascript" src="../js/admin/utils/newWeiXinPay.js?{{env('timestamp')}}"></script>

</head>

<body>

    <input id="version_type_c"  type="hidden" data-version_type="{{session('version_type')}}">

    <div class="header">
        <div class="headerPic">
            <img src="images/xiaoeTC_logo.png" alt="小鹅通图标">
        </div>
        <div class="user">
            <div class="userLogo">
                <img src="@if(session('wx_share_image')){{ session('wx_share_image') }}@else{{ session('avatar') }}@endif" />
            </div>
            <div class="userName">
                @if(session('wx_app_name')){{ session('wx_app_name') }}@else{{ session('nick_name') }}@endif
            </div>
        </div>
        <div class="returnBtn" onclick="$weiXinPay.closeCurrentWindow()">返回管理台</div>
    </div>

    <div class="mainContentPart" id="mainContent" style="display: block;">
        <div class="contentPartLeft">
            <div class="contentPic">
                <img src="images/pic_price_zunxiang.svg">
            </div>
            <div class="contentIntro">升级为小鹅通专业版用户，您将获得首页名称自定义、视频+语音直播、新功能内测、日签等众多专属功能</div>
        </div>
        <div class="contentPartRight">
            <div class="contentWord_1">小鹅通专业版</div>
            <div class="contentWord_2" onclick="window.location.href='/upgrade_account'">查看所有类型</div>
            <div class="contentChargePrice">¥4800.00
                <div class="contentChargeSign">
                    <img src="images/payment-tick.png">
                </div>
            </div>
            <div class="contentWord_3">您将通过微信支付¥4800.00，有效期一年，到期时请及时充值续订。(支付后不可退款)</div>

            <div class="contentWord_4">扫码支付即代表您已阅读并同意
                <a class="contentWord_5" href="/charge_protocol_page" target="_blank">《小鹅通服务协议》</a>
            </div>

            <div class="qrCodePic"  id="qr_code">

            </div>

            <div class="qrCodeScanTip">
                <div class="qrCodeScanTipPic">
                    <img src="../images/icon_scan_qr_code.png">
                </div>
                <div class="contentWord_6">微信扫码完成支付</div>
            </div>

        </div>
    </div>

    <div class="successWindow" style="display: none;">
        <div class="paymentSuccessPic">
            <img src="../images/version_charge_success.png">
        </div>
        <div class="paymentSuccessTip">恭喜您，版本升级成功！</div>
        <div class="paymentSuccessReturnTip" id="paymentSuccessReturn"><span>3</span>秒后跳回到管理台...</div>
        <div class="closeCurrentWindowBtn" id="closeCurrentWindow">立即返回</div>
    </div>

    <div class="failureWindow" style="display: none;">
        <div class="paymentSuccessPic">
            <img src="../images/version_charge_fail.png">
        </div>
        <div class="paymentSuccessTip">支付遇到了错误，请重新支付</div>
        <div class="refreshCurrentWindowBtn" id="payAgain">重新支付</div>
    </div>

    <div class="refreshWindow" style="display: none;">
        <div class="paymentSuccessPic">
            <img src="../images/version_charge_fail.png">
        </div>
        <div class="paymentSuccessTip">支付超时，请刷新当前页面</div>
        <div class="refreshCurrentWindowBtn" id="refreshCurrentPage">刷新页面</div>
    </div>

    {{--业务js--}}
    <script type="text/javascript" src="../js/admin/upgradeVersion/newVipVersionPage.js?{{env('timestamp')}}"></script>

</body>

</html>
