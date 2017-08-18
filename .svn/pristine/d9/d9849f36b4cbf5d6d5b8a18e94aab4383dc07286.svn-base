<!DOCTYPE html>
<html>
<head>
    {{--页面字符集--}}
    <meta charset="UTF-8">
    {{--360标签，页面需默认用极速核--}}
    <meta name="renderer" content="webkit">
    {{--使用Chrome内核来做渲染--}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="keywords" content="小鹅通，内容付费，知识服务，聚合型工具，专注于知识服务与社群运营的聚合型工具">
    <meta name="description" content="小鹅通，专注于知识服务与社群运营的聚合型工具">

    {{--标签icon--}}
    <link rel='icon' href='/logo-64.ico' type='image/x-ico'/>
    {{--升级不安全请求，http会被升级到https--}}
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>
    @endif
    {{--网页标题--}}
    <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    {{--base.css--}}
    <link type=text/css rel="stylesheet" href="/css/admin/base.css?{{env('timestamp')}}">
    <link rel="stylesheet" href="/css/admin/helpCenter/helpCenter.css?{{env('timestamp')}}">
    {{--分页面的css样式--}}
    @yield('page_css')
</head>

<body>
<div class="header midContent">
    <img class="xeLogo" src="/images/admin/helpCenter/xeLogo.png" alt="">
    <div class="splitLine"></div>
    <div class="headerTitle">帮助中心</div>
    <div class="contentSearch">
        <input type="text" class="inputDefault searchInput" placeholder="请问有什么可以帮到您">
        <div class="btnSmall btnBlue searchBtn">搜索</div>
    </div>

</div>
<div class="nav">
    <div class="navContent midContent">

        <a href="/helpCenter/index">
            <div class="navPart @if(isset($tabTitle) && $tabTitle === "index") navActive @endif">
                帮助首页
                @if(isset($tabTitle) && $tabTitle === "index")
                    <div class="navBlueBar"></div>
                @endif
            </div>
        </a>
        <a href="/helpCenter/freshMan">
            <div class="navPart @if(isset($tabTitle) && $tabTitle === "freshMan") navActive @endif">
                新手专区
                @if(isset($tabTitle) && $tabTitle === "freshMan")
                    <div class="navBlueBar"></div>
                @endif
            </div>

        </a>
        <a href="/helpCenter/problem?first_id&second_id&document_id=">
            <div class="navPart @if(isset($tabTitle) && $tabTitle === "problem") navActive @endif">
                问题汇总
                @if(isset($tabTitle) && $tabTitle === "problem")
                    <div class="navBlueBar"></div>
                @endif
            </div>
        </a>
    </div>
</div>
<div class="content midContent">
    <div class="helpContent">
        @yield('mainContent')
    </div>
</div>

<div class="footer">
    <div class="footerCol">
        <div class="footerColContent">
            <div class="footerColPart">
                <div class="footerColTitle">产品</div>
                <div class="footerColSection productFunctionNav" data-operate_type="function"><a
                            href="https://www.xiaoe-tech.com/#productFunction">功能</a></div>
                <div class="footerColSection versionSelectNav"><a
                            href="https://www.xiaoe-tech.com/homepage_charge">版本</a></div>
                <div class="footerColSection excellentCaseNav" data-operate_type="excellent_case"><a
                            href="https://www.xiaoe-tech.com/#excellentCase">案例</a></div>
            </div>
            <div class="footerColPart">
                <div class="footerColTitle">公司</div>
                <div class="footerColSection"><a
                            href="https://www.xiaoe-tech.com/aboutUs#aboutUsLink">加入我们</a></div>
                <div class="footerColSection"><a href="https://www.xiaoe-tech.com/aboutUs">关于我们</a></div>
                <div class="footerColSection"><a href="https://www.xiaoe-tech.com/aboutUs#report">媒体报道</a>
                </div>
                <div class="footerColSection"><a target="_blank"
                                                 href="https://admin.xiaoe-tech.com/charge_protocol_page">服务条款</a>
                </div>
            </div>

            <div class="footerColPart">
                <div class="footerColTitle">服务</div>
                <div class="footerColSection">
                    <a target="_blank" href="/helpCenter/index">帮助中心</a>
                </div>
            </div>
            <div class="footerColContact">
                <div class="footerColTitle">联系我们</div>
                <div class="footerColSection">18124689845 18126391294</div>
                <div class="footerColSection">support@xiaoe-tech.com</div>
                <div class="footerColSection">客服办公时间：9:00-19:00，周末不休</div>
                <div class="wxIcon">
                    <img src="/images/admin/helpCenter/icon-wechat.png" alt="">
                    <div class="wxQrcodeBox" style="display: none">
                        <div>在微信里<br/>使用小鹅通</div>
                        <div class="QrcodeBoxImg">
                            <img src="/images/admin/helpCenter/xe_qrcode.jpg" alt="">
                        </div>
                        <div class="boxTriangle"></div>
                        <div class="qrcodeBoxBar"></div>
                    </div>
                </div>
                <a href="http://weibo.com/xiaoetong?refer_flag=1001030102_&is_hot=1">
                    <div class="weiboIcon">
                        <img src="/images/admin/helpCenter/icon-weibo.png" alt="">
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="webInfo">Copyright © 2015-2017 深圳小鹅网络技术有限公司 ALL Rights Reserved. 粤ICP备15020529号-1</div>
</div>
{{-- 红色提示--}}
<div class="red_prompt" style="display: none">
    {{--<div class="red_prompt_word">3月1日- 3月15日 为第三方运营商扣费测试期，存储费、流量费和短信费由小鹅通在扣费当天进行补贴！有疑问请联系产品鹅（微信：exiaomei1994）</div>--}}
    <div class="red_prompt_word">提示：您的账户余额不足50元，为了不影响您的正常使用，请尽快充值</div>
    <div class="close_img_wrapper">
        <img class="red_prompt_close_img" src="/images/icon_pop_up_close.svg" alt="close"
             title="close">
    </div>
</div>
{{--顶部小框提示--}}
<div id="TopPrompt" class="topPrompt" style="display: none;">
    <div class="topPromptContent"></div>
</div>
{{--百度统计--}}
<script>
    var _hmt = _hmt || [];
    (function () {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?081e3681cee6a2749a63db50a17625e2";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
{{--jquery1.12.4--}}
<script type="text/javascript" src="/js/external/jquery1.12.4.min.js"></script>
{{--base.js--}}
{{--<script type="text/javascript" src="/js/admin/base.js?{{env('timestamp')}}"></script>--}}
{{--帮助中心--}}
<script type="text/javascript" src="/js/admin/helpCenter/helpCenter.js?{{env('timestamp')}}"></script>
@yield('page_js')
</body>

</html>