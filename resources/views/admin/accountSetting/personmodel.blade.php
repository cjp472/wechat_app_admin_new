<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';

$tabData = ['tabTitle'=>'modelSetting', 'model'=>'person'];
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountSetting/personmodel.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    {{--复制文本到剪贴板--}}
    <script type="text/javascript" src="../js/external/clipboard.min.js"></script>
    <script type="text/javascript" src="../js/admin/accountSetting/personmodel.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{--公众号设置--}}
    <div class="content" >
        <ul id="myTabs" class="nav nav-tabs">
            <li class="active">
                <a href="javascript:void(0)">个人模式</a>
            </li>
            <li>
                <a href="/companymodel">企业模式</a>
            </li>

            <a class="modelHelp" href="/helpCenter/problem?document_id=d_58f812aa4d22a_rEc36L5X" target="_blank">运营模式说明</a>
        </ul>
        {{--<div class="contentTitle">运营模式</div>--}}
        {{--<div class="model">
            <span class="nowModel">当前运营模式：个人模式</span>
            <span class="changeModel" id="bindNow">更改运营模式</span>
        </div>--}}
        <div class="contentTip">
            <div class="msgBox">
                <div class="msgTitle"><span>当 前 状 态</span> :</div>
                <div class="msgInfo">已开通</div>
            </div>
            <div class="msgBox">
                <div class="msgTitle"><span>说 明</span> :</div>
                <div class="msgInfo">启用小鹅通个人模式后，您可以通过发起提现申请与小鹅通结算相应货款。</div>
            </div>
            <div class="msgBox">
                <div class="msgTitle"><span>提 现 时 间</span> :</div>
                <div class="msgInfo">当天18点前申请提现，七天内到账，实际到账时间以微信入账时间为准。</div>
            </div>
            <div class="msgBox">
                <div class="msgTitle"><span>交易手续费</span> :</div>
                <div class="msgInfo">
                    <div class="sub_charge_1">小鹅通不收取任何提现手续费；</div>
                    <div class="sub_charge_2">支付机构收取0.6%交易手续费，通过小鹅通完成代缴。</div>
                </div>
            </div>
            {{--@if($info->use_collection==0)--}}
            <div class="personLink" style="margin-top: 10px;">
                <div class="msgTitle"><span style="width:120px">个人模式店铺地址</span> :</div>
                <div class="msgInfo">
                    <span style="color: #337ab7">{{$info->url}}</span>
                    <button type="button" data-clipboard-text="{{$info->url}}" class="copyButton copyHref">复制</button>
                </div>
            </div>
            {{--@endif--}}
        </div>
    </div>

    <input type="hidden" id="xcx_app_id" value="{{session("app_id")}}" />
@stop

@section('base_modal')
    <!-- 点击授权 -->
    <div class="modal fade" id="bindModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content content_mod">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">更改运营模式</h4>
                </div>
                <div class="modal-body">
                    <div class="tishi">请根据您的实际情况, 选择一种运营模式:</div>
                    <hr class="zhixian">
                    <div class="env">情况1: 已开通"认证服务号",且已向微信申请开通"微信支付权限"</div>
                    <div class="extraC">
                        <div class="extra">您可以在此配置,使用自己的微信支付。货款直接进入您的微信支付对应的微信支付商户。微信将收取每笔0.6%的交易手续费。</div>
                    </div>
                    <button class="btn btn-primary peizhi" id="toBindWx">立即配置</button>
                    <hr class="zhixian">
                    <div class="env">情况2: 无论您是否开通了"认证服务号"</div>
                    <div class="extraC">
                        <div class="extra">您可以通过小鹅通代销内容商品, 后由小鹅通与您结算货款(需您发起提现申请)。微信收取0.6%交易手续费,通过小鹅通完成代缴。</div>
                        <div class="extra">提现审核周期: 微信支付的结算周期为T+7, 提现申请后,7天后款项会自动转至您的微信钱包。</div>
                    </div>
                    <button class="btn btn-default useing" id="toCollection">正在使用</button>
                </div>
            </div>
        </div>
    </div>
@stop

