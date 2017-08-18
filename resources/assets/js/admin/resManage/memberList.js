/**
 * Created by Administrator on 2017/3/21.
 */


$(document).ready(function () {
    Business.init();

});

var Business = (function () {

    var Business = {};

    Business.searchContent = "";
    Business.state = 0;
    Business.is_distribute = -1;
    Business.init = function () {

        //  从当前URL中取出参数
        Business.state = GetQueryString("state") || 0;
        Business.searchContent = GetQueryString("search_content") || "";
        // Business.is_distribute = GetQueryString("is_distribute") || -1;
        //  创建会员
        $(".createMemberBtn").click(function () {
            window.location.href = "/create_member_page";
        });

        //  初始化搜索模块
        modal.initSearchPart();


        //  详情页
        $(".member_list_content").on("click", ".member_list_item", function () {
            var member_id = $(this).data("member_id");
            window.location.href = "/member_detail_page?id=" + member_id;
        });

        //  操作
        $(".member_list_content").on("click", ".toolBox li.operate", function (e) {
            e.stopPropagation();    //  阻止事件向下传递

            var member_id = $(this).parents(".member_list_item").data("member_id");

            var type = $(this).data("type");
            switch (type) {
                case "edit":
                    //  进入编辑页面
                    window.location.href = "/edit_member_page?id=" + member_id + "&page_origin=member_list";
                    break;

                case "to_up":
                    //  上移
                    modal.changeMemberWeight(member_id, 0);
                    break;

                case "to_down":
                    //  下移
                    modal.changeMemberWeight(member_id, 1);
                    break;

                case "show_member":
                    //  上架
                    modal.changeMemberState(member_id, 0);
                    break;

                case "hide_member":
                    //  下架
                    modal.changeMemberState(member_id, 1);
                    break;

                default:
                    break;
            }

        });




    };

    return Business;

})();

var modal = (function () {

    var modal = {};

    /**
     * 模块一：会员列表页面的搜索功能初始化
     */
    modal.initSearchPart = function () {

        //  1、开始搜索 - 全部单品
        $(".searchAllBtn").click(function () {
            var state = $("#selector").val();
            var search_content = $.trim($(".inputSearchAll").val());
            // var is_distribute = $("#selector_distribute").val();
            window.location.href = "/member_list_page?state=" + state + "&search_content=" + search_content;

        });

        // 2、输入完成后，回车开始搜索 - 全部会员
        $(".inputSearchAll").focusin(function () {
            $(document).keypress(function(e) {
                if(e.which == 13) {
                    $('.searchAllBtn').trigger("click");
                }
            });
        }).focusout(function () {
            $(document).off('keypress');    //  清除事件
        });

        //  监听筛选框选择事件
        $("#selector").on('change', function () {
            $('.searchAllBtn').click();
        });
        $("#selector_distribute").on('change', function () {
            $('.searchAllBtn').click();
        });

        //  3、搜索框 + 搜索类型 回显
        if (Business.searchContent != "") {
            $(".inputSearchAll").val(Business.searchContent);
        }
        // $('#selector_distribute').find('option').eq(++Business.is_distribute).attr('selected',true);
        $('#selector').find('option').eq(++Business.state).attr('selected',true);
        /*var num = $(".selector_member_type").children("option").length;
        if (num > 0) {
            for (var i = 0; i < num; i ++) {
                if ($('.selector_member_type').children("option").eq(i).attr("value") == Business.state) {
                    $('.selector_member_type').children("option").eq(i).attr("selected","selected");
                    return ;
                }
            }
        }*/

    };

    /**
     * 实现上移 + 下移
     * @param memberId
     * @param orderType
     */
    modal.changeMemberWeight = function (memberId, orderType) {
        showLoading();
        $.ajax("/change_package_weight", {
            type: "POST",
            dataType: "json",
            data: {
                "package_id": memberId, "order_type": orderType
            },
            success: function (result) {
                hideLoading();
                if (result.code == 0) {
                    if (orderType == 0) {
                        baseUtils.show.blueTip("上移成功！");
                    } else {
                        baseUtils.show.blueTip("下移成功！");
                    }
                    window.location.reload();
                } else {
                    baseUtils.show.redTip(result.msg);
                }
            },
            error: function (xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("操作失败，请稍后重试！");
            }
        });

    };

    /**
     * 实现上架 + 下架
     * @param memberid
     * @param operate_type
     */
    modal.changeMemberState = function (memberId, operate_type) {
        showLoading();
        $.ajax("/change_goods_state", {
            type: "POST",
            dataType: "json",
            data: {
                "goods_id": memberId, "goods_type": 0, "operate_type": operate_type
            },
            success: function (result) {
                hideLoading();
                if (result.code == 0) {
                    if (operate_type == 0) {
                        baseUtils.show.blueTip("上架成功！");
                    } else {
                        baseUtils.show.blueTip("下架成功！");
                    }
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    baseUtils.show.redTip("操作失败，请稍后重试！");
                }
            },
            error: function (xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("操作失败，请稍后重试！");
            }
        });


    };


    return modal;

})();





















