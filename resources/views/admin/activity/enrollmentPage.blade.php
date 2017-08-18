
<div class="activeNav clearfix">
    <a class="activeNavPart @if($activity_state == 0) activeNavPartActive @endif"
           data-activity_state="0">全部(<span>{{$all_num}}</span>)</a>

    {{--过期以及取消的活动没有待审核一栏，不能进行审核操作--}}
    @if($is_expire == 0 && $state != 2)
        <a class="activeNavPart @if($activity_state == 1) activeNavPartActive @endif"
           data-activity_state="1">待审核(<span>{{$confirming_num}}</span>)</a>
    @endif
    <a class="activeNavPart @if($activity_state == 2) activeNavPartActive @endif"
            data-activity_state="2">报名成功(<span>{{$pass_num}}</span>)</a>

    <a class="activeNavPart @if($activity_state == 3) activeNavPartActive @endif"
            data-activity_state="3">已关闭(<span>{{$unpass_num}}</span>)</a>

    {{--搜索栏--}}
    <div class="activeSearchPart">
        <input id="searchContent" type="text" class="SearchInput" placeholder="姓名/手机号码"
               @if(!empty($search_content)) value="{{$search_content}}" @endif />
        <button class="btnMid xeBtnDefault activeSearchBtn aeSearchBtn">搜索</button>
    </div>
</div>
<div class="activeOperate">
    @if($activity_state == 1)
        <div class="batchAction">
            <div id="batchAllow" class="btnMid btnBlue batchAllow">通过</div>
            <div id="batchRefuse" class="btnMid  xeBtnDefault batchRefuse">拒绝</div>
        </div>
    @endif
    <div class="activeOperate2">
        <button class="btnMid xeBtnDefault messageBtn">消息通知</button>
        <button class="btnMid xeBtnDefault excelBtn">导出Excel</button>
    </div>
</div>




{{--活动用户名单表格--}}
<table class="aeTable">
    <thead>
        <th><input id="allChoose" type="checkbox"></th>
        <th>头像/昵称</th>
        <th>姓名</th>
        <th>手机号码</th>
        <th>报名时间</th>
        <th>票种</th>
        <th>价格(元)</th>
        <th>状态</th>
        <th>操作</th>
    </thead>
    <tbody class="infoTbody">
    @foreach($activity_actor_list as $key=>$activeM)
        <tr class="userInfoWrapper {{$activeM->user_id}}"
            data-info="{{$activeM->field_content}}"
            data-avatar="{{$activeM->wx_avatar_wx}}"
            data-nickname="{{$activeM->wx_nickname}}"
            data-realname="{{$activeM->real_name}}"
            data-phone="{{$activeM->phone}}"
            data-state="{{$activeM->state}}"
            data-userid="{{$activeM->user_id}}">
            <td class="tdFirst notShowInfo">
                <input data-userid="{{$activeM->user_id}}" class="chooseUser" type="checkbox">
            </td>
            <td>
                <img class="tdMemberLogo" src="{{$activeM->wx_avatar_wx}}" alt="用户头像">
                <span class="tdMemberName">{{$activeM->wx_nickname}}</span>
            </td>
            <td class="tdMemberRealName">
                <span class="tdRealName">{{$activeM->real_name}}</span>
            </td>
            <td class="tdMenberPhone">{{$activeM->phone}}</td>
            <td>
                <span>{{$activeM->created_at}}</span>
            </td>
            <td>
                <span>{{$activeM->ticket_name}}</span>
            </td>
            <td>
                <span>{{$activeM->ticket_price/100}}</span>
            </td>
            <td>
                @if($activeM->state==0)
                    待审核
                @elseif($activeM->state ==1 || $activeM->state ==5)
                    报名成功
                @elseif($activeM->state ==2)
                    已拒绝
                @elseif($activeM->state ==3)
                    已取消
                @elseif($activeM->state ==4)
                    已作废
                @else
                    --
                @endif
            </td>
            <td class="tdOperate notShowInfo">
                @if($activeM->state==0)
                    @if($is_expire == 0)
                        <span class="adminPass allowUser" data-userid="{{$activeM->user_id}}">通过</span>
                        | <span class="refuseUser" data-userid="{{$activeM->user_id}}" data-realname="{{$activeM->real_name}}"
                                data-phone="{{$activeM->phone}}">拒绝</span>
                    @else
                        --
                    @endif
                @elseif($activeM->state==1 || $activeM->state ==5)
                    <span class="ejectUser" data-userid="{{$activeM->user_id}}">作废</span>
                @else
                    --
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(count($activity_actor_list)==0)
    <div class="contentNoneTip">没有相应的数据</div>
@endif

<div class="list-page">
    @if(!empty($search_content))
        <?php echo $activity_actor_list->appends(['searchContent' => $search_content, 'activity_id' => $activity_id, 'activity_state' => $activity_state])->render(); ?>
    @else
        <?php echo $activity_actor_list->appends(['activity_id' => $activity_id, 'activity_state' => $activity_state])->render(); ?>
    @endif
</div>