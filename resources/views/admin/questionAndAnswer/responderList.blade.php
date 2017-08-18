
{{--{{ dump($answerer_list) }}--}}

<table class="table responderListTable">
    <thead>
        <tr>
            {{--<th class="thInviteLink">邀请链接</th>--}}
            <th>头像</th>
            <th>姓名</th>
            <th>职业/头衔</th>
            <th>手机号</th>
            <th>提问价格</th>
            <th>提问分成</th>
            <th>总回答数</th>
            <th>今日回答</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($answerer_list as $key => $value)
        <tr class="tr_body">
            {{--<td class="tdInviteLink" >--}}
                {{--<div style="display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;"--}}
                {{-->{{$value->url}}</div>--}}
            {{--</td>--}}
            <td>
                <div class="answererInfo">
                    <img src="{{$value->answerer_avatar}}">
                </div>
            </td>
            <td title="{{$value->answerer_name}}">
                <div class="responderListName">
                    {{$value->answerer_name ? $value->answerer_name : '--'}}
                </div>
            </td>
            <td>{{$value->position ? $value->position : '--'}}</td>
            <td>{{$value->phone ? $value->phone : '--'}}</td>
            <td>{{$value->price ? ($value->price / 100.00).'元/次' : '--'}}</td>
            <td>
                @if(!empty($value->profit_answer))
                    @if($value->price >= 10)
                        {{number_format($value->price * $value->profit_answer / 10000.00, 2)}}元/次
                    @else
                        0元/次
                    @endif
                @else
                    --
                @endif
            </td>
            <td>{{$value->answerCount ? $value->answerCount : '--'}}</td>
            <td>{{$value->answerToday ? $value->answerToday : '--'}}</td>
            <td class="lastTd">
                <div class="responderListOperateArea">
                    <ul class="responderDetailOperate">
                        <li class="operate" data-type="editResponderInfo" data-id="{{$value->answerer_id}}" data-state="{{$value->state}}">设置</li>
                        <li class="verticalDivideLine" >&nbsp;|&nbsp;</li>
                        <li class="operate" data-type="changeResponderState" data-id="{{$value->answerer_id}}" data-state="{{$value->state}}">@if($value->state)上线 @else 下线 @endif</li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(empty($answerer_list) || count($answerer_list) == 0)

    <div class="contentNoneTip">暂无答主，赶紧去邀请答主吧！</div>
    <div class="contentNoneTip">（用户只能向在线答主进行提问，无上线答主用户将无法提问）</div>

@endif

@if(!empty($answerer_list))

    <div class="list-page">

        <?php echo $answerer_list->render() ?>

    </div>

@endif


















