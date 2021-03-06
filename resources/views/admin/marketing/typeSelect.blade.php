<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    <link rel="stylesheet" href="../css/admin/marketing/marketing1.css?{{env('timestamp')}}">
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/marketing/marketingSelect.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <input type="hidden" id="versionType" value="{{\App\Http\Controllers\Tools\AppUtils::get_version_type()}}" />
    <div class="pageTopTitle">营销中心</div>
    <div class="pageVessel">
    <p>营销功能</p>

      @if( session("access")["114"] == 1)

      <div class="marketingPart saler">
          <div class="PartIcon"><img src="../images/admin/marketing/saler.png" alt="图标"></div>
          <div class="PartWord">推广员</div>
      </div>
      @endif


      @if( session("access")["115"] == 1)

      <div class="marketingPart invitationCode">
            <div class="PartIcon"><img src="../images/admin/marketing/code.png" alt="图标"></div>
            <div class="PartWord">邀请码</div>
        </div>
      @endif

      @if( session("access")["113"] == 1)
          <div class="marketingPart channel">
              <div class="PartIcon"><img src="../images/admin/marketing/icon-pagedata.png" alt="图标"></div>
              <div class="PartWord">页面统计</div>
          </div>
      @endif

      @if( session("access")["116"] == 1)

      <div class="marketingPart invitation">
        <div class="PartIcon"><img src="../images/admin/marketing/invitation.png" alt="图标"></div>
        <div class="PartWord">邀请卡</div>
      </div>
      @endif

      @if( session("access")["117"] == 1)

      <div class="marketingPart discount">
        <div class="PartIcon"><img src="../images/admin/marketing/discount.png" alt="图标"></div>
        <div class="PartWord">优惠券</div>
      </div>
      @endif
      @if(\App\Http\Controllers\Tools\AppUtils::get_version_type()==3)
      <div class="marketingPart share_use">
          <div class="PartIcon"><img src="../images/admin/marketing/share_frends.png" alt="图标"></div>
          <div class="PartWord">请好友看</div>
      </div>
      @endif

      <div class="marketingPart chosen_shop">
          <div class="PartIcon"><img src="../images/admin/marketing/channel.png" alt="图标"></div>
          <div class="PartWord">分销市场</div>
      </div>

  </div>
  <div class="pageVessel">
      <p>辅助工具</p>
      <div class="marketingPart shortLink">
          <div class="PartIcon"><img src="../images/admin/marketing/shortLink.png" alt="图标"></div>
          <div class="PartWord">短链接生成</div>
      </div>
  </div>


@stop


@section('base_modal')

@stop