@extends('help.baseLayout')
@section('base_title')
    商户使用说明
@stop

@section('base_resource')
    <link type=text/css rel="stylesheet" href="../css/help/help.css?{{env('timestamp')}}">

@stop

@section('base_explain')
    <span style="font-size: 18px">商户使用说明</span>
@stop

@section('instructions')
    <div class="base_menu">
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help/instructions#hp1'">· 基础篇</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help/instructions#hp2'">· 运营篇</p>
        </div>
        <div class="base_menu_sub">
            <p class="hp " onclick="window.location.href ='/help/instructions#hp3'">· 进阶篇</p>
        </div>
    </div>
@stop

@section('base_mainContent')
    <div class="hearder_title">
        <h1>小鹅通内容付费管理台商户使用说明</h1>
        <hr />
    </div>

    <div>
        <img src="../images/help/index_logo1.png" style="width: 120px;margin: -30px 2px 26px;" alt=""/>
        <h4 class="b"> 小鹅通，专注于知识服务与社群运营的聚合型工具</h4>
        <p></p>
        <div style="width: 96%; margin: 10px auto; text-indent: 1px; text-align: left;">
            <p>· 为自媒体打造自己的微信小程序</p>
            <p>· 提供丰富的内容付费与社群运营服务</p>
            <p>· 吴晓波频道、张德芬空间和十点读书都在使用</p>
        </div>

    </div>
    <div style="width: 96%; margin: 30px auto; text-indent:2px;">
        <p>小鹅通是一套全功能的内容付费工具，为商家提供了完整的付费内容解决方案。小鹅通提供了音频、视频、图文等多样化的内容付费支持，精心打造了付费订阅、账号打通、会员体系、渠道推广、用户运维、数据分析等功能模块。使用小鹅您可以快速、低成本的搭建一个付费频道，引导您的粉丝成为付费订阅用户。</p>
    <p>为了让商户更好的利用小鹅通内容付费系统，下面将分<span class="b"> 基础篇、运营篇、进阶篇 </span>三篇来介绍管理台的使用。</p>
    </div>


    <div>
        <div id="hp1"></div>
        <div style="clear: both"></div>
        <h3 class="tc b">基 础 篇</h3><hr />
        <p> 本篇通过商户入驻、公众号接入、内容部署、上线准备的顺序，帮助商户完成上线运营前的平台接入工作。</p>

    </div>
    <div>
        <h4 class="b">一、商户入驻</h4>
        <p> 1、 登录小鹅通官网 <a href="http://www.xiaoe-tech.com" target="_blank">www.xiaoe-tech.com</a> ， 点击右上角【登录】；</p>
        <p>2、通过微信扫码登录管理台；</p>
        <img src="../images/help/help_1-1.png" alt=""/>
        <p>入驻平台轻松搞定，就是这么简单。</p>
        <p></p>
    </div>
    <div>
        <h4 class="b">二、公众号接入</h4>
        <p>   我要如何把自己的微信公众号接入平台呢？通过下面的接入说明，您将可以很轻松的完成这一工作。</p>
        <p>   *请参考《小鹅通平台接入指引》 <a href="/help" >点击浏览</a> </p>

    </div>
    <div>
        <h4 class="b">三、内容部署</h4>
    </div>
    <div>
        <p>怎样部署我的内容？常用组织方式通常时以建立专栏分类发布、单个发布。系统的课程内容或者主题内容适合建立专栏的形式分类发布，灵活自由的内容则适合单个发布。
        </p>
        <p class="po"></p>
        <p>1、我要怎样建立内容专栏</p>
        <p class="po"></p>
        <p class="tn">通过【新建】页，或者内容列表页的 专栏管理“新增专栏”，创建内容专栏</p>
        <p><img src="../images/help/h_packagelist.png" alt="专栏" /> </p>
        <p></p>
        <p class="po"></p>
        <p>2、如何部署付费内容</p>
        <p class="po"></p>
        <p class="tn">通过【新增】页或者内容管理页按钮，创建图文、音频、视频等内容</p>
        <p><img src="../images/help/h_createcontent.png" alt="专栏" /> </p>
        <p></p>
        <p>新建音频付费内容示例：</p>
        <p><img src="../images/help/h_createdemo.png" alt="新建内容" /> </p>
        <p></p>
        <p></p>
    </div>
    <div>
        <h4 class="b">四、上线前的准备工作</h4>
    </div>
    <div>
        <p class="tn">正式上线发布前，商户需要做好一些如下的准备工作。</p>
        <p class="po"></p>
        <p>1、轮播图设计部署</p>
        <p class="tn">通过管理台【轮播图】功能区，创建的轮播图在首页顶部进行展示。</p>
        <p><img src="../images/help/h_banners.png" alt="轮播图" /> </p>
        <p>*轮播图可以设置跳转到商户自己的部署内容如音频、视频、图文、专栏等，也可以自由设定外部链接，或者不做跳转，仅作为宣传图片展示。</p>
        <p>·轮播图效果实例展示：</p>
        <div>
            <div style="float: left; width: 35%">
                <img src="../images/help/h_banner1.png" style="width: 100%" />
            </div>
            <div style="float: left; width: 35%">
                <img src="../images/help/h_banner2.png" style="width: 100%" />
            </div>
        </div>
        <div style="clear: both"></div>
        <p></p>
        <p>2、关联公众号菜单，设置付费内容入口</p>
        <p class="po"></p>
        <p class="tn">商户可以进入微信公众号管理后台，设置相应的菜单。绑定入口网址：</p>
        <p class="ei"> <span class="red">$APPID</span>.h5.xiaoe-tech.com  （<span class="red">$APPID</span>替换为微信公众号APPID）</p>
        <p class="po"></p>
        <p class="tn">菜单一经设置成功，通过该菜单，您的微信关注用户，便可以方便阅览、分享页面到朋友圈和好友。</p>
        <p>公众号菜单入口设置效果实例展示：</p>
        <div>
            <div style="float: left; width: 35%">
                <img src="../images/help/h_wxmenu1.png" style="width: 100%" />
            </div>
            <div style="float: left; width: 35%">
                <img src="../images/help/h_wxmenu2.png" style="width: 100%" />
            </div>
        </div>
        <div style="clear: both"></div>
        <p></p>
        <p></p>
    </div>

    <div>
        <div id="hp2"></div>
        <div style="clear: both"></div>
        <h3 class="tc b">运 营 篇</h3><hr />
        <p> 本篇通过内容上线，用户运维，订单管理三个方面，让商户了解如何利用本系统的功能实现运营管理。</p>
    </div>
    <div>
        <h4 class="b">一、内容管理</h4>
    </div>
    <div>
        <p>1、可根据内容特征决定以专栏或单个的形式发布上线。</p>
        <p>2、建议以专栏的形式分类上线付费内容。</p>
        <p>3、专栏内容可以一次性完整上线，也可以先上线部分内容再逐个更新。</p>
        <p><img src="../images/help/h_business.png" alt="应用图示"/></p>
        <p class="po"></p>
        <p>*商户可以通过【内容列表】进行已部署内容的管理工作。</p>
        <p><img src="../images/help/h_content.png" alt="内容图示"/></p>
        <p></p>
    </div>
    <div>
        <h4 class="b">二、用户运维 </h4>
    </div>
    <div>
        <p>1、用户管理</p>
        <p class="tn">    系统上线运营后，商户可以在【用户列表】查看用户详情，给用户单发消息。</p>
        <p class="po"></p>
        <p>用户列表图示：</p>
        <p><img src="../images/help/h_users.png" alt="用户列表图示"/></p>
        <p class="po"></p>
        <p>2、评论管理</p>
        <p>用户浏览订阅内容过程中，可以对该内容进行评论，点赞其他用户的评论。</p>
        <p class="po"></p>
        <p class="tn">商户可以在管理台的【内容评论】功能模块，对所有的用户评论进行管理。
            <br>a. 除了基础的对评论的搜索查询、点击头像查看用户详情的功能；
            <br>b. 对于特定的用户评论，系统提供了设置标识“精选评论”功能；
            <br>c. 对于可能的负面评论，商户可以选择将其进行“隐藏”处理。
        </p>
        <p>内容评论管理图示：</p>
        <p><img src="../images/help/h_comment.png" alt="内容评论图示"/></p>
        <p>内容评论实例图示：</p>
        <div>
            <div style="float: left; width: 30%">
                <img src="../images/help/h_comment1.png" style="width: 100%" />
            </div>
            <div style="float: left; width: 30%">
                <img src="../images/help/h_comment2.png" style="width: 100%" />
            </div>
        </div>
        <div style="clear: both"></div>
        <p class="po"></p>
        <p>3、消息管理</p>
        <p>消息列表页， 群发消息、单发消息、反馈回复等消息记录的统一管理；
            <br>点击“撤回”，可以将已发送的消息撤除，撤回了的消息用户不会看到。
        </p>
        <p class="po"></p>
        <p>管理列表体验图示：</p>
        <p><img src="../images/help/message.png" alt="消息管理"/> </p>
        <p class="po"></p>
        <p>点击消息列表页的“推送全员消息”，可实现对所有用户的群发消息推送。</p>
        <p>群发消息推送图示：</p>
        <p><img src="../images/help/h_sendmessage.png" alt="消息管理"/> </p>
        <p class="po"></p>
        <p>4、反馈管理</p>
        <p>用户通过前端用户中心“我的”栏里面的“意见反馈”功能，向系统发送用户的反馈意见。
            <br>所有反馈信息可以进入管理台的“反馈列表”进行处理。</p>
        <p class="po"></p>
        <p>体验图示：</p>
        <p><img src="../images/help/h_feedback.png" alt="反馈列表"/> </p>
        <p class="po"></p>
        <p>如需回复反馈用户，只需点击右侧操作列“发消息”按钮进行操作。</p>
        <p>点击反馈列表中的用户头像，即可查看用户详情（点击详情页左上的“发消息”按钮也可回复用户）。</p>
        <p class="po"></p>
        <p>用户反馈消息回复示例：</p>
        <p><img src="../images/help/h_feedbackto.png" alt="反馈回复"/> </p>
        <p></p>
    </div>
    <div>
        <div style="clear: both"></div>
        <h4 class="b">三、订单管理</h4>
    </div>
    <div>
        <p>我的用户的付费订阅，在哪里查看管理？</p>
        <p>不用急，管理台的【财务管理】提供付费用户的订单列表浏览，订单搜索，以及收入统计等功能。
        </p>
        <p>订单管理图示：</p>
        <p><img src="../images/help/h_purchase.png" alt="订单管理图示" /></p>
        <p></p>
        <p>* 点击用户头像可查看用户详细信息。</p>
        <p class="po"></p>
        <p>*运营参考：</p>
        <p>部分商户会提供特制免费体验内容，上线提供给用户免费体验，以吸引用户关注、达成用户付费订阅。</p>
        <p></p>
    </div>

    <div>
        <div id="hp3"></div>
        <div style="clear: both"></div>
        <h3 class="tc b">进 阶 篇</h3><hr />
        <p> 本进阶篇介绍系统提供的渠道、邀请、数据分析功能。<br />
            商户接入小鹅通系统后，完成上线进行到运营阶段，可以充分利用系统的这些进阶功能，助力发展付费订阅用户，增粉增收。
        </p>
    </div>
    <div>
        <h4 class="b">一、渠道推广</h4>
    </div>
    <div>
        <p>功能服务：实现多种渠道的付费内容推广，实现付费用户及内容订阅的增长。</p>
        <p>系统提供的【渠道分销】管理模块，商户可以利用平台发布的内容建立分发渠道，将设置渠道生成的推广链接部署到相应的渠道中去。</p>
        <p></p>
        <p>通过渠道部署过来的用户数据系统将会自动识别、统计，得以实现对渠道效益的分析。</p>
        <p></p>
        <p>新增渠道图示：</p>
        <p><img src="../images/help/h_channels.png" alt="新增渠道图示"/></p>
        <p></p>
    </div>
    <div>
        <h4 class="b">二、邀请码</h4>
        <p>商户可以利用系统提供的【邀请码】功能，批量生成邀请码。</p>
        <p class="po"></p>
    </div>
    <div>
        <p>邀请码可以通过链接，二维码，印制卡片等形态，以免单、折扣等形式，通过赠与、限售等形式进行推广，发展订阅用户。</p>
        <p> 邀请码生成图示：</p>
        <p><img src="../images/help/h_invecode.png" alt="邀请码生成图示"/></p>
        <p class="po"></p>
        <p>邀请码管理图示：</p>
        <p><img src="../images/help/h_invecodelist.png" alt="邀请码管理图示"/></p>
    </div>
    <div>
        <h4 class="b">三、数据分析</h4>
    </div>
    <div>
        <p>1、用户及付费情况分析</p>
        <p class="po"></p>
        <p class="tn">    管理台【仪表盘】提供可视化的用户增长变化统计，用户付费与收入统计、图表分析。商户可以通过小鹅通官网，导航栏“立即体验”通道进入，通过体验账号进行体验。
        </p>
        <p class="po"></p>
        <p id="hp4_category">2、渠道来源情况分析</p> {{--id  指向首页分类导航教程 --}}
        <p class="po"></p>
        <p class="tn">    管理台【渠道分销】根据开通渠道及其的浏览量、开通量等数据进行分析，商户可以强化优质渠道，探索新的来源渠道，优化渠道开设，提高渠道效用。
        </p>
        <p class="po"></p>
        <p></p>
    </div>

    <div>
        <h4 class="b">四、首页分类导航教程</h4>
    </div>
    <div>
        <p>1、开通小鹅通“成长版”后，可使用首页分类导航功能，您可以自定义设置一个导航栏，将专栏分类。</p>
        <p><img src="../images/help/h_category_1.png"></p>
        <b><h4>2、设置方法：</h4></b>
        <p>在管理台选择【手机预览】>【功能管理】，点击首页分类导航右侧的编辑按钮</p>
        <p><img src="../images/help/h_category_2.png"></p>
        <p>在分类编辑页面中填写分类信息。</p>
        <p><img src="../images/help/h_category_3.png"></p>
        <p>在系统图标库中选择分类图标，选好后点击确定。</p>
        <p><img src="../images/help/h_category_4.png"></p>
        <p>信息填写完毕后点击【保存】。</p>
        <p>注意：四个分类全部配置完成后才可成功保存，保存成功后即可点击开启首页分类导航功能。</p>
        <p><img src="../images/help/h_category_5.png"></p>
        <p>开启首页分类导航功能后，即可在新建/编辑专栏时选择专栏的所属分类</p>
        <p><img src="../images/help/h_category_6.png"></p>
        <p>设置完成后即可在手机页面的分类中查看到对应的专栏。</p>
        <p><img class="phonePreview" src="../images/help/h_category_7.png"></p>
    </div>

    <div>
        <div id="hp5_wx_service"></div>
        <div style="clear: both"></div>
        <h4 class="b">五、服务号通知教程</h4>
    </div>
    <div>
        <p>1、您可以在内容上架时通过服务号向订阅了该内容的用户发送模板消息。</p>
        <p>(只有服务号具有发送模板消息的功能，才可以正常使用服务号通知。这里的服务号指的是您接入小鹅通的服务号。)</p>
        <p><img class="phonePreview" src="../images/help/h_service_10.png"></p>
        <b><h4>2、设置服务号通知：</h4></b>
        <p>
            <span>1.登录服务号的公众平台：</span>
            <a class="red_link" href="https://mp.weixin.qq.com/" target="_blank">https://mp.weixin.qq.com/</a>
        </p>
        <p>点击左侧菜单栏的<span class="red_word">“模板消息”</span>。</p>
        <p><img src="../images/help/h_service_2.png"></p>
        <p>2.在“模板库”中将所在行业设置为<span class="red_word">“教育 培训”</span>。</p>
        <p><img src="../images/help/h_service_3.png"></p>
        <p id="hp5_alive_prompt">3.您在小鹅通管理台创建内容的时候，可以看到“服务号通知”选项，开启后用户即可在内容上架时收到提醒。</p>
    </div>

    <div>
        <h4 class="b">六、开课提醒教程</h4>
    </div>
    <div>
        <p>1、您可以在直播开始前通过服务号向订阅了该直播的用户发送模板消息。</p>
        <p>(只有服务号具有发送模板消息的功能，才可以正常使用服务号通知。这里的服务号指的是您接入小鹅通的服务号。)</p>
        <p><img class="phonePreview" src="../images/help/h_alive_prompt_1.png"></p>
        <b><h4>2、设置模板消息：</h4></b>
        <p>
            <span>1.登录服务号的公众平台：</span>
            <a class="red_link" href="https://mp.weixin.qq.com/" target="_blank">https://mp.weixin.qq.com/</a>
        </p>
        <p>点击左侧菜单栏的<span class="red_word">“模板消息”</span>。</p>
        <p><img src="../images/help/h_alive_prompt_2.png"></p>
        <p>2.在“模板库”中将所在行业设置为<span class="red_word">“教育 培训”</span>。</p>
        <p><img src="../images/help/h_alive_prompt_3.png"></p>
        <p>3.您在小鹅通管理台创建直播的时候，可以看到“开课提醒”选项，选择提醒时间后，用户即可在设定的时间收到提醒。</p>
        <p id="hp5_day_sign"><img src="../images/help/h_alive_prompt_4.png"></p>
    </div>

    <div>
        <h4 class="b">七、日签使用教程</h4>
    </div>
    <div>
        <p>
            “<span class="red_word">专业版</span>”
            用户的音频内容详情页上，可以看到“<span class="red_word">签</span>”字样的日签功能按钮。这就是有助于提高用户日活跃度的日签功能。
        </p>
        <p></p>
        <p>付费用户收听音频时，点击该按钮点击会弹出日签图片，订阅的用户可以将图片发送给朋友或者保存后分享到朋友圈。可以每天生成不同的日签后鼓励用户连续学习和分享，形成用户习惯，提高粉丝黏性，也可基于日前功能使用一些激励手段来提升粉丝传播度与活性。</p>
        <p><img src="../images/help/h_day_sign_1.png"></p>
        <p>
            分享至好友或朋友圈的日签图片上，会有一个与内容相关自动生成的<span class="red_word">二维码</span>。还未转化为付费用户的粉丝可以通过长按识别日签图片中的二维码，跳转至会员/专栏的介绍页进行<span class="red_word">付费购买</span>，借助用户关系发展新的付费用户。
        </p>
        <br>
        <p>
            <h4>设置方法：</h4>
        </p>
        <br>
        <p>
            在新增音频时，在“<span class="red_word">日签（可选）</span>”中上传日签图片即可
        </p>
        <p>注：日签图片需为二维码预留出空白区域，二维码为系统自动生成，不需额外添加。</p>
        <p><img src="../images/help/h_day_sign_2.png"></p>
        <p>
            填写完其他信息后点击“<span class="red_word">保存</span>”。
        </p>

        <br>
        <div>
            <h4 class="b" id="invitation">八、邀请卡</h4>
        </div>
        <br>
        <h4>邀请卡推广教程</h4>

        <p>用户可通过分享邀请卡的方式，邀请好友订阅内容，好友成功开通后，邀请者可获得相应奖励。奖励比例在小鹅通管理台设置，奖励金用户可在手机端页面的【我的收益】中提现。</p>
        <p><img src="../images/help/invitation/pic_1.png"></p>
        <h4>管理台配置</h4>
        <p>第一步：点击左侧菜单【营销中心】，点击【邀请卡】进入邀请卡设置页面</p>
        <p><img src="../images/help/invitation/pic_2.png"></p>
        <p>第二步：找到想要推广的商品，点击设置</p>
        <p><img src="../images/help/invitation/pic_3.png"></p>
        <p>第三步：设置好奖励比例（例如输入数字20），并阅读相关说明后点击【确定】</p>
        <p><img src="../images/help/invitation/pic_4.png"></p>
        <p>注：
        <p>1.该值为邀请方的分成比例，被邀请方通过邀请卡购买商品，邀请方即可获得相应收益。</p>
        <p>2.您的平台收益自动进入【个人模式收入】中，您可在【财务管理】>【个人模式收入】中查看到该类型订单的收益，并提现至您的微信账户中。</p>
        <p><img src="../images/help/invitation/pic_5.png"></p>
        <p>手机端使用流程（以直播为例）</p>
        <p>第一步：用户可在直播间的如下位置获得专属邀请卡</p>
        <p><img src="../images/help/invitation/pic_6.png"></p>
        <p>第二步：长按保存邀请卡，转发给至好友或朋友圈，好友通过邀请卡成功购买直播后，邀请方即可获得相应收益</p>
        <p><img src="../images/help/invitation/pic_7.png"></p>
        <p>第三步：用户可在【我的收益】中对所获收益进行提现</p>
        <p><img src="../images/help/invitation/pic_8.png"></p>


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