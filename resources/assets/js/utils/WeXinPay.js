/**
 * Created by Administrator on 2017/3/6.
 */
//  充值类型常量
var TYPE_ACCOUNT_CHARGE = 1;
var TYPE_GROW_UP_VERSION = 2;
var TYPE_VIP_VERSION = 3;

var order_id = '';
var create_qr_flag = true;
//  记录上一次的成功生成二维码的时间点
var last_pay_time = 0;

//  记录上一次的成功生成二维码时的价格
var last_pay_price = 0;


/**
 *
 * @param price     微信充值钱数,单位:分
 * @param type      微信充值类型：1-余额充值,2-成长版,3-专业版。
 */
function pre_pay_wechat(price, type) {
    var params = {};
    params['price'] = price; //单位:分
    params['type'] = type;
    $.post('/pre_wechatPay', params, function(data) {
        if (type != 1) {
            var local_version = data.data['user_local_version'];
            if (local_version == TYPE_VIP_VERSION && (type == TYPE_VIP_VERSION || type == TYPE_GROW_UP_VERSION)) {
                alert("您已经是专业版了，点击确定跳回管理台");
                window.location.href = "/accountview";
                return false;
            } else if (local_version == TYPE_GROW_UP_VERSION && type == TYPE_GROW_UP_VERSION) {
                alert("您已经是成长版了，点击确定跳回管理台");
                window.location.href = "/accountview";
                return false;
            }
        }
        if (data.code == 0) {
            $(".scan_screen").fadeIn(300);
            $(".scan_screen_content").fadeIn(300);
            if (data.data) {
                if (type == TYPE_ACCOUNT_CHARGE) {
                    if (last_pay_price == 0) {
                        last_pay_price = price;
                        //  新建
                        create_new_qr_code(data);
                    } else {
                        if (last_pay_price == price) {
                            judge_pay_valid_time(data);
                        } else {
                            last_pay_price = price;
                            //  新建
                            create_new_qr_code(data);
                        }
                    }
                } else {
                    judge_pay_valid_time(data);
                }
            } else {
                baseUtils.show.redTip("生成二维码失败!");
            }

        } else {
            baseUtils.show.redTip(data.msg);
        }

    });
}

function create_new_qr_code(data) {
    order_id = data.data['order_id'];
    $("#qr_code").html("");
    createQR(data.data['code_url']);
    clear = setInterval(queryresult, 5000);
}

function createQR(code_url) {
    //生成二维码
    var qrcode = new QRCode(document.getElementById("qr_code"), {
        text: code_url,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    create_qr_flag = false;
}

/**
 * 判断支付时间是否过期
 */
function judge_pay_valid_time(data) {
    if (last_pay_time != 0) {
        //  获取当前时间戳
        var time_stamp = (new Date()).valueOf();
        if (time_stamp - last_pay_time >= max_time) {
            create_new_qr_code(data);
        }
        last_pay_time = time_stamp;
    } else {
        var time_stamp = (new Date()).valueOf();
        last_pay_time = time_stamp;

        create_new_qr_code(data);
    }
}


//查询支付结果
var clear;
var max_time = 300 * 1000;
var current_time = 0;

function queryresult() {
    query_pay_result(order_id);
}

function query_pay_result(orderId) {

    $.post('/getPayResult', { "order_id": orderId }, function(data) {
        if (data.code == 0) {
            clearInterval(clear);
            $(".scan_screen_content").hide();
            $(".scan_status_success").show();
            var chargeRemain = 3;
            var charge_timer = setInterval(function() {
                console.log(chargeRemain);
                chargeRemain--;
                if (chargeRemain >= 0) {
                    $(".scan_status_countdown span").text(chargeRemain);
                }
                if (chargeRemain < 0) {
                    $(".scan_screen").fadeOut(300);
                    clearInterval(charge_timer);
                    if(GetQueryString('first')!=1){  //从基础版升级
                        window.location.href = "/accountview";
                    }else{//第一次注册成长版或专业版
                        var href = window.location.href;
                        if(/open_growUp/.test(href)){//成长版
                            if(GetQueryString('first_login')==1){
                                window.location.href = "/accountview?first=1&type=1&first_login=1";
                            }else {
                                window.location.href = "/accountview?first=1&type=1";
                            }
                        }else if(/open_vip/.test(href)){//专业版
                            if(GetQueryString('first_login')==1){
                                window.location.href = "/accountview?first=1&type=2&first_login=1";
                            }else {
                                window.location.href = "/accountview?first=1&type=2";
                            }
                        }
                    }
                }
            }, 1000)
            // $("#bind_wx_account").html(data.data);
        } else {
            // clear = setInterval(queryresult,5000);

            current_time += 1000;

            if (current_time == max_time) {
                clearInterval(clear);
                $(".scan_screen_content").hide();
                $(".scan_status_fail").show();
            }
        }
    });

}

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}
