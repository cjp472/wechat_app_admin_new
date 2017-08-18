<?php
$pageData = [];
$pageData['sideActive'] = 'try_listener';
$pageData['barTitle'] = '试听渠道';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
<link type=text/css rel="stylesheet" href="../css/admin/channelAdmin.css?{{env('timestamp')}}">
@endsection


@section('page_js')
<script type="text/javascript" src="../js/admin/channelAdmin.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')

<div class="header">
    <ul class="header_ul">
        <li class="header_li" ><a class="header_a" href="/channel_admin">渠道管理</a></li>
        @if(session('app_id')=='appIK67joYW5412'||session('app_id')=='appe0MEs6qX8480'||session('app_id')=='apppcHqlTPT3482')
        <li class="header_li"><a class="header_a" href="/sale">分销审批</a></li>
        @endif
        @if(session('app_id')=='apppcHqlTPT3482'||session('app_id')=='appe0MEs6qX8480'||session('app_id')=='apprnDA0ZDw4581')
        <li class="header_li" style="border-bottom: 2px solid #2a75ed;"><a class="header_a" href="/channel/listen">试听渠道</a></li>
        @endif
    </ul>

</div>

<div>
    <div class="searchArea">
        <div style="float: left;">
            <button class="btn btn-default btn-blue" id="lisAddBtn">新增试听渠道</button>
        </div>
    </div>

    <div style="padding: 0 20px;">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>
                    <div class="th_channel_type">渠道名称</div>
                </th>
                <th>
                    <div class="th_channel_type">资源名</div>
                </th>
                <th>
                    <div class="th_channel_type long">推广链接</div>
                </th>
                <th>
                    <div class="th_channel_type">创建时间</div>
                </th>
                <th>
                    <div class="th_channel_type">领取量</div>
                </th>
                <th>
                    <div class="th_channel_type">开通量</div>
                </th>

            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)

            <tr>
                <td>{{$value->channel_name}}</td>
                <td>{{$value->purchase_name}}</td>
                <td>{{$value->url}}</td>

                <td>{{$value->created_at}}</td>
                <td>{{$value->receive_count}}</td>
                <td>{{$value->open_count}}</td>
            </tr>
            @endforeach
            </tbody>

        </table>

        @if(count($data)==0)
            <div style="text-align:center" class="contentNoneTip">没有相应的数据</div>
        @endif

        @if($data)
            <div class="list-page">
                {!! $data->render() !!}
            </div>
        @endif
    </div>
</div>

@stop

@section('base_modal')

<!-- 按钮触发模态框 -->
<!-- 模态框（Modal） -->
<div class="lisAddBox">
    <div class="lisAddCont">
        <h4>新增试听渠道</h4>
        <div class="lisAddName">
            <span>渠道名称：</span>
            <input type="text" placeholder="请输入渠道名称">
        </div>
        <div class="modal-footer center lisBtnBox" >
            <button type="button" class="btn btn-default lisCancel">
                关闭
            </button>
            <button type="button" class="btn btn-primary btn-blue lisConfirm" style="margin-left: 10px">
                确认
            </button>
        </div>
    </div>
</div>

@stop