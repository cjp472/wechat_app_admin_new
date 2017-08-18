{{--资费版本选择  /update_version_page--}}
<html>
<head>
    <meta charset="utf-8">
    <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    <link rel='icon' href='logo-64.ico' type='image/x-ico'/>
    <link type=text/css rel="stylesheet" href="../css/external/materialize.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/admin/base.css?{{env('timestamp')}}">
    <link type="text/css" rel="stylesheet" href="../css/admin/updateVersionPage.css?{{env('timestamp')}}">
    <script type="text/javascript" src="../js/external/jquery.js"></script>
</head>
<body>
{{--导航栏头部--}}
<div class="header">
    <div class="headerPic">
        <img src="images/xiaoeTC_logo.png" alt="小鹅通图标">
    </div>
    <div class="user">
        <div class="userLogo">
            <img src="@if(session('wx_share_image')){{ session('wx_share_image') }}@else{{ session('avatar') }}@endif"/>
        </div>
        <div class="userName">@if(session('wx_app_name')){{ session('wx_app_name') }}@else{{ session('nick_name') }}@endif</div>
    </div>
    <div class="returnBtn" onclick="window.location.href='/accountview'">返回管理台</div>
</div>
{{--版本选择--}}
@if(!empty(session("version_type")))
    <div class="levSelect lev1Select">
        {{--版本一--}}
        <div class="levSelectPart levSelect1">
            <div class="lpTitle lpTitle1">
                基础版
            </div>
            <div class="versionPrice">
                <p>永久免费</p>
                <p></p>
            </div>
            <div class="giftFlow">
                开户即赠送价值50元的流量包
            </div>
            <div class="giftEqual">
                相当于：5分钟音频完整收听3.5万次， 30分钟视频完整观看350次
            </div>
            {{--根据浏览器获取的session改变按钮的不同状态--}}
            <div class="buttonClick buttonClick1 buttonClickGray">
                <p>
                    @if(session("version_type") == 1)当前版本@endif
                    @if(session("version_type") == 2 ||session("version_type") == 3)基础版@endif
                </p>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    付费内容载体：
                </div>
                <div class="levContentC">
                    <span>付费图文、付费音频、付费视频</span>
                    <br/>
                    <span>付费直播（语音）</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    付费形式：
                </div>
                <div class="levContentC">
                    <span>单项售卖、专栏售卖</span>
                    <br/>
                    <span>专栏外单品售卖</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    运营管理：
                </div>
                <div class="levContentC">
                    <span>内容管理、用户关系管理</span>
                    <br/>
                    <span>财务管理，账户管理</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    数据分析：
                </div>
                <div class="levContentC">
                    <span>营收趋势分析、用户活跃度分析</span>
                    <br/>
                    <span>用户增长分析</span>
                </div>
            </div>
            <div class="levContentP1">
                <div class="levContentPTitle levContentC1Active">
                    特权功能及服务：
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>免费码
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>渠道分销
                </div>
                <div class="levContentC1">用户定向推送</div>
                <div class="levContentC1">赠送好友</div>
                <div class="levContentC1">试听分享</div>
                <div class="levContentC1">首页分类导航</div>
                <div class="levContentC1">会员(按时长付费)</div>
                <div class="levContentC1">首页名称自定义</div>
                <div class="levContentC1">日签分享</div>
                <div class="levContentC1">视频播放+语音互动</div>
                <div class="levContentC1">新功能首发试用</div>
                <div class="levContentC1">定期回访</div>
                <div class="levContentC1">运营数据分析+建议</div>
            </div>
        </div>
        {{--版本二--}}
        <div class="levSelectPart levSelect2">
            <div class="lpTitle lpTitle2">
                成长版
            </div>
            <div class="versionPrice">
                <p>年费：实际营收总额*1%/年</p>
                <p>（不超过4500元/年）</p>
            </div>
            <div class="giftFlow">
                开户即赠送价值100元的流量包
            </div>
            <div class="giftEqual">
                相当于：5分钟音频完整收听7万次， 30分钟视频完整观看700次
            </div>
            {{--根据浏览器获取的session改变按钮的不同状态--}}
            @if(session("version_type") == 1)
            <div class="buttonClick buttonClick2" onclick="window.location='/open_growUp_version_page'">
                <p>
                    立即开通
                </p>
            </div>
            @endif
            @if(session("version_type") == 2)
                <div class="buttonClick buttonClick2 buttonClickGray">
                    <p>
                        当前版本
                    </p>
                </div>
            @endif
            @if(session("version_type") == 3)
                <div class="buttonClick buttonClick2 buttonClickGray">
                    <p>
                        成长版
                    </p>
                </div>
            @endif
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    付费内容载体：
                </div>
                <div class="levContentC">
                    <span>付费图文、付费音频、付费视频</span>
                    <br/>
                    <span>付费直播（语音）</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    付费形式：
                </div>
                <div class="levContentC">
                    <span>单项售卖、专栏售卖</span>
                    <br/>
                    <span>专栏外单品售卖</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    运营管理：
                </div>
                <div class="levContentC">
                    <span>内容管理、用户关系管理</span>
                    <br/>
                    <span>财务管理，账户管理</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    数据分析：
                </div>
                <div class="levContentC">
                    <span>营收趋势分析、用户活跃度分析</span>
                    <br/>
                    <span>用户增长分析</span>
                </div>
            </div>
            <div class="levContentP1">
                <div class="levContentPTitle levContentC1Active">
                    特权功能及服务：
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>免费码
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>渠道分销
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>用户定向推送</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>赠送好友</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>试听分享</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>首页分类导航</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>会员(按时长付费)</div>
                <div class="levContentC1">首页名称自定义</div>
                <div class="levContentC1">日签分享</div>
                <div class="levContentC1">视频播放+语音互动</div>
                <div class="levContentC1">新功能首发试用</div>
                <div class="levContentC1">定期回访</div>
                <div class="levContentC1">运营数据分析+建议</div>
                <div class="levContentC1More">更多功能持续更新中</div>
            </div>
        </div>
        {{--版本三--}}
        <div class="levSelectPart levSelect3">
            <div class="lpTitle lpTitle3">
                专业版
            </div>
            <div class="versionPrice">
                <p>年费：4800元/年</p>
                <p></p>
            </div>
            <div class="giftFlow">
                开户即赠送价值500元的流量包
            </div>
            <div class="giftEqual">
                相当于：5分钟音频完整收听35万次， 30分钟视频完整观看3500次
            </div>
            {{--根据浏览器获取的session改变按钮的不同状态--}}
            @if(session("version_type") == 1)
                <div class="buttonClick buttonClick3" onclick="window.location='/open_vip_version_page'">
                    <p>
                        立即开通
                    </p>
                </div>
            @endif
            @if(session("version_type") == 2)
                <div class="buttonClick buttonClick3" onclick="window.location='/open_vip_version_page'">
                    <p>
                        立即开通
                    </p>
                </div>
            @endif
            @if(session("version_type") == 3)
                <div class="buttonClick buttonClick3 buttonClickGray">
                    <p>
                        当前版本
                    </p>
                </div>
            @endif
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle" style="width:100%;height: 20px;line-height: 20px;font-size: 14px;text-align: left;">
                    付费内容载体：
                </div>
                <div class="levContentC">
                    <span>付费图文、付费音频、付费视频</span>
                    <br/>
                    <span>付费直播（语音）</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    付费形式：
                </div>
                <div class="levContentC">
                    <span>单项售卖、专栏售卖</span>
                    <br/>
                    <span>专栏外单品售卖</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    运营管理：
                </div>
                <div class="levContentC">
                    <span>内容管理、用户关系管理</span>
                    <br/>
                    <span>财务管理，账户管理</span>
                </div>
            </div>
            <div class="levContentP">
                <span class="levContentIcon"></span>
                <div class="levContentPTitle">
                    数据分析：
                </div>
                <div class="levContentC">
                    <span>营收趋势分析、用户活跃度分析</span>
                    <br/>
                    <span>用户增长分析</span>
                </div>
            </div>
            <div class="levContentP1">
                <div class="levContentPTitle levContentC1Active">
                    特权功能及服务：
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>免费码
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>渠道分销
                </div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>用户定向推送</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>赠送好友</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>试听分享</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>首页分类导航</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>会员(按时长付费)</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>首页名称自定义</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>日签分享</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>视频播放+语音互动</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>新功能首发试用</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>定期回访</div>
                <div class="levContentC1 levContentC1Active"><span class="levContentIcon"></span>运营数据分析+建议</div>
                <div class="levContentC1More">更多功能持续更新中</div>
            </div>
        </div>
    </div>
@endif
<div class="chargeFooterWords">
    第三方运营商资源费用代收规则：<br>
    包括文件存储于云服务器产生的存储费及用户访问产生的流量费。<br>
    1、为规避用户多次视听导致流量费用不可控制，小鹅通采用“按独立访问用户数量计费”，即单个用户多次访问只计费一次。 <br>
    2、存储时，原始上传的音频/视频/图文，大小不做改变。 流量计费将以压缩/优化后的用户访问文件大小计算。用户访问时，小鹅通会统一将音频按照64kbps转码处理，视频按照720p高清优化处理。图片暂不做任何处理，请自行把握图片大小及流量使用。<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1）存储费：0.03元/G/每天<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2）流量费：用户访问文件大小*当日新增访问用户数*综合流量单价（0.90元/G）<br>
</div>

<script type="text/javascript" src="../js/admin/updateVersionPage.js?201702220524"></script>
</body>
</html>