/**
 * Created by Administrator on 2017/4/7.
 */

$(document).ready(function () {
    Business.init();

});


var Business = (function () {

    var Business = {};

    Business.init = function () {
        //  评论互动
        $(".comment").click(function () {
            window.location.href = "/comment_admin";
        });

        //  活动管理
        $(".activity").click(function () {
            var user_version_num = $(".user_version_num").data("version_type");
            if (user_version_num == 1) {
                baseUtils.show.redTip("当前版本不支持活动管理，如需开启请升级至成长版或专业版");

                return false;
            }
            window.location.href = "/activityManage";
        });

        //  作业本
        $(".exerciseBook").click(function () {
            var user_version_num = $(".user_version_num").data("version_type");
            if (user_version_num == 1 || user_version_num == 2) {
                baseUtils.show.redTip("当前版本不支持作业本，如需开启请升级至专业版");

                return false;
            }else{
                window.location.href = "/exercise/exercise_book_list";
            }
        });

        //  小社群
        $(".smallCommunity").click(function () {
            window.location.href = "/smallCommunity/communityList";
        });



        //  问答
        $(".askAndQuestion").click(function () {

            var user_version_num = $(".user_version_num").data("version_type");
            if (user_version_num == 1) {
                baseUtils.show.redTip("当前版本不支持付费问答，如需开启请升级至成长版或专业版");

                return false;
            }

            $.ajax("/QA/isHaveQA", {
                type: "GET",
                dataType: "json",
                data: {

                },
                success: function (data) {
                    if (data.code == 0) { // 已经有问答 则跳往详情
                        window.location.href = "/QA/questionAndAnswerDetail";
                    } else if(data.code == 1){ // 还没有问答 则跳往创建
                        window.location.href = "/QA/createQuestionAndAnswer";
                     }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                }
            });

            // var type = $("#admin_data").data("question_answer_type");
            // if (type == 1) {
            //
            // } else {
            //     window.location.href = "/QA/questionAndAnswerDetail";
            // }
        });


    };

    return Business;

})();
