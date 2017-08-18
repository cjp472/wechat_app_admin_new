<meta charset="UTF-8">
{{-- 360 使用Google Chrome Frame --}}
<meta name="renderer" content="webkit">
{{-- 优先使用 IE 最新版本和 Chrome --}}
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
{{-- 移动设备—定义页面宽度 --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
{{-- 禁止数字识自动别为电话号码 --}}
<meta name="format-detection" content="telephone=no">
@if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
@endif

