<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    <link rel="stylesheet" href="../css/admin/marketing/marketing1.css?{{env('timestamp')}}">
    {{--弹出提示--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    {{--弹出提示--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{--表单检查--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{-- 腾讯云上传V4 --}}
    <script type="text/javascript" src="../sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="../js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--上传工具类--}}
    <script src="../js/admin/utils/upload.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script type="text/javascript" src="../js/admin/marketing/invitation.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    <div class="pageTopTitle"><a href="/marketing">营销中心</a> > 邀请卡</div>
    <div class="pageContent">
        <div class="InviteHeader">
            <div class="salerTitle">邀请卡</div>
            <div class="inviteIntro">
                邀请卡是小鹅通推出的一款助力用户主动传播的功能。用户使用邀请卡邀请好友（不包括自己）成功付费课程，该用户将获得基于您设置的奖励比例所计算的奖励金额。
            </div>
            <div class="salerhelpBtn"><a href="/helpCenter/problem?document_id=d_58f46b51cf963_woTcSpty" target=_blank><span>使用教程</span></a></div>
        </div>

        @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("marketing_invite_card", "version_type"))
            <div class="salerContentDiv">
                <div class="salerContent" id="salerAllContent">
                    <div class="goodsSearch">
                        <span class="goodListIntro">商品列表（邀请卡订单数据及交易记录请前往“财务管理”查看）</span>
                        <div class="goodsSearchContent">
                            <input type="text" class="goodsSearchInput inputDefault" placeholder="输入名称"
                                   @if($name)value="{{$name}}"@endif/>
                            <div class="goodsSearchBtn btnSmall xeBtnDefault">搜索</div>
                        </div>
                    </div>
                    <div class="tableContainer tableContainer1">
                        <table cellpadding="0" class="table">
                            <thead>
                            <tr>
                                <th class="td_left">商品名称</th>
                                <th>商品类型</th>
                                <th>单价（元）</th>
                                <th>销量</th>
                                <th>当前奖励比例</th>
                                <th>当前奖励金额（元）</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($paginator as $key => $i)
                                <tr>
                                    <td class="td_left"><img src="{{$i->img_url_compressed}}" alt="商品图标">
                                        <p class="goodTitle">{{$i->name}}</p></td>
                                    <td>@if($i->goods_type==0) 专栏 @elseif($i->goods_type==5) 会员 @else 单品 @endif</td>
                                    <td>￥{{$i->price/100}}</td>
                                    <td>{{$i->sum}}</td>
                                    {{--<td>@if($i->is_invite) 是 @else 否 @endif</td>--}}
                                    <td>@if($i->price/100>1) @if($i->distribute_percent){{$i->distribute_percent}}%@else{{0}}
                                        %@endif @else -- @endif</td>
                                    <td>
                                        @if($i->price/100>1)￥@if($i->distribute_price){{$i->distribute_price/100}}@else{{0}}@endif @else -- @endif</td>
                                    <td><span class="salerGoodsOperate inviteOperate"
                                              data-good_type="{{$i->goods_type}}"
                                              data-price="{{$i->price/100}}"
                                              data-name="{{$i->name}}"
                                              data-is_invite="{{$i->is_invite}}"
                                              data-id="{{$i->id}}"
                                              data-distribute_percent="@if($i->distribute_percent){{$i->distribute_percent}}@else{{0}}@endif"
                                              data-inviteposter="{{$i->invite_poster}}"
                                              data-isshowinfo="{{$i->is_show_userinfo}}"
                                        >设置</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($paginator)==0)
                        <div class="contentNoneTip">没有相应的数据</div>
                    @endif

                    @if($paginator)
                        <div class="list-page">
                            @if(empty($name))
                                {{ $paginator->render() }}
                            @else
                                {!!$paginator->appends(['name'=>$name])->render() !!}
                            @endif
                        </div>
                    @endif
                </div>
                <div class="loadingS" style="display: none;">
                    <!-- <div class="loadingSPart loadingSPart1"></div>
                    <div class="loadingSPart loadingSPart2"></div> -->
                    <div class="loadingSContent">
                        <svg viewBox="25 25 50 50" class="circular">
                            <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                        </svg>
                        <p class="loadingText">加载中</p>
                    </div>
                </div>
            </div>
        @else
            <div class="promptText">
                <div class="promptTextWord">当前版本不支持邀请卡功能，如需开启请升级至成长版或专业版</div>
                <a href="/upgrade_account" class="updateAtOnceBtn btnMid btnBlue">立即升级</a>
            </div>
        @endif
    </div>
@stop


@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
    <div class="setSaleRatioWindow" style="display:none;">
        <div class="setSaleWindow">
            <div class="windowTopArea">
                <div class="selectWindowTitle">邀请卡设置</div>
                <div class="closeIconWrapper3">
                    <img class="closeIcon" src="/images/icon_Pop-ups_close.svg">
                </div>
            </div>
            <div class="setSaleContentArea">
                <div class="saleGoodsDesc">
                    <div class="setSaleTitle1">商品名称</div>
                    <div class="saleGoodsName">--</div>
                </div>
                <div class="setPercent">
                    <span class="setSaleTitle3">设置奖励比例</span>
                    <input class="inputRadioValue inputDefault" placeholder="0-50"
                           onkeyup="rateCheckNum1(this.value, this)">
                    <span>&nbsp;%</span>
                </div>
                {{--自定义邀请卡--}}
                <div class="salerSection">
                    <div class="salerSectionT salerSectionT1">自定义邀请卡</div>
                    <div class="salerSectionC">
                      <div class="salePosterWord">
                        点击上传您制作完成的邀请卡图片，建议尺寸750x1334px
                        或9：16，JPG、PNG格式， 图片小于1M
                      </div>
                      <div class="btnMid xeBtnDefault uploadImgBtn">
                        上传邀请卡
                        <input id="uploadImage" accept="image/jpeg,image/png,image/gif,image/bmp"
                               class="upLoadImage" type="file"/>
                        <input type="hidden" id="imgUrl" type="text"/>
                      </div>
                      <div class="uploadIntro">
                          <a target="_blank" href="/helpCenter/problem?document_id=d_5916afbae8b7f_6R0wiRbZ">如何自定义邀请卡</a>
                      </div>
                    </div>
                </div>
                {{--是否显示用户的头像昵称--}}
                <div class="salerSection" style="margin-bottom: 0;margin-top: -12px;">
                    <div class="salerSectionT salerSectionT1"></div>
                    <div class="salerSectionC">
                        <input class="with-gap" id="showInfo" name="group1" type="radio"/>
                        <label for="showInfo" class="showInfo">
                            显示头像昵称
                        </label>
                        <input class="with-gap" id="hideInfo" name="group1" type="radio" checked/>
                        <label for="hideInfo" class="hideInfo">
                            不显示
                        </label>
                    </div>
                </div>
                <div class="setRadioWord">1.奖励比例是邀请者获得收益的比例，商品价格大于1元才可设置奖励比例</div>
                <div class="setRadioWord">2.功能开启，用户通过邀请卡购买商品，邀请者可获得对应的奖励金额</div>
                <div class="setRadioWord">3.平台收益自动进入可提现余额中，您可在财务管理>个人模式收入中查看到该类型订单的收益，并提现至您的微信账户中</div>
            </div>
            <div class="rightArea">
                <div class="phonePreview">
                    @include('component.loadingPartial')
                    <div class="previewModel1"><img src="../images/admin/marketing/qr_model.png" alt=""></div>
                    <div class="previewModel2"><img src="../images/admin/marketing/fingerprint.png" alt=""></div>
                    <img class="phonePreviewImg" id="reBackImg" src="/images/admin/resManage/set_sale_preview.png">
                </div>
            </div>
            <div class="buttonArea">
                <div class="cancelSaleBtn btnMid xeBtnDefault">取消</div>
                <div class="confirmSaleBtn btnMid btnBlue">确定</div>
            </div>
            <div class="btnSmall xeBtnDefault deleteImg1" style="display: none">删除</div>
        </div>
    </div>

@stop
