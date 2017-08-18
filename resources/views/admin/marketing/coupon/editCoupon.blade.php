<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle']='营销中心';
?>

@extends('admin.baseLayout',$pageData)

@section("page_css")
    {{--时间插件--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
    {{--弹窗--}}
    <link href="../css/external/xcConfirm.css" rel="stylesheet" type="text/css"/>
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <link type=text/css rel="stylesheet" href="../css/admin/marketing/coupon.css?{{env('timestamp')}}" />
@stop

@section("page_js")
    {{--时间插件--}}
    <script src="../js/external/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    {{--弹窗--}}
    <script src="../js/external/xcConfirm.js" type="text/javascript"></script>
    {{--表单检查工具类--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script src="../js/admin/base.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/marketing/coupon/newForm.js?{{env('timestamp')}}"></script>
@stop

@section("base_mainContent")
    <div class="pageTopTitle">
        <a href="/coupon/index">优惠券</a> &gt;
        @if($data->type==0)
         修改商品优惠券
        @elseif($data->type==1)
         修改店铺优惠券
        @endif
    </div>
    <form class="formContent" action="/coupon/add" data-page_type="{{$data->page_type}}" method="POST">
   
    
        <table style="width:100%">
            <tr>
                <td><div class="msgTitle">基本信息</div></td>
            </tr>
            <tr>
                <td><label for="couponName">优惠券名称 <span class="redKey">*</span></label></td>
                <td><input id="couponName grayBackground" class="inputStyle" type="text" placeholder="优惠券名称，不超过10个字" value="{{$data->coupon_info->title}}" readonly disabled name="title"></td>
            </tr>
            <tr>
                <td><label for="">面额<span class="redKey">*</span></label></td>
                <td><input class="inputStyle grayBackground" type="text" placeholder="请输入优惠券的面额" value="{{$data->coupon_info->price/100}}" readonly disabled name="price"><span>元</span></td>
            </tr>
            <tr>
                <td>使用条件<span class="redKey">*</span></td>
                <td>
                    
                    <div class="radioBox">
                        {{--<input class="with-gap " id="useCdn_1" type="radio" name="useCdn" checked readonly disabled>--}}
                        <label for="useCdn_1"><span class="theText">满</span></label>
                        <input class="inputStyle grayBackground" style="width:120px"  readonly disabled value="{{$data->coupon_info->require_price/100}}" type="text" disabled="disabled" readonly="readonly" name="re_price">元使用
                    </div>
            
                </td>
            </tr>
            <tr>
                <td><label for="">有效时间<span class="redKey">*</span></label></td>
                <td>
                    <div class="dateBox">
                        <input class="dateInput dateSetInput inputStyle grayBackground" id="dateStart" name="valid_at" value="{{$data->coupon_info->valid_at}}" readonly disabled type="text"/>
                        <div class="dateUpIcon">
                            <img src="../images/admin/resManage/icon_riqi.png"/>
                        </div>
                    </div>
                    <div class="timeTip">至</div>
                    <div class="dateBox">
                        <input class="dateInput dateSetInput inputStyle grayBackground" id="dateEnd" name="invalid_at" value="{{$data->coupon_info->invalid_at}}" readonly disabled type="text"/>
                        <div class="dateUpIcon">
                            <img src="../images/admin/resManage/icon_riqi.png"/>
                        </div>
                    </div>
                    <span class="timeTip">开始前买家可以领取但不能使用</span>
                </td>
            </tr>
            <tr>
                <td><label for="">发行量<span class="redKey">*</span></label></td>
                <td>
                    <input class="inputStyle" type="text" placeholder="请输入数量" onkeyup="this.value=this.value.replace(/\D/g,'')" value="{{$data->coupon_info->count}}" name="count"><span>不超过1,000,000张</span><br/>
                    <div class="remindBox">修改发行量时只能增加不能减少，请谨慎设置。</div>
                </td>
            </tr>
            <tr>
                <td><label for="">每人限领<span class="redKey">*</span></label></td>
                <td>
                    <div class="SelectBox">
                    <select class="couponNum grayBackground" name="receive_rule" disabled readonly>
                        <option value="1" selected="selected">
                          {{$data->coupon_info->receive_rule}}
                        </option>
                    </select>
                    <span class="theText">张</span>
                </div>
                </td>
            </tr>
            @if($data->type==0)
            <tr class="isGoods">
                <td>商品范围<span class="redKey">*</span>
                <td>
                    <button type="button" class="plusArea" data-plus-type="{{$data->type}}">+添加商品</button>
                    <div class="addedList">
                        @foreach($data->res_info as $key => $value)
                            <div data-tab="{{$value->good_type}}" data-id="{{$value->id}}" class="addedItem">
                                <span>
                                @if($value->good_type<5)
                                单品
                                @elseif($value->good_type==5)
                                专栏
                                @elseif($value->good_type==6)
                                会员
                                @endif
                                </span>
                                <img src="{{$value->img_url_compressed}}" alt="{{$value->title}}">
                                <span class="spanCon">{{$value->title}}</span>
                            </div>
                        @endforeach
                    </div>
                    <br/>
                    <div class="remindBox">优惠券一旦创建，指定商品只能添加，不能删除。</div>
                </td>
            </tr>
            @endif
            <tr>
                <td><div class="msgTitle">推广信息</div></td>
            </tr>
            <tr>
                <td>推广方式</td>
                <td>
                    <div>
                        @if($data->coupon_info->spread_type==0)
                        <input class="btnSmall func funcClick" id="userGet" type="button" name="getType" value='用户领取'>
                        {{--<input class="btnSmall func" id="shopPut" type="button" name="getType" value='商家发放'>--}}
                        @else
                        <input class="btnSmall func" id="userGet" type="button" name="getType" value='用户领取'>
                        {{--<input class="btnSmall func funcClick" id="shopPut" type="button" name="getType" value='商家发放'>--}}
                        @endif
                        <div class="aboutThePop">推广方式说明
                            <div class="aboutContent">
                            <p>用户领取：商家复制优惠券链接，用户通过链接可领取商家优惠券。</p>
                            <p><span style="color:red">*</span>勾选“用户可在商品详情页领取”，优惠券将在商品详情页展示，访问页面用户可直接领取并使用。</p>
                            </div>
                        </div>
                    </div>
                    @if($data->coupon_info->spread_type==0)
                    <div class="aboutGet">
                        @if($data->coupon_info->is_show==0)
                        <input id="putOn" type="checkbox" readonly disabled>
                        @else
                        <input id="putOn" type="checkbox" checked readonly disabled>
                        @endif
                        <label for="putOn">用户可在商品详情页领取</label>
                        {{--<div class="remindBox"> <i class="notice"></i>勾选后优惠券将立即投放在商品详情页。</div>--}}
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="bottomLine">
                <td></td>
                <td>
                    <div class="submitBtm">
                        <input id="submitList" class="btnBlue btnMid" type="button" value ="保存" >
                    </div>
                </td>
            </tr>
        </table>
            
        
    </form>

@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
        <div class="downloadPop" id="addModal" style="display:none">
            <div class="darkScreen"></div>
            <div class="addBox">
                <div class="pageTopTitle">
                    <span class="outPutTime">添加商品</span>
                    <button type="button" class="close closePop" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" style="height:475px"> 
                    <div class="searchHeader">
                        <input class="" id="kwContent" type="text" placeholder="输入名称"/> 
                        <button class="btnMid" id="forTab" value="">搜索</button>
                    </div>
                    <div class="tab">
                        <a data-type="0" class="clicked">单品</a>
                        <a data-type="1">专栏</a>
                        <a data-type="2">会员</a>
                    </div>
                    <div class="initList">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="xeBtnDefault btnMid closePop">关闭</div>
                    <div class="btnBlue btnMid" id="add">添加</div>
                </div>
            </div>
        </div>
@stop