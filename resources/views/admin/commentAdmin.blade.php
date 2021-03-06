  <?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/commentAdmin.css?{{env('timestamp')}}">  {{--css--}}
    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../css/admin/ie-SelectArea.css?{{env('timestamp')}}">
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script type="text/javascript"
            src="../js/admin/commentAdmin.js?{{env('timestamp')}}"></script>     {{--JavaScript--}}
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        comment_attr = "{{$comment_attr}}";
        comment_state = "{{$comment_state}}";
        type = "{{$type}}";
        orderParameter = "{{$orderParameter}}";
        record_id = "{{$record_id}}";

        var microfunc = '{{$micro_func}}';
    </script>
@endsection


@section('base_mainContent')

    <div class="pageTopTitle">
        <a>社群运营</a> > 评论互动
    </div>
    <div class="commentBody">
        <div style="margin: 10px 0 20px 0;height: 34px;line-height: 34px">
            {{--原生版--}}
            <div style="float: right;">
                <button class="btn btn-default" id="comment_search_btn" onclick="searchComment()">搜索
                </button>
            </div>
            <div style="float: right">
                <input id="comment_search_content" type="text" class="form-control" aria-label="...">
            </div>
            <div class="searchSelectArea">
                <select class="form-control" id="comment_attr">
                    <option value="t_comments.content" selected>评论内容</option>
                    <option value="t_users.wx_nickname">用户昵称</option>
                    <option value="t_comments.record_title">内容名称</option>
                </select>
            </div>
            <div class="searchSelectArea">
                <select class="form-control" id="comment_state">
                    <option value="-1">全部状态</option>
                    <option value="0">显示</option>
                    <option value="1">隐藏</option>
                    <option value="2">未精选</option>
                    <option value="3">精选</option>
                </select>
            </div>
            @if($micro_func)
                <div class="searchSelectArea">
                    <select class="form-control" name="apptype">
                        <option value="0" selected="selected">全部来源</option>
                        <option value="1">小程序</option>
                        <option value="2">公众号</option>
                    </select>
                </div>
            @endif
        </div>

        <div>
            {{--<table class="table table-hover" style="border: 1px solid #ddd" onclick="hideDialog()">--}}
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="th_avatar_nickname">头像/昵称</th>
                    <th>类型</th>
                    <th>资源名称</th>
                    <th>评论内容</th>

                    {{--可按照评论时间 排序--}}
                    @if($orderParameter == 1)            {{--选中降序排列--}}
                    <th class="table-pointer" title="改为升序排列" onclick="orderByParameter(10)"
                        style="color:#0000cc;">
                        <span class="glyphicon glyphicon-arrow-down"></span>评论时间
                    </th>

                    @elseif($orderParameter == 10)       {{--选中升序排列--}}
                    <th class="table-pointer" title="改为降序排列" onclick="orderByParameter(1)"
                        style="color:#0000cc;">
                        <span class="glyphicon glyphicon-arrow-up"></span>评论时间
                    </th>
                    @elseif($orderParameter == '')       {{--还没有点击排序--}}
                    <th class="table-pointer" title="降序排列" onclick="orderByParameter(10)"
                        style="color:#0000cc;">
                        <span class="glyphicon glyphicon-arrow-down"></span>评论时间
                    </th>
                    @else                           {{--按照点赞数排列中--}}
                    <th class="table-pointer" title="降序排列" onclick="orderByParameter(1)">
                        <span class="glyphicon glyphicon-arrow-down"></span>评论时间
                    </th>
                    @endif

                    {{--可按照 点赞数 排序--}}
                    @if($orderParameter == 2)            {{--选中降序排列--}}
                    <th class="table-pointer" title="改为升序排列" onclick="orderByParameter(20)"
                        style="color:#0000cc;">
                        <span class="glyphicon glyphicon-arrow-down"></span>点赞
                    </th>
                    @elseif($orderParameter == 20)       {{--选中升序排列--}}
                    <th class="table-pointer" title="改为降序排列" onclick="orderByParameter(2)"
                        style="color:#0000cc;">
                        <span class="glyphicon glyphicon-arrow-up"></span>点赞
                    </th>
                    @else                           {{--还没有点击排序 或者 按照评论时间排列中--}}
                    <th class="table-pointer" title="降序排列" onclick="orderByParameter(2)">
                        <span class="glyphicon glyphicon-arrow-down"></span>点赞
                    </th>
                    @endif

                    {{--@if($micro_func)--}}
                        {{--<th>来源</th>@endif--}}
                    <th>回复</th>
                    <th>回复时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($commentList as $key=>$value)
                    <tr>
                        {{--<td class="td_left">
                            @if(($comment_attr=="t_users.wx_nickname") && (!empty($search_content)))
                                <img src="{{$user_info[$value->user_id]->wx_avatar?$user_info[$value->user_id]->wx_avatar:'../images/default.png'}}"
                                    style="cursor: pointer;"
                                    onclick="jumpDetail('{{$value->user_id}}')"/>
                            @else
                                <img src="{{$user_info[$key]->wx_avatar?$user_info[$key]->wx_avatar:'../images/default.png'}}"
                                 style="cursor: pointer;"
                                 onclick="jumpDetail('{{$value->user_id}}')"/>
                            @endif
                        </td>
                        <td class="title_long">
                                @if(($comment_attr=="t_users.wx_nickname") && (!empty($search_content)))
                                    <span>{{$user_info[$value->user_id]->wx_nickname}}</span>
                                @else
                                    <span>{{$user_info[$key]->wx_nickname}}</span>
                                @endif
                                @endif
                        </td>--}}

                        {{--头像 + 昵称 --}}
                        <td class="td_avatar_nickname">
                            <div class="avatar_nickname_wrapper">
                                <div class="avatar_nickname" data-app_id="{{$value->app_id}}"
                                     data-user_id="{{$value->user_id}}">
                                    <div class="avatar_icon_wrapper">
                                        @if(($comment_attr=="t_users.wx_nickname") && (!empty($search_content)))
                                            <img class="avatar_icon"
                                                 src="{{$user_info[$value->user_id]->wx_avatar?$user_info[$value->user_id]->wx_avatar:'../images/default.png'}}"/>
                                        @else
                                            <img class="avatar_icon"
                                                 src="{{$user_info[$key]->wx_avatar?$user_info[$key]->wx_avatar:'../images/default.png'}}"/>
                                        @endif
                                    </div>
                                    @if(($comment_attr=="t_users.wx_nickname") && (!empty($search_content)))
                                        <span class="nick_name"
                                              title="{{$user_info[$value->user_id]->wx_nickname}}">{{$user_info[$value->user_id]->wx_nickname}}</span>
                                    @else
                                        <span class="nick_name"
                                              title="{{$user_info[$key]->wx_nickname}}">{{$user_info[$key]->wx_nickname}}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{--资源类型--}}
                        @if($value->type==0)
                            <td class="type_min_width">{{'图文'}}</td>
                        @elseif($value->type==1)
                            <td class="type_min_width">{{'音频'}}</td>
                        @else
                            <td class="type_min_width">{{'视频'}}</td>
                        @endif

                        {{--资源名称--}}
                        <td class="title_long td_resource_name"
                            title="{{$value->record_title}}">{{$value->record_title}}</td>

                        {{--评论内容--}}
                        @if(empty($value->src_user_id))
                            {{--不是回复他人的评论--}}
                            <td class="td_comment_content"
                                title="{{$value->content}}">{{$value->content}}</td>
                        @else
                            {{--回复他人的评论--}}
                            <td class="td_comment_content"
                                title="{{$value->content.' || '.$value->src_content}}">{{$value->content.' || '.$value->src_content}}</td>
                        @endif

                        {{--评论时间--}}
                        <td>{{$value->comment_time}}</td>

                        {{--点赞数--}}
                        <td>
                            @if(!empty($value->zan_num))
                                {{$value->zan_num}}
                            @else
                                0
                            @endif
                        </td>

                        {{--@if($micro_func)    --}}{{--来源--}}
                        {{--<td>{{$apptypeName[$value->wx_app_type]}}</td>--}}
                        {{--@endif--}}

                        {{--管理员回复--}}
                        @if(empty($value->admin_name))
                            <td>-</td>
                        @else
                            <td class="td_administrator_reply" style="word-break: break-all; word-wrap:break-word;text-align:justify;">
                                {{--<div style="display: inline-block;height: 14px;width: 3px;background-color: lightblue"></div>--}}
                                <span class="">{{$value->admin_name}}：</span> <br> {{$value->admin_content}}
                            </td>
                        @endif

                        {{--回复时间--}}
                        <td class="">{{$value->admin_created_at?$value->admin_created_at:'-'}}</td>

                        {{--状态 - 先判断是否显示  0 表示显示 --}}
                        @if($value->comment_state==0)
                            {{--再判断是否置顶 0 表示不置顶 --}}
                            @if($value->is_top==0)
                                <td class="status_min_width" id="{{"state_".$value->id}}">
                                    {{--<button style="width: 40px" class="btn btn-link"></button>--}}
                                    <h5>正常</h5>
                                </td>
                            @else
                                <td class="status_min_width" id="{{"state_".$value->id}}">
                                    <button style="background: #3a7bd5;width: 40px;border-color: #3a7bd5"
                                            class="btn btn-primary">精选
                                    </button>
                                </td>
                            @endif
                        @else
                            <td class="status_min_width" id="{{"state_".$value->id}}">
                                <button style="background: #e64340;width: 40px;border-color: #e64340 "
                                        class="btn btn-danger">隐藏
                                </button>
                            </td>
                        @endif


                        {{--操作--}}
                        <td class="td_operation">
                            <div class="dropdown dropdown_w td_operation_div" style="width: 140px">
                                {{--管理员回复--}}
                                <button class="btn btn-default" type="button" data-target="#SmsModal"
                                        onclick="adminMsg('{{$value->id}}','{{$value->user_id}}','{{$value->admin_name?1:0}}')">
                                    回复
                                </button>
                                <button class="btn btn-default dropdown-toggle" type="button"
                                        id="dropdownMenu1" data-toggle="dropdown">
                                    更多
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right listnav_minwidth" role="menu"
                                    aria-labelledby="dropdownMenu1">
                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" data-target="#SmsModal"
                                           onclick="jumpMsg('{{$value->app_id}}|{{$value->user_id}}|{{$value->id}}')">发私人消息</a>
                                    </li>
                                    <li role="presentation">
                                        @if($value->comment_state==0)
                                            <a role="menuitem" tabindex="-1"
                                               onclick="changeState('{{$value->id}}','{{$value->comment_state}}','{{$value->record_id}}','{{$value->type}}')">隐藏</a>
                                        @else
                                            <a role="menuitem" tabindex="-1"
                                               onclick="changeState('{{$value->id}}','{{$value->comment_state}}','{{$value->record_id}}','{{$value->type}}')">显示</a>
                                        @endif
                                    </li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation">
                                        @if($value->is_top)
                                            <a role="menuitem" tabindex="-1"
                                               onclick="changeTopState('{{$value->id}}','{{$value->is_top}}','{{$value->type}}')">取消精选</a>
                                        @else
                                            <a role="menuitem" tabindex="-1"
                                               onclick="changeTopState('{{$value->id}}','{{$value->is_top}}','{{$value->type}}')">精选评论</a>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="list-page">
                @if($micro_func)
                    @if(empty($search_content))
                        <?php echo $commentList->appends(['comment_state' => $comment_state, 'comment_attr' => $comment_attr, 'type' => $type, 'record_id' => $record_id, 'apptype' => $apptype, 'reurl' => $reurl, 'order_parameter' => $orderParameter])->render(); ?>
                    @else
                        <?php echo $commentList->appends(['comment_state' => $comment_state, 'comment_attr' => $comment_attr, 'apptype' => $apptype, 'search_content' => $search_content, 'reurl' => $reurl, 'order_parameter' => $orderParameter])->render(); ?>
                    @endif
                @else
                    @if(empty($search_content))
                        <?php echo $commentList->appends(['comment_state' => $comment_state, 'comment_attr' => $comment_attr, 'type' => $type, 'record_id' => $record_id, 'reurl' => $reurl, 'order_parameter' => $orderParameter])->render(); ?>
                    @else
                        <?php echo $commentList->appends(['comment_state' => $comment_state, 'comment_attr' => $comment_attr, 'search_content' => $search_content, 'reurl' => $reurl, 'order_parameter' => $orderParameter])->render(); ?>
                    @endif
                @endif
            </div>
        </div>
    </div>
@stop

@section('base_modal')

    {{--发消息--}}
    <div class="modal fade" id="SmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content"
                 style="height: 450px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span
                                class="modal-title" style="font-size: 18px" id="myModalLabel">消息推送</span>
                    </div>
                    <div style="margin-top: 10px;">
                        @if(!empty($model)>0)
                            @foreach($model as $key=>$value)
                                <div class="model_type" id="model_{{$key+1}}" data-id="{{$value[0]}}"
                                     data-name="{{$value[1]}}" data-content="{{$value[2]}}">
                                    模板{{$key+1}}</div>
                            @endforeach
                            @for($i = 1 ; $i <= (5-count($model)) ; $i++)
                                <div class="model_type" id="model_{{$i+count($model)}}" data-id=""
                                     data-name="" data-content="">模板{{$i+count($model)}}</div>
                            @endfor
                        @else
                            @for($i = 1 ; $i <= 5 ; $i++)
                                <div class="model_type" id="model_{{$i}}" data-id="" data-name=""
                                     data-content="">模板{{$i}}</div>
                            @endfor
                        @endif
                        <div class="model_type" style="border: 0px;width: auto;height: auto"
                             id="cancel_model" data-id="" data-name="" data-content="">×
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="height: 200px">
                    <input class="form-control" placeholder="请输入发送人昵称" id="sms_nickname"
                           style="margin-bottom: 15px;"/>
                    <textarea class="form-control" placeholder="请输入您想发送的内容"
                              style="resize: none;height: 200px;border-radius:10px;width: 100%;"
                              id="sms_content"></textarea>
                </div>

                <div class="modal-footer" style="margin-top: 75px;">
                    <div class="edit_model hide" style="display: inline-block">保存修改</div>
                    <button type="button" class="btn btn-default btn-blue">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    {{--管理员回复--}}
    <div class="modal fade" id="AdminModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 150px;">
            <div class="modal-content"
                 style="height: 450px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span
                                class="modal-title" style="font-size: 18px" id="myModalLabel">管理员回复</span>
                    </div>
                </div>

                <div class="modal-body" style="height: 200px">
                    <input class="form-control" placeholder="管理员昵称" id="admin_nickname" value="管理员"
                           style="margin-bottom: 15px;"/>
                    <textarea class="form-control" placeholder="请输入您想发送的内容"
                              style="resize: none;height: 200px;border-radius:10px;width: 100%;"
                              id="admin_content"></textarea>
                </div>

                <div class="modal-footer" id="adminsend" style="margin-top: 75px;">
                    <div class="edit_model hide" style="display: inline-block">保存修改</div>
                    <button type="button" class="btn btn-default btn-blue">确认</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

@stop
