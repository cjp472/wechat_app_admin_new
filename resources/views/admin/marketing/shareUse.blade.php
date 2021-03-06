<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    {{--弹出提示--}}
    <link type=text/css rel="stylesheet" href="/css/external/jquery-alert.css?{{env('timestamp')}}">
    {{-- 扁平化框架 --}}
    <link type="text/css" rel="stylesheet" href="/css/external/materialize.css?{{env('timestamp')}}"/>
    <link type="text/css" rel="stylesheet"
          href="../../css/admin/resManage/resourceList.css?{{env('timestamp')}}"/>   {{--css--}}
@endsection

@section('page_js')
    {{--弹出提示--}}
    <script type="text/javascript" src="/js/external/jquery-alert.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="/js/admin/marketing/shareUse.js?{{env('timestamp')}}"></script>
@endsection

@section("base_mainContent")
    <a target="_blank" style="color:#2a75ed;float: right;line-height: 60px;margin-right: 20px;" href="/helpCenter/problem?document_id=d_59688b14a1431_U29A4EzR">请好友看教程</a>

    <ul class="baseManageTab">
        <li class="@if($listType=='col') baseActiveTab @endif">
            <a href="/invite/shareUseList/col" style="padding: 0 34px;">专栏</a>
        </li>

        {{--基础版看不到会员入口--}}
        <li class="@if($listType=='member') baseActiveTab @endif">
            <a href="/invite/shareUseList/member" style="padding: 0 34px;">会员</a>
        </li>

    </ul>
    <div class="content">
        <div class="top_area">
            <div class="resource_list_num" style="float: left;font-size: 14px;line-height: 36px;">
            @if($listType=='member')
                如会员内包含专栏，专栏的领取上限需前往专栏列表另行限制
            @else
                专栏的付费用户分享该专栏内某期内容给好友，好友领取后可免费查看该内容（单卖商品不参与请好友看）
            @endif
            </div>
            <div class="right_operate">
                <input value="{{$search}}" style="width:205px" class="inputSearchAll inputDefault" placeholder="输入搜索名称">
                <div data-url="{{$listType}}" class="searchAllBtn btnSmall xeBtnDefault">搜索</div>
            </div>
        </div>
        <table class="table_resource_list">
            <thead>
            <tr class="tr_head">
                <th class="th_resource_name">
                    @if($listType=='member')
                        会员名称
                    @else
                        专栏名称
                    @endif
                </th>
                <th class="share_limit">分享上限</th>
                <th class="th_resource_price">
                    领取上限
                    <div class="moneyTipArea">
                        <span class="moneyTipIcon"></span>
                        <div class="moneyTipText">领取上限：已购买的用户分享专栏/会员内某内容后，好友可领取的名额数量</div>
                    </div></th>
                <th class="th_information">领取数量</th>
                <th class="th_resource_state">购买数量</th>
                <th class="th_resource_operate">操作</th>
            </tr>
            </thead>
            <tbody>
                @foreach($data as $value)
                <tr class="tr_body">
                    <td class="td_resource" >
                        <img style="float: left;margin-left: 20px;margin-right: 20px;" src="{{$value->img_url}}" alt="">
                        <p style="text-align:left;margin-left: 20px;">{{$value->name}}</p>
                    </td>
                    <td class="share_limit_num">
                        @if($value->share_listen_resource != 0)
                            {{$value->share_listen_resource}}
                        @else
                            @if($value->is_share_listen != 0 )不限制@else未参与@endif
                        @endif
                    </td>
                    <td class="share_use_num">{{$value->is_share_listen?$value->share_listen_count:'未参与'}}</td>
                    <td class="td_relative_info">{{$value->has_received}}</td>
                    <td class="resource_show_state">{{$value->has_purchased}}</td>
                    <td class="td_event_operate"><a data-good-id="{{$value->id}}" data-listen-count="{{$value->share_listen_count}}" data-listen-resource="{{$value->share_listen_resource}}" style="float:none" href="javascript:void(0);" class="copy_resource_link_btn shareNumSet">设置</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(count($data)==0)
            <div class="contentNoneTip">没有相应的数据</div>
        @endif

        {{--实现 table 分页--}}
        <div class="page_offset_div list-page" align="center">
            <?php echo $data->render(); ?>
        </div>
    </div>


@stop

@section('base_modal')
    <div class="set_share_num">
        <div class="set_share_window">
            <p class="set_share_title greyBack">设置分享/领取上限</p>
            <div class="set_share_left">
                <p class="set_share_item_title">商品名称</p>
                <p class="set_share_item_title">请好友看</p>
                <p class="set_share_item_title second_height">分享上限</p>
                <p class="set_share_item_title" class="">领取上限</p>
            </div>

            <div class="set_share_right">
                <div class="set_share_item_cont set_share_good_title">- -</div>
                <div class="set_share_item_cont ">
                    <input type="radio" class="with-gap notFreeSelect" id="shareOpen" name="shareFunc">
                    <label for="shareOpen">开启</label>
                    <input type="radio" class="with-gap notFreeSelect" id="shareClose" name="shareFunc">
                    <label for="shareClose">关闭</label>
                </div>
                <div class="set_share_item_cont">
                    <div>
                        <input type="radio" class="with-gap" id="withoutLimit" name="shareLimit">
                        <label for="withoutLimit">不限制</label>
                    </div>
                    <div>
                        <input type="radio" class="with-gap" id="definedLimit" name="shareLimit">
                        <label for="definedLimit">自定义</label>
                        <input onkeyup="this.value=this.value.replace(/^[0]{1}|\D/g,'')" class="input_radio_value inputDefault limit_number" placeholder="请输入分享上限(大于0的整数)">
                    </div>
                    <div class="set_share_word">例如分享上限设为10，则该专栏的付费用户最多可将该<br>
                        专栏内的10期内容送给好友免费看。
                    </div>
                </div>
                <div class="set_share_item_cont second_cont">
                    <input onkeyup="this.value=this.value.replace(/^[0]{1}|\D/g,'')" class="input_radio_value inputDefault set_share_good_num" placeholder="请输入领取上限(大于0的整数)">
                </div>
                <div class="set_share_word">例如领取上限设为10，该专栏的付费用户分享该专栏<br>
                    的某个内容后，前10名好友领取后可免费试看该内容。<br>
                    <span>重复分享相同内容不会产生新的名额</span></div>
                <div class="button_area left" >
                    <div class="cancel_sale_btn btnMid xeBtnDefault">取消</div>
                    <div class="confirm_sale_btn btnMid btnBlue">确定</div>
                </div>
            </div>
        </div>
    </div>
@stop
