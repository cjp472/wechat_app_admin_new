<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
$tabData = ['tabTitle' => 'miniSetting', 'model' => 'company'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/admin/accountSetting/mini/info.css?{{env('timestamp')}}"/>
    <link rel="stylesheet" href="../css/external/jquery-alert.css"/>
@endsection

@section('page_js')
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    {{--弹出框--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js"></script>

    <script type="text/javascript" src="../js/external/clipboard.min.js"></script>
    <script type="text/javascript" src="../js/admin/accountSetting/mini/info.js?{{env('timestamp')}}"></script>

@endsection


@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    <div class="content" style="position: relative;">

        <div class="topBox">
            <a href="/mini/index">独立小程序</a>
            &gt;
            小程序配置
        </div>

        {{--dump($status)--}}
        {{-- {{dump($info)}} --}}
        <div class="msgBox">
            <div class="msgTitle"><span>当 前 状 态</span> :</div>
            @if($status == 1)
                @if($info->pay_directory_verified == 0)
                    <div class="msgInfo red_hint">授权成功，请继续完成用户验证</div>
                @else
                    <div class="msgInfo red_hint">用户验证成功，您的小程序即将提交微信审核，请耐心等待</div>
                @endif
            @elseif($status == 5)
                <div class="msgInfo red_hint">审核中，微信将在7个工作日内完成审核</div>
            @elseif($status == 7)
                <div class="msgInfo red_hint">已开启</div>
                <br/>
                <div class="msgTitle"><span>线上运行版本号</span> :</div>
                <div class="msgInfo">v{{$build_version}}</div>
                @if(!empty($latest_build_audited_fail))
                    <br/>
                    <div class="msgTitle"><span>最近一次提交版本</span> :</div>
                    <div class="msgInfo">v{{$latest_build_audited_fail}}
                    </div>
                    <br/>
                    <div class="msgTitle"><span>提交状态</span> :</div>
                    <div class="msgInfo">审核失败
                        <span class="red_hint">(不影响线上版本使用)</span>
                        <span class="reAudit" id="resubmitAudit">重新提交审核</span>
                    </div>
                    <br/>
                    @if(!empty($audited_fail_reason))
                        <div class="msgTitle"><span>审核失败原因</span> :</div>
                        <div class="msgInfo red_hint reasonDesc">{!! $audited_fail_reason."<a href = '/helpCenter/problem?document_id=d_592fe78a737b5_2Metsdi9'>查看解决方案</a>" !!}</div>
                    @endif
                @endif
            @elseif($status == 9)
                <div class="msgInfo red_hint">审核失败,请根据失败原因整改后继续提交审核</div>
                <span class="reAudit" id="resubmitAudit">重新提交审核</span>
            @elseif($status == 10)
                <div class="msgInfo red_hint">无法提交审核,请根据失败原因整改后继续提交审核</div>
                <span class="reAudit" id="resubmitAudit">重新提交审核</span>
            @elseif($status == -1)
                <div class="msgInfo">系统错误</div>
            @else
                <div class="msgInfo">系统错误</div>
            @endif

        </div>

        @if($status == 9&&!empty($audited_fail_reason))
            <div class="reasonBox">
                <div class="msgTitle"><span>审核失败原因</span> :</div>
                <div class="msgInfo red_hint reasonDesc">{!! $audited_fail_reason."<a href = '/helpCenter/problem?document_id=d_592fe78a737b5_2Metsdi9'>查看解决方案</a>" !!}</div>
            </div>
        @endif

        @if($status == 10&&!empty($audited_fail_reason))
            <div class="reasonBox">
                <div class="msgTitle"><span>审核失败原因</span> :</div>
                <div class="msgInfo red_hint reasonDesc">{!! $audited_fail_reason."<a href = '/helpCenter/problem?document_id=d_592fe78a737b5_2Metsdi9'>查看解决方案</a>" !!}</div>
            </div>
        @endif

        <input id="app_id" type="hidden" value="{{$info->app_id}}">
        <input id="pay_verify" type="hidden" value="{{$info->pay_directory_verified}}">
        <input id="is_update" type="hidden" value="{{$is_update ? 1 : 0}}">
        <div class="minContent firstmin">
            <div class="contentTitle">小程序授权信息</div>
            <div class="litterContent">
                <div class="infoArea">
                    <div class="infoLabel">小程序名称：</div>
                    <div class="infoValue">{{$info->wx_app_name}}</div>
                </div>

                <div class="infoArea">
                    <div class="infoLabel">账号类型：</div>
                    <div class="infoValue">
                        已授权小程序
                        <span id="reAuth" class="reAuth">重新授权</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="minContent">
            <div class="contentTitle">用户验证</div>
            {{--如果用户未验证完成用户信息,进入逻辑判断--}}
            @if($info->pay_directory_verified == 0)
                {{--如果数据没有写入，没有更新完用户的wx_union_id--}}
                @if(!$is_update)
                    {{--如果数据没有写入，但是写入操作已执行完，说明用户绑定错误--}}
                    @if($has_union_id == 2)
                        <div class="infoArea">
                            <div class="contentH4">请确认您已经将小程序和服务号与微信开放平台成功绑定。如已成功绑定，请通过下方按钮刷新重试。</div>
                        </div>
                        <div class="btnBox">
                            <div id="updateBindBtn" class="btnMid btnBlue">刷新</div>
                        </div>
                        {{--数据正在写入--}}
                    @elseif($has_union_id == 1)
                        <div class="litterContent">
                            <p class="contentH4 red_hint">数据正在写入，根据您服务号粉丝数需要半小时到半天不等，请耐心等待，稍后访问并刷新页面。</p>
                        </div>
                    @endif
                @else
                    <div id="notScreenCode" class="litterContent">
                        <div class="infoArea">
                            <div class="contentH4">请使用管理员微信先后扫描两个二维码，页面正常显示即可退出手机页面</div>
                        </div>
                        <div class="codeContent clearfix">
                            <div class="codeItem">
                                <img id="experUrlCode" src="" width="150px" height="150px"/>
                                <p>小程序体验码</p>
                            </div>
                            <div class="codeItem">
                                <input id="H5Url" type="hidden"
                                       value="{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).$wx_app_id}}.{{env("DOMAIN_NAME")}}//">
                                <div id="H5UrlCode" class="H5UrlCon"></div>
                                <p>知识店铺主页</p>
                            </div>
                        </div>
                        <div class="btnBox">
                            <div id="screenBtn" class="btnMid btnBlue">已都扫描完成，点击验证</div>
                        </div>
                    </div>
                @endif
            @else
                <div id="hasScreenCode" class="litterContent">
                    <p class="contentH4">验证成功</p>
                </div>
            @endif
        </div>
        <div id="setMiniInfo" class="minContent @if($info->pay_directory_verified==0) hide @endif">
            <div class="contentTitle">
                支付设置<img src="../images/icon_edit.png"
                         class="@if(empty($info->wx_mchid)||empty($info->wx_mchkey)) hide @endif" id="editImg" alt="编辑"
                         title="编辑"/>

                <sapn class="msgInfo normal_weight red_hint @if(!empty($info->wx_mchid)&&!empty($info->wx_mchkey)) hide @endif"
                      id="pay_set_hint">（请完成支付设置，否则您的小程序用户将无法购买商品）
                </sapn>

            </div>
            <div class="litterContent">
                <div class="contentH4">每个小程序均有独立的商户平台账户，请填写需要关联的小程序相关信息</div>

                <div class="infoArea">
                    <div class="infoLabel">商户号：</div>
                    @if(empty($info->wx_mchid)||empty($info->wx_mchkey))
                        <input type="text" class="inputDefault long bigFont" id="wx_mchid"
                               value="{{$info->wx_mchid}}"/>
                    @else
                        <input type="text" class="inputDefault long disEdit bigFont" disabled="disabled" id="wx_mchid"
                               value="{{$info->wx_mchid}}"/>
                    @endif
                    <a target="_blank" href="/help#hp3" style="font-size: 12px;">如何获取商户号？</a>
                </div>
                <div class="infoArea">
                    <div class="infoLabel">API密钥：</div>
                    @if(empty($info->wx_mchid)||empty($info->wx_mchkey))
                        <input type="text" class="inputDefault long  bigFont" id="wx_mchkey"
                               value="{{$info->wx_mchkey}}"/>
                    @else
                        <input type="text" class="inputDefault long disEdit bigFont" disabled="disabled" id="wx_mchkey"
                               value="{{$info->wx_mchkey}}"/>
                    @endif
                    <a target="_blank" href="/help#hp3" style="font-size: 12px;">如何获取API密钥？</a>
                </div>
                <div class="infoArea @if(!empty($info->wx_mchid)&&!empty($info->wx_mchkey)) hide @endif">
                    <button type="button" id="saveMer" class="btnMid btnBlue saveBtn">保存配置</button>
                </div>
            </div>
        </div>

    </div>

@stop



@section('base_modal')
    {{--点击授权--}}
    <div class="modal fade" id="bindModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog"
             style="width: 480px;height: 198px;border-radius: 10px;margin: 0 auto;margin-top: 200px;">
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
