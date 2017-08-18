<?php
$pageData = [];
$pageData['sideActive'] = 'knowledgeShop';
$pageData['barTitle'] = '店铺设置';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet">

    {{--业务逻辑 css--}}
    <link type=text/css rel="stylesheet" href="../css/admin/categorySetting.css?{{env('timestamp')}}"/>
@endsection

@section('page_js')
    {{--时间选择器--}}
    {{--<script src="../js/external/bootstrap-datetimepicker.min.js"></script>--}}

    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>--}}
    {{--<script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>--}}

    {{--获取文件MD5--}}
    {{--<script type="text/javascript" src="../js/external/browser-md5-file.js"></script>--}}
    {{--上传工具函数--}}
    {{--<script type="text/javascript" src="../js/admin/utils/upload.js"></script>--}}
    {{--表单验证工具函数--}}
    {{--<script type="text/javascript" src="../js/admin/utils/formCheck.js"></script>--}}

    {{--业务逻辑js--}}
    <script type="text/javascript" src="../js/admin/categorySetting.js?{{time()}}"></script>
@endsection

@section('base_mainContent')
    <div class="header">
        <div class="header_level left">首页分类导航 ></div>   <div class="header_level">编辑</div>
    </div>

    <div class="content">
        <div class="left_part_edit">
            @for($i = 0; $i < 4; $i ++)
                <div class="category_edit" id="{{"category_".($i + 1)}}">
                    <div class="title_wrapper">
                        <div class="blue_dot"></div>
                        <div class="category_title">
                            @if($i == 0)
                                分类一
                            @elseif($i == 1)
                                分类二
                            @elseif($i == 2)
                                分类三
                            @elseif($i == 3)
                                分类四
                            @endif
                        </div>
                    </div>
                    <div class="category_name_wrapper">
                        <div class="category_name_field_out clearfix">
                            <p class="category_name_field">分类名称<span class="category_name_field_dot">*</span></p>
                            <input class="category_name_input"
                                   @if($i == 0)
                                        placeholder="请输入分类名称（不超过4个字），例如：热门"
                                   @elseif($i == 1)
                                        placeholder="请输入分类名称（不超过4个字），例如：免费"
                                   @elseif($i == 2)
                                        placeholder="请输入分类名称（不超过4个字），例如：推荐"
                                   @elseif($i == 3)
                                        placeholder="请输入分类名称（不超过4个字），例如：最新"
                                   @endif

                                   @if(!empty($categoryInfo)) value="{{$categoryInfo[$i]->category_name}}" @endif
                            >
                        </div>
                        <div class="no_name_tip" style="display: none;">请输入分类名称</div>

                    </div>
                    <div class="upload_icon_wrapper">
                        <div class="upload_icon_field_out clearfix">
                            <p class="upload_icon_field">分类图标<span class="upload_icon_field_dot">*</span></p>
                            {{--上传图标 --}}
                            <div class="upload_icon">
                                {{--<input type="file" class="pic_input uploadCategoryPic" accept="image/jpeg,image/png"/>--}}
                                @if(empty($categoryInfo))
                                    <img class="pic_add" src="../images/admin/category_navigation/icon_upload_image.svg"/>
                                    <img class="pic_show hide" src=""/>
                                @else
                                    <img class="pic_add hide" src="../images/admin/category_navigation/icon_upload_image.svg"/>
                                    <img class="pic_show" src="{{$categoryInfo[$i]->icon_url}}"/>
                                @endif
                                <div class="upload_icon_btn">选择图标</div>
                            </div>
                        </div>
                        <div class="no_url_tip" style="display: none;">请选择分类图标</div>

                    </div>
                    {{--<div class="upload_icon_tip">建议尺寸：48px*48px.图片格式：png, jpg</div>--}}
                </div>
            @endfor
        </div>

        <div class="right_part_preview">
            <div class="phone_preview_title">分类导航示例</div>
            <img class="phone_preview_img" src="/images/category_navigation_preview.png">
        </div>
        <div class="bottom_part_save">
            <div class="save_edit_btn">保存</div>
        </div>
    </div>

@stop

@section('base_modal')  {{--选择图标弹窗--}}
    <div class="select_icon_window" style="display: none">
        <div class="select_window">
            <div class="top_area">
                <div class="select_icon_title">选择图标</div>
                <div class="close_icon_wrapper">
                    <img class="close_icon" src="/images/icon_Pop-ups_close.svg">
                </div>
            </div>
            <div class="select_area">
                {{--@for($i = 0; $i < 4; $i ++)
                    <div class="row_icons">
                        @for($j = 0; $j < 5; $j ++)
                            <div class="single_icon_wrapper">
                                <img class="single_icon" src="/images/icon_yunying.png">
                            </div>
                        @endfor
                    </div>
                @endfor--}}

                @foreach($urlArr as $v)
                    <div class="row_icons">
                        <div class="single_icon_wrapper">
                            <img class="single_icon" src="{{$v}}">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="button_area">
                <div class="cancel_btn">取消</div>    <div class="confirm_btn">确定</div>
            </div>
        </div>
    </div>


@stop










