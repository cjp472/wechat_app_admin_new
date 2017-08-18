<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    {{--meta标签--}}
    @include("headSetting.head_meta")
    {{--设置rem的基础font-size--}}
    @include("headSetting.set_htmlFontSize")

    {{--网页标题，必填--}}
    <title>分销内容</title>

    {{--初始化css--}}
    @include("publicSource.publicCss")
    {{--初始化js--}}
    @include("publicSource.publicJs")

    {{--页面的css--}}
    <link rel="stylesheet" href="css/admin_H5/submitSuccess.css?{{env('timestamp')}}">

    {{--分销主页js逻辑--}}
    <script src="js/admin_H5/submitSuccess.js?{{env('timestamp')}}"></script>

</head>
<body>
    <div class="body_page">
        <div class="success_icon_wrapper">
            <img class="success_icon" src="/images/submit_success.png">
        </div>
        <div class="paragraph_1 t7">提交成功</div>
        <div class="paragraph_2 c3">等待审核通过后，即可获得分销链接</div>

        <div class="bottom_footer">
            <div class="paragraph_3">小鹅通官网</div>
            <div class="paragraph_4 c3">Copyright © 2016-<?php echo date('Y')?> xiaoe-tech.com</div>

        </div>

    </div>

</body>
</html>