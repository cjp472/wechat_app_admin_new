<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/acitvity/activeBaseLayout.css?{{env('timestamp')}}"/>
    {{--时间选择器--}}
    <link href="../css/external/bootstrap-datetimepicker.min.css" rel="stylesheet">
    {{--弹窗插件--}}
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css" />

    <link type=text/css rel="stylesheet" href="../css/admin/acitvity/createActivity.css?{{env('timestamp')}}" />
@endsection

@section('ahead_js')
    {{--文本编辑器--}}
    <script src="../ueditor/ueditor.config.js" type="text/javascript"></script>
    <script src="../ueditor/ueditor.all.min.js?{{env('timestamp')}}" type="text/javascript"></script>
    {{--秀米sdk--}}
    <script src="../ueditor/xiumi-ue-dialog-v5.js" type="text/javascript"></script>
@endsection

@section('page_js')
    <script type="text/javascript" src="../js/admin/acitvity/activeBaseLayout.js?{{env('timestamp')}}"></script>
    {{--时间选择器--}}
    <script src="../js/external/bootstrap-datetimepicker.min.js"></script>

    {{--腾讯云上传js--}}
    {{--<script type="text/javascript" src="sdk/swfobject.js"></script>--}}
    {{--<script type="text/javascript" src="sdk/qcloud_sdk.js"></script>--}}
    <script type="text/javascript" src="sdk/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="js/admin/utils/v4QcloudUpload.js"></script>

    {{--获取文件MD5--}}
    <script type="text/javascript" src="../js/external/browser-md5-file.js"></script>
    {{--上传工具函数--}}
    <script type="text/javascript" src="../js/admin/utils/upload.js?{{env('timestamp')}}"></script>
    {{--表单验证工具函数--}}
    <script type="text/javascript" src="../js/admin/utils/formCheck.js?{{env('timestamp')}}"></script>
    {{--弹窗--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>

    <script type="text/javascript" src="../js/admin/acitvity/createActivity.js?{{env('timestamp')}}"></script>
@endsection


@section('base_mainContent')

    <input id="type" type="hidden" value="{{$type}}">
    <input id="xcx_app_id" type="hidden" value="{{session("app_id")}}">
    @if($type == 1)

        <input id="activityId" type="hidden" value="{{$activityInfo->id}}">

    @endif

    <div class="acitvityHeader">
        <a href="/activityManage">活动管理</a> >
        @if($type == 1)
            编辑活动
        @else
            发布活动
        @endif
    </div>
    <div class="createActivity">

        {{--活动信息start--}}
        <div class="activityDetail">
            <div class="baseInfoTitle">基本信息</div>

            {{--活动名称--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle"><span class="requiedTag">* </span>活动名称</div>
                <div class="baseInfoRightContent">
                    <input id="title" class="baseInfoText inputDefault" type="text" placeholder="请输入活动名称（建议字数在14字符以内）" maxlength="30"
                    @if($type == 1)value="{{$activityInfo->title}}"@endif>
                </div>
            </div>

            {{--活动地点--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle"><span class="requiedTag">* </span>活动地点</div>
                <div class="baseInfoRightContent">
                    <input id="place" class="baseInfoText inputDefault" type="text" placeholder="请输入活动地点" maxlength="200"
                   @if($type == 1)value="{{$activityInfo->place}}"@endif>
                </div>
            </div>

            {{--活动时间--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle"><span class="requiedTag">* </span>活动时间</div>
                <div class="baseInfoRightContent">
                    <div class="infoDateWrapper">
                        <input id="activity_start_at" class="baseInfoDate inputDefault activityDate" type="text" maxlength="200" readonly style="cursor: pointer"
                       @if($type == 1)value="{{$activityInfo->activity_start_at}}"@endif>
                        <span>至</span>
                        <input id="activity_end_at" class="baseInfoDate inputDefault activityDate" type="text" maxlength="200" readonly style="cursor: pointer"
                       @if($type == 1)value="{{$activityInfo->activity_end_at}}"@endif>
                    </div>
                </div>
            </div>

            {{--报名时间--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle"><span class="requiedTag">* </span>报名时间</div>
                <div class="baseInfoRightContent">
                    <div class="chooseTimeWrapper">
                        <input type="radio" class="with-gap" id="chooseTimeDefault" name="chooseActivityTime" value="0"
                        @if(($type == 1  && $activityInfo->is_default_enroll_time == 0 )|| $type == 0) checked @endif />
                        <label for="chooseTimeDefault">&nbsp;&nbsp;默认为活动发布后至活动结束前</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" class="with-gap" id="chooseTimeSelf" name="chooseActivityTime"  value="1"
                        @if($type == 1  && $activityInfo->is_default_enroll_time == 1) checked @endif />
                        <label for="chooseTimeSelf">&nbsp;&nbsp;自定义</label>
                    </div>
                    <div class="infoDateWrapper enrollDate"
                         @if($type == 1  && $activityInfo->is_default_enroll_time == 1)
                         @else style="display: none" @endif >
                        <input id="enroll_start_at" class="baseInfoDate inputDefault activityDate" type="text" maxlength="200" readonly style="cursor: pointer"
                           @if($type == 1 )value="{{$activityInfo->enroll_start_at}} "@endif/>
                        <span>至</span>
                        <input id="enroll_end_at" class="baseInfoDate inputDefault activityDate" type="text" maxlength="200" readonly style="cursor: pointer"
                           @if($type == 1 )value="{{$activityInfo->enroll_end_at}}" @endif/>
                    </div>
                </div>
            </div>

            {{--活动海报--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle">活动海报</div>
                <div class="baseInfoRightContent">
                    <div class="previewPic">
                        <div class="noPicPreview" @if($type == 1 && $activityInfo->img_url) style="display: none" @endif>
                            <input type="file" class="activityPic uploadActivityPic previewUpload" accept="image/jpeg,image/png,image/gif,image/bmp">
                        </div>
                        <div class="picWrapper">
                            <img id="deletePic" class="pic_close" src="../images/icon_close.png">
                            <img class="picPreview" id="img_url"  @if($type == 1 && $activityInfo->img_url) src="{{$activityInfo->img_url}}" @endif>
                        </div>
                    </div>

                    <div class="uploadPicWrapper">
                        <div class="uploadPicButton">
                            <button type="button" class="btn btn-default">上传</button>
                            <input type="file" class="activityPic uploadActivityPic" accept="image/jpeg,image/png,image/gif,image/bmp">
                        </div>
                        <p class="uploadTips">建议尺寸750*560px，jpeg、png格式，小于100kb</p>
                    </div>
                </div>
            </div>

            {{--活动人数 - 放入票务信息中 --}}
            {{--<div class="baseInfoWrapper clearfix">--}}
                {{--<div class="baseInfoLeftTitle">活动人数</div>--}}
                {{--<div class="baseInfoRightContent">--}}
                    {{--<input id="actor_num" class="baseInfoText inputDefault" type="number" min="1" placeholder="请输入活动人数，不填则为不限制人数"--}}
                    {{--@if($type == 1 && $activityInfo->actor_num !== 0)value="{{$activityInfo->actor_num}}"@endif>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--活动详情--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle">活动详情</div>
                <div class="baseInfoRightContent">
                    <div class="upload_input_div">
                        <script id="activityDesc"  type="text/plain"></script>
                        <script type="text/javascript">
                            var baiduUe =UE.getEditor('activityDesc',ueditor_config);

                            @if($type == 1  && $activityInfo->descrb)
                            baiduUe.ready(function()
                            {
                                baiduUe.setContent('{!!$activityInfo->descrb!!}');
                            });
                            @endif
                        </script>
                    </div>
                </div>
                <div class="waves-effect btnSmall xeBtnDefault coverUpbtn" id="preview" style="margin-left: 20px;margin-right: 0;">
                    预览
                </div>
            </div>

        </div>
        {{--活动信息end--}}

        {{--其他信息start--}}
        <div class="activityOtherInfo">
            {{--所属专栏--}}
            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle">所属频道</div>
                <div class="baseInfoRightContent">
                    <select id="package_list" class="baseInfoText inputDefault" @if($type == 1) disabled @endif>
                        <option value="">无</option>
                        @if($type == 0)
                            @foreach($package_list as $package)
                                <option value="{{$package->id}}">{{$package->name}}</option>
                            @endforeach
                        @elseif($type == 1)
                            @foreach($package_list as $package)
                                @if($activity_package_info && $activity_package_info->product_id == $package->id)
                                    <option value="{{$package->id}}" selected>{{$package->name}}</option>
                                @else
                                    <option value="{{$package->id}}">{{$package->name}}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="setPackageTip">一经设定不可修改；设置所属频道后，只允许已购买该频道的用户参与活动报名</div>

            {{--上架时间--}}
            {{--选了专栏才显示--}}
            <div class="baseInfoWrapper clearfix activityStartTime"
                @if($type == 0)
                    style="display: none"
                @elseif($type == 1)
                    @if($activity_package_info && $activity_package_info->product_id)
                    @else
                    style="display: none"
                    @endif
                @endif
            >
                <div class="baseInfoLeftTitle"><span class="requiedTag">* </span>上架时间</div>
                <div class="baseInfoRightContent">
                    <div class="infoDateWrapper">
                        <input id="start_at"  class="baseInfoDate inputDefault activityDate" type="text" readonly style="cursor: pointer"
                               @if($type == 1)value="{{$activityInfo->start_at}}"@endif>
                    </div>
                </div>
            </div>

            {{--是否审核 - 放入票务信息中--}}
            {{--<div class="baseInfoWrapper clearfix">--}}
                {{--<div class="baseInfoLeftTitle">是否审核</div>--}}
                {{--<div class="baseInfoRightContent">--}}
                    {{--<div class="activityCheck">--}}
                        {{--<label><input id="is_confirm" type="checkbox"--}}
                                      {{--@if($type == 1 && $activityInfo->is_confirm == 1) checked @endif>报名需要经过审核</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

        </div>
        {{--其他信息end--}}

        {{--表单信息start--}}
        <div class="activityTable clearfix">
            <div class="baseInfoTitle">表单设置</div>

            <div class="activityTableLeft">
                <p class="contactTitle">联系方式（报名用户资料，必填）</p>
                {{--姓名--}}
                <div class="baseInfoWrapper clearfix">
                    <div class="tableLeftPart">
                        <div class="requiedTagChoose">
                            <label><input type="checkbox" checked disabled readonly>必填</label>
                        </div>
                        <div class="tableTitle">
                            <p class="tableInfoTitle">姓名</p>
                        </div>
                    </div>
                    <div class="tableRightPart">
                        <input class="tableInfoText inputDefault formDisabled" type="text" placeholder="报名用户的姓名或昵称" disabled readonly>
                    </div>
                </div>

                {{--手机号码--}}
                <div class="baseInfoWrapper clearfix">
                    <div class="tableLeftPart">
                        <div class="requiedTagChoose">
                            <label><input type="checkbox" checked disabled readonly>必填</label>
                        </div>
                        <div class="tableTitle">
                            <p class="tableInfoTitle">手机号码</p>
                        </div>
                    </div>
                    <div class="tableRightPart">
                        <input class="tableInfoText inputDefault formDisabled" type="text" placeholder="报名用户的手机号码" disabled readonly>
                    </div>
                </div>

                <p class="contactTitle">其他</p>


                {{--有其他表单才显示这个提示--}}
                <p class="contactTitle otherInfoTips"
                @if($type == 1)
                    @if($activityInfo->form_field)
                        <?php
                            $form_field = [];
                            try{
                                $form_field = array_slice(json_decode($activityInfo->form_field),2);
                            }
                            catch (\Exception $e){}
                            ?>
                        @if(count($form_field) > 0)
                            style="display: none"
                        @endif
                    @endif
                @endif
                >未添加其他栏位，即不需要报名用户提供其他信息</p>


                <div class="copyWrapper">
                    <div class="activityTableCopy" style="display: none">
                        <?php
                        $otherData = [];
                        $otherData['titlePlaceholder'] = "单行文本";
                        $otherData['type'] = 2;
                        $otherData['title'] = "";
                        $otherData['tips'] = "";
                        $otherData['required'] = false;
                        ?>
                        @include('admin.activity.createOtherInfo',$otherData)
                    </div>

                    <div class="activityTableCopy" style="display: none">
                        <?php
                        $otherData = [];
                        $otherData['titlePlaceholder'] = "多行文本";
                        $otherData['type'] = 3;
                        $otherData['title'] = "";
                        $otherData['tips'] = "";
                        $otherData['required'] = false;
                        ?>
                        @include('admin.activity.createOtherInfo',$otherData)
                    </div>
                </div>

                <div id="otherTableWrapper">
                    @if($type == 0)
                    @elseif($type == 1)
                        @if($activityInfo->form_field)
                            <?php
                                $form_field = [];
                                try{
                                    $form_field = array_slice(json_decode($activityInfo->form_field),2);
                                }
                                catch (\Exception $e){}
                            ?>
                            @if(count($form_field)>0)
                                @foreach($form_field as $data)
                                    <?php
                                    $otherData = [];

                                    $titlePlaceholder = "";
                                    switch ($data->type){
                                        case 2:
                                            $titlePlaceholder = "单行文本";
                                            break;
                                        case 3:
                                            $titlePlaceholder = "多行文本";
                                            break;
                                    }

                                    $otherData['titlePlaceholder'] = $titlePlaceholder;
                                    $otherData['type'] = $data->type;
                                    $otherData['title'] = $data->field_name;
                                    $otherData['tips'] = isset($data->placeholder)?$data->placeholder:"";
                                    $otherData['required'] = $data->required;
                                    ?>
                                    @include('admin.activity.createOtherInfo',$otherData)
                                @endforeach
                            @else
                            @endif
                        @endif
                    @endif

                </div>

            </div>

            <div class="activityTableRight">
                <div class="addTableWrapper">
                    <p>自定义栏位</p>
                    <div class="addTableArea">单行文本框 <img src="/images/admin/activity/icon_add_uikit.png"></div>
                    <div class="addTableArea"> 多行文本框 <img src="/images/admin/activity/icon_add_uikit.png"></div>
                </div>

            </div>

        </div>
        {{--表单信息end--}}

        {{--票务信息 - start--}}
        <div class="activityTicket clearfix">
            <div class="baseInfoTitle">票务设置</div>

            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle">免费票</div>
                <div class="baseInfoRightContent">
                    <div class="ticketTable freeTicketTable" >
                        <ul>
                            <li class="tableHead">
                                <div class="ticketItem">票种名称</div>
                                <div class="ticketItem">票总数</div>
                                <div class="ticketItem">价格(元)</div>
                                <div class="ticketItem ticketItemOperate">操作</div>
                            </li>

                            <li id="freeTicketTableBody"
                                @if($type == 0 || empty($free_ticket_list) || count($free_ticket_list) == 0) class="hide" @endif
                                @if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0)
                                    data-ticket_id="{{$free_ticket_list[0]->id}}"
                                @else
                                    data-ticket_id=""
                                @endif
                            >
                                <div style="height: 50px;">
                                    @if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0)
                                        <input class="freeTicketItem inputDefault" id="freeTicketName" placeholder="免费票"
                                               value="{{$free_ticket_list[0]->ticket_name}}"
                                               readOnly="readonly" style="border: none;"
                                        />
                                        <input class="freeTicketItem inputDefault" id="freeTicketCount" type="number" placeholder="100"
                                               value="{{$free_ticket_list[0]->ticket_count}}"
                                               readOnly="readonly" style="border: none;"
                                        />
                                    @else
                                        <input class="freeTicketItem inputDefault" id="freeTicketName" placeholder="免费票" />
                                        <input class="freeTicketItem inputDefault" id="freeTicketCount" type="number" placeholder="100" />
                                    @endif
                                    <div class="ticketItem">免费</div>
                                    <div class="ticketItem">
                                        <div class="toolBox">
                                            <ul>
                                                @if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0)
                                                    <li id="editFreeTicket" edit_state="1">编辑此票</li>
                                                @else
                                                    <li id="editFreeTicket" edit_state="0">保存此票</li>
                                                @endif
                                                <li id="deleteFreeTicket">删除此票</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="freeTicketDesc
                                     @if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0) hide @endif "
                                >
                                    <div class="ticketExplanationWrapper">
                                        <span class="ticketExplanation">票种说明(选填)</span>
                                        <textarea id="ticketExplanationArea" class="ticketTextArea"
                                                  placeholder="您可以在这里说明此票的特点">@if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0){{$free_ticket_list[0]->extra}}@endif</textarea>
                                    </div>
                                    <div class="ticketCheckWrapper">
                                        <span class="ticketExplanation">是否审核</span>
                                        <input type="checkbox" id="isFreeTicketCheck"
                                            @if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0)
                                                @if($free_ticket_list[0]->is_need_check == 1)
                                                    data-check_state="1"
                                                    checked="checked"
                                                @else
                                                    data-check_state="0"
                                                @endif
                                            @else
                                                data-check_state="0"
                                            @endif
                                        >
                                        <label for="isFreeTicketCheck">报名本活动需要经过审核（仅限免费票）</label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div id="newFree" class="newTicket
                    @if($type == 1 && !empty($free_ticket_list) && count($free_ticket_list) > 0)
                        hide
                    @endif ">
                        <span class="add">+</span>添加免费票种
                    </div>
                </div>
            </div>

            <div class="baseInfoWrapper clearfix">
                <div class="baseInfoLeftTitle">收费票</div>
                <div class="baseInfoRightContent">
                    <div class="feeTip hide" id="payTicketTip">选择所属专栏后暂不支持添加收费票种</div>

                    <div id="payTicketTableWrapper">
                        <div class="ticketTable payTicketTable">
                            <ul id="payTicketBodyWrapper">
                                <li class="tableHead">
                                    <div class="ticketItem">票种名称</div>
                                    <div class="ticketItem">票总数</div>
                                    <div class="ticketItem">价格(元)</div>
                                    <div class="ticketItem ticketItemOperate">操作</div>
                                </li>

                                @if(!empty($pay_ticket_list))
                                @foreach($pay_ticket_list as $key => $value)
                                    <li class="payTicketTableBody" id="{{$value->id}}">
                                        <div class="payTicketInfo">
                                            <input class="payTicketItem inputDefault payTicketName" data-index="{{$key}}" placeholder="vip票"
                                                   value="{{$value->ticket_name}}"
                                                   readOnly="readonly" style="border: none;"
                                            />
                                            <input class="payTicketItem inputDefault payTicketCount" type="text" placeholder="0(不限)"
                                                   value="{{$value->ticket_count}}"
                                                   readOnly="readonly" style="border: none;"
                                            />
                                            <input class="payTicketItem inputDefault payTicketPrice" type="number" placeholder="请输入价格"
                                                   value="{{$value->ticket_price / 100.00}}"
                                                   readOnly="readonly" style="border: none;"
                                            />
                                            <div class="ticketItem" id="payTicketOperate">
                                                <div class="toolBox">
                                                    <ul>
                                                        <li class="editPayTicket" edit_state="1" id="{{$key}}">编辑此票</li>
                                                        <li class="deletePayTicket">删除此票</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="payTicketDesc hide">
                                            <div class="ticketExplanationWrapper">
                                                <span class="ticketExplanation">票种说明(选填)</span>
                                                <textarea class="payTicketExplanationArea ticketTextArea" placeholder="您可以在这里说明此票的特点">{{$value->extra}}</textarea>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                        <div id="newPayTicket" class="newTicket">
                            <span class="add">+</span>添加收费票种
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--票务信息 - end--}}


        <div class="saveButtonWrapper">
            <div id="saveActivity" class="btnBlue saveButton">发布</div>
        </div>



    </div>


@endsection


@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')
@stop
