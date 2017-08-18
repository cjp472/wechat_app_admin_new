<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    {{--meta标签--}}
    @include("headSetting.head_meta")
    {{--设置rem的基础font-size--}}
    @include("headSetting.set_htmlFontSize")

    {{--网页标题，必填--}}
    <title>分销内容</title>

    {{--初始化css--}}
    @include("publicSource.publicCss")
    {{--初始化js--}}
    @include("publicSource.publicJs")

    {{--页面的css--}}
    <link rel="stylesheet" href="css/admin_H5/saleHomePage.css?{{env('timestamp')}}">

    {{--分销主页js逻辑--}}
    <script src="js/admin_H5/saleHomePage.js?{{env('timestamp')}}"></script>


</head>
<body>

    {{--搜索页面--}}
    <div class="page_wrapper">

        <div class="page">
            {{--搜索内容--}}
            <div class="sale_content_title c7">小 鹅 通</div>
            <div class="sale_content_search_wrapper">
                <input class="sale_content_search" placeholder="输入商户名称">
            </div>
            <div class="search_suggestion c3">
                <p>1、搜索商户名称</p>
                <p>2、点击“申请分销”申请成为分销商</p>
                <p>3、申请通过后即可获得分销链接，还可随时查看分销数据</p>
            </div>

            {{--搜索结果--}}
            <div class="search_no_data hide">没有相关数据</div>
            <div class="search_result c3">
                <div class="search_result_title">搜索结果</div>
                <div class="search_area_wrapper">
                    {{--通过js添加搜索结果--}}
                    <div class="search_area">

                    </div>
                </div>

            </div>
        </div>

    </div>

    {{--提交申请模块--}}
    @if($is_applied && count($is_applied) > 0)
        {{--已经申请过--}}
        <div class="submit_application_page" data-is_applied="1">
            <div class="transparent_top_area"></div>
            <div class="submit_application_area">
                <div class="submit_list_item">
                    <div class="title_left t2">分销名称</div>
                    <input class="sale_name submit_input" placeholder="请输入你的分销名称">
                </div>
                <div class="submit_list_item">
                    <div class="title_left t2">真实姓名</div>
                    <input class="real_user_name submit_input"
                           value="{{$is_applied->applier}}" placeholder="请输入你的真实姓名">
                </div>
                <div class="submit_list_item">
                    <div class="title_left t2">手机号码</div>
                    <div class="phone_number_wrapper">
                        <input type="text" readonly="readonly" class="phone_number submit_input"
                               value="{{$is_applied->phone}}"  placeholder="请输入正确的手机号">
                        <span class="change_phone_number c3">修改手机号</span>
                    </div>
                </div>
                <div class="submit_list_item verify_code_wrapper hide">
                    <div class="title_left t2">验证码</div>
                    <input class="verify_code submit_input" placeholder="请输入验证码">
                    <div onclick="sendsms()" class="acquire_verify_code">获取验证码</div>
                </div>
                <div class="submit_list_item_remarks">
                    <div class="title_left t2">备注</div>
                    <textarea class="remarks submit_input" placeholder="输入备注信息"></textarea>
                </div>
                {{--输入内容有误--}}
                <div class="error_remind"></div>
                <div class="submit">提交申请</div>
            </div>
        </div>
    @else
        {{--还没有申请过--}}
        <div class="submit_application_page" data-is_applied="0">
            <div class="transparent_top_area"></div>
            <div class="submit_application_area">
                <div class="submit_list_item">
                    <div class="title_left t2">分销名称</div>
                    <input class="sale_name submit_input" placeholder="请输入你的分销名称">
                </div>
                <div class="submit_list_item">
                    <div class="title_left t2">真实姓名</div>
                    <input class="real_user_name submit_input" placeholder="请输入你的真实姓名">
                </div>
                <div class="submit_list_item">
                    <div class="title_left t2">手机号码</div>
                    <div class="phone_number_wrapper">
                        <input class="phone_number submit_input" placeholder="请输入正确的手机号">
                        <span class="change_phone_number c3 hide">修改手机号</span>
                    </div>
                </div>
                <div class="submit_list_item verify_code_wrapper">
                    <div class="title_left t2">验证码</div>
                    <input class="verify_code submit_input" placeholder="请输入验证码">
                    <div onclick="sendsms()" class="acquire_verify_code">获取验证码</div>
                </div>
                <div class="submit_list_item_remarks">
                    <div class="title_left t2">备注</div>
                    <textarea class="remarks submit_input" placeholder="输入备注信息"></textarea>
                </div>
                {{--输入内容有误--}}
                <div class="error_remind"></div>
                <div class="submit">提交申请</div>
            </div>
        </div>
    @endif

    @include("admin_H5.bottomTab")

    {{--所有不需要预先加载的js，都在下面引入--}}

</body>
</html>