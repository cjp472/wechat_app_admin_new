<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/inviteCode.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/giftCode.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    {{--头部--}}

    <div class="tab_div">
        <div class="tab_type" id="tab_invite">邀请码</div>
        @if($appmodule['group_buy'])<div class="tab_type" id="tab_group">团购</div>@endif
        @if($appmodule['gift_buy'])<div class="tab_type tab_active" id="tab_gift">购买赠送</div>@endif
    </div>

    <div class="header">
        {{--搜索--}}
        <div class="searchArea">
            {{--<button id="addInviteCode" type="button">生成邀请码</button>--}}

            <div class="searchButtonArea">
                <button class="btn btn-default" id="searchButton">搜索</button>
            </div>

            <div class='searchInputArea'>
                <input type="text" class="form-control" aria-label="..." name="search" />
            </div>

            <div class="searchSelectArea">
                <select class="form-control" name="ruler">
                    <option value="0" selected="selected">名称</option>
                    <option value="1">批次</option>
                    <option value="2">创建时间</option>
                    <option value="3">购买人</option>
                </select>
            </div>

        </div>

    </div>

   {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>批次</th>
                    <th>名称</th>
                    <th>标题</th>
                    <th>买赠数量</th>
                    <th>使用数量</th>
                    <th>购买人</th>
                    <th>专栏/单个</th>
                    <th>生效时间</th>
                    <th>失效时间</th>
                    <th>状态</th>

                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td><a href="/gift_list?bid={{$value['id']}}">{{$value['name']}}</a></td>
                        <td>{{$value['card_title']}}</td>
                        <td>{{$value['allCount']}}</td>
                        <td>{{$value['useCount']}}</td>

                        <td><a href="javascript:;" onclick="jumpDetail('{{$value['app_id']}}|{{$value['buy_user_id']}}')">{{$value['buy_user_name']}}</a></td>

                        <td>{{$value['target_name']}}</td>
                        <td>{{$value['start_at']}}</td>
                        <td>{{$value['stop_at']}}</td>
                        <td>{{$value['state']}}</td>

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
