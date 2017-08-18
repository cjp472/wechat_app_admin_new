<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '后台管理系统';
?>

@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/changeAdmin.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/utils/formCheck.js"></script>
    <script type="text/javascript" src="../js/admin/changeAdmin.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    <script type="text/javascript" src="https://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
@endsection

@section('base_mainContent')
    <div class="pageTopTitle">
        <a href="/accountmanage">账号设置</a>
        >更换管理员
    </div>
    <div class="content">
        <div class="titleBox">
            <div id="itemThree" class="stepBox">
                <div class="stepItem">3</div>
                <span>完成更换</span>
            </div>
            <div class="line"></div>
            <div id="itemTwo" class="stepBox">
                <div class="stepItem">2</div>
                <span>新管理员信息</span>
            </div>
            <div class="line"></div>
            <div id="itemOne" class="stepBox finStep">
                <div class="stepItem">1</div>
                <span>新微信号绑定</span>
            </div>
        </div>
        <div class="qrCode">
            <div id="hasBind">当前微信号已被绑定，请更换微信号重新扫码</div>
            <span>请使用新管理员的微信号扫描下方二维码进行绑定</span>
            <div id="change_qrCode"></div>
        </div>

        <div id="step_2" style="display:none">
            <div class="inputBox">
                <span class="leftTitle">管理员<span style="color:red;">*</span></span>
                <input id="linkman" type="text" class="inputDefault" placeholder="请输入管理员名称">
            </div>
            <div class="inputBox">
                <span class="leftTitle">手机号码<span style="color:red;">*</span></span>
                <input id="phone" type="text" class="inputDefault" placeholder="请输入管理员手机号码">
                <div id="phoneBox" class="redMsg">请输入正确的11位数字手机号</div>
            </div>
            <div class="inputBox">
                <span class="leftTitle">验证码<span style="color:red;">*</span></span>
                <input id="current" type="text" class="inputDefault identifyCodeInput" placeholder="请输入验证码">
                <img src="/images/admin/icon-successful.svg" class="successTip"/>
                <img src="/images/admin/icon-error.svg" class="errorTip"/>
                <div class="btnBlue btnMid getCodeBtn">获取验证码</div>
            </div>
            <div class="btnArea">
                <a id="return" href="/changeAdmin?type=1" class="btnMid xeBtnDefault">上一步</a>
                <a id="save" class="btnBlue btnMid">保存</a>
            </div>
        </div>

        <div id="step_3" style="display:none">
            <img id="success" src="/images/admin/icon-successful.svg" alt="">
            <span id="msgTitle">变更成功！</span>
            <p>店铺管理员已成功变更，请使用新绑定的微信号重新登录</p>
            <div class="jump">
                页面<span class="secondJump">3</span>秒后将跳转...
                <a id="jumpNow" href="javascript:void(0)">立即跳转</a>
            </div>
        </div>

    </div>


@stop

@section('base_modal')

    @if($type == 3)
    {{--<div class="leftScreen"></div>--}}
    @endif

@stop