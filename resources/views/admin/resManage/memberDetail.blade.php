<?php
$pageData = [];
$pageData['sideActive'] = 'memberList';
$pageData['barTitle'] = '知识商品';
?>
@extends('admin.baseLayout',$pageData)

@section("page_css")
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}"/>
    <link type=text/css rel="stylesheet" href="../css/admin/resManage/memberDetail.css?{{env('timestamp')}}"/>
@stop

@section("page_js")
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/resManage/memberDetail.js?{{env('timestamp')}}"></script>
@stop

@section("base_mainContent")
    <input type="hidden" class="admin_data"
           data-member_id="{{$member_info->id}}"
           data-member_price="{{$member_info->price / 100.00}}"
    >
    <div class="pageTopTitle">
        <a href="/member_list_page">会员列表</a> &gt; 会员详情
    </div>


    <div class="content">
        <div class="member_detail_part">
            <div class="member_icon_wrapper">
                @if(empty($member_info->img_url_compressed))
                    <img class="member_icon" src="{{$member_info->img_url}}">
                @else
                    <img class="member_icon" src="{{$member_info->img_url_compressed}}">
                @endif
            </div>
            <div class="member_detail_item_desc">
                <div class="member_title" title="{{$member_info->name}}">{{$member_info->name}}
                    <img class="member_icon_img" src="/images/admin/resManage/icon_member_2.png">
                </div>
                <div class="member_rights" title="{{$member_info->summary}}">{{$member_info->summary}}</div>
                <div class="member_price">
                    @if($member_info->period == 2592000)
                        ￥{{$member_info->price / 100.00}}元/月
                    @elseif($member_info->period == 7776000)
                        ￥{{$member_info->price / 100.00}}元/季度
                    @elseif($member_info->period == 15811200)
                        ￥{{$member_info->price / 100.00}}元/半年
                    @elseif($member_info->period == 31622400)
                        ￥{{$member_info->price / 100.00}}元/年
                    @else
                        ￥{{$member_info->price / 100.00}}元/年
                    @endif
                </div>
                <div class="member_num">会员数：{{$member_info->purchase_count}}</div>
            </div>
            <div class="toolBox">
                <ul>
                    @if( in_array($member_info->id,['p_5857d53b3342a_Tm6TjjTD','p_58e39a0550707_1WKNPejG','p_595f072157f45_PUt7bnoq'] ))
                    <li class="userDefined" style="cursor:pointer">自定义内容</li>
                    <li class="divide_line">&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                    @endif
                    <li class="copyHref" data-clipboard-text="{{$member_url}}">复制链接</li>
                    <li class="divide_line">&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                    <li class="operate" data-type="edit">编辑</li>
                    <li class="divide_line">&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                    @if($member_info->state == 1)
                        <li class="operate" data-type="show_member">上架</li>
                        <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                    @else
                        <li class="operate" data-type="hide_member">下架</li>
                        <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                    @endif
                    <li class="mainMoreBtn">更多
                        <ul class="mainMoreTool mainDownShow">
                            @if($member_info->h5_newest_hide)
                                <li class="operate" data-type="newListShow">在最新列表显示</li>
                            @else
                                <li class="operate" data-type="newListHide">不在最新列表显示</li>
                            @endif
                            @if($member_info->is_complete_info)
                                <li class="operate" data-type="closeForm">关闭资料填写</li>
                            @else
                                <li class="operate" data-type="openForm">开启资料填写</li>
                            @endif
                            @if($member_info->visible_on)
                                    <li class="operate" data-type="closeVisible">关闭内容展示</li>
                            @else
                                    <li class="operate" data-type="openVisible">开启内容展示</li>
                            @endif

                            @if($isHasLittleProgram)
                                <li data-type="copy_app_href" class="copyHref" id="copy_app_href"
                                    data-clipboard-text="{{\App\Http\Controllers\Tools\Utils::resourceH5UrlTransToAppUrl($member_url)}}">
                                    复制小程序链接
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>

                @if($member_info->state == 0)
                    <div class="member_show_state">已上架</div>
                @else
                    <div class="member_show_state">已下架</div>
                @endif
            </div>
        </div>

        <div class="member_operate_part">
            <div class="addMemberRights btnMid btnBlue" @if($member_info->is_distribute) style="background-color: grey" @endif>{{--，0-否、1-是',--}}
                添加会员权益
                @if($member_info->is_distribute == 0)
                    <ul class="clickBtnWrapper">
                        <li>
                            <a href="/create_resource_page?upload_channel_type=3&id={{$member_info->id}}&price={{$member_info->price / 100.00}}">
                                新建单品
                            </a>
                        </li>
                        <li class="selectRightsBtn" id="addSingle">选择已有</li>
                    </ul>
                @endif
            </div>
            <div class="operate_right_part">
                <select class="selector_resource_type browser-default">
                    <option id="firstOption" value="0">类型</option>
                    <option value="1">图文</option>
                    <option value="2">音频</option>
                    <option value="3">视频</option>
                    <option value="4">直播</option>
                </select>
                <input class="inputSearchAll inputDefault" placeholder="输入名称"
                       @if($search_content) value='{{$search_content}}' @endif>
                <div class="searchAllBtn btnSmall xeBtnDefault">搜索</div>
            </div>
        </div>
        <div class="member_rights_title">会员权益&nbsp;<span>（注：会员权益暂不支持打折优惠，后续版本增加该功能）</span></div>
        <div class="member_content_part">
            <ul class="content_tab_wrapper">
                <li id="packageTab">专栏</li>
                <li class="activeContentTab" id="singleTab">单品</li>
            </ul>

            <div class="contentAreaWrapper">
                <div class="member_list_wrapper">
                    @include('admin.resManage.resourceListOfMember', [
                            "single_list_member" => $single_list_member,
                            "is_distribute" => $member_info->is_distribute
                        ])

                    {{--@include('admin.resManage.packageListOfMember', $package_list_member)--}}
                </div>
                <div class="loadingS">
                    <div class="loadingSContent">
                        <svg viewBox="25 25 50 50" class="circular">
                            <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                        </svg>
                        <p class="loadingText">加载中</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
@stop

@section("base_modal")

    <div class="select_good_window" id="selectWindow">
        <div class="select_window">
            <div class="window_top_area">
                <div class="select_window_title">添加会员权益<span></span></div>
                <div class="close_icon_wrapper">
                    <img id="selctClose" class="close_icon" src="/images/icon_Pop-ups_close.svg">
                </div>
            </div>
            <div class="search_content_area">
                <input class="inputSearchPart inputDefault" placeholder="输入名称">
                <div id="selectSearchBtn" class="searchPartBtn btnSmall xeBtnDefault">搜索</div>
            </div>
            <ul class="selectGoodTabWrapper">
                <li id="selectGoodPackageTab" value="0">专栏</li>
                <li class="activeContentTab" id="selectGoodSingleTab" value="1">单品</li>
            </ul>
            <div class="select_content_area">
                <div id="selectAreaListWrapper">
                    <div id="selectAreaList">

                    </div>
                </div>
                <div class="loadingS_in_window">
                    <div class="loadingSContent">
                        <svg viewBox="25 25 50 50" class="circular">
                            <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                        </svg>
                        <p class="loadingText">加载中</p>
                    </div>
                </div>
            </div>

            <div class="button_area">
                <div id="selectCancelBtn" class="cancel_btn btnMid xeBtnDefault">取消</div>
                <div id="selectOkBtn" class="next_step_btn btnMid btnBlue">确定</div>
            </div>
        </div>
    </div>

    <div class="set_price_window" style="display: none">
        <div class="set_window">
            <div class="window_top_area">
                <div class="select_window_title">设置单品售价</div>
                <div class="close_icon_wrapper_2">
                    <img class="close_icon" src="/images/icon_Pop-ups_close.svg">
                </div>
            </div>
            <div class="resource_desc_area">
                <div class="resourceSingleWrapper">
                    <div class="resource_desc">
                        <div class="resource_icon_wrapper_3">
                            <img class="resource_icon_3" src="/images/icon_yunying.png">
                        </div>
                        <span class="resource_title_3">--</span>
                    </div>
                    <span class="resource_type_3">--</span>
                    <span class="resource_time_3">--</span>
                </div>
            </div>
            <div class="set_price_area">
                <span class="set_price_word_1">设置价格</span>
                <input class="input_setting_price inputDefault" placeholder="请输入价格">
                <span class="set_price_word_2">元</span>
            </div>
            <div class="button_area">
                <div class="cancel_set_price btnMid xeBtnDefault">取消</div>
                <div class="confirm_price_btn btnMid btnBlue">确定</div>
            </div>
        </div>
    </div>

    @include("admin.resManage.inviteGuestWindow")
    {{--直播间显示设置--}}
    @include("admin.resManage.aliveShowSetModal")
@stop
