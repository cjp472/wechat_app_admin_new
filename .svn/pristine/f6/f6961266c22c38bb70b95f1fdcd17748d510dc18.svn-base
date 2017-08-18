<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link rel="stylesheet" href="../css/admin/storageDetailList.css?{{env('timestamp')}}"/>     {{--css--}}
@endsection

@section('page_js')
    <script src="../js/admin/storageDetailList.js?{{env('timestamp')}}"></script>          {{--js--}}
@endsection


@section("base_mainContent")
    {{--标题 - header --}}
    <div class="storageDetail_content">
        <div class="header">
            <div class="header_level home_page">账户一览 ></div>
            <div class="header_level home_page">结算记录 ></div>
            <div class="header_level">存储详情</div>
        </div>
        <div class="charge_deal_title">
            <div class="charge_deal_num">结算单号：{{$id}}</div>
            <div class="charge_deal_time">费用产生日期：{{$charge_at}}</div>
            <div class="charge_deal_type">结算类型：存储费</div>
            <div class="charge_deal_type">费用总计：{{$fee_sum}}元</div>
        </div>
        <table class="charge_deal_table">
            <thead>
                <tr class="charge_deal_tr_thead">
                    <th class="charge_deal_name_th">资源名称</th>
                    <th class="charge_deal_size_th">资源大小</th>
                    <th class="charge_deal_fee_th">费用（元）</th>
                </tr>
            </thead>
            <tbody>
            @foreach($result_list as $key => $result)
                <tr>
                    <td class="td_resource_name">{{$result->resource_name}}</td>    {{--资源名称--}}
                    <td class="td_resource_size">   {{--资源大小--}}
                        <div class="resource_size_area">
                            <div class="resource_size_wrapper"
                                     data-resource_type="{{$result->resource_type}}"
                                     @if($result->resource_size < 1)
                                        data-original_size="{{number_format($result->resource_size * 1024, 2)}}KB"
                                     @else
                                        data-original_size="{{number_format($result->resource_size, 2)}}MB"
                                     @endif

                                     @if($result->img_size_total < 1)
                                        data-image_size="{{number_format($result->img_size_total * 1024, 2)}}KB">
                                     @else
                                        data-image_size="{{number_format($result->img_size_total, 2)}}MB">
                                     @endif
                                <span class="resource_size_num">
                                    @if(!empty($result->day_storage) && $result->day_storage > 1024)
                                        {{number_format($result->day_storage / 1024.00, 2)}}GB
                                    @elseif(!empty($result->day_storage) && $result->day_storage >= 1)
                                        {{number_format($result->day_storage, 2)}}MB
                                    @elseif(!empty($result->day_storage) && $result->day_storage > 0)
                                        {{number_format($result->day_storage * 1024, 2)}}KB
                                    @else
                                        0KB
                                    @endif
                                </span>
                                <div class="hover_prompt_icon_wrapper">
                                    <img class="hover_prompt_icon" src="../images/hover_prompt_info.svg">
                                </div>
                            </div>
                        </div>
                    </td>
                    @if($result->fee < 0.005)        {{--费用（元）--}}
                        <td class="td_fee">0</td>
                    @else
                        <td class="td_fee">{{number_format($result->fee / 100.00, 4)}}</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--实现table分页--}}
        <div class="list-page">
            <?php echo $result_list->appends(['charge_at' => $charge_at, 'id' => $id, 'fee_sum' => $fee_sum])->render(); ?>
        </div>
    </div>

@stop

@section("base_modal")
    <div class="hover_prompt">
        <div class="hover_prompt_wrapper">
            <div class="original_size">原音频大小：</div>
            <div class="image_size">图片大小：</div>
        </div>
    </div>
@stop




