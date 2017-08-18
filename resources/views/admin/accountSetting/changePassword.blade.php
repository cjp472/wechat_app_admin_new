<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
$tabData = ['tabTitle'=>'modelSetting', 'model'=>'company'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" href="/css/external/jquery-alert.css"/>
    <link rel="stylesheet" href="/css/admin/accountSetting/changePassword.css"/>
@endsection

@section('page_js')
    {{--格式验证--}}
    <script type="text/javascript" src="/js/admin/utils/formCheck.js"></script>
    {{--弹出框--}}
    <script type="text/javascript" src="/js/external/jquery-alert.js"></script>
    <script type="text/javascript" src="/js/admin/accountSetting/changePassword.js"></script>
@endsection


@section('base_mainContent')
     <div class="Header">
         <a href="/accountmanage">账号设置</a>
         <span>&nbsp;>&nbsp;找回密码</span>
     </div>
    <div class="content">
        <div class="contentHeader">
            找回密码
        </div>
        <div class="contentPart">
            <div class="contentSection">
                <div class="sectionTitle">手机号码</div>
                <div class="sectionContent"><span id="phone">{{$phone}}</span></div>
            </div>
            <div class="contentSection">
                <div class="sectionTitle">验证码</div>
                <div class="sectionContent">
                    <input type="text" class="inputDefault identifyCodeInput" placeholder="请输入验证码"/>
                    <img src="/images/admin/icon-successful.svg" class="successTip"/>
                    <img src="/images/admin/icon-error.svg" class="errorTip"/>
                    <div class="btnMid getCodeBtn">
                        <span>获取验证码</span>
                    </div>
                </div>
            </div>
            <div class="contentSection">
                <div class="sectionTitle">新密码</div>
                <div class="sectionContent">
                    <input type="password" class="inputDefault passwordInput" placeholder="请输入新的登录密码">
                    <div class="errorMsgFirst" style="display: none">6-16位字符可包含数字，字母（区分大小写）</div>
                </div>
            </div>
            <div class="contentSection">
                <div class="sectionTitle">再次输入密码</div>
                <div class="sectionContent">
                    <input type="password" class="inputDefault passwordInputAgain" placeholder="请再次输入登录密码">
                    <div class="errorMsgSecond" style="display: none">两次密码输入不相同</div>
                </div>
            </div>
        </div>

        <div class="btnMid btnBlue confirmBtn">保存</div>
        <div class="btnMid xeBtnDefault cancelBtn">取消</div>
    </div>
@stop

@section('base_modal')

@stop