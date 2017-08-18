<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '内容列表';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" href="../css/admin/memberList.css?{{env('timestamp')}}"/>     {{--css--}}
@endsection


@section('page_js')
    <script src="../js/admin/memberList.js?{{env('timestamp')}}"></script>               {{--js--}}
@endsection

@section('base_mainContent')
    <div class="tab_div">
        <div class="tab_type" id="tab_audio">音频</div>
        <div class="tab_type" id="tab_video">视频</div>
        <div class="tab_type" id="tab_article">图文</div>
        <div class="tab_type" id="tab_alive">直播(<span class="red-font">公测</span>)</div>
        <div class="tab_type" id="tab_package">专栏</div>
        <div class="tab_type tab_active" id="tab_member">会员</div>
    </div>
    <div class="member_content">
        <div class="content_word_desc_1">
            会员是一种全新的付费形式（按时长），表现内容的方式与专栏类似，已开通会员的用户可在会员有效期内查看权益内容，同时开通会员后可点亮会员专属标识，彰显会员身份。
        </div>
        <div class="content_word_desc_2">
            <div>会员功能开放步骤如下：</div>
            <div style="float:left;">1）</div>  <div style="margin-left: 25px;">3月10日，支持已上架专栏升级为会员模式（按时长计费），请联系产品鹅升级</div>
            <div style="float:left;">2）</div>  <div style="margin-left: 25px;">3月底前，管理台全新改版，支持直接上架会员产品</div>
            <div style="float:left;">3）</div>  <div style="margin: 0 0 20px 25px;">4月开始，持续迭代，逐步开放会员折扣等功能</div>

            <div style="float:left;">问：</div>  <div style="margin-left: 28px;">我的专栏是否应该升级为会员？</div>
            <div style="float:left;">答：</div>  <div style="margin: 0 0 20px 28px;">
                绝大部分专栏都不需要升级为会员。<br>
                只有有明确会员形式更新计划、并可保障购买会员者权益的产品可采用这种形式。
            </div>

            <div style="float:left;">问：</div>  <div style="margin-left: 28px;">开设会员功能有什么风险？</div>
            <div style="float:left;">答：</div>  <div style="margin: 0 0 20px 28px;">采用会员模式的产品，需要内容生产者具有长期、持续的生产能力，请务必做好评估。</div>

            <div style="float:left;">问：</div>  <div style="margin-left: 28px;">为什么暂不支持自行开通？</div>
            <div style="float:left;">答：</div>  <div style="margin: 0 0 30px 28px;">为控制风险并保障消费者权益，我们需要对这种产品形式进行一定时间的评估，若有意愿请联系产品鹅后表明相关情况，我们会判定您是否具有会员内容持续更新的能力，通过评估后即人工为您开通。</div>

            <div style="margin-left: 0px; float:left;">联系产品鹅：</div>
            <div style="margin-left: 90px;">产品鹅初号机（微信）：exiaomei1994</div>
            <div style="margin-left: 90px;">产品鹅贰号机（微信）：chanpine2</div>
        </div>

    </div>

    <div class="content_phone_preview">
        <img class="phone_preview_img" src="/images/member_phone_preview.png">
        <p class="phone_preview_title">示例图</p>
    </div>

@stop







