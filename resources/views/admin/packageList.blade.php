<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '专栏列表';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/contentList.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/packageList.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />

@endsection

@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/packageList.js?{{env('timestamp')}}"></script>
@endsection




@section('base_mainContent')

    <div class="tab_div">
        <div class="tab_type" id="tab_audio">音频</div>
        <div class="tab_type" id="tab_video">视频</div>
        <div class="tab_type" id="tab_article">图文</div>
        <div class="tab_type" id="tab_alive">直播(<span class="red-font">公测</span>)</div>
        <div class="tab_type tab_active" id="tab_package">专栏</div>
        @if(session('version_type') != 1)
            <div class="tab_type" id="tab_member">会员</div>
        @endif

    </div>

    <div class="package_list">
        <div class="package_flag">
            <div class="package_flag_blue"></div>
            <div class="package_flag_word">上架专栏</div>
        </div>
        @foreach($package_list_on as $key=>$package)
            <div class="list_item">
                <div class="linkCopy" aria-label="复制成功！" data-clipboard-text="{{$package_on_url_list[$key]}}" title="获取访问链接"><img src="../images/icon_copyLink.svg" alt="复制链接Icon"></div>
                <img  onclick="contentDetail('{{'/package_edit?id='.$package->id}}')" class="table-pointer" title="点击编辑" src="{{$package->img_url}}"/>
                <div  class="item_div">
                    <div class="item_title table-pointer" onclick="contentDetail('{{'/package_edit?id='.$package->id}}')" title="{{$package->name}}">{{$package->name}}</div>
                    <div class="item_resource" @if($package->finished_state)title="内容已完结">总共&nbsp;{{$package->resource_count}}期
                    @else title="内容更新中">更新至第&nbsp;{{$package->resource_count}}期
                    @endif
                        @if($package->finished_state)
                            <span class="operate_item_right finished" onclick="updatePackageFinishedStater(0,'{{$package->id}}',$(this))" title="将专栏设为更新中">更新</span>
                        @else
                            <span class="operate_item_right finished" onclick="updatePackageFinishedStater(1,'{{$package->id}}',$(this))" title="将专栏设为已完结">完结</span>
                        @endif
                    </div>
                    <div class="item_operate">
                        <span class="operate_item_left" onclick="window.location.href='{{'/package_edit?id='.$package->id}}'">编辑</span>
                        <span class="operate_item_right" onclick="updateResourceState('package',1,'{{$package->id}}')" >下架</span>

                        <span class="operate_item_right glyphicon glyphicon-arrow-up" onclick="updatePackageWeight(0,'{{$package->id}}')" title="排序:往前"></span>
                        <span class="operate_item_right glyphicon glyphicon-arrow-down" onclick="updatePackageWeight(1,'{{$package->id}}')" title="排序:往后"></span>
                        @if($package->h5_newest_hide == 0)
                        <span class="operate_item_right glyphicon glyphicon-eye-open" onclick="h5newestHider(1,'{{$package->id}}',$(this))" title="不在最新列表中显示"></span>
                        @else
                        <span class="operate_item_right glyphicon glyphicon-eye-close" onclick="h5newestHider(0,'{{$package->id}}',$(this))" title="在最新列表中显示"></span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="list_item item_click" onclick="window.location.href = '/package_create'"
         @if(count($package_list_on)%3 == 0)style="clear:both;"@endif>
            <img src="../images/icon_add_package.png" alt="新增专栏" title="新增专栏"/>
            <div class="operate_create">新增专栏</div>
        </div>
    </div>

    @if( count($package_list_off) > 0 )
    <div class="package_list" style="margin-top: -20px;">
        <div class="package_flag">
            <div class="package_flag_blue"></div>
            <div class="package_flag_word">下架专栏</div>
        </div>
        @foreach($package_list_off as $key=>$package)
            <div  class="list_item">
                <div class="linkCopy" aria-label="复制成功！" data-clipboard-text="{{$package_off_url_list[$key]}}" title="获取访问链接"><img src="../images/icon_copyLink.svg" alt="复制链接Icon"></div>
                <img src="{{$package->img_url}}"/>
                <div  class="item_div">
                    <div class="item_title" title="{{$package->name}}">{{$package->name}}</div>

                    <div class="item_resource">{{$package->resource_count}}期</div>

                    <div class="item_operate">
                        <span class="operate_item_left"
                          onclick="window.location.href='{{'/package_edit?id='.$package->id}}'">编辑</span>

                        <span class="operate_item_right" onclick="updateResourceState('package',0,'{{$package->id}}')" >上架</span>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

@stop

@section('base_modal')

@stop
