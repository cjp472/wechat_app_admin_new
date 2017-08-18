@extends('admin.baseLayout')


@section('page_css')
    <link rel="stylesheet" href="../css/admin/huidu.css?{{env('timestamp')}}"/>     {{--css--}}
@endsection

@section('page_js')
    <script src="../js/admin/huidu.js?{{env('timestamp')}}"></script>               {{--js--}}
@endsection

@section('base_mainContent')
    <div class="header">
        <ul>
            <li style="border-bottom:2px solid #2a75ed;"><a>账户删除</a></li>
        </ul>
    </div>

    {{-- 块：编号一 --}}
    <div class="content">
        <div class="word_desc_11">请输入查询账户所匹配的手机号：</div>
        <div class="word_wrapper_11">
            <input class="input_phone" placeholder="手机号">
        </div>

        <div class="word_wrapper_12">
            <div class="query_btn">开始查询</div>
            <div class="set_huidu_btn">设为个人版</div>
            {{--<div class="delete_account_btn">删除账户</div>--}}
        </div>

        <div class="account_model company_to_personal_css">当前账户模式:</div>
        <div class="account_wechat company_to_personal_css">绑定服务号:</div>
        <div class="account_mechant company_to_personal_css">保存商户信息:</div>
        <div class="account_detail_nick_name company_to_personal_css">昵称:</div>
        <div class="account_detail_phone company_to_personal_css">联系电话:</div>
        <div class="account_detail_company company_to_personal_css">公司:</div>

    </div>
@stop












