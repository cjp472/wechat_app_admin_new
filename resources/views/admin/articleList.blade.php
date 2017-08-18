<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '图文列表';
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
    <script type="text/javascript" src="../js/admin/articleList.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        resource_attr = "{{$resource_attr}}";
        setTopUrlCookie('article_listop','图文列表');
    </script>
@endsection



@section('base_mainContent')

    <div class="tab_div">
        <div class="tab_type" id="tab_audio">音频</div>
        <div class="tab_type" id="tab_video">视频</div>
        <div class="tab_type tab_active" id="tab_article">图文</div>
        <div class="tab_type" id="tab_alive">直播(<span class="red-font">公测</span>)</div>
        <div class="tab_type" id="tab_package">专栏</div>
        @if(session('version_type') != 1)
            <div class="tab_type" id="tab_member">会员</div>
        @endif
    </div>

    <div class="tool_bar">

        <div class="tool_bar_item">
            <button class="btn btn-default" onclick="searchResource('article')"
                    id="article_search_btn">搜索
            </button>
        </div>

        <div class="tool_bar_item">
            <input id="resource_search_content" type="text" class="form-control" aria-label="...">
        </div>

        <div class="searchSelectArea">
            <select class="form-control" id="resource_attr" >
                <option selected value="title">图文名称</option>
                <option value="create_at">创建时间</option>
                <option value="product_name">专栏名称</option>
            </select>
        </div>

        <div class="tool_bar_item_left">
            <a class="btn btn-default btn-blue" href="/article_create" style="margin-left: 0;">+新增图文</a>
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
            <th>上架时间</th>
            <th>浏览量</th>
            <th>评论数</th>
            <th style="width:169px;">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($articles as $key=>$article)
            <tr>
                <td class="td_left">
                    <img src="{{$article->img_url_compressed}}" class="img_rect"/>
                </td>
                <td class="title_long">{{ $article->title }}</td>
                @if($article->display_state==0)
                    <td class="status_min_widths">正常</td>
                @else
                    <td class="status_min_width">未上架</td>
                @endif
                @if($article->payment_type==1)
                    <td class="type_min_width">免费</td>
                @elseif($article->payment_type==2)
                    @if(empty($article->product_id))
                        <td class="type_min_width">单卖</td>
                    @else
                        <td class="type_min_width">专栏外单卖</td>
                    @endif
                @else
                    <td class="type_min_width">专栏</td>
                @endif
                @if($article->payment_type==3 || ($article->payment_type==2 && !empty($article->product_id) && !empty($article->product_name)))
                    <td>{{$article->product_name}}</td>
                @else
                    <td>--</td>
                @endif
                <td>¥{{$article->piece_price/100}}</td>
                <td>{{$article->start_at}}</td>
                <td>{{$article->view_count}}</td>
                <td>{{$article->comment_count}}</td>

                <td>
                    <div class="dropdown dropdown_w">
                        <button class="btn btn-default" type="button" onclick="contentDetail('{{'/article_edit?id='.$article->id}}')">编辑</button>
                        @if(session('wxapp_join_statu')==1 || session('is_collection') == 1)
                            <button class="btn btn-default copyHref"  aria-label="复制成功！" data-clipboard-text="{{$article->pageurl}}" title="获取访问链接">
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
                        <ul class="dropdown-menu dropdown-menu-right listnav_minwidth" role="menu" aria-labelledby="dropdownMenu1" >
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" onclick="resetUrl('{{'/comment_admin?type=0&record_id='.$article->id}}')">查看评论</a>
                            </li>
                            <li role="presentation">
                                @if($article->display_state==0)
                                <a role="menuitem" tabindex="-1" onclick="updateResourceState('article',1,'{{$article->id}}')">下架</a>
                                @else
                                <a role="menuitem" tabindex="-1" onclick="updateResourceState('article',0,'{{$article->id}}')">上架</a>
                                @endif
                            </li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="#" onclick="deleteResource('article','{{$article->id}}')">删除</a>
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
            <?php echo $articles->appends(['search_content' => $search_content, 'resource_attr' => $resource_attr])->render(); ?>
        @else
            <?php echo $articles->render(); ?>
        @endif
    </div>

@stop

@section('base_modal')

@stop
