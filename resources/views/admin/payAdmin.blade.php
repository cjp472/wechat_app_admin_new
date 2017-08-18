<?php
$pageData = [];
$pageData['sideActive'] = 'customerManage';
$pageData['barTitle'] = '用户管理';
$tabData = [];
$tabData['tabTitle'] = 'paymentRecord';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/admin/payAdmin.css?{{env('timestamp')}}">
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/selectTime.css?{{env('timestamp')}}">
@stop

@section('page_js')
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{--时间选择器--}}
    <script type="text/javascript" src="../js/external/dateRange.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/payAdmin.js?{{env('timestamp')}}"></script>
@stop


@section('base_mainContent')

    @include("admin.customerManage.baseTab", $tabData)

    {{--<div class="payTitle" style="border-bottom: 1px solid #ECECEC;">--}}
        {{--<ul>--}}
            {{--@if( \App\Http\Controllers\Tools\AppUtils::IsCollection()==0 )--}}
            {{--<li class="incomeLi"><a href="/income/company">企业模式收入</a></li>--}}
            {{--@endif--}}
            {{--<li class="incomeLi"><a href="/income/person">个人模式收入</a></li>--}}
            {{--<li><a href="/order_list" >订单记录</a></li>--}}
            {{--<li class="borderBlue"><a href="/pay_admin" >开通记录</a></li>--}}
            {{--@if(session('is_collection') == 1 || \App\Http\Controllers\Tools\AppUtils::IsVisualWithDraw())--}}
                {{--<li><a href="/withdraw_page" >提现记录</a></li>--}}
            {{--@endif--}}
        {{--</ul>--}}
    {{--</div>--}}

    <div class="payContainer">
        <div class="searchArea">
            <div class="exportExcel">
                <button class="btn_export" onclick="exportRecords()" id="export_btn" data-target="#ExportModal">导出开通记录</button>
            </div>
            <div class="sumPayUser">
                <div style="float:left;height:100%;line-height:34px;">购买人数：
                    <span style="color:#1E90FF;">{{$pay_user_sum}}</span>
                </div>
            </div>
            <form action="/pay_admin" method="GET" class="formSubmitPart">
                <div id="SelectTime" class="pull-left time_group">
                    <div id="dropdown-toggle" class="time_input dropdown-toggle" data-toggle="dropdown" >
                        <span id="SelectData">全部记录</span>
                        <span class="caret "></span>
                    </div>
                    <div id="SelectRange" class="time_option dropdown-menu">
                        <ul>
                            <li data-type='all'>全部记录</li>
                            <li data-type='nowMonth'>当月记录</li>
                        </ul>
                        <p id="optional" class="optional">自选时间</p>
                    </div>
                </div>
                <input type="hidden" id="startTime" name="start_time" />
                <input type="hidden" id="endTime" name="end_time" />

                <select name="generate_type" id="generate_type">
                    <option value="" @if($generate_type == "") selected @endif >购买类型</option>
                    <option value="1" @if($generate_type == 1) selected @endif >购买</option>
                    <option value="2" @if($generate_type == 2) selected @endif >邀请码</option>
                </select>
                <select name="search_type" id="search_type">
                    <option value="name" @if($search_type == "name") selected @endif >昵称</option>
                    <option value="content" @if($search_type == "content") selected @endif >商品名称</option>
                </select>
                <input type="text" name="search_content" class="inputDefault searchContentInput" aria-label="..." id="search_content"
                    placeholder="请输入搜索内容" @if($search_content) value="{{$search_content}}" @endif />
                <button class="xeBtnDefault btnMid searchPayRecordBtn" type="submit" id="pay_search_btn">搜索</button>
            </form>
        </div>
        <table class="table tableContent" >
            <thead>
                <tr>
                    <th class="th_left">头像</th>
                    <th>昵称</th>
                    <th>商品类型</th>
                    <th>商品名称</th>
                    <th>商品价格</th>
                    <th>购买类型</th>
                    <th>订单时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody class="pay_tbody">
            @foreach($ListInfo as $v)
                <tr>
                    <td class="th_left">
                        <img src="{{$v->img}}" style="cursor: pointer;" onclick="jumpDetail('{{$v->user_id}}')"/>
                    </td>
                    <td>
                        <span>{{$v->name}}</span>
                    </td>
                    {{--内容类型--}}
                    <td>{{$v->attr}}</td>
                    {{--消费内容--}}
                    <td class="msg">
                        <a href="/pay_admin?payment_type={{$v->payment_type}}&product_id={{$v->product_id}}&resource_id={{$v->resource_id}}">
                            {{$v->purchase_name}}
                        </a>
                    </td>
                    {{--消费总额--}}
                    @if($v->generate_type==0)
                        <td>￥{{$v->price}}</td>
                    @else
                        <td>￥0</td>
                    @endif
                    {{--订单类型--}}
                    <td>{{$v->generate}}</td>
                    {{--分享人昵称--}}
                    {{--@if(property_exists($v,'share_name'))--}}
                    {{--<td><a href="javascript:;" onclick="jumpDetail('{{$v->share_user_id}}')">{{$v->share_name}}</a></td>--}}
                    {{--@else--}}
                    {{--@endif--}}

                    {{--订单时间--}}
                    <td>{{$v->created_at}}</td>
                    <td>
                        <button class="btn btn-default" type="button" title="仅删除记录，不会自动退款"
                                onclick="deletePurchase('{{$v->user_id}}','{{$v->payment_type}}','{{$v->resource_type}}','{{$v->product_id}}','{{$v->resource_id}}')">删除</button>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="list-page">
            @if(empty($search_array))
                {!! $ListInfo->render() !!}
            @else
                {!! $ListInfo->appends($search_array)->render() !!}
            @endif
        </div>
    </div>
@stop

@section('base_modal')
    {{--发消息--}}
    <div class="modal fade downloadPop" id="ExportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="downloadPop_office">

                <div class="pageTopTitle">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div ><span class="modal-title" style="font-size: 18px" id="myModalLabel">导出开通记录</span></div>
                </div>

                <div class="modal-body">
        
                    <div>
                        <span class="outPutTime">开通时间</span>
                        <select class="form-control" id="export_time">
                            @foreach($export_times as $times)
                                <option value="{{$times->yearMonth}}">{{$times->yearMonth}}</option>
                            @endforeach
                        </select>
                        {{--<input class="form-control long" id="export_time" readonly/>--}}
                        {{--<input class="form-control long" id="start_time" readonly/>--}}
                        {{--<span>至</span>--}}
                        {{--<input class="form-control long" id="end_time" readonly/>--}}
                        <div>
                            <span>office使用版本</span>
                            <input class="with-gap popS" id="Office_false" name="selectOffice" type="radio" value="0">
                            <label for="Office_false">非office2003</label>
                            <input class="with-gap popS" id="Office_true" name="selectOffice" type="radio" value="1" checked>
                            <label for="Office_true">office2003</label>
                            <div class="declaration">如果下载文件出现乱码，请选择另一个office版本选项进行下载</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="xeBtnDefault btnMid" data-dismiss="modal">关闭</button>
                    <button type="button" class="btnBlue btnMid" id="applyOrderToExcel" onclick="exportToExcel()">确定</button>
                </div>
            </div>
    </div>
@stop


