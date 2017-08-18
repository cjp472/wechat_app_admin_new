<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
{{--弹出提示--}}
<link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
{{-- 扁平化框架 --}}
<link type="text/css" rel="stylesheet" href="../css/external/materialize.css?{{env('timestamp')}}"/>
<link type="text/css" rel="stylesheet"
      href="../../css/admin/resManage/resourceList.css?{{env('timestamp')}}"/>   {{--css--}}
@endsection

@section('page_js')
{{--弹出提示--}}
<script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>

<script type="text/javascript" src="../js/admin/marketing/shareUse.js?{{env('timestamp')}}"></script>
@endsection

@section("base_mainContent")
<ul class="baseManageTab">

    <li class="baseActiveTab">
        <a href="/invite/shareUseList" style="padding: 0 34px;">专栏</a>
    </li>

    {{--基础版看不到会员入口--}}
    <li>
        <a href="/invite/shareUseList" style="padding: 0 34px;">会员</a>
    </li>


</ul>
<div class="content">
    <div class="top_area">
        <div class="resource_list_num" style="float: left;font-size: 14px;line-height: 36px;">如会员内包含专栏，专栏的领取上限需前往专栏列表另行限制
        </div>
        <div class="right_operate">
            {{--
            <select class="selector_show_type browser-default">
                <option value="-1">状态</option>
                <option value="0">已上架</option>
                <option value="1">已下架</option>
            </select>
            <select class="selector_resource_type browser-default">
                <option value="0">类型</option>
                <option value="1">图文</option>
                <option value="2">音频</option>
                <option value="3">视频</option>
                <option value="4">直播</option>
            </select>
            --}}

            <input class="inputSearchAll inputDefault" placeholder="输入名称">
            <div class="searchAllBtn btnSmall xeBtnDefault">搜索</div>

        </div>
    </div>
    <table class="table_resource_list">
        <thead>
        <tr class="tr_head">
            <th class="th_resource_name">商品名称</th>
            <th class="th_resource_price">领取上限</th>
            <th class="th_resource_num">分享次数</th>
            <th class="th_information">领取数量</th>
            <th class="th_resource_state">购买数量</th>
            <th class="th_resource_operate">操作</th>
        </tr>
        </thead>
        <tbody>
        <tr class="tr_body">
            <td class="td_resource" >
                <p style="text-align:left;margin-left: 20px;">我是单品</p>
            </td>
            <td class="share_use_num">111</td>
            <td class="purchase_count_td">111</td>
            <td class="td_relative_info">11</td>
            <td class="resource_show_state">111</td>
            <td class="td_event_operate"><a data-good-id="aaa"  style="float:none" href="javascript:void(0);" class="copy_resource_link_btn shareNumSet">设置</a></td>
        </tr>
        </tbody>
    </table>

    @if(false)
    <div class="contentNoneTip">没有相应的数据</div>
    @endif

    {{--实现 table 分页--}}
    <div class="page_offset_div list-page" align="center">
        <ul class="pagination">
        </ul>
    </div>
</div>


@stop

@section('base_modal')
<div class="set_share_num">
    <div class="set_share_window">
        <p class="set_share_title center">设置领取上限</p>
        <div class="set_share_left">
            <p class="set_share_item_title">商品名称</p>
            <p class="set_share_item_title" class="">领取上限</p>
        </div>

        <div class="set_share_right">
            <div class="set_share_item_cont set_share_good_title">- -</div>
            <div class="set_share_item_cont"><input class="input_radio_value inputDefault set_share_good_num" placeholder="请输入领取上限"></div>
            <div class="set_share_word">例如领取上限设为10，该商品的付费用户分享该商品后，前10名好友领取后可免费试看该内容</div>
            <div class="button_area left" >
                <div class="cancel_sale_btn btnMid xeBtnDefault">取消</div>
                <div class="confirm_sale_btn btnMid btnBlue">确定</div>
            </div>
        </div>
    </div>
</div>
@stop
