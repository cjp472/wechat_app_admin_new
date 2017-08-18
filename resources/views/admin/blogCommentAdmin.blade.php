<?php
$pageData = [];
$pageData['sideActive'] = 'post_comment';
$pageData['barTitle'] = '帖子评论';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/blogCommentAdmin.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/blogCommentAdmin.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        comment_attr = "{{$comment_attr}}";
        comment_state = "{{$comment_state}}";

        //调到详情页
        function jumpDetail(info)
        {
            var info=info;
            var appId=info.split("|")[0];
            var userId=info.split("|")[1];
            window.location.href='/customerdetail?appId='+appId+'&userId='+userId;
        }
    </script>
@endsection




@section('base_title')

@stop



@section('base_mainContent')
        <div>

            <div class="tool_bar">
                <div class="tool_bar_item">
                    <button id="comment_search_btn" class="btn btn-default" onclick="searchBComment()" >搜索</button>
                </div>
                <div class="tool_bar_item">
                    <input id="comment_search_content" type="text" class="form-control" aria-label="...">
                </div>
                <div class="tool_bar_item_margin">
                    <select class="form-control" id="comment_attr">
                    <option value="t_comments.content" selected>评论内容</option>
                    <option value="t_users.wx_nickname">用户昵称</option>
                    <option value="t_comments.record_title">资源名称</option>
                    </select>
                 </div>
                <div class="tool_bar_item_margin">
                    <select class="form-control" id="comment_state">
                    <option value="-1">全部状态</option>
                    <option value="0">显示</option>
                    <option value="1">隐藏</option>
                    </select>
                </div>
            </div>

                <div class="content">
                        <table class="table table-hover" style="border: 1px solid #ddd;border-left: none; border-right: none" onclick="" >
                                <thead>
                                <tr>
                                    <th class="th_left">头像</th>
                                    <th >昵称</th>
                                        <th>帖子摘要</th>
                                        {{--<th>内容类型</th>--}}
                                        <th>评论内容</th>
                                        <th>评论时间</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($commentList as $key=>$value)
                                        <tr>
                                                <td class="td_left">
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

                                                <td>
                                                    @if(($comment_attr=="t_users.wx_nickname") && (!empty($search_content)))
                                                        <span>{{$user_info[$value->user_id]->wx_nickname}}</span>
                                                    @else
                                                        <span>{{$user_info[$key]->wx_nickname}}</span>
                                                    @endif
                                                </td>
                                            {{--评论所属记录的title--}}
                                                <td>{{$value->record_title}}</td>

                                                {{--评论内容--}}
                                                @if($value->src_comment_id==0 || $value->src_comment_id==null)
                                                        <td class="long">{{$value->content}}</td>
                                                @else
                                                        <td class="long">{{$value->content.' || '.$value->src_content}}</td>
                                                @endif

                                                <td style="min-width: 200px">{{$value->comment_time}}</td>

                                                {{--状态--}}
                                                {{--先判断是否显示  0 表示显示 --}}
                                                @if($value->comment_state==0)
                                                        {{--再判断是否置顶 0 表示不置顶 --}}
                                                        @if($value->is_top==0)
                                                                <td class="type_min_width" id="{{"state_".$value->id}}">
                                                                        {{--<button style="width: 40px" class="btn btn-link"></button>--}}
                                                                        <h5>正常</h5>
                                                                </td>
                                                        @else
                                                                <td class="type_min_width" id="{{"state_".$value->id}}">
                                                                        <button style="background: #3a7bd5;width: 40px" class="btn btn-primary">精选</button>
                                                                </td>
                                                        @endif
                                                @else
                                                        <td id="{{"state_".$value->id}}">
                                                                <button style="background: #e64340;width: 40px" class="btn btn-danger">隐藏</button>
                                                        </td>
                                                @endif


                                                {{--操作--}}
                                                <td>
                                                        @if($value->is_top)
                                                                <button id = "{{"btnTop_".$value->id}}" class="btn btn-default" value="0" onclick="changeTopState('{{$value->id}}','{{$value->is_top}}','{{$value->record_id}}')">取消精选
                                                                </button>
                                                        @else
                                                                <button id = "{{"btnTop_".$value->id}}" class="btn btn-default" value="0" onclick="changeTopState('{{$value->id}}','{{$value->is_top}}','{{$value->record_id}}')">精选评论
                                                                </button>
                                                        @endif
                                                        @if($value->comment_state==0)
                                                                <button id = "{{"btn_".$value->id}}" class="btn btn-default" value="0" onclick="changeState('{{$value->id}}','{{$value->comment_state}}','{{$value->record_id}}')">隐藏
                                                                </button>
                                                        @else
                                                                <button id = "{{"btn_".$value->id}}" class="btn btn-default" value="1" onclick="changeState('{{$value->id}}','{{$value->comment_state}}','{{$value->record_id}}')">显示
                                                                </button>
                                                        @endif
                                                </td>

                                        </tr>
                                @endforeach
                                </tbody>
                        </table>
                        <div class="list-page">
                                @if(empty($search_content))
                                        <?php echo $commentList->appends(['comment_state' => $comment_state, 'comment_attr' => $comment_attr])->render(); ?>
                                @else
                                        <?php echo $commentList->appends(['comment_state' => $comment_state, 'comment_attr' => $comment_attr, 'search_content' => $search_content])->render(); ?>
                                @endif
                        </div>
                </div>
        </div>

@stop

@section('base_modal')
        <!-- 按钮触发模态框 -->

            <!-- 模态框（Modal） -->
@stop
