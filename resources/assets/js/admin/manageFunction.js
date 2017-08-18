/**
 * Created by Administrator on 2017/3/13.
 */

$(document).ready(function () {
    $manageFunction.init();
});

$manageFunction = {

    currentInfo: {},    //点击节点信息

    editState: "", //是否编辑成功状态变量

    init: function () {

        this.editState = GetQueryString("state");

        //开关
        $("._switchOperateArea").on("click", "._functionSwitch", function (e) {

            var $delegateDom = $(e.delegateTarget),
                switchId = $delegateDom.data("switch_id");

            $manageFunction.currentInfo = {
                $delegateDom: $delegateDom,
                $currentDom: $(this),
                switchState: $delegateDom.data("switch_state"),    //当前开关的状态<true/false>
                onText: $delegateDom.data("on_text"),
                offText: $delegateDom.data("off_text")
            };

            switch (switchId) {
                case "category_navigation": //首页分类导航
                    $manageFunction.handleCategoryNavigation();
                    break;
                case "subscribe_count": //订阅量展示
                    $manageFunction.handleSubscribeCount();
                    break;
                case "update_num":  //更新期数展示
                    $manageFunction.handleUpdateNum();
                    break;
                case "message_remind":  //消息提醒
                    $manageFunction.handleMessageRemind();
                    break;
                case "service_remind":  //服务号消息通知
                    $manageFunction.handleServiceRemind();
                    break;
                default:
                    console.log("参数错误！");
                    break;
            }

        });

        //编辑分类导航
        $("#editCategoryNav").click(function () {
            window.location.href = "/category_setting";
        });

        //  初始化时查看是否编辑分类导航成功
        if ($manageFunction.editState == "finish_edit") {
            baseUtils.show.blueTip("分类导航编辑完成!");
            history.replaceState(null, "", "/manage_function");
        }

    },

    handleCategoryNavigation: function () {  //  点击切换分类导航开关

            if (this.currentInfo.switchState) {      //  关闭分类导航
                $.alert("关闭分类导航后，分类将会隐藏，所有内容均展示在首页", "info", {
                    btn: 3,
                    oktext: "确认关闭",
                    onOk: function () {
                        $.ajax("/category_switch",{
                            type: "POST",
                            dataType: "json",
                            data: {type: 0},
                            success: function (data) {
                                if (data.ret == 0) {        //  关闭分类导航成功 - 显示提示
                                    $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                                    baseUtils.show.blueTip("分类导航已隐藏！");
                                } else {
                                    if (data.msg != "" && data.msg != undefined) {
                                        baseUtils.show.redTip(data.msg);
                                    } else {
                                        baseUtils.show.redTip("关闭分类导航失败！");
                                    }
                                }
                            },
                            error: function (xhr, status, err) {
                                console.error(err);
                                baseUtils.show.redTip("网络错误，请稍后再试！");
                            }
                        });
                    },
                });

            } else {                            //  打开分类导航
                showLoading();

                $.ajax('/category_switch',{
                    type:"POST",
                    dataType:"json",
                    data:{type: 1},
                    success: function (data) {
                        hideLoading();
                        if (data.ret == 0) {        //  开启分类导航成功 - 显示提示
                            $.alert("已成功开启首页分类导航，您现在可以在编辑专栏时选择专栏的所属分类了", "info", {
                                btn: 2,
                            });
                            $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                        } else {
                            if (data.msg != "" && data.msg != undefined) {  //失败原因 - - 未完成分类导航的编辑
                                $.alert("您还未完成首页分类导航的编辑，暂时无法开启该功能", "info", {
                                    btn: 3,
                                    onOk: function () { //立即编辑
                                        window.location.href = '/category_setting';
                                    },
                                });
                            } else {                                        //失败原因 - - 其它错误
                                baseUtils.show.redTip("开启分类导航失败！");
                            }
                        }
                    },
                    error: function (xhr, status, err) {
                        hideLoading();
                        console.error(err);
                        baseUtils.show.redTip("网络错误，请稍后再试！");
                    }
                });
            };
    },
    handleSubscribeCount: function () { //显示or隐藏用户订阅数

            var version=$(".user_version_num").data("version_type");
            if(version && version==3) {
                if (this.currentInfo.switchState) { //隐藏订阅数显示
                    $.alert("关闭后用户将无法看到商品订阅数", "error", {
                        title: "提示",
                        icon: 'blue',
                        btn: 3,
                        onOk: function () {
                            $.ajax("/set_hid_sub", {
                                type: "POST",
                                dataType: "json",
                                data: {status: 0},
                                success: function (data) {
                                    if (data.code == 0) {
                                        baseUtils.show.blueTip("关闭成功！");
                                        $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                                    } else {
                                        baseUtils.show.redTip("关闭失败！");
                                    }
                                },
                                error: function (xhr, status, err) {
                                    console.error(err);
                                    baseUtils.show.redTip("网络错误，请稍后再试！");
                                }
                            })
                        }
                    })
                } else { //开启订阅数显示
                    $.alert("开启后您的用户可以看到商品的订阅数", "success", {
                        title: "提示",
                        icon: 'blue',
                        btn: 3,
                        onOk: function () {
                            $.ajax("/set_hid_sub", {
                                type: "POST",
                                dataType: "json",
                                data: {status: 1},
                                success: function (data) {
                                    console.log(data);
                                    if (data.code == 0) {
                                        baseUtils.show.blueTip("开启成功！");
                                        $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                                    } else {
                                        baseUtils.show.redTip("开启失败！");
                                    }
                                },
                                error: function (xhr, status, err) {
                                    console.error(err);
                                    baseUtils.show.redTip("网络错误，请稍后再试！");
                                }
                            });
                        }
                    });
                }
            } else {
                baseUtils.show.redTip("当前版本不支持订阅量展示控制功能，如需使用请升级至专业版");
            }

    },
    handleUpdateNum: function () {  //显示or隐藏专栏会员期数

            var version = $(".user_version_num").data("version_type");
            if(version && version == 3) {
                if (this.currentInfo.switchState) { //隐藏订阅数显示
                    $.alert("关闭后用户将无法看到商品的更新期数", "error", {
                        title: "提示",
                        icon: 'blue',
                        btn: 3,
                        onOk: function () {
                            $.ajax("/set_resource_count", {
                                type: "POST",
                                dataType: "json",
                                data: {status: 0},
                                success: function (data) {
                                    if (data.code == 0) {
                                        baseUtils.show.blueTip("关闭成功！");
                                        $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                                    } else {
                                        baseUtils.show.redTip("关闭失败！");
                                    }
                                },
                                error: function (xhr, status, err) {
                                    console.error(err);
                                    baseUtils.show.redTip("网络错误，请稍后再试！");
                                }
                            })
                        }
                    })
                } else {//开启订阅数显示
                    $.alert("开启后您的用户可以看到商品的更新期数", "success", {
                        title: "提示",
                        icon: 'blue',
                        btn: 3,
                        onOk: function () {
                            $.ajax("/set_resource_count", {
                                type: "POST",
                                dataType: "json",
                                data: {status: 1},
                                success: function (data) {
                                    if (data.code == 0) {
                                        baseUtils.show.blueTip("开启成功！");
                                        $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                                    } else {
                                        baseUtils.show.redTip("开启失败！");
                                    }
                                },
                                error: function (xhr, status, err) {
                                    console.error(err);
                                    baseUtils.show.redTip("网络错误，请稍后再试！");
                                }
                            })
                        }
                    })
                }
            } else {
                baseUtils.show.redTip("当前版本不支持更新期数展控制功能，如需使用请升级至专业版");
            }

    },
    handleMessageRemind: function () { //消息提醒开关

            $.ajax("/set_alert_message", {
                type: "POST",
                dataType: "json",
                data: {},
                success: function (result) {
                    if (result.code == 0) {
                        if ($manageFunction.currentInfo.switchState) {       //当前状态 - 开启
                            baseUtils.show.blueTip("已关闭消息提醒");
                        } else {
                            baseUtils.show.blueTip("已开启消息提醒");
                        }
                        $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                    } else {
                        baseUtils.show.redTip("操作失败，请稍后再试。");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                }
            });

    },
    handleServiceRemind: function () {//服务号消息通知开关
        var mid = $('.serviceRemind').data('is-push');
        var key= mid == 1?0:1;
        $.ajax("/set_service_notification", {
            type: "POST",
            dataType: "json",
            data: {status:key},
            success: function (result) {
                if (result.code == 0) {
                    if ($manageFunction.currentInfo.switchState) {       //当前状态 - 开启
                        baseUtils.show.blueTip("已关闭服务号消息通知");
                    } else {
                        baseUtils.show.blueTip("已开启服务号消息通知");
                    }
                    $switchBtn.changeSwitchBtnState($manageFunction.currentInfo);
                } else {
                    baseUtils.show.redTip("操作失败，请稍后再试。");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("服务器出小差了，请稍后再试！");
            }
        });
    }

};

$switchBtn = {

    /**
     * 切换按钮的状态
     */
    changeSwitchBtnState: function (currentInfo) {
        var state = !currentInfo.switchState;    //要转变到的状态
        if (state) {
            currentInfo.$currentDom.css({"background-color": "#2FCE6F"});
            currentInfo.$currentDom.children("._switchDescText").css({"left": "9px"});
            currentInfo.$currentDom.children("._switchDescText").text(currentInfo.onText);
            currentInfo.$currentDom.children("._switchButtonIcon").css({"left": "40px"});
        } else {
            currentInfo.$currentDom.css({"background-color": "#c2c2c2"});
            currentInfo.$currentDom.children("._switchDescText").css({"left": "44px"});
            currentInfo.$currentDom.children("._switchDescText").text(currentInfo.offText);
            currentInfo.$currentDom.children("._switchButtonIcon").css({"left": "0px"});
        }
        currentInfo.$delegateDom.data("switch_state", state);

    },

}











