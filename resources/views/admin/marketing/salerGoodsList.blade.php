{{--获取默认比例--}}
<input type="hidden" id="distributePercent" value="{{$configInfo->distribute_percent}}">
<input type="hidden" id="superiorDistributePercent" value="{{$configInfo->superior_distribute_percent}}">
<div class="goodsSearch">
    <div class="goodsSearchContent">
        <input id="phone" type="text" class="goodsSearchInput inputDefault" placeholder="输入名称" value="{{$name}}"/>
        <div id="searchBtn" data-url="/distribute/goods" class="goodsSearchBtn btnSmall xeBtnDefault">搜索</div>
    </div>
</div>
{{-- {{dump($paginator)}} --}}
{{--{{dump($configInfo)}}--}}
<div class="tableContainer tableContainer1">
    <table cellpadding="0" class="table">
        <thead>
        <tr>
            <th class="td_left">商品名称</th>
            <th>商品类型</th>
            <th>价格</th>
            <th>总销量</th>
            <th>是否参与推广</th>
            <th>佣金比例</th>
            <th>邀请奖励</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($paginator as $key => $i)
        <tr>
            <td class="td_left"><img src="{{$i->img_url_compressed}}" alt="商品图标"><p class="goodTitle">{{$i->name}}</p></td>
            @if($i->goods_type==0)
                <td>专栏</td>
            @elseif($i->goods_type==5)
                <td>会员</td>
            @elseif($i->goods_type==1)
                <td>图文</td>
            @elseif($i->goods_type==2)
                <td>音频</td>
            @elseif($i->goods_type==3)
                <td>视频</td>
            @elseif($i->goods_type==4)
                <td>直播</td>
            @endif
            <td>￥{{$i->price/100}}</td>
            <td>{{$i->sum}}</td>
            <td>@if($i->has_distribute) 是 @else 否 @endif</td>
            <td>@if($i->has_distribute) @if($i->first_distribute_default){{$i->first_distribute_percent}}%@else{{$configInfo->distribute_percent}}%@endif @else -- @endif</td>
            <td>@if($i->has_distribute) @if($i->superior_distribute_default){{$i->superior_distribute_percent}}%@else{{$configInfo->superior_distribute_percent}}%@endif @else -- @endif</td>
            <td><span class="salerGoodsOperate"
                      data-good_type="@if($i->goods_type){{$i->goods_type}}@endif"
                      data-id="@if($i->id){{$i->id}}@endif"
                      data-price="{{$i->price/100}}"
                      data-has_distribute="{{$i->has_distribute}}"
                      data-distribute_percent="{{$i->first_distribute_percent}}"
                      data-distribute_default="{{$i->first_distribute_default}}"
                      data-superior_distribute_default="{{$i->superior_distribute_default}}"
                      data-superior_distribute_percent="{{$i->superior_distribute_percent}}"
                      data-distribute_poster="{{$i->distribute_poster}}"
                      data-is_distribute_show_userinfo="{{$i->is_distribute_show_userinfo}}"
                >设置</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if(count($paginator)==0)
    <div class="contentNoneTip">没有相应的数据</div>
@endif

@if($paginator)
<div class="list-page">
    @if(empty($name))
        {!! $paginator->render() !!}
    @else
        {!! $paginator->appends(['name' => $name])->render() !!}
    @endif
</div>
@endif
