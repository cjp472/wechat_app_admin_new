<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';

$tabData = ['tabTitle'=>'miniSetting', 'model'=>'person'];
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountSetting/mini/person.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    <script type="text/javascript" src="../js/admin/accountSetting/mini/person.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{--公众号设置--}}
    <div class="content" >
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="javascript:void(0)">小程序集</a>
            </li>
            {{--@if(\App\Http\Controllers\Tools\AppUtils::get_version_type() > 2)--}}
            <li>
                <a href="/mini/index">独立小程序</a>
            </li>
            {{--@endif--}}
        </ul>

        {{-- {{dump( $info )}} --}}
        {{--dump( $switch )--}}
        <input id="app_id" type="hidden" value="{{$info->app_id}}">
        <div class="miniPersonSwitch">
            <div class="title">小程序集</div>
            <div class="intro">
                所有用户均可开启小程序集服务，您无需进行任何配置，小鹅通将自动为您生成涵盖售卖内容的小程序商店，您的用户可以通过“小鹅通+”小程序主入口访问、购买和订阅您的知识内容。
            </div>
            <div class="helpBtn"><a href="/helpCenter/problem?document_id=d_5911bf4700f73_hAo2bSRW" target="_blank">查看【小程序集】使用教程</a></div>

            <div id="miniPersonToggle" class="Switch @if($switch==1) opening @else closing @endif" data-toggle="{{$switch}}">
                @if($switch==1) 
                    <span>开启</span> 
                @else 
                    <span>关闭</span>  
                @endif
                <div class="SwitchCircle z-depth-2"></div>
            </div>
        </div>
        
        <div class="mainContainer">
            <div class="codeContent">
                <p class="topText">小程序二维码</p>
                <div id="notminiCode" @if($switch==1) style="display:none;" @endif>
                    <div class="noCodeContent" style="background-image: url(../images/mini_code.png)"></div>
                    <p class="bottomText">开启小程序功能后，可获取二维码</p>
                </div>
                <div id="hasminiCode" @if($switch==0) style="display:none;" @endif>
                    {{-- <div id="miniCode"></div> --}}
                    <img src="{{$img}}" alt="" width="150px" height="170px">
                    <p class="bottomBtnContent">
                        <a id="downloadCode" href="{{$img}}" class="btnMid btnBlue" download="二维码.jpeg">下载二维码</a>
                    </p>
                </div>
            </div>
            <div class="imgContent">
                <p class="topText">示意图</p>
                <img class="firstImg" src="../images/mini_set01.png" alt="" width="145px" height="260px" />
                <img src="../images/mini_set02.png" alt="" width="145px" height="260px" />
            </div>
        </div>
    </div>
@stop

