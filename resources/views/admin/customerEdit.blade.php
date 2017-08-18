<?php
$pageData = [];
$pageData['sideActive'] = 'user_list';
$pageData['barTitle'] = '详细信息';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/admin/customerEdit.css?{{env('timestamp')}}" />
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/customerEdit.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="header"><h3>详细信息</h3></div>

    <div class="content">
        <div class="editInputArea" style="margin-top: 40px;">
            <div class="editLabel">真实姓名</div>
            <input type="text" name="name" class="form-control long" placeholder="请输入您的姓名" value="{{$name}}"/>
        </div>
        <div class="editInputArea">
            <div class="editLabel">地址</div>
            <input type="text" name="address" class="form-control long" placeholder="请输入您的地址" value="{{$address}}"/>
        </div>
        <div class="editInputArea">
            <div class="editLabel">公司</div>
            <input type="text" name="company" class="form-control long" placeholder="请输入您的公司" value="{{$company}}"/>
        </div>
        <div class="editInputArea">
            <div class="editLabel">职位</div>
            <input type="text" name="job" class="form-control long" placeholder="请输入您的职位" value="{{$job}}"/>
        </div>
        <div class="editInputArea">
            <div class="editLabel">行业</div>
            <input type="text" name="industry" class="form-control long" placeholder="行业" value="{{$industry}}"/>
        </div>

        <button type="button" class="editSaveButton">提交信息</button>
    </div>
@stop

