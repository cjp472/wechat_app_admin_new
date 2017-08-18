/**
 * Created by jserk on 2017/6/26.
 */
$(function () {
    changePassword.init();
});

var changePassword=(function () {
    var changePassword={};

    var phone = $("#phone").text(), //手机号
        passwordFirst, //第一次输入密码
        passwordSecond, //第二次输入密码
        checkCode,  //手机验证码
        sendMsg=1,    //是否可获取验证码
        codeCorrect=0; //验证码是否正确

    function count() {
        var seconds=60;
        sendMsg=0;
        var round=setInterval(function () {
             if(seconds>0){
                 $(".getCodeBtn").text(seconds+'秒');
                 seconds--;
             }else{
                 $(".getCodeBtn").removeClass('preventClickBtn');
                 $(".getCodeBtn").addClass('btnBlue');
                 $(".getCodeBtn").text('再次获取');
                 sendMsg=1;
                 clearInterval(round);
             }
        },1000)
    }

    changePassword.init=function () {

        //获取验证码
        $(".getCodeBtn").click(function () {
            if(sendMsg){
                $(this).addClass('preventClickBtn');
                count();
            $.get("/sendmsg", { "phone": phone }, function(data) {
                if (data.ret == 0) {
                } else {
                }
            });
            }
        })

        //校验验证码
        $(".identifyCodeInput").keyup(function() {
            checkCode = $(".identifyCodeInput").val();

            if (checkCode.length == 0) {
                $(".successTip,.errorTip").show();
                $(".identifyCodeInput").removeClass('borderRed');
                return;
            }
            $.get('/identify', { "phoneInIdentify": phone, "code": checkCode },
                function(data) {
                    if (data.ret == 0) {
                        $(".successTip").show();
                        $(".errorTip").hide();
                        $(".identifyCodeInput").removeClass('borderRed');
                        codeCorrect=1;
                    } else {
                        $(".successTip").hide();
                        $(".errorTip").show();
                        $(".identifyCodeInput").addClass('borderRed');
                        codeCorrect=0;
                    }
                });
        });

        //输入密码
        $(".passwordInput").keyup(function() {
            //第一次输入密码
            passwordFirst=$(".passwordInput").val();
            //再次输入密码
            passwordSecond=$(".passwordInputAgain").val();
            if(!$formCheck.checkPassword(passwordFirst)){
                $(this).addClass('borderRed');
                $(".errorMsgFirst").fadeIn(300);
            }else{
                $(this).removeClass('borderRed');
                $(".errorMsgFirst").fadeOut(300);
            }
            if(passwordSecond.length!=0&&passwordSecond!=passwordFirst){
                $(".passwordInputAgain").addClass('borderRed');
                $(".errorMsgSecond").fadeIn(300);
            }else{
                $(".passwordInputAgain").removeClass('borderRed');
                $(".errorMsgSecond").fadeOut(300);
            }

        });

        $(".passwordInputAgain").keyup(function() {
            //第一次输入密码
            passwordFirst=$(".passwordInput").val();
            //再次输入密码
            passwordSecond=$(".passwordInputAgain").val();
            if(passwordSecond.length!=0&&passwordSecond!=passwordFirst){
                $(this).addClass('borderRed');
                $(".errorMsgSecond").fadeIn(300);
            }else{
                $(this).removeClass('borderRed');
                $(".errorMsgSecond").fadeOut(300);
            }
        });
    //    保存
        $(".confirmBtn").click(function () {
            phone = $("#phone").text();
            console.log(phone);
            //验证码
            checkCode = $(".identifyCodeInput").val();
            //第一次输入密码
            passwordFirst = $(".passwordInput").val();
            //第二次输入密码
            passwordSecond = $(".passwordInputAgain").val();

            if (checkCode == '') {
                baseUtils.show.redTip('请输入验证码');
                return false;
            }
            if (codeCorrect == 0) {
                baseUtils.show.redTip('验证码错误，请重新输入');
                return false;
            }
            if (codeCorrect == 0) {
                baseUtils.show.redTip('验证码错误，请重新输入');
                return false;
            }
            if (passwordFirst.length == 0 || passwordSecond.length == 0) {
                baseUtils.show.redTip('密码不能为空，请输入密码');
                return false;
            }

            if (passwordFirst.length < 6||passwordFirst.length > 16) {
                baseUtils.show.redTip('密码长度必须在6位到16位之间哦~');
                return false;
            }

            if(!$formCheck.checkPassword){
                baseUtils.show.redTip('密码格式不正确，请重新输入~');
                return false;
            }
            if (passwordFirst != passwordSecond) {
                baseUtils.show.redTip('两次密码输入不一致，请重新输入');
                return false;
            }
            $.ajax('/admin/addAdminAccount?only_password=1', {
                type: 'POST',
                dataType: 'json',
                data: {
                    password: passwordFirst,
                    phone:phone,
                    identify_code:checkCode
                },
                success: function (data) {
                    if (data.code == 0) {
                        baseUtils.show.blueTip('修改密码成功！');
                        location.href='/accountmanage';
                    } else {
                        baseUtils.show.blueTip('网络错误，请稍后再试');
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试");
                }
            })
        })
    //    取消
        $(".cancelBtn").click(function () {
            location.href='/accountmanage';
        })
    };
    return changePassword;
})();