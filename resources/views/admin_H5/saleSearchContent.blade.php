
@foreach($data as $key_1 => $value_1)
    <div class="sale_main_page">
        <a class="sale_main_page_link" href="{{$value_1->$skip_target}}}">
            <div class="sale_main_page_desc">
                <div class="main_page_icon_wrapper">
                    <img class="main_page_icon" src={{$value_1->$app_icon_url}}}>
                </div>
                <span id="{{$key_1}}" class="main_page_name">{{$value_1->$app_name}}</span>
            </div>
        </a>
        <span class="main_page_sale_apply">申请分销主页</span>
    </div>
    @if($value_1->$product_list && count($value_1->$product_list) != 0)
        <div class="sale_column_number">该公众号有{{count($value_1->$product_list)}}个专栏可供分销</div>
    @else
        <div class="sale_column_number">该公众号没有专栏可供分销</div>
    @endif
    @foreach($value_1->$product_list as $key_2 => $value_2)
        {{--每一个分销专栏的条目--}}
        <div class="sale_product">
            <a class="sale_product_link" href="{{$value_2->$skip_target}}}">
                <div class="sale_product_desc">
                    <div class="sale_product_icon_wrapper">
                        <img class="sale_product_icon" src={{$value_2->$column_icon_url}}}>
                    </div>
                    <span class="sale_product_name">{{$value_2->$column_name}}</span>
                </div>
            </a>
            <span class="sale_product_apply">申请分销</span>
        </div>
    @endforeach
    <div class="divide_line">分隔线</div>

@endforeach



















