<div class="salerContent">
	<div class="searchContent pull-right">
		<div class="searchArea">
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
			<div class="">
			    <input id="phone" class="inputDefault my-input" type="text" placeholder="搜索手机号查询" @if(!empty($search_array['phone'])) value="{{$search_array['phone']}}" @endif>
			</div>
			<div class="">
				<button data-url="/distribute/achieve" id="searchBtn" class="btnMid xeBtnDefault">搜索</button>
			</div>
			<div class="">
				<button id="showExcel"  data-url="/distribute/excel/achieve" class="btnMid xeBtnDefault">导出Excel</button>
			</div>
		</div>
	</div>

	{{--dump($search_array)--}}
	<div class="tableContainer">
		<table class="table mytable">
			<thead>
				<tr>
					<th class="td_left">头像/昵称</th>
					<th>姓名</th>
					<th>手机号码</th>
					<th>个人订单</th>
					<th>个人订单金额</th>
					<th>下级订单</th>
					<th>下级订单金额</th>
					<th>合计佣金</th>
				</tr>
			</thead>
			<tbody>
				{{--dump($paginator)--}}
				@foreach( $paginator as $key=>$value )
					<tr>
						<td class="td_left">
							<img src="{{$value->img or ''}}" alt="{{$value->wx_nickname or ''}}" width="40px" height="40px">
							<p class="username">{{$value->wx_nickname}}</p>
						</td>
						<td>{{$value->wx_name}}</td>
						<td>{{$value->phone}}</td>
						<td>{{$value->order_count}}</td>
						<td>￥{{number_format($value->order_price/100, 2)}}</td>
						<td>{{$value->sub_order_count}}</td>
						<td>￥{{number_format($value->sub_order_price/100, 2)}}</td>
						<td>￥{{number_format($value->commision/100, 2)}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		@if(count($paginator)==0)
		    <div class="contentNoneTip">没有相应的数据</div>
		@endif
		<div class="list-page">
	        @if(empty($search_array))
	            {!! $paginator->render() !!}
	        @else
	            {!! $paginator->appends($search_array)->render() !!}
	        @endif
	    </div>
	</div>

</div>

