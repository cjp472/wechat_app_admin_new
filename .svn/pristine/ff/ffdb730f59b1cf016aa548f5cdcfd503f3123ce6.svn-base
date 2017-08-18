<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/zhanghuSetting.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/zhanghuSetting.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    {{--标题--}}
    <div class="header">
        <ul>
            <li><a href="/accountmanage">注册信息</a></li>
            <li><a href="/smallprogramsetting">小程序设置</a></li>
            <li style="border-bottom:2px solid #3a7bd5;"><a href="/zhanghusetting">账号管理</a></li>
            <li><a href="/h5setting">公众号设置</a></li>
        </ul>
    </div>

    {{--账号设置--}}
    <div class="content" >
        {{--微信绑定--}}
        <div class="wechatLogoDiv">
            <img src="../images/wechat.png" alt="微信信息" title="微信信息"/>
            <span class="wechatWords">已绑定管理员微信号</span>
            <span class="wechatName">{{session('nick_name')}}</span>
        </div>
        <button type="button" class="btn btn-primary" id="addAdminUser">新增运营者</button>

        {{--新增/修改管理员--}}
        @if(empty($admin->name))
        <button type="button" class="btn btn-primary adjust" data-target="#addPrimaryModal"
        data-toggle="modal">绑定管理员账号</button>
        @else
        <button type="button" class="btn btn-primary adjust" data-target="#editPrimaryModal"
        data-toggle="modal">编辑管理员账号</button>
        @endif

        <table class="table table-hover" style="width: 97%;margin:30px 0 0 30px;">
            <thead>
                <tr>
                    <th>昵称</th>
                    <th>账号</th>
                    <th>添加人</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{$value['role_name']}}</td>
                        <td>{{$value['username']}}</td>
                        <td>{{$value['adder']}}</td>
                        <td style="width:200px;">
                            <button type="button" class="btn btn-default" onclick="editAdminUser('{{$value['id']}}')">编辑</button>
                            <button type="button" class="btn btn-default" onclick="deleteAdminUser('{{$value['id']}}')">删除</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            <?php echo $allInfo->render(); ?>
        </div>
    </div>
@stop

@section('base_modal')
    {{--新增--}}
    <div class="modal fade" id="addPrimaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content" style="height: 450px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">绑定管理员账号</span></div>
                </div>

                <div class="modal-body" style="height: 300px">
                    <div class="inputArea" style="margin-top: 50px;position: relative;">
                        <div class="inputLabel">账号</div>
                        <input type="text" class="form-control long" id="addName" placeholder="请输入您想绑定的账号" />
                        <img src="../images/success.png" class="checkImg hide"/>
                        <img src="../images/error.png" class="checkImg hide"/>
                    </div>

                    <div class="inputArea">
                        <div class="inputLabel">密码</div>
                        <input type="password" class="form-control long" id="addPassword" placeholder="请输入密码"/>
                    </div>

                    <div class="inputArea">
                        <div class="inputLabel">再次输入密码</div>
                        <input type="password" class="form-control long" id="addConfirm" placeholder="请再次输入密码"/>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 20px;">
                    <button type="button" class="btn btn-primary btn-blue" id="addPrimarySubmit">保存</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    {{--编辑--}}
    <div class="modal fade" id="editPrimaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content" style="height: 450px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">编辑管理员账号</span></div>
                </div>

                <div class="modal-body" style="height: 300px">
                    <div class="inputArea">
                        <div class="inputLabel">账号</div>
                        <input type="text" class="form-control long" style="border: none;
                        box-shadow: none;cursor: default;" disabled="disabled" id="editName" value="{{$admin->name}}" />
                    </div>

                    <div class="inputArea">
                        <div class="inputLabel">旧密码</div>
                        <input type="password" class="form-control long" id="editOldPassword" placeholder="请输入旧的密码"/>
                    </div>

                    <div class="inputArea">
                        <div class="inputLabel">新密码</div>
                        <input type="password" class="form-control long" id="editNewPassword" placeholder="请输入新的密码"/>
                    </div>

                    <div class="inputArea">
                        <div class="inputLabel">再次输入密码</div>
                        <input type="password" class="form-control long" id="editNewConfirm" placeholder="请再次输入新的密码"/>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 20px;">
                    <button type="button" class="btn btn-primary btn-blue" id="editPrimarySubmit">保存</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@stop

