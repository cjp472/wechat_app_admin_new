<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    {{--弹窗--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />

    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    <link type=text/css rel="stylesheet"
          href="../css/admin/communityOperate/communityList.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    {{--弹窗--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>

    {{-- 复制链接 --}}
    <script type="text/javascript" src="../js/external/clipboard.min.js"></script>
    {{-- 二维码 --}}
    <script type="text/javascript" src="../js/external/qrcode.js"></script>
    {{-- 业务代码 --}}
    <script type="text/javascript"
            src="../js/admin/communityOperate/communityList.js?{{env('timestamp')}}"></script>
@endsection



@section('base_mainContent')
    <div class="pageTopTitle">
        <a>社群运营</a> &gt; 小社群
        <a href="/helpCenter/problem?document_id=d_590c8d4c6037b_Ky8rfHc3" target="_blank" class="help_document_link">小社群功能说明</a>
    </div>
    <div class="content">
        <div class="top_area">

            <div class="addBtn btnMid btnBlue" id="addBtn">
                <a href="/smallCommunity/createCommunity">创建社群</a>
            </div>

            <div class="right_operate">
                <select class="selector" id="selector">
                    <option value="-1" id="communityStateAll" @if($ruler==-1) selected @endif>全部</option>
                    <option value="0" id="communityStateUp" @if($ruler==0) selected @endif>已上架</option>
                    <option value="1" id="communityStateDown" @if($ruler==1) selected @endif>已下架</option>
                </select>
                <input class="inputSearchAll inputDefault" placeholder="请输入社群名称" value="{{$search}}">
                <div class="searchAllBtn btnSmall xeBtnDefault">搜索</div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="td_left" style="width: 20%">社群名称</th>
                    <th>群主</th>
                    <th>价格(元)</th>
                    <th style="width: 15%">关联专栏/会员</th>
                    <th>状态</th>
                    <th>成员数量</th>
                    <th>动态数</th>
                    <th class="td_right toolTh">操作</th>
                </tr>
            </thead>
            <tbody>
            @foreach($communityList as $key => $value)
                <tr>
                    <td class="td_left" >
                        <div class="communityIconName">
                            <img class="avatarIcon" src="{{$value->img_url}}" alt="头像">
                            <p class="username" title="{{$value->title}}">{{$value->title}}</p>
                        </div>
                    </td>
                    <td>@if($value->admin==0){{$value->admin_name}}@elseif($value->admin==1)--@endif</td>

                    @if($value->payment_type == 3)
                        <td>--</td>
                    @else
                        <td>{{$value->piece_price ? $value->piece_price/100 : "免费"}}</td>
                    @endif

                    <td class="relevanceTd">@if($value->product_count==0)
                            --
                        @elseif($value->product_count==1)
                            @if($value->product_name_str[1]['type']=='product')
                                <span>专栏&nbsp;&nbsp;:&nbsp;{{$value->product_name_str[1]['name']}}</span>
                            @else
                                <span>会员&nbsp;&nbsp;:&nbsp;{{$value->product_name_str[1]['name']}}</span>
                            @endif
                        @elseif($value->product_count>1)
                            <div class="productCountBox">
                                {{$value->product_count}}
                                <img class="downIcon" src="../images/arrow_down.png" alt="">
                            </div>
                            <div class="relevanceBox" style="display: none">
                                @foreach($value->product_name_str as $k => $v)
                                    <div>
                                        @if($v['type']=='product')
                                            专栏：{{$v['name']}}
                                        @elseif($v['type']=='member')
                                            会员：{{$v['name']}}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif</td>
                    <td>@if($value->community_state==0) 上架 @else 下架 @endif</td>
                    <td>{{$value->users_count}}</td>
                    <td>{{$value->feeds_count}}</td>
                    <td class="td_right">
                        <div class="toolBox">
                            <ul>
                                @if($value->admin==1)
                                    <li class="setHolderBtn" data-id="{{$value->id}}">设置群主</li>
                                @else
                                    <li class="changeHolderBtn" data-old="{{$value->admin_name}}" data-id="{{$value->id}}">转移群主</li>
                                @endif
                                <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                                <li><a href="/smallCommunity/dynamicList?community_id={{$value->id}}">管理</a>
                                </li>
                                <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                                <li><a href="/smallCommunity/editCommunity?id={{$value->id}}">编辑</a></li>
                                <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
                                <li class="moreBtn">更多<span class="caret"></span>
                                    <ul class="moreTool downshow">
                                        @if($value->community_state==0)
                                            <li class="communityHide" data-id="{{$value->id}}">下架</li>
                                        @elseif($value->community_state==1)
                                            <li class="communityShow" data-id="{{$value->id}}">上架</li>
                                        @endif
                                        <li class="copyHref" data-clipboard-text="{{$value->pageUrl}}">复制链接</li>
                                    </ul>
                                </li>
                            </ul>
                            @if($key == 0)
                                <div class="newCommunityTipWrapper">
                                    <div class="newCommunityTip">第一次新建完社群, 请先设置群主</div>
                                    <span class="triangleIcon"></span>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--页标--}}
        <div class="list-page">
        <?php echo $communityList->appends(array('search' => $search, 'ruler' => $ruler))->render(); ?>
        </div>
    </div>
@endsection

@section('base_modal')
    <div class="darkScreen" style="display: none">
        <div class="GroupHolderSetBox">
            <div class="holderSetClose"><img src="../images/icon_Pop-ups_close.svg" alt="icon"></div>
            <div class="searchBox">
                <div class="holderSearchContent">
                    <input type="text" class="inputDefault holderSearchInput">
                    <img src="../images/icon_fenxiao_search.png" alt="icon" class="searchIcon">
                    <div class="btnSmall xeBtnDefault holderSearchBtn">搜索</div>
                </div>
                <input type="hidden" id="community_id" />
                <div class="holderList">
                    <div class="holderListContent">
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}
                        {{--<div class="holderListPart" data-userid="jserkid">--}}
                            {{--<div class="holderCheckBox">--}}
                                {{--<input class="with-gap" id="aaa1" name="group2" type="radio" checked/>--}}
                                {{--<label for="aaa1">--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="holderListImg"><img src="../images/login_account.png"></div>--}}
                            {{--<span class="holderListName">用户昵称</span>--}}
                        {{--</div>--}}

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
                <div class="qrCodePaper z-depth-2" style="display: none">
                    <p>微信扫一扫</p>
                    <p>设置群主</p>
                    <div class="qrImg" id="qrImgId"></div>
                </div>
                <div class="boxConfirm btnMid btnBlue">确定</div>
                <div class="setWxFriend btnMid xeBtnDefault setAdmin">微信扫一扫设置群主</div>
            </div>
        </div>
    </div>
@endsection