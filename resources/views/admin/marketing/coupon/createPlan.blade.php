<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>

@extends('admin.baseLayout',$pageData)

@section("page_css")
    <link href="../css/admin/base.css" rel="stylesheet" type="text/css"/>
    <link href="../css/com-form.css" rel="stylesheet" type="text/css"/>
    {{--时间插件--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
    {{--弹窗--}}
    <link href="../css/external/xcConfirm.css" rel="stylesheet" type="text/css"/>
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/admin/marketing/createPlan.css?{{env('timestamp')}}"/>


@stop

@section("page_js")
    {{--时间插件--}}
    <script src="../js/external/bootstrap-datetimepicker.min.js" type="text/javascript">
        {{--弹窗--}}
        <
        script
        src = "../js/external/xcConfirm.js"
        type = "text/javascript" >
                {{--表单检查工具类--}}
            < script
        src = "../js/admin/utils/formCheck.js?{{env('timestamp')}}"
        type = "text/javascript" ></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript"
            src="../js/admin/marketing/coupon/couponCreatePlan.js?{{env('timestamp')}}"></script>

@stop

@section("base_mainContent")
    <div class="pageTopTitle">
        <a href="/coupon/index">优惠券</a> &gt; 新建批量发送计划
    </div>

    <div class="formContent">
        <div class="contentBox">
            <p class="mainTitle">基本信息</p>
            <div class="cont-item">
                <div class="secTitle">计划名称<i>*</i></div>
                <div class="secCont"><input id="f_name" type="text" placeholder="计划名称，不超过十个字"></div>
            </div>
            <div class="cont-item">
                <div class="secTitle">目标人群<i>*</i></div>
                <div class="xeBtnDefault btnMid selectMembers">请选择目标人群</div>
                <div class="secCont userInfo memberInfo"  style="display: none;" >
                    <span class="memberType"><i class="memberName" id="f_mem_name" data-id=""></i></span>
                    <span class="memberNum" id="f_mem_num"></span>
                    <span class="iconBox">
                        <img class="memberInfoDel" src="../images/admin/marketing/coupon/icon-delete.svg" alt="">
                        <img class="memberInfoEdit" src="../images/admin/marketing/coupon/icon-edit.svg" alt="">
                    </span>
                </div>
            </div>
            <div class="cont-item">
                <div class="secTitle">优惠券<i>*</i></div>
                <div class="xeBtnDefault btnMid selectCoupons">请选择优惠券</div>
                <div class="secCont userInfo conponInfo" style="display: none;" >
                    <span class="couponName" id="f_cou_name" data-id=""></span>
                    <span class="couponNum"></span>
                    <span class="iconBox">
                        <img class="couponDel" src="../images/admin/marketing/coupon/icon-delete.svg" alt="">
                        <img class="couponEdit" src="../images/admin/marketing/coupon/icon-edit.svg" alt="">
                    </span>
                </div>
            </div>
            {{--<p class="mainTitle">发送时间</p>--}}
            {{--<div class="cont-item">--}}
                {{--<div class="secTitle">发送时间<i>*</i></div>--}}
                {{--<div class="secCont"><input type="text" placeholder="计划名称，不超过十个字"></div>--}}
            {{--</div>--}}
            <div class="cont-item">
                <div class="secTitle">&nbsp;</div>
                <div class="secCont">
                    <div class="btnBlue btnMid pageConfirm">确定</div>
                </div>
            </div>
            <p class="submit-tip"></p>
        </div>


@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    <div class="darkScreen2" style="display: none">
        <div class="addBox">
            <div class="pageTopTitle">
                <span class="outPutTime">添加优惠券</span>
                <div class="boxClose"><img src="/images/icon_Pop-ups_close.svg" alt=""></div>
            </div>
            <div class="modal-body" style="height:300px">
                <div class="searchHeader">

                    <!--
                    <input class="" id="couponName" type="text" placeholder="输入名称"/>
                    <button class="btnMid list-search" data-class="couponsContent" id="searchCoupons" value="">搜索</button>
                    -->
                </div>
                <div class="tab">
                    <table class="table">
                        <thead>
                        <tr class="th">
                            <th>优惠券名称</th>
                            <th>优惠券金额</th>
                            <th>剩余数量</th>
                            <th>使用条件</th>
                        </tr>
                        </thead>
                        <tbody class="couponsContent">
                        </tbody>
                    </table>
                </div>
                <div class="boxFooter">
                    <div class="xeBtnDefault btnMid closeAddBox">取消</div>
                    <div class="btnBlue btnMid addCoupons" id="addCoupons">确定</div>
                </div>
            </div>
        </div>
    </div>

    <div class="darkScreen1" style="display: none;">
        <div class="addBox addMemberBox">
            <div class="pageTopTitle">
                <span class="outPutTime">选择目标人群</span>
                <div class="boxClose"><img src="/images/icon_Pop-ups_close.svg" alt=""></div>
            </div>
            <div class="modal-body" style="height:300px">
                <div class="searchHeader">
                    <!--
                    <input class="" id="memberName" type="text" placeholder="输入名称"/>
                    <button class="btnMid list-search" data-class="membersContent" id="searchMembers" value="">搜索</button>
                    -->
                </div>
                <div class="tab">
                    <table class="table">
                        <thead>
                        <tr class="th">
                            <th>目标人群</th>
                            <th>人数</th>
                        </tr>
                        </thead>
                        <tbody class="membersContent">
                        </tbody>
                    </table>
                </div>
                <div class="boxFooter">
                    <div class="xeBtnDefault btnMid closeAddBox closeAddMemberBox">取消</div>
                    <div class="btnBlue btnMid addCoupons" id="addMembers">确定</div>
                </div>
            </div>
        </div>
    </div>


@stop