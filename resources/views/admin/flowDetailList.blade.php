<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link rel="stylesheet" href="../css/admin/flowDetailList.css?{{env('timestamp')}}"/>     {{--css--}}
@endsection

@section('page_js')
    <script src="../js/admin/flowDetailList.js?{{env('timestamp')}}"></script>          {{--js--}}
@endsection


@section("base_mainContent")
    {{--标题 - header --}}
    <div class="header">
        <div class="header_level home_page">账户一览 ></div>
        <div class="header_level home_page">结算记录 ></div>
        <div class="header_level">流量详情 </div>
    </div>

    <div class="content">
        <div class="content_title">
            <div class="transaction identifier">结算单号：{{$id}}</div>
            <div class="transaction time">费用产生日期：{{$charge_at}}</div>
            <div class="transaction type">结算类型：流量费</div>
            <div class="transaction fee_sum">费用总计：{{$fee_sum}}元</div>
        </div>
        <table class="table_flow_detail">
            <thead>
                <tr class="tr_head" height="40px">
                    <th class="th_resource_name">资源名称</th>
                    <th class="th_resource_size">资源大小</th>
                    <th class="th_new_visitor_num">新增访问人数</th>
                    <th class="th_fee">费用（元）</th>
                </tr>
            </thead>
            <tbody>

            @foreach($result_list as $key => $result)
                <tr class="tr_body">
                    <td class="td_resource_name">{{$result->resource_name}}</td>
                    <td class="td_resource_size">
                        <div class="resource_size_area">
                            <div class="resource_size_wrapper" data-resource_type="{{$result->resource_type}}"
                                     @if($result->resource_size < 1)
                                        data-original_size="{{number_format($result->resource_size * 1024, 2)}}KB"
                                     @else
                                        data-original_size="{{number_format($result->resource_size, 2)}}MB"
                                     @endif
                                     @if($result->resource_size_compressed < 1)
                                        data-compressed_size="{{number_format($result->resource_size_compressed * 1024, 2)}}KB"
                                     @else
                                        data-compressed_size="{{number_format($result->resource_size_compressed, 2)}}MB"
                                     @endif
                                     @if($result->img_size_total < 1)
                                        data-image_size="{{number_format($result->img_size_total * 1024, 2)}}KB">
                                     @else
                                        data-image_size="{{number_format($result->img_size_total, 2)}}MB">
                                     @endif
                                <span class="resource_size_num">
                                    @if($result->size_total != 0)
                                        {{number_format($result->size_total,2)}}MB
                                    @else
                                        0MB
                                    @endif
                                </span>
                                <div class="hover_prompt_icon_wrapper">
                                    <img class="hover_prompt_icon" src="../images/hover_prompt_info.svg">
                                </div>
                            </div>
                        </div>

                    </td>
                    <td class="td_new_visitor_num">{{$result->day_viewcount}}</td>
                    <td class="td_fee">{{number_format($result->fee / 100.00, 4)}}</td>
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
            <div class="two_size_wrapper">
                <span class="compressed_size">优化后资源大小：</span>    <span class="original_size">（原资源大小：）</span>
            </div>
            <div class="image_size">图片大小：</div>
        </div>
    </div>
@stop


