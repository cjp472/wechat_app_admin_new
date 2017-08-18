<!DOCTYPE html>
<html>
<head>
    {{--页面字符集--}}
    <meta charset="UTF-8">
    {{--360标签，页面需默认用极速核--}}
    <meta name="renderer" content="webkit">
    {{--使用Chrome内核来做渲染--}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    {{--升级不安全请求，http会被升级到https--}}
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>
    @endif
    {{--网页标题--}}
    <title>申请开票</title>
    {{--标签icon--}}
    <link rel='icon' href='/logo-64.ico' type='image/x-ico'/>
    {{------------------------------------引用css------------------------------------------------}}
    {{--Bootstrap v3.3.5--}}
    <link type=text/css rel="stylesheet" href="/css/external/bootstrap.min.css?{{env('timestamp')}}">
    {{--弹窗--}}
    <link rel="stylesheet" href="/css/external/jquery-alert.css?{{env('timestamp')}}"/>
    {{--base.css--}}
    <link type=text/css rel="stylesheet" href="/css/admin/base.css?{{env('timestamp')}}">

    <link type=text/css rel="stylesheet" href="/css/admin/invoice/manage.css?{{env('timestamp')}}">

    {{--百度统计--}}
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?081e3681cee6a2749a63db50a17625e2";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>

</head>
<body>

<div id="app" class="content">
    <div class="top">
        <a class="btnMid xeBtnDefault pull-left" href="/invoice_info" >申请列表</a>
    </div>
    <div class="main">
        <div class="title">发票申请信息</div>
        <div class="topInfo">
            <p>Hi 亲爱的鹅粉：</p>
            <p>
                感谢您对小鹅通的支持！请仔细确认以下发票信息，
                <span class="red">一经开票，无法修改</span>。
                我们将于一个月内寄出发票，请耐心等待。
                其他疑问请咨询产品鹅，联系电话18124689845，
                <span class="red">最低500元起开票</span>，
                如果不够500元，可以攒一下哦。
            </p>
        </div>

        <div class="invoiceMain" id="formInfo">
            <div class="section">
                <div class="sectionTitle">发票类型 <span class="red">*</span></div>
                <div class="sectionInfo" id="radioBox">
                    <div>
                        <input class="with-gap" id="type1" checked name="type" value="1" type="radio">
                        <label for="type1">
                            普票（请填写抬头、发票金额和邮寄地址）
                        </label>
                    </div>
                    <div>
                        <input class="with-gap" id="type2" name="type" value="2" type="radio">
                        <label for="type2">
                            专票（请填写除备注外全部信息）
                        </label>
                    </div>
                </div>
            </div>
            <div class="subtitle">基本信息</div>
            <div class="section">
                <div class="sectionTitle">发票抬头 <span class="red">*</span></div>
                <div class="sectionInfo">
                    <input class="inputDefault" name="invoice_title" data-text="发票抬头" type="text">
                </div>
            </div>

            <div class="section">
                <div class="sectionTitle">开票金额 <span class="red">*</span></div>
                <div class="sectionInfo">
                    <div class="radioBtn pull-left">
                        <input class="checkbox" id="ch1" name="moneytype" value="1" type="checkbox">
                        <label for="ch1" class="checkbox-desc">
                            专业版4800
                        </label>
                    </div>
                    <div class="radioBtn pull-left">
                        <input class="checkbox" id="ch2" name="moneytype" value="2" type="checkbox">
                        <label for="ch2" class="checkbox-desc">
                            成长版100
                        </label>
                    </div>
                    <div class="radioBtn pull-left">
                        <input class="checkbox" id="ch3" name="moneytype" value="3" type="checkbox">
                        <label for="ch3" class="checkbox-desc checkbox-include-input">
                            充值流量费用
                            <input id="flowMoney" type="text" name="amount" class="flow-cost">
                        </label>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="sectionTitle">联系人 <span class="red">*</span></div>
                <div class="sectionInfo">
                    <input class="inputDefault" name="contact" data-text="联系人" type="text">
                </div>
            </div>
            <div class="section">
                <div class="sectionTitle">电话 <span class="red">*</span></div>
                <div class="sectionInfo">
                    <input class="inputDefault" name="phone" data-text="电话" type="text">
                </div>
            </div>
            <div class="section">
                <div class="sectionTitle">地址 <span class="red">*</span></div>
                <div class="sectionInfo">
                    <input class="inputDefault" name="address" data-text="地址" type="text">
                </div>
            </div>

            <div class="section">
                <div class="sectionTitle">纳税识别号 <span class="red">*</span></div>
                <div class="sectionInfo">
                    <input class="inputDefault" name="tax_file_number" data-text="纳税识别号" type="text">
                </div>
            </div>

            <div id="specialTicket" style="display: none;">
                <div class="subtitle">专票必填</div>
                <div class="section">
                    <div class="sectionTitle">注册场所地址 <span class="red">*</span></div>
                    <div class="sectionInfo">
                        <input class="inputDefault" name="value_added_tax_address" data-value="add" data-text="注册场所地址" type="text">
                    </div>
                </div>
                <div class="section">
                    <div class="sectionTitle">公司注册电话 <span class="red">*</span></div>
                    <div class="sectionInfo">
                        <input class="inputDefault" name="value_added_tax_phone" data-value="add"  data-text="公司注册电话"  type="text">
                    </div>
                </div>
                <div class="section">
                    <div class="sectionTitle">开户行 <span class="red">*</span></div>
                    <div class="sectionInfo">
                        <input class="inputDefault" name="value_added_tax_bank" data-value="add" data-text="开户行" type="text">
                    </div>
                </div>

                <div class="section">
                    <div class="sectionTitle">银行账户 <span class="red">*</span></div>
                    <div class="sectionInfo">
                        <input class="inputDefault" name="value_added_tax_account" data-value="add" data-text="银行账户" type="text">
                    </div>
                </div>

            </div>

            <div class="subtitle">其他信息</div>
            <div class="section">
                <div class="sectionTitle">备注</div>
                <div class="sectionInfo">
                    <input class="inputDefault" name="remark"  data-text="" type="text">
                </div>
            </div>

            <div class="bottomInfo">
                如有疑问，请咨询产品鹅，微信exiaoemei1994.或拨打18124689845，解释权归深圳小鹅网络技术有限公司。
            </div>
            <div class="line"></div>

            <button id="makeInvoice" class="btnMid btnBlue">申请开票</button>
        </div>

    </div>
</div>

{{--Loading动画--}}
<div id="base_loading">
    <!-- <img style="width: 150px;height: 150px" id="login_progressImage" src="/images/Loading2.gif"/> -->
    <div class="loadingContent">
        <svg viewBox="25 25 50 50" class="circular">
            <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
        </svg>
        <p class="loadingText">加载中</p>
    </div>
</div>

{{--顶部小框提示--}}
<div id="TopPrompt" class="topPrompt" style="display: none;">
    <div class="topPromptContent"></div>
</div>
{{--提前加载alert里的图片，避免首次弹出闪动--}}
<div class="hide">
    <img src="/images/alert/blue_info_prompt.svg"/>
    <img src="/images/alert/green_info_prompt.svg"/>
    <img src="/images/alert/red_info_prompt.svg"/>
</div>


{{--------------------------------------- 引用js ----------------------------------------------}}
{{--jquery1.12.4--}}
<script type="text/javascript" src="/js/external/jquery1.12.4.min.js"></script>
{{--Bootstrap v3.3.5--}}
<script type="text/javascript" src="/js/external/bootstrap.min.js"></script>
{{--弹窗--}}
<script type="text/javascript" src="/js/external/jquery-alert.js?{{env('timestamp')}}"></script>

<script type="text/javascript" src="/js/admin/invoice/manage.js?{{env('timestamp')}}"></script>


</body>

</html>
