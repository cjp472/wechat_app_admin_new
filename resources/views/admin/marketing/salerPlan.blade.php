<div class="salerSection">
    <div class="salerSectionT">链接地址</div>
    <div class="salerSectionC">
        <div class="salerLink">@if($url){{$url}}@endif</div>
        <div class="salerLinkCopy copyHref" data-clipboard-text="{{$url}}">复制链接</div>
    </div>
</div>
<div class="salerSection">
    <div class="salerSectionT">页面标题</div>
    <div class="salerSectionC">
        <input type="text" class="inputDefault planNameInput" value="{{$info->title}}">
    </div>
</div>
<div class="salerSection">
    <div class="salerSectionT">详情描述</div>
    <div class="salerSectionC">
        {{--临时存储--}}
        @if($info->descrb)
            <input id="rubbish" type="hidden" value="{{$info->org_content}}" />
        @endif
        <div class="salerDescribe">
            {{--<script id="resource_desc" type="text/plain"></script>--}}

        </div>
        <div class="waves-effect btnSmall xeBtnDefault coverUpbtn" id="preview" style="margin-left: 80px;margin-right: 0;">
            预览
        </div>
        <div class="checkModelBtn">查看模板</div>
    </div>
</div>

<div class="boxLine"></div>
<div class="btnMid btnBlue salerPlansubMit">保存</div>