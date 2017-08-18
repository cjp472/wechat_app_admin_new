<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
$tabData = ['tabTitle'=>'modelSetting', 'model'=>'company'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/admin/accountSetting/bindcompanymodel.css?{{env('timestamp')}}" />
    <link rel="stylesheet" href="../css/external/jquery-alert.css"/>
@endsection

@section('page_js')
    {{--配置--}}
    <script type="text/javascript" src="../js/admin/config/config.js"></script>
    {{--复制文本到剪贴板--}}
    <script type="text/javascript" src="../js/external/jquery.zclip.min.js"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    {{--弹出框--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js"></script>
    {{--文件上传到本地服务器--}}
    <script type="text/javascript" src="sdk/uploadfive/jquery.uploadifive.js"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js"></script>
    <script type="text/javascript" src="../js/admin/accountSetting/bindcompanymodel.js?{{env('timestamp')}}"></script>


    {{--公共支付js--}}
    <script type="text/javascript" src="../js/utils/WeXinPay.js?201704"></script>
@endsection


@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    <div class="content" style="position: relative;">

        <ul id="myTabs" class="nav nav-tabs">
            <li>
                <a href="/personmodel">个人模式</a>
            </li>
            <li class="active">
                <a href="javascript:void(0)">企业模式</a>
            </li>
            <a class="modelHelp" href="/helpCenter/problem?document_id=d_58f812aa4d22a_rEc36L5X" target="_blank">运营模式说明</a>
        </ul>

        <div class="msgBox">
            <div class="msgTitle"><span>当 前 状 态</span> :</div>
            @if($h5->use_collection == 1){{-- 表示为个人版 --}}
                <div class="msgInfo">未开通</div>
            @else
                <div class="msgInfo">已开通</div>
            @endif
        </div>
        @if($h5->use_collection != 1){{-- 当前版本为企业版 --}}
        <div class="msgBox" style="margin-top: -8px;margin-bottom: 13px;">
            <div class="msgTitle"><span>重 要 提 示</span> :</div>
            <span class="spanCon">开通企业模式后的商品链接只可挂靠于已认证公众号，否则可能出现用户无法支付等严重后果！此种情况由微信打击跨号支付造成
                <a target="_blank" href="/helpCenter/problem?document_id=d_59433aa010b20_v9SbwjYu" style="margin-left: 10px;">点击查看解决方案</a>
            </span>
        </div>
        @endif
        <div class="minContent firstmin clearfix">
            <div class="contentTitle">公众号授权信息</div>
            <div class="pull-left">
                <div class="infoArea">
                    <div class="infoLabel">微信公众号:</div>
                    <div class="infoValue">{{$h5->wx_app_name}}</div>
                </div>
                <!-- <div class="infoArea">
                    <div class="infoLabel">公众号昵称:</div>
                    <div class="infoValue">{{$h5->wx_app_name}}</div>
                </div> -->
                <div class="infoArea">
                    <div class="infoLabel">微信账号类型：</div>
                    @if($accessOpen==1)
                    <div class="infoValue">
                        已在微信公众号后台取消授权，请 重新授权：<a id="reAuth">重新授权</a>
                    </div>
                    @endif
                    @if($accessOpen==0)
                        <div class="infoValue">
                            已认证服务号
                        </div>
                    @endif
                </div>
                <div class="infoArea" id="showPhone">
                    <div class="infoLabel">店铺地址：</div>
                    <div class="infoValue" style="color:#337ab7;">{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}/homepage</div>
                    <button type="button" data-clipboard-text="{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}/homepage" class="copyButton copyHref">复制</button>
                </div>
            </div>

            <div id="qrcodeArea" class="pull-left">
                <div class="codeText">微信扫一扫访问店铺</div>
                <div id="h5qrcode"></div>
            </div>
        </div>
        <a href="#payset" name="payset"></a>
        <div class="minContent">
            <div class="contentTitle">
                支付设置<img src="../images/icon_edit.png" id="editImg" alt="编辑" title="编辑"/>
            </div>
            <div class="contentH4">您已绑定“认证服务号”，且已向微信申请开通“微信支付权限”</div>
            <div class="contentTip">您可以在此配置，使用自己的微信支付。货款直接进入您的微信支付对应的财付通账号。微信将收取每笔0.6%的交易手续费。</div>
            <div class="infoArea" style="margin-top: 30px;">
                <div class="infoLabel">微信商户号：</div>
                <input type="text" class="form-control long disEdit bigFont" disabled="disabled"
                id="wx_mchid" value="{{$h5->wx_mchid}}"/>
                <a target="_blank" href="/help#hp3" style="font-size: 12px;">如何获取商户号？</a>
            </div>
            <div class="infoArea">
                <div class="infoLabel">微信商户API密钥：</div>
                <input type="text" class="form-control long disEdit bigFont" disabled="disabled"
                id="wx_mchkey" value="{{$h5->wx_mchkey}}"/>
                <a target="_blank" href="/help#hp3" style="font-size: 12px;">如何获取商户密钥？</a>
            </div>
            <div class="infoArea">
                <div class="infoLabel">支付授权目录({{ empty(\App\Http\Controllers\Tools\AppUtils::getIsNew(\App\Http\Controllers\Tools\AppUtils::getAppID())) == "https" ? "https" : "http" }})：</div>
                <div class="infoValue" style="color:#337ab7;padding-left: 10px;">{{$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}/content_page/</div>
                <button type="button" data-clipboard-text="{{$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}/content_page/" class="copyButton copyHref">复制</button>
                <a target="_blank" href="/help#hp4" style="font-size: 12px;margin-left: 30px;">如何配置支付授权目录？</a>
            </div>
            <div class="infoArea hide" >
                <button type="button" id="saveMer" >保存</button>
            </div>
        </div>


        {{--验证支付信息配置  支付授权目录配置 start--}}
        <div class="minContent">

            <div class="contentTitle">验证支付信息</div>
            @if($h5->pay_directory_verified == 1){{-- 表示已经验证 --}}
                <div class="contentH4">验证支付信息成功</div>
            @else
                <div class="contentH4">已绑定“认证服务号”，且已向微信申请开通“微信支付权限”，且配置好“支付授权目录”</div>
                <div class="infoArea">
                    <button type="button" id="confirmButton">验证支付信息</button>
                </div>
            @endif

        </div>
        {{--验证支付信息配置  支付授权目录配置 end--}}

        <div class="minContent">
            <div class="contentTitle">微信公众号配置</div>
            {{-- <div class="contentH4">微信公众号后台配置</div> --}}
            <div class="contentTip topTip">需要登录微信公众平台将以下URL配置到对应位置</div>
            <div class="infoArea" style="margin-top: 30px;">
                @if(empty($h5->wx_bus_verify_txt))
                <div class="infoLabel">业务域名文件：</div>
                <input type="file" id="wx_bus_verify_txt" />
                @else
                <div class="infoLabel">业务域名文件：</div>
                <div class="infoValue" style="margin-right: 20px;">{{$h5->wx_bus_verify_txt}}</div>
                <input type="file" id="wx_bus_verify_txt" />
                @endif
            </div>
            <div class="infoArea">
                <div class="infoLabel">业务域名：</div>
                <div class="infoValue" style="color:#337ab7;">{{$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}</div>
                <button type="button" data-clipboard-text="{{$h5->wx_app_id}}.{{env("DOMAIN_NAME")}}" class="copyButton copyHref">复制</button>
            </div>
        </div>
        <div class="minContent lastmin">
            <div class="contentTip">开发配置指南</div>
            <div class="bottomTip" >1、请确保已经开发公众号微信支付
                <a target="_blank" href="https://mp.weixin.qq.com">https://mp.weixin.qq.com（查看配置说明）</a>
            </div>
            <div class="bottomTip">2、支付授权目录：微信支付—开发配置—公众号支付—支付授权目录；</div>
            <div class="bottomTip">3、业务域名：公众号设置—功能配置—业务域名</div>
        </div>

        <input type="hidden" id="xcx_app_id" value="{{session("app_id")}}" />

    </div>

@stop



@section('base_modal')
    {{--验证支付信息配置  弹窗 start--}}
    <div class="modal fade" id="setModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
        <div class="modal-dialog" style="width: 480px;height: 408px;border-radius: 10px;margin: 0 auto;margin-top: 200px;">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">验证支付信息</h4>
                </div>
                    {{--二维码div--}}
                    <div id="confirmPayDirCode" style="margin-left:160px; margin-top: 40px;margin-bottom: 8px;"></div>


                <div class="modal-body" style="height: 60px;text-align:center;margin-bottom: 10px">
                    微信扫一扫上方二维码，完成支付！<br>
                    本次支付为验证支付信息配置是否成功，不可退款。
                </div>

            </div>
        </div>
    </div>
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
