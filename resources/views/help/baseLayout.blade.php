<!doctype html>
<html>
<head>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    @endif
    <title>@yield('base_title')——小鹅通，基于微信的内容付费工具</title>
    <link rel='icon' href='{{URL::to('/')}}/logo-64.ico' type=‘image/x-ico’ />
    <link type=text/css rel="stylesheet" href="../css/external/bootstrap.min.css">
    <script src="../js/external/jquery.js"></script>
    <script type="text/javascript" src="../js/external/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/external/jquery.cookie.js"></script>

    <link type=text/css rel="stylesheet" href="../css/help/base.css?{{env('timestamp')}}">
    <script type="text/javascript" src="../js/external/jquery.js"></script>
    <!--[if IE 8]>
    <script src="../js/external/jquery-1.11.3.js"></script>
    <![endif]-->
    <script type="text/javascript" src="../js/external/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/external/jquery.cookie.js"></script>
    <script type="text/javascript" src="../js/help/base.js?{{env('timestamp')}}"></script>

    {{--进度条js和样式--}}
    <script type="text/javascript" src="../js/external/jquery.toastmessage.js?{{env('timestamp')}}"></script>
    <link rel="stylesheet" type="text/css" href="../css/external/jquery.toastmessage.css?{{env('timestamp')}}">
    <fontbase family="Microsoft Yahei"></fontbase>

    <!--[if lt IE 9]>

    　　<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.js?{{env('timestamp')}}"></script>

    　　<script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>

    <![endif]-->

    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../css/help/ie.css?{{env('timestamp')}}">
    <![endif]-->
    @yield('base_resource')
</head>
<body>

<div class="base_slide">
    <div class="base_logo_div">
        <a href="/">
            <img src="../images/logo-xiaoe-white.png"/>
        </a>
    </div>

    <div class="base_menu">
        <div class="base_menu_sub" id="index_help">
            <img src="../images/icon_data_new.png" class="base_icon" style="margin-left: -15px;"/>
            <span >文档首页</span>
        </div>
        @yield('index_help')
    </div>

    <div class="base_menu">
        <div class="base_menu_sub" id="join_help">
            <img src="../images/icon_feedback.png" class="base_icon" />
            <span>开通指引</span>
        </div>
        @yield('join_help')
        <div class="base_menu_sub" id="instructions">
            <img src="../images/icon_neironglist.png" class="base_icon" />
            <span>使用文档</span>
        </div>
        @yield('instructions')

        <div class="base_menu_sub" id="system_update">
            <img src="../images/icon_neironglist.png" class="base_icon" />
            <span>知识商品</span>
        </div>
        @yield('system_update')

        <div class="base_menu_sub" id="explainDoc">
            <img src="../images/icon_neironglist.png" class="base_icon" />
            <span>说明文档</span>
        </div>
        @yield('explainDoc')
        <div class="base_menu_sub" id="qs_help">
            <img src="../images/icon_neirongpinglun.png" class="base_icon" />
            <span>帮助答疑</span>
        </div>
        @yield('qs_help')
    </div>

</div>

<div class="base_right">
    <div class="base_topBar">

        <div class="base_avatar" >
            <img src="@if(session('wx_share_image')){{ session('wx_share_image') }}@else{{ session('avatar') }}@endif" />
            <div class="name">@if(session('wx_app_name')){{ session('wx_app_name') }}@else{{ session('nick_name') }}@endif</div>
            <div class="join">@if(session('wxapp_join_statu')==1)已接入@else<a href="/help" title="我要接入">未接入</a>@endif</div>
        </div>

        <div class="base_explain">
            <span id="toolbar_title">@yield('base_explain')</span>
        </div>

    </div>
    <div class="base_content">
        <div class="base_mainContent">
            @yield('base_mainContent')
        </div>
    </div>
</div>

{{--加载进度条--}}
<div id="base_loading">
    <img style="width: 150px;height: 150px" id="login_progressImage" src="../images/Loading2.gif"/>
</div>

@yield('base_modal')

</body>

</html>
