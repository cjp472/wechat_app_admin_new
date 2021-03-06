<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css" />

    <link type=text/css rel="stylesheet" href="../css/admin/acitvity/activeBaseLayout.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/admin/acitvity/activityEnrollment.css?{{env('timestamp')}}"/>
@stop

@section('page_js')
    {{--模板引擎js--}}
    <script type="text/javascript" src="../js/admin/utils/template.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/acitvity/activeBaseLayout.js?{{env('timestamp')}}"></script>
    {{--弹窗--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/acitvity/activityEnrollment.js?{{env('timestamp')}}"></script>
@stop


@section('base_mainContent')
    <input id="activity_id" type="hidden" value="{{$activity_id}}">
    <input id="activity_state" type="hidden" value="{{$state}}">
    <input id="activity_link" type="hidden" value="{{$activity_link}}">
    <div class="contentHeader">
        <a href="/activityManage">活动管理</a> > 名单管理
        <a class="help pull-right" href="/helpCenter/problem?document_id=d_59005ee74f52e_XpAtqGxm" target="_blank">活动管理教程</a>
    </div>
    <div class="activityName">
        {{$activity_title}}
    </div>

    <ul class="baseManageTab">
        <li class="enrollmentManage baseActiveTab">
            <a>报名管理</a>
        </li>
        <li class="attendanceManage">
            <a>签到管理</a>
        </li>
    </ul>

    <div class="contentAreaWrapper">
        <div class="contentArea">

        </div>
        <div class="loadingS">
            <div class="loadingSContent">
                <svg viewBox="25 25 50 50" class="circular">
                    <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                </svg>
                <p class="loadingText">加载中</p>
            </div>
        </div>
    </div>


@stop


@section('base_modal')
    {{--黑色幕布--}}
    <div class="darkScreen" style="display: none">
    </div>
    {{--发送通知弹框--}}
    <div class="massageBox" style="display: none">
        <div class="massageBoxClose msgClose"><img src="../images/icon_Pop-ups_close.svg" alt=""></div>
        <div class="massageBoxTitle">发送通知</div>
        <div class="massageBoxContent">
            <div class="sendRangeTitle">发送方式</div>

            <label><input class="sendMethod" type="checkbox" value="0">系统消息</label>
            <label class="shortMessage"><input class="sendMethod" type="checkbox" value="1">短信通知</label>

            {{--<select id="sendMethod" class="form-control">--}}
            {{--<option value="0" selected>管理员消息</option>--}}
            {{--<option value="1">短信通知</option>--}}
            {{--</select>--}}

            <div class="sendRangeTitle">发送范围</div>
            <select id="sendRange" class="form-control">
                <option value="0" selected>全部用户</option>
                <option value="1">已报名成功用户</option>
                <option value="2">本页选中用户</option>
            </select>
            <div class="noticeContentTitle">
                通知内容
            </div>
            <textarea id="sendContent" class="form-control" placeholder="请输入通知内容" rows="9"></textarea>
        </div>
        <div class="massageBoxBtnGroup">
            <div class="btnMid xeBtnDefault massageBoxBtnCancel msgClose">取消</div>
            <div id="sendMsg" class="btnMid btnBlue massageBoxBtnConfirm">确定</div>
        </div>
    </div>
    {{--活动成员个人信息框--}}
    <div class="memberInfoBox">
        <div class="infoBoxHeader">报名资料
            <div class="memberInfoBoxClose"><img src="../images/icon_Pop-ups_close.svg" alt="关闭图标"></div>
        </div>
        <div id="enrollPersonInfo">

        </div>

        <script id="peopleInfo" type="text/html">
            <div class="memberInfoContent">
                <div class="memberInfoP1">
                    <div class="memberLogo"><img src="@{{imgSrc}}" alt="用户头像"></div>
                </div>
                <div class="memberInfoP2">
                    <div class="memberName">@{{memberName}}</div>
                    <div class="memberRealName">姓名：@{{realName}}</div>
                    <div class="memberPhone">手机号：@{{memberPhone}}</div>
                </div>
                <div class="memberInfoP3">
                    <div class="memberInfoP3Title">更多资料</div>
                    <div class="memberInfoP3Line"></div>
                    <div class="memberInfoP3Content">
                        @{{each moreInfo as moreValue moreIndex}}
                        <div class="memberInfoP3Part">
                            @{{each moreValue as value index}}
                            <div class="memberInfoP3T">@{{ index }}</div>
                            <div class="memberInfoP3C">@{{ value }}</div>
                            @{{/each }}
                        </div>
                        @{{/each}}

                    </div>
                </div>
            </div>
            {{--个人信息框按钮--}}
            @{{if (state==0)}}
            <div class="btnMid btnBlue memberPass allowUser" data-userid="@{{userId}}">通过</div>
            <div class="btnMid xeBtnDefault memberRefuse refuseUser" data-userid="@{{userId}}">拒绝</div>
            @{{/if}}
        </script>

    </div>
    {{--拒绝设置框--}}
    <div class="memberRefuseBox" style="display: none">
        <div class="RefuseBoxClose"><img src="../images/icon_Pop-ups_close.svg"></div>
        <div class="refuseIcon"><img src="../images/version_charge_fail.png" alt="拒绝图标"></div>
        <div class="refuseTitle">请提交拒绝理由</div>
        <div class="refuseTip"></div>
        <div class="refuseContent">
            <div class="refuseMTitle">您本次要拒绝的用户</div>
            <div class="refuseMContent"></div>
            <div class="refuseReansonTitle">如果确定要拒绝选中人员，请填写选中详细原因</div>
            <textarea name="rReason" id="refuseReasonInput" cols="8" rows="10"
                      placeholder="请输入拒绝理由"></textarea>
            <div class="btnMid xeBtnDefault rCancel">取消</div>
            <div class="btnMid xeBtnDefault rRefuse" style="color: #e64340">拒绝</div>
        </div>
    </div>
    {{--下载签到二维码--}}
    <div class="show_qrCode_window" style="display: none;">
        <div class="qrCode_window">
            <div class="window_top_area">
                <div class="close_icon_wrapper">
                    <img class="close_icon" src="/images/icon_Pop-ups_close.svg">
                </div>
            </div>
            <div class="qrCodeWrapper" id="qrCodeWrapper">

            </div>
            <div class="qrCode_window_word_1">微信扫描二维码签到</div>
            <div class="qrCode_window_word_2">下载二维码可用于线下扫码签到</div>
            <div class="downloadQRCode xeBtnDefault btnMid">下载</div>
        </div>
    </div>

    {{--导出Excel文件选择弹框--}}
    <dav class="modal downloadPop" id="ExportModal" style="display:none">
        <div class="darkScreen"></div>
        <form class="downloadPop_office">
            <div class="pageTopTitle">
                <span>导出活动<i id="businessType">报名</i>名单</span>
                <button type="button" class="close closePop" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <span>office使用版本</span>
                <input class="with-gap popS" id="Office_false" name="selectOffice" type="radio" value="0">
                <label for="Office_false">非office2003</label>
                <input class="with-gap popS" id="Office_true" name="selectOffice" type="radio" value="1" checked>
                <label for="Office_true">office2003</label>
                <div class="declaration">如果下载文件出现乱码，请选择另一个office版本选项进行下载</div>
            </div>
            <div class="modal-footer">
                <div class="xeBtnDefault btnMid closePop">关闭</div>
                <div class="btnBlue btnMid" id="applyExcel">确定</div>
            </div>
        </form>
    </dav>
@stop
