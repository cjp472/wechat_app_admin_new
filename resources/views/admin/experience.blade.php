<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/experience.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/experience.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    {{--头部--}}

    <div class="tab_div">
        <div class="tab_type tab_active" id="tab_invite">试听链接</div>
    </div>

    <div class="header">
        <div class="searchArea">
            <button id="addExperience" type="button">生成试听链接</button>
        </div>
    </div>

   {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>链接名称</th>
                    <th>试听时长（天）</th>
                    <th>试听链接</th>
                    <th>领取量</th>
                    <th>开通量</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{$value['purchase_name']}}</td>
                        <td>{{$value['period']}}</td>
                        <td>{{$value['url']}}</td>
                        <td>{{$value['record_num']}}</td>
                        <td>{{$value['purchase_num']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="list-page">
            @if(empty($search))
                <?php echo $allInfo->render(); ?>
            @else
                <?php echo $allInfo->render(); ?>
            @endif
        </div>
    </div>
@stop
