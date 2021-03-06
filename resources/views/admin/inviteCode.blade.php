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
    <script type="text/javascript" src="../js/admin/inviteCode.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    {{--头部--}}

    <div class="tab_div">
        <div class="tab_type tab_active" id="tab_invite">邀请码</div>
        @if($appmodule['group_buy'])<div class="tab_type" id="tab_group">团购</div>@endif
        @if($appmodule['gift_buy'])<div class="tab_type" id="tab_gift">购买赠送</div>@endif
        <div class="pull-right"><a href="/helpCenter/problem?document_id=d_58f0bdbc60a8c_UIUTYrZ7" target="_blank">邀请码教程</a></div>
    </div>

    <div class="header">
        {{--搜索--}}
        <div class="searchArea">
            <button id="addInviteCode" type="button">生成邀请码</button>
            <a href="javascript:void(0)" id="couponGuide">如何批量邀请用户</a>
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
                    <th>批次名称</th>
                    <th>邀请码标题</th>
                    <th>邀请码数量</th>
                    <th>使用数量</th>
                    {{--<th>类型</th>--}}
                    {{--<th>购买人</th>--}}
                    <th>专栏/单个</th>
                    <th>申请人</th>
                    <th>生效时间</th>
                    <th>失效时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td><a href="/invite_list?bid={{$value['id']}}">{{$value['name']}}</a></td>
                        <td>{{$value['card_title']}}</td>
                        <td>{{$value['allCount']}}</td>
                        <td>{{$value['useCount']}}</td>

                        {{--@if(empty($value['buy_user_id']))--}}
                            {{--<td>普通</td>--}}
                        {{--@else--}}
                            {{--<td>用户团购</td>--}}
                        {{--@endif--}}
                        {{--<td><a href="javascript:;" onclick="jumpDetail('{{$value['app_id']}}|{{$value['buy_user_id']}}')">{{$value['buy_user_name']}}</a></td>--}}

                        <td><p class="goodsName" title="{{$value['target_name']}}">{{$value['target_name']}}</p></td>
                        <td>{{$value['applier']}}</td>
                        <td>{{$value['start_at']}}</td>
                        <td>{{$value['stop_at']}}</td>
                        <td>{{$value['state']}}</td>
                        <td>
                            <button type="button" class="btn btn-default" onclick="getInviteCodeDownUri('{{$value['id']}}')">
                            下载</button>
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
         {{--导出Excel文件选择弹框--}}
        <div class="downloadPop" id="ExportModal" style="display:none">
            <div class="darkScreen"></div>
            <form class="downloadPop_office">
                <div class="pageTopTitle">
                    <span class="outPutTime">下载邀请码</span>
                    <button type="button" class="close closePop" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body"> 
                    <span>office使用版本</span>
                    <input class="with-gap popS" id="Office_false" name="selectOffice" type="radio" value="0">
                    <label for="Office_false">非office2003</label>
                    <input class="with-gap popS" id="Office_true" name="selectOffice" type="radio" value="1" checked>
                    <label for="Office_true">office2003</label>
                    <div class="declaration">如果下载文件出现乱码，请选择另一个office版本选项进行下载</div>
                </div>
                <div class="modal-footer">
                    <div class="xeBtnDefault btnMid closePop">关闭</div>
                    <div class="btnBlue btnMid" id="applyExcel">确定</div>
                </div>
            </form>
        </div>
    </div>
@stop
