/**
 * Created by Frank on 2017/3/21.
 */
var business;
$(function() {
    var id = $('#mainToolBox').data('id');
    business = new Business(id);
    business.init();
    $inviteGuest.init();
    $aliveShowSet.init();
});


function Business(id) { //定义一些公共属性
    this.id = id; //当前专栏的id
    this.hasDown = false;
    this.hasClose = true;
    this.page = 1; //已有单品的页码
    this.searchContent = '';
    this.btnState = true;

    this.toolBoxBtn = true; //工具按钮的点击状态

    this.searchResourceType = 0;
    this.searchResourceName = "";
}
Business.prototype = {
    init: function() { //页面的初始化操作，绑定事件
        var self = this;

        self.searchResourceType = GetQueryString("resource_type");
        self.searchResourceName = GetQueryString("search_content");

        //打开直播窗口
        var aliveId = sessionStorage.getItem("inviteGuestAliveId");
        if (aliveId && aliveId != "") {
            sessionStorage.setItem("inviteGuestAliveId", "");
            $inviteGuest.openGuestSettingWindow(aliveId);
        }

        //  初始化搜索筛选
        this.initSearchPart();

        this.initCopy('copyHref'); //加载复制链接插件

        $('#mainToolBox')
            .on('click', 'li[data-type]', function(e) { //专栏的功能
                var $ele = $(e.target),
                    type = $ele.data('type'),
                    id = self.id;
                type && self.btnState && (self.maintool(type, id));
            });
        $('')
        $('.singleToolBox')
            .on('click', 'li[data-type]', function(e) { //专栏内单品的功能
                var $ele = $(e.target),
                    type = $ele.data('type'),
                    packageid = self.id,
                    resType = $ele.parentsUntil('.singleToolBox', '.toolUl').data('type'),
                    resid = $ele.parentsUntil('.singleToolBox', '.toolUl').data('id');

                var resTitle = $ele.parentsUntil('.singleToolBox', '.toolUl').data('res_title'),
                    isTry = $ele.parentsUntil('.singleToolBox', '.toolUl').data('is_try'),
                    showTime = $ele.parentsUntil('.singleToolBox', '.toolUl').data('show_time'),
                    resImgUrl = $ele.parentsUntil('.singleToolBox', '.toolUl').data('img_url'),
                    paymentType = $ele.parentsUntil('.singleToolBox', '.toolUl').data('payment_type');

                type && self.btnState && (self.singleTool(type, packageid, resType, resid, resTitle, isTry, showTime, resImgUrl, paymentType));
            });

        $('#addSingle').on('click', function() { //弹出选选择择框
            showLoading();
            self.page = 1;
            self.hasDown = false;
            self.hasClose = false;
            self.getSelectData();
        });
        $('#selectWindow #selctClose, #selectWindow #selectCancelBtn')
            .on('click', function() { //关闭选择弹窗
                var $selectWin = $('#selectWindow');
                $selectWin.fadeOut('normal', function() {
                    $(this).find('#selectAreaList').html('');
                });
                $selectWin.find('.inputSearchPart').val(''); //清空搜索框
                self.page = 1;
                self.hasClose = true;
                self.searchContent = '';
            });

        $('#selectWindow #selectSearchBtn')
            .on('click', function() { //选择框的单品搜索
                var searchVal = $.trim($(this).prev().val());
                showLoading();
                self.searchContent = searchVal;
                self.page = 1;
                self.hasClose = true;
                self.getSelectData();
            })
            .prev().on('keypress', function(e) {
                if (e.keyCode == "13") {
                    $('#selectWindow #selectSearchBtn').click();
                }
            });

        $('#selectWindow .select_content_area').scroll(function(e) {
            var DivHeight = $('#selectAreaList').height(),
                ScrollHeight = $(this).height(),
                ScrollTop = $(this).scrollTop();

            if ((ScrollTop + ScrollHeight >= DivHeight - 5) &&
                !self.hasDown //判断是否到达底部
                &&
                !self.hasClose //判断弹框是否关闭
            ) {
                self.hasClose = true;
                self.getSelectData();
            }
        });

        //提交要添加的单品
        $('#selectWindow #selectOkBtn').on('click', function(e) {
            var $selectArea = $('#selectWindow .select_content_area'),
                resList = [];

            showLoading();
            $selectArea.find(':checkbox:checked').each(function(i, item) {
                var resObj = {
                    resource_id: item.getAttribute('id'),
                    resource_type: item.getAttribute('value')
                };
                resList[resList.length] = resObj;
            });
            /*参数:
            1-channel_type(1-单品,2-专栏,3-会员);
            2-resource_list(选中的资源集合(数组),
            键值对的格式如:resource_id:a_kjjak345,resource_type:1);
            3-package_id(当channel_type=2、3时,有值);
            4-piece_price(但channel_type=1时有值)*/
            $.ajax('/submit_choice_resource', {
                type: 'POST',
                dataType: 'json',

                data: {
                    channel_type: 2,
                    resource_list: resList,
                    package_id: self.id,
                },
                success: function(data) {
                    console.log(data);
                    if (data.code == 0) {
                        //选择成功，主动关闭选择弹窗
                        $('#selectWindow #selctClose').click();
                        baseUtils.show.blueTip(data.msg);
                        //刷新页面
                        setTimeout(function() {
                            reloadPage();
                        }, 700);
                    } else {
                        baseUtils.show.redTip(data.msg);
                        hideLoading();
                    }
                },
                error: function(xhr, status, err) {
                    console.error(err);
                    baseUtils.show.redTip('操作失败，请稍后再试');
                    hideLoading();
                }
            });
        });


    },
    setSingleSale: function(resType, resTitle, isTry, showTime, resImgUrl, resId) {
        //	设置单卖
        //	1、判断单品是否为试听/ 试看 -->
        //  2、若为试听： 弹窗 提示
        //  3、若不为试听： 弹窗 设置单品售价

        if (isTry == 1) { //是
            if (resType == 2) {
                $.alert("该单品已设为试听，取消试听后方可设为单卖", "info", {
                    btn: 2,
                });
            } else if (resType == 1 || resType == 3 || resType == 4) {
                $.alert("该单品已设为试看，取消试看后方可设为单卖", "info", {
                    btn: 2,
                });
            }

        } else {
            this.initSetSingleSaleWindow(resImgUrl, resTitle, resType, showTime, resId);
            $(".set_price_window").fadeIn(300);
        }

    },
    cancelSingleSale: function(resType, resId, paymentType) {
        // 	取消单卖
        //	1、弹窗提示
        if (paymentType == 1) {
            var text = "取消单卖后，未开通专栏的用户将不可单独查看该单品!";
        } else if (paymentType == 2) {
            var text = "取消单卖后，未开通专栏的用户将不可单独付费购买该单品!";
        }
        $.alert(text, "info", {
            onOk: function() {
                showLoading();
                var arr = {};
                arr["resource_id"] = resId;
                arr["resource_type"] = resType;
                var resourceListArr = [arr];

                $.ajax("/submit_choice_resource", {
                    type: "POST",
                    data: {
                        "channel_type": 1,
                        "resource_list": resourceListArr,
                        "submit_type": 1
                    },
                    success: function(result) {
                        hideLoading();
                        if (result.code == 0) {
                            baseUtils.show.blueTip("取消单卖成功！");
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            baseUtils.show.redTip("操作失败，请稍后再试!");
                        }
                    },
                    error: function(xhr, status, err) {
                        hideLoading();
                        console.log(err);
                        baseUtils.show.redTip("服务器开小差啦，请稍后再搜索！");

                    }
                });
            }
        });
        //	2、确定取消


    },
    //	初始化设置专栏外单卖
    initSetSingleSaleWindow: function(resImgUrl, resTitle, resType, showTime, resId) {

        //  给设置价格填充数据
        $(".resource_icon_3").attr("src", resImgUrl);
        $(".resource_title").html(resTitle);
        switch (resType) {
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
        $(".resource_time_3").html(showTime);

        // $("input[name='set_sale_price'][value='1']").attr("checked", "checked");
        //
        // // 	设置单品售价绑定事件
        // $("input[name='set_sale_price']").on("change", function () {
        //     var saleType = $("input[name='set_sale_price']:checked").val();
        //
        //     if (saleType == 1) {	//	付费
        //         $(".input_setting_price").removeAttr("readonly");
        //         $(".input_setting_price").removeAttr("disabled");
        //     } else if (saleType == 0) {		// 免费
        //         $(".input_setting_price").attr("readonly", "readonly");
        //         $(".input_setting_price").attr("disabled", "disabled");
        //     }
        //
        // });

        // 	取消
        $(".close_icon_wrapper_2, .cancel_set_price").click(function() {
            $(".set_price_window").fadeOut(300);
        });
        //	确定
        $(".confirm_price_btn").click(function() {

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
            if (price > baseUtils.maxInputPrice) {
                baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
                return false;
            }


            var arr = {};
            arr["resource_id"] = resId;
            arr["resource_type"] = resType;
            var resourceListArr = [arr];

            $.ajax("/submit_choice_resource", {
                type: "POST",
                data: {
                    "channel_type": 1,
                    "resource_list": resourceListArr,
                    "piece_price": price * 100, // 单位： 分
                    "submit_type": 0
                },
                success: function(result) {
                    if (result.code == 0) {
                        $(".set_price_window").fadeOut(300);
                        baseUtils.show.blueTip("设为单卖成功");
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        baseUtils.show.redTip("操作失败，请稍后再试!");
                    }
                },
                error: function(xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器开小差啦，请稍后再搜索！");

                }
            });
        });

    },
    initSearchPart: function() {
        var self = this;
        //  1、开始搜索 - 全部单品
        $(".searchAllBtn").click(function() {
            var packageId = GetQueryString("id");
            var resource_type = $(".selector_resource_type").val();
            var search_content = $.trim($(".inputSearchAll").val());

            window.location.href = "/package_detail_page?id=" + packageId + "&search_content=" + search_content + "&resource_type=" + resource_type;

        });

        // 2、输入完成后，回车开始搜索 - 全部单品
        $(".inputSearchAll").focusin(function() {
            $(document).keypress(function(e) {
                if (e.which == 13) {
                    $('.searchAllBtn').click();
                }
            });
        }).focusout(function() {
            $(document).off('keypress'); //  清除事件
        });

        //  监听筛选框选择事件
        $(".selector_resource_type").on('change', function () {
            $('.searchAllBtn').click();
        });

        // 3、回显搜索
        if (self.searchResourceName != "") {
            $(".inputSearchAll").val(self.searchResourceName);
        }

        var num = $(".selector_resource_type").children("option").length;
        if (num > 0) {
            for (var i = 0; i < num; i++) {
                if ($('.selector_resource_type').children("option").eq(i).attr("value") == self.searchResourceType) {
                    $('.selector_resource_type').children("option").eq(i).attr("selected", "selected");
                    return;
                }
            }
        }
    },
    initCopy: function(className) {
        var clipboard = new Clipboard('.' + className); //加载复制链接插件
        clipboard.on('success', function(e) {
            if(e.trigger.id=='copy_app_href'){
                baseUtils.show.blueTip("复制成功！");
            }else{
                baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            }

            e.clearSelection();
        });
    },
    maintool: function(type, id) { //专栏工具集
        self.btnState = false;
        switch (type) {
            case 'newListShow':
                this.newListShow(id, false);
                break;
            case 'newListHide':
                this.newListShow(id, true);
                break;
            case 'stopupdate':
                this.packageUpdate(id, false);
                break;
            case 'startupdate':
                this.packageUpdate(id, true);
                break;
            case 'soldout':
                this.packageisShow(id, false);
                break;
            case 'putaway':
                this.packageisShow(id, true);
                break;
            case 'openForm':
                this.packageForm(id, false);//传入当前关闭状态的false值
                break;
            case 'closeForm':
                this.packageForm(id, true);//传入当前开启状态的真值
                break;
            default:
                console.error('参数错误');
                break;
        }
    },
    newListShow: function(id, showType) {
        var h5_newest_hide = showType ? 1 : 0;
        console.log(h5_newest_hide);
        console.log(id);
        showLoading();
        $.ajax('/set_h5_newest_hide', {
            type: 'POST',
            dataType: 'json',
            data: {
                //参数:1-package_id;2-h5_newest_hide(0-最新列表中显示,1-最新列表中不显示
                id: id,
                //0， 1下
                h5_newest_hide: showType ? 1 : 0
            },
            success: function(data) {
                //console.log(data);
                if (data.code == 0) {
                    baseUtils.show.blueTip(data.msg);
                    setTimeout(function() {
                        reloadPage();
                    }, 700);
                } else {
                    hideLoading();
                    baseUtils.show.redTip(data.msg);
                    self.btnState = true;
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.btnState = true;
                baseUtils.show.redTip('操作失败，请稍后再试');

            }
        });
    },
    packageUpdate: function(id, stateType) {
        showLoading();
        $.ajax('/save_package_finished_state', {
            type: 'POST',
            dataType: 'json',
            data: {
                //参数:1-package_id;2-finished_state(0-未完结,1-已完结
                package_id: id,
                //0上移， 1下移
                finished_state: stateType ? 0 : 1
            },
            success: function(data) {
                //console.log(data);
                if (data.code == 0) {
                    baseUtils.show.blueTip(data.msg);
                    setTimeout(function() {
                        reloadPage();
                    }, 700);
                } else {
                    hideLoading();
                    baseUtils.show.redTip(data.msg);
                    self.btnState = true;
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.btnState = true;
                baseUtils.show.redTip('操作失败，请稍后再试');

            }
        });
    },
    packageForm: function(id,formType){
        // id:专栏是否要设置支付时填写信息的专栏id，formType ：true-允许 false-禁止
        var version=$(".user_version_num").data("version_type");
        if(version && version==3) {
            $.alert(formType ? "关闭资料填写后，用户在付费购买本专栏前将不再填写个人信息，您确认要关闭么？" :
                "开启资料填写后，用户在付费购买本专栏前将填写个人信息，您确认要开启么？", "info", {
                icon: "blue",
                onOk: function () {
                    showLoading();
                    $.ajax('/set_is_complete_info', {
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: id,
                            is_complete_info: formType
                        },
                        success: function (data) {
                            if (data.code == 0) {
                                baseUtils.show.blueTip(data.msg);
                                setTimeout(function () {
                                    reloadPage();
                                }, 200);
                            } else {
                                baseUtils.show.redTip(data.msg);
                                hideLoading();
                            }

                        }

                    })
                }
            })
        }else{
            baseUtils.show.redTip("当前版本不支持开启资料填写，如需开启请升级至专业版");
        }
    },
    packageisShow: function(id, changeType) { //专栏上下架
        /*id: 要上下架的专栏id， changeType： true-上架， false-下架*/

        $.alert(changeType ? "上架后该专栏将对外显示" : "下架后该专栏将不对外显示", "info", {
            onOk: function() {
                showLoading();
                $.ajax('/change_goods_state', {
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        /*
                        1-goods_id;
                        2-goods_type(0-专栏,1-图文,2-音频,3-视频,4-直播);
                        3-operate_type(0-上架,1-下架)
                        */
                        goods_id: id,
                        goods_type: 0,
                        operate_type: changeType ? 0 : 1,
                    },
                    success: function(data) {
                        //console.log(data);
                        if (data.code == 0) {
                            baseUtils.show.blueTip(data.msg);
                            setTimeout(function() {
                                reloadPage();
                            }, 700);
                        } else {
                            baseUtils.show.redTip(data.msg);
                            hideLoading();
                            self.btnState = true;
                        }
                    },
                    error: function(xhr, status, err) {
                        console.error(err);
                        hideLoading();
                        self.btnState = true;
                        baseUtils.show.redTip('操作失败，请稍后再试');
                    }
                });

            }
        });

    },
    singleTool: function(type, packageid, resType, resid, resTitle, isTry, showTime, resImgUrl, paymentType) { //单品工具集
        self.btnState = false;
        switch (type) {
            case 'totop':
                this.setTop(packageid, resType, resid, true);
                break;
            case 'canceltop':
                this.setTop(packageid, resType, resid, false);
                break
            case 'aliveGuestSet':
                if (resType == 4) {
                    $inviteGuest.openGuestSettingWindow(resid);
                }
                break
            case 'showComment':
                if (resType == 4) {
                    contentDetail("/alivecomment?alive_id=" + resid);
                } else {
                    contentDetail("/comment_admin?type=" + (resType - 1) + "&record_id=" + resid);
                }
                break;
            case 'setTry':
                this.setTry(packageid, resType, resid, paymentType, true);
                break;
            case 'cancelTry':
                this.setTry(packageid, resType, resid, paymentType, false);
                break;
            case 'set_single_sale':
                //	设置单卖
                //	1、判断单品是否为试听/ 试看 -->
                //  2、若为试听： 弹窗 提示
                //  3、若不为试听： 弹窗 设置单品售价
                this.setSingleSale(resType, resTitle, isTry, showTime, resImgUrl, resid);
                break;
            case 'cancel_single_sale':
                // 	取消单卖
                //	1、弹窗提示
                //	2、确定取消
                this.cancelSingleSale(resType, resid, paymentType);
                break;
            case 'soldout':
                this.singleisShow(packageid, resType, resid, true);
                break;
            case 'putaway':
                this.singleisShow(packageid, resType, resid, false);
                break;
            case 'del':
                this.querySingle(packageid, resType, resid);
                break;
            case 'enable_copy'://允许复制
                this.changeCanSelectState(resid, true);
                break;
            case 'forbid_copy'://禁止复制
                var $ele = this;
                $.alert("禁止复制后，该篇图文的图片点击放大和长按识别二维码功能将被禁用", "info", {
                    btn: 3,
                    onOk: function () {
                        $ele.changeCanSelectState(resid, false);
                    }
                });
                break;
            case 'export_audio'://导出音频
                this.exportAudio(resid);
                break;
            case 'copy_app_href': //复制小程序链接
                break;

            default:
                console.error('参数错误');
                break;
        }
    },
    exportAudio: function (aliveId) {

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

    },
    changeCanSelectState: function (resId, canSelect) {

        $.ajax("/set_resource_select_can", {
            type: "POST",
            dataType: "json",
            data: {
                id: resId,
                resource_type: 1,
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

    },
    setTop: function(packageid, resType, resid, top) { //置顶操作
        showLoading();
        /*1-package_id;
        2-resource_type(0-专栏,1-图文,2-音频,3-视频,4-直播);
        3-resource_id;
        4-top_state(0-取消置顶,1-设置置顶)*/
        $.ajax('/set_package_resource_top', {
            type: 'POST',
            dataType: 'json',
            data: {
                package_id: packageid,
                resource_type: resType,
                resource_id: resid,
                top_state: top ? 1 : 0
            },
            success: function(data) {
                if (data.code == 0) {
                    baseUtils.show.blueTip(data.msg);
                    setTimeout(function() {
                        reloadPage();
                    }, 700);
                } else {
                    hideLoading();
                    self.btnState = true;
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.btnState = true;
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        });
    },
    setTry: function(packageid, resType, resid, paymentType, tryState) { //试听

        // if (resType == 4) {
        //     baseUtils.show.redTip("暂不支持将直播设为试看!");
        //     return false;
        // }
        if (paymentType == 1 || paymentType == 2) {
            if (resType == 2) {
                $.alert("该单品已设为单卖，取消单卖后方可设为试听", "info", {
                    btn: 2,
                });
            } else if (resType == 1 || resType == 3 || resType == 4) {
                $.alert("该单品已设为单卖，取消单卖后方可设为试看", "info", {
                    btn: 2,
                });
            }
            return false;
        }

        showLoading();
        $.ajax('/set_package_resource_try', {
            type: 'POST',
            dataType: 'json',
            /*参数:
            1-package_id;
            2-resource_type(2-音频);
            3-resource_id;
            4-try_state(0-取消试听,1-设为试听)*/
            data: {
                package_id: packageid,
                resource_type: resType,
                resource_id: resid,
                try_state: tryState ? 1 : 0
            },
            success: function(data) {
                if (data.code == 0) {
                    baseUtils.show.blueTip(data.msg);
                    setTimeout(function() {
                        reloadPage();
                    }, 700);
                } else {
                    hideLoading();
                    self.btnState = true;
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.btnState = true;
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        })
    },
    singleisShow: function(packageid, resType, resid, showState) { //单品上下架
        /*showState: true-下架  false-上架*/
        var self = this,
            text = showState ? '下架后该单品将不对外显示' : '上架后该单品将对外显示';
        $.alert(text, 'info', {
            title: '',
            onOk: function() {
                showLoading();
                $.ajax('/change_goods_state', {
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        /*
                        1-goods_id;
                        2-goods_type(0-专栏,1-图文,2-音频,3-视频,4-直播);
                        3-operate_type(0-上架,1-下架)
                        */
                        goods_id: resid,
                        goods_type: resType,
                        operate_type: showState ? 1 : 0,
                    },
                    success: function(data) {
                        //console.log(data);
                        if (data.code == 0) {
                            baseUtils.show.blueTip(data.msg);

                            setTimeout(function() {
                                reloadPage();
                            }, 700);
                        } else {
                            hideLoading();
                            self.btnState = true;
                            /*self.toolBoxBtn = true;
                            self.toDetail = true;*/
                            baseUtils.show.redTip(data.msg);
                        }
                    },
                    error: function(xhr, status, err) {
                        console.error(err);
                        hideLoading();
                        self.btnState = true;
                        /*self.toolBoxBtn = true;
                        self.toDetail = true;*/
                        baseUtils.show.redTip('操作失败，请稍后再试');
                    }
                });
            }
        });
    },
    querySingle: function(packageid, resType, resid) {
        /*showState: true-下架  false-上架*/
        var self = this,
            text = '';
        $.ajax('/query_goods_state', {
                type: 'POST',
                dataType: 'json',
                /*参数:
                1-goods_id;
                2-goods_type(1-图文,2-音频,3-视频,4-直播);
                3-channel_type(0-单品,1-专栏内资源);
                4-package_id(当channel_type=1时有值)*/
                data: {
                    goods_id: resid,
                    goods_type: resType,
                    channel_type: 1,
                    package_id: packageid
                }
            })
            .then(function(result) {
                if (result.code == 0) {

                    var recordsList = result.data.records_list,
                        arrLength = Object.keys(recordsList).length;

                    if (result.data.is_exist == 0) {            //  只属于某个专栏的单品
                        text = '确认移除该内容，移除后所有用户将无法查看此内容且<spa style="color: red;">不可恢复</spa>';
                    } else if (recordsList[0] == undefined) {      //  只属于多个专栏的单品

                        $.each(recordsList, function (key, value) {
                            text += "《" + value.title + "》";
                        })
                        text = "确认移除该内容，移除后该专栏用户将无法查看此内容。此内容仍存在于" + text + "中";

                    }  else {
                        if (arrLength == 1) {                   //  单品单卖且属于一个专栏
                            text = "确认移除该内容，移除后该专栏用户将无法查看此内容。此内容仍在单品列表且单独售卖";

                        } else {                                //  单品单卖且属于多个专栏

                            $.each(recordsList, function (key, value) {
                                if (key != 0) {
                                    text += "《" + value.title + "》";
                                }
                            })
                            text = "确认移除该内容，移除后该专栏用户将无法查看此内容。此内容单独售卖且仍存在于" + text + "中";

                        }
                    }
                    return text;
                } else {
                    baseUtils.show.redTip(result.msg)
                    return false;
                }
            }, function(xhr, status, err) {
                console.error(err);
                baseUtils.show.redTip('操作失败，请稍后再试');
            })
            .then(function(text) {
                if (text) {
                    $.alert(text, 'info', {
                        oktext: '确认移除',
                        onOk: function() {
                            self.deleteSingle(packageid, resType, resid);
                        }
                    });
                }
            });
    },
    deleteSingle: function(packageid, resType, resid) {
        showLoading();
        $.ajax('/move_goods', {
            type: 'POST',
            dataType: 'json',
            data: {
                /*参数:
                1-goods_id;
                2-goods_type(1-图文,2-音频,3-视频,4-直播);
                3-channel_type(0-单品,1-专栏内资源);
                4-package_id(当channel_type=1时有值)
                */
                goods_id: resid,
                goods_type: resType,
                channel_type: 1,
                package_id: packageid
            },
            success: function(data) {
                console.log(data);
                if (data.code == 0) {
                    baseUtils.show.blueTip(data.msg);

                    setTimeout(function() {
                        reloadPage();
                    }, 700);
                } else {
                    hideLoading();
                    self.btnState = true;
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.btnState = true;
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        });
    },

    getSelectData: function() {
        var self = this;
        $.ajax('/choice_resource_list', {
            type: 'POST',
            dataType: 'json',
            /*参数:
            1-channel_type(1-单品,2-专栏,3-会员)
            2-搜索内容-search_content;
            3-resource_type(0-全部,1-图文,2-音频,3-视频,4-直播);
            4-page(分页页码);
            5-package_id(当channel_type=2、3时,有值)
            */
            data: {
                search_content: self.searchContent || '',
                channel_type: 2,
                resource_type: 0,
                package_id: self.id,
                page: self.page
            },
            success: function(result) {
                console.log(result);
                if (result.code == 0) {
                    var $selectArea = $('#selectAreaList'),
                        selectStr = '',
                        pageObj = JSON.parse(result.data.page_offset);
                    $.each(result.data.resource_list, function(i, item) {
                        var resource_type = '';
                        switch (+this.resource_type) {
                            case 1:
                                resource_type = '图文';
                                break;
                            case 2:
                                resource_type = '音频';
                                break;
                            case 3:
                                resource_type = '视频';
                                break;
                            case 4:
                                resource_type = '直播';
                                break;
                            default:
                                resource_type = '无';
                                break;

                        }
                        selectStr += '<div class="resourceItemWrapper">' +
                            '<input value="' + this.resource_type + '" id="' + this.id + '" class="with-gap singleResourceRadio" name="select_resource" type="checkbox">' +
                            '<label for="' + this.id + '" class="radio_desc">' +
                            '<div class="resource_icon_wrapper">' +
                            '<img class="resource_icon" src="' + this.img_url + '">' +
                            '</div>' +
                            this.title +
                            '</label>' +
                            '<span class="resource_type">' + resource_type + '</span>' +
                            '<span class="resource_time">' + this.start_at + '</span>' +
                            '</div>';
                    });
                    if (selectStr.length == 0) {
                        selectStr +=
                            '<div class="contentNoneTip">暂无数据</div>';
                    }
                    if (self.page == 1 || !self.page) {
                        $selectArea.html(selectStr);
                        $selectArea.append('<div class="isDown">资源加载中</div>');
                        self.hasClose = false;
                        hideLoading();
                        //首次加载，显示
                        if (!self.searchContent) {
                            $('#selectWindow').fadeIn();
                        }
                    } else if (self.page > 1) {
                        $selectArea.find('.isDown').before(selectStr);
                        self.hasClose = false;
                    }

                    //数据加载完毕后的操作
                    if (pageObj.current_page >= pageObj.total_pages) {
                        $selectArea.find('.isDown').hide();
                        self.hasDown = true;
                    }
                    self.page++;

                } else {
                    hideLoading();
                    baseUtils.show.redTip("拉取已有单品失败！");
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                baseUtils.show.redTip('操作失败，请稍后再试');
                hideLoading();
            }
        });
    }
}
