/**
 * Created by Administrator on 2017/5/18.
 */


var $weiXinPay = {

    //常量
    TYPE_ACCOUNT_CHARGE: 1,
    TYPE_GROW_UP_VERSION: 2,
    TYPE_VIP_VERSION: 3,

    currentTime: 0,             //记录时间
    maxTime: 60 * 5,     //5分钟超时间隔

    //重复查询函数对象
    repeatQuery: -1,

    //生成二维码
    createQR: function(codeUrl) {

        var qrCode = new QRCode(document.getElementById("qr_code"), {
            text: codeUrl,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    },

    //查询支付结果
    queryPayResult: function(orderId) {

        $weiXinPay.currentTime += 1;

        if ($weiXinPay.currentTime >= $weiXinPay.maxTime) {
            clearInterval($weiXinPay.repeatQuery);
            $("#mainContent").hide();
            $(".refreshWindow").fadeIn(300);
        }

        $.post('/getPayResult', {"order_id": orderId }, function(result) {

            if (result.code == 0) {
                clearInterval($weiXinPay.repeatQuery);
                $("#mainContent").hide();
                $(".successWindow").fadeIn(300);
                var countdown = 3;
                var chargeTimer = setInterval(function() {
                    console.log(countdown);
                    countdown--;
                    if (countdown >= 0) {
                        $("#paymentSuccessReturn span").text(countdown);

                    } else {
                        clearInterval(chargeTimer);

                        if($weiXinPay.GetQueryString('first')!=1){  //从基础版升级
                            window.location.href = "/accountview";
                        }else{//第一次注册成长版或专业版
                            var href = window.location.href;
                            if(/openNewGrowUp/.test(href)){//成长版
                                if($weiXinPay.GetQueryString('first_login')==1){
                                    window.location.href = "/accountview?first=1&type=1&first_login=1";
                                }else {
                                    window.location.href = "/accountview?first=1&type=1";
                                }
                            }else if(/openNewVip/.test(href)){//专业版
                                if($weiXinPay.GetQueryString('first_login')==1){
                                    window.location.href = "/accountview?first=1&type=2&first_login=1";
                                }else {
                                    window.location.href = "/accountview?first=1&type=2";
                                }
                            }
                        }
                    }
                }, 1000);

            } else if (result.code == 1) {
                console.log(result.msg);
                clearInterval($weiXinPay.repeatQuery);
                $("#mainContent").hide();
                $(".failureWindow").fadeIn(300);

            } else {
                console.log(result.msg);
            }

        });

    },

    //采用正则表达式获取地址栏参数（name）
    GetQueryString: function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)return decodeURIComponent(r[2]);
        return null;
    },

    closeCurrentWindow: function () {
        self.opener = null;
        var close = self.close();
        console.log(close);
        if (close == undefined) {
            window.location.href = "/accountview";
        }
    },


}









