<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
$tabData = ['tabTitle'=>'accountManage'];
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/addAdminUser.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script src="../js/admin/editAdminUser.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')

    @include("admin.accountSetting.baseTab", $tabData)

    <div class="content" >
        <h4>账号登录设置</h4>

        <div class="settingInputArea" style="margin-top: 20px;">
            <div class="settingLabel">角色名</div>
            <input type="text" class="form-control long" id="role_name" placeholder="请输入角色名" value="{{$user->role_name}}"/>
        </div>

        <div class="settingInputArea" style="position:relative;">
            <div class="settingLabel">账户</div>
            <input type="text" class="form-control long" id="username"
           placeholder="请输入账号" value="{{$user->username}}" disabled="disabled"/>
        </div>

        <div class="settingInputArea">
            <div class="settingLabel">密码</div>
            <input type="password" class="form-control long" id="password" placeholder="请输入密码" />
        </div>

        <div class="settingInputArea">
            <div class="settingLabel">确认密码</div>
            <input type="password" class="form-control long" id="passwordConfirm" placeholder="请再次输入密码" />
        </div>

        {{--<div class="settingInputArea">--}}
            {{--<div class="settingLabel">添加者</div>--}}
            {{--<input type="text" class="form-control long" id="adder" placeholder="请输入添加者" value="{{$user->adder}}" disabled="disabled"/>--}}
        {{--</div>--}}

        <h4 style="padding-left: 30px;margin-top: 30px;">权限设置(请勾选该角色可以操作的模块)</h4>
        <div class="settingInputArea">
            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="dashMenu"/>
                </div>
                <div class="mytext" style="font-weight: bold;">仪表盘：</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="dashboard_admin_access"
                    @if($group->dashboard_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">仪表盘</div>
            </div>
        </div>

        <div class="settingInputArea">
            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="contentMenu"/>
                </div>
                <div class="mytext" style="font-weight: bold;">内容管理：</div>
            </div>

            {{--<div class="checkArea">--}}
                {{--<div class="mycheckbox">--}}
                    {{--<input type="checkbox" id="create_content_access"--}}
                   {{--@if($group->create_content == 1) checked="checked" @endif/>--}}
                {{--</div>--}}
                {{--<div class="mytext" >新增</div>--}}
            {{--</div>--}}

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="content_list_access"
                   @if($group->content_list == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">知识商品</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="banner_access"
                   @if($group->banner == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">轮播图</div>
            </div>
        </div>

        <div class="settingInputArea">
            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="userMenu"/>
                </div>
                <div class="mytext" style="font-weight: bold;">用户管理：</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="content_comment_access"
                   @if($group->content_comment == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">用户评论</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="user_list_access"
                   @if($group->user_list == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">用户列表</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="message_admin_access"
                   @if($group->message_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">消息列表</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="feedback_admin_access"
                   @if($group->feedback_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">反馈列表</div>
            </div>
        </div>

        <div class="settingInputArea">
            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="incomeMenu"/>
                </div>
                <div class="mytext" style="font-weight: bold;">收入管理：</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="channel_admin_access"
                   @if($group->channel_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">统计分发</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="invitecode_admin_access"
                   @if($group->invitecode_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">邀请码</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="money_admin_access"
                   @if($group->money_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">财务管理</div>
            </div>
        </div>

        <div class="settingInputArea">
            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="accountMenu"/>
                </div>
                <div class="mytext" style="font-weight: bold;">账号管理：</div>
            </div>

            <div class="checkArea">
                <div class="mycheckbox">
                    <input type="checkbox" id="account_admin_access"
                   @if($group->account_admin == 1) checked="checked" @endif/>
                </div>
                <div class="mytext">账号管理</div>
            </div>
        </div>

        <button id="finish" type="button">保存修改</button>
    </div>
@stop

