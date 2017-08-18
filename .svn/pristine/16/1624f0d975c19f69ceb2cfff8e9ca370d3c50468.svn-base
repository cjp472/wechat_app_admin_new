/**
 * Created by Administrator on 2017/5/2.
 */

$(document).ready(function () {

    dynamicList.init();
})

var dynamicList = (function () {

    var dynamicList = {};

    dynamicList.communityId = -1;      //社群id
    dynamicList.count_notices = -1;      //群公告数量

    dynamicList.init = function () {

        //  初始化公共数据
        dynamicList.communityId = GetQueryString("community_id");
        dynamicList.count_notices = $("#admin_data").data("count_notices");

        // 搜索 + 筛选
        $("#searchDynamic").click(function () {

            var dynamicType = $("#dynamicTypeSelector").val();
            var DynamicName = $("#dynamicNameInput").val();

            window.location.href = "/smallCommunity/dynamicList?community_id=" + dynamicList.communityId + "&search_content=" + DynamicName + "&state=" + dynamicType;

        });

        //  回车触发搜索
        $("#dynamicNameInput").on("keypress", function (e) {
            if (e.keyCode == 13) {
                $("#searchDynamic").click();
            }
        });

        //  点击筛选触发搜索
        $("#dynamicTypeSelector").on("change", function () {
            $("#searchDynamic").click();
        });

        //  新建一个动态
        $("#createDynamic").click(function () {

            $.ajax("/smallCommunity/queryCommunityRoomer", {
                type: "POST",
                dataType: "json",
                data: {
                    community_id: dynamicList.communityId
                },
                success: function (result) {
                    if (result.code == 0) {
                        window.location.href = "/smallCommunity/createDynamic?community_id=" + dynamicList.communityId;
                    } else {
                        $.alert("还没有设置群主，暂时不能新建一个动态", "info", {
                            btn: 2
                        });
                        console.log(result.msg);
                    }
                },
                error: function (xhr, status, err) {
                    $.alert("服务器出小差了，暂时不能新建一个动态", "info", {
                        btn: 2
                    });
                    console.log(err);
                }
             });

        });

        //  造作更多选项
        $(".moreBtn ul li").click(function () {
            var community_id = $(this).parents(".dynamicItem").data("community_id");
            var dynamic_id = $(this).parents(".dynamicItem").data("dynamic_id");
            var sendType = $(this).parents(".dynamicItem").data("send_type");

            var type = $(this).data("type");
            switch (type) {
                case "edit_dynamic":
                    if (sendType == 0) {
                        baseUtils.show.redTip("非管理台创建的动态不能编辑");
                        return false;
                    }
                    window.location.href = "/smallCommunity/editDynamic?id=" + dynamic_id;
                    break;
                case "move_in":     //移入精选
                    changeDynamicState({
                        id: dynamic_id,
                        is_chosen: 1
                    });
                    break;
                case "move_out":    //移出精选
                    changeDynamicState({
                        id: dynamic_id,
                        is_chosen: 0
                    });
                    break;
                case "set_group_notice":        //设为群公告
                    if (dynamicList.count_notices >= 2) {
                        $.alert("已达到群公告上限2条，请先撤下之前的群公告", "error", {
                            btn: 2,
                            title: "提示"
                        });
                        return false;
                    }
                    changeDynamicState({
                        id: dynamic_id,
                        is_notice: 1
                    }, "群公告设置成功，将在小社群主页置顶显示");
                    break;
                case "cancel_group_notice":     //取消群公告
                    changeDynamicState({
                        id: dynamic_id,
                        is_notice: 0
                    }, "群公告已撤，将不再置顶显示");
                    break;
                case "delete_dynamic":  //删除
                    $.alert("确认删除此动态", "error", {
                        btn: 3,
                        title: "提示",
                        oktext: "删除",
                        onOk: function () {
                            changeDynamicState({
                                id: dynamic_id,
                                feeds_state: 2
                            });
                        }
                    });
                    break;
                default:
                    console.log("参数错误");
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


    return dynamicList;

})();
















