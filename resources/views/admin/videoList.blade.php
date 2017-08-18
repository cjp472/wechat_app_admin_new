<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '视频列表';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/contentList.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../css/admin/ie-SelectArea.css?{{env('timestamp')}}">
    <![endif]-->
@endsection


@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/videoList.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        resource_attr = "{{$resource_attr}}";
        setTopUrlCookie('video_listop','视频列表');
    </script>
@endsection



@section('base_mainContent')

    <div class="tab_div">
        <div class="tab_type" id="tab_audio">音频</div>
        <div class="tab_type tab_active" id="tab_video">视频</div>
        <div class="tab_type" id="tab_article">图文</div>
        <div class="tab_type" id="tab_alive">直播(<span class="red-font">公测</span>)</div>
        <div class="tab_type" id="tab_package">专栏</div>
        @if(session('version_type') != 1)
            <div class="tab_type" id="tab_member">会员</div>
        @endif
    </div>

    <div class="tool_bar">
        <div class="tool_bar_item">
            <button class="btn btn-default" onclick="searchResource('video')"
                    id="resource_search_btn">搜索
            </button>
        </div>

        <div class="tool_bar_item">
            <input id="resource_search_content" type="text" class="form-control" aria-label="...">
        </div>

        <div class="searchSelectArea">
            <select class="form-control" id="resource_attr">
                <option selected value="title">视频名称</option>
                <option value="start_at">上传时间</option>
                <option value="product_name">专栏名称</option>
            </select>
        </div>

        <div class="tool_bar_item_left">
            @if($video_upload<=$uploadmax)
            <a class="btn btn-default btn-blue" href="/video_create"  style="margin-left: 0;">+新增视频</a>
            @else
            <a class="btn btn-default btn-gray" onclick="showErrorToast('限量每天新增{{$uploadmax}}个视频，敬请明天再传！')" style="margin-left: 0;">+新增视频</a>
            @endif
        </div>
    </div>
    <table class="table table-hover">
        <thead>
        <tr>
            <th class="th_left">封面</th>
            <th>名称</th>
            <th>状态</th>
            <th>付费类型</th>
            <th>所属专栏</th>
            <th>价格(元)</th>
            <th>评论数</th>
            <th>上架时间</th>
            <th>是否转码</th>
            <th style="width:169px;">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($videos as $key=>$video)
            <tr>
                <td class="td_left">
                    <img src="{{$video->img_url_compressed}}" class="img_rect"/>
                </td>
                <td class="title_long">{{ $video->title }}</td>
                @if($video->video_state==0)
                    <td class="status_min_width">正常</td>
                @else
                    <td class="status_min_width">未上架</td>
                @endif
                @if($video->payment_type==1)
                    <td class="type_min_width">免费</td>
                @elseif($video->payment_type==2)
                    @if(empty($video->product_id))
                        <td class="type_min_width">单卖</td>
                    @else
                        <td class="type_min_width">专栏外单卖</td>
                    @endif
                @else
                    <td class="type_min_width">专栏</td>
                @endif
                {{--所属专栏列 - start--}}
                @if($video->payment_type==3 || ($video->payment_type==2 && !empty($video->product_id) && !empty($video->product_name)))
                    <td>{{$video->product_name}}</td>
                @else
                    <td>--</td>
                @endif
                {{--所属专栏列 - end--}}
                <td>¥{{$video->piece_price/100}}</td>
                <td>{{ $video->comment_count }}</td>


                <td style="min-width: 100px">{{$video->start_at}}</td>
                @if($video->is_transcode==1)
                    <td>已转码</td>
                @elseif($video->is_transcode==2)
                    <td class="error">转码失败</td>
                @else
                    <td>转码中</td>
                @endif
                <td>
                    <div class="dropdown dropdown_w">
                        <button class="btn btn-default" type="button" onclick="resetUrl('{{'/video_edit?id='.$video->id}}')">编辑</button>
                        @if(session('wxapp_join_statu')==1 || session('is_collection') == 1)
                            <button class="btn btn-default copyHref"  aria-label="复制成功！" data-clipboard-text="{{$video->pageurl}}" title="获取访问链接">
                                <span class="glyphicon glyphicon-link"></span>
                            </button>
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"  title="更多">
                                <span class="caret"></span>
                            </button>
                        @else
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" title="">
                                更多<span class="caret"></span>
                            </button>
                        @endif
                        <ul class="dropdown-menu dropdown-menu-right listnav_minwidth" role="menu" aria-labelledby="dropdownMenu1">
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" onclick="resetUrl('{{'/comment_admin?type=2&record_id='.$video->id}}')">查看评论</a>
                            </li>
                            <li role="presentation">
                                @if($video->video_state==0)
                                <a role="menuitem" tabindex="-1" onclick="updateResourceState('video',1,'{{$video->id}}')">下架</a>
                                @else
                                <a role="menuitem" tabindex="-1"
                                   @if($video->is_transcode == 1)
                                   onclick="updateResourceState('video',0,'{{$video->id}}')"
                                   @else onclick="showErrorToast('转码未完成，上架失败');"
                                        @endif>上架</a>
                                @endif
                            </li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" onclick="deleteResource('video','{{$video->id}}')">删除</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="list-page">
        @if(!empty($search_content))
            <?php echo $videos->appends(['search_content' => $search_content, 'resource_attr' => $resource_attr])->render(); ?>
        @else
            <?php echo $videos->render(); ?>
        @endif
    </div>
@stop

@section('base_modal')

@stop
