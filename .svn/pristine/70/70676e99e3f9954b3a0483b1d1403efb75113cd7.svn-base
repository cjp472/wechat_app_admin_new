<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle']='营销中心';
?>

@extends('admin.baseLayout',$pageData)

@section("page_css")
    {{--时间选择器--}}
    <link href="../css/external/selectTime.css" rel="stylesheet" type="text/css">
    <link type=text/css rel="stylesheet" href="../css/admin/base.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/marketing/coupon.css?{{env('timestamp')}}" />
@stop

@section("page_js")
    {{--时间选择器--}}
    <script src="../js/external/dateRange.js" type="text/javascript"></script>
    <script src="../js/admin/base.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/marketing/coupon/couponIndex.js?{{env('timestamp')}}"></script>
@stop

@section("base_mainContent")
    <div class="pageTopTitle">
        <a href="/marketing">优惠券</a>
    </div>
    <div class="pageContent">
        <a class="couponIndexStudy" target="_blank" href="/helpCenter/problem?document_id=d_59329384c4304_rLyRPXsG">优惠券使用教程</a>
        <div class="tab_box couponTabTitle">
            <div class="tab_type tab_active" >优惠券</div>
            {{--<div class="tab_type" ><a href="/coupon/planIndex">发放记录</a></div>--}}
        </div>

        {{--搜索页头--}}
        <div class="header">
            {{--搜索--}}
            <form action="/coupon/index" method="GET" class="searchBox">
                <a href="/coupon/select" class="new_ticket btnMid btnBlue">新建优惠券</a>
                <div class="searchBtn">
                    <button class="btnSmall xeBtnDefault" id="searchButton">搜索</button>
                </div>
                <div class="searchOutBox">
                @if(array_key_exists('coupon_name',$search_array)) 
                    <input class="searchInputBox" type="text" value="{{$search_array['coupon_name']}}" placeholder="输入优惠券名称" aria-label="..." name="coupon_name" />
                @else
                    <input class="searchInputBox" type="text" placeholder="输入优惠券名称" aria-label="..." name="coupon_name" />
                @endif
                </div>
                
                <div id="SelectTime" class="pull-right time_group">
                    <div id="dropdown-toggle" class="time_input dropdown-toggle" data-toggle="dropdown" >
                        <span id="SelectData">全部优惠券</span>
                        <span class="caret "></span>
                    </div>
                    <div id="SelectRange" class="time_option dropdown-menu">
                        <ul>
                            <li data-type='all'>全部优惠券</li>
                            {{--<li data-type='nowMonth'>当月优惠券</li>--}}
                        </ul>
                        <p id="optional" class="optional">自选时间范围</p>
                    </div>
                </div>
                <input type="hidden" id="startTime" name="start_time" />
                <input type="hidden" id="endTime" name="end_time" />

                <div class="searchSelectBox">
                    <select class="couponSelect" name="select_state">
                        <option value="0" selected="selected">状&nbsp;&nbsp;态</option>
                        <option value="1">领取中</option>
                        <option value="2">已领完</option>
                        <option value="3">已结束</option>
                    </select>
                </div>
            </form>

        </div>

        {{--table区--}}

        <div class="tabContent">
            <table class="table">
                <thead>
                    <tr class="th">
                        <th>优惠券名称</th>
                        <th>面额(元)</th>
                        <th>有效期</th>
                        <th>限领/人</th>
                        <th>已领取</th>
                        
                        <th>已使用</th>
                        <!--<th>成交金额(元)</th>-->
                        <th>领取状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resInfo as $key => $value)
                    <tr>
                        <td>
                            <div class="coupontitle">{{$value->title}}</div>
                            <div class="nameTip">
                            @if($value->spread_type==0)
                                用户领取
                            @elseif($value->spread_type==1)
                                商家发放
                            @endif
                            </div>
                        </td>
                        <td>{{($value->price)/100}}
                            <div class="descTip">
                            @if($value->type==0)
                                @if($value->require_price == 0)
                                指定商品无门槛使用
                                @else
                                指定商品满{{($value->require_price)/100}}元可用
                                @endif
                            @else
                                @if($value->require_price == 0)
                                全店商品无门槛使用
                                @else
                                全店商品满{{($value->require_price)/100}}元可用
                                @endif
                            @endif
                            </div>
                        </td>
                        <td>
                            <div><span>起：</span>{{$value->valid_at}}</div>
                            <div><span>至：</span>{{$value->invalid_at}}</div>
                        </td>
                        <td>
                            <div>{{$value->receive_rule}}张/人</div>
                            <div class="descTip">发行量{{$value->count}}</div>
                        </td>
                        <td>{{$value->has_received}}</td>
                        <td>{{$value->is_use}}</td>
                        {{--<td>1112.1(var)</td>--}}
                        <td id="coupon_state">
                            @if($value->coupon_state==1)
                                领取中
                            @elseif($value->coupon_state==2)
                                已领完
                            @elseif($value->coupon_state==3)
                                已结束
                            @endif
                        </td>
                        <td>
                            <ul class="operation">
                            @if($value->spread_type==0)
                                @if($value->coupon_state==1||$value->coupon_state==2)
                                <li><a class="editCoupon" data-edit-id="{{$value->id}}">编辑</a></li>
                                <li>&nbsp;|&nbsp;</li>
                                <li><a class="copyhref" href="javascript:;"
                                    data-clipboard-text="{{$value->url}}" title="获取访问链接">复制链接</a>
                                </li>
                                <li>&nbsp;|&nbsp;</li>
                                <li><a class="overCoupon" data-style="{{$value->id}}">结束</a></li>
                                @elseif($value->coupon_state==3)
                                <li><a class="copyhref" href="javascript:;"
                                    data-clipboard-text="{{$value->url}}" title="获取访问链接">复制链接</a>
                                </li>
                                @endif
                            @else
                                @if($value->coupon_state==3)
                                    <li>-$nbsp;-</li>
                                @else
                                    <li class="editCoupon" data-edit-id="{{$value->id}}"><a>编辑</a></li>{{--编辑--}}
                                @endif
                            @endif
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{--页标--}}

            @if(count($resInfo)==0)
            <div class="contentNoneTip">没有相应的数据</div>
            @endif

            <div class="list-page">
                @if(empty($search_array))
                    {!! $resInfo->render() !!}
                @else
                    {!! $resInfo->appends($search_array)->render() !!}
                @endif
            </div>

        </div>
    </div>
@stop
