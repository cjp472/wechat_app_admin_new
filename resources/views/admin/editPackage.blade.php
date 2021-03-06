<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '专栏编辑';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/admin/acitvity/activeBaseLayout.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/admin/editPackage.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js?{{env('timestamp')}}"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../ueditor/ueditor.config.js?{{env('timestamp')}}"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="../ueditor/ueditor.all.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/acitvity/activeBaseLayout.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/config/config.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/editPackage.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="header">
        <div class="header_level left">专栏列表 ></div>   <div class="header_level">编辑</div>
    </div>

    <span style="display: none" id="data"  data-id ={{$package->id}}></span>

    <div class="upload_item_div">
        <div class="upload_item_title">专栏名称</div>
        <div class="upload_input_div">
            <input  class="form-control long" placeholder="请输入专栏名称" id="resource_title" value="{{$package->name}}" />
            <div class="upload_input_msg">
                <span>请设置专栏的名称,建议不要太长,控制在14个汉字以内。</span>
            </div>
        </div>
    </div>


    <div class="upload_item_div">
        <div class="upload_item_title">专栏简介</div>
        <div class="upload_input_div">
            <textarea class="form-control longer" placeholder="请输入专栏简介" id="resource_summary" cols="64" rows="3" style="resize: none">{{$package->summary}}</textarea>
            <div class="upload_input_prompt">
                <span>你还可输入<span id="letter"></span>个字符</span>
            </div>
            <div class="upload_input_msg">
                <span>简单描述下专栏，请注意提示有字数限制</span>
            </div>
        </div>
    </div>

    <input type="hidden" id="rubbish" value="{{$package->org_content}}"/>
    <div class="upload_item_div">
        <div class="upload_item_title">专栏描述</div>
        <div class="upload_input_div">
            <!-- 加载编辑器的容器 -->
            <script id="resource_desc"  type="text/plain"></script>
            <!-- 实例化编辑器 -->
            <script type="text/javascript">
                var ue = UE.getEditor('resource_desc',ueditor_config);
                ue.ready(function()
                {
                    ue.setContent($("#rubbish").val());
                });
            </script>
        </div>
    </div>


    <div class="upload_item_div">
        <div class="upload_item_title">专栏封面</div>
        <div class="upload_input_div">
            <div style="height: 100px">
                <div class="pic_div">
                    <input type="file" class="pic_input" id="resource_pic" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                    <img class="pic_close hide" src="../images/icon_close.png"/>
                    <img src="../images/addbanner.png" class="pic_add hide"/>
                    <img class="pic_show" src="@if($package->img_url_compressed){{$package->img_url_compressed}}@else{{$package->img_url}}@endif"/>
                </div>
            </div>
            <div class="upload_input_msg">
                <span>图片最佳尺寸750*560,支持gif、jpg、bmp、png格式的图片</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div hide">
        <div class="upload_item_title">有效时间</div>
        <div class="upload_input_div">
            <select class="form-control" id="resource_period">
                <option value="">无期限</option>
                @foreach($period_time as $key => $period)
                    <option value="{{$key}}" @if($key == $period_long)selected="selected"@endif>{{$period}}</option>
                @endforeach
            </select>
            <div class="upload_input_msg">
                <span>请选择你的专栏购买后有效时长。</span>
            </div>
        </div>
    </div>

    <div class="upload_item_div">
        <div class="upload_item_title">专栏价格</div>
        <div class="upload_input_div">
            <input class="form-control long" placeholder="请选择你的专栏收费金额" type="number" id="resource_price" value="{{$package->price/100}}"/>
            <span class="price_unit">元</span>
        </div>
    </div>

    {{--同时判断 version_type + app_module--}}
    @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category","version_type") &&
        \App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category","app_module"))
        <div class="upload_item_div">
            <div class="upload_item_title category">所属分类</div>
            @foreach($category_info as $key => $value)  {{--$key - id值 // $value - 分类名--}}
            <div class="checkBoxWrapper">
                {{--编辑页面 需要初始化checkbox状态--}}
                <input id="{{$key}}" class="radio_category" name="category" type="checkbox" value="{{$key}}" @if(in_array($key,$package_category)) checked="checked" @endif />
                <label for="{{$key}}" class="checkboxLabel"></label>
                <label for="{{$key}}" class="checkboxText">{{$value}}</label>
            </div>
            @endforeach
        </div>
    @endif


    <div class="upload_button_div">
        <button class="upload_button btnMid btnBlue" onclick="saveEditResource('package')">保存
        </button>
        <div>
            <a class="cancel_upload_button btnMid xeBtnDefault" href="/package_list">取消
            </a>
        </div>
    </div>


    <input type="hidden" id="xcx_app_id" value="{{session("app_id","")}}"/>
@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
@stop
