<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    {{--meta标签--}}
    @include("headSetting.head_meta")
    {{--设置rem的基础font-size--}}
    @include("headSetting.set_htmlFontSize")

    {{--网页标题，必填--}}
    <title>分销详情</title>

    {{--初始化css--}}
    @include("publicSource.publicCss")
    {{--初始化js--}}
    @include("publicSource.publicJs")

    {{--页面的css--}}
    <link rel="stylesheet" href="../../../css/admin_H5/mySalePage.css?{{env('timestamp')}}">


</head>
<body>


    {{--我的分销页面--}}
    <div class="page_wrapper">

        <div id="page20" class="page pbottom">
            {{--我的分销--}}
            <div id="mySalePage_wrapper">
                <div class="mysale_product_detail">
                    {{--<div class="mysale_product_desc">--}}
                        {{--<div class="mysale_product_icon_wrapper">--}}
                            {{--<img class="mysale_product_icon" src="/images/default.jpg" />--}}
                        {{--</div>--}}
                        {{--<span class="mysale_resource_title">新锐会员</span>--}}
                        {{--<span class="myale_resource_name">吴晓波频道 | 我的朋友圈</span>--}}
                    {{--</div>--}}
                    {{--<div class="mysale_count">--}}
                        {{--<div class="sale_url">https//h5.xiaoeknow.com/</div>--}}
                        {{--<div class="view_count">--}}
                            {{--<span class="count_title">浏览量</span>--}}
                            {{--<span class="count_num">1896</span>--}}
                        {{--</div>--}}
                        {{--<div class="open_count">--}}
                            {{--<span class="count_title">订阅量</span>--}}
                            {{--<span class="count_num">562</span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="mysale_detail">--}}
                    {{--<div class="mysale_detail_content">--}}
                        {{--<div class="detail_title">分销链接</div>--}}
                        {{--<div class="detail_content_url">http://wxd020b599c5733b21.h5.inside.xiaoe-tech.com/homepage...</div>--}}
                    {{--</div>--}}
                    {{--<button class="copyBtn">复制链接</button>--}}
                    {{--<button class="loctoBtn">查看内容</button>--}}
                {{--</div>--}}

                {{--<div class="mysale_product apply_ing">--}}
                    {{--<a class="mysale_product_link" href="#">--}}
                        {{--<div class="mysale_product_desc">--}}
                            {{--<div class="mysale_product_icon_wrapper">--}}
                                {{--<img class="mysale_product_icon" src="/images/default.jpg" />--}}
                            {{--</div>--}}
                            {{--<span class="mysale_resource_title">新锐会员</span>--}}
                            {{--<span class="myale_resource_name">吴晓波频道 | 我的朋友圈</span>--}}
                            {{--<span class="mysale_product_apply">申请中</span>--}}
                        {{--</div>--}}
                    {{--</a>--}}
                {{--</div>--}}
                {{--<div class="mysale_product apply_fail">--}}
                    {{--<a class="mysale_product_link" href="#">--}}
                        {{--<div class="mysale_product_desc">--}}
                            {{--<div class="mysale_product_icon_wrapper">--}}
                                {{--<img class="mysale_product_icon" src="/images/default.jpg" />--}}
                            {{--</div>--}}
                            {{--<span class="mysale_resource_title">新锐会员</span>--}}
                            {{--<span class="myale_resource_name">十点读书 | 我的公众号</span>--}}
                            {{--<span class="mysale_product_apply_failed">申请失败</span>--}}
                        {{--</div>--}}
                    {{--</a>--}}
                {{--</div>--}}

                @if($result)
                    <div class="mysale_product">
                        <div class="mysale_product_desc">
                            <div class="mysale_product_icon_wrapper">
                                <img class="mysale_product_icon" src="{{$result->sale_img_url}}" />
                            </div>
                            <span class="mysale_resource_title">{{$result->sale_content}}</span>
                            <span class="myale_resource_name">{{$result->app_name}} | {{$result->sale_name}}</span>
                            @if($result->state==0)
                            <span class="mysale_product_apply">申请中</span>
                            @elseif($result->state==2)
                            <span class="mysale_product_apply_failed">申请失败</span>
                            @endif
                        </div>
                        @if($result->state==1)
                        <div class="mysale_count">
                            <div class="view_count">
                                <span class="count_title">浏览量</span>
                                <span class="count_num">{{$result->view_count}}</span>
                            </div>
                            <div class="open_count">
                                <span class="count_title">订阅量</span>
                                <span class="count_num">{{$result->open_count}}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($result->state==1)
                    <div class="mysale_detail">
                        <div class="mysale_detail_content">
                            <div class="detail_title">我的分销链接</div>
                            <div class="detail_content_url">
                                <textarea type="text" id="sourceUrl" readonly>{{$result->sale_url}}</textarea>
                            </div>
                            <div class="detail_url_copy">(长按链接复制)</div>
                        </div>

                        <button class="loctoBtn">查看内容</button>
                    </div>
                    @elseif($result->state==2)
                        <div class="mysale_detail">
                            <div class="mysale_detail_content">
                                <div class="detail_title">失败原因</div>
                                <div class="detail_content_remark">
                                    <textarea type="text" class="remarktext" readonly>{{$result->refuse_reason}}</textarea>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mysale_detail">
                            <div class="mysale_detail_content">
                                <div class="detail_content_remark">
                                    <textarea type="text" class="remarktext" readonly>{{$result->remark}}</textarea>
                                    <div class="detail_url_copy">(备注)</div>
                                </div>
                            </div>
                        </div>

                    @endif

                @endif

                <div class="xiaoetong">
                    <div class="domain_name">小鹅通官网</div>
                    <div class="copyright">Copyright © 2016-2017 xiaoe-tech.com</div>
                </div>
            </div>
            <div class="dropload-down">
                <div class="dropload-refresh t2 c3 drop_text"><span class="loading"></span> 加载中...</div>
                <div class="dropload-noData t2 c3 drop_text hide">已加载完</div>
            </div>
        </div>

    </div>



    {{--分销主页js逻辑--}}
    <script src="../../../js/admin_H5/mySalePage.js"></script>


    {{--所有不需要预先加载的js，都在下面引入--}}

</body>
</html>