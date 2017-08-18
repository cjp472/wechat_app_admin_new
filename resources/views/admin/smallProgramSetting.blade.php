<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/smallProgramSetting.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/smallProgramSetting.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')

    <div class="content">
        <div class="imgArea">
            <img src="../images/small_program.png" alt="功能搭建中" title="功能搭建中" />
            <div class="building">功能升级中...</div>
        </div>
    </div>

@stop

