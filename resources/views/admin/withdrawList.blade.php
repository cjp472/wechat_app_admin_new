<?php
$pageData = [];
$pageData['sideActive'] = 'my_money_admin';
$pageData['barTitle'] = '财务管理';
$tabData = [];
$tabData['tabTitle'] = 'withdrawPage';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/selectTime.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/withdrawAdmin.css?{{env('timestamp')}}">
@stop

@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    {{--时间选择器--}}
    <script type="text/javascript" src="../js/external/dateRange.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/withdrawList.js?{{env('timestamp')}}"></script>

@stop


@section('base_mainContent')

    @include("admin.financialManage.baseTab", $tabData)

    <div class="payContainer">
        <div>
            <div style="width: 100% ;">
                <div class="revenueInformation">
                    <div style="text-align:left;width: 60% ;float: left ;height:100px;line-height:70px;">
                        <div class="ul-revenue" style="list-style-type: none;margin-left: 10px;">
                            <p class="account_amount" >可提现余额(元)</p>
                            <p class="money">{{$accountBalance}}</p>
                            <p class="money_extra">注：此账户用于由小鹅通代收产生的相关收益。微信将收取每笔0.6%的交易手续费，由小鹅通完成代缴。</p>
                        </div>
                    </div>
                    <div style="text-align:right;width: 40% ;float: left ;height:100px;line-height:70px;">
                        <div class="exportExcel" >
                            @if($bool_authenticate == 0)
                                <button onclick="applyWithdrawPage()" class="btn_withdraw" style="margin-bottom: 3px;margin-top: 20px;">提现</button>
                            @elseif($bool_authenticate == 1)
                                <button onclick="getBindwxPage()" class="btn_withdraw" style="margin-bottom: 3px;margin-top: 20px;">提现</button>
                            @endif
                        </div>
                        <div class="contract hide">
                            <p class="contract_tel">提现咨询专线:400-640-8800</p>
                            <p class="contract_time">服务时间:10:00-18:00</p>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="searchArea">
            <div class="tableText pull-left">提现记录</div>
            <form action="/withdraw_page" method="GET" class="submitFormPart">
                <div id="SelectTime" class="pull-left time_group">
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

                <select name="cash_status" class="cashStatusSelector" id="cash_status">
                    <option value="-1" selected="selected">提现状态</option>
                    <option value="0">待审核</option>
                    <option value="1">待打款</option>
                    <option value="3">提现成功</option>
                    <option value="2">提现失败</option>
                </select>
                <button type="submit" class="xeBtnDefault btnMid searchWithdrawListBtn" id="withdraw_search_btn">筛选</button>
            </form>
        </div>

        <table class="table table-hover" style="margin-top: 10px;border: 1px solid #ddd;border-left: none; border-right: none">
            <thead>
            <tr>
                <th>提现时间</th>
                <th>到账微信账号</th>
                <th>金额(元)</th>
                <th>完成时间</th>
                <th>状态</th>
                <th>申请人</th>
                <th>操作</th>
            </tr>
            </thead>

            <tbody class="pay_tbody">
            @foreach($withdrawList as $key=>$value)
                <tr>
                    <td>
                        {{$value->cash_time}}
                    </td>
                    <td>
                        <img src="{{$user_info[$key]->wx_avatar?$user_info[$key]->wx_avatar:'../images/default.png'}}"
                             style="cursor: pointer;width: 30px;height: 30px;"
                             onclick="jumpDetail('{{$value->app_id}}')"/>
                        <span>{{$user_info[$key]->wx_nickname}}</span>
                    </td>
                    <td style="color: red;">{{$value->total_cash_money/100}}</td>
                    @if($value->cash_statue == 2 || $value->cash_statue == 3)
                        <td >
                            {{$value->updated_time}}
                        </td>
                    @else
                        <td >
                            -
                        </td>
                    @endif
                    @if($value->cash_statue == 0)
                        <td>待审核</td>
                    @elseif($value->cash_statue == 1)
                            <td>待打款</td>
                    @elseif($value->cash_statue == 2)
                        <td>提现失败</td>
                    @elseif($value->cash_statue == 3)
                        <td>提现成功</td>
                    {{--@elseif($value->cash_statue == 4)--}}
                        {{--<td>打款失败</td>--}}
                    @endif
                    <td>{{$user_info[$key]->name}}</td>
                    <td><a href="/get_withdraw_detail?serial_number='{{$value->serial_number}}'">详情</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(count($withdrawList) == 0)
            <div style="text-align: center">没有相应的数据</div>
        @endif
            <div class="list-page">
                @if($start_time)
                    <?php echo $withdrawList->appends(['start_time' => $start_time,  'end_time' => $end_time, 'cash_status' => $cash_status])->render(); ?>
                @else
                    <?php echo $withdrawList->appends(['start_time' => $start_time,  'end_time' => $end_time, 'cash_status' => $cash_status])->render(); ?>
                @endif
            </div>
        </div>
    </div>
@stop




