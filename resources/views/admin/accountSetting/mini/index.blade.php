<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';

$tabData = ['tabTitle' => 'miniSetting', 'model' => 'company'];
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountSetting/mini/index.css?{{env('timestamp')}}"/>
    <link href="../css/external/materialize.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
@endsection


@section('page_js')
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    <script type="text/javascript" src="../js/admin/accountSetting/mini/index.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{--公众号设置--}}
    <div class="content">
        <ul class="nav nav-tabs">
            <li>
                <a href="/mini/person">小程序集</a>
            </li>
            <li class="active">
                <a href="javascript:void(0)">独立小程序</a>
            </li>
        </ul>
        {{--dump($info)--}}
        {{--dump($use_collection)--}}
        <input id="app_id" type="hidden" value="{{$info->app_id}}">
        <div class="miniPersonSwitch">
            <div class="title">独立小程序</div>
            <div class="intro">
                企业模式用户可以申请生成独立的小程序，小程序有独立访问入口，小程序内实现商品的展示，同时用户可以完成内容商品的订阅和浏览。
            </div>
            <div class="helpBtn"><a href="/helpCenter/problem?document_id=d_591580aedb6ed_ZBMk578Q"
                                    target="_blank">查看【独立小程序】使用教程</a></div>
            {{--专业版才显示配置入口--}}
            @if(\App\Http\Controllers\Tools\AppUtils::get_version_type() > 2)
                @if($use_collection == 0)
                    @if($info->use_collection == 1)
                        <div id="notSet" class="Switch">
                            @if($info->wx_app_id)
                                <a href="/mini/info" class="btnMid btnBlue">立即配置</a>
                            @else
                                <a href="/mini/guide" class="btnMid btnBlue">立即配置</a>
                            @endif
                        </div>
                    @else
                        <div id="hadSet" class="Switch">
                            <!-- <span class="setText">授权成功</span> -->
                            @if($info->wx_app_id)
                                <a href="/mini/info" class="btnMid xeBtnDefault">查看配置</a>
                            @else
                                <a href="/mini/guide" class="btnMid xeBtnDefault">查看配置</a>
                            @endif
                        </div>
                        @if($status==7)
                            <span class="openStateHint">已开启</span>
                        @endif
                    @endif
                @endif
            @endif
        </div>

        @if(\App\Http\Controllers\Tools\AppUtils::get_version_type() > 2)
            @if($use_collection != 0)
                <div class="mainContainer">
                    <h3 class="openInfo">申请独立的小程序需要服务号授权，请先开通企业模式</h3>
                    <p class="gotoOpen">
                        <button onclick="javascript:window.location.href='/companymodel'" class="btnMid btnBlue">前往开通
                        </button>
                    </p>
                </div>
            @else
                <div class="mainContainer">
                    <div class="codeContent">
                        @if($pay_switch==0 or $pay_switch==1)
                            <div class="pay_switch_wrapper">
                                <div class="hover_wrapper">
                                    <span class="pay_switch_title">是否显示付费内容</span>
                                    <div class="msg_hint_wrapper">
                                        依据相关规定,在小程序内使用虚拟支付有一定的风险,请谨慎使用!
                                    </div>
                                </div>
                                <div class="clear_float"></div>
                                <div class="radio_wrapper">
                                    <div>
                                        <input class="with-gap" id="open_pay" name="pay_switch" type="radio"
                                               @if($pay_switch==1) checked @endif/>
                                        <label for="open_pay" class="pay_switch_label" id="open_pay_label">
                                            开启
                                        </label>
                                    </div>
                                    <div>
                                        <input class="with-gap" id="close_pay" name="pay_switch" type="radio"
                                               @if($pay_switch==0) checked @endif />
                                        <label for="close_pay" class="pay_switch_label" id="close_pay_label">
                                            关闭(关闭后,小程序内将不再显示付费内容)
                                        </label>
                                    </div>

                                </div>
                            </div>
                        @endif
                        <p>小程序二维码</p>
                        @if($status == 7)
                            @if(!$img)
                                <div class="noCodeContent" style="background-image: url(../images/mini_code.png)"></div>
                                <p class="bottomText">配置成功后，显示小程序二维码</p>
                            @else
                                {{-- <div id="miniappCode"></div> --}}
                                <p class="openHint">您可以通过微信直接搜索到您的小程序<br/>或者通过二维码扫描访问</p>
                                <img src="{{$img}}" alt="" width="150px" height="150px">
                            @endif
                        @else
                            <div class="noCodeContent" style="background-image: url(../images/mini_code.png)"></div>
                            <p class="bottomText">配置成功后，显示小程序二维码</p>
                        @endif
                    </div>
                    <div class="imgContent">
                        <p class="topText">示意图</p>
                        <img class="firstImg" src="../images/personMini.jpg" width="145px" height="260px"/>
                        <img src="../images/mini_set02.png" width="145px" height="260px"/>
                    </div>
                </div>
            @endif
        @else
            <div class="update_container">
                <div class="update_hint">
                    当前版本不支持独立小程序,如需开启请升级专业版!
                </div>
                <div class="update_btn_wrapper">
                    <button onclick="javascript:window.location.href='/upgrade_account'" class="btnMid btnBlue">升级版本
                    </button>
                </div>
            </div>
        @endif

    </div>
@stop


