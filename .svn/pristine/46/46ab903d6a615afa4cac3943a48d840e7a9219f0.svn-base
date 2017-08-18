<?php
$pageData = [];
$pageData['sideActive'] = 'money_admin';
$pageData['barTitle'] = '财务管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/withdrawDetail.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/applywithdraw.js?{{env('timestamp')}}"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
@endsection



@section('base_title')
    {{--<span style="font-size: 18px">财务管理</span>--}}
@stop

@section('base_mainContent')
    <div class="payTitle">
        <ul>
            <span class="sub_title"><a href="/withdraw_page" >提现记录</a></span>
            <span class="tubiao"> > </span>
            <span class="sub_title_2">提现详情</span>
        </ul>
    </div>

    <hr>
    <table class="table table-hover" style="margin-top: 10px;border: 1px solid #ddd;border-left: none; border-right: none">
        <thead>
        <tr>
            <th class="liushui">交易流水</th>
            <th class="jinqian">提现金额(元)</th>
            <th>到账时间</th>
            <th>说明</th>
        </tr>
        </thead>
        <tbody class="pay_tbody">
            <tr>
                <td>
                    {{$withdrawDetail->serial_number}}
                </td>
                <td class="cash_money">{{$withdrawDetail->total_cash_money/100}}</td>
                @if($withdrawDetail->cash_statue == 2 || $withdrawDetail->cash_statue == 3)
                <td >
                    {{$withdrawDetail->updated_time}}
                </td>
                @else
                    <td >
                        -
                    </td>
                @endif
                {{--<td class="shuoming">{{$withdrawDetail->extra}}</td>--}}
                @if($withdrawDetail->cash_statue == 2)
                    <td class="shuoming">{{$withdrawDetail->reason}}</td>
                @elseif($withdrawDetail->cash_statue == 1)
                    <td class="shuoming">提交微信审批</td>
                @elseif($withdrawDetail->cash_statue == 3)
                    <td class="shuoming">提现成功</td>
                @elseif($withdrawDetail->cash_statue == 4)
                    <td class="shuoming">微信转账失败</td>
                @elseif($withdrawDetail->cash_statue == 5)
                    <td class="shuoming">订单操作异常</td>
                @elseif($withdrawDetail->cash_statue == 0)
                    <td class="shuoming">订单审核中</td>
                @endif
            </tr>
        </tbody>
    </table>
@stop