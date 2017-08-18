<?php
$pageData = [];
$pageData['sideActive'] = 'post_list';
$pageData['barTitle'] = '帖子列表';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/blogAdmin.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/blogAdmin.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        blog_attr = "{{$blog_attr}}";
        blog_state = "{{$blog_state}}";

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

    <div class="tool_bar">
            <div class="tool_bar_item">
                <button id="btn_search" class="btn btn-default" onclick="searchBlog()" >搜索</button>
            </div>
            <div class="tool_bar_item">
                <input id="blog_search_content" type="text" class="form-control" aria-label="...">
            </div>
            <div class="tool_bar_item_margin">
                <select class="form-control" id="blog_attr">
                    <option value="t_community_record.content" selected>帖子内容</option>
                    <option value="t_users.wx_nickname">用户昵称</option>
                    <option value="t_community_record.created_at">发帖时间</option>
                </select>
            </div>
            <div class="tool_bar_item_margin">
                <select class="form-control" id="blog_state" >
                    <option value="-1">全部状态</option>
                    <option value="0">显示</option>
                    <option value="1">隐藏</option>
                    <option value="2">未精选</option>
                    <option value="3">精选</option>
                </select>
            </div>
        </div>

    {{--table区--}}
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="th_left">头像</th>
                <th>昵称</th>
                <th>帖子摘要</th>
                <th>发帖时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($blogResult as $key => $value)
                <tr >
                    <td class="td_left">
                        @if(($blog_attr=="t_users.wx_nickname") && (!empty($search_content)))
                            <img src="{{$user_info[$value->user_id]->wx_avatar?$user_info[$value->user_id]->wx_avatar:'../images/default.png'}}" style="cursor: pointer" onclick="jumpDetail('{{$value->user_id}}')"/>
                        @else
                            <img src="{{$user_info[$key]->wx_avatar?$user_info[$key]->wx_avatar:'../images/default.png'}}" style="cursor: pointer" onclick="jumpDetail('{{$value->user_id}}')"/>
                        @endif
                    </td>
                    <td>
                        @if(($blog_attr=="t_users.wx_nickname") && (!empty($search_content)))
                            {{$user_info[$value->user_id]->wx_nickname?$user_info[$value->user_id]->wx_nickname:'无'}}
                        @else
                            {{$user_info[$key]->wx_nickname?$user_info[$key]->wx_nickname:'无'}}                        @endif
                    </td>
                    <td style="max-width: 350px">
                        <a onclick="blogDetail('{{$value->content}}','{{$value->img_url}}')" href="javascript:;">
                            @if(mb_strlen($value->content)<=45)
                                {{$value->content}}
                            @else
                                {{mb_substr($value->content,0,43,'utf-8')."..."}}
                            @endif
                        </a>
                    </td>
                    <td>{{$value->create_time}}</td>

                    {{--状态--}}
                    {{--先判断是否显示  0 表示显示 --}}
                    @if($value->display_state==0)
                        @if($value->is_top==0)
                            <td id="{{"state_".$value->id}}">
                                {{--<button style="width: 40px" class="btn btn-link">正常</button>--}}
                                <h5>正常</h5>
                            </td>
                        @else
                            <td id="{{"state_".$value->id}}">
                                <button style="background: #3a7bd5;width: 40px" class="btn btn-primary">精选</button>
                            </td>
                        @endif
                    @else
                        <td id="{{"state_".$value->id}}">
                            <button style="background: #e64340;width: 40px" class="btn btn-danger">隐藏</button>
                        </td>
                    @endif

                    <td class="operate_long">
                        {{--@if($value->is_top)--}}
                            {{--<button id = "{{"btnTop_".$value->id}}" class="btn btn-default" value="0" onclick="changeTopState('{{$value->id}}','{{$value->is_top}}')">取消--}}
                            {{--</button>--}}
                        {{--@else--}}
                            {{--<button id = "{{"btnTop_".$value->id}}" class="btn btn-default" value="0" onclick="changeTopState('{{$value->id}}','{{$value->is_top}}')">精选--}}
                            {{--</button>--}}
                        {{--@endif--}}
                        @if($value->display_state==0)
                            <button id = "{{"btn_".$value->id}}" class="btn btn-default" value="0" onclick="changeState('{{$value->id}}','{{$value->display_state}}')">隐藏
                            </button>
                        @else
                            <button id = "{{"btn_".$value->id}}" class="btn btn-default" value="1" onclick="changeState('{{$value->id}}','{{$value->display_state}}')">显示
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{--页标--}}
        <div class="list-page">
            @if(empty($search_content))
                <?php echo $blogResult->appends(['blog_state' => $blog_state, 'blog_attr' => $blog_attr])->render(); ?>
            @else
                <?php echo $blogResult->appends(['blog_state' => $blog_state, 'blog_attr' => $blog_attr, 'search_content' => $search_content])->render(); ?>
            @endif
        </div>
@stop

@section('base_modal')
    <!-- 按钮触发模态框 -->
    {{--发消息--}}
    <div style="text-align: center" class="modal fade" id="SmsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 380px;height: 80%;">
            <div class="modal-content" style=";margin: 0 auto ; text-align: center;height: 100%;width: 380px;padding-left: 10px;padding-right: 10px">
                {{--background: url('../images/phone-bg.png')--}}
                <div class="modal-header" style="text-align: left;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">帖子内容</h4>
                </div>

                <div class="modal-body" style="text-align: left;position:absolute; overflow:auto;float: left;height: 84%">
                </div>

                {{--<div class="modal-footer" style="">--}}
                    {{--<button type="button" style="" class="btn btn-default" data-dismiss="modal">关闭</button>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    <!-- 模态框（Modal） -->
@stop

<script>

    function blogDetail(content , imgArray) {

//        console.log("dialog height = "+ $('.modal-dialog').height());
//        console.log("body height = "+ $('.modal-body').height());

        $('.modal-body').empty();
        $('.modal-body').append("<p style='max-width:100% '></p>");
        var content = content;
        $("#SmsModal").modal('show');
        $('.modal-body').children().eq(0).html(content);
        var imgArr = imgArray.split(';');
        $('.modal-body').append("<br>");
        if(imgArr[0]!=""){
            for(var i = 0 ; i < imgArr.length; i ++ ){
//            if(i%3==0){
//                $('.modal-body').append("<br>");
//            }
                var img = " <img style='margin:10px 0;height: 320px;width: 320px' src='"+imgArr[i]+"'/> ";
                $('.modal-body').append(img);
                $('.modal-body').append("<br>");
            }
        }

//        $('.modal-dialog').height((imgArr.length+1)*280);
//
//        console.log("dialog height = "+ $('.modal-dialog').height());
//        console.log("body height = "+ $('.modal-body').height());
    }

</script>
