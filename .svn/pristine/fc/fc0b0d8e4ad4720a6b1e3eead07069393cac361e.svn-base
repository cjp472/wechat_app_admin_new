<?php
$pageData = [];
$pageData['sideActive'] = 'communityOperate';
$pageData['barTitle'] = '社群运营';

//dump($exercise_list);
//dump($user_wx_nickname);

?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    {{--插件Viewer.js--}}
    <link type="text/css" rel="stylesheet" href="../css/utils/viewer.min.css">
    <link type="text/css" rel="stylesheet" href="../css/admin/exercise/exerciseList.css?{{env('timestamp')}}"/>
@stop

@section('page_js')
    {{--插件Viewer.js--}}
    <script type="text/javascript" src="../js/utils/viewer.min.js"></script>
    <script type="text/javascript" src="../js/admin/exercise/exerciseList.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    <header class="mainHeader">
        <div class="pageTopTitle">
            <a href="/exercise/exercise_book_list?page={{$page_index}}">作业本</a>
            &gt;
            作业列表
        </div>
        <div class="headerContent">
            <span class="exerciseBookName">{{$exercise_book_title}}</span>
            <span class="exerciseBookTeacherName">老师：{{$user_wx_nickname}}</span>
            <form class="submitSearchPart" action="/exercise/exercise_list" method="GET">
                <input type="hidden" name="exercise_book_id" value="{{$exercise_book_id}}">
                <input class="inputDefault searchContentInput" name="search_content" type="search"
                       placeholder="请输入作业内容" value="{{trim($search_content)}}">
                <button class="btnMid xeBtnDefault submitSearchBtn" type="submit">搜索</button>
            </form>
        </div>
    </header>

    <main class="mainContent">
        <div class="tableHeader">
            <div>作业标题</div>
            <div>布置时间</div>
            <div>关联课程</div>
            <div>回答数</div>
            <div>当前状态</div>
            <div>操作</div>
        </div>
        @if(empty($exercise_list) || count($exercise_list) == 0)
            @if(empty($search_content))
                <div class="contentNoData">老师暂未布置作业，您可以引导老师在手机端店铺 [我的-我的作业] 发布课程作业</div>
            @else
                <div class="contentNoData">暂无搜索结果</div>
            @endif
        @endif
        <div class="tableContent" id="tableContent">
            @foreach($exercise_list as $key => $value)
                <?php
                /*********** 作业标题 ***********/
                $exerciseTitle = $value->title ? $value->title : "--";

                /*********** 作业内容 ***********/
                $textContent = $value->content;
                $imgUrls = $value->img_compressed_urls ? $value->img_compressed_urls : $value->img_urls;
                $originalImgUrls = $value->img_urls ? $value->img_urls : $value->img_compressed_urls;
                $audioUrls = $value->audio_urls;

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
                /*********** 作业状态 ***********/
                $exerciseState = ($value->state?"隐藏":"显示");
                ?>
                <div class="singleExerciseItem">
                    <div title="{{$exerciseTitle}}">{{$exerciseTitle}}</div>
                    <div title="{{$value->created_at}}">{{$value->created_at}}</div>
                    <div title="{{$relevantRes}}">{{$relevantRes}}</div>
                    <div>{{$value->submit_exercise_num}}</div>
                    <div>{{$exerciseState}}</div>
                    <div class="exerciseOperateArea"
                         data-exercise_id="{{$value->exercise_id}}"
                         data-exercise_title="{{$exerciseTitle}}"
                         data-text_content="{{$textContent}}"
                         data-original_img_urls="{{$originalImgUrls}}"
                         data-audio_urls="{{$audioUrls}}"
                    >
                         <ul class="operateList">
                             <li class="operate" data-type="look_exercise_content">查看作业内容</li>
                             <li class="verticalGapLine">&nbsp;|&nbsp;</li>
                             <li class="operate" data-type="delete_exercise">删除</li>
                         </ul>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="list-page">
            @if(empty($search_content))
                <?php echo $exercise_list->appends(['exercise_book_id' => $exercise_book_id])->render(); ?>
            @else
                <?php echo $exercise_list->appends(['exercise_book_id' => $exercise_book_id])->appends(['search_content' => $search_content])->render(); ?>
            @endif
        </div>
    </main>

@stop


@section("base_modal")

    <div class="lookExerciseDetailWindow" id="lookExerciseDetailWindow">
        <div class="windowHeader" id="windowHeader">
            为什么要做用户分层？
        </div>
        <div class="windowContentRegion">
            <div class="textContentArea" id="textContentArea">
                什么叫用户运营？用户运营是指以最大化提升用户价值为目的，通过各类运营手段提高活跃度、留存率或者付费指标的动作。
            </div>
            <div class="imgContentArea" id="imgContentArea">
                <ul class="viewJsImgList clearfix" id="dowebok">    {{--临时数据，不用删除 - 这里引用了插件Viewer.js--}}
                    <li><img data-original="img/tibet-1.jpg" src="/images/bg_page-home.jpg" alt="图片1"></li>
                </ul>
            </div>
            <div class="audioContentArea" id="audioContentArea">
                    <div class="singleExeAudio">    {{--临时数据，不用删除--}}
                        <audio class="audioDom"
                               src="http://wechatappdev-10011692.file.myqcloud.com/apppcHqlTPT3482/audio/73e0562b2ea60701fa9e0a291fc43cf0.mp3"></audio>
                        <div class="audioController">
                            <div class="audioPlayStateIcon paused"></div>
                            <div class="progressBar">
                                <span class="finishedProgress"></span>
                                <span class="progressBarDot"></span>
                            </div>
                            <div class="audioLengthSecond"><span></span>"</div>
                        </div>
                    </div>
            </div>
        </div>
        <div class="windowFooter">
            <div class="btnMid xeBtnDefault closeExeDetailWindow" id="closeExeDetailWindow">关闭</div>
        </div>
    </div>

@stop











