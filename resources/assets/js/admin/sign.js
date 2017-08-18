/**
 * Created by Stuphin on 2016/10/07.
 */
//计数
var wait = 60,
    submitFlag = true;

function count() {
    if (wait > 0) {
        $("#sendMsg").html(wait + 's后重新获取');
        wait--;
        $("#sendMsg").attr("disabled", true);
        $("#sendMsg").css({ 'cursor': 'not-allowed', 'background-color': '#b2b2b2' });
        setTimeout("count()", 1000);
    } else {
        $("#sendMsg").html("获取验证码");
        $("#sendMsg").attr("disabled", false);
        $("#sendMsg").css({ 'cursor': 'pointer', 'background-color': '#00a0e9' });
        wait = 60;
    }

}

$(document).ready(function() {
    //发送验证码
    $("#sendMsg").click(function() {
        var phoneInIdentify = $("input[name='phoneInIdentify']").val();
        if (phoneInIdentify.length == 0) {
            window.wxc.xcConfirm("请输入手机号码", "error");
            return false;
        }
        if (!(/^1[34578]\d{9}$/.test(phoneInIdentify))) {
            window.wxc.xcConfirm("请输入正确的手机号码", "error");
            return false;
        }
        $.get("/sendmsg", { "phone": phoneInIdentify }, function(data) {
            if (data.ret == 0) {
                count();
            } else {
                window.wxc.xcConfirm("系统繁忙，请稍后再试", "error");
            }
        });
    });

    //校验验证码
    $("input[name='checkCode']").keyup(function() {
        var checkCode = $("input[name='checkCode']").val();
        if (checkCode.length == 0) {
            return;
        }
        $.get('/identify', { "phoneInIdentify": $("input[name='phoneInIdentify']").val(), "code": checkCode },
            function(data) {
                if (data.ret == 0) {
                    $(".checkImg").eq(0).css({ 'display': 'block' });
                    $(".checkImg").eq(1).css({ 'display': 'none' });
                    $("#finish").attr('disabled', false);
                    $("#finish").css({ 'cursor': 'pointer' });
                } else {
                    $(".checkImg").eq(0).css({ 'display': 'none' });
                    $(".checkImg").eq(1).css({ 'display': 'block' });
                    $("#finish").attr('disabled', true);
                    $("#finish").css({ 'cursor': 'not-allowed' });
                }
            });
    });

    //认证提交
    $("#finish").click(function() {
        //获取数据
        var versionType = GetQueryString("version_type");
        var contactPerson = $("input[name='contactPerson']").val();
        var officialAccount = $("input[name='officialAccount']").val();
        var phoneInIdentify = $("input[name='phoneInIdentify']").val();
        var checkCode = $("input[name='checkCode']").val();
        var checkedFlag = $("input[type='checkbox']").is(':checked');
        //校验
        if (officialAccount.length == 0) {
            window.wxc.xcConfirm("亲，还没输入公众号哦！~", "error");
            return false;
        }
        if (contactPerson.length == 0) {
            window.wxc.xcConfirm("亲，还没输入联系人姓名哦！~", "error");
            return false;
        }
        if (phoneInIdentify.length == 0) {
            window.wxc.xcConfirm("亲，还没输入手机号码哦！~", "error");
            return false;
        }
        if (!(/^1[34578]\d{9}$/.test(phoneInIdentify))) {
            window.wxc.xcConfirm("亲，没有正常输入手机号码哦！~", "error");
            return false;
        }
        if (checkCode.length == 0) {
            window.wxc.xcConfirm("亲，还未输入验证码哦！~", "error");
            return false;
        }
        if (checkedFlag == false) {
            window.wxc.xcConfirm("亲，需要仔细阅读协议哦！~", "error");
            return false;
        }
        //提交
        if( submitFlag ){
            submitFlag = false;
            $.ajax("/identifysubmit", {
                type: 'POST',
                dataType: 'json',
                data: {
                    "officialAccount": officialAccount,
                    "contactPerson": contactPerson,
                    "phoneInIdentify": phoneInIdentify,
                    "checkCode": checkCode
                },
                success: function(data) { //ret
                    submitFlag = true;
                    if (data.ret == 0) {
                        window.wxc.xcConfirm("注册成功", "success", {
                            onOk: function() {
                                if (versionType == 1) {//基础版
                                    window.location.href = "/index?first=1&first_login=1";
                                }else if (versionType == 2) {//成长版
                                    window.location.href = "/open_growUp_version_page?first=1&first_login=1";
                                }else if (versionType == 3) {//专业版
                                    window.location.href = "/open_vip_version_page?first=1&first_login=1";
                                }else{
                                    window.location.href = "/index?first=1&first_login=1";
                                }
                            }
                        });
                    } else {
                        submitFlag = true;
                        alert("系统繁忙");
                    }
                },
                error: function(xhr, status, error) {
                    submitFlag = true;
                    console.error(error);
                    alert("系统繁忙");
                }
            });
        }

    });

    //打开协议
    $(".agree").children("span").click(function() {
        $(".seperate").css({ 'display': 'block' });
        $(".agreeModal").css({ 'display': 'block' });
    });

    //关闭协议
    $("#iAgree").click(function() {
        $(".seperate").css({ 'display': 'none' });
        $(".agreeModal").css({ 'display': 'none' });
        $("input[type='checkbox']").prop("checked", true);
    });
});

//获取地址栏参数

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}
