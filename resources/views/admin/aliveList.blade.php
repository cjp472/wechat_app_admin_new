<?php
$pageData = [];
$pageData['sideActive'] = 'content_list';
$pageData['barTitle'] = '直播列表';
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/aliveList.css?{{env('timestamp')}}" />
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}" />
@endsection

@section('page_js')
    <script src="../js/external/xcConfirm.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/external/clipboard.min.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/aliveList.js?{{env('timestamp')}}"></script>
    <script>
        setTopUrlCookie('alive_listop','直播列表');
    </script>
@endsection


@section('base_mainContent')
    {{--标题--}}
    <div class="title">
        <div class="eachTitle" id="audio">音频</div>
        <div class="eachTitle" id="video">视频</div>
        <div class="eachTitle" id="article">图文</div>
        <div class="eachTitle" style="border-bottom: 2px solid #2e64cb;" id="alive">直播(<span class="red-font">公测</span>)</div>
        <div class="eachTitle" id="package">专栏</div>
        @if(session('version_type') != 1)
            <div class="eachTitle" id="tab_member">会员</div>
        @endif
    </div>

    {{--搜索框--}}
    <div class="searchArea">
        <div class="searchButtonArea">
            <button class="btn btn-default" id="searchButton">搜索</button>
        </div>

        <div class="searchInputArea">
            <input id="search" type="text" class="form-control" aria-label="..." />
        </div>

        <div class="searchSelectArea">
            <select class="form-control" id="ruler" >
                <option selected value="0">直播名称</option>
                <option value="1">创建时间</option>
                <option value="2">专栏名称</option>
            </select>
        </div>

        @if(\App\Http\Controllers\Tools\AppUtils::isWhiltList(\App\Http\Controllers\Tools\AppUtils::getAppID()))
            <div class="tool_bar_item_left">
                <a id="addAliveButton" class="btn btn-default btn-blue" href="/addalive"  style="margin-left: 0;">+新增直播</a>
            </div>
        @else
            {{--<button type='button' id="addAliveButton">+新增直播</button>--}}
            <span style="margin: 10px" class="red-font">直播平台内测中,敬请期待...</span>
        @endif

    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>封面</th>
                <th><div class="alive_table_name">名称</div></th>
                <th><div class="alive_table_title">显示状态</div></th>
                <th><div class="alive_table_title">直播状态</div></th>
                <th><div class="alive_table_title">付费类型</div></th>
                <th><div class="alive_table_title">所属专栏</div></th>
                <th>价格</th>
                <th><div class="alive_table_title">开播时间</div></th>
                <th><div class="alive_table_title">直播人员</div></th>
                {{--<th><div class="alive_table_title">上架时间</div></th>--}}
                <th><div class="alive_table_title">转码状态</div></th>
                <th style="min-width: 170px;">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
                <tr class="tr_body" data-resource_name="{{$value["title"]}}" data-resource_id="{{$value["id"]}}" data-piece_price="{{$value["piece_price"]}}">
                    <td style="text-align: left;"><img src="{{$value['img_url_compressed']}}" class="img_url"/></td>
                    <td>{{$value['title']}}</td>

                    {{--显示状态--}}
                    @if($value['state']==0)
                    <td>显示</td>
                    @else
                    <td>隐藏</td>
                    @endif

                    <td>{{$value['zb_state']}}</td>
                    <td>{{$value['payment_type']}}</td>
                    <td class="product_name_column">{{$value['product_name']}}</td>
                    <td>{{$value['piece_price']}}</td>
                    <td>{{$value['zb_start_at']}}</td>

                    {{--直播人员列表 - start--}}
                    @if(empty($value['zb_user_name']) || count($value['zb_user_name']) == 0)
                        <td>无</td>
                    @else
                        <td>
                            <div class="{{$key}} alive_name_wrapper" value={{$key}}>
                                @foreach($value['zb_user_name'] as $k => $name)
                                    @if($k === 3)
                                        <p id={{$key}} class="alive_name_hide">...</p>
                                        <p class="alive_name">{{$name}}</p>
                                    @else
                                        <p class="alive_name">{{$name}}</p>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endif
                    {{--直播人员列表 - end--}}

                    {{--上架时间列表暂时隐藏--}}
                    {{--<td>{{$value['start_at']}}</td>--}}

                    {{--是否转码--}}
                    @if($value['is_transcode']==0)
                    <td>转码中</td>
                    @elseif($value['is_transcode']==1)
                    <td>已转码</td>
                    @else
                    <td>转码失败</td>
                    @endif
                    {{--根据直播状态+转码状态设置可进行的操作--}}
                    <td>
                        <div class="dropdown dropdown_w">
                            <button class="btn btn-default" type="button" onclick="aliveComment('{{$value['id']}}')">查看评论</button>
                            @if(session('wxapp_join_statu')==1 || session('is_collection') == 1)
                                <button class="btn btn-default copyHref"  aria-label="复制成功！" data-clipboard-text="{{$value['pageurl']}}" title="获取访问链接">
                                    <span class="glyphicon glyphicon-link"></span>
                                </button>
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"  title="更多">
                                    <span class="caret"></span>
                                </button>
                            @else
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" title="">
                                    更多<span class="caret"></span>
                                </button>
                            @endif
                            <ul class="dropdown-menu dropdown-menu-right listnav_minwidth" role="menu" aria-labelledby="dropdownMenu1">
                                <li role="presentation">
                                    <a role="menuitem" tabindex="-1" onclick="editAlive('{{$value['id']}}')">编辑</a>
                                </li>

                                @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("has_invite", "app_module") && $value["payment_type"] != '专栏')
                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" class="set_sale_ratio_btn">分销设置</a>
                                    </li>
                                @endif

                                <li role="presentation">
                                    @if($value['state']==0)
                                        <a role="menuitem" tabindex="-1" onclick="offSale('{{$value['id']}}')">下架</a>
                                    @else
                                        <a role="menuitem" tabindex="-1"
                                       @if($value['is_transcode']==1)
                                       onclick="onSale('{{$value['id']}}')"
                                       @else
                                       onclick='showErrorToast("转码中无法上架");'
                                        @endif>上架</a>
                                    @endif
                                </li>
                                @if($value['zb_state']!='直播结束')
                                <li role="presentation">
                                    <a role="menuitem" tabindex="-1" onclick="endAlive('{{$value['id']}}')">结束直播</a>
                                </li>
                                @endif
                                <li role="presentation" class="divider"></li>
                                <li role="presentation">
                                    <a role="menuitem" tabindex="-1" onclick="deleteResource('{{$value['id']}}')">删除</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{--页标--}}
    <div class="list-page">
        @if(empty($search))
            <?php echo $allInfo->appends(['ruler' => $ruler])->render(); ?>
        @else
            <?php echo $allInfo->appends(['ruler' => $ruler, 'search'=> $search])->render(); ?>
        @endif
    </div>
@stop

@section("base_modal")

    <div class="set_sale_ratio_window" style="display: none">
        <div class="set_sale_window">
            <div class="window_top_area">
                <div class="select_window_title">分销设置</div>
                <div class="close_icon_wrapper_3">
                    <img class="close_icon" src="/images/icon_Pop-ups_close.svg">
                </div>
            </div>
            <div class="set_sale_content_area">
                <div class="sale_goods_desc">
                    <div class="set_sale_title_1">商品名称</div>
                    <div class="sale_goods_name">--</div>
                </div>
                <div class="switch_sale">
                    <span class="set_sale_title_2">邀请卡分销</span>
                    <input id="set_radio_on" class="with-gap" name="set_sale_radio" type="radio" value="0" />
                    <label for="set_radio_on" class="label_1">开启</label>
                    <input id="set_radio_off" class="with-gap" name="set_sale_radio" type="radio" value="1" />
                    <label for="set_radio_off" class="label_2">关闭</label>
                </div>
                <div class="set_percent">
                    <span class="set_sale_title_3">设置分成比例</span>
                    <input class="input_radio_value inputDefault" placeholder="请输入比例">
                    <span>%</span>
                </div>
                <div class="set_radio_word">1.该值为分销者的分成比例</div>
                <div class="set_radio_word">2.分成比例仅可设置为1%—50%</div>
                <div class="set_radio_word">3.开启分销功能后，用户通过邀请卡购买商品，分销者即可获得收益</div>
                <div class="set_radio_word">4.平台收益自动进入可提现余额中，您可在财务管理>提现记录中查看到该类型订单的收益，并提现至您的微信账户中</div>
                <div class="set_radio_word">5.每张邀请卡有效期为30天</div>
            </div>
            <div class="right_area">
                <div class="phone_preview">
                    <img class="phone_preview_img" src="/images/admin/resManage/set_sale_preview.png">
                    <div class="phone_preview_title">示意图</div>
                </div>
            </div>
            <div class="button_area">
                <div class="cancel_sale_btn btnMid xeBtnDefault">取消</div>    <div class="confirm_sale_btn btnMid btnBlue">确定</div>
            </div>
        </div>
    </div>

@stop

