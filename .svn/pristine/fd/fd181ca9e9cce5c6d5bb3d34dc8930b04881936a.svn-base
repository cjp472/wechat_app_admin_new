

<div class="operateArea">
    <div class="operateAreaLeft">
        报名成功：{{$pass_num}}&nbsp;&nbsp;&nbsp;已签到：{{$sign_num}}&nbsp;&nbsp;&nbsp;未签到：{{$unsign_num}}
    </div>

    <div class="operateAreaRight">
        <button class="btnMid xeBtnDefault excelBtn">导出Excel</button>
    </div>

    <div class="operateAreaRight">
        <div class="qrCodeAttend">二维码签到</div>
        <input class="phoneNumSearch inputDefault" placeholder="姓名/手机号码"
               @if(!empty($search_content)) value="{{$search_content}}" @endif >
        <div class="searchAttendanceList xeBtnDefault btnMid">搜索</div>
    </div>
</div>

<table class="attendanceListTable">
    <thead>
    <tr>
        <th style="padding-left: 35px; text-align: left; min-width: 100px;">头像/昵称</th>
        <th>姓名</th>
        <th>手机号码</th>
        <th>票种</th>
        <th>价格/元</th>
        <th>票号</th>
        <th>状态</th>
        <th style="width: 10%;">操作</th>
    </tr>
    </thead>
    <tbody class="attendanceListInfo">
    @foreach($activity_actor_list as $key => $value)
        <tr class="attendanceInfoWrapper">
            <td class="avatarNicknameWrapper">
                <img class="userIcon" src="{{$value->wx_avatar_wx}}"  alt="用户头像">
                <span class="userName" title="{{$value->wx_nickname}}">{{$value->wx_nickname}}</span>
            </td>
            <td>{{$value->real_name}}</td>
            <td>{{$value->phone}}</td>
            <td>{{$value->ticket_name}}</td>
            <td>{{$value->ticket_price/100}}</td>
            <td>{{$value->ticket_num}}</td>
            <td class="signState">
                @if($value->state == 5)
                    已签到
                @else
                    未签到
                @endif
            </td>
            <td class="changeSignState"
                data-user_id="{{$value->user_id}}"
                data-ticket_type="{{$value->ticket_type}}"
                data-real_name="{{$value->real_name}}"
                data-phone="{{$value->phone}}"
            >
                @if($value->state == 5)
                    <div class="cancelSign">取消签到</div>
                @else
                    <div class="confirmSign">签到</div>
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
        <?php echo $activity_actor_list->appends(['searchContent' => $search_content, 'activity_id' => $activity_id])->render(); ?>
    @else
        <?php echo $activity_actor_list->appends(['activity_id' => $activity_id])->render(); ?>
    @endif
</div>