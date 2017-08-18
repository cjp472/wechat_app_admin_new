/**
 * Created by Administrator on 2017/5/2.
 */

$(document).ready(function () {

    dynamicDetail.init();
})

var dynamicDetail = (function () {

    var dynamicDetail = {};

    dynamicDetail.communityId = -1;      //社群id
    dynamicDetail.dynamicId = -1;        //动态id
    dynamicDetail.replyCommentState = false;        //回复评论的状态 true - 有回复被打开 false - 没有

    dynamicDetail.init = function () {

        dynamicDetail.communityId = $("#admin_data").data("community_id");
        dynamicDetail.dynamicId = $("#admin_data").data("dynamic_id");

        $("#pushCommentBtn").click(function () {    //评论动态
            var commentContent = $("#commentContent").val();

            if (commentContent == "" || commentContent == undefined) {
                baseUtils.show.redTip("评论内容不能为空");
                return false;
            }

            $.ajax("/smallCommunity/commentDynamic", {
                type: "POST",
                dataType: "json",
                data: {
                    id: dynamicDetail.dynamicId,
                    comment_content: commentContent,
                    comment_type: 0    //(0-主评论;1-附属评论)
                },
                success: function (result) {
                    if (result.code == 0) {
                        baseUtils.show.blueTip("评论成功");
                        setTimeout(function () {
                            window.location.reload();
                        }, 700);
                    } else {
                        baseUtils.show.redTip("评论失败，请稍后再试");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                }
             });

        });

        $("#praiseDynamic").click(function () {
            if ($(this).data("praise_state") == 1) {    //状态为已点赞 -> 操作：取消点赞
                changeDynamicPraiseState(0);
            } else {
                changeDynamicPraiseState(1);
            }

        });

        //  对动态的操作
        $(".detailShowWrapper ul li").click(function () {

            var type = $(this).data("type");
            switch (type) {
                case "edit_dynamic":
                    window.location.href = "/smallCommunity/editDynamic?id=" + dynamicDetail.dynamicId;
                    break;
                case "move_in":
                    changeDynamicState({
                        id: dynamicDetail.dynamicId,
                        is_chosen: 1
                    });
                    break;
                case "move_out":
                    changeDynamicState({
                        id: dynamicDetail.dynamicId,
                        is_chosen: 0
                    });
                    break;
                case "set_group_notice":
                    changeDynamicState({
                        id: dynamicDetail.dynamicId,
                        is_notice: 1
                    }, "群公告设置成功，将在小社群主页置顶显示");
                    break;
                case "cancel_group_notice":
                    changeDynamicState({
                        id: dynamicDetail.dynamicId,
                        is_notice: 0
                    }, "群公告已撤，将不再置顶显示");
                    break;
                case "delete_dynamic":
                    $.alert("确认删除此动态", "error", {
                        btn: 3,
                        title: "提示",
                        oktext: "删除",
                        onOk: function () {
                            deleteDynamic({
                                id: dynamicDetail.dynamicId,
                                feeds_state: 2
                            });
                        }
                    });
                    break;
            }

        });

        $(".replyCommentBtn").click(function () {    //回复评论
            var commentContent = $(this).prev(".replyContent").val(),
                commentId = $(this).parents(".commentReplyPart").data("comment_id");

            if (commentContent == "" || commentContent == undefined) {
                baseUtils.show.redTip("评论内容不能为空");
                return false;
            }

            $.ajax("/smallCommunity/commentDynamic", {
                type: "POST",
                dataType: "json",
                data: {
                    id: dynamicDetail.dynamicId,
                    comment_content: commentContent,
                    comment_type: 1,    //(0-主评论;1-附属评论)
                    comment_id: commentId
                },
                success: function (result) {
                    if (result.code == 0) {
                        baseUtils.show.blueTip("评论成功");
                        setTimeout(function () {
                            window.location.reload();
                        }, 700);
                    } else {
                        baseUtils.show.redTip(result.msg);
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                }
            });

        });

        $(".showReplyWindow").click(function () {   //  打开回复窗口
            var $replyArea = $(this).parents(".commentItem").next(".commentReplyPart");

            // if ($replyArea.hasClass("hide") && dynamicDetail.replyCommentState) {
            //     baseUtils.show.redTip("请先完成其它回复");
            //     return false;
            // }
            if ($(this).text() == "回复") {
                $(this).text("取消回复");
            } else {
                $(this).text("回复");
            }
            if ($replyArea.hasClass("hide")) {
                $replyArea.removeClass("hide");
                dynamicDetail.replyCommentState = true;
            } else {
                $replyArea.addClass("hide");
                dynamicDetail.replyCommentState = false;
            }

        });

        $(".deleteComment").click(function () { //  删除评论

            var commentId = $(this).parents(".commentItem").data("comment_id");

            $.ajax("/smallCommunity/deleteDynamicComment", {
                type: "POST",
                dataType: "json",
                data: {
                    id: commentId
                },
                success: function (result) {
                    if (result.code == 0) {
                        baseUtils.show.blueTip("删除成功");
                        setTimeout(function () {
                            window.location.reload();
                        }, 700);
                    } else {
                        baseUtils.show.redTip("删除失败，请稍后再试");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                }
             });

        });


    };

    function changeDynamicPraiseState(state) {

        $.ajax("/smallCommunity/dynamicPraise", {
            type: "POST",
            dataType: "json",
            data: {
                id: dynamicDetail.dynamicId,
                state: state
            },
            success: function (result) {
                if (result.code == 0) {
                    var num = parseInt($("#dynamicPraiseNum").text());
                    if (state == 0) {   //操作：取消点赞
                        $("#praiseDynamic").attr("src", "../images/admin/communityOperate/praise_icon.png");
                        $("#dynamicPraiseNum").text(num - 1);
                        $("#praiseDynamic").data("praise_state", 0);
                    } else {
                        $("#praiseDynamic").attr("src", "../images/admin/communityOperate/praise_pre_icon.png");
                        $("#dynamicPraiseNum").text(num + 1);
                        $("#praiseDynamic").data("praise_state", 1);
                    }
                } else {
                    baseUtils.show.redTip(result.msg);
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
         });

    };

    /**
     * 1-id(社群动态id)
     * 2-is_chosen 0：普通状态  1：精选状态
     * 3-feeds_state  0：可见  1：隐藏  2：删除
     * 4-is_notice 0：普通状态  1：公告状态
     */
    function changeDynamicState(sendData, promptText) {

        $.ajax("/smallCommunity/changeDynamicState", {
            type: "POST",
            dataType: "json",
            data: sendData,
            success: function (result) {
                if (result.code == 0) {
                    baseUtils.show.blueTip(promptText || "操作成功");
                    setTimeout(function () {
                        window.location.reload();
                    }, 700);
                } else {
                    console.log(result.msg);
                    baseUtils.show.redTip("操作失败，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });

    };
    function deleteDynamic(sendData) {

        $.ajax("/smallCommunity/changeDynamicState", {
            type: "POST",
            dataType: "json",
            data: sendData,
            success: function (result) {
                if (result.code == 0) {
                    baseUtils.show.blueTip("删除成功");
                    setTimeout(function () {
                        window.location.href = "/smallCommunity/dynamicList?community_id=" + dynamicDetail.communityId;
                    }, 700);
                } else {
                    console.log(result.msg);
                    baseUtils.show.redTip("操作失败，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });

    };



    return dynamicDetail;

})();

















