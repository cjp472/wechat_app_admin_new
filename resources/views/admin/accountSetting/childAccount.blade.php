<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '子账户管理';
$tabData = ['tabTitle'=>'childAccount'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountManage.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/accountManage.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)

    <div class="content" style="min-height: 250px;">
        <div class="contentTitle">子账号管理</div>
        {{--新增/修改管理员--}}
        <button type="button" class="greenButton" id="addAdminUser">新增账号</button>
        <table class="table table-hover" style="width: 96%;margin:30px 0 0 20px;">
            <thead>
            <tr>
                <th>昵称</th>
                <th>账号</th>
                {{--<th>添加人</th>--}}
                <th>账户类型</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->role_name==''?'-':$value->role_name}}</td>
                    <td>{{$value->username}}</td>
                    {{--<td>{{$value['adder']}}</td>--}}
                    <td>子账号</td>
                    <td style="width:200px;">
                        <button type="button" class="btn btn-default" onclick="editAdminUser('{{$value->id}}')">编辑</button>
                        <button type="button" class="btn btn-default" onclick="deleteAdminUser('{{$value->id}}')">删除</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            <?php echo $data->render(); ?>
        </div>
    </div>
@stop

@section('base_modal')


@stop

