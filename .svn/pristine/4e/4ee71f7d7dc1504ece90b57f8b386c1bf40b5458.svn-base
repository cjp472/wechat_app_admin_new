<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';

if ($page_type) {
    $selectedResPrice =
            $exercise_book_info->resource_type==5? $exercise_book_info->price : $exercise_book_info->piece_price;

    if ($selectedResPrice !== 0) {
        $selectedResPrice = $selectedResPrice ? $selectedResPrice : "--";
    }

}

//dump($exercise_book_info);
//dump($resource_list);

?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/exercise/manageExerciseBook.css?{{env('timestamp')}}"/>
@stop

@section('page_js')
    <script type="text/javascript" src="../js/admin/exercise/manageExerciseBook.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    <input type="hidden" id="admin_data"
           data-page_type="{{$page_type}}"
           @if($page_type)
                data-resource_id="{{$exercise_book_info->resource_id}}"
                data-resource_type="{{$exercise_book_info->resource_type}}"
                data-resource_name="{{$exercise_book_info->resource_name}}"
                data-resource_price="{{$selectedResPrice}}"
           @endif
    >

    <header class="mainHeader">
        <a class="go_back" href="/exercise/exercise_book_list?page={{$page_index}}">作业本</a>
        &nbsp;&gt;&nbsp;
        @if($page_type == 0)新建作业本@else编辑作业本@endif
    </header>

    <main class="mainContent clearfix">
        <div class="baseInfoTag">
            <span class="tagIcon"></span>
            <span class="tagText">基本信息</span>
        </div>
        <div class="singleItem clearfix">
            <div class="itemTitle">
                作业本名称<span class="startKey">*</span>
            </div>
            <div class="itemContent" style="width: auto">
                <input class="inputDefault exerciseBookNameInput" id="exerciseBookName"
                       placeholder="请输入名称" @if($page_type) value="{{$exercise_book_info->title}}" @endif>
                <div class="characterNumLimit"><span>0</span>/14</div>
            </div>
        </div>
        <div class="singleItem clearfix">
            <div class="itemTitle">
                关联课程<span class="startKey">*</span>
            </div>
            <div class="itemContent">
                <div class="relevantCourseTip">
                    1个课程（专栏/课程）只能创建1个作业本，老师可以发布和该课程相关的作业；老师一旦布置作业后不可更改
                </div>
                <div class="selectorWrapper clearfix">
                    <select class="resTypeSelector" id="resTypeSelector" @if($page_type && $exercise_count) disabled @endif>
                        <option value="-1">请选择类型</option>
                        <option value="1" @if($page_type && $exercise_book_info->resource_type==1) selected  @endif>图文</option>
                        <option value="2" @if($page_type && $exercise_book_info->resource_type==2) selected  @endif>音频</option>
                        <option value="3" @if($page_type && $exercise_book_info->resource_type==3) selected  @endif>视频</option>
                        <option value="4" @if($page_type && $exercise_book_info->resource_type==4) selected  @endif>直播</option>
                        <option value="5" @if($page_type && $exercise_book_info->resource_type==5) selected  @endif>专栏</option>
                    </select>
                    <select class="resItemSelector" id="resItemSelector" @if($page_type && $exercise_count) disabled @endif>
                        @if($page_type==0)
                            <option data-res_id="-1">请选择具体课程</option>
                        @else
                            <option data-res_id="{{$exercise_book_info->resource_id}}" data-res_price="{{$selectedResPrice}}" selected>
                                {{$exercise_book_info->resource_name}}
                            </option>
                            @foreach($resource_list as $k => $v)
                                <?php
                                $price = ($exercise_book_info->resource_type==5)?$v->price:$v->piece_price;
                                if ($price !== 0) {
                                    $price = $price ? $price : "--";
                                }
                                $name = ($exercise_book_info->resource_type==5)?$v->name:$v->title;
                                ?>
                                <option data-res_id="{{$v->id}}" data-res_price="{{ $price }}">{{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="singleItem clearfix">
            <div class="itemTitle">
                关联社群
            </div>
            <div class="itemContent">
                <div class="relevantCourseTip">
                    关联社群后，老师布置的作业将在社群内作为带作业标签的动态显示
                </div>
                @if(empty($community_list) || count($community_list) == 0)
                    <div>暂无可关联的社群</div>
                @else
                    <select class="communitySelector" id="communitySelector">
                        <option value="-1">不关联</option>
                        @foreach($community_list as $k => $v)
                            <option value="{{$v->id}}" @if($page_type && $exercise_book_info->community_id==$v->id) selected @endif >
                                {{$v->title}}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="singleItem clearfix">
            <div class="itemTitle">
                作业提醒<span class="startKey">*</span>
            </div>
            <div class="itemContent">
                <div class="relevantCourseTip hide">
                    折飞机地方Jeri法国家乐福个Jeri哦干劲儿破防
                </div>
                <div class="remindRadioWrapper">
                    <div class="remindRadio">
                        <span class="circleRadio @if($page_type == 0 || ($page_type && $exercise_book_info->is_enable_notify == 0)) radioActive @endif"
                              data-is_remind="0" id="noRemindMsg"></span>
                        <span>不提醒</span>
                    </div>
                    <div class="remindRadio">
                        <span class="circleRadio @if($page_type && $exercise_book_info->is_enable_notify == 1) radioActive @endif"
                              data-is_remind="1" id="remindMsg"></span>
                        <span>提醒</span><span class="remindTip">（开启后，老师可以向购买课程的学员推送课程作业消息）</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mainFooter">
        <div class="footerTip @if($page_type) hide @endif">
            创建后，请继续完成人员（老师/助教）管理。被设置的老师/助教可以在手机端发布和点评课程作业
        </div>
        <div class="footerBtnWrapper">
            <div class="xeBtnDefault btnMid cancelSaveExerciseBtn @if($page_type == 0) hide @endif" id="cancelSaveExercise">取消</div>
            <div class="btnBlue btnMid confirmSaveExerciseBtn" id="confirmSaveExercise">@if($page_type) 保存 @else 创建 @endif</div>
        </div>
    </footer>

@stop

@section('base_modal')
    {{--编辑后离开页面的提示--}}
    @include('admin.actionTips.leaveTips')

@stop












