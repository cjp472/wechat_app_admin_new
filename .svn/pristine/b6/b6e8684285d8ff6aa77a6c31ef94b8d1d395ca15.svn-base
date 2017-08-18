/**
 * Created by Jervis on 2017/6/28.
 */

$(function(){
    init();
});

function init(){
    bindInit();
    stepChange();
    newAdmin();




    function bindInit(){
        //生成二维码
        // var qrcode_href='https://admin.inside.xiaoe-tech.com/css/admin/wechatCode.css';
        var versionType = GetQueryString("version_type");
        try {
            var obj = new WxLogin({
            id: "change_qrCode",
            appid: qrcode_app_id,
            scope: "snsapi_login",
            redirect_uri: encodeURI(new_redirect_url + "?version_type=" + versionType),
            state: "",
            style: "black",
            href: qrcode_href
        });
            console.log(new_redirect_url + "?version_type=" + versionType);
        } catch (ex) {
            console.log(ex);
        }
    }

    function stepChange(){
        var type=GetQueryString('type');
        var wx_is_used=GetQueryString('wx_is_used');
        var scan_error=GetQueryString('scan_error');

        console.log(type);
        if(type == 1){
            $('#step_2').hide();
        }else if(type == 2){
            $('#itemTwo').addClass('finStep');
            $('.qrCode').hide();
            $('#step_2').show();
            $('#step_3').hide();

        }else if(type == 3){
            $('#itemTwo').addClass('finStep');
            $('#itemThree').addClass('finStep');
            $('.qrCode').hide();
            $('#step_2').hide();
            $('#step_3').show();
            finishJump();
        }
        if(wx_is_used ==1){
           $('#hasBind').show();
        }
    }
    function newAdmin() {
        var checkCode;  //手机验证码
        var sendMsg = 1;    //是否可获取验证码
        var codeCorrect = 0; //验证码是否正确
        var phone; //手机号
        var name;  //联系人
        function count() {
            var seconds = 60;
            sendMsg = 0;
            var round = setInterval(function () {
                if (seconds > 0) {
                    $(".getCodeBtn").text(seconds + '秒');
                    seconds--;
                } else {
                    $(".getCodeBtn").removeClass('preventClickBtn');
                    $(".getCodeBtn").addClass('btnBlue');
                    $(".getCodeBtn").text('再次获取');
                    sendMsg = 1;
                    clearInterval(round);
                }
            }, 1000)
        }
        $('#phone').keyup(function(){
            phone = $('#phone').val();
            if(!$formCheck.checkPhone(phone)){
                $("#phone").addClass('borderRed');
                $('#phoneBox').show();
            }else{
                $("#phone").removeClass('borderRed');
                $('#phoneBox').hide();
            }
        })
        //获取验证码
        $(".getCodeBtn").click(function () {
            phone = $('#phone').val();
            if($formCheck.checkPhone(phone)){
                console.log(phone);
                if (sendMsg) {
                    $(".getCodeBtn").removeClass('btnBlue');
                    $(this).addClass('preventClickBtn');
                    count();
                    $.get("/sendmsg", {"phone": phone}, function (data) {
                        if (data.ret == 0) {
                        } else {
                        }
                    });
                }
            }else{
                baseUtils.show.redTip('请输入正确的手机号');
            }
        })

        //校验验证码
        $(".identifyCodeInput").keyup(function () {
            checkCode = $(".identifyCodeInput").val();

            if (checkCode.length == 0) {
                $(".successTip,.errorTip").show();
                $(".identifyCodeInput").removeClass('borderRed');
                return;
            }
            $.get('/identify', {"phoneInIdentify": phone, "code": checkCode},
                function (data) {
                    if (data.ret == 0) {
                        $(".successTip").show();
                        $(".errorTip").hide();
                        $(".identifyCodeInput").removeClass('borderRed');
                        codeCorrect = 1;
                    } else {
                        $(".successTip").hide();
                        $(".errorTip").show();
                        $(".identifyCodeInput").addClass('borderRed');
                        codeCorrect = 0;
                    }
                });
        });
        //验证验证码，确认点击事件

        // $('#phoneCheck .btnBlue').on('click',function(){
        //     if(codeCorrect == 1){
        //         $(".identifyCodeInput").val('');
        //         window.location.href='/changeAdmin';
        //     }else if(codeCorrect == 0){
        //         // baseUtils.show.redTip('您验证码未通过');
        //     }
        // });

        //save 保存按钮ajax
        $('#save').on('click',function(){
            name = $('#linkman').val();
            phone = $('#phone').val();
            if(codeCorrect == 1) {
                $.ajax('/admin/changePhone', {
                    type: 'POST',
                    data: {
                        phone: phone,
                        contacts: name
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.code == 0) {
                            $(".identifyCodeInput").val('');
                            // window.location.href='/changeAdmin?type=3';
                            $('#itemTwo').addClass('finStep');
                            $('#itemThree').addClass('finStep');
                            $('.qrCode').hide();
                            $('#step_2').hide();
                            $('#step_3').show();
                            finishJump();
                        } else {
                            baseUtils.show.redTip(data.msg)
                        }
                    },
                    error: function (xhr, status, err) {
                        console.log(xhr);
                        console.error(err);
                        console.error(status);
                        baseUtils.show.redTip('网络错误，请稍后再试！');
                    }
                })
            }else{
                baseUtils.show.redTip('请输入验证码')
            }
        })
    }

    function finishJump(){
        //数字跳转定时器
        var seconds=2;
        var jump=setInterval(function(){
            if(seconds >= 0){
                $('.secondJump').text(seconds);
                seconds--;
            }else{
                loginOut();
                clearInterval(jump);
            }
        },1000)

        //立即跳转
        $("#jumpNow").click(function () {
            loginOut();
        });
        function loginOut(){
            $.get('/loginout', function (result) {
                var code = result.ret;
                if (code == "0") {
                    window.location = "/login";
                }
                else {
                    baseUtils.show.redTip("错误")
                    return;
                }
            });
        }
    }

}