<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/admin/h5SettingEdit.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    {{--配置--}}
    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    {{--复制文本到剪贴板--}}
    <script type="text/javascript" src="../js/external/jquery.zclip.min.js?{{env('timestamp')}}"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
    {{--文件上传到本地服务器--}}
    <script type="text/javascript" src="sdk/uploadfive/jquery.uploadifive.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/editpayinfo.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')

    <div class="content" style="position: relative;">
        <div class="contentTitle">公众号授权信息</div>
        <div class="infoArea">
            <div class="infoLabel">微信公众号:</div>
            <div class="infoValue">{{$h5->wx_app_name}}</div>
        </div>
        <div class="infoArea">
            <div class="infoLabel">微信账号类型:</div>
            <div class="infoValue">
                已认证服务号&nbsp;&nbsp;|&nbsp;&nbsp;账号已升级？ 点此：<a id="reAuth">重新授权</a>
            </div>
        </div>
        <div class="infoArea">
            <div class="infoLabel">手机访问页面:</div>
            <div class="infoValue" style="color:#337ab7;">{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}/homepage</div>
            <button type="button" class="copyButton">复制</button>
        </div>
        <div id="qrcodeArea">
            <div class="codeText">手机预览二维码：</div>
            <div id="h5qrcode"></div>
        </div>
    </div>

    <div class="content" style="margin-top:10px; ">
        <div class="contentTitle">
            支付设置<img src="../images/icon_edit.png" id="editImg" alt="编辑" title="编辑"/>
        </div>
        <div class="contentH4">您已绑定“认证服务号”，且已向微信申请开通“微信支付权限”</div>
        <div class="contentTip">您可以在此配置，使用自己的微信支付。货款直接进入您的微信支付对应的财付通账号。微信将收取每笔0.6%的交易手续费。</div>
        <div class="infoArea" style="margin-top: 30px;">
            <div class="infoLabel">微信商户号：</div>
            <input type="text" class="form-control long  bigFont"
                   id="wx_mchid" value=""/>
            <a target="_blank" href="/help#hp3" style="font-size: 12px;">如何获取商户号？</a>
        </div>
        <div class="infoArea">
            <div class="infoLabel">微信商户API密钥：</div>
            <input type="text" class="form-control long  bigFont"
                   id="wx_mchkey" value=""/>
            <a target="_blank" href="/help#hp3" style="font-size: 12px;">如何获取商户密钥？</a>
        </div>
        <div class="infoArea">
            <div class="infoLabel">支付授权目录：</div>
            <div class="infoValue" style="color:#337ab7;padding-left: 10px;">{{$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}/content_page/</div>
            <button type="button" class="copyButton">复制</button>
            <a target="_blank" href="/help#hp4" style="font-size: 12px;margin-left: 30px;">如何配置支付授权目录？</a>
        </div>
        <button type="button" id="saveMer" class="show">保存</button>
    </div>

    {{--<div class="content" style="margin-top:10px; ">--}}
        {{--<div class="contentTitle">公众号配置设置</div>--}}
        {{--<div class="contentH4">微信公众号后台配置</div>--}}
        {{--<div class="contentTip">需要登录微信公众平台将以下URL配置到对应位置</div>--}}
        {{--<div class="infoArea" style="margin-top: 30px;">--}}
            {{--@if(empty($h5->wx_bus_verify_txt))--}}
                {{--<div class="infoLabel">业务域名文件：</div>--}}
                {{--<input type="file" id="wx_bus_verify_txt" />--}}
            {{--@else--}}
                {{--<div class="infoLabel">业务域名文件：</div>--}}
                {{--<div class="infoValue" style="margin-right: 20px;">{{$h5->wx_bus_verify_txt}}</div>--}}
                {{--<input type="file" id="wx_bus_verify_txt" />--}}
            {{--@endif--}}
        {{--</div>--}}
        {{--<div class="infoArea">--}}
            {{--<div class="infoLabel">业务域名：</div>--}}
            {{--<div class="infoValue" style="color:#337ab7;">{{$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}</div>--}}
            {{--<button type="button" class="copyButton">复制</button>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="content" style="border-top:1px solid #f2f2f2;">--}}
        {{--<div class="contentTip">开发配置指南</div>--}}
        {{--<div class="bottomTip" >1、请确保已经开通公众号微信支付--}}
            {{--<a target="_blank" href="https://mp.weixin.qq.com">https://mp.weixin.qq.com（查看配置说明）</a>--}}
        {{--</div>--}}
        {{--<div class="bottomTip">2、支付授权目录：微信支付—开发配置—公众号支付—支付授权目录；</div>--}}
        {{--<div class="bottomTip">3、业务域名：公众号设置—功能配置—业务域名</div>--}}
    {{--</div>--}}

    <input type="hidden" id="xcx_app_id" value="{{session("app_id")}}" />
@stop


@section('base_modal')
    {{--点击授权--}}
    <div class="modal fade" id="bindModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 480px;height: 198px;border-radius: 10px;margin: 0 auto;margin-top: 200px;">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">提示</h4>
                </div>

                <div class="modal-body" style="height: 70px;line-height: 40px;">
                    请在新窗口中完成微信公众号授权！<a target="_blank" href="/help#hp2">查看授权教程</a>
                </div>

                <div class="modal-footer">
                    <button type="button" id="bindSuccess">授权成功</button>
                    <button type="button" id="bindFail">授权失败，重试</button>
                </div>
            </div>
        </div>
    </div>
@stop

