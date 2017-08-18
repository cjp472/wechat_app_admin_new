<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}"/>
    {{-- 业务代码 --}}
    <link type=text/css rel="stylesheet" href="../css/admin/communityOperate/manageCommunity.css?{{env('timestamp')}}">

@endsection

@section('page_js')
    {{--弹出框--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{-- 腾讯云上传V4 --}}
    <script type="text/javascript" src="../sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="../js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>

    {{--上传工具类--}}
    <script src="../js/admin/utils/upload.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--表单检查--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{-- 业务代码 --}}
    <script type="text/javascript" src="../js/admin/communityOperate/manageCommunity.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    <input type="hidden" id='pageType' value="{{$page_type}}" />
    <div class="pageTopTitle">
        <a>社群运营</a>
        &gt;
        <a href="/smallCommunity/communityList">小社群</a>
        &gt;
        创建社群
    </div>

    <div class="resAddContent">
        <div class="resAddPart resAddPart1">
            <div class="resAddPartTitle">基本信息</div>
            {{--社群名称--}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    社群名称
                    <span class="startKey">*</span>
                </div>
                <div class="resAddSectionC">
                    <input id="title" class="communityName inputDefault" name="communityName"
                           placeholder="请输入社群名称(不超过15个字)" type="text"
                           @if($page_type) value="{{$data->title}}" @endif/>
                </div>
            </div>
            {{-- 社群简介 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    社群简介
                    <span class="startKey">
	                    *
	                </span>
                </div>
                <div class="resAddSectionC">
                    <textarea id="intro" class="communityInfo"
                              placeholder="请输入社群简介(不超过40个字)">@if($page_type){{$data->describe}}@endif</textarea>
                </div>
            </div>
            {{-- 社群封面 --}}
            <div class="resAddSection">
                <div class="resAddSectionT">
                    社群封面
                    <span class="startKey">
	                    *
	                </span>
                </div>
                <div class="resAddSectionC">
                    {{-- 图片预览，包括空的图片模板和图片预览模板 --}}
                    <label class="previewPic" for="uploadImage">
                        <img id="reBackImg"
                             src="@if($page_type) {{$data->img_url}} @else ../images/admin/resManage/pic_addfengmian.png @endif"/>
                    </label>
                    <div class="previewInfo">
                        <div class="btnSmall xeBtnDefault coverUpbtn">
	                        <span>
	                            选择文件
	                        </span>
                            <input id="uploadImage" accept="image/jpeg,image/png,image/gif,image/bmp"
                                   class="upLoadImage upLoadImage1" type="file"/>
                            <input type="hidden" id="imgUrl" type="text"
                                   @if($page_type) value="{{$data->img_url}}" @endif/>
                        </div>
                        <div class="coverUpTip">
                            图片格式为：bmp, jpeg, jpg, gif，尺寸1：1，不可大于2M。
                        </div>
                    </div>
                </div>
            </div>

            <div class="resAddPartTitle">加入形式 <span class="titleInfo">（请至少选择一种加入形式）</span></div>

            <div class="resAddSection">
                <div class="resAddSectionT">入群价格</div>
                <div class="resAddSectionC">
                    <div>
                        <input id="setFee" name="select_resource" type="checkbox" @if($page_type&&$data->piece_price!==null) checked @endif>
                        <label for="setFee" class="setFee">付费入群</label>
                        <input id="setPrice" class="inputDefault priceInput" placeholder="输入0则为免费"
                               @if($page_type&&$data->piece_price!==null) value="{{$data->piece_price/100}}"
                               @else readonly disabled @endif onkeyup="clearNoNum(this.value, this)"/>
                        <span class="yuan">元</span>
                    </div>

                    <div class="secondCheckbox">
                        <input id="setRelevance" name="select_resource" type="checkbox" @if($page_type && count($relation) != 0) checked @endif >
                        <label for="setRelevance" class="setRelevance">关联专栏或会员<span class="titleInfo">（可关联多个，关联社群后，购买专栏或会员的用户可以免费加入该社群）</span></label>

                        <div class="relevanceContent" id="relevanceContent"
                             @if($page_type == 1 && count($relation) > 0) style="display: block"
                             @else  style="display: none"
                             @endif
                        >
                            <div class="newRelevanceWrapper">
                                <div class="newRelevance" id="newRelevance" >
                                    <span class="add">+</span>添加关联
                                </div>
                                <div class="selectRelevance" style="display:none;">
                                    <select name="packageType" id="packageType" class="packageTypeSelector">
                                        <option value="package" id="package">专栏</option>
                                        <option value="member" id="member">会员</option>
                                    </select>
                                    <select id="packageSel" name="package" class="packageSelect">
                                        <option value="none">请选择具体专栏</option>
                                        @foreach($pay_products as $key => $value)
                                            <option value="{{$key}}" data-product_id="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                    <select id="memberSel" name="member" class="packageSelect" style="display: none">
                                        <option value="none">请选择具体会员</option>
                                        @foreach($member as $key => $value)
                                            <option value="{{$key}}" data-product_id="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                    <button id="addRelevance" class="btnMid btnBlue addRelevanceBtn">保存</button>
                                    <button id="removeRelevance" class="btnMid xeBtnDefault removeRelevanceBtn">取消</button>
                                </div>
                            </div>
                            @if($page_type)
                            @foreach($relation as $key => $value)
                                <div class="oneRelevance clearfix" data-product_id="{{$value->product_id}}">
                                    <div class="packageClass">{{$value->type == 1 ? "会员" : "专栏"}}</div>
                                    <div class="packageName" title="{{$value->product_name}}">{{$value->product_name}}</div>
                                    <button type="cancel" class="xeBtnDefault btnMid cancelRelevance">取消关联</button>
                                </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="resAddSection">
                <div class="resAddSectionT">是否上架</div>
                <div class="resAddSectionC">
                    <div class="radioGroup">
                        <div class="radioBtn1">
                            <input class="with-gap" id="showCommunity" name="isCommunityShow" value="0" type="radio" @if($page_type == 0 ||  ($page_type == 1 && $data->community_state == 0)) checked @endif >
                            <label for="showCommunity" class="isCommunityShowLabel">立即上架</label>
                        </div>
                        <div class="radioBtn2">
                            <input class="with-gap" id="hideCommunity" name="isCommunityShow" value="1" type="radio" @if($page_type == 1 && $data->community_state == 1) checked @endif >
                            <label for="hideCommunity" class="isCommunityShowLabel">暂不上架 - 显示为“下架”状态</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button id="saveBtn" class="btnMid btnBlue saveBtn">@if($page_type == 0) 创建 @else 保存 @endif</button>
    </div>
@endsection


