/**
 * Created by Administrator on 2017/5/17.
 */

$(document).ready(function () {
    growUpVersion.init();
});

growUpVersion = {

    payPrice: 100 * 100,     //  单位:分

    init: function () {

        $.ajax("/pre_wechatPay", {
            type: "POST",
            dataType: "json",
            data: {
                price: growUpVersion.payPrice,
                type: $weiXinPay.TYPE_GROW_UP_VERSION
            },
            success: function (result) {

                //检查版本
                var localVersion = result.data['user_local_version'];
                if (localVersion == $weiXinPay.TYPE_GROW_UP_VERSION) {
                    alert("您已经是成长版了，点击确定跳回管理台");
                    window.location.href = "/accountview";
                    return false;
                }
                if (result.code == 0) {
                    $("#qr_code").html("");
                    $weiXinPay.createQR(result.data['code_url']);

                    $weiXinPay.repeatQuery = setInterval($weiXinPay.queryPayResult, 5000, result.data['order_id']);
                } else {
                    console.log(result.msg);
                    alert("服务器出小差了，请刷新页面再试！");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                alert("服务器出小差了，请刷新页面再试！");
            }
        });

        $("#closeCurrentWindow").click(function () {
            window.location.href = "/accountview";
        });

        $("#payAgain, #refreshCurrentPage").click(function () {
            window.location.href = window.location.href;
        });

    },

};





