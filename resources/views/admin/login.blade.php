<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="renderer" content="webkit">
        <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
        <link rel="stylesheet" type="text/css" href="../css/admin/login.css?{{env('timestamp')}}" />
        <!--[if IE 8]>

            <script src="../js/external/jquery-1.11.3.js"></script>
            <link rel="stylesheet" type="text/css" href="../css/admin/ie-login.css" />
        <![endif]-->
        {{--提示框--}}
        <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
        <link rel='icon' href='logo-64.ico' type='image/x-ico' />
        {{-- {{dump($isMobile)}} --}}
        @if($isMobile)
        <script>
            !function() {
                var defwidth,
                    deviceWidth =  parseInt(window.screen.width),
                    deviceHeight = parseInt(window.screen.height),
                    ratio = deviceHeight/deviceWidth,
                    ua = navigator.userAgent;
                if(ratio>1.5) {
                    defwidth = 400;
                    deviceScale = deviceWidth/defwidth;
                }else{
                    defwidth = 600;
                    deviceScale = deviceWidth/defwidth;
                }
                if (/Android (\d+\.\d+)/.test(ua)){
                    var version = parseFloat(RegExp.$1);
                    if(version>2.3){
                        document.write('<meta name="viewport" content="width='+defwidth+',initial-scale='+deviceScale+', minimum-scale = '+deviceScale+', maximum-scale = '+deviceScale+', target-densitydpi=device-dpi">');
                    }else{
                        document.write('<meta name="viewport" content="width='+defwidth+',initial-scale=0.75,maximum-scale=0.75,minimum-scale=0.75,target-densitydpi=device-dpi" />');
                    }
                    window.addEventListener("resize", function() {
                        if(document.activeElement.tagName=="INPUT" || document.activeElement.tagName=="TEXTAREA") {
                            var currHeight = window.screen.height;
                            var changeHeight = deviceHeight -  currHeight;
                            window.setTimeout(function() {
                                document.body.style.height = deviceHeight + 'px';
                                document.body.style.webkitTransform = 'translateY(-'+changeHeight+'px)';
                                document.body.style.mozTransform = 'translateY(-'+changeHeight+'px)';
                                document.body.style.transform = 'translateY(-'+changeHeight+'px)';
                            },0);
                        }
                    });
                }else{
                    document.write('<meta name="viewport" content="width='+defwidth+', user-scalable=no">');
                }
            }()
        </script>
        @endif

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
        <input type="hidden" id="xet_machineip" value="{{\App\Http\Controllers\Tools\Utils::getServerInsideAddress()}}">
        <input type="hidden" id="xet_userip" value="{{Illuminate\Support\Facades\Request::ip()}}">
        {{--logo区--}}
        <div class="logoArea">
            <img onclick="backHomePage()" src="../images/logo-xiaoe.png" class="logo" alt="小鹅通" title="小鹅通" />
        </div>

        {{--微信登录--}}
        <div class="content" id="wechatLogin" @if($isMobile) style="display: none;" @endif>
            <ul class="loginTitle">
                <li>
                    <img src="../images/login_wechat_selected.png" alt="微信登录" title="微信登录"/>
                    <a href="javascript:void(0)">微信登录</a>
                </li>
                <div class="line"></div>
                <li>
                    <img src="../images/login_account.png" alt="账号登录" title="账号登录"/>
                    <a href="javascript:void(0)" style="color: #c3c3c3;">账号登录</a>
                </li>
            </ul>
            <div class="qrCodeArea">
                <div id="login_container"></div>
            </div>
        </div>

        {{--正常登陆--}}
        <div class="content" id="normalLogin" @if(!$isMobile) style="display: none;" @endif>
            <ul class="loginTitle">
                <li>
                    <img src="../images/login_wechat.png" alt="微信登录" title="微信登录"/>
                    <a href="javascript:void(0)" style="color: #c3c3c3;">微信登录</a>
                </li>
                <div class="line"></div>
                <li>
                    <img src="../images/login_account_selected.png" alt="账号登录" title="账号登录"/>
                    <a href="javascript:void(0)" >账号登录</a>
                </li>
            </ul>

            <input type="text" class="loginInput" id="username" style="margin-top: 30px;"
            placeholder="请输入账户名称" autocomplete="off"/>
            <input type="password" class="loginInput" id="password" style="margin-top: 20px;"
            placeholder="请输入密码" autocomplete="off"/>

            <input id="loginCheckbox" type="checkbox" class="loginCheckbox"/>
            <label for="loginCheckbox" class="rememberMeHTML">记住密码</label>

            <button type="button" id="submit">登&nbsp;&nbsp;&nbsp;&nbsp;录</button>
            <div class="horLine"></div>
            @if($isMobile)
                <div class="tip" style="font-size: 15px;">新用户请使用电脑登录小鹅通官网进行注册</div>
            @else
                <div class="tip">首次登陆请使用微信扫码登录</div>
            @endif
        </div>

        {{--底部--}}
        <div class="footerArea">—— 小鹅通，专注于知识服务与社群运营的聚合型工具 ——</div>


        <script type="text/javascript" src="../js/external/js-error-catch.min.js"></script>
        <script type="text/javascript" src="../js/admin/config/config.js"></script>
        <script type="text/javascript" src="../js/external/jquery.js"></script>
        {{--二维码--}}
        <script type="text/javascript" src="https://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
        {{--弹框--}}
        <script type="text/javascript" src="../js/external/xcConfirm.js"></script>
        {{--Cookie--}}
        <script type="text/javascript" src="../js/external/jquery.cookie.js"></script>
        <script src="/js/admin/client.js"></script>
        <script type="text/javascript" src="../js/admin/login.js?{{env('timestamp')}}"></script>
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
