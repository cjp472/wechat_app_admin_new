@extends('help.baseLayout')
@section('base_title')
    帮助答疑
@stop

@section('base_resource')
    <link type=text/css rel="stylesheet" href="../css/help/help.css?{{env('timestamp')}}">

@stop

@section('base_explain')
    <span style="font-size: 18px">帮助答疑</span>
@stop

@section('qs_help')
@stop

@section('base_mainContent')
    <div class="hearder_title">
        <h1>帮助答疑</h1><hr />
    </div>
    <div>
        <h3>接入使用常见问题</h3>
        <div id="hp1"></div>
        <div style="clear: both"></div>
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


    <div style="margin-top: 80px;">
        <p>联系我们<hr /></p>
        <p>联系电话：+86 400-640-8800</p>
        <p>商务合作：support@xiaoe-tech.com</p>
        <p>公司地址： 深圳市南山区科技园讯美科技广场3号楼16A09</p>
        <p class="tc"><img src="../images/help/help_1-0.png" style="padding: 1px 240px;" alt="官方微信公众号二维码" /> </p>
        <p class="tc">微信扫一扫 关注小鹅通</p>
        <p class="tc">Copyright ©  深圳小鹅网络技术有限公司</p>
        <p></p>
        <p></p>
    </div>


@stop