

{{--{{ dump($settingData) }}--}}

<div class="QA_settingSection">
    <div class="settingSectionT2">
        偷听人数显示<span class="starIcon">*</span>
    </div>
    <div class="settingSectionC">
        <div class="radioGroup">
            <div class="radioBtn1">
                <input class="with-gap" id="openEavesdrop" name="isShowListen" value="1" type="radio"
                        @if($settingData['isShowListen'] == 1) checked @endif >
                <label for="openEavesdrop" >显示偷听人数</label>
            </div>
            <div class="radioBtn2">
                <input class="with-gap" id="closeEavesdrop" name="isShowListen" value="0" type="radio"
                       @if($settingData['isShowListen'] == 0) checked @endif >
                <label for="closeEavesdrop" >不显示偷听人数</label>
            </div>
        </div>
    </div>
</div>

<div class="QA_settingSection">
    <div class="settingSectionT2">
        短信提醒设置<span class="starIcon">*</span>
    </div>
    <div class="settingSectionC">
        <div class="radioGroup">
            <div class="radioBtn1">
                <input class="with-gap" id="openSmsRemind" name="isSmsRemind" value="1" type="radio"
                       @if($settingData['isSmsRemind'] == 1) checked @endif >
                <label for="openSmsRemind" >开启<span>（建议开启。当天有用户提问，答主会在20：00收到短信提醒；用户提问被回答，该用户会收到短信提醒）</span></label>
            </div>
            <div class="radioBtn2">
                <input class="with-gap" id="closeSmsRemind" name="isSmsRemind" value="0" type="radio"
                       @if($settingData['isSmsRemind'] == 0) checked @endif >
                <label for="closeSmsRemind" >关闭<span>（关闭后，用户/答主只能收到系统小纸条，可能会错过回答时间噢。）</span></label>
            </div>
        </div>
    </div>
</div>

<div class="contentTemplateDesc1">答主收到提问短信内容（暂不可编辑）</div>
<div class="contentTemplateWrapper">
    <div class="contentTemplate">
        {{--[问答专区名称] 您好，有用户向您提了一个问题，赶紧去回答吧！<br>--}}
        {{--问题链接：[链接地址]（请复制此消息在微信中打开链接）--}}
        亲爱的答主：有用户向您提出问题，赶紧去问答专区回答问题吧！
    </div>
</div>

<div class="contentTemplateDesc2">用户被回答短信内容（暂不可编辑）</div>
<div class="contentTemplateWrapper">
    <div class="contentTemplate">
        {{--[问答专区名称] 您好，答主已经回答了您的问题，赶紧去查看吧！<br>--}}
        {{--问题链接：[链接地址]（请复制此消息在微信中打开链接）--}}
        亲爱的用户，答主[答主名称]已经回答了您的问题，您可直接在问答专区查看！
    </div>
</div>












