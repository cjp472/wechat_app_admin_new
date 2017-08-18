<ul class="baseManageTab">

    @if( session("access")["126"] == 1)
    @if( \App\Http\Controllers\Tools\AppUtils::getCollection()==0 )
        <li  @if(isset($tabTitle) && $tabTitle === "companyIncome") class="baseActiveTab" @endif>
            <a href="/income/company">企业模式收入</a>
        </li>
    @endif
    @endif

    @if( session("access")["127"] == 1)
    <li  @if(isset($tabTitle) && $tabTitle === "personIncome") class="baseActiveTab" @endif>
        <a href="/income/person">个人模式收入</a>
    </li>
    @endif

    @if( session("access")["129"] == 1)
        {{--@if(session('is_collection') == 1 || \App\Http\Controllers\Tools\AppUtils::IsVisualWithDraw())--}}
        <li  @if(isset($tabTitle) && $tabTitle === "withdrawPage") class="baseActiveTab" @endif>
            <a href="/withdraw_page" >提现记录</a>
        </li>
    {{--@endif--}}
    @endif

</ul>