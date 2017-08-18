<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/sale.css?{{env('timestamp')}}" />
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />

@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    {{--复制文本到剪贴板--}}
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/sale.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    <div class="header">
        <ul class="header_ul">
            <li class="header_li"><a class="header_a" href="/channel_admin">渠道管理</a></li>
            @if(session('app_id')=='appIK67joYW5412'||session('app_id')=='appe0MEs6qX8480'||session('app_id')=='apppcHqlTPT3482')
            <li class="header_li" style="border-bottom: 2px solid #2a75ed;"><a class="header_a" href="/sale">分销审批</a></li>
            @endif
            @if(session('app_id')=='apppcHqlTPT3482'||session('app_id')=='appe0MEs6qX8480'||session('app_id')=='apprnDA0ZDw4581')
            <li class="header_li"><a class="header_a" href="/channel/listen">试听渠道</a></li>
            @endif
        </ul>
    </div>

    <div class="content">
        <div class="searchArea">
            <button class="btn btn-default" id="searchButton">搜索</button>
            <div class="searchInputArea">
                <input type="text" class="form-control long" id="search" placeholder="请输入搜索的申请人" />
            </div>
            <div class="selectArea">
                <select class="form-control" id="ruler">
                    <option value="-1" selected="selected">全部</option>
                    <option value="0">申请中</option>
                    <option value="1">同意</option>
                    <option value="2">拒绝</option>
                </select>
            </div>
        </div>

        <div style="padding: 0 20px;margin-top: 20px;">
            <table class="table table-hover" >
                <thead>
                    <tr>
                        <th>分销名称</th>
                        <th>申请人</th>
                        <th>手机号码</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th class="long">分销内容</th>
                        <th>申请时间</th>
                        <th>备注</th>
                        {{--<th>浏览量</th>--}}
                        {{--<th>开通量</th>--}}
                        <th style="min-width: 120px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                @if($all_info)
                    @foreach($all_info as $key => $value)
                    <tr>
                        <td>{{$value->sale_name}}</td>
                        <td>{{$value->applier}}</td>
                        <td>{{$value->phone}}</td>
                        <td>
                            <img @if($value->xiaoe_img_url) src="{{$value->xiaoe_img_url}}"
                            @else src="../images/default.jpg" @endif />
                        </td>
                        <td>
                            @if($value->xiaoe_nick_name){{$value->xiaoe_nick_name}}
                            @else 无 @endif
                        </td>
                        <td class="overomit">{{$value->sale_content}}</td>
                        <td>{{$value->apply_at}}</td>
                        <td>{{$value->remark}}</td>
                        {{--<td>--}}
                            {{--@if($value->view_count){{$value->view_count}}--}}
                            {{--@else - @endif--}}
                        {{--</td>--}}
                        {{--<td>--}}
                            {{--@if($value->open_count){{$value->open_count}}--}}
                            {{--@else - @endif--}}
                        {{--</td>--}}

                        @if($value->state==0){{--申请中--}}
                        <td>
                            <a href="javascript:void(0);" onclick="agreeSale('{{$value->id}}')">通过申请</a>
                            <a>|</a>
                            <a href="javascript:void(0);" onclick="disAgreeSale('{{$value->id}}')">拒绝</a>
                        </td>
                        @elseif($value->state==1){{--同意--}}
                        <td style="position: relative;">
                            <a class="copyLink copyHref" data-clipboard-text="{{$value->sale_url}}">复制分销链接</a>

                        </td>
                        @else{{--拒绝--}}
                        <td>
                            <span class="hasRefused">已拒绝</span>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

        {{--页标--}}
        <div class="list-page">
            @if(empty($search))
                <?php echo $all_info->appends(['ruler'=>$ruler])->render(); ?>
            @else
                <?php echo $all_info->appends(['ruler'=>$ruler,'search'=> $search])->render(); ?>
            @endif
        </div>
    </div>
@stop

@section('base_modal')
    {{--同意的弹窗--}}
    <div class="modal fade" id="agreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 800px;margin-top: 70px;">
            <div class="modal-content" style="height: 550px;width: 800px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">通过申请</span></div>
                </div>

                <div class="modal-body">
                    <div class="chooseChannelArea">
                        <input class="with-gap" value="0" id="newSelect" name="create_select" type="radio" />
                        <label for="newSelect">生成新的分销链接</label>
                    </div>

                    <div class="chooseChannelArea">
                        <input class="with-gap" value="1" id="hasSelect" name="create_select" type="radio" checked/>
                        <label for="hasSelect">使用已有的渠道链接</label>
                    </div>

                    <div class="tableArea">
                        <div class="tableAreaContainer">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>勾选</th>
                                        <th>渠道名称</th>
                                        <th>资源名称</th>
                                    </tr>
                                </thead>
                                <tbody id="channel_list">
                                {{--@foreach($channels as $key=>$value)--}}
                                    {{--<tr>--}}
                                        {{--<td>--}}
                                            {{--<input type="radio" name="channel_select" value="{{$value->acc_url}}"--}}
                                            {{--channel_id="{{$value->id}}"  @if($key == 0) checked="checked" @endif />--}}
                                        {{--</td>--}}
                                        {{--<td>{{$value->name}}</td>--}}
                                        {{--<td>{{$value->resource_title}}</td>--}}
                                    {{--</tr>--}}
                                {{--@endforeach--}}
                                </tbody>
                            </table>
                            <div id="no_data" class="hide" style="margin-left: 305px">没有相关数据</div>
                        </div>
                    </div>

                    <div class="buttonArea">
                        <button type="button" class="blueButton" id="updateUrl">确定</button>
                    </div>

                    <input type="hidden" id="agree_id" />
                </div>
            </div>
        </div>
    </div>

    {{--拒绝的弹窗--}}
    <div class="modal fade" id="disAgreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 120px;">
            <div class="modal-content" style="height: 430px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">拒绝申请</span></div>
                </div>

                <div class="modal-body">
                    <textarea class="form-control" cols="22" rows="15" id="refuse_reason"
                    placeholder="在此输入拒绝理由"></textarea>

                    <div class="buttonArea">
                        <button type="button" class="blueButton" id="updateRefuse">确定</button>
                    </div>

                    <input type="hidden" id="disagree_id" />
                </div>
            </div>
        </div>
    </div>
@stop