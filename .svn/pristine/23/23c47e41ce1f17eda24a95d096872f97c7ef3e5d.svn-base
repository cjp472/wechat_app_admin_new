
<ul class="baseManageTab">
    @if( session("access")["122"] == 1)
    <li @if(isset($tabTitle) && $tabTitle === "customerList") class="baseActiveTab" @endif>
        <a href="/customer">用户列表</a>
    </li>
    @endif

    @if( session("access")["123"] == 1)
    <li @if(isset($tabTitle) && $tabTitle === "paymentRecord") class="baseActiveTab" @endif>
        <a href="/pay_admin">开通记录</a>
    </li>
    @endif

    @if( session("access")["124"] == 1)
        <li @if(isset($tabTitle) && $tabTitle === "messageList") class="baseActiveTab" @endif>
            <a href="/message">消息列表</a>
        </li>
    @endif

    @if( session("access")["125"] == 1)
        <li @if(isset($tabTitle) && $tabTitle === "feedbackList") class="baseActiveTab" @endif>
            <a href="/feedback">反馈列表</a>
        </li>
    @endif


</ul>