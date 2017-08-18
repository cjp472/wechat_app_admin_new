<?php
$pageData = [];
$pageData['sideActive'] = '_packagePart';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)

@section("page_css")
    <link type=text/css rel="stylesheet" href="../css/admin/resManage/packageList.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
@stop

@section("page_js")
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/resManage/packageList.js?{{env('timestamp')}}"></script>
@stop

@section("base_mainContent")
    {{--公共tab--}}
    {{--@include('admin.resManage.baseTab', ["tabTitle" => "packageList"])--}}
    <div class="pageTopTitle">
        知识商品 &gt; 专栏
    </div>

    <div class="content">
    	<div class="contentHead">
    		<div class="pull-left btnArea">
    			<a href="/create_package_page" class="createPackageBtn btnMid btnBlue">新建专栏</a>
    		</div>
    		<div class="pull-right searchArea">
                {{--<select class="selector" id="selector_distribute">--}}
                    {{--<option value="-1">全部</option>--}}
                    {{--<option value="0">自有</option>--}}
                    {{--<option value="1">推广</option>--}}
                {{--</select>--}}
                <select class="selector" id="selector">
                    <option value="-1">全部</option>
                    <option value="0">已上架</option>
                    <option value="1">已下架</option>
                </select>
                <input class="inputDefault" id="searchVal" placeholder="输入名称">
                <span id="searchBtn" class="searchBtn btnSmall xeBtnDefault">搜索</span>
            </div>
    	</div>

		<div class="contentBody" id="packageList">
            @foreach($packageListInfo as $key=>$package)

            <div class="listItem clearfix" data-id="{{$package->id}}" data-url="{{$package_on_url_list[$key]}}">
                <div class="listImg">
                    <a href="/package_detail_page?id={{$package->id}}">
                        <img width="120px" height="90px" src="{{$package->img_url_compressed}}" alt="{{$package->name}}"/>
                    </a>
                </div>
                <div class="listInfo">
                    <div class="listInfoTitle">
                        <a href="/package_detail_page?id={{$package->id}}">
                            <span class="title" title="{{$package->name}}">{{$package->name}}
                               @if($package->is_distribute == 1) <span class="distribute_target">推广</span> @endif
                            </span>
                        </a>
                        <div class="toolBox">
                            <ul>
                                <li class="operate" data-type="toup">上移</li>
                                <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                                <li class="operate" data-type="todown">下移</li>
                                <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                                <li class="operate" data-type="edit">{{--<a href="/package_detail_page?id={{$package->id}}">详情</a>--}}
                                    详情
                                </li>
                            </ul>
                        </div>

                        {{--<div class="listInfoState">--}}
                            {{--<span class="resCount">共{{$package->resource_count}}期</span>--}}
                            {{--@if($package->finished_state)--}}
                                {{--<span>已完结</span>--}}
                            {{--@else--}}
                                {{--<span>更新中</span>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    </div>
                    <div class="listInfoDesc">
                        {{$package->summary}}
                    </div>
                    <div class="listInfoPrice">
                        @if($package->price<=0)
                            <span class="price">免费</span>
                        @else
                            <span class="price">{{$package->price/100}}元</span>
                        @endif
                        @if($package->state)
                            <span class="pull-right hasDown">已下架</span>
                        @endif
                    </div>
                </div>
            </div>

            @endforeach

            @if(count($packageListInfo)==0)
                <div class="contentNoneTip">没有相应的数据</div>
            @endif


            <div class="list-page">
                @if(!empty($search_content))
                    {{$packageListInfo->appends(['search_content' => $search_content,'state' => $state])->render()}}
                @elseif(!empty($state))
                    {{ $packageListInfo->appends(['state' => $state])->render() }}
                @else
                    {{ $packageListInfo->render() }}
                @endif
            </div>

		</div>
    </div>


@stop
