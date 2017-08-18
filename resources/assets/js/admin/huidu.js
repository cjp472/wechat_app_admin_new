/**
 * Created by Administrator on 2017/2/24.
 */


$(document).ready(function () {

    $(".query_btn").click(function () {
        var phone = $(".input_phone").val();
        $.post("/query_account_by_phone", {'phone': phone}, function (data) {
            if (data.code == 0) {       //  成功
                baseUtils.show.blueTip("查询成功！");

                if (data.data["name"]) {
                    $(".account_detail_nick_name").html("昵称:" + data.data["name"]);
                }
                if (data.data["phone"]) {
                    $(".account_detail_phone").html("联系电话:" + data.data["phone"]);
                }
                if (data.data["company"]) {
                    $(".account_detail_company").html("公司:" + data.data["company"]);
                }
                if (data.data["use_collection"]) {
                    var model = "";
                    if (data.data["use_collection"] == 0 ) {
                        model = '企业模式';
                    } else if (data.data["use_collection"] == 1 ) {
                        model = '个人模式';
                    } else if (data.data["use_collection"] == 521 ) {
                        model = '灰度模式';
                    }
                    $(".account_model").html("当前账户模式:" +  model);
                }
                if (data.data["wx_app_id"]) {
                    var is_wx_app_id = "否";
                    if (data.data["wx_app_id"] !== null && data.data["wx_app_id"] !== undefined
                        && data.data["wx_app_id"] !== '') {
                        is_wx_app_id = "是";
                    }
                    $(".account_wechat").html("绑定服务号:" + is_wx_app_id);
                }
                if (data.data["wx_mchid"]) {
                    var is_mchid = '否';
                    if (data.data["wx_mchid"] !== null && data.data["wx_mchid"] !== undefined
                        && data.data["wx_mchid"] !== '') {
                        is_mchid = "是";
                    }
                    $(".account_mechant").html("保存商户信息:" + is_mchid);
                }
            } else {
                baseUtils.show.redTip("查询失败！");
                baseUtils.show.redTip(data.msg);

                $(".account_detail_nick_name").html("昵称:");
                $(".account_detail_phone").html("联系电话:");
                $(".account_detail_company").html("公司:");
                $(".account_model").html("当前账户模式:");
                $(".account_wechat").html("绑定服务号:");
                $(".account_mechant").html("保存商户信息:");
            }
        });

    });
    $(".set_huidu_btn").click(function () {
        var phone = $(".input_phone").val();
        $.post("/set_huidu_by_phone", {'phone': phone}, function (data) {
            if (data.code == 0) {
                baseUtils.show.blueTip("设置个人模式成功！");
            } else {
                baseUtils.show.redTip("设置个人模式失败！");
                baseUtils.show.redTip(data.msg);
            }
        });

    });
    $(".delete_account_btn").click(function () {
        var phone = $(".input_phone").val();
        showLoading();
        // $.post("/delete_account_by_phone", {'phone': phone}, function (data) {
        //     hideLoading();
        //     if (data.code == 0) {
        //         baseUtils.show.blueTip("删除账户成功！");
        //     } else {
        //         baseUtils.show.redTip("删除账户失败");
        //         baseUtils.show.redTip(data.msg);
        //     }
        // });

        $.ajax("/delete_account_by_phone",{
            type: "POST",
            dataType: "json",
            data: {'phone': phone},
            success: function (data) {
                hideLoading();
                if (data.code == 0) {
                    baseUtils.show.blueTip("删除账户成功！");
                } else {
                    baseUtils.show.redTip("删除账户失败");
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function (xhr, status, err) {
                hideLoading();
                console.error(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });

    });


});

