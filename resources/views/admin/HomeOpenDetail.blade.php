<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/payAdmin.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/homeOpenDetail.js?{{env('timestamp')}}"></script>
    <script>
        {{--search_content = "{{$search_content}}";--}}
                {{--order_attr = "{{$order_attr}}";--}}
            ChannelTitle = "{{$title}}";
        //调到详情页
        function jumpDetail(info)
        {
            var info=info;
            var appId=info.split("|")[0];
            var userId=info.split("|")[1];
            window.location.href='/customerdetail?appId='+appId+'&userId='+userId;
        }
    </script>
@endsection




@section('base_title')
    {{--<span style="font-size: 18px">财务管理</span>--}}
@stop

@section('base_mainContent')


    <div >
        {{--<div style="margin-top: 10px;height: 60px;line-height: 60px">--}}
            {{--<div style="float: right">--}}
                {{--<button class="btn btn-default" onclick="" style="margin-bottom: 3px" id="pay_search_btn">搜索</button>--}}
            {{--</div>--}}
            {{--<div style="float: right;">--}}
                {{--<input type="text" class="form-control" aria-label="..." id="order_search_content">--}}
            {{--</div>--}}
            {{--<div style="float: right;margin-right: 10px">--}}
                {{--<select class="form-control" id="order_attr" style="margin-top: 13px">--}}
                    {{--<option value="t_users.wx_nickname">资源名</option>--}}
                    {{--<option value="t_purchase.created_at">时间</option>--}}
                {{--</select>--}}
            {{--</div>--}}
        {{--</div>--}}

        <table class="table table-hover" style="margin-top: 10px;border: 1px solid #ddd;border-left: none; border-right: none">
            <thead>
            <tr>
                <th>资源名称</th>
                <th>付费类型</th>
                <th>资源类型</th>
                <th>开通量</th>

            </tr>
            </thead>
            <tbody>
            @foreach($results as $key=>$value)
                <tr>
                    <td style="text-align: center">{{$value->title}}</td>
                    @if($value->payment_type==2)
                        <td>单笔</td>
                    @else
                        <td>专栏</td>
                    @endif
                    @if($value->type==4)
                        <td>专栏</td>
                    @elseif($value->type==3)
                        <td>图文</td>
                    @elseif($value->type==2)
                        <td>视频</td>
                    @else
                        <td>音频</td>
                    @endif
                    <td>{{$idArray[$key]}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@stop


