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


    {{--升级不安全请求，http会被升级到https--}}
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>
    @endif
    {{--网页标题--}}
    <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    {{--标签icon--}}
    <link rel='icon' href='/logo-64.ico' type='image/x-ico'/>

    {{------------------------------------引用css------------------------------------------------}}
    {{--Bootstrap v3.3.5--}}
    <link type=text/css rel="stylesheet" href="/css/external/bootstrap.min.css?{{env('timestamp')}}">
    {{--弹窗--}}
    <link rel="stylesheet" href="/css/external/jquery-alert.css?{{env('timestamp')}}"/>
    {{--base.css--}}
    <link type=text/css rel="stylesheet" href="/css/admin/base.css?{{env('timestamp')}}">
    {{--iconfont+图标库cdn--}}
    <link type=text/css rel="stylesheet" href="//at.alicdn.com/t/font_kcwrgmp315l5l8fr.css?">
    {{--分页面的css样式--}}
    @yield('page_css')


    {{--js报错监控--}}
    <script type="text/javascript" src="/js/external/js-error-catch.min.js"></script>{{--配置项js--}}
    <script type="text/javascript" src="/js/admin/config/config.js?{{env('timestamp')}}"></script>
    {{--支持css3及html5标签--}}
    <!--[if lt IE 9]>
    <script src="/js/external/respond.min.js"></script>
    <script src="/js/external/html5.min.js"></script>
    <![endif]-->

    {{--页面加载时间上报js--}}
    <script src="/js/admin/client.js"></script>
    {{--灰度标识--}}
    <script>
        is_huidu = "{{session("is_huidu")}}";
    </script>

    {{--顶部需要提前加载的js插件--}}
    @yield('ahead_js')

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
<body >
    <input type="hidden" id="forbidAccess" value="{{session('accessError','')}}">
    {{session(['accessError'=>''])}}
    <input type="hidden" id="xet_app_id" value="{{session('app_id')}}">
    <input type="hidden" id="xet_machineip" value="{{\App\Http\Controllers\Tools\Utils::getServerInsideAddress()}}">
    <input type="hidden" id="xet_userip" value="{{Illuminate\Support\Facades\Request::ip()}}">

<div class="base_slide">
    <div class="base_logo_div">
        <a href="/index">
            <img src="/images/logo-xiaoe-white.png"/>
        </a>
    </div>

    <div class="base_watch">
        @include('admin.baseSideMenu.sideMenuPart',
        [
            'id'=>'guide_admin',
            'iconSrc'=>'/images/admin/baseSlide/nav_guide.png',
            'iconSrcActive'=>'/images/admin/baseSlide/nav_guide_pre.png',
            'title'=>'店铺概况',
            'href'=>'/index'
        ])
    </div>
    <div class="base_divide_line"></div>

    @if( session("access")["101"] == 1)
    <div class="base_menu">
        @include('admin.baseSideMenu.sideMenuPart',
        [
            'id'=>'goods_manage',
            'iconSrc'=>'/images/admin/baseSlide/nav-contentShop.png',
            'title'=>'知识商品',
            'href'=>'/package_list_page',   //  ?prompt=1 可以添加参数判断是否由侧边栏进入
            'isSpread' => true  //有二级菜单且默认展开二级菜单
        ])
        <div class="secondIndexWrapper"> {{--这里定义高度 4 * 40 px--}}

            @include('admin.baseSideMenu.sideMenuPart', [
               'id'=>'resourceList',
               'title'=>'单品',
               'href'=>'/resource_list_page'
            ])
            @include('admin.baseSideMenu.sideMenuPart', [
                'id'=>'aliveList',
               'title'=>'直播',
               'href'=>'/resource_list_page?resource_type=4'
            ])
            @include('admin.baseSideMenu.sideMenuPart', [
               'id'=>'_packagePart',
               'title'=>'专栏',
               'href'=>'/package_list_page'
            ])
            @include('admin.baseSideMenu.sideMenuPart', [
               'id'=>'memberList',
               'title'=>'会员',
               'href'=>'/member_list_page'
            ])
        </div>
    </div>
    <div class="base_divide_line"></div>
    @endif

    @if( session("access")["103"] == 1)
    <div class="base_menu">
        @include('admin.baseSideMenu.sideMenuPart',
           [
               'id'=>'communityOperate',
               'iconSrc'=>'/images/admin/baseSlide/nav_community.png',
               'iconSrcActive'=>'/images/admin/baseSlide/nav-community-pre.png',
               'title'=>'社群运营',
               'href'=>'/community_operate'
           ])
    </div>
    <div class="base_divide_line"></div>
    @endif

    @if( session("access")["102"] == 1)
    <div class="base_menu">
        @include('admin.baseSideMenu.sideMenuPart',
           [
               'id'=>'marketing_admin',
               'iconSrc'=>'/images/admin/baseSlide/nav_sales.png',
               'iconSrcActive'=>'/images/admin/baseSlide/nav_sales_pre.png',
               'title'=>'营销中心',
               'href'=>'/marketing'
           ])
    </div>
    <div class="base_divide_line"></div>
    @endif

    @if( session("access")["104"] == 1)
        <div class="base_menu">
            @include('admin.baseSideMenu.sideMenuPart',
            [
                'id'=>'customerManage',
                'iconSrc'=>'/images/admin/baseSlide/nav_user.png',
                'iconSrcActive'=>'/images/admin/baseSlide/nav_user_pre.png',
                'title'=>'用户管理',
                'href'=>'/getRedirect/104'
            ])
        </div>

    <div class="base_divide_line"></div>
    @endif


    @if( session("access")["106"] == 1 || session("access")["128"] == 1)
        <div class="base_menu">
            @include('admin.baseSideMenu.sideMenuPart',
            [
                'id' => 'money_admin',
                'iconSrc' => '/images/admin/baseSlide/nav_finance.png',
                'iconSrcActive' => '/images/admin/baseSlide/nav_finance.png',
                'title' => '财务管理',
                'href'=>'/getRedirect/',
                'isSpread'=> true //有二级菜单
            ])
        </div>
        <div class="secondIndexWrapper">
            @if( session("access")["128"] == 1)
                    @include('admin.baseSideMenu.sideMenuPart',
                    [
                        'id' => 'order_list',
                        'title' => '全部订单',
                        'href'=>'/getRedirect/128'
                    ])
            @endif

            @if( session("access")["106"] == 1)
                    @include('admin.baseSideMenu.sideMenuPart',
                    [
                        'id' => 'my_money_admin',
                        'title' => '我的收入',
                        'href'=>'/getRedirect/106'
                    ])
            @endif
        </div>
    <div class="base_divide_line"></div>
    @endif

    @if( session("access")["105"] == 1 || session("access")["109"] == 1 || session("access")["107"] == 1)
    <div class="base_menu">
        @include('admin.baseSideMenu.sideMenuPart',
        [
            'id'=>'goods_manage',
            'iconSrc'=>'/images/admin/baseSlide/nav-contentShop.png',
            'title'=>'店铺管理',
            'href'=>'/package_list_page',   //  ?prompt=1 可以添加参数判断是否由侧边栏进入
            'isSpread' => true  //有二级菜单且默认展开二级菜单
        ])
        <div class="secondIndexWrapper"> {{--这里定义高度 3 * 42 px--}}
            @if( session("access")["105"] == 1)
                @include('admin.baseSideMenu.sideMenuPart',
                   [
                       'id'=>'dashboard_admin',
                       'title'=>'数据分析',
                       'href'=>'/getRedirect/105'
                   ])
            @endif

            @if( session("access")["109"] == 1)
            @include('admin.baseSideMenu.sideMenuPart',
                [
                    'id'=>'knowledgeShop',
                    'title'=>'店铺设置',
                    'href'=>'/interfacesetting'
                ])
            @endif

            @if(session('app_id') != env('TEST_APP_ID','') && session("access")["107"] == 1)
                @include('admin.baseSideMenu.sideMenuPart',
                   [
                       'id'=>'account_admin',
                       'title'=>'账户管理',
                       'href'=>'/getRedirect/107'
                   ])
            @endif
        </div>
    </div>
    <div class="base_divide_line"></div>
    @endif

    <div class="base_menu">
        <a class="base_menu_sub sideMenu cm_leftHelpCenter" id="helpCenter" href="/helpCenter/index" target="_blank">
            <img src="/images/admin/baseSlide/nav_help.png" class="base_icon" />
            <span style="color: #cdd3e6;">帮助中心</span>
        </a>
    </div>

</div>

<div class="base_right">
    {{--新的 base_topBar 样式--}}
    <div class="base_topBar">

        <div class="base_explain">
            <span id="toolbar_title">@if(isset($barTitle) && $barTitle){{$barTitle}}@endif</span>
        </div>

        <div class="base_topBar_right">
            <div class="helperClick">
                <img src="/images/admin/helper_center_icon.png" alt="帮助中心">
            </div>
            <div class="clientNotice">
                <div class="noticeUnreadNum" style="display: none"></div>
                <span><span class="noticeIcon"></span>通知中心</span>
            </div>
            <div class="NoticeBox" style="display: none">
                <input type="hidden" id="viewState"/>
                <input type="hidden" id="noticeId"/>
                <input type="hidden" id="noticeLink"/>
                <div class="NoticeBoxHeader">
                    <div class="NoticeBoxTitle">通知中心</div>
                    <div class="NoticeBoxClose"><img src="/images/icon_Pop-ups_close.svg" alt=""></div>
                </div>
                <div class="noticeBoxContent">
                    <div class="noticePart">
                        <div class="noticePartH">
                            <div class="notifierImg"><img src="/images/icon_notelogo.png" alt=""></div>
                            <div class="notifierName">系统通知</div>
                            <div class="noticeTime"></div>
                        </div>
                        <div class="noticePartC">
                            <div class="noticeTitle"></div>
                            <div class="noticeContent"></div>
                            <a target="_blank" class="noticeLink"></a>
                        </div>
                    </div>
                    <div class="noticeMore">查看全部</div>
                </div>
            </div>
            <div class="base_avatar">
                <img src="@if(session('wx_share_image')){{ session('wx_share_image') }}@else{{ session('avatar') }}@endif"/>

                <div class="join">@if(session('wxapp_join_statu')==1)已接入@else<a href="/help" title="我要接入">未接入</a>@endif
                </div>

                    <div class="hide_drop_down">
                     <span class="close close_hide">×</span>
                    @if(session('version_type') && session('version_type') != 3)
                    <div id="more_function_btn" class="more_function">
                        <span>享受更多功能<img src="/images/admin/baseSlide/icon_profile__more.png" ></span>
                    </div>
                    @endif
                    <div class="drop_content">
                        <div>
                            <img src="@if(session('wx_share_image')){{ session('wx_share_image') }}@else{{ session('avatar') }}@endif"/>
                        </div>
                        <div class="box_name"
                             title="@if(session('wx_app_name')){{ session('wx_app_name') }}@else{{ session('nick_name') }}@endif">
                            @if(session('wx_app_name')){{ session('wx_app_name') }}@else{{ session('nick_name') }}@endif的知识店铺</div>

                        @if(session('is_huidu') == 1)
                                <div class="user_version_num" data-version_type={{session("version_type")}}>
                                    @if(session("version_type") && session("version_type") == 3)专业版@elseif(session("version_type") && session("version_type") == 2)成长版@else基础版@endif账户
                                 @if(session('version_type') && session('version_type') != 3)
                                 | <a href="/upgrade_account">升级</a>
                                 @endif
                                </div>
                        @endif
                    </div>

                    <div class="avatar_bottom">
                        <a href="/accountview" >
                            <div class="avater_btn">
                                <img src="/images/admin/baseSlide/icon_profile_account.png" >
                                账户一览
                            </div>
                        </a>
                        <a  href="/helpCenter/index" target="_blank">
                            <div class="avater_btn">
                                <img src="/images/admin/baseSlide/icon_profile_help.png" >
                                帮助中心
                            </div>
                        </a>
                        <div id="account_exit" class="account_exit avater_btn">
                            <img src="/images/admin/baseSlide/icon_profile_signout.png" >
                            退出登录
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="base_content">
        @yield('base_contentTop')
        <div class="base_mainContent clearfix">
            {{--公共header模板 - 默认隐藏--}}
            <div class="content_header">
                <ul class="content_header_ul"></ul>
            </div>
            @yield('base_mainContent')

        </div>
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

</div>
{{-- 欠费通知 - 弹窗提示 --}}
<div class="window_prompt" style="display: none;">
    <div class="window_prompt_bg"></div>
    <div class="window_no_money">
        <div class="pop_up_close">
            <img src="/images/icon_Pop-ups_close.svg">
        </div>
        <div class="window_title">欠费通知</div>
        <div class="window_desc">您的账户已欠费，为不影响您正常使用，请尽快充值！</div>
        <a href="/get_recharge_page" class="btnBlue btnMid window_charge_btn">立即充值</a>
    </div>
</div>

{{-- 扫码补填UnionId提醒 --}}
{{--@if( \App\Http\Controllers\Tools\APIUtils::IsNeedPop() === 0 )--}}
{{--<div class="scanning_window_prompt">--}}
{{--<div class="window_prompt_bg"></div>--}}
{{--<div class="scanning_window">--}}
{{--<div class="popUpClose" id="closeScanningWindow">--}}
{{--<img src="/images/icon_Pop-ups_close.svg">--}}
{{--</div>--}}
{{--<div class="scanning_window_title">扫码补填UnionId提醒</div>--}}
{{--<div class="scanning_window_desc">扫码补填UnionId提醒</div>--}}
{{--<div class="windowScanningBtn">确定</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--@endif--}}

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

@yield('base_modal')


{{--------------------------------------- 引用js ----------------------------------------------}}
{{--jquery1.12.4--}}
<script type="text/javascript" src="/js/external/jquery1.12.4.min.js"></script>
{{--Bootstrap v3.3.5--}}
<script type="text/javascript" src="/js/external/bootstrap.min.js"></script>

{{--jquery cookie插件--}}
<script type="text/javascript" src="/js/external/jquery.cookie.js"></script>
{{--弹窗--}}
<script type="text/javascript" src="/js/external/jquery-alert.js?{{env('timestamp')}}"></script>
{{--base.js--}}
<script type="text/javascript" src="/js/admin/base.js?{{env('timestamp')}}"></script>


{{--分页面的js逻辑--}}
@yield('page_js')
<script type="text/javascript" src="/js/utils/nj-client.min.js?{{env('timestamp')}}"></script>
<script>
    NJBUS.init({
        nj_appid: 2,//异常上报系统的appid
        nj_url: "https://jsreport.xiaoe-tech.com:5188/accepter?",//异常上报系统的安装地址
        level: 4,//错误级别 1-debug 2-info 4-error
        ignore: [],// 忽略某个错误, 支持Regexp、Function
        b_uid: '{{\App\Http\Controllers\Tools\AppUtils::getAppID()}}',//（可不填）业务用户id，标识当前出错用户的业务id，便于查找错误
        ext_info: null,//（可不填）扩展参数
        rate: 1,// 抽样上报比例 (0-1]
        combo_report: -1,//合并上报的合并时间，单位（秒），默认不合并上报
        repeat: 5,// 重复上报次数(对于同一个错误超过多少次不上报)
        on_submit: null,// 需要上报时回调，设置了该项，且返回true则不会进行系统上报了，返回false会继续上报
        localLog: false,//是否开启本地离线日志
        localLogExpTime: 5,//本地日志，默认7天过期
    });

    {{--包裹所有的js，上报异常--}}
    NJBUS.wrapAll();
</script>
</body>

</html>
