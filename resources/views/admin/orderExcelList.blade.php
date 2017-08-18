<?php
$pageData = [];
$pageData['sideActive'] = 'money_admin';
$pageData['barTitle'] = '财务管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/selectTime.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/payAdmin.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/excelList.js?{{env('timestamp')}}"></script>

@endsection
{{--dump($search_array)--}}

@section('base_title')
    {{--<span style="font-size: 18px">财务管理</span>--}}
@stop

@section('base_mainContent')
    <div class="packageDetailHeader">
        <a href="/order_list">订单列表</a> &gt; 导出订单详情
    </div>

    <div >

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>文件名</th>
                    <th>内容类型</th>
                    <th>开始时间</th>
                    <th>结束时间</th>
                    <th>导出时间</th>
                </tr>
            </thead>
            <tbody class="pay_tbody">
                @foreach($ListInfo as $v)
                <tr>
                    <td>{{$v->title}}</td>
                    <td>{{$v->type}}</td>
                    <td>{{$v->start_time}}</td>
                    <td>{{$v->end_time}}</td>
                    <td>
                       {{$v->created_at}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="list-page">
            {!! $ListInfo->render() !!}
        </div>
    </div>
@stop
