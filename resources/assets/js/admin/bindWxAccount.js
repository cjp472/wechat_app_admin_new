/**
 * Created by fuhaiwen on 2017/1/17.
 */
var app_id;
var bind_account_wx_id;
var create_qr_flag ;
var qt_http ;
$(document).ready(function () {
    //生成二维码
    create_qr_flag = true;
    // createQR();
    //填入搜索值
    // $("select[id='generate_type']").val((getUrlParam("generate_type")==null  || getUrlParam("generate_type")=='') ? '' :getUrlParam("generate_type"));

    setTopUrlCookie('payadmin_listop','财务管理');
    keyEnter($('#pay_search_btn'));
    $("tbody tr").mouseover(function()
    {
        $(this).css({'background-color':'#f5f5f5'});
    }).mouseout(function()
    {
        $(this).css({'background-color':'#fff'});
    });

    // $("#sms_code").blur(function () {
    //     judgeSmscode("#sms_code", "#sms_code_err");
    //     }
    // );

});

// function judgeSmscode() {
//
// }

function createQR() {
    //生成二维码
    app_id = $("#bind_wxaccount").data("app_id");
    var qrcode = new QRCode(document.getElementById("qr_code"),
        {
            text: qt_http+bind_account_wx_id,
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    create_qr_flag = false;
}
//扫码弹框显示
var create_recode = true;
function ShowQRCode() {
    $("#ExportModal").modal('show');

    if(create_recode) {
         //发送ajax请求生成一条记录在表t_bind_account_wx中
         $.get('/create_wx_account_by_appid', {'app_id': app_id}, function (data) {
             if (data.code == 0) {
                 //显示弹框
                 // $("#bind_wxaccount").data('bind_account_wx_id').val(data.data);
                 bind_account_wx_id = data.data;
                 if (create_qr_flag) {
                     createQR();
                 }
                 // queryresult();
                 clear = setInterval(queryresult,5000);

             } else {
                 baseUtils.show.redTip(data.msg);
             }
         });
     }
    create_recode = false;


}
//查询扫码结果
var clear;
var max_time = 300*1000;
var current_time = 0;
function queryresult() {

    $.get('/query_saomiao_result',function (data) {
        if(data.code == 0){
            clearInterval(clear);
            create_recode = true;
            create_qr_flag = true;
            $("#ExportModal").modal('hide');
            $("#bind_wx_account").html(data.data);
        }else{
            // clear = setInterval(queryresult,5000);

            current_time += 1000;

            if(current_time == max_time){
                create_recode = true;
                create_qr_flag = true;
                clearInterval(clear);
            }
        }
    });

}

function confirm_bind_wx() {

    //发送确认绑定的请求:ajax请求
    //传的参数有:头像、昵称、open_id以及验证码

    var wx_avatar = $("#wx_avatar")[0].src;
    var wx_nickname = $("#wx_nickname").text();
    var wx_open_id = $("#wx_avatar").data('wx_open_id');
    var sms_code = $("#sms_code").val();

    // alert("wx_open_id"+wx_open_id);
    if (wx_open_id == '' || wx_nickname == '' || wx_avatar == '' ) {
        baseUtils.show.redTip('请重新扫码!');
        return;
    }
    if (sms_code == '') {
        baseUtils.show.redTip('验证码不能为空');
        return;
    }

    $.post('/bind_wx_account',
        {
            'wx_avatar':wx_avatar,
            'wx_nickname':wx_nickname,
            'wx_open_id':wx_open_id,
            'sms_code':sms_code
        },
        function(data){
            if(data.code == 0){
                window.location.href = "/apply_withdraw_page";
            }else{
                baseUtils.show.redTip(data.msg);
            }
        });
}
var send_sms_flag = true;
function sendsms() {

    if(send_sms_flag) {
        register.sendCoder();
    }else{
        return false;
    }
}
var timer;
var register = {
    count: 60,
    clear: null,
    flag: false,
    loginFlag: false,
    coderTick: function ($phone) {
        //倒计时
        timer = setInterval(register.tick, 1000);
    },
    tick: function () {
        var $coder = $('#get_sms_code');
        //util.layer($phone);
        if (register.count == 0) {
            clearInterval(timer);
            register.count = 60;
            $coder.html('获取验证码');
            $("#get_sms_code").removeClass("disabled");
            // send_sms_flag = true;
            register.flag = false;
            $coder.click(function () {
                register.sendCoder();
            });
        } else {
            register.count--;
            $coder.unbind('click').html(register.count + 's后重新发送');
            $("#get_sms_code").addClass("disabled");
            register.flag = true;
            send_sms_flag = false;
        }
    },
    sendCoder: function (phone) {
        var param = {};
        //1:短信
        param.code_type = 6;
        param.phone = phone;
        //防止多次点击时，多次提交请求
        if (!register.flag) {
            register.flag = true;
            //发送验证码
            $.get('/send_sms',  function (data) {
                if (data.code == 0) {
                    register.coderTick();
                } else {
                    baseUtils.show.redTip("验证码发送失败,请重试!");
                    register.flag = false;
                }
            });
        }
    }

}