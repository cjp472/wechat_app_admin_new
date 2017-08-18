<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '内容列表';
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
    <script type="text/javascript" src="../js/admin/contentList.js?{{env('timestamp')}}"></script>

    <script>
        search_content = "{{$search_content}}";
        resource_attr = "{{$resource_attr}}";
        setTopUrlCookie('audio_listop','音频列表');
    </script>
@endsection



@section('base_mainContent')

    <div class="tab_div">
        <div class="tab_type tab_active" id="tab_audio">音频</div>
        <div class="tab_type" id="tab_video">视频</div>
        <div class="tab_type" id="tab_article">图文</div>
        <div class="tab_type" id="tab_alive">直播(<span class="red-font">公测</span>)</div>
        <div class="tab_type" id="tab_package">专栏</div>
        @if(session('version_type') != 1)
            <div class="tab_type" id="tab_member">会员</div>
        @endif
    </div>

    <div class="tool_bar">
        <div class="tool_bar_item">
            <button class="btn btn-default" onclick="searchResource('audio')"
                    id="resource_search_btn">搜索
            </button>
        </div>

        <div class="tool_bar_item">
            <input id="resource_search_content" type="text" class="form-control" aria-label="...">
        </div>

        <div class="searchSelectArea">
            <select class="form-control" id="resource_attr">
                <option selected value="title">音频名称</option>
                <option value="start_at">上架时间</option>
                <option value="product_name">专栏名称</option>
            </select>
        </div>

        <div class="tool_bar_item_left">
            <a class="btn btn-default btn-blue" href="/audio_create" style="margin-left: 0;">+新增音频</a>
        </div>

    </div>

    <table class="table table-hover">
        <thead>
        <tr>
            <th class="th_left">封面</th>
            <th>名称</th>
            <th>付费类型</th>
            <th>价格(元)</th>
            <th>所属专栏</th>
            <th>上架时间</th>
            <th id="count_glyphicon">统计</th>
            <th>状态</th>
            <th style="width: 169px;">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($audios as $key=>$audio)
            <tr>
                <td class="td_left">
                    <img src="{{$audio->img_url_compressed}}" class="img_rect"/>
                </td>
                <td class="title_long">{{ $audio->title }}</td>
                @if($audio->payment_type==1)
                    <td class="type_min_width">免费</td>
                @elseif($audio->payment_type==2)
                    @if(empty($audio->product_id))
                        <td class="type_min_width">单卖</td>
                    @else
                        <td class="type_min_width">专栏外单卖</td>
                    @endif
                @else
                    <td class="type_min_width">专栏</td>
                @endif
                <td>¥{{$audio->piece_price/100}}</td>

                {{--所属专栏列 - start--}}
                @if($audio->payment_type==3 || ($audio->payment_type==2 && !empty($audio->product_id) && !empty($audio->product_name)))
                    <td style="max-width: 280px;">{{$audio->product_name}}</td>
                @else
                    <td>--</td>
                @endif
                {{--所属专栏列 - end--}}

                <td>{{$audio->start_at}}</td>
                <td class="count_glyphicon">
                        <div class="dropdown dropdown_w " style="width:30px;">
                                    <span class="glyphicon glyphicon-tasks"></span>

                            <ul class="dropdown-menu pull-left count_showbox" role="menu" aria-labelledby="dropdownMenu1">
                                <li role="presentation"><a role="menuitem" tabindex="-1">评论数：{{$audio->comment_count}}</a></li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1">播放量：{{$audio->playcount}}</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1">完播量：{{$audio->finishcount}}</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1">完播率：{{$audio->finishpercent}} %</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1">分享量：{{$audio->share_count}}</a></li>
                                <li role="presentation" class="divider"></li>
                @if($result && $result[0]->try_audio == 1)
                    <li role="presentation"><a role="menuitem" tabindex="-1">试听数：{{$audio->try_count}}</a></li>
                @endif
                                @if($result && $result[0]->daily_sign==1)
                                    <li role="presentation"><a role="menuitem" tabindex="-1">日签点击量：{{$audio->click_sign_count}}</a></li>
                                @endif
                            </ul>
                        </div>

                </td>
                @if($audio->audio_state==0)
                    <td class="status_min_width">正常</td>
                @else
                    <td class="status_min_width">未上架</td>
                @endif

                <td>
                    {{--操作菜单--}}
                    <div class="dropdown dropdown_w">
                        <button class="btn btn-default" type="button" onclick="resetUrl('{{'/audio_edit?id='.$audio->id}}')">编辑</button>
                        @if(session('wxapp_join_statu')==1 || session('is_collection') == 1)
                            <button class="btn btn-default copyHref"  aria-label="复制成功！" data-clipboard-text="{{$audio->pageurl}}" title="获取访问链接">
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
                        <ul class="dropdown-menu pull-right listnav_minwidth" role="menu" aria-labelledby="dropdownMenu1">
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" onclick="resetUrl('{{'/comment_admin?type=1&record_id='.$audio->id}}')">查看评论</a>
                            </li>
                            <li role="presentation">
                                @if($audio->audio_state==0)
                                    <a role="menuitem" tabindex="-1" onclick="updateResourceState('audio',1,'{{$audio->id}}')">下架</a>
                                @else
                                    <a role="menuitem" tabindex="-1" onclick="updateResourceState('audio',0,'{{$audio->id}}')">上架</a>
                                @endif
                            </li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" onclick="deleteResource('audio','{{$audio->id}}')">删除</a></li>
                        </ul>

                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="list-page">
        @if(!empty($search_content))
            <?php echo $audios->appends(['search_content' => $search_content, 'resource_attr' => $resource_attr])->render(); ?>
        @else
            <?php echo $audios->render(); ?>
        @endif
    </div>
@stop

@section('base_modal')

@stop
