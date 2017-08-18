<?php
$pageData = [];
$pageData['sideActive'] = 'resourceList';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    {{-- 扁平化框架 --}}
    <link href="../css/external/materialize.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
    {{--弹窗--}}
    <link href="../css/external/xcConfirm.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
    {{--页面样式--}}
    <link href="../css/admin/resManage/resAdd.css?{{env('timestamp')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('page_js')
    {{--materializeUI--}}
    <script src="../js/external/materialize.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--页面逻辑--}}
    <script src="../js/admin/resManage/resTypeSelect.js?{{env('timestamp')}}"></script>
@endsection
@section('base_mainContent')
    <div class="pageTopTitle">
        <a href="javascript:void(0)" id="getBack">
        @if($upload_channel_type==1)
            单品列表
        @elseif($upload_channel_type==2)
            专栏详情
        @elseif($upload_channel_type==3)
            会员详情
        @endif
        </a>
        &nbsp;&nbsp;>&nbsp;&nbsp;新增
    </div>
    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
            <div class="resAddPartTitle">
                <div class="titleLine"></div>
                <div class="AddPartTitleWords">
                    选择单品类型
                </div>
            </div>
        </div>
        <div class="resTypeGrop">
            <div class="resType resType1">
                <div class="resTypeIcon"><img src="../images/admin/resManage/icon_voice.png" alt="资源类型图标">
                </div>
                <div class="resTypeTitle">音频</div>
            </div>
            <div class="resType resType2">
                <div class="resTypeIcon"><img src="../images/admin/resManage/icon_video.png" alt="资源类型图标">
                </div>
                <div class="resTypeTitle">视频</div>
            </div>
            <div class="resType resType3">
                <div class="resTypeIcon"><img src="../images/admin/resManage/icon_picture.png" alt="资源类型图标">
                </div>
                <div class="resTypeTitle">图文</div>
            </div>
            @if($upload_channel_type != 1)
            <div class="resType resType4">
                <div class="resTypeIcon"><img src="../images/admin/resManage/icon_live.png" alt="资源类型图标">
                </div>
                <div class="resTypeTitle">直播</div>
            </div>
            @endif
        </div>
        <div class="boxLine">
            {{--<div class="typeSelectCancel btnMid xeBtnDefault waves-effect">取消</div>--}}
            {{--<div class="typeSelectNext btnMid btnBlue waves-effect">下一步</div>--}}
        </div>
    </div>
@stop