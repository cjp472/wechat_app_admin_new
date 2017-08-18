<?php
$pageData = [];
$pageData['sideActive'] = 'customerManage';
$pageData['barTitle'] = '用户管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/admin/messageEdit.css?{{env('timestamp')}}" />
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js"></script>
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/messageEdit.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')

    <div class="pageTopTitle"><a href="/message">消息列表</a> &gt; 消息编辑</div>

    <div class="content">
        <div class="messageInputArea" style="margin-top: 30px;">
            <div class="messageInputLabel">推送时间</div>
            <div class="messageInput">
                <input class="form-control long" name="send_at" value="{{$sendAt}}"/>
            </div>
        </div>

        <div class="messageInputArea" style="margin-top: 25px;">
            <div class="messageInputLabel">发送人</div>
            <div class="messageInput">
                <input class="form-control long" name="send_nick_name" placeholder="请输入发送人昵称" value="{{$sendNickName}}"/>
            </div>
        </div>

        <div class="messageInputArea">
            <div class="messageInputLabel">消息内容</div>
            <div class="messageInput">
                <textarea class="form-control" name="content" placeholder="请输入消息内容" rows="8" cols="80">{{$content}}
                </textarea>
            </div>
        </div>
    </div>

    <div class="footer">
        <button class="editButton">保存消息</button>
    </div>
@stop

