
<ul class="baseManageTab">
    @if( session("access")["108"] == 1)
    <li @if(isset($tabTitle) && $tabTitle === "phonePreview") class="baseActiveTab" @endif>
        <a href="/interfacesetting">手机预览</a>
    </li>
    @endif

    @if( session("access")["109"] == 1)
    <li @if(isset($tabTitle) && $tabTitle === "shopIndexDiy") class="baseActiveTab" @endif>
        <a href="/shopIndexDiy">首页自定义</a>
    </li>
    @endif



    {{--@if( session("access")["banner"] == 1)--}}
        {{--<li @if(isset($tabTitle) && $tabTitle === "bannerPicture") class="baseActiveTab" @endif>--}}
            {{--<a href="/getBannerList">轮播图</a>--}}
        {{--</li>--}}
    {{--@endif--}}

    @if( session("access")["110"] == 1)
    <li @if(isset($tabTitle) && $tabTitle === "shareSetting") class="baseActiveTab" @endif>
        <a href="/sharesetting">分享设置</a>
    </li>
    @endif


    @if( session("access")["111"] == 1)
    <li @if(isset($tabTitle) && $tabTitle === "wxAccountSetting") class="baseActiveTab" @endif>
        <a href="/wxaccountsetting">公众号设置</a>
    </li>
    @endif

    {{--<li @if(isset($tabTitle) && $tabTitle === "attentionSetting") class="baseActiveTab" @endif>--}}
        {{--<a href="/interfacesetting">关注设置<开发中></a>--}}
    {{--</li>--}}
    {{--@if( session("access")["112"] == 1)--}}
    {{--@if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("resource_category", "version_type"))--}}
        <li @if(isset($tabTitle) && $tabTitle === "functionManage") class="baseActiveTab" @endif>
            <a class="functionManage" href="javascript:void(0)">功能管理</a>
        </li>
    {{--@endif--}}
    {{--@endif--}}
    {{--manage_function--}}

</ul>
<input type="hidden" id="versionType" value="{{\App\Http\Controllers\Tools\AppUtils::get_version_type()}}" />
