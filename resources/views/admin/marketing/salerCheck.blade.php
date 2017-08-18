<div class="salerContent" style="padding-top: 25px;">
	<ul id="myTabs" class="nav nav-tabs">
		<li>
			<a href="javascript:void(0)" data-url="/distribute/saler">推广员</a>
		</li>
		<li class="active">
			<a href="javascript:void(0)" data-url="/distribute/audit">审核信息</a>
		</li>
	</ul>
	{{--dump($ListInfo)--}}
	{{--dump($search_array)--}}
	<div class="searchContent pull-right">
		<div class="searchArea">
			<div>
				<select class="myselect" name="status" id="checkStatus">
					<option value="">状态</option>
					<option value="0" @if(array_key_exists('status', $search_array) && $search_array['status'] == 0) selected @endif>待审核</option>
					<option value="1" @if(array_key_exists('status', $search_array) && $search_array['status'] == 1) selected @endif>已拒绝</option>
				</select>
			</div>
			<div id="SelectTime" class="pull time_group">
			    <div id="dropdown-toggle" class="time_input dropdown-toggle" data-toggle="dropdown" >
			        <span id="SelectData">全部时间</span>
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

			<div>
			    <input id="phone" class="inputDefault my-input" type="text" placeholder="搜索手机号查询" @if(!empty($search_array['phone'])) value="{{$search_array['phone']}}" @endif>
			</div>
			<div>
				<button id="searchBtn"  data-url="/distribute/audit" class="btnMid xeBtnDefault">搜索</button>
			</div>
		</div>
	</div>
	<div id="salerCheck">
		<div class="tableContainer">
			<table cellpadding="0" class="table mytable">
				<thead>
					<tr>
						<th class="td_left">头像/昵称</th>
						<th>姓名</th>
						<th>手机号码</th>
						<th>申请时间</th>
						<th>状态</th>
						<th>操作</th>
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
						<td>{{$value->apply_at}}</td>
						<td>@if($value->status==0) 待审核 @elseif($value->status==1) 已拒绝 @endif</td>
						<td class="checkContent">
							@if($value->status==0)
							<a href="javascript:void(0)" onclick="agreeSaler('{{$value->user_id}}')">通过</a>
							|
							<a href="javascript:void(0)" onclick="rejectSaler('{{$value->user_id}}')">拒绝</a>
							{{-- <div class="checkBox">
								<input class="with-gap" id="success-{{$value->user_id}}" name="{{$value->user_id}}" type="radio" checked value="2" />
								<label for="success-{{$value->user_id}}">通过</label>
								<input class="with-gap" id="fail-{{$value->user_id}}" name="{{$value->user_id}}" type="radio" value="1" />
								<label for="fail-{{$value->user_id}}">拒绝</label>
								<button onclick="checkUser('{{$value->user_id}}')" class="btnMid btnBlue">确认</button>
								<button onclick="hideCheckBox(this)" class="btnMid xeBtnDefault">取消</button>
							</div> --}}
							@else
							<span>--</span>
							@endif

						</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			@if(count($ListInfo)==0)
			    <div class="contentNoneTip">没有相应的数据</div>
			@endif

			@if( $ListInfo )
		    <div class="list-page">
		        @if(empty($search_array))
		            {!! $ListInfo->render() !!}
		        @else
		            {!! $ListInfo->appends($search_array)->render() !!}
		        @endif
		    </div>
		    @endif
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