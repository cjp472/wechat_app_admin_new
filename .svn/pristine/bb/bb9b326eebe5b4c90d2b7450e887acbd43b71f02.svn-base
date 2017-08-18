<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';

$tabData = ['tabTitle'=>'miniSetting', 'model'=>'company'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountSetting/mini/guide.css?{{env('timestamp')}}"/>
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/accountSetting/mini/guide.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{--公众号设置--}}
    <div class="content" >

        <div class="topBox">
            <a href="/mini/index">独立小程序</a>
            &gt;
            小程序配置
        </div>
        
        {{--dump($auth)--}}
        <input id="app_id" type="hidden" value="{{$app_id}}">
        <input id="auth" type="hidden" value="{{$auth}}">
        <div class="contentTip">
            {{--<div class="msgBox">
                <div class="msgTitle"><span>当 前 状 态</span> :</div>
                <div class="msgInfo">未开通</div>
            </div>--}}
            <div class="contentH4">马上授权小程序给小鹅通吧：</div>
            
            <div class="contentText">如果您已经有一个“微信开放平台账号”，创建了一个“小程序”账号，同时将“服务号”和“需要关联的小程序”分别关联至微信开放平台，您可以选择在此将已经关联至开放平台的小程序授权给小鹅通，小鹅通将自动为您生成涵盖售卖内容的小程序，您的用户可以通过小程序访问购买和订阅您的内容。同时您的小程序有独立的入口展示。
            </div>
            <div class="contentBottomText">
                申请微信开放平台账号请参阅此教程
                <a class="changeModel" target="_blank" href="/help_document?id=d_591c3a1f9a8d4_h0kkmPsy">微信开放平台申请教程</a>
            </div>
            <div class="contentBottomText">
                将服务号和小程序关联至微信开放平台请参与此教程
                <a class="changeModel" target="_blank" href="/help_document?id=d_591580aedb6ed_ZBMk578Q">开通指引</a>
            </div>
            
            <div id="bindNow">已完成上述配置，立即授权小程序</div>
        </div>
    </div>
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

                <div class="modal-body" style="text-align:center;height: 70px;line-height: 40px;">
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
