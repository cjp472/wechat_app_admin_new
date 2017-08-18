<div class="salerContent" style="padding-top: 25px;">
	<ul id="myTabs" class="nav nav-tabs">
		<li class="active">
			<a href="javascript:void(0)" data-url="/distribute/saler">推广员</a>
		</li>
		<li>
			<a href="javascript:void(0)" data-url="/distribute/audit">审核信息</a>
		</li>
	</ul>
	{{--dump($ListInfo)--}}

	<div class="searchContent pull-right">
		<div class="searchArea">
			<div id="SelectTime" class="pull time_group">
			    <div id="dropdown-toggle" class="time_input dropdown-toggle" data-toggle="dropdown" >
			        <span id="SelectData">申请时间</span>
			        <span class="caret "></span>
			    </div>
			    <div id="SelectRange" class="time_option dropdown-menu">
			        <ul>
			            <li data-type='all'>全部时间</li>
			            <li data-type='nowMonth'>当月时间</li>
			        </ul>
			        <p id="optional" class="optional">自选时间</p>
			    </div>
			</div>
			<input type="hidden" id="startTime" name="start_time" />
			<input type="hidden" id="endTime" name="end_time" />
			<div class="">
			    <input id="phone" class="inputDefault my-input" type="text" placeholder="搜索手机号查询" @if(!empty($search_array['phone'])) value="{{$search_array['phone']}}" @endif>
			</div>
			<div class="">
				<button id="searchBtn" data-url="/distribute/saler" class="btnMid xeBtnDefault">搜索</button>
			</div>
		</div>
	</div>
	<div id="salerList">
		<div class="tableContainer">
			<table cellpadding="0" class="table mytable">
				<thead>
					<tr>
						<th class="td_left">头像/昵称</th>
						<th>姓名</th>
						<th>手机号码</th>
						<th>邀请方</th>
						<th>累计成交笔数</th>
						<th>累计成交金额</th>
						<th>加入时间</th>
						<th class="td_right">操作</th>
					</tr>
				</thead>
				<tbody>
				@foreach($ListInfo as $key=>$value)
					<tr>
						<td class="td_left">
							<img src="{{$value->wx_avatar}}" alt="{{$value->wx_nickname}}" width="40px" height="40px">
							<p class="username">{{$value->wx_nickname}}</p>
						</td>
						<td>{{$value->wx_name}}</td>
						<td>{{$value->phone}}</td>
						<td>{{$value->super_name or '--'}}</td>
						<td>{{$value->count}}</td>
						<td>￥{{number_format($value->count_price/100,2)}}</td>
						<td>{{$value->passed_at}}</td>
						<td class="td_right"><a href="javascript:void(0)" onclick="clearSaler('{{$value->user_id}}')">清退</a></td>
					</tr>
				@endforeach
				</tbody>
			</table>

			@if(count($ListInfo)==0)
			    <div class="contentNoneTip">没有相应的数据</div>
			@endif


		    <div class="list-page">
		        @if(empty($search_array))
		            {!! $ListInfo->render() !!}
		        @else
		            {!! $ListInfo->appends($search_array)->render() !!}
		        @endif
		    </div>
		</div>
	</div>
</div>



<script>
	/*$('#myTabs a:first').tab('show');
	$('#myTabs a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})*/

	timeRange = {
	    start: '{{empty($search_array['start_time']) ? '' : $search_array['start_time']}}',
	    end: '{{empty($search_array['end_time']) ? '' : $search_array['end_time']}}'
	}


</script>