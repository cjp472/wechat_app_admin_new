<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/admin/addInviteCode.css?{{env('timestamp')}}" />
    {{--时间选择器--}}
    <link rel="stylesheet" type="text/css" href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}"  />
    {{--弹框--}}
    <link rel="stylesheet" type="text/css" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>
    {{--弹窗--}}
    <script src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/addInviteCode.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="pageTopTitle">生成邀请码</div>

    <div class="content">
        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">批次名称</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault long" placeholder="请输入批次备注信息" id="name"/>
            </div>
        </div>

        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">邀请码数量</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault long" placeholder="请输入邀请码数量（不大于1000）" id="count"/>
            </div>
        </div>

        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">有效时间</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault" style="width:160px" placeholder="生效时间" id="start_at"/>
            </div>
            <div class="zhi">至</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault" style="width:160px" placeholder="失效时间" id="stop_at"/>
            </div>
        </div>

        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">对应内容</div>

            <div class="inviteCodeInput">
                <select class="inputDefault" id="typeSelect" style="width:100px">
                    <option value="0" selected="selected">专栏</option>
                    <option value="1">音频</option>
                    <option value="2">视频</option>
                    <option value="3">图文</option>
                    <option value="4">直播</option>
                    <option value="5">会员</option>
                    <option value="7">社群</option>
                </select>
            </div>

            <div class="inviteCodeInput" style="margin-left: 20px;">
                <select class="inputDefault" id="targetSelect" style="width: 180px;">
                @foreach($packages as $key => $value)
                    <option value="{{$value->id}}" price="{{$value->price}}" img_url="{{$value->img_url}}">
                    {{$value->name}}</option>
                @endforeach
                </select>
            </div>

        </div>

        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">邀请码标题</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault long" placeholder="请输入邀请码标题" id="card_title" />
            </div>
        </div>

        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">使用须知</div>
            <div class="inviteCodeInput">
                <textarea class="inputDefault" style="height:auto;" id="card_desc" placeholder="请输入邀请码的使用规则"
                rows="9" cols="80"></textarea>
            </div>
        </div>

        @if($result)
         @if($result[0]->group_buy == 1)
            <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">购买人</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault long" placeholder="请输入购买人信息" id="purchaser" readonly />
                <button class="btn btn-default" data-target="#zbModal" data-toggle="modal">添加用户</button>
            </div>
            </div>

            <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">团购信息</div>

            <div class="inviteCodeInput">
                <select class="inputDefault" id="is_group">
                    <option value="0" selected="selected">无</option>
                    <option value="1">有</option>
                </select>
            </div>

            <div class="inviteCodeInput" style="margin-left: 20px;">
                <select class="inputDefault hide" id="group_select" >
                    @foreach($group_data as $key => $value)
                        <option value="{{$value->id}}" app_id="{{$value->app_id}}">
                            {{$value->sell_name}}</option>
                    @endforeach
                </select>
            </div>
            </div>
         @endif
        @endif


        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">申请人</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault long" placeholder="请输入申请人信息" id="applier" />
            </div>
        </div>

        <div class="inviteCodeInputArea">
            <div class="inviteCodeLabel">申请原因</div>
            <div class="inviteCodeInput">
                <input type="text" class="inputDefault long" placeholder="请输入申请邀请码原因" id="reason" />
            </div>
        </div>
    </div>

    <div class="footer">
        <button id="finish" class="btnMid btnBlue">立即生成</button>
    </div>
@stop

@section('base_modal')

    {{--发消息--}}
    <div class="modal fade" id="zbModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content" style="height: 450px;width: 700px;padding-left: 10px;padding-right: 10px">
                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">搜索讲师</span></div>
                </div>

                <div class="modal-body" style="height: 320px;overflow-y:scroll;overflow-x:hidden;">
                    <div id="searchArea">
                        <button class="btn btn-default" id="searchButton">搜索</button>
                        <input type="text" class="inputDefault long" id="search" placeholder="请输入讲师的昵称或者手机号"/>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 0px;">
                    <button type="button" class="btn btn-primary btn-blue">选择</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

@stop

