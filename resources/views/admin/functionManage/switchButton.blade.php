
<?php
if (empty($onText) || empty($offText)) {
    $onText = "开启";
    $offText = "关闭";
}
?>

<div class="_switchOperateArea"
     data-switch_id='{{$switchId}}'
     data-switch_state='{{$switchState}}'
     data-on_text="{{$onText}}"
     data-off_text="{{$offText}}"
>
    <div class="_verticalGapLine"></div>

    @if($switchState)
        <div class="_functionSwitchOn _functionSwitch">
            <div class="_switchDescTextOn _switchDescText">{{$onText}}</div>
            <div class="_switchButtonIconOn _switchButtonIcon"></div>
        </div>
    @else
        <div class="_functionSwitchOff _functionSwitch">
            <div class="_switchDescTextOff _switchDescText">{{$offText}}</div>
            <div class="_switchButtonIconOff _switchButtonIcon"></div>
        </div>
    @endif

</div>





