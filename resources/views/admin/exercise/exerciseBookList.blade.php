<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';

$isExerciseOpen =
        \App\Http\Controllers\Tools\AppUtils::IsModuleVisual("is_show_exercise_system");//0-不开启；1-开启',

$contentNoData = (empty($exercise_book_list) || count($exercise_book_list) == 0);

//dump($exercise_book_list);
//dump($exercise_book_role_list);

?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/exercise/exerciseBookList.css?{{env('timestamp')}}"/>
@stop

@section('page_js')
    <script type="text/javascript" src="../js/admin/exercise/exerciseBookList.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    <input type="hidden" id="admin_data" value="{{$contentNoData}}">

    <div class="pageTopTitle">
        <a href="/community_operate">社群运营</a>
        &gt;
        作业本
    </div>

    <div class="switchArea">
        <div class="function_item">
            <div class="function_content">
                <div class="function_title">作业本</div>
                <div class="function_desc">
                    作业本是小鹅通推出的帮助老师实现课程教学环节后续与学员进行互动与答疑的全新功能。
                    您可以为课程设置老师/助教，在手机端店铺可以完成老师发布作业、学员提交作业、老师/助教点评作业的完整服务流程，且支持语音、图片、文字的内容展现形式。
                    此外，老师可以生成作业卡直接发送给学员，还可以在关联小社群后以动态的形式展示作业。
                </div>
                <a class="function_help_doc" target="_blank" href="/helpCenter/problem?document_id=doc_598bbd90c4135_u5wuS">
                    查看【作业本教程】
                </a>
            </div>
            @include("admin.functionManage.switchButton", [
                "switchId" => "subscribe_count",
                "switchState" => $isExerciseOpen == 1 ? true : false
            ])
        </div>
    </div>

    <header class="mainHeader">
        <div class="btnMid btnBlue createExerciseBookBtn" id="createExerciseBook">新建作业本</div>
        <form class="submitSearchPart" action="/exercise/exercise_book_list" method="GET">
            <input class="inputDefault searchContentInput" name="search_content" type="search"
                   placeholder="请输入作业本名称" value="{{trim($search_content)}}">
            <button class="btnMid xeBtnDefault submitSearchBtn" type="submit">搜索</button>
        </form>
    </header>

    <main class="mainContent">
        <div class="tableHeader" id="tableHeader">
            <div>名称</div>
            <div>关联课程</div>
            <div class="roleSet">
                老师/助教
                <div class="stateHoverBoxWrapper">
                    <div class="stateHoverBox">
                        <img src="/images/alert/blue_info_prompt.svg">
                        <div class="hoverTextContent">
                            老师/助教在[手机端店铺-我的-我的作业]可以进行作业管理。<br>
                            权限说明：<br>
                            老师可以布置作业和点评作业，<br>
                            助教可以以老师身份点评作业。<br>
                        </div>
                    </div>
                </div>
            </div>
            <div>关联社群</div>
            <div>作业数</div>
            <div>操作</div>
        </div>
        @if($isExerciseOpen == 0)
            <div class="contentNoData" id="functionHasClose">功能已关闭, 作业内容暂时不显示</div>
        @else
            @if($contentNoData)
                @if(empty($search_content))
                    <div class="contentNoData" id="contentNoData">暂无作业本，请点击新建作业本为课程设置老师</div>
                @else
                    <div class="contentNoData" id="contentNoData">暂无搜索结果</div>
                @endif
            @endif
        @endif
        <div class="tableContent" id="tableContent" @if($isExerciseOpen == 0) style="display: none;" @endif>
            @foreach($exercise_book_list as $key => $value)
                <?php
                /*********** 关联课程 ***********/
                    $relevantRes = "";
                    switch ($value->resource_type) {
                        case 1:
                            $relevantRes= "图文：".$value->resource_name;
                            break;
                        case 2:
                            $relevantRes= "音频：".$value->resource_name;
                            break;
                        case 3:
                            $relevantRes= "视频：".$value->resource_name;
                            break;
                        case 4:
                            $relevantRes= "直播：".$value->resource_name;
                            break;
                        case 5:
                            $relevantRes= "专栏：".$value->resource_name;
                            break;
                        default:
                            $relevantRes= "--";
                            break;
                    }

                /*********** 老师/助教 ***********/
                    $teacherNum = 0;//老师个数
                    $assistantNum = 0;//助教个数
                    foreach ($exercise_book_role_list[$key] as $k1 => $v1) {
                        if ($v1->role_type == 0) {
                            $teacherNum ++;
                        } else if ($v1->role_type == 1) {
                            $assistantNum ++;
                        }
                    }

                    $roleName = "";
                    if (empty($exercise_book_role_list[$key])) {
                        $roleName = "--";
                    } else {
                        if ($teacherNum == 0) {
                            $roleName .= "无/";
                        } else {
                            foreach ($exercise_book_role_list[$key] as $k => $v) {
                                if ($v->role_type == 0) {/*老师*/
                                    $roleName .= $v->wx_nickname.",";
                                }
                            }
                            $roleName = substr($roleName, 0, -1)."/";
                        }

                        if ($assistantNum == 0) {
                            $roleName .= "无";
                        } else {
                            foreach ($exercise_book_role_list[$key] as $k => $v) {
                                if ($v->role_type == 1) {/*助教*/
                                    $roleName .= $v->wx_nickname.",";
                                }
                            }
                            $roleName = substr($roleName, 0, -1);
                        }
                    }

                /*********** 关联社群 ***********/
                    $communityName = $value->community_name?$value->community_name:"--";
                ?>
                <div class="exerciseBookItem">
                    <div title="{{$value->title}}">{{$value->title}}</div>
                    <div title="{{$relevantRes}}">{{$relevantRes}}</div>
                    <div title="{{$roleName}}">{{$roleName}}</div>
                    <div title="{{$communityName}}">{{$communityName}}</div>
                    <div>{{$value->exercise_num}}</div>
                    <div class="exerciseBookOperateArea"
                         data-resource_id="{{$value->resource_id}}"
                         data-resource_type="{{$value->resource_type}}"
                         data-exercise_book_id="{{$value->exercise_book_id}}"
                         data-role_list="{{json_encode($exercise_book_role_list[$key])}}"
                    >
                        <ul class="operateList">
                            <li class="operate" data-type="role_manage">人员管理</li>
                            <li class="verticalGapLine">&nbsp;|&nbsp;</li>
                            <li class="operate" data-type="exercise_list">作业列表</li>
                            <li class="verticalGapLine">&nbsp;|&nbsp;</li>
                            <li class="operate" data-type="edit_exercise_book">编辑</li>
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="list-page" id="listPage" @if($isExerciseOpen == 0) style="display: none;" @endif>
            @if(empty($search_content))
                <?php echo $exercise_book_list->render(); ?>
            @else
                <?php echo $exercise_book_list->appends(['search_content' => $search_content])->render(); ?>
            @endif
        </div>
    </main>

@stop


@section("base_modal")

    <div class="roleManageWindow" id="roleManageWindow">
        <div class="windowHeader">
            <span class="headerText1">人员管理</span>
            <span class="headerText2">一个作业本仅能设置1位老师和多个助教</span>
            <div class="windowCloseIcon" id="windowCloseIcon1">
                <img src="/images/icon_Pop-ups_close.svg">
            </div>
        </div>
        <div class="windowContentRegion1">
            <div class="windowContentWrapper1">
                <div class="windowContent1 clearfix" id="windowContent1">
                    {{--已添加用户信息--}}
                    {{--<div class="singleUserInfo1">--}}
                        {{--<img class="singleGuestAvatar1" src="/images/bg_page-home.jpg">--}}
                        {{--<div class="singleGuestName1" title="allen">allen</div>--}}
                        {{--<div class="roleTypeRadioWrapper">--}}
                            {{--<div class="roleTypeRadio">--}}
                                {{--<span class="circleRadio radioActive" data-role_type="0"></span><span>老师</span>--}}
                            {{--</div>--}}
                            {{--<div class="roleTypeRadio">--}}
                                {{--<span class="circleRadio" data-role_type="1"></span><span>助教</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="deleteSingleUser">删除</div>--}}
                    {{--</div>--}}
                </div>
            </div>
            @include("admin.functionManage.localLoading", ["id" => "roleManageLoading"])
        </div>
        <div class="windowFooter">
            {{--<div class="windowFooterTip">--}}
                {{--保存后，老师可以在店铺[我的-我的作业]布置课程作业--}}
            {{--</div>--}}
            <div class="footerBtnWrapper">
                <div class="btnBlue btnMid addExerciseBookRoleBtn" id="addExerciseBookRole">
                    添加人员
                    <ul class="windowHoverBox" id="windowHoverBox">
                        <li data-type="add_teacher">添加老师</li>
                        <li data-type="add_assistant">添加助教</li>
                    </ul>
                </div>
                <div class="xeBtnDefault btnMid confirmSaveRoleBtn" id="confirmSaveRole">保存</div>
            </div>
        </div>
    </div>

    <div class="searchUserWindow" id="searchUserWindow">
        <div class="windowHeader">
            <span class="headerText1">搜索用户</span>
            <span class="headerText2"></span>
            <div class="windowCloseIcon" id="windowCloseIcon2">
                <img src="/images/icon_Pop-ups_close.svg">
            </div>
        </div>
        <div class="windowSearchArea">
            <input class="inputDefault windowSearchInput" id="windowSearchInput" placeholder="请输入用户昵称">
            <img src="/images/search.png">
            <div class="xeBtnDefault btnMid windowSearchBtn" id="windowSearchBtn">搜索</div>
        </div>
        <div class="windowContentRegion2">
            <div class="windowContentWrapper2">
                <div class="windowContent2 clearfix" id="windowContent2">
                    {{--可添加用户信息--}}
                </div>
            </div>
            @include("admin.functionManage.localLoading", ["id" => "searchUserLoading"])
        </div>
        <div class="windowFooter2 clearfix">
            <div class="qrCodeHoverBoxWrapper hide">    {{--暂时隐藏--}}
                <div class="xeBtnDefault btnMid addUserBtn" id="inviteWeiXinFriend">邀请微信好友</div>
                <div class="qrCodeHoverBox">
                    <img class="_closeHoverBox" src="/images/icon_Pop-ups_close.svg" >
                    <div class="_hoverBoxText">微信扫描二维码<br>邀请微信好友为嘉宾</div>
                    <div class="_qrCodeImgWrapper" id="_qrCodeImgWrapper">

                    </div>
                </div>
            </div>
            <div class="btnBlue btnMid confirmAddUserBtn" id="confirmSaveAddedUser">确定</div>
        </div>
    </div>

@endsection











