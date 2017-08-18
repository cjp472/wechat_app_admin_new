<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/bootstrap-datetimepicker.min.css">
    <link type=text/css rel="stylesheet" href="../css/admin/visitSearch.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/external/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="../js/admin/visitSearch.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')

    <div class="visitTitle">
       {{--<span style="float: left;">{{$search_attr}} &nbsp; 搜索</span>--}}
        <span style="float: right;color:#ff0000;">统计时间段（ {{$start_time}} -- {{$end_time}} ）</span>
        {{--@if($time_warning)--}}
            {{--<span style="float: right; margin-right: 180px; ">(？ {{$search_time}} ？）{{$time_warning}}</span>--}}
        {{--@endif--}}
    </div>

    <div >

        <div class="searchArea">
            <div style="float: right">
                <button class="btn btn-default" style="margin-bottom: 3px" id="searchButton">搜索</button>
            </div>
            <div style="float: right;">
                <input type="text" class="form-control" aria-label="..." id="search_url" placeholder="请输入链接" style="width: 600px;"/>
            </div>
            {{--<div class="searchSelectArea">--}}
                {{--<select class="form-control" id="search_attr">--}}
                    {{--<option value="PV" selected="selected">PV</option>--}}
                    {{--<option value="UV" selected="selected">UV</option>--}}
                {{--</select>--}}
            {{--</div>--}}
            {{--<div style="float: right;margin-right: 5px;">--}}
                {{--<input type="text" class="form-control" aria-label="..." id="top" placeholder="Top" style="width: 50px;"/>--}}
            {{--</div>--}}
            <div style="float: right;margin-right: 5px;">
                <input type="text" class="form-control" id="search_time" value="{{$search_time}}" placeholder="请输入时间" />
            </div>
            <div style="float:right;height:100%;line-height:34px;margin-right: 5px;">统计时间：</div>
            {{--<div style="float:left;height:100%;line-height:34px;">@if($countSum)总计({{$search_attr}})：--}}
                {{--<span style="color:#1E90FF;">{{$countSum}}</span>--}}
                {{--@endif--}}
            {{--</div>--}}
        </div>

        <table class="table table-hover" style="margin-top: 10px;border: 1px solid #ddd;border-left: none; border-right: none">
            <thead>
                <tr>
                    <th class="url">URL地址</th>
                    <th style="width: 120px;">PV</th>
                    <th style="width: 120px;">UV</th>
                </tr>
            </thead>
            <tbody>
            @if($search)
                @foreach($search as $value)
                    <tr>
                        <td class="url">{{$value->target_url}}</td>
                        <td style="text-align: center;">{{$value->sumpv}}</td>
                        <td style="text-align: center;">{{$value->sumuv}}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>


    </div>
@stop


