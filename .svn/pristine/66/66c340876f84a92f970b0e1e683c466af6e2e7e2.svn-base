/**
 * Created by Stuphin on 2016/9/26.
 */

$(function(){
    init();
    phoneCodeInit();

});



function init(){
    //  Content区域小标题
    /*showContentTitle();
     if (is_huidu == 1) {
     appendContentHeader("账户一览", "/accountview", false);
     }
     appendContentHeader("账号管理", "/accountmanage", true);
     appendContentHeader("运营模式设置", "/personmodel", false);*/
    // appendContentHeader("小程序设置", "/smallprogramsetting", false);
    var addName;//用户名
    var addPassword;//第一次输入密码
    var rePassword;//重复输入
    var editOldPassword;//旧密码
    var editNewPassword;//新密码
    var reEditNew;//重复新密码
    var hasName;//用户名重复判定键值  0无重复 1重复


    //新增页面
    $("#addAdminUser").click(function () {
        window.location.href = "/admin/child/add";
    });


    //新增账号时检测账号唯一性
    $("#addName").keyup(function () {
        $("#nameExist").hide();
        $('#nameMsg').hide();
        var addName = $("#addName").val().trim();
        console.log(addName);
        if(!$formCheck.checkAccount(addName)){
            $("#addName").addClass('borderRed');
            $('#nameMsg').show();
        }else{
            $("#addName").removeClass('borderRed');
            $('#nameMsg').hide();
        }
        if (addName.length == 0) {
            return;
        }
        else {
            // console.log(addName);
            $.post("/admin/isAcountRepeat", {"name": addName}, function (data) {
                if (data.ret == 0) {
                    hasName = 0;
                    $(".checkImg").eq(0).removeClass("hide");
                    $(".checkImg").eq(1).addClass("hide");
                    $("#nameExist").hide();
                    $("#addPrimarySubmit").attr("disabled", false);
                }
                else {
                    hasName = 1;
                    $(".checkImg").eq(1).removeClass("hide");
                    $(".checkImg").eq(0).addClass("hide");
                    $("#nameExist").show();
                    $('#nameMsg').hide();
                    $("#addPrimarySubmit").attr("disabled", true);
                }
            });
        }
    });

    $('#addPassword').keyup(function(){
        addPassword = $("#addPassword").val().trim();
        console.log(addPassword);
        if(!$formCheck.checkPassword(addPassword)){
            $("#addPassword").addClass('borderRed');
            $('#addPsd').show();
        }else{
            $("#addPassword").removeClass('borderRed');
            $('#addPsd').hide();
        }
        if(rePassword != addPassword && rePassword.length !=0){
            $("#addConfirm").addClass('borderRed');
            $('#rePsd').show();
        }else{
            $("#addConfirm").removeClass('borderRed');
            $('#rePsd').hide();
        }
    });

    $('#addConfirm').keyup(function(){
        rePassword = $("#addConfirm").val().trim();
        if(rePassword != addPassword && rePassword.length !=0){
            $("#addConfirm").addClass('borderRed');
            $('#rePsd').show();
        }else{
            $("#addConfirm").removeClass('borderRed');
            $('#rePsd').hide();
        }
    });
    //新增管理员账号
    $("#addSubmit").click(function () {
        addName = $("#addName").val().trim();
        addPassword = $("#addPassword").val().trim();
        if(hasName == 1){
            baseUtils.show.redTip('用户名已存在');
            return false;
        }

        if (!$formCheck.checkAccount(addName)){
            baseUtils.show.redTip("账号格式错误，6~18位字符，只能包含字母、数字、下划线");
            return false;
        }

        if (!$formCheck.checkPassword(addPassword)) {
            baseUtils.show.redTip("密码格式错误，6-16位字符可包含数字，字母（区分大小写）");
            return false;
        }
        if (addPassword != $("#addConfirm").val().trim()) {
            baseUtils.show.redTip("亲,两次密码不一致");
            return false;
        }
        $.post("admin/addAdminAccount", {"name": addName, "password": addPassword}, function (data) {
            if (data.code == 0) {
                baseUtils.show.blueTip("绑定成功", function () {
                    window.location.reload();
                });
            }
            else {
                baseUtils.show.redTip("绑定失败");
            }
        });
    });







    //修改管理员账号
    $('#editNewPassword').keyup(function(){
        editNewPassword = $("#editNewPassword").val().trim();
        console.log(editNewPassword);
        if(!$formCheck.checkPassword(editNewPassword)){
            $("#editNewPassword").addClass('borderRed');
            $('#reSet').show();
        }else{
            $("#editNewPassword").removeClass('borderRed');
            $('#reSet').hide();
        }
        if(reEditNew != editNewPassword && reEditNew.length !=0){
            $("#editNewConfirm").addClass('borderRed');
            $('#reSetCheck').show();
        }else{
            $("#editNewConfirm").removeClass('borderRed');
            $('#reSetCheck').hide();
        }
    });

    $('#editNewConfirm').keyup(function(){
        reEditNew = $("#editNewConfirm").val().trim();
        if(reEditNew != editNewPassword && reEditNew.length !=0){
            $("#editNewConfirm").addClass('borderRed');
            $('#reSetCheck').show();
        }else{
            $("#editNewConfirm").removeClass('borderRed');
            $('#reSetCheck').hide();
        }
    });
    $("#editSubmit").click(function () {//提交修改页面
        editOldPassword = $("#editOldPassword").val().trim();
        editNewPassword = $("#editNewPassword").val().trim();

        if (editOldPassword.length == 0) {
            baseUtils.show.redTip("亲,请输入旧的密码");
            return false;
        }

        if (!$formCheck.checkPassword(editNewPassword)) {
            baseUtils.show.redTip("密码格式错误，6-16位字符可包含数字，字母（区分大小写）");
            return false;
        }
        if (editNewPassword != reEditNew) {
            baseUtils.show.redTip("亲,两次密码输入不一致");
            return false;
        }
        $.post("/admin/addAdminAccount", {
            "enter_password": editOldPassword,
            "password": editNewPassword,"page_type":1
        }, function (data) {
            if (data.code == 0) {
                baseUtils.show.blueTip("修改成功", function () {
                    window.location.reload();
                });
            }
            else if (data.code != 0) {

                baseUtils.show.redTip(data.msg);
            }
        });
    });

    //编辑商户名
    // $('#editWxName').click(function () {
    //     $('#wxnameInput').prop({
    //         disabled: false,
    //         readonly: false
    //     }).focus();
    //     $(this).hide();
    //     $('#editContent').show();
    // });
    // $('#cancleEdit').click(exitEdit);//退出编辑
    // $('#saveEdit').click(function () {
    //     var wxName = $.trim($('#wxnameInput').val());
    //     if (wxName == '') {
    //         baseUtils.show.redTip('请输入商户名！');
    //     }
    //     $.ajax('/edit_wx_name', {
    //         type: 'GET',
    //         dataType: 'json',
    //         data: {name: wxName},
    //         success: function (json) {
    //             console.log(json);
    //             if (json.code == 1) {
    //                 baseUtils.show.blueTip('保存成功');
    //                 exitEdit();
    //             } else {
    //                 baseUtils.show.redTip(json.msg);
    //             }
    //         },
    //         error: function (err) {
    //             console.error(err);
    //             baseUtils.show.redTip('网络错误，请稍后再试！');
    //         }
    //     })
    // });




    // 弹窗相关事件
    //弹出激活
    $('#edit').on('click',function(){
       $('#editCode').fadeIn();
    });
    $('#add').on('click',function(){
        $('#loginBox').fadeIn();
    })
    $('#check').on('click',function(){
        $('#phoneCheck').fadeIn();
    })
    // 取消
    $('.closeBox').on('click',function(){
        $('.popBox').fadeOut();
        // setTimeout($('.popBox').fadeOut(200));
    })

}


function phoneCodeInit() {
    var phone = $("#phone").text(); //手机号
    var checkCode;  //手机验证码
    var sendMsg = 1;    //是否可获取验证码
    var codeCorrect = 0; //验证码是否正确

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

    //获取验证码
    $(".getCodeBtn").click(function () {
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

    $('#phoneCheck .btnBlue').on('click',function(){
       if(codeCorrect == 1){
           $(".identifyCodeInput").val('');
           window.location.href='/changeAdmin';
       }else if(codeCorrect == 0){
           // baseUtils.show.redTip('您验证码未通过');
       }
    });
}

//退出编辑(通用)
function exitEdit(){
    $('.editInput').prop({
        disabled: true,
        readonly: true
    });
    $('.editBtn').show();
    $('.editContent').hide();
}

//编辑
function editAdminUser(id)
{
    window.location.href="/admin/child/edit/"+id;
}

//删除
function deleteAdminUser(id)
{
    $.get("/admin/del/"+id,{},function(data)
    {
        if(data.code==0)
        {
            baseUtils.show.blueTip("删除成功",function()
            {
                window.location.reload();
            });
        }
        else
        {
            baseUtils.show.redTip("删除失败");
        }
    });
}




