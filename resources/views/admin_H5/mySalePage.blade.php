<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    {{--meta标签--}}
    @include("headSetting.head_meta")
    {{--设置rem的基础font-size--}}
    @include("headSetting.set_htmlFontSize")

    {{--网页标题，必填--}}
    <title>我的分销</title>

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

        <div id="page20" class="page">
            {{--我的分销--}}
            <div id="mySalePage_wrapper">
                {{--<div class="mysale_product apply_ok">--}}
                    {{--<a class="mysale_product_link" href="#">--}}
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
                    {{--</a>--}}
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

                @foreach($saleList as $key => $value)
                    <div class="mysale_product {{$StateClass[$value->state]}}">
                        <a class="mysale_product_link" href="/query_sale_detail?id={{$value->id}}">
                        <div class="mysale_product_desc">
                            <div class="mysale_product_icon_wrapper">
                                <img class="mysale_product_icon" src="{{$value->sale_img_url}}" />
                            </div>
                            <span class="mysale_resource_title">{{$value->sale_content}}</span>
                            <span class="myale_resource_name">{{$value->app_name}} | {{$value->sale_name}}</span>
                            @if($value->state==0)
                            <span class="mysale_product_apply">申请中</span>
                            @elseif($value->state==2)
                            <span class="mysale_product_apply_failed">申请失败</span>
                            @endif
                        </div>
                        @if($value->state==1)
                        <div class="mysale_count">
                            <div class="view_count">
                                <span class="count_title">浏览量</span>
                                <span class="count_num">{{$value->view_count}}</span>
                            </div>
                            <div class="open_count">
                                <span class="count_title">订阅量</span>
                                <span class="count_num">{{$value->open_count}}</span>
                            </div>
                        </div>
                        @endif
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="dropload-down">
                <div class="dropload-refresh t2 c3 drop_text"><span class="loading"></span> 加载中...</div>
                <div class="dropload-noData t2 c3 drop_text hide">已加载完</div>
            </div>
        </div>

    </div>


    @include("admin_H5.bottomTab")


    {{--分销主页js逻辑--}}
    <script src="../../../js/admin_H5/mySalePage.js?20170216"></script>


    {{--所有不需要预先加载的js，都在下面引入--}}

</body>
</html>