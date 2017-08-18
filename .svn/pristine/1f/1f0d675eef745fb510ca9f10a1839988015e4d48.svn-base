<!doctype html>
<html>
<head>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>
    @endif
    <title>小鹅通，基于微信的内容付费工具</title>
    <link rel='icon' href='{{URL::to('/')}}/logo-64.ico' type=‘image/x-ico’/>

    <link type=text/css rel="stylesheet" href="../css/help/style.css?{{env('timestamp')}}">

</head>
<body>
<div class="pcContent">
    <div class="base_slide">
        <div class="base_logo_div">
            <a href="/">
                <img src="../images/logo-xiaoe-white.png"/>
            </a>
        </div>
        <ul id="sidebarNav" class="sidebar-nav">
            @foreach( $help_doc_list as $key1=>$firstKey )
                <li class="firstKeyContent">
                    <p class="firstKey" data-id="{{$firstKey->id}}">{{$firstKey->name}}</p>
                    <ul>
                        @foreach( $firstKey->sublist as $key2=>$secondKey )
                            <li>
                                <p class="secondKey" data-link="{{$secondKey->out_line}}"
                                   data-type="{{$secondKey->link_type}}" data-id="{{$secondKey->id}}">
                                    · {{$secondKey->name}}</p>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
            {{-- <li>
                <p class="firstkey" data-id="0">测试一个外链</p>
                <ul>
                    <li>
                        <p class="secondKey" data-link="http://www.baidu.com" data-type="1" data-id="000">外链百度</p>
                    </li>
                </ul>
            </li> --}}
        </ul>

    </div>
    <div class="base_right">
        <div class="base_topBar">

            <div class="base_explain">
                <span id="toolbar_title"></span>
            </div>

        </div>
        <div class="base_content">
            <div id="mainContent" class="base_mainContent show">

            </div>
            <iframe src="" id="frameContent" class="hide" frameborder="0" width="100%"
                    height="800px"></iframe>
        </div>
    </div>
    <div id="base_loading">
        <!-- <img style="width: 150px;height: 150px" id="login_progressImage" src="../images/Loading2.gif"/> -->
        <div class="loadingContent">
            <svg viewBox="25 25 50 50" class="circular">
                <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
            </svg>
            <p class="loadingText">加载中</p>
        </div>
    </div>
</div>
<div class="mobileContent">
    <div class="base_topBar moblie_top">

        <div class="base_explain">
            <span id="toolbar_title_mobile"></span>
        </div>

    </div>
    <div class="base_content helpContentMobile">
        <div id="mainContentMoblie" class="base_mainContent show">

        </div>
        <iframe src="" id="frameContent" class="hide" frameborder="0" width="100%" height="800px"></iframe>
    </div>
</div>
<script src="../js/external/jquery.js"></script>
<script type="text/javascript" src="../js/help/index.js?{{env('timestamp')}}"></script>
<script type="text/javascript" src="../js/utils/nj-client.min.js?{{env('timestamp')}}"></script>
<script>
    NJBUS.init({
        nj_appid: 2,//异常上报系统的appid
        nj_url: "https://jsreport.xiaoe-tech.com:5188/accepter?",//异常上报系统的安装地址
        level: 4,//错误级别 1-debug 2-info 4-error
        ignore: [],// 忽略某个错误, 支持Regexp、Function
        b_uid: 0,//（可不填）业务用户id，标识当前出错用户的业务id，便于查找错误
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
