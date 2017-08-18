@extends('help.baseLayout')
@section('base_title')
    小鹅通平台企业模式开通指引
@stop

@section('base_resource')
    <link type=text/css rel="stylesheet" href="../css/help/help.css?{{env('timestamp')}}">
@stop

@section('base_explain')
    <span style="font-size: 18px">小鹅通平台企业模式开通指引</span>
@stop

@section('join_help')
    <div class="base_menu">
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp0'">· 准备工作</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp1'">· 注册登录</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp2'">· 授权服务号</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp3'">· 设置支付</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp4'">· 支付授权</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp5'">· 公众号配置</p>
        </div>
    </div>
@stop

@section('base_mainContent')
    <div class="hearder_title">
        <h1>小鹅通平台企业模式开通指引</h1>
    </div>
    <style type=text/css rel="stylesheet">
        .limg{
            width: 80% !important;
        }
    </style>
    <div>
        <div id="hp0"></div>
        <div style="clear: both"></div>
        <h3>第一步 准备工作</h3><hr />
        <p>1、如果您已经拥有一个经认证的微信服务号，请略过此步骤。</p>
        <p class="po"></p>
        <p>2、如果您还未申请微信服务号，请点击 <a href="https://mp.weixin.qq.com" target="_blank">https://mp.weixin.qq.com/</a> 申请</p>
        <p class="po"></p>
        <p>注*1：如需使用小鹅通工具的内容付费功能等相关高级功能，目前微信公众平台提供的三种账号类型中，<span style="color:#ff0000;"> 只有服务号同时具备高级接口能力和微信支付功能</span>，这关系到接下来功能点的切入，所以尝试使用小鹅通内容付费和社群运营功能前，请先确认您拥有一个已经认证的微信服务号。</p>
        <img src="../images/help/joinGuide/1.png" class="limg" alt="微信公众号注册"/>
        <p>注 2*：微信公众平台账号申请相关问题请咨询 </p>
        <p><a href="http://kf.qq.com/faq/120911VrYVrA1509086vyumm.html" target="_blank">http://kf.qq.com/faq/120911VrYVrA1509086vyumm.html</a> </p>
    </div>
    <div>
        <div id="hp1"></div>
        <div style="clear: both"></div>
        <h3>第二步 开始开通</h3><hr />
        <p>1、在【运营模式设置】页面点击更改运营模式</p>
        <img src="../images/help/joinGuide/2.jpeg" class="limg" alt="注册"/>
        <p class="po"></p>
        <p>2、在弹出窗口中点击立即配置</p>
        <img src="../images/help/joinGuide/3.jpeg" class="limg" alt="专栏示例" />
    </div>
    <div>
        <div id="hp2"></div>
        <div style="clear: both"></div>
        <h3>第三步 授权服务号</h3><hr />
        <p class="">1、点击【我有认证服务号，立即设置】，在新窗口用微信扫描二维码授权。</p>
        <p>注*：此处需要授权的是您之前申请的<span style="color:#ff0000;">服务号</span>，这里需要您使用该服务号<span style="color:#ff0000;">管理员的微信号</span>进行扫码，并且完成此步骤之后请不要关闭小鹅通管理后台页面。</p>
        <img src="../images/help/joinGuide/4.jpeg" class="limg" />
        <p>2、支付信息需要填写微信商户号和微信商户 API 密钥，获取这两项信息的方式见“第四步”。</p>
        <img src="../images/help/joinGuide/5.png" class="limg" />
    </div>
    <div>
        <div id="hp3"></div>
        <div style="clear: both"></div>
        <h3>第四步 支付设置</h3><hr />
        <p class="b">1、获取微信商户号 ID</p>
        <p>注*：此步骤需要您登录<span style="color:#ff0000;">已授权的微信服务号</span>，在进行下面的操作之前请确保您已经完成授权服务号。（登录地址：<a href="https://mp.weixin.qq.com/" target="_blank">https://mp.weixin.qq.com/）</a></p>
        <p class="po"></p>
        <p class="po"></p>
        <p>step1：登录已授权的<span style="color:#ff0000;">微信服务号</span>，点击左下方【微信支付】，<span style="color:#ff0000;">如果您已开通支付功能，则可查看商户号及交易信息（未开通则需要开通；刚开通支付功能的用户，需要等待大约1天时间才会生成商户信息）</span>。</p>
        <p class="po"></p>
        <p class="po"></p>
        <p>step2：查看商户号 ID 并<span style="color:#ff0000;">记录</span>。</p>
        <img src="../images/help/joinGuide/6.png" class="limg" alt="查看商户ID" />
        <p>此步骤结束后，点击【微信支付商户平台】，跳转至微信支付商户平台进行下一步操作。</p>
        <p class="po"></p>
    </div>
    <div>
        <p class="b">2、获取微信商户 API 秘钥</p>
        <p class="po"></p>
        <p>step1：若您之前在<span style="color:#ff0000;">微信商户平台</span>设置过API 密钥且记录了密钥，请跳过step2。</p>
        <p>注*：微信商户平台不支持再次查看API密钥，只能重新设置，若您忘记了之前设置过的API 密钥，请进行step2重新设置。（<span style="color:#ff0000;">友情提示</span>：如之前已设置过此密钥，重置前请确认没有其他业务在使用该密钥）</p>
        <p class="po"></p>
        <p class="po"></p>
        <p>step2：若未设置过或忘记已设置的 API 秘钥，请登录<span style="color:#ff0000;">微信商户平台</span>（可在获取商户 ID 页面直接跳转，见上图），点击【账户中心】->【API 安全】第二栏 API 秘钥，设置 <span style="color:#ff0000;">32 位</span>随机密钥并<span style="color:#ff0000;">记录</span>.</p>
        <img src="../images/help/joinGuide/7.png" class="limg" alt="" />
        <p>注*：请记住您的 API 密钥，以防以后需要使用的时候再次重复设置。</p>
        <p class="po"></p>
    </div>
    <div>
        <p class="b">3、填写支付信息</p>
        <p>回到小鹅通管理后台，点击“编辑”图标，填写微信商户号，微信商户 API 密钥。 </p>
        <img src="../images/help/joinGuide/8.png" class="limg" alt="" />
    </div>
    <div>
        <div id="hp4"></div>
        <div style="clear: both"></div>
        <h3>第五步 设置支付授权</h3>
        <p class="po"></p>
        <p class="po"></p>
        <p>1. 复制支付授权目录。</p>
        <p class="po"></p>
        <img src="../images/help/joinGuide/9.jpeg" class="limg" alt="复制支付授权目录"/>
        <p class="po"></p>
        <p>2. 返回您的<span style="color:#ff0000;">微信公众平台</span>点击【微信支付】-> 【开发配置】->【⽀付授权⽬录】，选择{{ empty(\App\Http\Controllers\Tools\AppUtils::getOpenId()) ? "https" : (empty(\App\Http\Controllers\Tools\AppUtils::getIsNew(\App\Http\Controllers\Tools\AppUtils::getAppID())) ? "https" : "http") }}类型，黏贴刚刚复制的支付授权目录并添加确认。</p>
        <img src="../images/help/joinGuide/10.png" class="limg" />
        @if(empty(\App\Http\Controllers\Tools\AppUtils::getOpenId()) || empty(\App\Http\Controllers\Tools\AppUtils::getIsNew(\App\Http\Controllers\Tools\AppUtils::getAppID())))
            <img src="../images/help/zhifushouquanmulu.png" class="limg" />
        @endif
        <p>3、添加完成后返回管理台，点击【保存】</p>
        <img src="../images/help/joinGuide/12.png" class="limg" alt="" />
        <p><span style="color:#ff0000;">注*1：</span>完成<span style="color:#ff0000;">以上五步的所有步骤</span>之后，即成功开通小鹅通企业模式；您可以在【运营模式设置】->【公众号授权信息】中扫描手机页面预览二维码预览您的页面，并将新的手机访问页面链接复制到<span style="color:#ff0000;">公众号的自定义菜单栏</span>等位置。</p>
        <img src="../images/help/joinGuide/13.png" class="limg" alt="" />

        <p>您可以按照教程添加付费内容，开始体验知识服务的各项功能，具体使用教程您可以关注我们的微信公众号“xiaoeservice”，在【鹅资讯】中找到新手使用教程。</p>
        <p class="po"></p>
        <p>注*2：完成以上五步后，建议您不要关闭微信公众平台页面，请进行第六步操作。</p>
        <p class="po"></p>,
    </div>
    <div>
        <div id="hp5"></div>
        <div style="clear: both"></div>
        <h3>第六步 填写公众号配置</h3><hr />
        <p><span class="red">注*</span>公众号配置不填写不影响功能使用，但会出现用户评论时评论框上面弹出安全提示的问题，因此建议您完成填写公众号配置。</p>
        <p class="po"></p>
        <p>step1：进入<span class="red">微信公众平台</span>，点击左下方设置中的【公众号设置】->【功能设置】->【业务域名】，点击设置。</p>
        <img src="../images/help/joinGuide/14.png" class="limg"alt="" />
        <p class="po"></p>
        <p>step2：点击下载微信业务域名文件。</p>
        <img src="../images/help/joinGuide/15.png" class="limg" alt="" />
        <p class="po"></p>
        <p>step3：登录<span class="red">小鹅通管理后台</span>，点击【账户管理】->【运营模式设置】->【公众号配置】，点击选择文件，上传<span class="red">下载的微信业务域名文件</span>并保存。</p>
        <p class="po"></p>
        <p>step4：复制【业务域名】</p>
        <img src="../images/help/joinGuide/16.png" class="limg" alt="" />
        <p class="po"></p>
        <p>step5：回到微信公众平台，在域名处黏贴【业务域名】，3个域名选项中选择任意一个黏贴即可。</p>
        <img src="../images/help/joinGuide/17.png" class="limg" alt="" />
        <p class="po"></p>
        <p>step6：保存信息。</p>
        <p class="po"></p>
        <p class="po"></p>
        <p>完成。</p>
    </div>
    <div>


    <div>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="b">扫码关注小鹅通内容付费公众号</p>
        <img src="../images/help/joinGuide/18.png" style="padding: 20px 240px;" alt="" />

    </div>

@stop