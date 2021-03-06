
{{--{{ dump($question_list) }}--}}

<table class="table questionListTable">
    <thead>
        <tr>
            <th class="thQuestionDetail">问题详情</th>
            <th style="text-align: left;padding: 0 0 0 30px;width: 150px;">提问者</th>
            <th>提问时间</th>
            <th style="text-align: left;padding: 0 0 0 30px;width: 150px;">回答者</th>
            <th>回答时间</th>
            <th style="min-width: 50px;padding: 0;">偷听者</th>
            <th style="min-width: 60px;padding: 0;">今日偷听</th>
            <th style="min-width: 70px;padding: 0;">状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($question_list as $key => $value)
        <?php
                $expire_time = strtotime($value->expire_at);   //   过期时间
                $current_time = time();                        //   当前时间
                $isExpired = ($expire_time < $current_time);
        ?>
        <tr class="tr_body">
            <td>
                <div class="questionContent" style="display: -webkit-box;-webkit-box-orient: vertical; -webkit-line-clamp: 3"
                >{{$value->content}}</div>
            </td>
            <td>
                <div class="questionerInfo">
                    <img src="{{$value->questioner_avatar}}">
                    <span title="{{$value->questioner_name}}">{{$value->questioner_name}}</span>
                </div>
            </td>
            <td>{{$value->created_at}}</td>
            <td>
                <div class="answerQuestionInfo">
                    <img src="{{$value->answerer_avatar}}">
                    <span title="{{$value->answerer_name}}">{{$value->answerer_name}}</span>
                </div>
            </td>
            <td>
                @if($value->answered_at == '0000-00-00 00:00:00') -- @else {{$value->answered_at}} @endif
            </td>
            <td>{{$value->listenCount ? $value->listenCount : "--"}}</td>
            <td>{{$value->listenToday ? $value->listenToday : "--"}}</td>
            <td>
                {{--{{$value->is_enable_eavesdrop}}--}}
                @if(empty($value->answerer_content) && empty($value->answerer_text) && empty($value->answerer_imgs))
                    @if($isExpired)
                        @if($value->phase == 3)
                            <div style="min-width: 50px;">已退款</div>
                        @else
                            <div style="color: orange;min-width: 50px;">待退款</div>
                        @endif
                    @else
                        <div>待回答</div>
                    @endif
                @else
                    <div>已回答</div>
                @endif
            </td>
            <td class="lastTd">
                <div class="questionListOperateArea">
                    <ul class="questionDetailOperate">
                        @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("has_set_answer", "app_module"))
                        {{--@if(!$isExpired)--}}
                            {{--@if(empty($value->answerer_content))--}}
                                {{--<li class="operate answerQuestionOpera" data-type="answerQuestion" data-id="{{$value->id}}">回答</li>--}}
                            {{--@else--}}
                                {{--<li class="operate answerQuestionOpera" data-type="answerQueAgain" data-id="{{$value->id}}">重新回答</li>--}}
                            {{--@endif--}}
                            {{--<li class="verticalDivideLine" >&nbsp;|&nbsp;</li>--}}
                        {{--@endif--}}
                        @if(empty($value->answerer_content) && empty($value->answerer_text) && empty($value->answerer_imgs))
                            @if(!$isExpired)
                                <li class="operate answerQuestionOpera" data-type="answerQuestion" data-id="{{$value->id}}" data-is_enable="{{$value->is_enable_eavesdrop}}">回答</li>
                                <li class="verticalDivideLine" >&nbsp;|&nbsp;</li>
                            @endif
                        @else
                            <li class="operate answerQuestionOpera" data-type="answerQueAgain" data-id="{{$value->id}}" data-is_enable="{{$value->is_enable_eavesdrop}}" >重新回答</li>
                            <li class="verticalDivideLine" >&nbsp;|&nbsp;</li>
                        @endif

                        @endif
                        <li class="operate" data-type="lookQuestion" data-id="{{$value->question_id}}" data-content="{{$value->content}}" data-imgs="{{$value->imgs}}">查看</li>
                        <li class="verticalDivideLine" >&nbsp;|&nbsp;</li>
                        <li class="operate" data-type="changeQuestionState" data-id="{{$value->question_id}}" data-state="{{$value->state}}">@if($value->state)显示@else隐藏@endif</li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


@if(empty($question_list) || count($question_list) == 0)

    <div class="contentNoneTip">暂无用户提问！</div>

@endif

@if(!empty($question_list))

    <div class="list-page">

        <?php echo $question_list->render() ?>

    </div>

@endif


















