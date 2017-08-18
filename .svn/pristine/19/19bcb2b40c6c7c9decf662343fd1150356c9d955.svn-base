<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/h5Setting.css?{{env('timestamp')}}"/>
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/h5Setting.js?{{env('timestamp')}}"></script>
@endsection

@section('base_contentTop')
    @if($flag)
    <p class="tipsInfo">
        变更运营模式为企业模式后，将不可恢复为个人模式，请确认后操作
        <a class="changeModel" href="/h5setting">暂不配置，点击返回</a>
        <a id="closebtn" href="javascript:void(0)">
            <img width="12px" src="../images/icon_Pop-ups_close.svg" alt="">
        </a>
    </p>
    @endif
@endsection

@section('base_mainContent')

    {{--公众号设置--}}
    <div class="content" >
        {{--<div class="contentTitle">运营模式</div>--}}
        {{--<div class="model">
            <span class="nowModel">当前运营模式：个人模式</span>
            <a class="changeModel" href="/h5setting">返回</a>
        </div>--}}
        <div class="contentH4">马上将微信公众号和小鹅打通吧：</div>
        <div class="contentTip">为了保证所有功能正常，授权公众号时请保持默认选择，把权限统一授权给小鹅通。</div>
        <a class="changeModel" target="_blank" href="/help#hp2">查看接入教程</a>

        {{--<button type="button" id="bindNow">我有认证服务号，立即设置</button>--}}
        <div id="bindNow">我有认证服务号，立即设置</div>
        {{--@if($is_openUser == 1)--}}
            <!-- <span  id="choiceModel">我是未认证订阅号，选择个人版</span> -->
        {{--@endif--}}

    </div>

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

    {{--
    <div class="modal fade" id="explainModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 640px;height: 400px;border-radius: 10px;margin: 0 auto;margin-top: 200px;">
            <div class="modal-content content_mod" style="height: 400px;">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">个人版说明</h4>
                </div>

                <div class="modal-body" style="height: 220px;margin-top: 15px;margin-left: 40px;margin-right: 40px;">
                    <div class="extra">
                        <div class="extra_1">1、您确认您在小鹅通平台发布的全部内容商品不侵害他人名誉权、商标权、著作权等合法权益，未经版权人许可不得随意摘编、转载。</div>
                        <div class="extra_1" style="margin-bottom: -20px;">2、您在小鹅通平台发布的所有内容商品的单价不得超过200元人民币。</div>
                        <div class="extra_1">3、您在小鹅通平台产生的订单，商家只需承担微信收取的0.6%交易手续费，小鹅通不加收手续费。</div>
                        <div class="extra_1">4、小鹅通有权利对您在小鹅通平台发布的全部内容商品进行审核，小鹅通在发现交易异常或您有违反相关法律规定时，有权不经通知先行暂停或终止该账户的使用（包括但不限于对该账户名下的款项进行调账等限制措施），并拒绝您使用本服务的部分或全部功能。</div>
                    </div>

                </div>
                <button class="btn btn-primary peizhi" id="confirmChoice">确定</button>
            </div>
        </div>
    </div>
    --}}
@stop
