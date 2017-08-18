<?php
$pageData = [];
$pageData['sideActive'] = 'money_admin';
$pageData['barTitle'] = '财务管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/payAdmin.css?{{env('timestamp')}}">

@endsection

@section('page_js')
    <script type="text/javascript" src="../js/external/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="../js/admin/dataUsage.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')

    <div class="payTitle">
        <ul>
            <li><a href="/order_list" >订单记录</a></li>
            <li><a href="/pay_admin" >开通记录</a></li>
            <li class="borderBlue"><a href="/data_usage" >消费记录</a></li>
            <li><a href="/withdraw_page" >提现记录</a></li>
        </ul>
    </div>

    <div >
        <div style="width: 100% ; height: 100px" >
            <div class="revenueInformation">
                <div style="text-align:center;width: 49% ;float: left ;height:100px;line-height:70px;">
                    <ul class="ul-revenue" style="list-style-type: none;">
                        <li style="font-size: 30px;width: 100%;height: 30px">￥{{round(($todaySize/1024)*0.6,2)}}</li>
                        <li style="width: 100%;height: 30px">今日流量费(元)</li>
                    </ul>
                </div>
                <div style="margin-top: 10px;margin-bottom: 10px;width: 1px;height: 75%;background: #ECECEC;float: left">

                </div>
                <div style="text-align:center;width: 49% ;float: left ;height:100px;line-height:70px;">
                    <ul class="ul-revenue" style="list-style-type: none;">
                        <li style="font-size: 30px;width: 100%;height: 30px">￥{{round(($allSize/1024)*0.6,2)}}</li>
                        <li style="width: 100%;height: 25px">总流量费(元)</li>
                        <li class="paydoc" data-target="#usageModal" data-toggle="modal">计费说明</li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="searchArea">
            <div style="float: right">
                <button class="btn btn-default" style="margin-bottom: 3px" id="searchButton">搜索</button>
            </div>
            <div style="float: right;">
                <input type="text" class="form-control" aria-label="..." id="search" />
            </div>
            <div class="searchSelectArea">
                <select class="form-control" id="ruler">
                    <option value="0" selected="selected">资源</option>
                </select>
            </div>
            <div style="float: right;margin-right: 10px;">
                <input type="text" class="form-control" id="end" placeholder="请输入结束时间" />
            </div>
            <div style="float:right;height:100%;line-height:34px;margin:0 5px;">至</div>
            <div style="float: right;">
                <input type="text" class="form-control" id="start" placeholder="请输入开始时间" />
            </div>
            <div style="float:right;height:100%;line-height:34px;margin-right: 10px;">起止时间：</div>
            <div style="float:left;height:100%;line-height:34px;">总计(元)：
                <span style="color:#1E90FF;">￥{{round(($resultSize/1024)*0.6,2)}}</span>
            </div>
        </div>

        <table class="table table-hover" style="margin-top: 10px;border: 1px solid #ddd;border-left: none; border-right: none">
            <thead>
                <tr>
                    <th>内容名称</th>
                    <th>类型</th>
                    <th>时间段</th>
                    <th>流量(M)</th>
                    <th>费用(元)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td style="text-align: center;">{{$value['resource_name']}}</td>
                        <td>{{$value['resource_type']}}</td>
                        <td>{{$value['duration']}}</td>
                        <td>{{$value['size']}}</td>
                        <td>{{$value['fee']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            @if(empty($search))
                <?php echo $resultInfo->appends(['ruler' => $ruler,'start'=>$start,'end'=>$end])->render(); ?>
            @else
                <?php echo $resultInfo->appends(['ruler' => $ruler, 'search'=> $search,'start'=>$start,'end'=>$end])->render(); ?>
            @endif
        </div>
    </div>
@stop

@section('base_modal')
    <div class="modal fade" id="usageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 130px;">
            <div class="modal-content" style="height: 400px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">计费说明</span></div>
                </div>

                <div class="modal-body" style="height: 200px">
                    <p>  只收取音视频流量费（0.6元/1G），其他基础功能免费使用。<br />
                        费用说明：<br />
                        1、基础功能（免费）<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;a）图文、音频、视频等内容形式<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;b）专栏订阅、单个购买等付费方式<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;c）用户互动、下发通知<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;d）渠道分发、营销推广<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;e）邀请码（会员卡）<br />
                        2、音视频流量（0.6元/1G流量）<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;a）10分钟音频（约5M），单个用户收听1次只需0.003元<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;b）30分钟视频（约250M），单个用户观看1次只需0.15元</p>
                </div>

                <div class="modal-footer" style="margin-top: 75px;">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
@stop


