/**
 * Created by Administrator on 2017/5/2.
 */
$(function () {
    userList.init();
})

var userList = (function () {

    var userList = {};

    userList.submitLimit = false;
    userList.communityId = -1;

    userList.init = function () {

        userList.communityId = GetQueryString("community_id");

        $(".moveInBlackList").click(function () {
            var userId = $(this).parents(".userListItem").data("user_id");
            $.alert("移入黑名单后，该用户只能查看动态，不能发表、点赞和评论。", "info", {
                btn: 2,
                onOk: function () {
                    changeUserState(userId, 2);
                }
            });
        });

        $(".moveOutBlackList").click(function () {
            var userId = $(this).parents(".userListItem").data("user_id");
            changeUserState(userId, 0);
        });

        // 搜索 + 筛选
        $("#searchUser").click(function () {

            var userType = $("#userTypeSelector").val();
            var searchContent = $("#userNameInput").val();

            window.location.href = "/smallCommunity/userList?community_id=" + userList.communityId + "&search_content=" + searchContent + "&user_type=" + userType;

        });

        //  回车触发搜索
        $("#userNameInput").on("keypress", function (e) {
            if (e.keyCode == 13) {
                $("#searchUser").click();
            }
        });

        //  点击筛选触发搜索
        $("#userTypeSelector").on("change", function () {
            $("#searchUser").click();
        });


    };

    /**
     *
     * @param userId
     * @param userState (0-移出黑名单;2-加入黑名单)
     */
    function changeUserState(userId, userState) {
        if (userList.submitLimit) {
            return false;
        }
        userList.submitLimit = true;

        $.ajax("/smallCommunity/changeUserState", {
            type: "POST",
            dataType: "json",
            data: {
                community_id: userList.communityId,
                user_id: userId,
                state: userState
            },
            success: function (result) {
                if (result.code == 0) {
                    baseUtils.show.blueTip(userState == 2 ? "加入黑名单成功" : "移出黑名单成功");
                    setTimeout(function () {
                        window.location.reload();
                    }, 700);
                } else {
                    userList.submitLimit = false;
                    console.log(result.msg);
                    baseUtils.show.redTip(userState == 2 ? "加入黑名单失败，请稍后再试" : "移出黑名单失败，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                userList.submitLimit = false;
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
         });

    };


    return userList;

})();