<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" type="text/css" href="../css/admin/experienceAdd.css?{{env('timestamp')}}" />
    <link rel="stylesheet" type="text/css" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/experienceAdd.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="pageTopTitle">生成试听链接</div>

    <div class="content">
        <div class="experienceInputArea">
            <div class="experienceLabel">链接名称</div>
            <div class="experienceInput">
                <input type="text" class="inputDefault long" placeholder="请输入链接名称" id="purchase_name"/>
            </div>
        </div>

        <div class="experienceInputArea">
            <div class="experienceLabel">试听时长</div>
            <div class="experienceInput">
                <input type="text" class="inputDefault long" placeholder="请输入允许试听时长" id="period"/><span> 天</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <button id="finish" class="btnMid btnBlue">立即生成</button>
    </div>
@stop


