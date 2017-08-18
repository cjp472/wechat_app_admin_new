<div id="salerRecord" class="salerContent">
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
				<button data-url="/distribute/records" id="searchBtn" class="btnMid xeBtnDefault">搜索</button>
			</div>
			<div class="">
				<button id="showExcel" data-url="/distribute/excel/records" class="btnMid xeBtnDefault">导出Excel</button>
			</div>
		</div>
	</div>


	<div class="tableContainer">
		<table cellpadding="0" class="table mytable recordTable">
			<thead>
				<tr>
					<th class="td_left" style="width:170px">交易时间</th>
					<th>推广员姓名</th>
					<th>手机号码</th>
					<th style="width: 250px;">商品名称</th>
					<th>成交金额</th>
					<th>佣金比例</th>
					<th style="text-align: left;">佣金</th>
					<th class="td_right">状态</th>
				</tr>
			</thead>
			{{--dump($ListInfo)--}}
			<tbody>
				@foreach($ListInfo as $key=>$value)
				<tr>
					<td class="td_left">{{$value->created_at}}</td>
					<td>{{$value->wx_name}}</td>
					<td>{{$value->phone}}</td>
					<td class="goodsName">
						<p title="{{$value->distribute_name}}">{{$value->distribute_name}}</p>
					</td>
					<td>￥{{ number_format($value->price/100, 2) }}</td>
					<td>{{$value->distribute_percent}}%</td>
					<td class="txt_left">￥{{number_format($value->distribute_price/100, 2)}}</td>
					<td class="td_right">{{$value->status ? '已结算' : '未结算'}}</td>
				</tr>
				@if($value->superior_distribute_user_id)
				<tr class="litterTr">
					<td class="td_left"> </td>
					<td>{{$value->super_wx_name}}</td>
					<td>{{$value->super_phone}}</td>
					<td></td>
					<td></td>
					<td>{{$value->superior_distribute_percent}}%</td>
					<td class="biLevel txt_left">
						￥{{number_format($value->superior_distribute_price/100,2)}}
						<span class="caret"></span>
						<p class="invite">邀请奖励</p>
					</td>
					<td class="td_right">{{$value->status ? '已结算' : '未结算'}}</td>
				</tr>
				@endif
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

