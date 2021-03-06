<?php
$pageData = [];
$pageData['sideActive'] = 'marketing_admin';
$pageData['barTitle'] = '营销中心';
?>
@extends('admin.baseLayout',$pageData)
@section('page_css')
    {{--<link href="../css/external/materialize.css" rel="stylesheet" type="text/css"/>--}}
    <link rel="stylesheet" href="../css/admin/marketing/marketing1.css?{{env('timestamp')}}">
    <link rel="stylesheet" href="../css/admin/marketing/salerList.css?{{env('timestamp')}}">
    {{--时间选择器--}}
    <link type=text/css rel="stylesheet" href="../css/external/selectTime.css?{{env('timestamp')}}">
    {{--弹出提示--}}
    <link type=text/css rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
@endsection

@section('ahead_js')
    {{--文本编辑器--}}
    <script src="../ueditor/ueditor.config.js" type="text/javascript"></script>
    <script src="../ueditor/ueditor.all.min.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--秀米sdk--}}
    <script src="../ueditor/xiumi-ue-dialog-v5.js" type="text/javascript"></script>
@endsection

@section('page_js')
    {{--弹出提示--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    {{--时间选择器--}}
    <script type="text/javascript" src="../js/external/dateRange.js?{{env('timestamp')}}"></script>
    {{-- 腾讯云上传V4 --}}
    <script type="text/javascript" src="../sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="../js/admin/utils/v4QcloudUpload.js"></script>
    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--上传工具类--}}
    <script src="../js/admin/utils/upload.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--复制插件--}}
    <script type="text/javascript" src="../js/external/clipboard.min.js"></script>
    {{--表单检查--}}
    <script src="../js/admin/utils/formCheck.js?{{env('timestamp')}}" type="text/javascript"></script>
    <script type="text/javascript" src="../js/admin/marketing/marketing.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')
    <div class="pageTopTitle"><a href="/marketing">营销中心</a> > 推广员</div>
    <div class="pageContent">
        <div class="masterSwitch">
            <div class="salerTitle">推广员</div>
            <div class="salerIntro">
                推广员是小鹅通推出的一款可帮助商户拓宽推广渠道的应用营销工具，商户通过制定推广计划招募买家加入推广队伍，并在其成功推广后给予奖励，以此给商户带来更多传播和促进销量提升（推广员通过自己的推广链接购买不会获得奖励）。您可在个人模式收入中查看通过推广员产生的订单收益，并可随时申请提现。
            </div>
            <div class="salerhelpBtn">
                <span>相关教程：</span>
                <a href="/helpCenter/problem?document_id=d_591c1968b2c01_bSdPa3WF" target="_blank">&nbsp;【功能介绍及使用流程】&nbsp;</a>
                <a href="/helpCenter/problem?document_id=d_591d01729c551_D4z5Fz19" target="_blank">&nbsp;【常见问题与解答】&nbsp;</a>
                <a href="/helpCenter/problem?document_id=d_58f58bcb77cff_Sqy3QSe8" target="_blank">&nbsp;【配置教程】&nbsp;</a>
            </div>
            @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("marketing_saler", "version_type"))
                <div id="salerToggle" class="salerSwitch @if($switch==1) opening @else closing @endif" data-toggle="{{$switch}}">
                    @if($switch==1) <span>开启</span> @else <span>关闭</span> @endif

                    <div class="salerSwitchCircle z-depth-2"></div>
                </div>
            @endif
        </div>
        <div class="salerNav">
            <div class="salerNavPart salerNavList" data-classurl="/distribute/saler">推广员</div>
            <div class="salerNavPart salerNavGoodsList" data-classurl="/distribute/goods">商品列表</div>
            <div class="salerNavPart salerNavRecord" data-classurl="/distribute/records">推广记录</div>
            <div class="salerNavPart salerNavCount" data-classurl="/distribute/achieve">业绩统计</div>
            <div class="salerNavPart salerNavPlan" data-classurl="/distribute/recruit">招募计划</div>
            <div class="salerNavPart salerNavSet" data-classurl="/distribute/set">设置</div>
            <div class="salerNavPart salerNavChosen hide" data-classurl="/distribute/chosen " >内容分销</div>

            <div class="navLine"></div>

        </div>
        <div class="salerContentDiv">
            @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("marketing_saler", "version_type"))
                <div class="salerContent" id="salerAllContent">
                </div>
                <div class="loadingS">
                    <!-- <div class="loadingSPart1 loadingSPart1"></div>
                    <div class="loadingSPart loadingSPart2"></div> -->
                    <div class="loadingSContent">
                    <svg viewBox="25 25 50 50" class="circular">
                        <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                    </svg>
                    <p class="loadingText">加载中</p>
                    </div>
                </div>
            @else
                <div class="promptText">
                    <div class="promptTextWord">当前版本不支持推广员功能，如需开启请升级至成长版或专业版</div>
                    <a href="/upgrade_account" class="updateAtOnceBtn btnMid btnBlue">立即升级</a>
                </div>
            @endif
        </div>
        <input type="hidden" id="xcx_app_id" value="">
    </div>
@stop


@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
    <div class="modal fade downloadPop" id="ExportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="downloadPop_office">
                <div class="pageTopTitle">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div ><span class="modal-title" style="font-size: 18px" id="myModalLabel"></span></div>
                </div>
                <div class="modal-body">
                    <div>
                        <span class="outPutTime" id="timeIsGood">订单产生时间</span>
                        <select class="form-control" id="export_time">
                        </select>
                        <div>
                            <span>office使用版本</span>
                            <input class="with-gap popS" id="Office_false" name="selectOffice" type="radio" value="0">
                            <label for="Office_false">非office2003</label>
                            <input class="with-gap popS" id="Office_true" name="selectOffice" type="radio" value="1" checked>
                            <label for="Office_true">office2003</label>
                            <div class="declaration">如果下载文件出现乱码，请选择另一个office版本选项进行下载</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="xeBtnDefault btnMid" data-dismiss="modal">关闭</button>
                    <button type="button" class="btnBlue btnMid" id="exportExcel">确定</button>
                </div>
        </div>
    </div>
    <div class="darkScreen" style="display:none;">
        <div class="salerSetBox">
            <div class="setBoxHeader">推广设置
                <div class="setBoxClose"><img src="../images/icon_Pop-ups_close.svg"></div>
            </div>
            <div class="salerSetBoxContent">
                {{--推广--}}
                <div class="salerSection">
                    <div class="salerSectionT">推广<span class="redStart">*</span></div>
                    <div class="salerSectionC">
                        <div class="radioBtn3">
                            <input class="with-gap" id="tBtn1" name="group2" type="radio"/>
                            <label for="tBtn1" class="active distributeY">
                                参与
                            </label>
                        </div>
                        <div class="radioBtn3">
                            <input class="with-gap" id="tBtn2" name="group2" type="radio"/>
                            <label for="tBtn2" class="distributeN">
                                不参与
                            </label>
                        </div>
                    </div>
                </div>
                {{--佣金比例--}}
                <div class="salerSection">
                    <div class="salerSectionT">佣金比例<span class="redStart">*</span></div>
                    <div class="salerSectionC">
                        <div class="defaultRate">
                            <input class="with-gap" id="yjBtn1" name="group3" type="radio"/>
                            <label for="yjBtn1" class="yjBtn1">
                                默认比例 <span class="yjrate"></span>%
                            </label>
                        </div>
                        <div class="userDefinedRate">
                            <input class="with-gap" id="yjBtn2" name="group3" type="radio"/>
                            <label for="yjBtn2" class="yjBtn2">
                                <span>自定义比例</span>
                            </label>
                            <input type="text" class="selfRateInput inputDefault" readonly="readonly"
                                   disabled="disabled" onkeyup="rateCheckNum(this.value, this)"
                                   placeholder="百分比1-50"/>&nbsp;%
                        </div>
                    </div>
                </div>
                {{--邀请奖励--}}
                <div class="salerSection">
                    <div class="salerSectionT">邀请奖励<span class="redStart">*</span></div>
                    <div class="salerSectionC">
                        <div class="defaultRate">
                            <input class="with-gap" id="yqBtn1" name="group4" type="radio"/>
                            <label for="yqBtn1" class="yqBtn1">
                                默认比例 <span class="yqrate"></span>%
                            </label>
                        </div>
                        <div class="userDefinedRate">
                            <input class="with-gap" id="yqBtn2" name="group4" type="radio"/>
                            <label for="yqBtn2" class="yqBtn2">
                                <span>自定义比例</span>
                            </label>
                            <input type="text" class="otherRateInput inputDefault" readonly="readonly"
                                   disabled="disabled" placeholder="百分比0-50"
                                   onkeyup="rateCheckNum1(this.value, this)"/>&nbsp;%
                        </div>
                    </div>
                </div>
                {{--自定义推广海报--}}
                <div class="salerSection">
                    <div class="salerSectionT">自定义推广海报</div>
                    <div class="salerSectionC">
                      <div class="salePosterWord">
                        点击上传您制作完成的海报图片，建议尺寸750x1334px
                        或9：16，JPG、PNG格式， 图片小于1M
                      </div>
                      <div class="btnMid xeBtnDefault uploadImgBtn">
                        上传海报图片
                        <input id="uploadImage" accept="image/jpeg,image/png,image/gif,image/bmp"
                               class="upLoadImage" type="file"/>
                        <input type="hidden" id="imgUrl" type="text"/>
                      </div>
                        <div class="uploadIntro">
                          <a target="_blank" href="/helpCenter/problem?document_id=d_5916afbae8b7f_6R0wiRbZ">如何自定义推广海报</a>
                      </div>
                        <br/>
                        {{--是否显示用户的头像昵称--}}
                        <div style="margin-top: 20px;">
                        <input class="with-gap" id="showInfo" name="group5" type="radio"/>
                        <label for="showInfo" class="showInfo">
                            显示头像昵称
                        </label>
                        <input class="with-gap" id="hideInfo" name="group5" type="radio" checked/>
                        <label for="hideInfo" class="hideInfo">
                            不显示
                        </label>
                        </div>
                    </div>
                </div>
                <div class="salerBoxLine"></div>
                <div class="btnMid xeBtnDefault salerBoxCancel">取消</div>
                <div class="btnMid btnBlue salerBoxConfirm">确定</div>
            </div>
             <div class="UploadBox">
                 <div class="UploadImgBox previewPic">
                     @include('component.loadingPartial')
                     <div class="previewModel1"><img src="../images/admin/marketing/qr_model.png" alt=""></div>
                     <div class="previewModel2"><img src="../images/admin/marketing/fingerprint.png" alt=""></div>
                       <img id="reBackImg" src="" alt="">
                 </div>
             </div>
            <div class="btnSmall xeBtnDefault deleteImg" style="display: none">删除</div>
        </div>
    </div>
    <div class="darkScreen1" style="display: none">
        <div class="modalBox">
            <div class="modalBoxHeader">模板
                <div class="modalBoxClose"><img src="../images/icon_Pop-ups_close.svg"></div>
            </div>
            <div class="modalBoxContent">
                <p>以下计划说明模板，请根据情况自行修改，仅供参考。</p>
                <p>———————————
                    <br/>
                <p>小伙伴你好，我们是XXX运营团队。
                <p>
                    <br/>
                <p>欢迎加入并成为我们的推广员。将优质内容分享给他人的同时，您也可以获得一定的奖励。
                <p>
                    <br/>
                <p>一、奖励说明
                <p>
                <p>1.用户通过您分享的推广链接成功付款，您将获得对应比例的奖励；如果用户在购买过程中跳出链接，您将无法获得奖励；
                <p>
                <p>2.您同时可以邀请好友成为推广员，用户在你邀请的推广员的链接下成功付费，您将获得额外比例的奖励。
                <p>
                    <br/>
                <p>二、结算说明
                <p>
                <p>1.您所获得的每笔订单的奖励依据商品的分成比例有所不同；
                <p>
                <p>2.您可以在“首页——我的——我的收益”完成查看您的奖励数据，并进行提现操作。
                    <br/>
                <p>三、其它
                <p>1.推广过程中如有任何疑问，请直接与运营方取得联系；
                <p>
                <p>2.禁止传播扩散任何关于政治、色情等任何违法内容。一经发现，您将被直接移除推广员身份。触犯任何法律相关问题，XXX运营团队不负任何责任；
                <p>
                <p>3.以上所述内容解释权归XXX运营团队所有。
                <p>


            </div>
            <div class="modalBoxLine"></div>
            <div class="btnGroup">
                <div class="btnMid xeBtnDefault modalBoxCancle">关闭</div>
                <div class="btnMid btnBlue modalBoxConfirm">使用模板替换当前内容</div>
            </div>
        </div>
    </div>
    <div class="darkScreen2" style="display: none">
        <div class="welcomeBoxClose"><img src="../images/admin/marketing/saler_box_close.svg" alt=""></div>
          <div class="salerWelcomeBox">
              <div class="welcomeBoxBanner"><img src="../images/admin/marketing/distribute_banner.svg" alt=""></div>
              <div class="welcomeBoxTitle">营销利器 -- 推广员</div>
              <div class="welcomeBoxIntro">推广员成功推广后给予奖励，以此给商户带来更多传播和促进销量提升</div>
              <a href="/helpCenter/problem?document_id=d_591c1968b2c01_bSdPa3WF" target="_blank"><div class="enterIntroBtn btnMid btnBlue">查看功能介绍</div></a>
          </div>
    </div>
    <div class="modal fade" id="disAgreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;margin-top: 120px;">
            <div class="modal-content" style="height: 430px;width: 700px;padding-left: 10px;padding-right: 10px">

                <div class="modal-header-message">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <div style="display: inline-block;height:34px;line-height: 34px"><span class="modal-title" style="font-size: 18px" id="myModalLabel">拒绝申请</span></div>
                </div>

                <div class="modal-body">
                    <textarea class="form-control" cols="22" rows="15" id="reject_reason"
                    placeholder="在此输入拒绝理由"></textarea>

                    <div class="rejectbuttonArea">
                        <span class="btnMid btnBlue" id="rejectBtn">确定</span>
                    </div>

                    <input type="hidden" id="disagree_id" />
                </div>
            </div>
        </div>
    </div>
@stop
