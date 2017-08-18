$(function () {
    resTypeSelect.init();
});

var uploadChannelType = +GetQueryString('upload_channel_type');

var resTypeSelect = function () {
    var resTypeSelect = {};

    var resType;

    resTypeSelect.init = function () {

        //回资源列表
        var $getBack = $("#getBack");
        switch (uploadChannelType) {
            case 1:
                $getBack.attr("href","/resource_list_page");
                break;
            case 2:
                $getBack.attr("href","/package_detail_page?id=" + GetQueryString('id'));
                break;
            case 3:
                $getBack.attr("href","/member_detail_page?id=" + GetQueryString('id'));
                break;
            default:
                $getBack.attr("href","/resource_list_page");
                break;
        }

        $(".resType").click(function () {
            $(".resType").css("border", "1px solid #e3e3e3");
            $(this).css("border", "1px solid #2a75ed");
        });
        $(".resType").mouseover(function () {
            $(".resType").removeClass("z-depth-4");
            $(this).addClass("z-depth-4");
        });
        $(".resType").mouseleave(function () {
            $(".resType").removeClass("z-depth-4");
        });

        $(".resType1").click(function () {
            resType = 2;
            resChoiceEnter();
        });
        $(".resType2").click(function () {
            resType = 3;
            resChoiceEnter();
        });
        $(".resType3").click(function () {
            resType = 1;
            resChoiceEnter();
        });
        $(".resType4").click(function () {
            resType = 4;
            resChoiceEnter();
        });
        function resChoiceEnter() {
            if (resType == null) {
                baseUtils.show.redTip("请选择您的资源类型！");
            } else {
                var channel, packageId,
                    url = "/create_resource_page?type=" + resType;

                if( channel =  GetQueryString('upload_channel_type')){
                    url += '&upload_channel_type=' + channel;
                }
                if( channel != 1 && (packageId = GetQueryString('id')) ){
                    url += '&package_id=' + packageId + '&price=' + GetQueryString('price');
                }
                window.location = url;
            }
        };

        $('.typeSelectCancel').click(function() {
            window.history.back();
        });
    };
    return resTypeSelect;
}();