<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle']='营销中心';
?>

@extends('admin.baseLayout',$pageData)

@section("page_css")
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/marketing/coupon.css?{{env('timestamp')}}" />
@stop

@section("page_js")
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
@stop

@section("base_mainContent")
    <div class="pageTopTitle">
        <a href="/coupon/index">优惠券</a> &gt; 新建优惠券
    </div>
    <div class="content">
        <div class="discountBox">
            <img src="../images/admin/marketing/shopTicket.png" alt="店铺优惠券">
            <div class="discountStyle">店铺优惠券</div>
            <span class="tinyTip">全店通用</span>
            <div class="listContent">买家购买全店商品，凭券抵扣现金</div>
            <a href="/coupon/create?type=1"><div class="btnMid btnBlue">立即创建</div></a>
        </div>
        <div class="discountBox">
            <img src="../images/admin/marketing/goodsTicket.png" alt="店铺优惠券">
            <div class="discountStyle">商品优惠券</div>
            <span class="tinyTip">指定商品可用</span>
            <div class="listContent">买家购买指定商品，凭券抵扣现金</div>
            <a href="/coupon/create?type=0"><div class="btnMid btnBlue">立即创建</div></a>
        </div>
    </div>

@stop
