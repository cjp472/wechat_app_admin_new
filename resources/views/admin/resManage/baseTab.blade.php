
<ul class="baseManageTab">
    <li @if(isset($tabTitle) && $tabTitle === "resourceList") class="baseActiveTab" @endif>
        <a href="/resource_list_page" style="padding: 0 34px;">单品</a>
    </li>
    <li @if(isset($tabTitle) && $tabTitle === "packageList") class="baseActiveTab" @endif>
        <a href="/package_list_page" style="padding: 0 34px;">专栏</a>
    </li>

    @if(\App\Http\Controllers\Tools\AppUtils::IsPageVisual("vip_period", "version_type")){{--基础版看不到会员入口--}}
    <li @if(isset($tabTitle) && $tabTitle === "memberList") class="baseActiveTab" @endif>
        <a href="/member_list_page" style="padding: 0 34px;">会员</a>
    </li>
    @endif

</ul>