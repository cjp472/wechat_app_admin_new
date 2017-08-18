<?php
$pageData = [];
$pageData['sideActive'] = 'money_admin';
$pageData['barTitle'] = '财务管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/bindWxAccount.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/bindWxAccount.js?{{env('timestamp')}}"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
    <script>
        qt_http = "http://{{env('DOMAIN_DUAN_NAME')}}/platform/bind_account/";
    </script>
@endsection

@section('base_title')
    {{--<span style="font-size: 18px">财务管理</span>--}}
@stop

@section('base_mainContent')
    <div class="payTitle">
        <ul>
            <span class="sub_title"><a href="/bind_wx_account_page" >绑定到账微信账号</a></span>
        </ul>
    </div>

    <div style="width: 100%;height: 200px;">
        <div class="bindWx">
            <span style="font-size: 10px;">提现前需先绑定到账微信账号(到账微信账号一经绑定不可更改)。微信实名认证后每日能提现上限2万,未认证每日提现上限2000元!</span>
            <a target="_blank" style="font-size: 10px; cursor:pointer;" href="/help/explainDoc#hp1">如何实名认证?</a>
        </div>
        <div id="bind_wx_account" class="weixin_zhanghao">
            <div id="bind_wxaccount" data-app_id="{{$app_id}} " data-bind_account_wx_id="" onclick="ShowQRCode()" class="btn_bindwx" >绑定微信</div>
        </div>
    </div>

    <div class="smscode_c">
        <span class="get_smscode">获取验证码</span>
        <p class="code_extra">验证码将发送到管理员绑定的手机:{{$phone}}, 请注意查收</p>
        <div class="code_num">
            <span class="code_xin">*</span>
            <span class="code_desc">验证码</span>
            <input type="text" class="form-control code_input" placeholder="请输入验证码" aria-label="..." id="sms_code">
            <button  class="get_code_class btn btn-default" onclick="sendsms()" id="get_sms_code">获取验证码</button>
        </div>
        <div class="bind_confirm">
            <div id="period_week" onclick="confirm_bind_wx()" class="btn_bindwx" >确认绑定</div>
        </div>
    </div>
@stop

@section('base_modal')
    {{--发消息--}}
    <div class="modal fade" id="ExportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 500px;margin-top: 100px;">
            <div class="modal-content" style="height: 400px;width: 500px;padding-left: 10px;padding-right: 10px;">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">微信授权</span></div>
                </div>

                <div class="modal-body qrcode_class" style="height: 100px;width: 300px;float: left;">
                    {{--二维码--}}
                    <div id="qr_code" style="margin-bottom: 5px"></div>
                    <span class="beizhu">请使用到账微信扫描二维码授权!</span>
                </div>
            </div>
        </div>
    </div>
@stop