<?php
$pageData = [];
$pageData['sideActive'] = 'customerManage';
$pageData['barTitle'] = '用户管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/admin/messageAdd.css?{{env('timestamp')}}" />
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js"></script>
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/messageAdd.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    <div class="header"><h3>消息推送</h3></div>

    <div class="content">
        <div class="messageInputArea" style="margin-top: 30px;">
            <div class="messageInputLabel">推送时间</div>
            <div class="messageInput">
                <input class="form-control long" name="send_at" />
            </div>
        </div>

        <div class="messageInputArea" style="margin-top: 25px;">
            <div class="messageInputLabel">发送人</div>
            <div class="messageInput">
                <input class="form-control long" name="send_nick_name" placeholder="请输入发送人昵称"/>
            </div>
        </div>

        @if($result)
            @if($result[0]->message == 1)
            <div class="messageInputArea" style="margin-top: 25px;">
            <div class="messageInputLabel">小纸条(可选)</div>
            <div class="messageInput">
                <select class="form-control"  id="message_selector">
                    <option value="">————选择————</option>
                    @foreach($audioList as $key=>$value)
                        <option value="{{ $value->id }}">{{ $value->title }}</option>
                    @endforeach
                </select>
            </div>
            </div>
            @endif
        @endif

        <div class="messageInputArea" style="margin-top: 25px;">
            <div class="messageInputLabel">消息内容</div>
            <div class="messageInput">
                <textarea class="form-control" name="content" placeholder="请输入消息内容" rows="8" cols="80"></textarea>
            </div>
        </div>

        <div class="messageInputArea" style="margin-top: 150px;margin-bottom: 5px">
            <div class="messageInputLabel" style="width: 300px">发送带链接的消息（如不需要可不填）</div>
        </div>

        <div class="messageInputArea" style="margin-top: 5px;">
            <div class="messageInputLabel">链接名称</div>
            <div class="messageInput">
                <input class="form-control long" placeholder="例：点我抢福利" id="url_title"/>
            </div>
        </div>

        <div class="messageInputArea">
            <div class="messageInputLabel">跳转链接</div>
            <div class="upload_input_div">
                <div style="float: left">
                    <select class="form-control"  id="type_selector">
                        <option value="audio">音频</option>
                        <option value="video">视频</option>
                        <option value="image_text">图文</option>
                        <option value="alive">直播</option>
                        <option value="package">专栏</option>
                        <option value="h5">外部链接</option>
                        <option value="no_jump">无跳转</option>
                    </select>
                </div>
                <div style="float: left;margin-left: 15px">
                    <select class="form-control"  id="sub_selector">
                        @foreach($audioList as $key=>$value)
                            <option value="{{ $value->id }}">{{ $value->title }}</option>
                        @endforeach
                    </select>
                    <input class="form-control hide" id="sub_input" style="width: 410px"/>
                </div>
            </div>
        </div>

    </div>

    {{--检测网址的输入错误时，添加文字提醒--}}
    <div class="http_error_tip hide">
        您输入的网址不符合要求，输入网址请以 http:// 或 https:// 开头
    </div>
    {{--私人消息新添加外链-end--}}

    <div class="footer">
        <button class="addButton">推送消息</button>
    </div>
@stop

