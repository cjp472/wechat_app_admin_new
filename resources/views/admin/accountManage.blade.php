<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
$tabData = ['tabTitle'=>'accountManage'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountManage.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/utils/formCheck.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/accountManage.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{--联系人块--}}
    <div class="content" >
        <div class="contentTitle">管理员信息</div>

        <div class="infoArea" style="display:none">
            <div class="infoLabel">商户名</div>
            <div class="infoValue">
                <input id="wxnameInput" class="inputDefault editName" type="text" disabled readonly value="{{$merchantConfig->wx_app_name}}">

                <a id="editWxName" href="javascript:void(0)">编辑</a>
            </div>

            <div id="editContent" class="editContent">
                <button id="saveEdit" class="btnMid btnBlue">保存</button>
                <button id="cancleEdit" class="btnMid xeBtnDefault">取消</button>
            </div>
        </div>

        <div class="infoArea">
            <div class="infoLabel">管理员名称</div>
            <div class="infoValue">{{$merchantConfig->name}}</div>
            <a id="check" class="changeBtn">更换管理员</a>
        </div>
        <div class="infoArea">
            <div class="infoLabel">手机号码</div>
            <div class="infoValue">{{$merchantConfig->phone}}</div>
        </div>
        <div class="infoArea">
            <div class="infoLabel">绑定微信</div>
            <div class="infoValue">{{session('nick_name')}}</div>
        </div>
        <div class="infoArea pageFoot">
            <div class="infoLabel">登录账号</div>
            @if(empty($admin->name))
            <div class="infoValue">未设置</div>
            <a id="add">点击设置</a>
            @else
            <div class="infoValue">{{$admin->name}}</div>
            <a id="edit">修改密码</a>
            @endif
        </div>
    </div>


@stop

@section('base_modal')

    {{--编辑修改new--}}
    <div id="editCode" class="popBox" style="display:none">
        <div class="darkScreen"></div>
        <div class="editBox content">
            <div class="boxTitle">修改密码</div>
            <div class="bodyPage">
                <div class="boxItem">
                    <div class="leftTitle">登录账号</div>
                    <span>{{$admin->name}}</span>
                </div>
                <div class="lostCode boxItem">
                    <label class="leftTitle" for="editOldPassword">原始密码</label>
                    <input id="editOldPassword" type="password" class="inputDefault" placeholder="请输入原始密码">
                    {{--<div id="theOrg" class="redMsg">6-16位字符可包含数字，字母（区分大小写）</div>--}}
                    <a href="/admin/changePasswordPage">忘记密码？</a>
                </div>
                <div class="boxItem">
                    <label class="leftTitle" for="editNewPassword">设置新密码</label>
                    <input id="editNewPassword" type="password" class="inputDefault" placeholder="请设置登录密码">
                    <div id="reSet" class="redMsg">6-16位字符可包含数字，字母（区分大小写）</div>
                </div>
                <div class="boxItem">
                    <label class="leftTitle" for="editNewConfirm">再次确认密码</label>
                    <input id="editNewConfirm" type="password" class="inputDefault" placeholder="请再次确认密码">
                    <div id="reSetCheck" class="redMsg">两次密码输入不一致</div>
                </div>
            </div>
            <div class="boxFooter">
                <div class="btnMid xeBtnDefault closeBox">取消</div>
                <div id="editSubmit" class="btnMid btnBlue">保存</div>
            </div>
        </div>
    </div>
{{--登录账号弹窗--}}
    <div id="loginBox" class="popBox" style="display:none">
        <div class="darkScreen"></div>
        <div class="editBox content">
            <div class="boxTitle">设置登录账号</div>
            <div class="bodyPage">
                <div class="boxItem">
                    <label for="addName" class="leftTitle">登录账号</label>
                    <input id="addName" type="text" class="inputDefault" placeholder="请设置登录账号，可设置数字、字母">
                    <div id="nameMsg" class="redMsg">6~18位字符，只能包含字母、数字、下划线</div>
                    <div id="nameExist" class="redMsg">用户名已存在</div>
                    <img src="../images/success.png" class="checkImg hide"/>
                    <img src="../images/error.png" class="checkImg hide"/>
                </div>
                <div class="boxItem">
                    <label for="addPassword" class="leftTitle">设置密码</label>
                    <input id="addPassword" type="password" class="inputDefault" placeholder="请设置登录密码">
                    <div id="addPsd" class="redMsg">6-16位字符可包含数字，字母（区分大小写）</div>
                </div>
                <div class="boxItem">
                    <label for="addConfirm" class="leftTitle">再次确认密码</label>
                    <input id="addConfirm" type="password" class="inputDefault" placeholder="请再次确认登录密码">
                    <div id="rePsd" class="redMsg">两次密码输入不一致</div>
                </div>
            </div>
            <div class="boxFooter">
                <div class="btnMid xeBtnDefault closeBox">取消</div>
                <div id="addSubmit" class="btnMid btnBlue">保存</div>
            </div>
        </div>
    </div>
{{--手机验证弹窗--}}
    <div id="phoneCheck" class="popBox" style="display:none">
        <div class="darkScreen"></div>
        <div class="editBox content">
            <div class="boxTitle">本次操作需要管理员手机验证</div>
            <div class="bodyPage">
                <div class="boxItem">
                    <span for="addName" class="leftTitle">手机号</span>
                    <span id="phone">{{$merchantConfig->phone}}</span>
                </div>
                <div class="boxItem currentCode">
                    <span class="leftTitle">验证码</span>
                    <input type="text" class="inputDefault identifyCodeInput" placeholder="请输入验证码">
                    <div class="btnBlue btnSmall getCodeBtn">获取验证码</div>
                    <img src="/images/admin/icon-successful.svg" class="successTip"/>
                    <img src="/images/admin/icon-error.svg" class="errorTip"/>
                </div>
            </div>
            <div class="boxFooter">
                <div class="btnMid xeBtnDefault closeBox">取消</div>
                <a id="phoneCheck" class="btnMid btnBlue">确定</a>
            </div>
        </div>
    </div>

@stop

