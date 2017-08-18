<!doctype html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>小鹅通平台接入指引——小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    <link rel='icon' href='logo-64.ico' type='image/x-ico' />
    <link type=text/css rel="stylesheet" href="../css/external/bootstrap.min.css">
    <link type=text/css rel="stylesheet" href="../css/help/help.css?{{env('timestamp')}}">
    <script src="../js/external/jquery.js"></script>
    <script type="text/javascript" src="../js/external/bootstrap.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/jquery.cookie.js?{{env('timestamp')}}"></script>

</head>
<body>
    <div class="hearder_title">
        <h1>小鹅通平台接入指引</h1><h2>&nbsp;</h2>
    </div>

    <div>
        <h3>一、准备工作</h3>
        <p class="b">1、 微信服务号</p>
        <p>请到微信公众平台（网址：<a href="https://mp.weixin.qq.com/" target="_blank">https://mp.weixin.qq.com/</a>）申请好微信服务号，并通过微信认证，和开通微信支付；</p>
        <p>注：认证的服务号拥有微信调⽤的相关接⼜权限，所以需要⼀个服务号作为平台承载， 可以通过订阅号菜单设置的⽅式将⽤户访问⼊⼜挂载在订阅号上。</p>
    </div>
    <div>
        <h3>二、注册登录</h3>
        <p> 1、 登录小鹅通官网 <a href="http://www.xiaoe-tech.com" target="_blank">www.xiaoe-tech.com</a> ， 点击右上角【登录】；</p>
        <img src="{{URL::to('images/help/help_1-1.png')}}" alt=""/>
        <p>2、通过微信扫码登录管理台；</p>
        <p>3、通过新增创建专栏、音频、视频等内容；</p>
        <img src="{{URL::to('images/help/help_1-2.png')}}" alt="" />
    </div>
    <div>
        <h3>三、接入工作</h3>
        <p>1、登录小鹅通管理平台设置公众号信息基础信息，如图所示⽰(公众号相关信息获取请查看第 三条)</p>
        <img src="{{URL::to('images/help/help_1-3.png')}}" alt="" />
    </div>
    <div>
        <p>2、保存设置后弹出小鹅通授权扫码页面，需要接入公众号的运营者微信扫码进行授权如图</p>
        <img src="{{URL::to('images/help/help_1-4.png')}}" alt="" />
    </div>
    <div>
        <p>3、微信公众平台相关操作</p>
        <p class="b">3.1.获取微信公众号APPID </p>
        <p>登录微信公众平台，点击【基本配置】，获取开发者APPID，如图</p>
        <img src="{{URL::to('images/help/help_3.1.1.png')}}" alt="" />
        <img src="{{URL::to('images/help/help_3.1.2.png')}}" alt="" />
    </div>
    <div>
        <p class="b">3.2.获取微信商户ID </p>
        <p>点击【微信⽀支付】，如果开通⽀支付功能，可以看到微信⽀支付商户号ID信息</p>
        <img src="{{URL::to('images/help/help_3.2.png')}}" alt="" />
    </div>
    <div>
        <p class="b">3.3.获取微信商户API秘钥 </p>
        <p>登录微信商户平台，点击【账户中⼼心】->【API安全】第⼆二栏API秘钥</p>
        <img src="{{URL::to('images/help/help_3.3.1.png')}}" alt="" />
        <p>如果没有设置API秘钥，设置32位随机秘钥，记得保存好秘钥，微信秘钥只能重设不不能查看</p>
        <img src="{{URL::to('images/help/help_3.3.2.png')}}" alt="" />

    </div>
    <div>
        <h4 class="b">4、设置支付</h4>
        <p>登录微信公众平台，去设置支付授权目录</p>
        <p>去公众号设置  微信支付 》开发配置 》⽀付授权⽬录》配置 </p>
        <p>http://$APPID.h5.xiaoe-tech.com/content_page/（$APPID为微信公众号APPID），如图</p>
        <img src="{{URL::to('images/help/help_1-5.png')}}" alt="" />
        <p>现在您已经在小鹅通平台生成您的平台了，访问链接</p>
        <p class="ei"><span class="red">$APPID</span>.h5.xiaoe-tech.com  （<span class="red">$APPID</span>替换为微信公众号APPID）</p>
        <p>就可以使用啦</p>
    </div>
    <div>
        <h3>四、优化体验</h3>
        <p class="b">1、登录小鹅通管理平台设置公众号信息的补充信息</p>
        <p>如果您需要更好的体验，比如首页分享到微信，评论框上面弹出安全提示等，需要在小鹅通管理平台公众号设置中补充信息</p>
        <img src="{{URL::to('images/help/help_1-6.png')}}" alt="" />
        <p class="b">2、微信业务域名文件上传，用户评论时不显示安全提示弹窗</p>
        <p>先在公众号中下载域名验证文件上传文件到小鹅通管理平台业务域名文件上传处，同时在微信公众号中设置业务域名，如图</p>
        <p>步骤：</p>
        <p>1. 公众号设置 》 功能设置 》找到“业务域名”行 点击“设置”</p>
        <img src="{{URL::to('images/help/help_1-7.png')}}" alt="" />
        <img src="{{URL::to('images/help/help_1-8.png')}}" alt="" />
        <p>2. 下载微信业务域名文件，在小鹅通管理台账户管理》公众号管理 》补充信息处选择该文件上传 ，提交修改</p>
        <p>3. 上传完成后，回到微信公众号业务域名设置页，填写域名，格式为 </p>
        <p class="ei"> <span class="red">$APPID</span>.h5.xiaoe-tech.com  （<span class="red">$APPID</span>替换为微信公众号APPID）</p>
        <img src="{{URL::to('images/help/help_4.4.png')}}" alt="" />
    </div>
    <div>
        <h3>五、提前体验</h3>
        <p class="b">扫码关注小鹅通内容付费公众号</p>
        <img src="{{URL::to('images/help/help_1-0.png')}}" style="padding: 20px 240px;" alt="" />

    </div>
<div>
    <h3>六、接入使用常见问题</h3>
    <P class="qs">1.使用小鹅通平台接入公众号，用户为内容付款后钱跑到哪里去了？</P>
    <P>答：用户付款后，付的款项会直接进入在公众号设置里设置的微信商户平台里。</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">2.接入小鹅通平台的公众号必须要使用服务号吗？订阅号可以吗？</P>
    <P>答：链接入口可以挂在订阅号菜单上或者订阅号内，但是使用服务需要申请一个服务号，开通支付功能；微信服务号目前拥有所有需要的接口权限，比如用户登录授权、支付等，而现在订阅号基本都不支持微信的这些接口权限，所以需要服务号作为业务服务功能的承载。</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">3.没有找到扫码授权页，通过对应链接进入报错（scope参数错误或没有scope权限）</P>
    <P>答：可以通过扫码页链接（https://app.xiaoe-tech.com/platform/request_auth）直接扫码授权给小鹅通平台 ，扫码微信号必须是对应公众号的管理员或者运营者微信。</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">4.公众号设置里的补充信息是做什么的？</P>
    <P>答：
        业务域名文件，接入后在用户微信上的输入框会弹出安全提示警告，设置了业务域名后可以不显示警告；</P>
    <P>公众号的二维码，一些推广功能会用到二维码，如用户使用邀请码后弹出二维码让用户扫码关注，二维码可以自由设置为想要推广公众号的二维码；</P>
    <P>首页分享标题，首页分享图片，首页分享描述，在首页分享链接给朋友或朋友圈显示的内容；</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">5.小鹅通平台登录管理员微信号是否可以更换？</P>
    <P>答：登录小鹅通平台可以通过设置账号名和密码登录，微信号暂时不支持更换。</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">6.如果我们想要上线产品，需要一些什么配合？</P>
    <P>答：现在上线比较简单，只需要去【账户管理】【公众号设置】那里把补充信息填写完整，然后你们可以上传音频、视频、图文三种类型的内容，可以以专栏或单笔的形式去定价，然后把链接挂载在公众号菜单上去正式对外使用就可以了。</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">7.课程内容能不能不在主页显示，但直接可以放链接销售？</P>
    <P>答：可以通过下架操作，对专栏或者单个内容进行下架，下架后内容不在首页显示，可以通过内容链接访问，用户通过链接访问下架内容观看购买等操作和上架内容操作一致，可以放课程内容链接去做推广销售。</P>
    <p class="p0"> &nbsp;</p>
    <P class="qs">8.如果我的课程希望在其他公号上推广，他们也接入小鹅，可以实现在他们那里支付，观看我们的视频课程吗？</P>
    <P>答：可以在管理端渠道分销菜单新增渠道，生成不同的渠道推广链接，把链接给分销端，分销端可以通过发文章或者放在公众号菜单上等推广方式去推广，推广数据可以在管理端渠道分销里展示。</P>
    <P class="p0"> &nbsp;</P>
    <P></P>
    <P></P>

</div>

</body>
</html>