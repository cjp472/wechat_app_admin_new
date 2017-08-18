<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/admin/communityOperate/typeSelect.css?{{env('timestamp')}}">
@stop

@section('page_js')
    <script type="text/javascript" src="../js/admin/communityOperate/typeSelect.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    <input id="admin_data" type="hidden" data-question_answer_type="0">

    <div class="pageTopTitle">社群运营</div>
    <div class="pageVessel">


        @if( session("access")["120"] == 1)
            @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("has_community", "app_module"))
                <div class="selectItem smallCommunity">
                    <div class="PartIcon"><img src="../images/admin/communityOperate/small_community.png" alt="图标"></div>
                    <div class="PartWord">小社群</div>
                </div>
            @endif
        @endif

        @if( session("access")["121"] == 1)
            <div class="selectItem askAndQuestion">
                <div class="PartIcon"><img src="../images/admin/communityOperate/ask_question.png" alt="图标"></div>
                <div class="PartWord">付费问答</div>
            </div>
        @endif

        @if( session("access")["119"] == 1)
            <div class="selectItem activity">
                <div class="PartIcon"><img src="../images/admin/communityOperate/activity_manage.png" alt="图标"></div>
                <div class="PartWord">活动管理</div>
            </div>
        @endif

        @if(\App\Http\Controllers\Tools\AppUtils::isWhiltList(\App\Http\Controllers\Tools\AppUtils::getAppID()))
            <div class="selectItem exerciseBook">
                <div class="PartIcon"><img src="/images/admin/communityOperate/icon-homework.png" alt="图标"></div>
                <div class="PartWord">作业本</div>
            </div>
        @endif

        @if( session("access")["118"] == 1)
            <div class="selectItem comment">
                <div class="PartIcon"><img src="../images/admin/communityOperate/comment_interact.png" alt="图标"></div>
                <div class="PartWord">评论互动</div>
            </div>
        @endif


    </div>




@stop



