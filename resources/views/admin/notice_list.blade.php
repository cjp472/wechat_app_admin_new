<?php
$pageData = [];
//$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '通知中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type="text/css" rel="stylesheet" href="../css/admin/noticeList.css?{{env('timestamp')}}">
@stop

@section('page_js')
    <script type="text/javascript" src="../js/admin/noticeList.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')
    <div class="noticeHeader">通知列表</div>
    <div class="noticeListContent">
            @foreach($notice_list as $key => $value)
             <div class="noticeListPart" data-id="{{$value->notice_id}}" data-viewstate="{{$value->view_state}}">
                 <div class="noticeListPart1">
                 <div class="noticePartTitle">{{$value->title}} @if($value->view_state==0)<span class="unreadPoint"></span>@endif</div>
                 <div class="noticeListTime">{{$value->notice_time}}<span class="listSdIcon"></span></div>
                 </div>
                 <div class="noticePartContent" style="display: none">
                     <div class="noticeDetail">{!! $value->notice_detail !!}</div>
                     <a href="{{$value->notice_link}}" target="_blank" class="noticePartLink">{{$value->link_name}}</a>
                 </div>
             </div>
            @endforeach
                {{--页标--}}
                <div class="list-page">
                        <?php echo $notice_list->appends(array('show_all' => '1'))->render(); ?>
                </div>
    </div>
@stop
