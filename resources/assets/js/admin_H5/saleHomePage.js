/**
 * Created by Administrator on 2017/2/14.
 */

var params = {};
var allParams = {};
// 是否已经申请分销
var is_applied;
//  是否修改手机号
var is_change_phone;

/**
 * 网页加载完毕触发
 */
$(document).ready(function () {
    $frame.init();

    $searchContent.startSearch();

});

//框架逻辑
$frame = {

    init: function () {

        is_applied = $(".submit_application_page").data("is_applied");
        if (is_applied == "1") {    //  已经申请过
            is_applied = true;
            is_change_phone = 0;
        } else {
            is_applied = false;
            is_change_phone = 1;
        }

        /*底部tab 搜索栏*/
        $("#tab_item_search").click(function () {
            $("#tab_item_search").removeClass("searchOff");
            $("#tab_item_search").addClass("searchOn");

            $("#tab_item_mine").removeClass("mineOn");
            $("#tab_item_mine").addClass("mineOff");

            window.location.href = "saleHomePage";
        })
        /*底部tab 我的分销栏*/
        $("#tab_item_mine").click(function () {
            $("#tab_item_mine").removeClass("mineOff");
            $("#tab_item_mine").addClass("mineOn");

            $("#tab_item_search").removeClass("searchOn");
            $("#tab_item_search").addClass("searchOff");

            window.location.href = "/getSaleList";
        })

        /*点击申请表顶部*/
        $(".transparent_top_area").click(function () {
            $submitContent.clickTopArea();
        });

        /*监听搜索栏输入事件*/
        $('.sale_content_search').bind('input propertychange', function() {
            $searchContent.startSearch();
        });

        /*修改手机号*/
        $(".change_phone_number").click(function () {
            $submitContent.changePhoneNumber();
        })

        /*获取验证码*/
        $(".acquire_verify_code").click(function () {
            $submitContent.acquireVerifyCode();
        })

        /*提交申请*/
        $(".submit").click(function () {
            $submitContent.submitApplication();
        })
    }

};

/*搜索内容部分*/
$searchContent = {

    /*点击搜按钮 触发搜索*/
    startSearch: function() {
        var contentSearch = $.trim($('input.sale_content_search').val());
        if (contentSearch != '') {
            $.post("/saleSearchContent", {'contentSearch':contentSearch}, function (data) {
                $('.sale_content_title').addClass('hide');
                $('.search_suggestion').hide();

                if (data.code == 0) {
                    $(".search_no_data").addClass("hide");
                    $(".search_result_title").removeClass("hide");

                    $('.search_result').show();
                    $(".search_area").removeClass("hide");
                    $(".search_area").html(data.data);
                }else{
                    $(".search_no_data").removeClass("hide");
                    $(".search_result_title").addClass("hide");
                    $(".search_area").addClass("hide");


                }
            });

        }
    },

    // 申请主页分销
    applyMainPageSale: function ($btn) {
        params["sale_type"] = 1;

        params["app_id"] = $($btn).data("app_id");
        params["app_name"] = $($btn).data("app_name");

        params["sale_img_url"] = $($btn).data("app_image");
        params["sale_id"] = "";
        params["sale_content"] = $($btn).data("app_name");


        $(".submit_application_page").css({"display":"block"});
        $(".tab_selector").hide();

    },

    //  申请专栏分销
    applyProductSale: function ($btn) {
        params["sale_type"] = 0;

        params["app_name"] = $($btn).data("app_name");        ////分销所属的app_name
        params["app_id"] = $($btn).data("app_id");           //分销所属的app_id

        params["sale_img_url"] = $($btn).data("package_image");           //分销专栏封面
        params["sale_id"] = $($btn).data("product_id");          //分销id,sale_type为0时是专栏id，sale_type为1时是空
        params["sale_content"] = $($btn).data("product_name");   //分销内容,sale_type为0时是专栏名称，sale_type为1时是公众号名称

        $(".submit_application_page").css({"display":"block"});
        $(".tab_selector").hide();

    },


}

$submitContent = {

    clickTopArea: function () {
        $(".submit_application_page").css({"display": "none"});
        $(".error_remind").html("");
        $(".tab_selector").show();

        /*清空数据*/
        $(".sale_name").val("");            //自定义的分销名称
        $(".remarks").val("");              //备注
        $(".verify_code").val("");          //验证码

    },

    changePhoneNumber: function () {
        is_change_phone = 1;
        $(".verify_code_wrapper").removeClass("hide");
        $(".phone_number").removeAttr("readonly");
        $(".phone_number").removeAttr("type");

        $(".change_phone_number").addClass("hide");
    },

    submitApplication: function () {

        params["sale_name"] = $(".sale_name").val();        //自定义的分销名称
        params["applier"] = $(".real_user_name").val();     //申请人
        params["phone"] = $(".phone_number").val();         //用户的手机号码
        params["remark"] = $(".remarks").val();             //备注

        var verifyCode = $(".verify_code").val();           //验证码

        if (params["sale_name"] == "") {
            $(".error_remind").html("分销名称不能为空！");
            return false;
        }
        if (params["applier"] == "") {
            $(".error_remind").html("真实姓名不能为空！");
            return false;
        }
        if (params["phone"] == "") {
            $(".error_remind").html("手机号码不能为空！");
            return false;
        } else if (is_change_phone == 1 && verifyCode == "") {
            $(".error_remind").html("验证码不能为空！");
            return false;
        }
        $(".error_remind").html("");

        allParams["params"] = params;
        allParams["is_change_phone"] = is_change_phone;
        allParams["code"] = verifyCode;


        /*开始提交*/
        $.post("/applySale",  allParams, function (data) {
            if (data.code == 0) {   //  成功
                window.location.href = "/submitSuccess";
            } else {
                $(".error_remind").html(data.msg);
                /*弹框提示*/
                // alert(data.msg);
            }

        });



    },

}

//发送验证码
var send_sms_flag = true;
var phone = '';
function sendsms() {

    phone = $(".phone_number").val();

    if(send_sms_flag) {
        register.sendCoder(phone);
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
    coderTick: function (phone) {
        //倒计时
        timer = setInterval(register.tick, 1000);
    },
    tick: function () {
        var $coder = $('.acquire_verify_code');
        //util.layer($phone);
        if (register.count == 0) {
            clearInterval(timer);
            register.count = 60;
            $coder.html('获取验证码');
            $(".acquire_verify_code").removeClass("disabled");
            // send_sms_flag = true;
            register.flag = false;
            $coder.click(function () {
                register.sendCoder();
            });
        } else {
            register.count--;
            $coder.unbind('click').html(register.count + 's后重新发送');
            $(".acquire_verify_code").addClass("disabled");
            register.flag = true;
            send_sms_flag = false;
        }
    },
    sendCoder: function (phone) {
        // var param = {};
        //1:短信
        // param.code_type = 6;
        // param.phone = phone;
        //防止多次点击时，多次提交请求
        if (!register.flag) {
            register.flag = true;
            //发送验证码
            $.post('/send_apply_sms',{'phone':phone},  function (data) {
                if (data.code == 0) {
                    register.coderTick();
                } else {
                    alert("验证码发送失败,请重试!");
                    register.flag = false;
                }
            });
        }
    }

}


