/**
 * Created by Administrator on 2017/3/20.
 */

var search_content;
var resource_type;
var page_offset_json;
var page_offset = new Array();
//  请求到的数据，用于设置价格
var requestedData;

$(document).ready(function () {
    Business.init();
    $inviteGuest.init();
    $aliveShowSet.init();
});

var Business = (function () {

    var Business = {};

    Business["aliveId"] = -1;

    Business["piecePrice"] = -1;

    Business.init = function () {

        var aliveId = sessionStorage.getItem("inviteGuestAliveId");
        if (aliveId && aliveId != "") {
            sessionStorage.setItem("inviteGuestAliveId", "");
            $inviteGuest.openGuestSettingWindow(aliveId);
        }

        //  在已有单品中选择
        $(".selectResourceBtn").click(function () {
            showLoading();

            $.ajax("/choice_resource_list", {
                type: "POST",
                dataType: "json",
                data: {
                    'channel_type': 1, 'search_content': '', 'resource_type': 0
                },
                success: function (result) {
                    hideLoading();
                    if (result.code == 0) {
                        //  保存请求的数据
                        requestedData = result.data;

                        //  拼接html代码
                        var html_code = ResourceUtils.getSelectPageHtmlCode(result.data);

                        $(".select_good_window").fadeIn(300);

                        $(".select_content_area").html("");
                        $(".select_content_area").append(html_code);

                    } else {
                        baseUtils.show.redTip("拉取已有单品失败！");

                    }
                },
                error: function (xhr, status, err) {
                    hideLoading();
                    console.error(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                }
            });

        });

        //  初始化搜索全部单品
        modal.initSearchAll();

        //  初始化选择单品
        modal.initSelectResource();


        //  table 分页实现
        var html_code = ResourceUtils.getHomePagination("/resource_list_page", resource_type, search_content, (GetQueryString("state")||0));
        $("ul.pagination").append(html_code);



        //直播资源 打开嘉宾设置窗口
        $(".guest_setting_btn").click(function () {
            var resId = $(this).parents(".tr_body").data("resource_id");
            $inviteGuest.openGuestSettingWindow(resId);
        });

        //  操作 - 复制到剪贴板
        (function () {
            var clipboard = new Clipboard('.copyHref');
            clipboard.on('success', function(e) {
                if(e.trigger.id=='copy_app_href'){
                    baseUtils.show.blueTip("复制成功！");
                }else{
                    baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
                }

                e.clearSelection();

            });
        })();

        //  操作 - 编辑
        $(".edit_resource_btn").click(function () {
            var resource_type = $(this).parents(".tr_body").data("resource_type");
            var resource_id = $(this).parents(".tr_body").data("resource_id");

            window.location.href = "/edit_resource_page?type=" + resource_type + "&id=" + resource_id + "&upload_channel_type=" + 1;

        });

        //  点击更多事件
        $("ul.more_operate>li").click(function () {

            var resType = $(this).parents(".tr_body").data("resource_type"),
                resId = $(this).parents(".tr_body").data("resource_id"),
                resName = $(this).parents(".tr_body").data("resource_name"),
                piecePrice = $(this).parents(".tr_body").data("piece_price"),
                isTranscode = $(this).parents(".tr_body").data("is_transcode");
                // showViewCount = $(this).parents('.tr_body').data('config_show_view_count'),
                // showReward = $(this).parents('.tr_body').data('config_show_reward');

            var type = $(this).data("type");
            switch (type) {
                // case "alive_show_set": // 直播间显示设置
                //     modal.aliveShowSet($(this),resId,showViewCount,showReward);
                //     break;
                case "look_comment":
                    if (resType == 4) {
                        resetUrl("/alivecomment?alive_id=" + resId);
                    } else {
                        resetUrl("/comment_admin?type=" + (resType-1) + "&record_id=" + resId);
                    }
                    break;
                case "export_audio":
                    modal.exportAudio(resId);
                    break;
                case "end_alive":
                    ResourceUtils.endAlive(resId);
                    break;
                case "set_sale_ratio":
                    modal.setSaleRatio(resName, piecePrice, resId);
                    break;
                case "enable_copy":
                    ResourceUtils.changeCanSelectState(resId, true,resType);
                    break;
                case "forbid_copy":
                    var alertBoxTitlePart='';
                    if(resType==1){
                        alertBoxTitlePart='该篇图文的'
                    }
                    if(resType==2){
                        alertBoxTitlePart='音频详情的'
                    }
                    if(resType==3){
                        alertBoxTitlePart='视频详情的'
                    }
                    $.alert("禁止复制后，"+alertBoxTitlePart+"图片点击放大和长按识别二维码功能将被禁用", "info", {
                        btn: 3,
                        onOk: function () {
                            ResourceUtils.changeCanSelectState(resId, false,resType);
                        }
                    });
                    break;
                case "hide_resource":
                    ResourceUtils.updateResourceState(resType, 1, resId);
                    break;
                case "show_resource":
                    if (isTranscode == 2) {
                        baseUtils.show.redTip("当前资源转码失败，请重新上传！");
                        return false;
                    }
                    ResourceUtils.updateResourceState(resType, 0, resId);
                    break;
                case "remove_resource":
                    modal.removeResource(resType, resId);
                    break;
                default :
                    console.log("参数错误");
                    break;
            }

        });


        //  初始化 设置分销窗口
        modal.initSetSale();

        //  初始化设置嘉宾设置窗口
        // modal.initSetGuestWindow();  //  todo


    };

    return Business;

})();


var modal = (function () {

    var modal = {};

    var position = -1;


    /**
     * 模块一：资源列表页面的搜索功能初始化
     */
    modal.initSearchAll = function () {

        //  1、开始搜索 - 全部单品
        $(".searchAllBtn").click(function () {
            var resource_type = $(".selector_resource_type").val();
            var state = $.trim($(".selector_show_type").val());
            var search_content = $.trim($(".inputSearchAll").val());

            window.location.href = "/resource_list_page?resource_type=" + resource_type + "&search_content=" + search_content + "&state=" + state;

        });

        // 1、输入完成后，回车开始搜索 - 全部单品
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
        $(".selector_show_type, .selector_resource_type").on('change', function () {
            $('.searchAllBtn').trigger("click");
        });


        //  1、搜索框 + 搜索类型 回显
        ResourceUtils.reBackSearch();

    };

    /**
     * 模块二 ：选择已有页面的初始化 （搜索 + 分页）
     */
    modal.initSelectResource = function () {

        // 直播间显示设置
        // $(".close-icon,#alive_show_cancel_btn").click(function(){ // 关闭
        //     $(".alive-show-set-modal").fadeOut(300);
        // });
        // $('.choose-1').on('click',function(){ // 按钮点击切换
        //     $('.choose-1').children('.choose-btn').removeClass('choose-active');
        //     $(this).children('.choose-btn').addClass('choose-active');
        // });
        // $('.choose-2').on('click',function(){ // 按钮点击切换
        //     $('.choose-2').children('.choose-btn').removeClass('choose-active');
        //     $(this).children('.choose-btn').addClass('choose-active');
        // });

        //  2、关闭搜索框 - 点击 ×
        $(".close_icon_wrapper").click(function () {
            $(".select_good_window").fadeOut(300);
        });
        //  2、关闭搜索框 - 点击 取消
        $(".cancel_btn").click(function () {
            $(".select_good_window").fadeOut(300);
        });
        //  2、 - 点击 下一步
        $(".next_step_btn").click(function () {

            //  选中资源的位置（0 - 4）
            position = $("input:radio:checked").val();

            if (position == undefined) {
                baseUtils.show.redTip("请先选择一个已有单品！");
                return ;
            }

            $(".select_good_window").fadeOut(300);

            //  给设置价格填充数据
            var singleData = requestedData["resource_list"][position];
            $(".resource_icon_3").attr("src", singleData['img_url']);
            $(".resource_title").html(singleData['title']);
            switch (singleData['resource_type']) {
                case 1:
                    $(".resource_type_3").html("图文");
                    break;
                case 2:
                    $(".resource_type_3").html("音频");
                    break;
                case 3:
                    $(".resource_type_3").html("视频");
                    break;
                case 4:
                    $(".resource_type_3").html("直播");
                    break;
                default:
                    break;
            }
            $(".resource_time_3").html(singleData['start_at']);

            $(".set_price_window").fadeIn(300);


        });

        //  2、开始搜索 - 已有单品
        $(".searchPartBtn").click(function () {
            var search_content = $.trim($(".inputSearchPart").val());

            ResourceUtils.requestSelectData(1, search_content, 0, 1);

        });

        // 2、输入完成后，回车开始搜索 - 已有单品
        $(".inputSearchPart").focusin(function () {
            $(document).keypress(function(e) {
                if(e.which == 13) {
                    $('.searchPartBtn').trigger("click");
                }
            });
        }).focusout(function () {
            $(document).off('keypress');    //  清除事件
        });

        //  点击分页 获取其它页
        $(".select_content_area").on("click", ".page_offset_div_2 a", function () {
            var search_content = $.trim($(".inputSearchPart").val());
            var page_index = $(this).data("page_index");

            ResourceUtils.requestSelectData(1, search_content, 0, page_index);


        });

        //  3、设置价格 - 点击 ×
        $(".close_icon_wrapper_2").click(function () {
            $(".set_price_window").fadeOut(300);
        });

        //  3、设置价格 - 点击上一步
        $(".back_btn").click(function () {
            $(".set_price_window").fadeOut(300);
            $(".select_good_window").fadeIn(300);
        });

        //  3、设置价格 - 点击确定
        $(".confirm_price_btn").click(function () {

            var price = $.trim($(".input_setting_price").val());

            var reg = /^([1-9][\d]*|0)([/.][\d]{1,2})?$/;
            if (!reg.test(price)) {
                baseUtils.show.redTip('价格输入有误');
                $('.input_setting_price').val("");
                return false;
            }
            if (price <= 0) {
                baseUtils.show.redTip("价格不能为0 或 负数!");
                return false;
            }

            var arr = {};
            arr["resource_id"] = requestedData["resource_list"][position]['id'];
            arr["resource_type"] = requestedData["resource_list"][position]['resource_type'];
            var resourceListArr = [arr];

            $.ajax("/submit_choice_resource", {
                type: "POST",
                data: {
                    "channel_type": 1,
                    "resource_list": resourceListArr,
                    "piece_price": price * 100  // 单位： 分
                },
                success: function (result) {
                    baseUtils.show.blueTip("选择已有单品成功！");
                    $(".set_price_window").fadeOut(300);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器开小差啦，请稍后再搜索！");

                }
            });

        });
    };

    //  更多 - 导出音频
    modal.exportAudio = function (aliveId) {

        $.ajax("/async_download_alive_voice", {
            type: "POST",
            dataType: "json",
            data: {
                alive_id: aliveId
            },
            success: function (result) {
                switch (result.code) {
                    case 0:
                        window.location.href = result.data['url'];      //直接开始下载音频文件
                        break;
                    case 1:
                        $.alert("语音正在合成中，根据您直播间内的音频数量需要10分钟到4个小时不等，请稍候访问。", "info", {
                            btn: 2,
                        });
                        break;
                    case 2:
                        $.alert("开始进行语音合成，根据您直播间内的音频数量需要10分钟到4个小时不等，请稍候访问。", "info", {
                            btn: 2,
                        });
                        break;
                    case 3:
                        $.alert("服务器正在维护，直播音频导出功能受到影响，请稍后再试。", "info", {
                            btn: 2,
                        });
                        break;
                    case 4:
                        $.alert("该直播没有音频。", "info", {
                            btn: 2,
                        });
                        break;
                    case 5:
                        $.alert("这个直播不是语音类型的，不支持音频导出。", "info", {
                            btn: 2,
                        });
                        break;
                    case 6:
                        $.alert("这个直播还没结束，暂不支持音频导出。", "info", {
                            btn: 2,
                        });
                        break;
                    default:
                        console.log("参数错误");
                        baseUtils.show.redTip(result.msg);
                        break;
                }
                console.log(result.msg);
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("服务器出小差了，请稍后再试！");
            }
         });

    };

    // 更多 - 直播间显示设置
    modal.aliveShowSet = function(that, aliveId, showViewCount, showReward){
        // 回填数据
        if(showViewCount){
            // 讲师可见
            $('#num_teacher_show > .choose-btn').addClass('choose-active');
            $('#num_all_show > .choose-btn').removeClass('choose-active');
        }else{
            // 所有人可见
            $('#num_all_show > .choose-btn').addClass('choose-active');
            $('#num_teacher_show > .choose-btn').removeClass('choose-active');
        }
        if(showReward){
            // 讲师可见
            $('#reword_teacher_show > .choose-btn').addClass('choose-active');
            $('#reword_all_show > .choose-btn').removeClass('choose-active');
        }else{
            // 所有人可见
            $('#reword_all_show > .choose-btn').addClass('choose-active');
            $('#reword_teacher_show > .choose-btn').removeClass('choose-active');
        }

        // 显示模态框
        $(".alive-show-set-modal").fadeIn(300);

        // 点击保存
        $('#alive_show_save_btn').unbind('click').click(function(){

            console.log("已经点击保存");

            var show_view_count = 1,
                show_reward = 1;

            if($('#num_all_show').children('.choose-btn').hasClass('choose-active')){
                show_view_count = 0;
            }
            if($('#reword_all_show').children('.choose-btn').hasClass('choose-active')){
                show_reward = 0;
            }

            $.ajax("/set_alive_config", {
                type: "POST",
                dataType: "json",
                data: {
                    id: aliveId,
                    config_show_view_count: show_view_count,
                    config_show_reward: show_reward
                },
                success: function (result) {
                    if (result.code === 0) {
                        $(".alive-show-set-modal").fadeOut(300);
                        that.parents('.tr_body').data('config_show_view_count', show_view_count);
                        that.parents('.tr_body').data('config_show_reward',show_reward);
                        console.log("保存成功");
                    } else {
                        baseUtils.show.redTip("操作失败，请稍后重试！");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                },
            });
        });

    };

    //  更多 - 设置分销
    modal.setSaleRatio = function (aliveName, piecePrice, aliveId) {

        $(".sale_goods_name").html(aliveName);

        Business["piecePrice"] = piecePrice;

        //  1、获取点击条目的直播 aliveId
        Business["aliveId"] = aliveId;

        //  2、根据 aliveId 查询直播资源的分成比例值：（1-50）-开启，0-关闭
        $.ajax("/query_profit_ratio", {
            type: "POST",
            dataType: "json",
            data: {
                alive_id: aliveId
            },
            success: function (result) {
                if (result.code == 0) {

                    var distribute_percent = result.data["distribute_percent"];
                    if (distribute_percent == 0) {
                        //  关闭开关 , 清空输入框， 隐藏输入框
                        $("input#set_radio_off").prop("checked",true);
                        $(".input_radio_value").val("");
                        $('.set_percent').hide();

                    } else if (distribute_percent > 0 && distribute_percent <= 50) {
                        //  打开开关， 回显输入框， 显示输入框
                        $("input#set_radio_on").prop("checked", true);
                        $(".input_radio_value").val(distribute_percent);
                        $('.set_percent').show();

                    } else {
                        //  默认状态
                        $("input#set_radio_off").prop("checked", true);
                        $(".input_radio_value").val("");
                        $('.set_percent').hide();

                    }
                    $(".set_sale_ratio_window").fadeIn(300);

                } else {
                    baseUtils.show.redTip("操作失败，请稍后重试！");

                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            },
        });

    };


    /**
     * 模块三 ： 初始化 设置 分销窗口
     */
    modal.initSetSale = function () {

        //  点击取消    点击 ×
        $(".cancel_sale_btn, .close_icon_wrapper_3").click(function () {
            $(".set_sale_ratio_window").fadeOut(300);

        });

        //  监控radio值的变化
        $("input[name='set_sale_radio']").on("change", function () {
            var saleSwitch = $("input[name='set_sale_radio']:checked").val();
            if (saleSwitch == 0) {
                if (Business["piecePrice"] < 1) {
                    baseUtils.show.redTip("商品价格不低于1元才可开启分销!");
                    $("input#set_radio_off").prop("checked",true);      //  重置radio

                } else {
                    $('.set_percent').fadeIn(300);

                }
            } else {
                $('.set_percent').fadeOut(300);
            }
        });

        //  实时监控input中的输入变化()
        $(".input_radio_value").bind("input propertychange", function () {
            var inputValue = $(".input_radio_value").val();

            var reg = /^[1-9][\d]*$/;
            if (!reg.test(inputValue)) {
                inputValue = inputValue.replace(/\D/g, "");
                $(".input_radio_value").val(inputValue);
            }

        });


        //  点击确认
        $(".confirm_sale_btn").click(function () {

            var saleSwitch = $("input[name='set_sale_radio']:checked").val();

            if (saleSwitch == 0) {  //  开启
                var inputValue = $(".input_radio_value").val();     //自定义分成比例

                if (inputValue < 1 || inputValue > 50) {
                    baseUtils.show.redTip("分成比例只能设置为1% ~ 50%!");
                    return false;
                }
                //  提交分销设置
                ResourceUtils.submitSaleSet(Business["aliveId"], inputValue);
            } else {
                var inputValue = 0;     //分成比例 为 0
                //  提交分销设置
                ResourceUtils.submitSaleSet(Business["aliveId"], inputValue);
            }
        });

    };

    // 移除资源
    modal.removeResource = function (resType, resId) {

        //  1、查询 单品是否有在专栏中
        $.ajax("/query_goods_state", {
            type: "POST",
            dataType: "json",
            data: {
                goods_id: resId,
                goods_type: resType,
                channel_type: 0
            },
            success: function (result) {
                if (result.code == 0) {

                    var isExist = result.data['is_exist'];  //  1 - 存在； 0 - 不存在

                    if (isExist == 0) {     //  单品只在单品列表中存在
                        $.alert("移除后所有用户将无法查看该单品，是否确认移除？", "info", {
                            onOk: function () {
                                //  2、移除
                                ResourceUtils.removeGood(resId, resType, 0);
                            }
                        });
                    } else {                //  单品在其它专栏总也有  文案>> 移除后该单品将不再单独售卖，是否确认移除？该单品仍会存在于：《每天听见吴晓波》《晓课堂》《晓报告》

                        var recordsList = result.data['records_list'];
                        var packageNames = "";
                        for (var i = 0; i < recordsList.length; i ++) {
                            packageNames += ("《" + recordsList[i]['title'] + "》");
                        }

                        $.alert("移除后该单品将不再单独售卖，是否确认移除？" + "<br>" + "该单品仍会存在于：" + packageNames, "info", {
                            onOk: function () {
                                //  2、移除
                                ResourceUtils.removeGood(resId, resType, 0);

                            }
                        });
                    }
                } else {
                    baseUtils.show.redTip("移除失败！");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("服务器开小差啦，请稍后再移除！");
            }
        });

    };


    return modal;

})();


var ResourceUtils = (function () {

    var utils = {};

    /**
     * 修改图文的是否可复制的状态
     * @param resId
     * @param canSelect     - true: 允许复制， false:禁止复制
     */
    utils.changeCanSelectState = function (resId, canSelect,type) {

        $.ajax("/set_resource_select_can", {
            type: "POST",
            dataType: "json",
            data: {
                id: resId,
                resource_type: type,
                can_select: canSelect ? 1 : 0,
            },
            success: function (result) {
                if (result.code == 0) {
                    if (canSelect) {
                        baseUtils.show.blueTip("已经允许复制");
                    } else {
                        baseUtils.show.blueTip("已经禁止复制");
                    }
                    window.location.reload();

                } else {
                    baseUtils.show.redTip("操作失败，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
         });
    };

    /**
     * 回显搜索内容 + 资源类型
     */
    utils.reBackSearch = function () {
        if (search_content != "") {
            $(".inputSearchAll").val(search_content);
        }

        var num = $(".selector_resource_type").children("option").length;
        if (num > 0) {
            for (var i = 0; i < num; i ++) {
                if ($('.selector_resource_type').children("option").eq(i).attr("value") == resource_type) {
                    $('.selector_resource_type').children("option").eq(i).attr("selected","selected");
                }
            }
        }

        var state = GetQueryString("state") || 0;
        var num_2 = $(".selector_show_type").children("option").length;
        if (num_2 > 0) {
            for (var i = 0; i < num_2; i ++) {
                if ($('.selector_show_type').children("option").eq(i).attr("value") == state) {
                    $('.selector_show_type').children("option").eq(i).attr("selected","selected");
                    return ;
                }
            }
        }

    };

    /**
     * 结束直播
     */
    utils.endAlive = function(id) {

        $.alert("您确定要结束该直播吗?", "info", {
            onOk: function () {
                $.get("/end_alive",{"id":id},function(data) {
                    if(data.code==0) {
                        $.alert("结束直播成功", "success", {
                            onOk: function () {
                                window.location.reload();
                            }
                        });

                    }
                    else {
                        baseUtils.show.redTip("直播结束失败");
                    }
                });
            }
        });

    };

    /**
     * 更新资源状态
     * @param resource_type - 1 - article, 2 - audio , 3 - video  , 4 - alive
     * @param new_state     - 0-上架  1-下架
     * @param id            - 资源id
     */
    utils.updateResourceState = function (resource_type, new_state, id) {

        var allParams = {};
        allParams['goods_id'] = id;
        allParams['goods_type'] = resource_type;
        allParams['operate_type'] = new_state;

        var tip = "";
        if (new_state == 0) {
            tip = "上架后该单品将对外显示";
        } else {
            tip = "下架后该单品将不对外显示";
        }

        $.alert(tip, "info", {
            onOk: function () {
                $.post('/change_goods_state', allParams, function (result) {

                    if (result.code == 0) {
                        switch (new_state) {
                            case 0:
                                baseUtils.show.blueTip("上架成功！");
                                break;
                            case 1:
                                baseUtils.show.blueTip("下架成功！");
                                break;
                            default:
                                break;
                        }
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        console.log(result.msg);
                        baseUtils.show.redTip(result.msg);
                    }
                });

            }
        });

    };

    /**
     * 移除资源
     * @param goods_id
     * @param goods_type
     * @param channel_type
     */
    utils.removeGood = function (goods_id, goods_type, channel_type) {
        var allParams = {};
        allParams['goods_id'] = goods_id;
        allParams['goods_type'] = goods_type;
        allParams['channel_type'] = channel_type;

        $.post('/move_goods', allParams, function (result) {

            if (result.code == 0) {
                baseUtils.show.blueTip("移除资源成功!");
                window.location.reload();
            } else {
                baseUtils.show.redTip("移除资源失败!");
            }
        });
    };

    /**
     * 提交分销设置
     *
     * @param aliveId       -   直播资源id
     * @param inputValue    -   设置分成比例： 邀请卡分销开启：（1 ~ 50）； 邀请卡分销关闭：0
     */
    utils.submitSaleSet = function (aliveId, inputValue) {
        $.ajax("/set_profit_ratio", {
            type: "POST",
            dataType: "json",
            data: {
                "alive_id": aliveId,
                "distribute_percent": inputValue
            },
            success: function (result) {
                if (result.code == 0) {

                    $(".set_sale_ratio_window").fadeOut(300);
                    baseUtils.show.blueTip("分销设置成功！");
                } else {
                    baseUtils.show.redTip("分销设置失败！");
                }

            },
            error: function (xhr, status, err) {
                console.error(err);
                baseUtils.show.redTip("服务器开小差啦，请稍后再提交！");
            }
        });
    };



    /**
     * 拼接已有单品内容 html代码
     */
    utils.getSelectPageHtmlCode = function (data) {
        var html_code = "";

        var pageOffset = data["page_offset"];
        var resourceListArr = data["resource_list"];

        if (resourceListArr.length <= 0) {

            html_code += '<div class="search_no_data">暂无数据，请重新搜索！</div>';

            return html_code;
        }

        /*搜索内容代码*/
        for (var i = 0; i < resourceListArr.length; i ++) {
            var resourceItem = resourceListArr[i];

            html_code += '<div class="resourceItemWrapper">';
            html_code += '<input id="resource_item_' + i + '" class="with-gap resource_radio" name="select_resource" value="' + i + '" type="radio"/>';
            html_code += '<label for="resource_item_' + i + '" class="radio_desc">';
            html_code += '<div class="resource_icon_wrapper_2">';
            html_code += '<img class="resource_icon_2" src="' + resourceItem["img_url"] + '">';
            html_code += '</div>';
            html_code += resourceItem["title"];
            html_code += '</label>';
            html_code += '<span class="resource_type_2">';

            switch (resourceItem["resource_type"]) {        //(0-全部,1-图文,2-音频,3-视频,4-直播);
                case 1:
                    html_code += '图文';
                    break;
                case 2:
                    html_code += '音频';
                    break;
                case 3:
                    html_code += '视频';
                    break;
                case 4:
                    html_code += '直播';
                    break;
                default:
                    html_code += '资源';
                    break;
            }

            html_code += '</span>';
            html_code += '<span class="resource_time_2">';
            html_code += resourceItem["start_at"];
            html_code += '</span>';
            html_code += '</div>';

        }

        /*搜索内容 - 分页代码*/
        html_code += '<div class="page_offset_div_2" align="center">';
        html_code += '<ul class="pagination">';

        html_code += utils.getSelectPagination(pageOffset);

        html_code += '</ul>';
        html_code += '</div>';

        return html_code;

    };

    /**
     * 发送请求 ， 获取已有单品数据, 并处理
     *
     * @param channelType       - 1-单品,2-专栏,3-会员
     * @param searchContent     - 搜索内容
     * @param resourceType      - 0-全部,1-图文,2-音频,3-视频,4-直播
     * @param pageIndex         - 分页页码
     */
    utils.requestSelectData = function (channelType, searchContent, resourceType, pageIndex) {

        $.ajax("/choice_resource_list", {
            type: "POST",
            dataType: "json",
            data: {
                'channel_type': channelType, 'search_content': searchContent, 'resource_type': resourceType, 'page': pageIndex
            },
            success: function (result) {
                //  保存请求的数据
                requestedData = result.data;
                if (result.code == 0) {
                    //  拼接html代码
                    var html_code = utils.getSelectPageHtmlCode(result.data);

                    $(".select_content_area").html("");
                    $(".select_content_area").append(html_code);
                } else {
                    baseUtils.show.redTip("搜索失败！");
                }
            },
            error: function (xhr, status, err) {
                console.error(err);
                baseUtils.show.redTip("服务器开小差啦，请稍后再搜索！");
            }
        });

    };


    /**
     * 实现分页 - pagination
     * 需要数据; total_pages' - total_count' - current_page' - page_size'
     * state :   value="-1">所有状态
                 value="0">已上架
                 value="1">已下架
     */
    utils.getHomePagination = function (routeUrl, resourceType, searchContent, state) {
        page_offset = JSON.parse(page_offset_json);
        page_offset['current_page'] = +page_offset["current_page"];

        var html_code = "";

        if (page_offset['total_pages'] <= 0) {      //没有数据
            $(".page_offset_div").addClass("hide");
            return "";
        }

        /**
         * 确定skip_url
         */
        var skip_url = routeUrl + "?resource_type=" + resourceType + "&search_content=" + searchContent + "&state=" + state + "&page=";

        /**
         * 左箭头
         */
        if (page_offset['current_page'] == 1) {     //  位于第一页
            html_code = "<li class='disabled'><span>«</span></li>"
        } else {
            html_code = "<li><a href='" + skip_url + (page_offset['current_page'] - 1) + "' rel='prev'>«</a></li>";
        }

        /**
         * 中间数字
         */
        if (page_offset['total_pages'] < 12) {      //全部显示,没有省略号
            for (var i = 1; i <= page_offset['total_pages']; i ++) {
                if (page_offset['current_page'] == i) {
                    html_code += "<li class='active'><span>" + i + "</span></li>";
                } else {
                    html_code += "<li><a href='" + skip_url + i + "'>" + i + "</a></li>";
                }
            }

        } else {       //  部分显示，有省略号，
            if (page_offset['current_page'] > 6 && (page_offset['total_pages'] - page_offset['current_page']) < 6) {     //  左边有省略号
                for (var j = 1; j <= 2; j ++ ) {
                    html_code += "<li><a href='resource_list_page?page=" + j + "'>" + j + "</a></li>";
                }
                html_code += "<li class='disabled'><span>...</span></li>";
                for (j = page_offset['total_pages'] - 8; j <= page_offset['total_pages']; j ++) {
                    if (page_offset['current_page'] == j) {
                        html_code += "<li class='active'><span>" + j + "</span></li>";
                    } else {
                        html_code += "<li><a href='" + skip_url + j + "'>" + j + "</a></li>";
                    }
                }

            } else if (page_offset['current_page'] < 7 && (page_offset['total_pages'] - page_offset['current_page']) > 5){      //  右边有省略号
                for (var k = 1; k <= 8; k ++) {
                    if (page_offset['current_page'] == k) {
                        html_code += "<li class='active'><span>" + k + "</span></li>";
                    } else {
                        html_code += "<li><a href='" + skip_url + k + "'>" + k + "</a></li>";
                    }
                }
                html_code += "<li class='disabled'><span>...</span></li>";
                for (k = page_offset['total_pages'] - 1; k <= page_offset['total_pages']; k ++) {
                    html_code += "<li><a href='" + skip_url + k + "'>" + k + "</a></li>";
                }

            } else {    // 两边都有省略号
                for (var i = 1; i <= 2; i ++ ) {
                    html_code += "<li><a href='" + skip_url + i + "'>" + i + "</a></li>";
                }
                html_code += "<li class='disabled'><span>...</span></li>";

                for (var j = page_offset['current_page'] - 3; j <= (page_offset['current_page'] + 3); j ++) {
                    if (page_offset['current_page'] == j) {
                        html_code += "<li class='active'><span>" + j + "</span></li>";
                    } else {
                        html_code += "<li><a href='" + skip_url + j + "'>" + j + "</a></li>";
                    }
                }

                html_code += "<li class='disabled'><span>...</span></li>";
                for (var k = page_offset['total_pages'] - 1; k <= page_offset['total_pages']; k ++) {
                    html_code += "<li><a href='" + skip_url + k + "'>" + k + "</a></li>";
                }

            }

        }

        /**
         * 右箭头
         */
        if (page_offset['current_page'] == page_offset['total_pages']) {    //  位于最后一页
            html_code += "<li class='disabled'><span>»</span></li>";
        } else {
            html_code += "<li><a href='" + skip_url + (page_offset['current_page'] + 1) + "' rel='next'>»</a></li>";
        }

        return html_code;

    };

    /**
     * 实现分页 - pagination
     * 选择单品页面分页代码
     */
    utils.getSelectPagination = function (pageOffset) {
        var html_code = '';

        var pageOffsetArr = JSON.parse(pageOffset);
        pageOffsetArr['current_page'] = +pageOffsetArr['current_page'];

        if (pageOffsetArr['total_pages'] <= 0) {      //没有数据
            return '';
        }

        /**
         * 左箭头
         */
        if (pageOffsetArr['current_page'] == 1) {     //  位于第一页
            html_code = "<li class='disabled'><span>«</span></li>"
        } else {
            html_code = "<li><a data-page_index='" + (pageOffsetArr['current_page'] - 1) + "' rel='prev'>«</a></li>";
        }

        /**
         * 中间数字
         */
        if (pageOffsetArr['total_pages'] < 12) {      //全部显示,没有省略号
            for (var i = 1; i <= pageOffsetArr['total_pages']; i ++) {
                if (pageOffsetArr['current_page'] == i) {
                    html_code += "<li class='active'><span>" + i + "</span></li>";
                } else {
                    html_code += "<li><a data-page_index='" + i + "'>" + i + "</a></li>";
                }
            }

        } else {       //  部分显示，有省略号，
            if (pageOffsetArr['current_page'] > 6 && (pageOffsetArr['total_pages'] - pageOffsetArr['current_page']) < 6) {     //  左边有省略号
                for (var j = 1; j <= 2; j ++ ) {
                    html_code += "<li><a data-page_index='" + j + "'>" + j + "</a></li>";
                }
                html_code += "<li class='disabled'><span>...</span></li>";
                for (j = pageOffsetArr['total_pages'] - 8; j <= pageOffsetArr['total_pages']; j ++) {
                    if (pageOffsetArr['current_page'] == j) {
                        html_code += "<li class='active'><span>" + j + "</span></li>";
                    } else {
                        html_code += "<li><a data-page_index='" + j + "'>" + j + "</a></li>";
                    }
                }

            } else if (pageOffsetArr['current_page'] < 7 && (pageOffsetArr['total_pages'] - pageOffsetArr['current_page']) > 5){      //  右边有省略号
                for (var k = 1; k <= 8; k ++) {
                    if (pageOffsetArr['current_page'] == k) {
                        html_code += "<li class='active'><span>" + k + "</span></li>";
                    } else {
                        html_code += "<li><a data-page_index='" + k + "'>" + k + "</a></li>";
                    }
                }
                html_code += "<li class='disabled'><span>...</span></li>";
                for (k = pageOffsetArr['total_pages'] - 1; k <= pageOffsetArr['total_pages']; k ++) {
                    html_code += "<li><a data-page_index='" + k + "'>" + k + "</a></li>";
                }

            } else {    // 两边都有省略号
                for (var i = 1; i <= 2; i ++ ) {
                    html_code += "<li><a data-page_index='" + i + "'>" + i + "</a></li>";
                }
                html_code += "<li class='disabled'><span>...</span></li>";

                for (var j = pageOffsetArr['current_page'] - 3; j <= (pageOffsetArr['current_page'] + 3); j ++) {
                    if (pageOffsetArr['current_page'] == j) {
                        html_code += "<li class='active'><span>" + j + "</span></li>";
                    } else {
                        html_code += "<li><a data-page_index='" + j + "'>" + j + "</a></li>";
                    }
                }

                html_code += "<li class='disabled'><span>...</span></li>";
                for (var k = pageOffsetArr['total_pages'] - 1; k <= pageOffsetArr['total_pages']; k ++) {
                    html_code += "<li><a data-page_index='" + k + "'>" + k + "</a></li>";
                }

            }

        }

        /**
         * 右箭头
         */
        if (pageOffsetArr['current_page'] == pageOffsetArr['total_pages']) {    //  位于最后一页
            html_code += "<li class='disabled'><span>»</span></li>";
        } else {
            html_code += "<li><a data-page_index='" + (pageOffsetArr['current_page'] + 1) + "' rel='next'>»</a></li>";
        }

        return html_code;
    };

    return utils;

})();











