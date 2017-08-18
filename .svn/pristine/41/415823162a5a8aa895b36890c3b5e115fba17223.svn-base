<?php
$pageData = [];
$pageData['sideActive'] = 'customerManage';
$pageData['barTitle'] = '用户管理';
$tabData = [];
$tabData['tabTitle'] = 'feedbackList';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/feedback.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
@stop

@section('page_js')

    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/feedback.js?{{env('timestamp')}}"></script>
    <script>
        setTopUrlCookie('feedback_listop', '反馈列表');
        var microfunc = '{{$micro_func}}' ;
    </script>
@stop


@section('base_mainContent')

    @include("admin.customerManage.baseTab", $tabData)

    {{--头部--}}
    <div class="header">
        {{--搜索--}}
        <div class="searchArea">
            @if($micro_func)
                <select class="appTypeSelector" name="apptype">
                    <option value="0" @if($apptype == 0) selected @endif>全部来源</option>
                    <option value="1" @if($apptype == 1) selected @endif>小程序</option>
                    <option value="2" @if($apptype == 2) selected @endif>公众号</option>
                </select>
            @endif
            <select class="rulerSelector" name="ruler">
                <option value="0" @if($ruler == 0) selected @endif>内容</option>
                <option value="1" @if($ruler == 1) selected @endif>时间</option>
                <option value="2" @if($ruler == 2) selected @endif>昵称</option>
            </select>
            <input type="text" class="inputDefault searchContentInput" aria-label="..." name="search"
                placeholder="请输入搜索内容" @if($search) value="{{$search}}" @endif />
            <button class="xeBtnDefault btnMid searchFeedbackBtn" id="searchButton">搜索</button>
        </div>

    </div>

   {{--table区--}}
    <div class="content">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="th_left">头像</th>
                    <th>昵称</th>
                    <th class="long">内容</th>
                    <th>反馈时间</th>
                    @if($micro_func)<th>来源</th>@endif
                    {{--<th>最新回复内容</th>--}}
                    <th>最新回复时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                <tr >
                    <td class="td_left">
                        <img src="{{$value['avatar']}}" class="avatar"
                         onclick="jumpDetail('{{$value['app_id']}}|{{$value['user_id']}}')"/>
                    </td>
                    <td>{{$value['nickname']}}</td>
                    <td class="msg">{{ $value['content']}}</td>
                    <td>{{$value['created_at']}}</td>
                    @if($micro_func)<td>{{$value['wx_app_type']}}</td>@endif
                    {{--<td>{{$value['adminmsg']}}</td>--}}
                    <td class="msg_glyphicon">@if($value['replied_at']){{$value['replied_at']}} @else  --  @endif
                        @if($forbid)
                            @if($value['adminmsg'])
                        <div class="msg_showbox">
                            <em></em>
                            <div class="msg_show">
                                <p>
                                    <span>（最新回复）</span><span>{{$value['send_nick_name']}}：{{$value['adminmsg']}}</span>
                                </p>
                            </div>
                        </div>
                        @endif
                        @endif
                    </td>
                    <td><button type="button" class="btn btn-default" data-target="#SmsModal"
                                onclick="jumpMsg('{{$value['app_id']}}|{{$value['user_id']}}|{{$value['id']}}')">发消息</button>

                        {{--白名单处理--}}
                        @if($forbid)
                            @if($value['inforbid'])
                                <button type="button" class="btn btn-default" onclick="showNoticeToast('已加白名单')">已加</button>
                            @else
                                <button type="button" class="btn btn-default" onclick="forbid('{{$value['user_id']}}|1')">加白名单</button>
                            @endif
                        @endif
                        {{--白名单处理--}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            @if($micro_func)
                @if($forbid)
                    @if(empty($search))
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'apptype' => $apptype, 'forbid' => $forbid])->render(); ?>
                    @else
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'apptype' => $apptype, 'forbid' => $forbid, 'search'=> $search])->render(); ?>
                    @endif
                @else
                    @if(empty($search))
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'apptype' => $apptype])->render(); ?>
                    @else
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'apptype' => $apptype, 'search'=> $search])->render(); ?>
                    @endif
                @endif
            @else
                @if($forbid)
                    @if(empty($search))
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'forbid' => $forbid])->render(); ?>
                    @else
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'forbid' => $forbid, 'search'=> $search])->render(); ?>
                    @endif
                @else
                    @if(empty($search))
                        <?php echo $allInfo->appends(['ruler' => $ruler])->render(); ?>
                    @else
                        <?php echo $allInfo->appends(['ruler' => $ruler, 'search'=> $search])->render(); ?>
                    @endif
                @endif
            @endif
        </div>

    </div>
@stop

@section('base_modal')
    {{--发消息--}}
    <div class="modal fade" id="SmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content" style="height: 450px;width: 700px;padding-left: 10px;padding-right: 10px">

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

                <div class="modal-body" style="height: 200px">
                    <input class="form-control" placeholder="请输入发送人昵称" id="sms_nickname"
                           style="margin-bottom: 15px;"/>
                    <textarea class="form-control" placeholder="请输入您想发送的内容"
                              style="resize: none;height: 200px;border-radius:10px;width: 100%;" id="sms_content"></textarea>
                </div>

                <div class="modal-footer" style="margin-top: 75px;">
                    <div class="edit_model hide" style="display: inline-block">保存修改</div>
                    <button type="button" class="btn btn-default btn-blue">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@stop