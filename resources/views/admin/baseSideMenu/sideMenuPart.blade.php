{{--侧边栏的菜单选项--}}

<?php
if (isset($isSpread)) {
    $isContainSecondIndex = true;   //是否有二级菜单
    if ($isSpread == true) {
        $isSpreadOut = true;    //是否展开一级索引
    } else {
        $isSpreadOut = false;
    }
} else {
    $isContainSecondIndex = false;
}
?>

<div id="{{$id}}"
     class="base_menu_sub sideMenu
        @if(isset($sideActive) && $sideActive === $id) active @endif @if($isContainSecondIndex) isContainSecondIndex @endif"
     data-href="{{$href}}"
     data-is_contain_second_index="{{$isContainSecondIndex}}"
>
    @if(isset($iconSrc) && $iconSrc != "")
        <img class="base_icon"
             @if(isset($sideActive) && $sideActive === $id)
                src="{{$iconSrcActive}}"
             @else
                src="{{$iconSrc}}"
             @endif
        />
    @endif
    <span>{{$title}}</span>
    @if($isContainSecondIndex && 1==2){{--暂时先隐藏小箭头--}}
        @if($isSpreadOut)
            <div class="_spreadIcon _spreadIconDown"></div>
        @else
            <div class="_spreadIcon _spreadIconUp"></div>
        @endif
    @endif
</div>










