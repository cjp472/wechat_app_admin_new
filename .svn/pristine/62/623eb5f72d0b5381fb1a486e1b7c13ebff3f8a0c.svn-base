<!DOCTYPE html>
<html>
<head>
    {{--页面字符集--}}
    <meta charset="UTF-8">
    {{--360标签，页面需默认用极速核--}}
    <meta name="renderer" content="webkit">
    {{--使用Chrome内核来做渲染--}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    {{--升级不安全请求，http会被升级到https--}}
    @if($_SERVER['SERVER_PORT'] == env("SSL_PORT"))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>
    @endif
    {{--网页标题--}}
    <title>发票列表</title>
    {{--标签icon--}}
    <link rel='icon' href='/logo-64.ico' type='image/x-ico'/>
    {{------------------------------------引用css------------------------------------------------}}
    {{--Bootstrap v3.3.5--}}
    <link type=text/css rel="stylesheet" href="/css/external/bootstrap.min.css?{{env('timestamp')}}">
    {{--弹窗--}}
    <link rel="stylesheet" href="/css/external/jquery-alert.css?{{env('timestamp')}}"/>
    {{--base.css--}}
    <link type=text/css rel="stylesheet" href="/css/admin/base.css?{{env('timestamp')}}">

    <link type=text/css rel="stylesheet" href="/css/admin/invoice/list.css?{{env('timestamp')}}">

    {{--百度统计--}}
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?081e3681cee6a2749a63db50a17625e2";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>

</head>
<body>

<div id="app" class="content">
    {{--{{dump($data)}}--}}
    <div class="top">
        <a class="btnMid xeBtnDefault pull-left" href="/manage_invoice" >申请发票</a>
    </div>
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>申请日期</th>
                <th>发票抬头</th>
                <th>发票类型</th>
                <th>税  号</th>
                <th>开票金额</th>
                <th>开票类型</th>
                <th>联系人</th>
                <th>快递单号</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $val)
            <tr>
                <td>{{$val->created_at}}</td>
                <td>{{$val->invoice_title}}</td>
                <td>@if($val->invoice_type==1) 普票 @elseif($val->invoice_type==2) 专票 @endif</td>
                <td>{{$val->tax_file_number}}</td>
                <td>{{$val->invoice_amount/100}}</td>
                <td>@if($val->invoice_content==1) 服务费 @elseif($val->invoice_content==2) 软件服务费 @endif</td>
                <td>{{$val->contact}}</td>
                <td>{{$val->express_no}}</td>
                <td @if($val->state==3) class="=red" @endif>
                    @if($val->state==1)
                        待审核
                    @elseif($val->state==3)
                        已拒绝
                    @elseif($val->state==2)
                        待开票
                    @elseif($val->state==4)
                        待邮寄
                    @elseif($val->state==5)
                        已邮寄
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>




    @if(empty($data) || count($data) == 0)

        <div class="singleListNoData">暂无开票数据！</div>

    @endif

    <div class="list-page">

        {!! $data->render() !!}

    </div>
</div>

{{--Loading动画--}}
<div id="base_loading">
    <!-- <img style="width: 150px;height: 150px" id="login_progressImage" src="/images/Loading2.gif"/> -->
    <div class="loadingContent">
        <svg viewBox="25 25 50 50" class="circular">
            <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
        </svg>
        <p class="loadingText">加载中</p>
    </div>
</div>

{{--顶部小框提示--}}
<div id="TopPrompt" class="topPrompt" style="display: none;">
    <div class="topPromptContent"></div>
</div>
{{--提前加载alert里的图片，避免首次弹出闪动--}}
<div class="hide">
    <img src="/images/alert/blue_info_prompt.svg"/>
    <img src="/images/alert/green_info_prompt.svg"/>
    <img src="/images/alert/red_info_prompt.svg"/>
</div>


{{--------------------------------------- 引用js ----------------------------------------------}}
{{--jquery1.12.4--}}
<script type="text/javascript" src="/js/external/jquery1.12.4.min.js"></script>
{{--Bootstrap v3.3.5--}}
<script type="text/javascript" src="/js/external/bootstrap.min.js"></script>
{{--弹窗--}}
<script type="text/javascript" src="/js/external/jquery-alert.js?{{env('timestamp')}}"></script>


<script type="text/javascript" src="/js/admin/invoice/list.js?{{env('timestamp')}}"></script>


</body>

</html>
