<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/inviteCode.css?{{env('timestamp')}}">
    {{--<link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css">--}}
    {{--弹出提示--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
@endsection


@section('page_js')

    {{--<script src="../js/external/xcConfirm.js"></script>--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/giftList.js?{{env('timestamp')}}"></script>
    {{--弹出提示--}}

@endsection



@section('base_mainContent')
    {{--头部--}}


        <div class="header">
        {{--搜索--}}
        <div class="searchArea">
            <span style="font-size: 16px;">使用记录</span>

            <div class="searchButtonArea">
                <button class="btn btn-default" id="searchButton">搜索</button>
            </div>

            <div class='searchInputArea'>
                <input type="text" class="form-control" aria-label="..." name="search" />
            </div>

            <div class="searchSelectArea">
                <select class="form-control" name="ruler">
                    <option value="0" selected="selected">用户名称</option>
                    <option value="1">邀请码</option>

                </select>
            </div>

        </div>

    </div>

   {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>邀请码</th>
                    <th>用户头像</th>
                    <th>用户名称</th>
                    <th>使用状态</th>
                    <th>卡名</th>
                    <th>赠言</th>
                    <th>使用时间</th>
                    <th>操作</th>

                </tr>
            </thead>
            <tbody>
            @foreach($data as $key => $value)
                <tr>
                    <td>{{$value['code']}}</td>
                    <td><img src="{{$value['wx_avatar']}}" class="avatar" /> </td>
                    <td>@if($value['wx_nickname'])
                        <a href="javascript:;" onclick="jumpDetail('{{$app_id}}|{{$value['user_id']}}')">{{$value['wx_nickname']}}</a>
                        @endif
                    </td>
                    <td>{{$value['state']}}</td>
                    <td>{{$value['card_name']}}</td>
                    <td>{{$value['card_wish']}}</td>
                    <td>{{$value['used_at']}}</td>
                    <td>
                       @if ($value['state'] === '未使用')
                                <button class="btn btn-default toInvalid"  data-code="{{$value['code']}}">作废</button>
                       @else
                                ---
                       @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            @if(empty($search))
                <?php echo $allInfo->appends(['ruler' => $ruler])->render(); ?>
            @else
                <?php echo $allInfo->appends(['ruler' => $ruler, 'search'=> $search])->render(); ?>
            @endif
        </div>
    </div>
@stop
