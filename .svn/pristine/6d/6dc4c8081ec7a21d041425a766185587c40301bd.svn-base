<?php
$pageData = [];
$pageData['sideActive'] = 'dashboard_admin';
$pageData['barTitle'] = '数据分析';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/dashboard.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/dashboardAdmin.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/echarts.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')

    <div class="data_tip">提示：数据展示会有30分钟延时，实时数据请到财务管理中查看</div>

    <div style="width: 100%;">
        <div style="text-align: center;">

            <div style="display: block;margin: 20px 20px 20px 20px;">

                <table cellspacing="0" cellpadding="0" class="list" id="IndexPreviewTableList">
                    <tbody>
                    <tr class="title">
                        <th></th>
                        <th>新增用户 (付费用户)</th>
                        <th>活跃用户（付费活跃用户）</th>
                        <th>总用户 (总付费用户)</th>
                        <th>新增收入(元)</th>
                        <th>总收入(元)</th>
                    </tr>
                    <tr class="">
                        <td class="normal">今天</td>
                        <td class="">{{ number_format($data[0]->dayCount) }} ({{ number_format($day_pay_data[0]->dayPay) }})</td>
                        <td class="">{{ number_format($active_data[0]->active_count) }} ({{ number_format($paid_active_data[0]->paid_active_count) }})</td>
                        <td class="">{{ number_format($data[0]->sumCount) }} ({{ number_format($sum_pay_data[0]->sumPay) }})</td>
                        <td class="todayIncome">{{ number_format($data[0]->dayPrice * 0.01, 2) }}</td>
                        <td class="">{{ number_format($data[0]->sumPrice * 0.01, 2) }}</td>
                    </tr>
                    <tr><td class="normal">昨天</td>
                        <td class="">{{ number_format($data[1]->dayCount) }} ({{ number_format($day_pay_data[1]->dayPay) }})</td>
                        <td class="">{{ number_format($active_data[1]->active_count) }} ({{ number_format($paid_active_data[1]->paid_active_count) }})</td>
                        <td class="">{{ number_format($data[1]->sumCount) }} ({{ number_format($sum_pay_data[1]->sumPay) }})</td>
                        <td class="">{{ number_format($data[1]->dayPrice * 0.01, 2) }}</td>
                        <td class="">{{ number_format($data[1]->sumPrice * 0.01, 2) }}</td>
                    </tr>
                    <tr class="normal">
                        <td class="normal">7天前</td>
                        <td class="">{{ number_format($data[2]->dayCount) }} ({{ number_format($day_pay_data[2]->dayPay) }})</td>
                        <td class="">{{ number_format($active_data[2]->active_count) }} ({{ number_format($paid_active_data[2]->paid_active_count) }})</td>
                        <td class="">{{ number_format($data[2]->sumCount) }} ({{ number_format($sum_pay_data[2]->sumPay) }})</td>
                        <td class="">{{ number_format($data[2]->dayPrice * 0.01, 2) }}</td>
                        <td class="">{{ number_format($data[2]->sumPrice * 0.01, 2) }}</td>
                    </tr>
                    <tr class="last"><td class="normal">30天前</td>
                        <td class="">{{ number_format($data[3]->dayCount) }} ({{ number_format($day_pay_data[3]->dayPay) }})</td>
                        <td class="">{{ number_format($active_data[3]->active_count) }} ({{ number_format($paid_active_data[3]->paid_active_count) }})</td>
                        <td class="">{{ number_format($data[3]->sumCount) }} ({{ number_format($sum_pay_data[3]->sumPay) }})</td>
                        <td class="">{{ number_format($data[3]->dayPrice * 0.01, 2) }}</td>
                        <td class="">{{ number_format($data[3]->sumPrice * 0.01, 2) }}</td>
                    </tr>
                    </tbody></table>

            </div>
            <div style="clear: both"></div>
        </div>



    </div>

    <div style="background: #F9F9F9;height: 15px;width: 100%;align-self: center;"></div>

    <div style="width: 100%;">
        <div style="text-align: center;">
            <div style="display: inline-block;margin-top: 10px">
                <span style="font-size: 20px;margin-left: 150px">收入增长趋势分析</span>
            </div>
            <div style="display: inline-block;float: right;margin-right: 20px;margin-top: 20px">
                <div class="btn-group">
                    <button type="button" class="btn btn-default active" id="todayIncomeBtn" onclick="selectIncomeDate(0)">今天</button>
                    {{--<button type="button" class="btn btn-default" id="yesterdayIncomeBtn" onclick="selectIncomeDate(1)">昨天</button>--}}
                    <button type="button" class="btn btn-default" id="sevenIncomeBtn" onclick="selectIncomeDate(7)">7天</button>
                    <button type="button" class="btn btn-default" id="thirtyIncomeBtn" onclick="selectIncomeDate(30)">30天</button>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>

        <div id="echarts_income" class="echats_class">

        </div>

    </div>

    <div style="background: #F9F9F9;height: 15px;width: 100%;align-self: center;"></div>

    <div style="width: 100%;">
        <div style="text-align: center;">
            <div style="display: inline-block;margin-top: 10px">
                <span style="font-size: 20px;margin-left: 150px">用户增长趋势分析</span>
            </div>
            <div style="display: inline-block;float: right;margin-right: 20px;margin-top: 20px">
                <div class="btn-group">
                    <button type="button" class="btn btn-default active" id="todayUserBtn" onclick="selectDate(0)">今天</button>
                    {{--<button type="button" class="btn btn-default" id="yesterdayUserBtn" onclick="selectDate(1)">昨天</button>--}}
                    <button type="button" class="btn btn-default" id="sevenUserBtn" onclick="selectDate(7)">7天</button>
                    <button type="button" class="btn btn-default" id="thirtyUserBtn" onclick="selectDate(30)">30天</button>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>

        <div id="echarts_content" class="echats_class">

        </div>

    </div>

    <div style="background: #F9F9F9;height: 15px;width: 100%;align-self: center;"></div>

    <div style="width: 100%;">
        <div style="text-align: center;">
            <div style="display: inline-block;margin-top: 10px">
                <span style="font-size: 20px;margin-left: 150px">用户活跃趋势分析</span>
            </div>
            <div style="display: inline-block;float: right;margin-right: 20px;margin-top: 20px">
                <div class="btn-group">
                    <button type="button" class="btn btn-default active" id="todayActiveBtn" onclick="selectActiveDate(0)">今天</button>
                    {{--<button type="button" class="btn btn-default" id="yesterdayActiveBtn" onclick="selectActiveDate(1)">昨天</button>--}}
                    <button type="button" class="btn btn-default" id="sevenActiveBtn" onclick="selectActiveDate(7)">7天</button>
                    <button type="button" class="btn btn-default" id="thirtyActiveBtn" onclick="selectActiveDate(30)">30天</button>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>

        <div id="echarts_acitve" class="echats_class">

        </div>

    </div>

@stop
