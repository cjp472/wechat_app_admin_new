<?php
$pageData = [];
$pageData['sideActive'] = 'knowledgeShop';
$pageData['barTitle'] = '店铺设置';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/interfaceSetting.css?201701" />
@endsection


@section('page_js')
    {{--复制文本到剪贴板--}}
    <script type="text/javascript" src="../js/external/jquery.zclip.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/interfaceSetting.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')

    @include("admin.knowledgeShop.baseTab", ["tabTitle" => "phonePreview"])

    {{--代收，全是短链接--}}
    @if($info->use_collection === 1)
        <div class="content">
            <img src="../images/phone_preview.png" class="phonePreview" alt="手机端预览图" title="手机端预览图" />
                @if($info->if_caption_define)
                    <div class="homeInfoArea">
                        <div class="infoLabel">首页名称：</div>
                        <div class="infoValue">
                            <div class="titleShow">{{$info->home_title}}</div>
                            <div id="edit">编辑</div>

                            <input type="text" class="form-control long hide" style="float: left;"
                                   id="home_title" placeholder="请输入首页名称" maxlength="32" value="{{$info->home_title}}" />
                            <div class="hide" id="save">保存</div>
                        </div>

                    </div>
                @endif

            <div class="wxName">
                <div class="infoLabel">店铺名称：</div>
                <div class="infoValue">
                    <div class="wxTitleShow">{{$info->wx_app_name}}</div>
                    <a id="editWxName" href="javascript:void(0)">编辑</a>

                    <input id="wxnameInput" class="form-control long hide" style="float:left;"
                           type="text" value="{{$info->wx_app_name}}">
                    <div class="hide" id="saveWx">保存</div>

                </div>
            </div>

            <div class="bindInfoArea">
                <div class="infoLabel">店铺地址：</div>
                <div class="infoValue" style="color:#337ab7;line-height: 34px;">{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).env("DOMAIN_DUAN_NAME")}}/{{session("app_id")}}/</div>
                <button type="button" data-clipboard-text="{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).env("DOMAIN_DUAN_NAME")}}/{{session("app_id")}}/" class="copyButton copyHref">复制</button>
            </div>

            <div class="qrcodeArea" id="qrcodeArea">
                <div class="infoLabel">微信扫一扫访问店铺：</div>
                <div class="infoValue">
                    <div id="h5qrcode"></div>
                </div>

                <div id="mask"></div>
                <div id="alertAppend" class="appendText" >
                    <p class="textInfo">您已成功开通小鹅通基础版账户，打开微信扫描左侧二维码即可访问您的专属店铺</p>
                    <p class="textBtn" id="iKnow">我知道了</p>
                </div>
            </div>
        </div>
        {{--自有，有公众号--}}
    @elseif($info->use_collection == 0)
        <div class="content">
            <img src="../images/phone_preview.png" class="phonePreview" alt="手机端预览图" title="手机端预览图" />


                @if($info->if_caption_define)
                    <div class="homeInfoArea">
                        <div class="infoLabel">首页名称：</div>
                        <div class="infoValue">
                            <div class="titleShow">{{ $info->home_title }}</div>
                            <div id="edit">编辑名称</div>

                            <input type="text" class="form-control long hide" style="float: left;"
                                   id="home_title" placeholder="请输入首页名称" maxlength="32" value="{{$info->home_title}}" />
                            <div class="hide" id="save">保存</div>
                        </div>
                    </div>
                @endif

            <div class="wxName">
                <div class="infoLabel">店铺名称：</div>
                <div class="infoValue">
                    <div class="wxTitleShow">{{$info->wx_app_name}}</div>
                    <a id="editWxName" href="javascript:void(0)">编辑</a>

                    <input id="wxnameInput" class="form-control long hide" style="float:left;"
                           type="text" value="{{$info->wx_app_name}}">
                    <div class="hide" id="saveWx">保存</div>

                </div>
            </div>

            <div class="bindInfoArea">
                <div class="infoLabel">店铺地址：</div>
                <div class="infoValue" style="color:#337ab7;line-height: 34px;">{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).$info->wx_app_id}}.{{env("DOMAIN_NAME")}}/</div>
                <button type="button" data-clipboard-text="{{\App\Http\Controllers\Tools\AppUtils::getUrlHeader(\App\Http\Controllers\Tools\AppUtils::getAppID()).$info->wx_app_id}}.{{env("DOMAIN_NAME")}}/" class="copyButton copyHref">复制</button>
            </div>

            <div class="qrcodeArea" id="qrcodeArea">
                <div class="infoLabel">微信扫一扫访问店铺：</div>
                <div class="infoValue">
                    <div id="h5qrcode"></div>
                </div>
                <div id="mask"></div>
                <div id="alertAppend" class="appendText" >
                    <p class="textInfo">您已成功开通小鹅通基础版账户，打开微信扫描左侧二维码即可访问您的专属店铺</p>
                    <p class="textBtn" id="iKnow">我知道了</p>
                </div>
            </div>
        </div>
        {{--自有，无公众号--}}
    @else
        <div class="content">
            <img src="../images/phone_preview.png" class="phonePreview" alt="手机端预览图" title="手机端预览图" />
            <div class="no_wx_temp1">您还未进行接入配置，暂不能预览手机端页面</div>
            <div class="no_wx_temp2">选择企业版运营模式时，为了保证所有功能正常，授权公众号时请保持默认选择，把权限统一授权给小鹅通</div>
            <a class="no_wx_temp3" href="/h5setting">点击配置</a>
        </div>
    @endif
@stop
