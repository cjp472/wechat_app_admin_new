/**
 * Created by Administrator on 2017/3/21.
 */

$(document).ready(function() {
    Business.init();
    $inviteGuest.init();
    $aliveShowSet.init();
});

var Business = (function() {

    var Business = {};

    Business.isLoading = false;
    Business.tabState = "singleList";  // 默认显示专栏列表     （"packageList", "singleList"）

    Business.member_id = -1;
    Business.member_price = 0;

    //  搜索
    Business.searchContentForSingle = "";
    Business.searchContentForPackage = "";
    Business.resource_type = 0;

    Business.pageIndex = 1;

    Business.init = function() {
        //  从当前URL中取出参数
        Business.member_id = GetQueryString("id");
        modal.id = GetQueryString("id");
        Business.member_price = $(".admin_data").data("member_price");

        //打开直播窗口
        var aliveId = sessionStorage.getItem("inviteGuestAliveId");
        if (aliveId && aliveId != "") {
            sessionStorage.setItem("inviteGuestAliveId", "");
            $inviteGuest.openGuestSettingWindow(aliveId);
        }


        //  针对会员信息的操作
        $('.toolBox').on("click", "li", function() {
            var type = $(this).data("type");
            switch (type) {
                case "edit"://  编辑
                    window.location.href = "/edit_member_page?id=" + Business.member_id + "&page_origin=member_detail";
                    break;
                case "newListShow"://在最新列表中显示
                    memberDetailUtils.changeNewlListState(Business.member_id, 0, 0);
                    break;
                case "newListHide"://在最新列表中显示
                    memberDetailUtils.changeNewlListState(Business.member_id, 0, 1);
                    break;
                case "show_member"://  上架
                    memberDetailUtils.changeGoodsState(Business.member_id, 0, 0);
                    break;
                case "hide_member"://  下架
                    memberDetailUtils.changeGoodsState(Business.member_id, 0, 1);
                    break;
                case "openForm"://  开启资料填写
                    memberDetailUtils.packageForm(Business.member_id, 1);
                    break;
                case "closeForm"://  关闭资料填写
                    memberDetailUtils.packageForm(Business.member_id, 0);
                    break;
                case "openVisible"://  开启内容展示
                    memberDetailUtils.visibleSwitch(Business.member_id, 1);
                    break;
                case "closeVisible"://  关闭内容展示
                    memberDetailUtils.visibleSwitch(Business.member_id, 0);
                    break;
                default:
                    console.log("会员操作，参数错误。");
                    break;
            }

        });

        //  针对单品列表中每一个条目的操作
        $(".member_list_wrapper").on("click", ".hover_tool_box li.operate", function(e) {
            //  阻止事件向下传递
            e.stopPropagation();

            //  当前条目的资源id
            var resId = $(this).parents(".member_list_item").data("resource_id"),
                resType = $(this).parents(".member_list_item").data("resource_type"),
                resTitle = $(this).parents(".member_list_item").data("res_title"),
                isTry = $(this).parents(".member_list_item").data("is_try"),
                showTime = $(this).parents(".member_list_item").data("show_time"),
                resImgUrl = $(this).parents(".member_list_item").data("img_url"),
                paymentType = $(this).parents(".member_list_item").data("payment_type");

            var type = $(this).data("type");
            switch (type) {
                case "to_top"://  置顶
                    memberDetailUtils.setResourceTop(Business.member_id, resType, resId, 1);
                    break;
                case "cancel_top"://  取消置顶
                    memberDetailUtils.setResourceTop(Business.member_id, resType, resId, 0);
                    break;
                case "aliveGuestSet"://  嘉宾设置
                    if (resType == 4) {
                        $inviteGuest.openGuestSettingWindow(resId);
                    }
                    break;
                case "look_comments"://  查看评论
                    if (resType == 4) {
                        contentDetail("/alivecomment?alive_id=" + resId);
                    } else {
                        contentDetail("/comment_admin?type=" + (resType - 1) + "&record_id=" + resId);
                    }
                    break;
                case "edit"://  编辑
                    window.location.href = "/edit_resource_page?type=" + resType + "&id=" + resId +
                        "&upload_channel_type=3" + "&package_id=" + Business.member_id + "&price=" + Business.member_price;
                    break;
                case "set_try"://  设为试听 或 试看
                    memberDetailUtils.setResourceTry(Business.member_id, resType, resId, paymentType, 1);
                    break;
                case "cancel_try"://  取消试听 或 试看
                    memberDetailUtils.setResourceTry(Business.member_id, resType, resId, paymentType, 0);
                    break;
                case "export_audio"://  导出音频
                    memberDetailUtils.exportAudio(resId);
                    break;
                case "set_single_sale"://  设置单卖
                    memberDetailUtils.setSingleSale(resType, resId, resTitle, isTry, showTime, resImgUrl);
                    break;
                case "cancel_single_sale"://  取消单卖
                    memberDetailUtils.cancelSingleSale(resType, resId, paymentType);
                    break;
                case "show_resource"://  上架
                    memberDetailUtils.changeGoodsState(resId, resType, 0);
                    break;
                case "hide_resource"://  下架
                    memberDetailUtils.changeGoodsState(resId, resType, 1);
                    break;
                case "delete"://  移除该商品
                    memberDetailUtils.querySingleResource(resId, resType, Business.member_id);
                    break;
                case 'enable_copy'://允许复制
                    memberDetailUtils.changeCanSelectState(resId, true);
                    break;
                case 'forbid_copy'://禁止复制
                    $.alert("禁止复制后，该篇图文的图片点击放大和长按识别二维码功能将被禁用", "info", {
                        btn: 3,
                        onOk: function () {
                            memberDetailUtils.changeCanSelectState(resId, false);
                        }
                    });
                    break;
                default:
                    console.error('参数错误');
                    break;
            }

        });

        //  针对专栏列表中每一个条目的操作
        $(".member_list_wrapper").on("click", "#package_item_operate li.operate", function (e) {
            e.stopPropagation();
            var packageId = $(this).parents(".package_list_item").data("package_id"),
                type = $(this).data("type");
            switch (type) {
                // case "to_top":  //置顶
                //     memberDetailUtils.setResourceTop(Business.member_id, 6, packageId, 1);
                //     break;
                // case "cancel_top":  //置顶
                //     memberDetailUtils.setResourceTop(Business.member_id, 6, packageId, 0);
                //     break;
                case 'soldout':
                    memberDetailUtils.packageisShow(packageId, false);
                    break;
                case 'putaway':
                    memberDetailUtils.packageisShow(packageId, true);
                    break;
                case "edit_package":    //编辑
                    window.location.href = "edit_package_page?id=" + packageId + "&member_id=" + modal.id;
                    break;
                case "remove_package":  //移除该权益
                    memberDetailUtils.queryPackageResource(packageId);
                    break;
                default:
                    console.log("参数错误");
                    break;
            }
        });

        //  操作 - 复制到剪贴板
        (function() {
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

        // 专栏 + 单品 tab 切换
        $(".content_tab_wrapper li").click(function () {
            if (!Business.isLoading) {
                $(".inputSearchAll").val("");
                Business.pageIndex = 1;     //切换后，分页页码置为1
                Business.isLoading = true;  //记录加载状态，防重复点击
                var text = $(this).text();
                if (!$(this).hasClass("activeContentTab")) {
                    if (text == "专栏") {
                        $("#packageTab").addClass("activeContentTab");
                        $("#singleTab").removeClass("activeContentTab");
                        $(".selector_resource_type").addClass("hide");
                    } else if (text == "单品") {
                        $("#singleTab").addClass("activeContentTab");
                        $("#packageTab").removeClass("activeContentTab");
                        $(".selector_resource_type").removeClass("hide");
                    }
                }
                if (text == "专栏") {
                    Business.searchContentForPackage = "";
                    Business.tabState = "packageList";
                    memberDetailUtils.switchWindowTab("packageTab");
                    modal.initPackageListOfMember();

                } else if (text == "单品") {
                    Business.searchContentForSingle = "";
                    Business.resource_type = 0;
                    $('#firstOption').removeAttr("selected").attr("selected","selected");
                    Business.tabState = "singleList";
                    memberDetailUtils.switchWindowTab("singleTab");
                    modal.initResourceListOfMember();

                }
            }


        });

        //  初始化选择已有单品
        modal.selectResource();

        //  初始化搜索筛选
        modal.initResourceAndPackageSearch();

        //  初始化列表的分页
        memberDetailUtils.initListPagination();

        //分页请求
        $(".member_list_wrapper").on('click', ' .pagination li a', function(e) {
            var ele = $(e.target),
                url = ele.data('url');
            Business.pageIndex = ele.text();
            if (Business.tabState == "packageList") {
                modal.initPackageListOfMember();
            } else if (Business.tabState == "singleList") {
                modal.initResourceListOfMember();
            }
        });

        //吴晓波专用自定义内容列表跳转按钮
        $(".userDefined").click(function(){
            var pid = $('.admin_data').data("member_id");
            window.location.href="/user_defined?id=" + pid;
        });

    };

    return Business;

})();

var modal = (function() {

    var modal = {};

    /**
     * 模块一：初始化单品列表
     */
    modal.initResourceListOfMember = function() {
        $(".loadingS").fadeIn(300);

        $.ajax("/singe_list_member", {
            type: "GET",
            dataType: "html",
            data: {
                "id": Business.member_id,
                "search_content": Business.searchContentForSingle,
                "resource_type": Business.resource_type,
                "page": Business.pageIndex
            },
            success: function(result) {
                Business.isLoading = false;
                $(".loadingS").fadeOut(100);
                if (result && result.length > 0) {
                    $(".member_list_wrapper").html(result);
                    memberDetailUtils.initListPagination();//初始化分页按钮
                } else {
                    console.log("请求数据为空");
                    baseUtils.show.redTip("网络问题，请稍后再试");
                }
            },
            error: function(xhr, status, err) {
                Business.isLoading = false;
                $(".loadingS").fadeOut(100);
                console.log(err);
                baseUtils.show.redTip("获取单品列表失败");
            }
        });

    };

    /**
     * 模块二：初始化专栏列表
     */
    modal.initPackageListOfMember = function() {
        $(".loadingS").fadeIn(300);
        $.ajax("/package_list_member", {
            type: "GET",
            dataType: "html",
            data: {
                id: Business.member_id,
                search_content: Business.searchContentForPackage,
                page: Business.pageIndex
            },
            success: function(result) {
                Business.isLoading = false;
                $(".loadingS").fadeOut(100);
                if (result && result.length > 0) {
                    $(".member_list_wrapper").html(result);
                    memberDetailUtils.initListPagination();//初始化分页按钮

                } else {
                    console.log("请求数据为空");
                    baseUtils.show.redTip("网络问题，请稍后再试");
                }
            },
            error: function(xhr, status, err) {
                Business.isLoading = false;
                $(".loadingS").fadeOut(100);
                console.log(err);
                baseUtils.show.redTip("获取单品列表失败");
            }
        });
    };

    /**
     *  模块三 ： 选择已有单品
     */
    modal.hasDown = false;
    modal.hasClose = true;
    modal.page = 1; //添加已有单品的页码
    modal.searchContent = '';

    modal.isLoading = false;    //是否处于加载状态
    modal.resType = 0;  //  6-获取专栏信息， 0-获取单品信息
    modal.tabState = "singleList";  // 默认显示单品列表     （"packageList", "singleList"）


    modal.selectResource = function() {
        var self = this;
        $('#addSingle').on('click', function() { //弹出选择框
            self.page = 1;
            self.hasDown = false;
            self.hasClose = false;
            self.getSelectData();
        });

        $('#selectWindow #selctClose, #selectWindow #selectCancelBtn').on('click', function() { //关闭选择弹窗
            var $selectWin = $('#selectWindow');
            $selectWin.fadeOut('normal', function() {
                $(this).find('#selectAreaList').html('');
            });
            $selectWin.find('.inputSearchPart').val(''); //清空搜索框
            self.page = 1;
            self.hasClose = true;
            self.searchContent = '';
        });

        $('#selectWindow #selectSearchBtn').on('click', function() { //选择框的单品搜索
            var searchVal = $.trim($(this).prev().val());
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

        //tab切换
        $(".selectGoodTabWrapper li").click(function () {
            var value = $(this).val();

            if (!modal.isLoading) {
                self.page = 1;
                $('#selectWindow #selectAreaListWrapper').scrollTop(0);
                modal.isLoading = true;  //记录加载状态，防重复点击

                if (value == 1) {
                    memberDetailUtils.switchWindowTab("singleTab");
                } else {
                    memberDetailUtils.switchWindowTab("packageTab");
                }
                self.getSelectData();

            }

        });

        $('#selectWindow #selectAreaListWrapper').scroll(function(e) {
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
            } else {
                // $("#selectAreaList .isDown").addClass("hide");
            }
        });

        //提交要添加的单品
        $('#selectWindow #selectOkBtn').on('click', function(e) {
            var $selectArea = $('#selectWindow #selectAreaListWrapper'),
                resList = [];

            $selectArea.find(':checkbox:checked').each(function(i, item) {
                if (modal.tabState == "packageList") {
                    var resObj = {
                        resource_id: item.getAttribute('id'),
                        resource_type: 6
                    };
                } else if (modal.tabState == "singleList") {
                    var resObj = {
                        resource_id: item.getAttribute('id'),
                        resource_type: item.getAttribute('value')
                    };
                }
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
                    channel_type: 3,
                    resource_list: resList,
                    package_id: self.id,
                },
                success: function(data) {
                    console.log(data);
                    if (data.code == 0) {

                        $('#selectWindow #selctClose').click(); //选择成功，主动关闭选择弹窗
                        baseUtils.show.blueTip(data.msg);
                        if (modal.tabState == "packageList") {
                            $("#packageTab").click();
                        } else if (modal.tabState == "singleList") {
                            $("#singleTab").click();
                        }
                    } else {
                        baseUtils.show.redTip(data.msg);
                    }
                },
                error: function(xhr, status, err) {
                    console.error(err);
                    baseUtils.show.redTip('操作失败，请稍后再试');
                }
            });
        });

    };

    modal.getSelectData = function() {
        var self = this;
        if (self.page == 1) {
            $(".loadingS_in_window").fadeIn(300);
        }
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
                channel_type: 3,
                resource_type: self.resType,
                package_id: self.id,
                page: self.page
            },
            success: function(result) {
                $(".loadingS_in_window").fadeOut(300);
                modal.isLoading = false;
                console.log(result);
                if (result.code == 0) {
                    var $selectArea = $('#selectAreaList'),
                        selectStr = '',
                        pageObj = JSON.parse(result.data.page_offset);
                    $.each(result.data.resource_list, function(i, item) {
                        var resource_type = '';
                        if (self.resType == 0) {
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
                        }
                        if (self.resType == 0) {
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
                        } else {
                            selectStr += '<div class="resourceItemWrapper">' +
                                '<input' + ' id="' + this.id + '" class="with-gap singleResourceRadio" name="select_resource" type="checkbox">' +
                                '<label for="' + this.id + '" class="radio_desc">' +
                                '<div class="resource_icon_wrapper">' +
                                '<img class="resource_icon" src="' + this.img_url + '">' +
                                '</div>' +
                                this.name +
                                '</label>' +
                                '<span class="resource_time">' + this.updated_at + '</span>' +
                                '</div>';
                        }
                    });
                    if (self.page == 1 || !self.page) {
                        $selectArea.html(selectStr);
                        $selectArea.append('<div class="isDown">资源加载中</div>');
                        self.hasClose = false;
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
                    baseUtils.show.redTip("拉取已有单品失败！");
                }
            },
            error: function(xhr, status, err) {
                $(".loadingS_in_window").fadeOut(300);
                modal.isLoading = false;
                console.error(err);
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        });
    }

    /**
     * 模块四： 单品/专栏列表的搜索
     */
    modal.initResourceAndPackageSearch = function() {

        //  1、开始搜索、单品/专栏
        $(".searchAllBtn").click(function() {

            if (Business.tabState == "packageList") {
                Business.searchContentForPackage = $.trim($(".inputSearchAll").val());
                modal.initPackageListOfMember();
            } else if (Business.tabState == "singleList") {
                Business.resource_type = $(".selector_resource_type").val();
                Business.searchContentForSingle = $.trim($(".inputSearchAll").val());
                modal.initResourceListOfMember();
            }


        });

        // 1、输入完成后，回车开始搜索 - 全部单品/专栏
        $(".inputSearchAll").on('keypress', function (e) {
            if (e.keyCode == 13) {
                $('.searchAllBtn').click();
            }
        });

        //  监听筛选框选择事件
        $(".selector_resource_type").on('change', function () {
            $('.searchAllBtn').click();
        });

    };

    /**
     * 模块五： 初始化会员外单卖设置窗口
     */
    modal.initSetSingleSaleWindow = function(resImgUrl, resTitle, resType, showTime, resId) {

        //  给设置价格填充数据
        $(".resource_icon_3").attr("src", resImgUrl);
        $(".resource_title_3").html(resTitle);
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

        $(".close_icon_wrapper_2, .cancel_set_price").click(function() {
            $(".set_price_window").fadeOut(300);
        });

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

            showLoading();
            $.ajax("/submit_choice_resource", {
                type: "POST",
                data: {
                    "channel_type": 1,
                    "resource_list": resourceListArr,
                    "piece_price": price * 100, // 单位： 分
                    "submit_type": 0
                },
                success: function(result) {
                    hideLoading();
                    if (result.code == 0) {
                        $(".set_price_window").fadeOut(300);
                        baseUtils.show.blueTip("设为单卖成功");
                        modal.initResourceListOfMember();
                    } else {
                        baseUtils.show.redTip("操作失败，请稍后再试!");
                    }
                },
                error: function(xhr, status, err) {
                    hideLoading();
                    console.log(err);
                    baseUtils.show.redTip("服务器开小差啦，请稍后再操作！");

                }
            });
        });

    };


    return modal;

})();


var memberDetailUtils = (function() {

    var utils = {};

    /**
     * 会员 或者 单品 实现上架 + 下架
     * @param memberId
     * @param operate_type
     */
    utils.changeGoodsState = function(goodsId, goodsType, operate_type) {

        if (goodsType == 0) {
            var text = operate_type == 0 ? "上架后该会员将对外显示" : "下架后该会员将不对外显示";
        } else if (goodsType == 1 || goodsType == 2 || goodsType == 3 || goodsType == 4) {
            var text = operate_type == 0 ? "上架后该单品将对外显示" : "下架后该单品将不对外显示";
        }

        $.alert(text, "info", {
            onOk: function() {
                showLoading();
                $.ajax("/change_goods_state", {
                    type: "POST",
                    dataType: "json",
                    data: {
                        "goods_id": goodsId,
                        "goods_type": goodsType, //  goods_type(0-专栏 或 会员,1-图文,2-音频,3-视频,4-直播);
                        "operate_type": operate_type //  0-上架,1-下架
                    },
                    success: function(result) {
                        hideLoading();
                        if (result.code == 0) {
                            if (operate_type == 0) {
                                baseUtils.show.blueTip("上架成功！");
                            } else {
                                baseUtils.show.blueTip("下架成功！");
                            }
                            if (goodsType == 0) {
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                modal.initResourceListOfMember();
                            }
                        } else {
                            baseUtils.show.redTip("操作失败，请稍后重试！");
                        }
                    },
                    error: function(xhr, status, err) {
                        hideLoading();
                        console.log(err);
                        baseUtils.show.redTip("操作失败，请稍后重试！");
                    }
                });

            }
        });

    };

    /**
     * 是否在最新列表中显示
     * @param memberId
     * @param operate_type
     */
    utils.changeNewlListState = function(goodsId, goodsType, operate_type) {
        showLoading();
        $.ajax("/set_h5_newest_hide", {
            type: "POST",
            dataType: "json",
            data: {
                "id": goodsId,
                "h5_newest_hide": operate_type //  0-显示,1-隐藏
            },
            success: function(result) {
                hideLoading();
                if (result.code == 0) {
                    if (operate_type == 0) {
                        baseUtils.show.blueTip("设置成功！");
                    } else {
                        baseUtils.show.blueTip("设置成功！");
                    }
                    if (goodsType == 0) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        modal.initResourceListOfMember();
                    }
                } else {
                    baseUtils.show.redTip("操作失败，请稍后重试！");
                }
            },
            error: function(xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("操作失败，请稍后重试！");
            }
        });


    };

    /**
     * 置顶操作
     * @param packageId
     * @param resourceType  -> (0-专栏,1-图文,2-音频,3-视频,4-直播);
     * @param resourceId
     * @param topState      -> (0-取消置顶,1-设置置顶)
     */
    utils.setResourceTop = function(packageId, resourceType, resourceId, topState) {
        showLoading();

        $.ajax('/set_package_resource_top', {
            type: 'POST',
            dataType: 'json',
            data: {
                package_id: packageId,
                resource_type: resourceType,
                resource_id: resourceId,
                top_state: topState
            },
            success: function(data) {
                hideLoading();
                if (data.code == 0) {
                    if (topState == 0) {
                        baseUtils.show.blueTip("已经取消置顶！");
                    } else {
                        baseUtils.show.blueTip("置顶成功！");
                    }
                    modal.initResourceListOfMember();
                } else {
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                hideLoading();
                console.error(err);
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        });

    };

    /**
     * 单品上下架
     * @param resource_id
     * @param changeType  true-上架， false-下架
     */
    utils.packageisShow = function(id, changeType) { //专栏上下架
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

    };

    /**
     * 查询单品 是否在单品列表 或 专栏列表
     * @param resource_id
     * @param resource_type  (1-图文,2-音频,3-视频,4-直播);
     * @param package_id  (当channel_type=1时有值)
     */
    utils.querySingleResource = function(resource_id, resource_type, package_id) {
        showLoading();
        $.ajax("/query_goods_state", { //  1 -> 查询单品 是否在单品列表或者其它专栏、会员列表
            type: "POST",
            dataType: "json",
            data: {
                goods_id: resource_id,
                goods_type: resource_type,
                channel_type: 2, //  channel_type ( 0-单品,1-专栏内资源,2-会员内 )
                package_id: package_id
            },
            success: function(result) {
                hideLoading();
                if (result.code == 0) {
                    var isExist = result.data['is_exist'],
                        recordsList = result.data['records_list'],
                        arrLength = Object.keys(recordsList).length,
                        text = "";

                    if (isExist == 0) {                             //  只属于某个专栏的单品,其它地方不存在
                        text = "确认移除该内容，移除后所有用户将无法查看此内容且<spa style='color: red;'>不可恢复</spa>";
                    } else if (recordsList[0] == undefined) {       //  只属于多个专栏的单品

                        for (var index = 1; index <= arrLength; index ++) {
                            text += ("《" + recordsList[index]['title'] + "》");
                        };
                        text = ("确认移除该内容，移除后该会员用户将无法查看此内容。此内容仍存在于" + text + "中");

                    } else {
                        if (arrLength == 1) {              //  单品单卖且属于一个专栏
                            text = "确认移除该内容，移除后该会员用户将无法查看此内容。此内容仍在单品列表且单独售卖";
                        } else {                                    //  单品单卖且属于多个专栏

                            for (var index = 1; index < arrLength; index ++) {
                                text += ("《" + recordsList[index]['title'] + "》");
                            };
                            text = "确认移除该内容，移除后该会员用户将无法查看此内容。此内容单独售卖且仍存在于" + text + "中";
                        }
                    }
                    $.alert(text, "info", { // 2 -> 作出相应的提示
                        oktext: '确认移除',
                        onOk: function() {
                            utils.deleteSingleResource(resource_id, resource_type, package_id); // 3 -> 移除
                        }
                    });

                } else {
                    baseUtils.show.redTip("操作失败！");
                }
            },
            error: function(xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("服务器开小差了，请稍后再试！");
            }
        });

    };

    //  查询专栏 是否在其它会员列表
    utils.queryPackageResource = function (resource_id) {
        showLoading();
        $.ajax("/query_goods_state", { //  1 -> 查询专栏 是否在其它会员列表
            type: "POST",
            dataType: "json",
            data: {
                goods_id: resource_id,
                goods_type: 6,
                channel_type: 2, //  channel_type ( 0-单品,1-专栏内资源,2-会员内 )
                package_id: Business.member_id
            },
            success: function(result) {
                hideLoading();
                if (result.code == 0) {
                    var isExist = result.data['is_exist'],
                        recordsList = result.data['records_list'],
                        arrLength = Object.keys(recordsList).length,
                        text = "";

                    if (isExist == 0) {
                        text = "确认移除该专栏，移除后该会员用户将无法查看此专栏，此专栏仍在专栏列表且单独售卖";
                    } else {
                        for (var index = 1; index <= arrLength; index ++) {
                            text += ("《" + recordsList[index]['title'] + "》");
                        };
                        text = ("确认移除该专栏，移除后该会员用户将无法查看此专栏，此专栏仍存在于" + text + "中");
                    }

                    $.alert(text, "info", { // 2 -> 作出相应的提示
                        oktext: '确认移除',
                        onOk: function() {
                            utils.deleteSingleResource(resource_id, 6, Business.member_id); // 3 -> 移除专栏
                        }
                    });

                } else {
                    baseUtils.show.redTip("操作失败！");
                    console.log(result.msg);
                }
            },
            error: function(xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("服务器开小差了，请稍后再试！");
            }
        });


    };
    utils.visibleSwitch= function(id,visible_on){
        // id:专栏是否要设置支付时填写信息的专栏id,列表显示开关状态0-关闭，1-开启
        var version=$(".user_version_num").data("version_type");
        if(version&&version==3) {
            $.alert(visible_on ? "开启内容展示后，未购买专栏的用户进去专栏后将可见内容列表，您确认要开启么？" :
                "关闭内容展示后，未购买专栏的用户进去专栏后将无法查看内容列表，您确认要关闭么？", "info", {
                icon: "blue",
                onOk: function () {
                    showLoading();
                    $.ajax('/visible_on_switch', {
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: id,
                            visible_on:visible_on
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
    }
    utils.packageForm = function(id,formType){
        // id:专栏是否要设置支付时填写信息的专栏id，formType ：true-允许 false-禁止.
        var version=$(".user_version_num").data("version_type");
        if(version && version==3) {
            $.alert(formType ? "开启资料填写后，用户在付费购买本会员前将填写个人信息，您确认要开启么？" :
                "关闭资料填写后，用户在付费购买本会员前将不再填写个人信息，您确认要关闭么？", "info", {
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
                            console.log(data);
                            location.reload();
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
    }

    /**
     * 移除单品资源
     * @param resourceId
     * @param resourceType
     * @param memberId
     */
    utils.deleteSingleResource = function(resourceId, resourceType, memberId) {
        // $(".loadingS").fadeIn(300);
        $.ajax("/move_goods", {
            type: "POST",
            dataType: "json",
            data: {
                goods_id: resourceId,
                goods_type: resourceType,
                channel_type: 2, //  channel_type(0-单品,1-专栏内资源,2-会员内)
                package_id: memberId
            },
            success: function(result) {
                $(".loadingS").fadeOut(300);
                if (result.code == 0) {
                    baseUtils.show.blueTip("移除成功！");
                } else {
                    baseUtils.show.redTip("移除失败，请稍后再试");
                }
                if (resourceType == 6) {
                    modal.initPackageListOfMember();
                } else {
                    modal.initResourceListOfMember();
                }
            },
            error: function(xhr, status, err) {
                $(".loadingS").fadeOut(300);
                console.log(err);
                baseUtils.show.redTip("服务器开小差了，请稍后再试！");
            }
        });

    };

    /**
     * 设置试听 或 试看
     * @param packageId
     * @param resId
     * @param tryState  (0-取消试听,1-设为试听)
     */
    utils.setResourceTry = function(packageId, resType, resId, paymentType, tryState) {

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
            data: {
                package_id: packageId,
                resource_type: resType,
                resource_id: resId,
                try_state: tryState
            },
            success: function(data) {
                hideLoading();
                if (data.code == 0) {
                    if (tryState == 1) {
                        baseUtils.show.blueTip("设置成功！");
                    } else {
                        baseUtils.show.blueTip("取消成功！");
                    }
                    modal.initResourceListOfMember();
                } else {
                    baseUtils.show.redTip("操作失败！");
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        });

    };

    //导出音频
    utils.exportAudio = function (aliveId) {

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

    utils.setSingleSale = function(resType, resId, resTitle, isTry, showTime, resImgUrl) {

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
            modal.initSetSingleSaleWindow(resImgUrl, resTitle, resType, showTime, resId);
            $(".set_price_window").fadeIn(300);
        }

    };

    utils.cancelSingleSale = function(resType, resId, paymentType) {

        if (paymentType == 1) {
            var text = "取消单卖后，未开通会员的用户将不可单独查看该单品!";
        } else if (paymentType == 2) {
            var text = "取消单卖后，未开通会员的用户将不可单独付费购买该单品!";
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
                            modal.initResourceListOfMember();
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

    };

    utils.changeCanSelectState = function (resId, canSelect) {

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

    };

    utils.initListPagination = function () {

        $('.list-page .pagination li a').each(function() {
            var $ele = $(this);
            $ele.attr('data-url', $ele.attr('href'));
            $ele.removeAttr('href');
        });

    };

    /**
     * 切换选择已有窗口中 tab 的显示
     * @param target(String)    packageTab - 切换到专栏； singleTab - 切换到单品
     */
    utils.switchWindowTab = function (target) {

        var tabValue = $(".selectGoodTabWrapper>li.activeContentTab").val(); //    1-单品  0-专栏

        if ((tabValue == 1 && target == "singleTab") || (tabValue == 0 && target == "packageTab")) {
            return false;
        }
        if (target == "singleTab") {
            $("#selectGoodSingleTab").addClass("activeContentTab");
            $("#selectGoodPackageTab").removeClass("activeContentTab");

            modal.tabState = "singleList";
            modal.resType = 0;

        } else if (target == "packageTab") {
            $("#selectGoodPackageTab").addClass("activeContentTab");
            $("#selectGoodSingleTab").removeClass("activeContentTab");

            modal.tabState = "packageList";
            modal.resType = 6;

        }

    };

    return utils;

})();
