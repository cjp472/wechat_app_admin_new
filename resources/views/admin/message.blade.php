<?php
$pageData = [];
$pageData['sideActive'] = 'customerManage';
$pageData['barTitle'] = '用户管理';
$tabData = [];
$tabData['tabTitle'] = 'messageList';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/admin/message.css?{{env('timestamp')}}">
@stop

@section('page_js')
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/message.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    @include("admin.customerManage.baseTab", $tabData)

    {{--头部--}}
    <div class="header">
        <div class="searchArea">
            <a class="btnBlue btnMid sendMessageToAll" href="/messageadd">+推送全员消息</a>

            <form class="submitFormPart" action="/message" method="GET">
                <select class="sendTypeSelector" name="typer">
                    <option value="0" @if($typer && $typer == 0) selected @endif>全部</option>
                    <option value="1" @if($typer && $typer == 1) selected @endif>私人</option>
                    <option value="2" @if($typer && $typer == 2) selected @endif>群发</option>
                </select>
                <select class="receiverTypeSelector" name="ruler">
                    <option value="0" @if($ruler && $ruler == 0) selected @endif>内容</option>
                    <option value="1" @if($ruler && $ruler == 1) selected @endif>发送人</option>
                    <option value="2" @if($ruler && $ruler == 2) selected @endif>接收人</option>
                </select>
                <input class="inputDefault searchContentInput" name="search" type="text"
                       placeholder="请输入搜索内容" @if(!empty($search)) value="{{$search}}" @endif />
                <button type="submit" class="xeBtnDefault btnMid searchMsgBtn" id="searchButton">搜索</button>
            </form>

        </div>
    </div>

    {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>类型</th>
                    <th>发送人</th>
                    <th>接收人</th>
                    <th>消息内容</th>
                    <th>发送时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data as $key => $value)
            <tr>
                <td>{{$value['type']}}</td>
                <td>{{$value['send_nick_name']}}</td>
                <td>{{$value['receiver']}}</td>
                <td>{{$value['content']}}</td>
                <td>{{$value['send_at']}}</td>
                <td>{{$value['state']}}</td>
                <td class="operate_long">
                    @if($value['state']=='已撤回')
                    <button type="button" class="btn btn-default"  disabled="disabled" style="cursor:not-allowed;"
                    onclick="messageEdit('{{$value['id']}}')">编辑</button>
                    <button type="button" class="btn btn-default" disabled="disabled" style="cursor: not-allowed;"
                    onclick="messageDelete('{{$value['id']}}')">撤回</button>
                    @elseif($value['state']=='已发送')
                    <button type="button" class="btn btn-default"  disabled="disabled" style="cursor:not-allowed;"
                    onclick="messageEdit('{{$value['id']}}')">编辑</button>
                    <button type="button" class="btn btn-default" style="cursor: pointer;"
                    onclick="messageDelete('{{$value['id']}}')">撤回</button>
                    @else
                    <button type="button" class="btn btn-default"  style="cursor:pointer;"
                    onclick="messageEdit('{{$value['id']}}')">编辑</button>
                    <button type="button" class="btn btn-default" disabled="disabled" style="cursor:not-allowed;"
                    onclick="messageDelete('{{$value['id']}}')">撤回</button>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            @if(empty($search))
                <?php echo $allInfo->appends(['ruler' => $ruler, 'typer' => $typer])->render(); ?>
            @else
                <?php echo $allInfo->appends(['ruler' => $ruler, 'typer'=>$typer, 'search'=> $search])->render(); ?>
            @endif
        </div>
    </div>
@stop

