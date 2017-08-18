

{{--{{ dump($data) }}--}}
{{--{{ dump($classify) }}--}}

<div class="addDistributeGoodsArea">
    <div class="btnBlue btnMid addDistributeGoodsBtn" id="addDistributeGoods" data-count="{{$count}}">添加推广商品</div>
    <div class="addDistributeGoodsDesc">(<span class="countSpan">{{$count}}</span>/20)&nbsp;&nbsp;添加说明：您最多可添加20个商品。添加后请务必：<span style="color: #f77849;">1.设置梯度分成 2.设置分类 3.上传推广文案 否则无法通过审核</span></div>
</div>

<div class="tableHeaderPart">
    <div class="tableHeader">
        <div>商品名称</div>
        <div>商品类型</div>
        <div>商品价格(元）</div>
        <div class="distributeGoodsState">梯度分成
            <div class="stateHoverBoxWrapper" style="left: 75%;">
                <div class="stateHoverBox">
                    <img src="/images/alert/blue_info_prompt.svg">
                    <div class="hoverTextContent">
                        渠道商直接推广您上架的商品时将采用梯度分成机制，您最多可设置三层梯度，渠道商根据您设置的梯度获得不同级别的奖励分成。
                    </div>
                </div>
            </div>
        </div>
        <div>分类</div>
        <div>推广文案</div>
        <div class="distributeGoodsState" >状态
            <div class="stateHoverBoxWrapper">
                <div class="stateHoverBox">
                    <img src="/images/alert/blue_info_prompt.svg">
                    <div class="hoverTextContent">
                        {{--完成商品设置的课程将会被运营人员审核，后续会有小鹅通精选运营人员与您取得联系。通过审核的课程将会显示已上架状态--}}
                        完成梯度分成设置、分类设置、上传推广文案后，该商品将会提交给我们的运营人员，经过筛选后优质内容将会上架至小鹅通分销市场
                    </div>
                </div>
            </div>
        </div>
        <div>推广数量</div>
    </div>
</div>
<div class="tableBodyPart">
@foreach($data as $key => $value)
    <div class="tableBodyItem"
         data-resource_id="{{$value->resource_id}}"
         data-resource_type="{{$value->resource_type}}"
         data-old_class_id="{{$value->classify_id}}"
    >
        <div>
            <div class="goodsCoverImg">
                <img src="{{$value->img_url_compressed}}" alt="封面">
            </div>
            <div class="goodsName" title="{{$value->resource_name}}">{{$value->resource_name}}</div>
        </div>
        <div>
            <?php
            switch ($value->resource_type) {
                case 1:
                    echo "图文";
                    break;
                case 2:
                    echo "音频";
                    break;
                case 3:
                    echo "视频";
                    break;
                case 4:
                    echo "直播";
                    break;
                case 5:
                    echo "专栏";
                    break;
                case 6:
                    echo "会员";
                    break;
                default:
                    echo "其它";
                    break;
            }
            ?>
        </div>
        <div>
            <?php
            if ($value->price == 0) {
                $goodsPrice = 0;
            } else {
                $goodsPrice = number_format($value->price / 100, 2);
            }
            if ($value->resource_type == 6) {
                if($value->period == 2592000)
                    echo $goodsPrice."/月";
                else if($value->period == 7776000)
                    echo $goodsPrice."/季度";
                else if($value->period == 15811200)
                    echo $goodsPrice."/半年";
                else if($value->period == 31622400)
                    echo $goodsPrice."/年";
                else
                    echo $goodsPrice."/年";
            } else {
                echo $goodsPrice;
            }
            ?>
        </div>
        <div>
            @if($value->is_chosen == 1)
                <div class="gradientRatioValue">{{$value->min_precent."% ~ ".$value->max_precent."%"}}</div>
            @else
                @if($value->max_precent == 0 && $value->min_precent == 0)
                    <div class="setSingleGoodsRatioBtn setGradient" data-type="set">设置</div>
                @else
                    <div class="gradientRatioValue">{{$value->min_precent."% ~ ".$value->max_precent."%"}}
                        <span class="setGradient"
                              data-type="edit"
                              data-distribute_data="{{ json_encode($value->distribute_data) }}"
                        >修改</span>{{--未上架可以修改--}}
                    </div>
                @endif
            @endif
        </div>
        <div>
            @if($value->is_chosen == 1)
                <div class="selectedChosenGoodsClass">{{$value->classify_name?$value->classify_name:"--"}}</div>
            @else
                <select class="selectChosenGoodsClass">
                    <option value="-1">请选择分类</option>
                    @foreach($classify as $k => $v)
                        <option value="{{$v->id}}" @if($v->id == $value->classify_id) selected @endif >{{$v->name}}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <div>
            @if($value->is_chosen == 1)
                @if($value->distribute_content_state == 1)
                    <div class="previewQrCodeWrapper">
                        <div class="previewQrCodeBtn">预览</div>
                        <div class="previewQrCodeBox">
                            <div class="qrCodeImage" id="{{"qrCode_".$key}}" data-url="{{$value->content_qrcode_url}}">

                            </div>
                        </div>
                    </div>
                @else
                    <p>--</p>
                @endif
            @else
                @if($value->distribute_content_state == 1)
                    <div class="previewQrCodeWrapper" style="width: 50%;float: left;">
                        <div class="previewQrCodeBtn" style="text-align: right;">预览</div>
                        <div class="previewQrCodeBox">
                            <div class="qrCodeImage" id="{{"qrCode_".$key}}" data-url="{{$value->content_qrcode_url}}">

                            </div>
                        </div>
                    </div>
                    |&nbsp;&nbsp;<a href="manage_content?resource_id={{$value->resource_id}}&resource_type={{$value->resource_type}}">修改</a>
                @else
                    <a href="manage_content?resource_id={{$value->resource_id}}&resource_type={{$value->resource_type}}">上传</a>
                @endif
            @endif

        </div>
        <div style="@if($value->is_chosen == 1) color: #f77849; @endif">
            @if($value->is_chosen == 1)
                已上架
            @else
                @if($value->distribute_data && $value->classify_name && $value->distribute_content_state)
                    已提交
                @else
                    待设置
                 @endif
            @endif
        </div>
        <div>{{$value->purchase_count}}</div>
    </div>
@endforeach
</div>











