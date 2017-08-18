<!doctype html>
<html style="height: 100%;background: white">
<head>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    @endif
    <title>小鹅通，基于微信的内容付费工具</title>
    <link rel='icon' href='{{URL::to('/')}}/logo-64.ico' type=‘image/x-ico’ />

    <link type=text/css rel="stylesheet" href="../css/help/style.css?{{env('timestamp')}}">

</head>
<body >
<div class="mobileContent">
    <div class="base_topBar moblie_top">

        <div class="base_explain">
            <span id="toolbar_title"></span>
        </div>

    </div>
    <div class="base_content helpContentMobile">
        <div id="mainContent" class="base_mainContent show">

        </div>
        <iframe src="" id="frameContent" class="hide" frameborder="0" width="100%" height="800px" ></iframe>
    </div>
</div>




<script src="../js/external/jquery.js"></script>
<script type="text/javascript" src="../js/help/index.js?{{env('timestamp')}}"></script>
</body>

</html>
