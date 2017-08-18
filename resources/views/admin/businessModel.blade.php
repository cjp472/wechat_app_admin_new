<?php
$pageData = [];
$pageData['sideActive'] = 'my_money_admin';
$pageData['barTitle'] = '财务管理';
$tabData = [];
$tabData['tabTitle'] = 'companyIncome';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/selectTime.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/payAdmin.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    {{--时间选择器--}}
    <script type="text/javascript" src="../js/external/dateRange.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/Model.js?{{env('timestamp')}}"></script>
@endsection
{{--dump($search_array)--}}


@section('base_mainContent')

    @include("admin.financialManage.baseTab", $tabData)

    <div class="payContainer">
        <div class="incomeData">
            <ul class="clearfix">
                <li>
                    <span class="moneyText">企业模式总收入 (元)</span>
                    <p class="moneyNumber">{{number_format($count_sum/100,2)}}</p>
                </li>
                <li>
                    <span class="moneyText">今日收入 (元)</span>
                    <p class="moneyNumber">{{number_format($count_sum_today/100,2)}}</p>
                </li>
            </ul>
        </div>

        <div class="searchArea">
            <div class="tableText pull-left">收入记录</div>
            <div class="modelInfoText pull-left">
                <p>企业模式收入说明</p>
                <div class="modelInfoBox">
                企业模式收入会直接存入您的微信商户账户，包含除去个人模式收入的其他收入
                <br>
                个人模式收入包含以下部分：
                    <ul>
                        <li>您更改为企业模式前的全部收入</li>
                        <li>用户通过邀请卡购买产生的收入</li>
                        <li>用户通过推广员的推广链接购买产生的收入</li>
                    </ul>
                </div>
            </div>
            <div class="pull-right">
                <button class="btn btn-default" type="submit" style="margin-bottom: 3px" id="pay_search_btn">筛选</button>
            </div>
            <div id="SelectTime" class="pull-right time_group">
                <div id="dropdown-toggle" class="time_input dropdown-toggle" data-toggle="dropdown" >
                    <span id="SelectData">全部订单</span>
                    <span class="caret "></span>
                </div>
                <div id="SelectRange" class="time_option dropdown-menu">
                    <ul>
                        <li data-type='all'>全部订单</li>
                        <li data-type='nowMonth'>当月订单</li>
                    </ul>
                    <p id="optional" class="optional">自选时间</p>
                </div>
            </div>
            <input type="hidden" id="startTime" name="start_time" />
            <input type="hidden" id="endTime" name="end_time" />
        </div>
        {{--dump($ListInfo)--}}
        <table class="table table-hover" style="margin-top: 10px;border-left: none; border-right: none">
            <thead>
                <tr>
                    <th class="td_left">时间</th>
                    <th>商品类型</th>
                    <th>商品名称</th>
                    <th class="td_right">收入 (元)</th>
                </tr>
            </thead>
            <tbody class="model_tbody">
                @foreach($ListInfo as $key=>$value)
                <tr>
                    <td class="td_left">{{$value->created_at}}</td>
                    <td>{{$value->attr}}</td>
                    <td><p class="goodsName" title="{{$value->purchase_name}}">{{$value->purchase_name}}</p></td>
                    <td class="td_right">+ {{number_format($value->price/100,2)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(count($ListInfo)==0)
            <div class="contentNoneTip">没有相应的数据</div>
        @endif

        <div class="list-page">
            @if(empty($search_array))
                {!! $ListInfo->render() !!}
            @else
                {!! $ListInfo->appends($search_array)->render() !!}
            @endif
        </div>
    </div>
@stop


