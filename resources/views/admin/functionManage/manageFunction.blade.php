@extends('admin.baseLayout',[
    'sideActive' => 'knowledgeShop',
    'barTitle' => '店铺设置'
])

@section('page_css')
    {{--弹窗组件--}}
    <link type="text/css" rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}">
    <link type="text/css" rel="stylesheet" href="../css/admin/manageFunction.css?{{env('timestamp')}}"/>
@stop

@section('page_js')
    {{--弹窗--}}
    <script type="text/javascript" src="../js/external/jquery-alert.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/manageFunction.js?{{env('timestamp')}}"></script>
@stop

@section('base_mainContent')

    @include("admin.knowledgeShop.baseTab", ["tabTitle" => "functionManage"])

    <div class="content">
        <div class="function_item category_nav" id="category_navigation">
            <div class="function_content">
                <div class="function_title">首页分类导航</div>
                <div class="function_desc">可以将内容放在对应名称分类里面，用户可以直接快速通过分类找到自己想要的内容。</div>
                <a class="function_help_doc" href="/helpCenter/problem?document_id=d_58f0bdbc6d480_cG4yTPMw" target="_blank">查看【首页分类导航】教程</a>
            </div>
            @include("admin.functionManage.switchButton", [
                "switchId" => "category_navigation",
                "onText" => "显示",   //默认值<开启/关闭>，可不定义
                "offText" => "隐藏",
                "switchState" => $data->resource_category == 1 ? true : false
            ])
            <div class="editCategoryNavBtn" id="editCategoryNav">编辑</div>
        </div>

        <div class="function_item resource_subscribe" data-subscribe="{{$data->hide_sub_count}}">
            <div class="function_content">
                <div class="function_title">订阅量展示</div>
                <div class="function_desc">关闭后您的手机端店铺页面将不会展示商品的订阅量。</div>
            </div>
            @include("admin.functionManage.switchButton", [
                "switchId" => "subscribe_count",
                "switchState" => $data->hide_sub_count == 0 ? true : false
            ])
        </div>

        <div class="function_item subscribe_per_num" data-per_num="{{$data->is_show_resourcecount}}">
            <div class="function_content">
                <div class="function_title">更新期数展示</div>
                <div class="function_desc">关闭后您的手机端店铺页面将不会展示商品的更新期数。</div>
            </div>
            @include("admin.functionManage.switchButton", [
                "switchId" => "update_num",
                "switchState" => $data->is_show_resourcecount == 1 ? true : false
            ])
        </div>

        <div class="function_item category_nav">
            <div class="function_content">
                <div class="function_title">消息提醒</div>
                <div class="function_desc">打开消息提醒开关，能够帮助您更好地将消息触达到用户。您推送给用户的消息将不仅在小纸条内显示，同时会在用户访问页面内弹窗提醒。</div>
                <a class="function_help_doc" href="/helpCenter/problem?document_id=d_593a6284ea1c5_Q288HB1m" target="_blank">查看【消息提醒】说明</a>
            </div>
            @include("admin.functionManage.switchButton", [
                "switchId" => "message_remind",
                "switchState" => $data->is_alert_message == 1 ? true : false
            ])
        </div>
        @if($use_collection == 1)
        <div class="function_item serviceRemind" data-is-push="{{$data->is_person_message_push}}">
            <div class="function_content">
                <div class="function_title">服务号通知</div>
                <div class="function_desc">开启该功能后，用户可通过【小鹅通晓】服务号接收消息通知。通知内容包括：专栏/会员更新提醒；直播开课提醒；社群动态提醒；问答专区提醒。</div>
                <a class="function_help_doc" href="/helpCenter/problem?first_id=44&second_id=45&document_id=doc_598dcf69a8367_8AjB9" target="_blank">查看【服务号通知】说明</a>
            </div>
            @include("admin.functionManage.switchButton", [
                "switchId" => "service_remind",
                "switchState" => $data->is_person_message_push == 1 ? true : false
            ])
        </div>
        @endif
    </div>

@stop

















