<?php
$pageData = [];
$pageData['sideActive'] = 'customerManage';
$pageData['barTitle'] = '用户管理';
$tabData = [];
$tabData['tabTitle'] = 'customerList';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/customer.css?{{env('timestamp')}}">
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}"/>
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/customer.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script>
        setTopUrlCookie('customer_listop', '用户列表');
    </script>
@endsection

@section('base_mainContent')

    @include("admin.customerManage.baseTab", $tabData)

    {{--头部--}}
    <div class="header">
        {{--搜索--}}
        <div class="searchArea">
            <div class="userCount">
                人数：<span>{{$count}}</span>
            </div>
            <div class="searchRightArea">
                <div class="pull-left" style="margin-right: 10px; line-height: 34px;">
                    <a href="/helpCenter/problem?document_id=d_58f0bdbc5dc9b_kpnvN8CD" target="_blank">如何给用户发消息</a>
                </div>
                <select class="isPaySelector" name="is_pay">
                    <option value="0" @if($is_pay == 0) selected @endif>所有</option>
                    <option value="1" @if($is_pay == 1) selected @endif>已消费</option>
                </select>
                <select class="rulerSelector" name="ruler">
                    <option value="0" @if($ruler == 0) selected @endif>昵称</option>
                    <option value="1" @if($ruler == 1) selected @endif>手机号码</option>
                </select>
                <input class="inputDefault searchContentInput" id="searchContent" aria-label="..." type="text"
                       placeholder="请输入搜索内容" value="{{$search}}" />
                <div class="btnMid xeBtnDefault searchUserBtn" id="searchUser">搜索</div>
            </div>
        </div>
    </div>

   {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="th_left">头像</th>
                    <th>昵称</th>
                    <th>性别</th>
                    <th>生日</th>
                    <th>手机号码</th>
                    <th>消费总额(元)</th>
                    <th>用户创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                <tr >
                    <td class="td_left">
                        <img src="{{$value['avatar']}}" class="avatar"
                         onclick="jumpDetail('{{$value['app_id']}}|{{$value['user_id']}}')"/>
                    <td>{{$value['nickname']}}</td>
                    <td>{{$value['gender']}}</td>
                    <td>{{$value['birth']}}</td>
                    <td>{{$value['phone']}}</td>
                    <td>¥{{ $value['sum'] / 100}}</td>
                    <td>{{$value['created_at']}}</td>
                    <td class="operate_long_comment">
                        <button type="button" class="btn btn-default" data-target="#SmsModal"
                        onclick="jumpMsg('{{$value['app_id']}}|{{$value['user_id']}}')">发消息</button>

                        <button type="button" class="btn btn-default"
                        onclick="jumpDetail('{{$value['app_id']}}|{{$value['user_id']}}')">详情</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(empty($data) || count($data) == 0)
            <div class="contentNoneTip">没有相应的数据</div>
        @endif

        {{--页标--}}
        <div class="list-page">
            @if(empty($search))
                <?php echo $allInfo->appends(['is_pay' => $is_pay,'ruler' => $ruler])->render(); ?>
            @else
                <?php echo $allInfo->appends(['is_pay' => $is_pay,'ruler' => $ruler, 'search'=> $search])->render(); ?>
            @endif
        </div>

    </div>
@stop

@section('base_modal')
    {{--发消息--}}
    <div class="modal fade" id="SmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 640px;margin-top: 150px;">
            <div class="modal-content" style="height: 550px;width: 640px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">消息推送</span></div>
                    <div style="margin-top: 10px;">
                        @if(!empty($model)>0)
                            @foreach($model as $key=>$value)
                                <div class="model_type" id="model_{{$key+1}}" data-id="{{$value[0]}}" data-name="{{$value[1]}}" data-content="{{$value[2]}}">模板{{$key+1}}</div>
                            @endforeach
                            @for($i = 1 ; $i <= (5-count($model)) ; $i++)
                                <div class="model_type" id="model_{{$i+count($model)}}" data-id="" data-name="" data-content="">模板{{$i+count($model)}}</div>
                            @endfor
                        @else
                            @for($i = 1 ; $i <= 5 ; $i++)
                                <div class="model_type" id="model_{{$i}}" data-id="" data-name="" data-content="">模板{{$i}}</div>
                            @endfor
                        @endif
                        <div class="model_type" style="border: 0px;width: auto;height: auto" id="cancel_model" data-id="" data-name="" data-content="">×</div>
                    </div>
                </div>

                <div class="modal-body">
                    <input id="sms_nickname" class="form-control" placeholder="在此输入发送人昵称，例如：小鹅通"/>
                    <textarea id="sms_content" class="form-control" placeholder="在此输入消息内容"></textarea>

                    {{--私人消息新添加外链-start--}}
                    <div class="link-part-title">消息链接 (可不填)</div>
                    <input class="form-control" placeholder="链接名称，例如：戳我领取福利！" id="link_name"/>

                    <div>
                        <div style="float: left; width: 100px;">
                            <select class="form-control"  id="link_type_selector">
                                <option value="h5">外部链接</option>
                                <option value="audio">音频</option>
                                <option value="video">视频</option>
                                <option value="image_text">图文</option>
                                <option value="alive">直播</option>
                                <option value="package">专栏</option>
                                <option value="no_jump">无跳转</option>
                            </select>
                        </div>
                        <div style="float: left;margin-left: 10px">
                            <select class="form-control"  id="skip_target_selector">
                                @foreach($audioList as $key=>$value)
                                    <option value="{{ $value->id }}">{{ $value->title }}</option>
                                @endforeach
                            </select>
                            <input class="form-control hide" id="skip_target_input"/>
                        </div>
                    </div>

                    {{--检测网址的输入错误时，添加文字提醒--}}
                    <div class="http_error_tip hide">
                        您输入的网址不符合要求，输入网址请以 http:// 或 https:// 开头
                    </div>
                    {{--私人消息新添加外链-end--}}

                </div>

                <div class="modal-footer" style="margin: 45px 15px 0; padding: 30px 0px;">
                    {{--style="margin: 0 15px 0; padding: 15px 0 0 30px;"--}}
                    <div class="edit_model hide">
                        保存修改
                    </div>
                    <button type="button" class="btn btn-primary btn-blue sendAtOnce_Button">
                        立即发送</button>
                    <button type="button" class="btn btn-default cancel_Button" data-dismiss="modal">
                        取消</button>
                </div>
            </div>
        </div>
    </div>
@stop