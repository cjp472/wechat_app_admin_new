<?php
$pageData = [];
$pageData['sideActive'] = 'knowledgeShop';
$pageData['barTitle'] = '店铺设置';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="../css/admin/ie-SelectArea.css?{{env('timestamp')}}">
    <![endif]-->

    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/xcConfirm.css?{{env('timestamp')}}">

    <link type=text/css rel="stylesheet" href="../css/admin/bannerAdmin.css?{{env('timestamp')}}">
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/bannerList.js?{{env('timestamp')}}"></script>
    <script>
        search_content = "{{$search_content}}";
        resource_attr = "{{$resource_attr}}";
        search_status = "{{$search_status}}";
    </script>
@endsection


@section('base_title')
    <span style="font-size: 18px">轮播栏</span>
@stop


@section('base_mainContent')

    @include("admin.knowledgeShop.baseTab", ["tabTitle" => "bannerPicture"])

    <div class="myContent">
        <div class="tool_bar" >

            <div class="tool_bar_item">
                <button class="btn btn-default" onclick="searchBanner()" id="resource_search_btn">搜索
                </button>
            </div>

            <div class="tool_bar_item">
                <input id="resource_search_content" type="text" class="form-control" aria-label="...">
            </div>
            <div class="searchSelectArea">
                <select class="form-control" id="resource_attr">
                    <option selected value="title">图片名称</option>
                </select>
            </div>
            <div class="searchSelectArea">
                <select class="form-control" id="search_status">
                    <option selected value="-1">全部</option>
                    <option  value="0">正常</option>
                    <option  value="1">下架</option>
                </select>
            </div>

            <div class="tool_bar_item_left">
                <a class="btn btn-default btn-blue" href="/banner_create" style="margin-left: 0;">+新增轮播图</a>
            </div>
        </div>

        {{--table区--}}
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="th_left">图片</th>
                <th>名称</th>
                <th>跳转类型</th>
                <th>链接名称</th>
                <th>生效时间</th>
                <th>下架时间</th>
                <th>状态</th>
                <th>显示顺序</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bannerList as $key=>$banner)
                <tr>
                    <td class="td_left">
                        <img src="@if($banner->img_url_compressed){{$banner->img_url_compressed}}@else{{$banner->image_url}}@endif" class="img_rect_banner"/>
                    </td>
                    <td class="title_long">
                        <span style="margin-left: 10px">{{$banner->title}}</span>
                    </td>
                    @if($banner->skip_type == 1)
                        <td>图文</td>
                    @elseif($banner->skip_type == 2)
                        <td>音频</td>
                    @elseif($banner->skip_type == 3)
                        <td>视频</td>
                    @elseif($banner->skip_type == 4)
                        <td>直播</td>
                    @elseif($banner->skip_type == 6)
                        <td>专栏</td>
                    @elseif($banner->skip_type == 5)
                        <td>外部链接</td>
                    @else
                        <td>无</td>
                    @endif

                    @if($banner->skip_type == 5)
                    <td class="url_long">{{ empty($banner->skip_target) ? "--": $banner->skip_target }}</td>
                    @else
                    <td>{{ empty($banner->skip_title) ? "--": $banner->skip_title }}
                        <span style="color:red;">{{$banner->resource_state}}</span>
                    </td>
                    @endif

                    <td>{{$banner->start_at}}</td>
                    <td>
                        {{--@if($banner->state_offline==1)--}}
                            {{--{{$banner->updated_at}}--}}
                        {{--@else--}}
                            @if($banner->stop_at)
                                {{$banner->stop_at}}
                            @else
                                长期
                            @endif
                        {{--@endif--}}
                    </td>

                    @if($banner->state_offline==0)
                        @if( $banner->stop_at>0 && $banner->stop_at<$thistime )
                            <td>下架(自动)</td>
                        @else
                            <td>正常</td>
                        @endif
                    @else
                        <td>下架(手动)</td>
                    @endif

                    @if($banner->weight == 10)
                        <td>第一页</td>
                    @elseif($banner->weight == 9)
                        <td>第二页</td>
                    @elseif($banner->weight == 8)
                        <td>第三页</td>
                    @else
                        <td>第一页</td>
                    @endif

                    <td class="operate_long">
                        <button class="btn btn-default" onclick="setreUrl('{{ '/edit_banner?id='.$banner->id }}')">编辑
                        </button>
                        @if($banner->state_offline==0)
                            <button class="btn btn-default" onclick="updateBannerState(1,'{{ $banner->id }}')">下架</button>
                        @else
                            <button class="btn btn-default" onclick="updateBannerState(0,'{{ $banner->id }}')">上架</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>

        <div align="center">
            {!! $bannerList->links() !!}
        </div>
    </div>

@stop
