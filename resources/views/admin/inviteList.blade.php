<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/inviteCode.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/inviteList.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    {{--头部--}}

    <div class="header">
        {{--搜索--}}
        <div class="searchArea">
            <span style="font-size: 16px;">使用记录</span>

            <div class="searchButtonArea">
                <button class="btn btn-default" id="searchButton">搜索</button>
            </div>

            <div class='searchInputArea'>
                <input type="text" class="form-control" aria-label="..." name="search" />
            </div>

            <div class="searchSelectArea">
                <select class="form-control" name="ruler">
                    <option value="0" selected="selected">用户名称</option>
                    <option value="1">邀请码</option>
                </select>
            </div>

            <div class="searchSelectArea">
                <select class="form-control" name="state">
                    <option value="0" selected="selected">全部</option>
                    <option value="1">未使用</option>
                    <option value="2">已使用</option>
                </select>
            </div>

        </div>

    </div>

   {{--table区--}}
    <div class="content">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>邀请码</th>
                    <th>用户头像</th>
                    <th>用户名称</th>
                    <th>使用状态</th>
                    <th>使用时间</th>
                    <th>链接</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listInfo as $k => $v)
                    <tr>
                        <td>{{$v->code}}</td>
                        <td><img src="{{ $v->wx_avatar or "../images/default.png" }}" class="avatar" /> </td>
                        <td><a href="javascript:;" onclick="jumpDetail('{{$app_id}}|{{$v->user_id}}')">{{ $v->wx_nickname }}</a></td>
                        <td>{{ $v->state }}</td>
                        <td>{{$v->used_at}}</td>
                        <td>{{$v->qr_code_url}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            {!! $listInfo->appends(['bid' => $bid,'ruler' => $ruler,'state' => $state, 'search'=> $search])->render() !!}
        </div>
    </div>
@stop
