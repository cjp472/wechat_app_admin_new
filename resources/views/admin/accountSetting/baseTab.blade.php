<ul class="baseManageTab">
    @if( session("access")["130"] == 1)
    <li  @if(isset($tabTitle) && $tabTitle === "accountList") class="baseActiveTab" @endif>
        <a href="/accountview">账户一览</a>
    </li>
    @endif

    @if( session("access")["999"] == 1)
    <li  @if(isset($tabTitle) && $tabTitle === "accountManage") class="baseActiveTab" @endif>
        <a href="/accountmanage" >账号管理</a>
    </li>
    @endif

    @if( session("access")["131"] == 1)
    <li  @if(isset($tabTitle) && $tabTitle === "childAccount") class="baseActiveTab" @endif>
        <a href="/admin/child" >子账号管理</a>
    </li>
    @endif

    @if( session("access")["132"] == 1)
    <li  @if(isset($tabTitle) && $tabTitle === "modelSetting") class="baseActiveTab" @endif>
        <a @if(isset($model) && $model === "company") href="/companymodel" @else href="/personmodel" @endif >运营模式</a>
    </li>
    @endif

    @if( session("access")["133"] == 1)
    {{--@if(session('app_id')=='appTCVlUyvG2205')--}}
    <li @if(isset($tabTitle) && $tabTitle === "miniSetting") class="baseActiveTab" @endif>
    	{{-- <a @if(isset($model) && $model === "person") href="/mini/person" @else href="/mini/index" @endif >小程序配置</a> --}}
        <a href="/mini/configure" >小程序配置</a>
    </li>
    {{--@endif--}}
    @endif
    @if(false)
    <li @if(isset($tabTitle) && $tabTitle === "openApiSetting") class="baseActiveTab" @endif>
        <a href="/open/apisetting">开放平台配置</a>
    </li>
    @endif
</ul>
