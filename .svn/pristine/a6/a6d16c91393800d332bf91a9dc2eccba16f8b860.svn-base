<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '子账户管理';
$tabData = ['tabTitle'=>'childAccount', 'model'=>'personmodel'];
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="/css/external/materialize.css?{{env('timestamp')}}">

    <link type=text/css rel="stylesheet" href="/css/admin/addAdminUser.css?{{env('timestamp')}}" />

@endsection

@section('page_js')
    <script src="/js/admin/addAdminUser.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    <div class="content" >
        <h4 class="leftBold">账号登录设置</h4>
        <input type="hidden" id="userInfo" value="{{isset($info->id)?$info->id:''}}">
        <div class="settingInputArea" style="margin-top: 20px;">
            <div class="settingLabel">联系人</div>
            <input size="18"  value="{{isset($info->role_name)?$info->role_name:''}}" type="text" class="form-control long" id="role_name" placeholder="请输入子账户联系人"/>
        </div>
        <div class="settingInputArea" style="margin-top: 20px;">
            <div class="settingLabel">手机号码</div>
            <input value="{{isset($info->phone)?$info->phone:''}}" onkeyup="this.value=this.value.replace(/\D/g,'')" maxlength="11" type="text" class="form-control long" id="phone" placeholder="请输入子账户联系人手机号"/>
        </div>

        <div class="settingInputArea" style="position:relative;">
            <div class="settingLabel">登录账号<span>*</span></div>
            <input maxlength="18" {{isset($info->id)?'disabled':''}} value="{{isset($info->username)?$info->username:''}}" type="text" data-tips="请输入登录账号" placeholder="6~18位字符，只能包含英文字母、数字、下划线" class="{{isset($info->id)?'forbidEdit':''}} mustSet form-control long" id="username"/>
            <img src="/images/success.png" class="nameIcon checkImg hide"/>
            <p class="inputUsername defaltTips">用户名不能小于6位字符</p>
            <p class="inputUsername defaltTips">用户名不能包含中文或是特殊字符</p>
            <p class="inputUsername defaltTips">用户名已存在</p>
        </div>

        <div class="settingInputArea">
            <div class="settingLabel">登录密码<span>*</span></div>
            <input type="password" maxlength="16" data-tips="请输入登录密码" value="{{isset($info->password)?$info->password:''}}" class="mustSet form-control long" id="password" placeholder="6-16位字符,可包含数字，字母(区分大小写)"/>
            <p class="passUsername defaltTips">6-16位字符,可包含数字、字母(区分大小写)</p>
            <img src="/images/success.png" class="passIcon checkImg hide"/>

        </div>

        <div class="settingInputArea">
            <div class="settingLabel">再次输入密码<span>*</span></div>
            <input type="password" maxlength="16" data-tips="请再次输入登录密码" value="{{isset($info->password)?$info->password:''}}" class="mustSet form-control long" id="repassword" placeholder="请再次输入登录密码"/>
            <p class="passUsername defaltTips">两次密码输入不相同</p>
            <img src="/images/success.png" class="repassIcon checkImg hide"/>

        </div>

        {{--<div class="settingInputArea">--}}
            {{--<div class="settingLabel">添加人</div>--}}
            {{--<input type="text" class="form-control long" id="adder" placeholder="请输入添加人昵称"/>--}}
        {{--</div>--}}

        <h4 class="leftBold">权限设置(请勾选该角色可以操作的模块.)</h4>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[1]->is_chose) ? $data[1]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[1]->id}}"/>
                <label for="{{$data[1]->id}}" class="setTopPri mytext">{{$data[1]->pri_name}}</label>
            </div>
            @foreach($data[1]->child as $childkey=>$child)
            <div class="setChildPriBox checkArea">
                <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
            </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[3]->is_chose) ? $data[3]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[3]->id}}"/>
                <label for="{{$data[3]->id}}" class="setTopPri mytext">{{$data[3]->pri_name}}</label>
            </div>
            @foreach($data[3]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[2]->is_chose) ? $data[2]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[2]->id}}"/>
                <label for="{{$data[2]->id}}" class="setTopPri mytext">{{$data[2]->pri_name}}</label>
            </div>
            @foreach($data[2]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[4]->is_chose) ? $data[4]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[4]->id}}"/>
                <label for="{{$data[4]->id}}" class="setTopPri mytext">{{$data[4]->pri_name}}</label>
            </div>
            @foreach($data[4]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[8]->is_chose) ? $data[8]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[8]->id}}"/>
                <label for="{{$data[8]->id}}" class="setTopPri mytext">{{$data[8]->pri_name}}</label>
            </div>
            @foreach($data[8]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[6]->is_chose) ? $data[6]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[6]->id}}"/>
                <label for="{{$data[6]->id}}" class="setTopPri mytext">{{$data[6]->pri_name}}</label>
            </div>
            @foreach($data[6]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[5]->is_chose) ? $data[5]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[5]->id}}"/>
                <label for="{{$data[5]->id}}" class="setTopPri mytext">{{$data[5]->pri_name}}</label>
            </div>
            @foreach($data[5]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[0]->is_chose) ? $data[0]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[0]->id}}"/>
                <label for="{{$data[0]->id}}" class="setTopPri mytext">{{$data[0]->pri_name}}</label>
            </div>
            @foreach($data[0]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>
        <div class="settingInputArea">
            <div class="checkArea">
                <input class="filled-in" {{isset($data[7]->is_chose) ? $data[7]->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$data[7]->id}}"/>
                <label for="{{$data[7]->id}}" class="setTopPri mytext">{{$data[7]->pri_name}}</label>
            </div>
            @foreach($data[7]->child as $childkey=>$child)
                <div class="setChildPriBox checkArea">
                    <input class="filled-in" {{isset($child->is_chose) ? $child->is_chose==1 ? 'checked':'':''}} type="checkbox" id="{{$child->id}}"/>
                    <label for="{{$child->id}}" class="setChildPri mytext">{{$child->pri_name}}</label>
                </div>
            @endforeach
        </div>

        <div class="submitBox">
            <button id="finish" type="button">保存</button>
        </div>
    </div>
@stop

