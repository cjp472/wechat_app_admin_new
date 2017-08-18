<?php
$pageData = [];
$pageData['sideActive'] = 'redpacket_admin';
$pageData['barTitle'] = '红包记录';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/redPacket.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">

    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/bootstrap-datetimepicker.min.css?{{env('timestamp')}}">
@endsection


@section('page_js')

    <script type="text/javascript" src="../js/external/bootstrap-datetimepicker.min.js?{{env('timestamp')}}"></script>

    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script src="../js/admin/redPacket.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        packet_attr = "{{$packet_attr}}";
        state = "{{$state}}";
    </script>
@endsection


@section('base_mainContent')
    {{--头部--}}
    <div class="header">
        <div class="searchArea">
            <div class="searchButtonArea">
                <button type="button" class="btn btn-default" id="searchButton" onclick="searchComment()">查找内容</button>
            </div>

            <div class="searchInputArea">
                <input id="redPacket_search_content" type="text" class="form-control" placeholder="搜索" id="search"/>
            </div>

            <div class="searchSelectArea">
                <select class="form-control" id="packet_attr">
                    <option value="wx_nickname">昵称</option>
                    <option value="lucky_money_send_at">时间</option>
                    <option value="lucky_money_send_id">外部订单号</option>
                </select>
            </div>

            <div class="searchSelectArea">
                <select class="form-control" id="state">
                    <option value="-1">全部状态</option>
                    <option value="0">待发送</option>
                    <option value="1">已发送</option>
                    <option value="2">领取成功</option>
                    <option value="3">发送失败(用户多次未领)</option>
                    <option value="4">发送失败(其他原因)</option>
                    <option value="5">发送失败（待重试）</option>
                </select>
            </div>

            <button class="exportRedPacket" onclick="exportRedpacket()">导出</button>
        </div>
    </div>

    {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>领取人头像</th>
                <th>领取人昵称</th>
                <th>开通用户</th>
                <th>红包金额(元)</th>
                <th>外部订单号</th>
                <th>红包状态</th>
                <th>最后一次发送时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($redPacket as $key =>$value)
                <tr>
                    <td>
                        <img src="{{$user_info[$key]->wx_avatar?$user_info[$key]->wx_avatar:'../images/default.png'}}"
                         style="cursor: pointer;"
                         onclick="jumpDetail('{{$value->share_user_id}}')"/>
                    </td>
                    <td>{{$user_info[$key]->wx_nickname}}</td>
                    <td>
                        <a href="javascript:;" onclick="jumpDetail('{{$value->buy_user_id}}')">
                            {{$openUsers[$key]->wx_nickname}}
                        </a>
                    </td>
                    <td>{{$value->money/100}}</td>
                    <td>{{$value->lucky_money_send_id}}</td>

                    @if($value->state == 0)
                        <td>待发送</td>
                        @elseif($value->state == 1)
                        <td>已发送</td>
                        @elseif($value->state == 2)
                        <td>领取成功</td>
                        @elseif($value->state == 3)
                        <td>用户多次不领取红包，不再尝试发送</td>
                        @elseif($value->state == 4)
                        <td>多次发送失败，不再尝试发送</td>
                        @else($value->state == 5)
                        <td>发送失败，待重试</td>
                    @endif

                    <td>{{$value->lucky_money_send_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            @if(empty($search_content))
                <?php echo $redPacket->appends(['state' => $state, 'packet_attr' => $packet_attr])->render(); ?>
            @else
                <?php echo $redPacket->appends(['state' => $state, 'packet_attr' => $packet_attr, 'search_content' => $search_content])->render(); ?>
            @endif
        </div>
    </div>
@stop

@section('base_modal')
    {{--发消息--}}
    <div class="modal fade" id="ExportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 100px;">
            <div class="modal-content" style="height: 250px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">红包记录导出</span></div>
                </div>

                <div class="modal-body" style="height: 100px">
                    <p>红包产生时间</p>
                    <div>
                        <input class="form-control long" id="start_time" readonly/>
                        <span>至</span>
                        <input class="form-control long" id="end_time" readonly/>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 30px;">
                    <button type="button" class="btn btn-default btn-blue" onclick="exportToExcel()">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@stop

