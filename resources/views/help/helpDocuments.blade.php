@extends('help.baseLayout')
@section('base_title')
    小鹅通平台接入指引
@stop

@section('base_resource')
    <link type=text/css rel="stylesheet" href="../css/help/help.css?{{env('timestamp')}}">
@stop

@section('base_explain')
    <span style="font-size: 18px">小鹅通平台接入指引</span>
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
            <p class="hp " onclick="window.location.href ='/help#hp2'">· 接入工作</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp3'">· 设置支付</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help#hp4'">· 优化体验</p>
        </div>
    </div>
@stop

@section('base_mainContent')
    <div class="hearder_title">
        <h1>小鹅通平台接入指引</h1>
    </div>

    <div>
        <div id="hp0"></div>
        <div style="clear: both"></div>
        <h3>一、准备工作</h3><hr />
        <p>1、如果您已经拥有一个经认证的微信服务号，请跳略此步骤。</p>
        <p class="po"></p>
        <p>2、如果您还未申请微信服务号，请点击 <a href="https://mp.weixin.qq.com" target="_blank">https://mp.weixin.qq.com/</a> 申请</p>
        <p class="po"></p>
        <p>注*1：如需使用小鹅通工具的内容付费功能等相关高级功能，目前微信公众平台提供的三种账号类型中，只有服务号同时具备高级接口能力和微信支付功能，这关系到接下来功能点的切入，所以尝试使用小鹅通内容付费和社群运营功能前，请先确认您拥有一个已经认证的微信服务号。</p>
        <img src="../images/help/wechat_server_register.png" alt="微信公众号注册"/>
        <p>注 2*：微信公众平台账号申请相关问题请咨询 </p>
        <p><a href="http://kf.qq.com/faq/120911VrYVrA1509086vyumm.html" target="_blank">http://kf.qq.com/faq/120911VrYVrA1509086vyumm.html</a> </p>
    </div>
    <div>
        <div id="hp1"></div>
        <div style="clear: both"></div>
        <h3>二、注册登录</h3><hr />
        <p>1、登录小鹅通官网 <a href="http://www.xiaoe-tech.com" target="_blank">www.xiaoe-tech.com</a>，点击右上角【登录】；</p>
        <p>微信扫码，完善注册信息后勾选“我已阅读并同意《小鹅通服务协议》”，完成注册。</p>
        <img src="../images/help/help_1-1.png" alt="注册"/>
        <p>2、现在页面已跳转至您的专属管理后台，在左侧的菜单栏，您可以看到【仪表盘】【内容列表】等相关功能。</p>
        <img src="../images/help/help_1-2.png" alt="专栏示例" />
    </div>
    <div>
        <div id="hp2"></div>
        <div style="clear: both"></div>
        <h3>三、接入小鹅通</h3><hr />
        <p class="b">1、登录您要匹配的微信公众账号，获取匹配所需的相关信息。</p>
        <div id="hp6"></div>
        <div style="clear: both"></div>
        <p class="po"></p>
        <p>1.1、 获取微信公众号 AppID</p>
        <p class="po"></p>
        <p>step1：登录您要匹配的微信公众账号，点击左下方【基本配置】</p>
        <img src="../images/help/help_3.1.1.png" alt="" />
        <p class="po"></p>
        <p>step2：查看开发者 AppID 并记录。</p>
        <img src="../images/help/help_3.1.2.png" alt="" />
    </div>
    <div>
        <div id="hp7"></div>
        <div style="clear: both"></div>
        <p>1.2、获取微信商户号 ID</p>
        <p class="po"></p>
        <p>step1：点击左下方【微信支付】，如果您已开通支付功能，则可查看商户号及交易信息（未开通则需要开通）。</p>
        <p class="po"></p>
        <p>step2：查看商户号 ID 并记录。</p>
        <p class="po"></p>
        <p>step3：点击【微信支付商户平台】登录商户平台。</p>
        <img src="../images/help/help_3.2.png" alt="" />
        <p class="po"></p>
            </div>
    <div>
        <div id="hp8"></div>
        <div style="clear: both"></div>
        <p>1.3、获取微信商户 API 秘钥</p>
        <p class="po"></p>
        <p>step1：登录微信商户平台，点击【账户中心】->【API 安全】第二栏 API 秘钥</p>
        <img src="../images/help/help_3.3.1.png" alt="" />
        <p>step2：若已设置 API 秘钥，请查看并记录。</p>
        <p class="po"></p>
        <p>若未设置过 API 秘钥，请设置 32 位随机秘钥并记录（微信秘钥只能重设 能再次查看）</p>
        <img src="../images/help/help_3.3.2.png" alt="" />

    </div>
    <div>
        <p class="b">2、登录小鹅通管理平台并填写基础信息 </p>
        <p class="po"></p>
        <p>2.1、登录小鹅通管理平台，点击左下方【账户管理】->【公众号设置】</p>
        <p class="po"></p>
        <p>填写刚才记录下的信息，如图所示。</p>
        <p>（补充信息可暂时不填）</p>
        <p style="    margin-bottom: -20px;"><a href="#hp6" title="查看获取微信公众号 AppID 步骤" style="margin-right:10px;">如何获取微信公众号 AppID？</a>
            <a href="#hp7" title="查看获取微信商户号 ID 步骤" style="margin-right:10px;">如何获取微信商户号 ID？</a>
            <a href="#hp8" title="查看获取微信商户 API 密钥 步骤" style="margin-right:10px;">如何获取微信商户 API 秘钥？</a>
        </p>
        <img src="../images/help/help_1-3.png" alt="" />
        <p class="po"></p>
        <p>2.2、填写完毕，保存设置后会弹出小鹅通授权扫码页面，需要所接入公众号的管理员微信扫码进行授权</p>
        <img src="../images/help/help_1-4.png" alt="" />

    </div>
    <div>
        <div id="hp3"></div>
        <div style="clear: both"></div>
        <h4 class="b">3、设置支付</h4>
        <p class="po"></p>
        <p class="po"></p>
        <p>再次登录微信公众平台，设置支付授权</p>
        <p class="po"></p>
        <p class="po"></p>
        <p>微信公众平台点击【微信支付】-> 【开发配置】->【⽀付授权⽬录】 </p>
        <p>填写 http://$APPID.h5.xiaoe-tech.com/content_page/ </p>
        <p>（$APPID为微信公众号APPID）</p>
        <img src="../images/help/help_1-5.png" alt="" />
        <p>现在您已经成功接入小鹅通自媒体工具，可以使用我们为您提供的功能了。</p>
        <p class="po"></p>
        <p>您的前端外置链接为 <span class="red">$APPID</span>.h5.xiaoe-tech.com  （<span class="red">$APPID</span>替换为微信公众号APPID）</p>
        <p class="po"></p>
        <p>可以将链接嵌入公众号的二级菜单等位置</p>
        <img src="../images/help/wechat_menu.png" alt="菜单设置" />
    </div>
    <div>
        <div id="hp4"></div>
        <div style="clear: both"></div>
        <h3>四、优化体验</h3><hr />
        <p>填写补充信息可以帮助您获得更完美的使用体验。</p>
        <p>若未补充，则会出现首页分享至朋友圈或微信好友后无法显示内容、用户评论时评论框上面弹出安全提示等小问题。</p>
        <p class="po"></p>
        <p>step1：登录微信公众平台，点击左下方设置中的【公众号设置】->【功能设置】->【业务域名】，点击设置。</p>
        <img src="../images/help/help_1-7.png" alt="" />
        <p class="po"></p>
        <img src="../images/help/help_1-8.png" alt="" />
        <p>step2：点击下载微信业务域名文件。</p>
        <p class="po"></p>
        <p>step3：登录小鹅通管理后台，点击【账户管理】->【公众号管理】->【补充信息】，点击选择文件，上传下载的微信业务域名文件并保存。</p>
        <p class="po"></p>
        <p>step4：回到微信公众平台，在域名处填写$AppID.h5.xiaoe-tech.com。</p>
        <p class="po"></p>
        <p>step5：保存信息。</p>
        <img src="../images/help/help_4.4.png" alt="" />
        <p class="b">恭喜！您已完成接入小鹅通自媒体工具管理后台所需的全部步骤，接下来请尽情享受关于知识服务与社群运营的种种新玩法吧。</p>
            </div>
    <div>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <p class="po"></p>
        <P class="hp" onclick="window.location.href ='/help/qs#hp1'">帮助答疑：常见问题</P>
        <p class="po"></p>
        <p class="po"></p>
        <h4>提前体验</h4><hr />
        <p class="b">扫码关注小鹅通内容付费公众号</p>
        <img src="../images/help/help_1-0.png" style="padding: 20px 240px;" alt="" />

    </div>

@stop