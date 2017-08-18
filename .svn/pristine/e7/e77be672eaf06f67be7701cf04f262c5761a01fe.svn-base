/**
 * Created by xiaoe on 2017/2/22.
 */

$(document).ready(function () {
    rechargePage.init();
});

var rechargePage = {

    lastPrice: -1,

    init: function () {

        //先默认生成一个二维码
        rechargePage.checkInputPrice();

        //  回车事件
        $(document).keypress(function(e) {
            if (e.which == 13) {
                rechargePage.checkInputPrice();
            }
        });

        $("#money").on("keyup", function () {
            rechargePage.checkInputPrice();
        });

        $("#money").on("blur", function () {
            rechargePage.checkInputPrice();
        });

        $("#returnAccount").click(function () {
            window.location.href = "/accountview";
        });

        $("#continueCharge").click(function () {
            $(".successWindow").fadeOut(100);
        });

        $("#payAgain, #refreshCurrentPage").click(function () {
            window.location.href = window.location.href;
        });

    },

    checkInputPrice: function () {

        var value = $("#money").val();

        //清除"数字"和"."以外的字符
        value = value.replace(/[^\d.]/g, "");

        //验证第一个字符是数字而不是.
        value = value.replace(/^\./g, "");

        //只保留第一个. 清除多余的
        value = value.replace(/\.{2,}/g, ".");
        value = value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");

        value = value.replace(/^0[\d]+/g, "0");

        //只能输入两个小数
        value = value.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');

        //  充值最大值 - 999,999
        value = value.replace(/^(\d{6})(\d+)$/, '$1');

        $("#money").val(value);

        if (value == rechargePage.lastPrice) {
            console.log("充值价格没有变化。");
            return false;
        }
        rechargePage.lastPrice = value;

        if (value < 100) {
            $("#inputPriceTip").css("color", "red");
            $("#qrCodeScan").hide();
            $("#qrCodeScanTip").hide();
            return false;
        }

        $("#inputPriceTip").css("color", "#999999");
        rechargePage.prePayWeChat(value * 100);
        $("#qrCodeScan").fadeIn(300);


    },

    prePayWeChat: function(inputPrice) {

        $.ajax("/pre_wechatPay", {
            type: "POST",
            dataType: "json",
            data: {
                price: inputPrice,
                type: $weiXinPay.TYPE_ACCOUNT_CHARGE
            },
            success: function (result) {
                if (result.code == 0) {
                    $("#qr_code").html("");
                    $weiXinPay.createQR(result.data['code_url']);
                    $("#qrCodeScanTip").fadeIn(300);

                    clearInterval($weiXinPay.repeatQuery);
                    $weiXinPay.repeatQuery = setInterval($weiXinPay.queryPayResult, 1000, result.data['order_id']);

                } else {
                    console.log(result.msg);
                    alert("生成二维码失败!");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                alert("服务器出小差了，请刷新页面再试！");
            }
        });

    },

};

