/**
 * Created by Administrator on 2017/2/20.
 */

$(document).ready(function () {

    init();

    /*检查账户余额，*/
    // checkAccountBalance();

    //  红色横条提示
    //++++ showCustomNotice();

    //  资费说明弹窗
    $(".cost_explain").click(function () {
        $(".cost_explain_prompt").show();
    });

    $(".cost_explain_bg").click(function () {
        $(".cost_explain_prompt").hide();
    });

    $(".i_know_btn").click(function () {
        $(".cost_explain_prompt").hide();
    });

    //  立即升级
    $(".update_atOnce_btn").click(function () {
        window.location.href = "/upgrade_account";
    });

    //  查看所有高级功能 ==> 升级
    $(".look_up_senior_function").click(function () {
        window.location.href = "/upgrade_account";
    });

    //  充值
    $(".charge_btn").click(function () {
        window.location.href = "/get_recharge_page";
    });



});

//  初始化
function init() {

    var type = GetQueryString('type')==1 ? '成长版' : '专业版',
    txt = '<p style="padding: 0 30px;">您已成功开通小鹅通'+type+'账户，您现在可前往手机预览页面查看您的专属店铺</p>',
    option = {
        title: "开通成功", //弹出框标题
        btn: 3, //确定&&取消
        oktext: '立即查看',
        canceltext: '关闭',
        icon: 'green',
        onOk: function() {
            if(GetQueryString('first_login')==1) {
                window.location.href = "/interfacesetting?first_login=1";
            }else {
                window.location.href = "/interfacesetting";
            }
        }
    };
    if(GetQueryString('first')==1){
        $.alert(txt, "custom", option);
    }

}

//  流量详情
function getFlowDetails(charge_at, id, fee_sum) {
    window.location.href = "/flow_detail_list?charge_at=" + charge_at + "&id=" + id + "&fee_sum=" + fee_sum;
}

//  存储详情
function getStorageDetails(charge_at, id, fee_sum) {
    window.location.href = "/storage_detail_list?charge_at=" + charge_at + "&id=" + id + "&fee_sum=" + fee_sum;
}

/**
 * 短信详情
 * @param charge_at - 交易时间
 * @param id    - 交易单号
 * @param fee_sum   - 交易费用总计
 */
function getSmsDetails(charge_at, id, fee_sum) {
    window.location.href = "/sms_detail_list?charge_at=" + charge_at + "&id=" + id + "&fee_sum=" + fee_sum;
}


function checkAccountBalance() {
    var value = $(".remaining_number").text();

    value = value.replace(/,/g, '');

    var app_balance = parseFloat(value);


    // if (app_balance >= 0 && app_balance < 50) {
    //     $(".red_prompt").show();
    //     return false;
    // }

    if (app_balance < 0) {// 欠费弹出框
        $(".window_prompt").show();
        return false;
    }

}

function showCustomNotice() {
    $(".red_prompt").show();
}




