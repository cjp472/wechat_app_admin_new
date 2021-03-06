
$(document).ready(function () {
    QADetail.init();
});

var resAudio1Length = -1,   //  单位：秒
    resAudioSize = -1;          //  单位：M

var QADetail = function () {

    var QADetail = {};

    QADetail.tabState = "questionList";     //  默认显示的tab - "questionList" , < "questionList", "responderList", "settingTab" >
    QADetail.pageIndex = 1;

    QADetail.searchAnswererContent = "";
    QADetail.searchAnswererState = "";

    QADetail.searchQuestionContent = "";

    QADetail.timeInterval = {   //  保存时间选择器的时间区间起始值
        startDate: "",
        endDate: ""
    }

    QADetail.init = function () {
        var set = GetQueryString("set");
        if(set == 'answerer')
            location.href = '/QA/questionAndAnswerDetail?page_type=1';

        var pageType = GetQueryString("page_type") || 1;
        if (pageType == 1) {
            QADetail.tabState = "responderList";
        } else {
            QADetail.tabState = "questionList";
        }

        //生成二维码
        var codeUrl = $("#admin_data").data("code_url");
        createQR(codeUrl);


        /**
         * 处理搜索 + 时间插件
         */
        modal.handleSearch();

        /**
         * 处理退款
         */
        // modal.handleRefundEvent();

        /**
         * 问答详情操作
         */
        modal.initDetailOperate();

        /**
         * contentTabWrapper tab切换
         */
        modal.initContentTab();

        /**
         * 刚进入页面处理分页
         */
        modal.initListPagination();

        /**
         * 刚进入页面处理分页 的 点击事件
         */
        modal.initPaginationEvent();

        /**
         * 问题列表操作
         */
        modal.initQuestionOperate();

        /**
         * 答主列表操作
         */
        modal.initResponderOperate();

        /**
         * 设置页面操作
         */
        modal.initSettingOperate();

        /**
         * 处理邀请答主的弹出式窗口
         */
        modal.handleInviteAnswererWindow();
        
        /**
         * 处理查看问题的弹出式窗口
         */
        modal.handleScanQuestionWindow();


    };


    function createQR(code_url) {
        //生成二维码
        var qrcode = new QRCode(document.getElementById("qr_code"), {
            text: code_url,
            width: 120,
            height: 120,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        create_qr_flag = false;
    }

    return QADetail;
}();

var modal = (function () {

    var modal = {};

    modal.questionId = -1;

    modal.isEnable = -1;            //是否允许偷听
    modal.submitLimit = false;      //请求是否正在进行

    modal.isAudioUploading = false; //音频是否正在上传到云

    modal.initDetailOperate = function () {

        $(".detailOperateArea li.operate").click(function () {

            var type = $(this).data("type");
            var state = $(this).data("state")  == 1 ? 0 : 1;
            var id = $('#product_id').val();
            switch (type) {
                case "editQA":
                    window.location.href = "/QA/editQuestionAndAnswer?id=" + id;
                    break;

                case "changeQAState":
                    $.ajax("/QA/changeStateQueProducts", {
                        type: "POST",
                        dataType: "json",
                        data: {
                            id:id,
                            state: state
                        },
                        success: function (data) {
                            if (data.code == 0) {
                                if (state == 1) {
                                    baseUtils.show.blueTip('已下架，问答专区将不在首页显示');
                                } else {
                                    baseUtils.show.blueTip('已上架，问答专区将在首页显示');
                                }
                                window.location.href = "/QA/questionAndAnswerDetail";
                            } else {
                                baseUtils.show.redTip("网络错误，请稍后再试！");

                            }
                        },
                        error: function (xhr, status, err) {
                            console.log(err);
                            baseUtils.show.redTip("网络错误，请稍后再试！");
                        }
                    });

                    break;
                default:
                    console.log("参数错误.");
                    break;
            }

        });

        //  复制到剪贴板
        (function () {
            var clipboard = new Clipboard('.QA_DetailUrl');
            clipboard.on('success', function(e) {
                baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
                e.clearSelection();
            });
        })();

    };

    modal.initContentTab = function () {

        $(".contentTabWrapper li").click(function () {
            var value = $(this).val();

            if (!$(this).hasClass("activeContentTab")) {
                if (value == 1) {                                       //  点击全部答主
                    $("#responderTab").addClass("activeContentTab");
                    $("#questionTab").removeClass("activeContentTab");
                    $("#settingTab").removeClass("activeContentTab");

                } else if (value == 0) {                                                //  点击全部问题
                    $("#questionTab").addClass("activeContentTab");
                    $("#responderTab").removeClass("activeContentTab");
                    $("#settingTab").removeClass("activeContentTab");

                } else if (value == 2) {
                    $("#settingTab").addClass("activeContentTab");
                    $("#questionTab").removeClass("activeContentTab");
                    $("#responderTab").removeClass("activeContentTab");

                }
            }
            QADetail.pageIndex = 1;
            QADetail.searchAnswererContent = "";
            QADetail.searchAnswererState = "";
            QADetail.searchQuestionContent = "";
            $('#searchAnswererInput').val("");
            $("#answererIsShow").val("");
            $('#searchQuestionInput').val("");
            $('#SelectData').text('全部问题');

            if (value == 1) {
                QADetail.tabState = "responderList";    //获取答主列表
                history.replaceState(null, "", "/QA/questionAndAnswerDetail?page_type=1");
                modal.getResponderList(true, true);

            } else if (value == 0) {
                QADetail.tabState = "questionList";     //获取问题列表
                history.replaceState(null, "", "/QA/questionAndAnswerDetail?page_type=0");
                modal.getQuestionList(true, true);

            } else if (value == 2) {
                QADetail.tabState = "settingTab";       //获取设置页面
                history.replaceState(null, "", "/QA/questionAndAnswerDetail?page_type=2");
                modal.getSettingPage();

            }

        });
    };

    /**
     * @param isShowLoading     true - show loading picture or do not show loading picture
     * @param isSwitchContentTab    true - switch the content tabs, or do not switch
     */
    modal.getResponderList = function (isShowLoading, isSwitchContentTab) {

        if (isShowLoading == undefined || isShowLoading == true) {
            $(".loadingS").fadeIn(300);
        }

        $.ajax("/QA/getResponderList", {
            type: "GET",
            dataType: "html",
            data: {
                search: QADetail.searchAnswererContent,
                state: QADetail.searchAnswererState,
                page: QADetail.pageIndex
            },
            success: function (result) {
                $(".loadingS").fadeOut(100);
                if (result && result.length > 0) {
                    $(".mainListContent").html(result);
                    modal.initListPagination();

                    if (isSwitchContentTab) {
                        $(".listOperateArea").show();
                        $(".questionListOperate").hide();
                        $(".responderListOperate").fadeIn(300);
                    }

                } else {
                    console.log("请求数据为空");
                    baseUtils.show.redTip("网络问题，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                $(".loadingS").fadeOut(100);
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
         });

    };

    /**
     * @param isShowLoading     true - show loading picture or do not show loading picture
     * @param isSwitchContentTab    true - the first time to get the responder list, or not the first time
     */
    modal.getQuestionList = function (isShowLoading, isSwitchContentTab) {

        if (isShowLoading == undefined || isShowLoading == true) {
            $(".loadingS").fadeIn(300);
        }
        var timeString = $('#SelectData').text();       //  传入时间区间字符串， "2017-05-01 ~ 2017-05-12"
        if (timeString == "全部问题") {
            timeString = "";
        }

        $.ajax("/QA/getQuestionList", {
            type: "GET",
            dataType: "html",
            data: {
                search: QADetail.searchQuestionContent,
                time1: timeString,
                page: QADetail.pageIndex
            },
            success: function (result) {
                $(".loadingS").fadeOut(100);
                if (result && result.length > 0) {
                    $(".mainListContent").html(result);
                    modal.initListPagination();

                    if (isSwitchContentTab) {
                        $(".listOperateArea").show();
                        $(".responderListOperate").hide();
                        $(".questionListOperate").fadeIn(300);
                    }

                } else {
                    console.log("请求数据为空");
                    baseUtils.show.redTip("网络问题，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                $(".loadingS").fadeOut(100);
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });

    };

    /**
     * 获取设置页面
     */
    modal.getSettingPage = function () {

        $(".loadingS").fadeIn(300);

        $.ajax("/QA/getSettingPage", {
            type: "GET",
            dataType: "html",
            data: {},
            success: function (result) {
                $(".loadingS").fadeOut(100);
                if (result && result.length > 0) {
                    $(".mainListContent").html(result);

                    $(".listOperateArea").hide();
                } else {
                    console.log("请求数据为空");
                    baseUtils.show.redTip("网络问题，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                $(".loadingS").fadeOut(100);
                console.log(err);
                baseUtils.show.redTip("服务器出小差了，请稍后再试！");
            }
         });


    };

    modal.initQuestionOperate = function () {

        //  点击问题列表的条目的操作
        $(".mainListContent").on("click", ".questionListOperateArea li.operate", function () {

            var question_id = $(this).data("id");
            var is_enable   = $(this).data('is_enable');
            var state = $(this).data("state") == 1 ? 0 : 1;

            var type = $(this).data("type");
            switch (type) {
                case "answerQuestion":      //回答问题
                    modal.handAnswerQuestion(question_id,is_enable);
                    break;
                case "answerQueAgain":      //重新回答
                    $.alert("确认上传音频并重新回答，确认后新的回答内容将覆盖原内容，且该动作无法撤回。", "info", {
                        btn: 3,
                        onOk: function () {
                            modal.handAnswerQuestion(question_id,is_enable);
                        },
                    });

                    break;
                case "lookQuestion":    //查看问题详情

                    $("#scanQueWindowContent").text("");
                    $("#scanQueImgWrapper").html("");

                    var content = $(this).data("content");
                    $("#scanQueWindowContent").text(content);

                    var imgJsonArr = $(this).data("imgs");
                    if (imgJsonArr.length > 0) {
                        var imgString = "";
                        imgJsonArr.forEach(function (item) {
                            imgString +=
                                '<img src="' + item + '">';
                        });
                        $("#scanQueImgWrapper").html(imgString);
                    }

                    $(".scanQuestionWindowBg").fadeIn(300);
                    break;

                case "changeQuestionState":
                    $(".loadingS").fadeIn(300);
                    $.ajax("/QA/changeQuestionState", {
                        type: "POST",
                        dataType: "json",
                        data: {
                            state: state,
                            question_id: question_id
                        },
                        success: function (data) {
                            if (data.code == 0) {
                                if (state == 1) {
                                    baseUtils.show.blueTip('操作成功，问题将不在问答专区展示');
                                } else {
                                    baseUtils.show.blueTip('操作成功，问题将在问答专区展示');
                                }
                                modal.getQuestionList(false);
                            } else {
                                $(".loadingS").fadeOut(100);
                                baseUtils.show.redTip("网络错误，请稍后再试！");

                            }
                        },
                        error: function (xhr, status, err) {
                            $(".loadingS").fadeOut(100);
                            console.log(err);
                            baseUtils.show.redTip("网络错误，请稍后再试！");
                        }
                    });

                    break;
                default:
                    console.log("参数错误.");
                    break;
            }

        });


        // 点击选择文件
        $("#selectAudioFile").click(function () {
            $("#uploadAudioFile").click();

        });
        $(".closeUploadFileWindow, #cancelUploadAudio").click(function () {
            $(".uploadFileWindowBg").fadeOut(300);

        });

        $("#uploadAudioFile").on("change", function () {
            resUpload(this.files, "audio", "Audio1", 1000);

        });

        //  点击确定上传音频文件
        $("#confirmUploadAudio").click(function () {
            var is_enable_eavesdrop = $('#is_enable_eavesdrop').is(':checked');
            if(is_enable_eavesdrop == false)
                is_enable_eavesdrop = 0;
            else
                is_enable_eavesdrop = 1;
            console.log(is_enable_eavesdrop);

            var uploadAudioUrl = $("#uploadAudioUrl").val();
            if (modal.isAudioUploading) {
                baseUtils.show.redTip("音频文件正在上传中...");
                return false;
            }
            if (uploadAudioUrl.length == 0) {
                baseUtils.show.redTip("请先上传音频文件.");
                return false;
            }
            if (modal.submitLimit) {
                console.log("音频提交中，请稍后再试。");
                return false;
            }
            modal.submitLimit = true;

            $.ajax("/QA/commitAnswer", {
                type: "POST",
                dataType: "json",
                data: {
                    id: modal.questionId,
                    answerer_size: resAudioSize,
                    answerer_length: resAudio1Length,
                    answerer_content: uploadAudioUrl,
                    is_enable_eavesdrop:is_enable_eavesdrop
                },
                success: function (result) {
                    modal.submitLimit = false;
                    if (result.code == 0) {
                        baseUtils.show.blueTip("音频文件提交成功。");
                        $(".uploadFileWindowBg").fadeOut(300);
                        modal.getQuestionList(true);
                    } else {
                        baseUtils.show.redTip(result.msg);
                        console.log(result.msg);
                    }
                },
                error: function (xhr, status, err) {
                    modal.submitLimit = false;
                    console.log(err);
                    baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                }
             });

        });

    };


    modal.initResponderOperate = function () {

        $(".mainListContent").on("click", ".responderListOperateArea li.operate", function () {

            /**
             * @type {number}   0-上线，1-下线',<状态值>
             */
            var state = $(this).data('state');
            var answererId = $(this).data('id');

            var type = $(this).data("type");
            switch (type) {
                case "editResponderInfo":
                    window.location.href = "/QA/editAnswerer?answerer_id=" + answererId + "&state=" + state;
                    break;

                case "changeResponderState":
                    $(".loadingS").fadeIn(300);
                    state = (state == 1 ? 0 : 1);
                    $.ajax("/QA/changeAnswererState", {
                        type: "POST",
                        dataType: "json",
                        data: {
                            answerer_id: answererId,
                            state: state
                        },
                        success: function (data) {
                            if (data.code == 0) {
                                if (state == 1) {
                                    baseUtils.show.blueTip('答主下线成功');
                                } else {
                                    baseUtils.show.blueTip('答主上线成功');
                                }
                                modal.getResponderList(false);
                            } else if(data.code == 2){
                                $(".loadingS").fadeOut(100);
                                baseUtils.show.redTip("请您先填写答主信息 才能上线答主！");
                                return false;
                            } else {
                                $(".loadingS").fadeOut(100);
                                baseUtils.show.redTip("网络错误，请稍后再试！");
                                return false;
                            }
                        },
                        error: function (xhr, status, err) {
                            $(".loadingS").fadeOut(100);
                            console.log(err);
                            baseUtils.show.redTip("网络错误，请稍后再试！");
                        }
                    });
                    break;
                default:
                    console.log("参数错误.");
                    break;
            }

        });

    };

    modal.initSettingOperate = function () {

        $(".mainListContent").on("change", "input[type=radio]", function (e) {

            var targetId = e.target.id,
                isShowListen = $('input[name=isShowListen]:checked').val(),
                isSmsRemind = $('input[name=isSmsRemind]:checked').val();

            $.ajax("/QA/commitSetting", {
                type: "POST",
                dataType: "json",
                data: {
                    params: {
                        isShowListen: isShowListen,
                        isSmsRemind: isSmsRemind
                    }
                },
                success: function (result) {
                    if (result.code == 0) {
                        var outputText = "";
                        switch (targetId) {
                            case "openEavesdrop":
                                outputText = "问题页已显示偷听人数";
                                break;
                            case "closeEavesdrop":
                                outputText = "问题页已隐藏偷听人数";
                                break;
                            case "openSmsRemind":
                                outputText = "短信提醒已开启";
                                break;
                            case "closeSmsRemind":
                                outputText = "短信提醒已关闭";
                                break;
                            default:
                                console.log("参数错误。");
                                break;
                        }
                        baseUtils.show.blueTip(outputText);
                    } else {
                        baseUtils.show.redTip("保存失败");
                        window.location.reload();
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                }
             });

        });

    };

    modal.handleInviteAnswererWindow = function () {

        //  邀请答主
        $("#inviteResponder").click(function () {

            $(".scanQrCodeWindowBg").fadeIn(300);

        });

        $(".closeScanQrCodeWindow img").click(function () {
            $(".scanQrCodeWindowBg").fadeOut(300);
        });

        //  复制到剪贴板
        (function () {
            var clipboard = new Clipboard('.copyHref');
            clipboard.on('success', function(e) {
                baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
                e.clearSelection();
                setTimeout(function () {
                    $(".scanQrCodeWindowBg").fadeOut(100);
                }, 2000);
            });
        })();
    };

    modal.handleScanQuestionWindow = function () {

        //  隐藏问题详情
        $(".closeQuestionDetailWindow, .scanQuestionWindowBg").click(function () {
            $(".scanQuestionWindowBg").fadeOut(100);

        });
        $(".scanQuestionWindow").click(function (e) {
            e.stopPropagation();

        });



    };


    modal.initListPagination = function () {

        $('.list-page .pagination li a').each(function() {
            var $ele = $(this);
            $ele.attr('data-url', $ele.attr('href'));
            $ele.removeAttr('href');
        });

    };

    modal.initPaginationEvent = function () {
        //分页请求
        $(".mainListContent").on('click', ' .pagination li a', function(e) {
            var ele = $(e.target),
                url = ele.data('url');

            var index = url.indexOf("page");
            QADetail.pageIndex = url.substring(index + 5);

            if (QADetail.tabState == "questionList") {
                modal.getQuestionList(true);
            } else if (QADetail.tabState == "responderList") {
                modal.getResponderList(true);
            }
        });

    };

    modal.handleSearch = function () {

        /**
         * 初始化时间插件
         */
        var dataRangeInstance = new pickerDateRange('SelectData', {
            isTodayValid : true,
            defaultText : ' ~ ',
            inputTrigger : 'optional',
            theme : 'ta',
            success : function(obj) {
                updateTimeInterval(obj);
                $("#searchQuestion").click();
            }
        });

        $('#SelectRange').on('click', 'li', function() {
            var type = $(this).data('type');
            var timeRange = -1;
            switch (type) {
                case "all":
                    timeRange = {
                        startDate: '', endDate: ''
                    };
                    break;
                case "current_month":
                    timeRange = {
                        startDate: getNowMonth(), endDate: getNowDay()
                    };
                    break;
                case "last_seven":
                    timeRange = {
                        startDate: getOneDay(6), endDate: getNowDay()
                    };
                    break;
                case "last_month":
                    timeRange = {
                        startDate: getOneDay(29), endDate: getNowDay()
                    };
                    break;
            };
            updateTimeInterval(timeRange);
            $("#searchQuestion").click();
        });

        $('#optional').click(function() {
            $('#dropdown-toggle').dropdown('toggle');
        });
        $('#SelectData').off('click').text('全部问题');  //设置开始结束时间


        /*********************** 搜索问题列表 *************************/
        $('#searchQuestion').click(function () {
            QADetail.pageIndex = 1;
            QADetail.searchQuestionContent = $('#searchQuestionInput').val();
            modal.getQuestionList(true);
        });
        $("#searchQuestionInput").on("keypress", function (e) {
            if (e.keyCode == 13) {
                $("#searchQuestion").click();
            }
        });

        /*********************** 搜索答主列表 *************************/
        $('#searchAnswerer').click(function () {
            QADetail.pageIndex = 1;
            QADetail.searchAnswererContent = $('#searchAnswererInput').val();
            QADetail.searchAnswererState = $("#answererIsShow").val();
            modal.getResponderList(true);
        });

        $("#answererIsShow").on("change", function () {
            $('#searchAnswerer').click();
        });
        $("#searchAnswererInput").on("keypress", function (e) {
            if (e.keyCode == 13) {
                $("#searchAnswerer").click();
            }
        });

    };

    modal.handAnswerQuestion = function (question_id,is_enable) {
        $(".uploadAudio1Name").html("");
        $(".uploadAudio1Size span").html("");
        $(".uploadPLineActiveAudio1").css("width", '0');
        $(".uploadPersentAudio1").text("");

        $(".uploadPLineAudio1").hide();
        $(".uploadBoxAudio1").hide();
        $("#uploadAudioUrl").val("");
        $('#is_enable_eavesdrop').attr("checked",'true');

        modal.questionId = question_id;

        $(".uploadFileWindowBg").fadeIn(100);
        if(is_enable == 1){
            $('.simi_tip').hide();
            $('.is_enable_eavesdrop').show();
        } else {
            $('.simi_tip').show();
            $('.is_enable_eavesdrop').hide();
        }

    };

    // var pageIndex = 1,
    //     isWindowClose = true,
    //     isScrollToBottom = false,       //数据加载完毕
    //     searchRefundListContent = "",	//退款列表窗口搜索内容
    //
    //     questionArr = new Array(),
    //     totalPrice = 0;


    // modal.handleRefundEvent = function () {
    //
    //     $("#handleRefund").click(function () {			//	点击退款处理按钮
    //         pageIndex = 1;
    //         $(".refundListWindowBg").fadeIn(300);
    //         $("#loadingRefund").show();
    //         getRefundList(function () {
    //             $("#loadingRefund").fadeOut(100);
    //         });
    //     });
    //     $("#selectAllRefundList").on("click", function () {	 // 选中/取消 全部
    //         if ($(this).prop("checked")) {
    //             changeChecked("isSelectCheckBox", true)
    //         } else {
    //             changeChecked("isSelectCheckBox", false)
    //         }
    //         function changeChecked(className, status) {
    //             $("input." + className).each(function () {
    //                 $(this).prop("checked", status)
    //             })
    //         }
    //     });
    //     $("#cancelRefund, .closeIconWrapper").click(function () {	//取消退款 - 关闭退款窗口
    //         $(".refundListWindowBg").fadeOut(100);
    //         clearRefundWindowData();
    //     });
    //
    //     //监听是否选中退款人员
    //     $(".refundListWindow").on('change', 'input[type=checkbox]', function () {
    //         var count = $("input.isSelectCheckBox:checked").length;
    //         if (count > 0) {
    //             $("#confirmRefund").addClass("btnBlue");
    //         } else {
    //             $("#confirmRefund").removeClass("btnBlue");
    //         }
    //     });
    //
    //     $("#confirmRefund").click(function () {		//	点击退款
    //         questionArr = new Array();
    //         totalPrice = 0;
    //         $("input.isSelectCheckBox:checked").each(function () {
    //             var questionId = $(this).data("question_id");
    //             questionArr.push(questionId);
    //             totalPrice += $(this).data("price");
    //         });
    //
    //         if (questionArr.length == 0) {
    //             // baseUtils.show.redTip("请先选择退款用户");
    //             return false;
    //         }
    //         totalPrice = Math.round(totalPrice * 100) / 100;
    //
    //         $(".refundListWindowBg").fadeOut(300);
    //
    //         $(".confirmRefundWindowBg .confirmRefundWindowText").text("确定给 " + questionArr.length + " 位用户退款 " + totalPrice + " 元");
    //
    //         $(".confirmRefundWindowBg").fadeIn(300);
    //
    //     });
    //     $(".closeConfirmRefundWindow img, .confirmRefundWindowBtn_1").click(function () {   //	点击取消
    //         $(".confirmRefundWindowBg").fadeOut(300);
    //         $(".refundListWindowBg").fadeIn(300);
    //     });
    //     $(".confirmRefundWindowBtn_2").click(function () {   //	点击确认
    //         confirmRefund();
    //     });
    //
    //     $("#searchRefundUser").click(function () {          //  搜索
    //         pageIndex = 1;
    //         searchRefundListContent = $.trim($("#searchRefundUserInput").val());
    //         $("#loadingRefund").show();
    //         getRefundList(function () {
    //             $("#loadingRefund").fadeOut(100);
    //         });
    //     });
    //
    //     $("#searchRefundUserInput").on("keypress", function (e) {
    //         if (e.keyCode == 13) {
    //             $("#searchRefundUser").click();
    //         }
    //     });
    //
    //     /*********************** 处理退款列表滑动事件 *************************/
    //
    //     $('.refundListWindow .windowContentWrapper').scroll(function(e) {
    //         var DivHeight = $('.windowContentArea').height(),
    //             ScrollHeight = $(this).height(),
    //             ScrollTop = $(this).scrollTop();
    //
    //         if ((ScrollTop + ScrollHeight >= DivHeight - 5) && !isWindowClose && !isScrollToBottom) {
    //
    //             isWindowClose = true;
    //
    //             $("#loadingRefund").show();
    //             getRefundList(function () {
    //                 $("#loadingRefund").fadeOut(100);
    //             });
    //         }
    //     });
    //
    //
    // };

    // function getRefundList(callback, args) {
    //
    //     $.ajax("/QA/refundList", {
    //         type: "POST",
    //         dataType: "json",
    //         data: {
    //             page: pageIndex,
    //             search_content: searchRefundListContent
    //         },
    //         success: function (result) {
    //
    //             if (result.code == 0) {
    //                 console.log(result.data);
    //
    //                 var $area = $(".windowContentArea"),
    //                     htmlString = '',
    //                     pageObj = result.data.refund_record_list;
    //
    //                 pageObj.data.forEach(function (item) {
    //                     htmlString +=
    //                         '<div class="refundListItem">' +
    //                             '<input type="checkbox" class="isSelectCheckBox" data-question_id="' + item.id + '" data-price="' + item.price / 100.00 + '">' +
    //                             '<div class="refundUserInfo">' +
    //                                 '<img src="' + item.questioner_avatar + '">' +
    //                                 '<span>' + item.questioner_name + '</span>' +
    //                             '</div>' +
    //                             '<div class="refundType">付费问答</div>' +
    //                             '<div class="refundGoodName">' + item.content + '</div>' +
    //                             '<div class="refundPrice">' + item.price / 100.00 + '</div>' +
    //                         '</div>';
    //
    //                 });
    //                 if (htmlString.length == 0) {
    //                     htmlString +=
    //                         '<div class="contentNoneTip">暂无数据</div>';
    //                 }
    //                 isWindowClose = false;
    //                 if (pageIndex == 1) {
    //                     $area.html(htmlString);
    //                     $area.append('<div class="isDownAnimation isDown">数据加载中</div>');
    //
    //                 } else {
    //                     $area.find('.isDown').before(htmlString);
    //
    //                 }
    //                 if (pageObj.to >= pageObj.total) {      //数据加载完毕后的操作
    //                     if (pageObj.total > 10) {
    //                         $area.find('.isDown')
    //                             .text("数据已加载完毕")
    //                             .removeClass("isDownAnimation");
    //                     } else {
    //                         $area.find('.isDown').hide();
    //                     }
    //                     isScrollToBottom = true;
    //                 }
    //                 pageIndex ++;
    //
    //                 $("#selectAllRefundList").prop("checked", false);
    //
    //                 if (callback) {
    //                     callback.apply(window, args);
    //                 }
    //
    //             } else {
    //                 baseUtils.show.redTip("网络异常，请稍后再试");
    //             }
    //         },
    //         error: function (xhr, status, err) {
    //             if (callback) {
    //                 callback.apply(window, args);
    //             }
    //             console.log(err);
    //             baseUtils.show.redTip("网络错误，请稍后再试！");
    //         }
    //     });
    //
    // };
    // function clearRefundWindowData() {
    //     pageIndex = 1;
    //     isWindowClose = true;
    //     isScrollToBottom = false;
    //     searchRefundListContent = '';
    //     $("#searchRefundUserInput").val("");
    //     $(".windowContentArea").html("");
    // };

    // function confirmRefund() {
    //     $.ajax("/QA/commitRefund", {
    //         type: "POST",
    //         dataType: "json",
    //         data: {
    //             que_id_list: questionArr
    //         },
    //         success: function (result) {
    //             $(".confirmRefundWindowBg").fadeOut(100);
    //             if (result.code == 0) {
    //                 clearRefundWindowData();
    //                 baseUtils.show.blueTip("退款成功");
    //             } else {
    //                 $(".refundListWindowBg").fadeIn(300);
    //                 baseUtils.show.redTip("退款遇到问题，请稍后再试");
    //             }
    //         },
    //         error: function (xhr, status, err) {
    //             $(".confirmRefundWindowBg").fadeOut(100);
    //             $(".refundListWindowBg").fadeIn(300);
    //             console.log(err);
    //             baseUtils.show.redTip("网络错误，请稍后再试！");
    //         }
    //     });
    //
    // };

    function updateTimeInterval(obj) {
        if (obj.startDate == "" && obj.endDate == "") {
            $('#SelectData').text('全部问题');
        } else {
            $('#SelectData').text(obj.startDate + ' ~ ' + obj.endDate);
        }

        QADetail.timeInterval.startDate = obj.startDate;
        QADetail.timeInterval.endDate = obj.endDate;

    };

    /**
     * 切换tab的显示
     * @param target(String)    questionTab - 切换到全部问题； responderTab - 切换到全部答主
     */
    function switchContentTab(target) {
        var tabValue = $(".contentTabWrapper>li.activeContentTab").val(); //    1-全部答主  0-全部问题

        if ((tabValue == 1 && target == "responderTab") || (tabValue == 0 && target == "questionTab")) {
            return false;
        }
        if (target == "questionTab") {
            $("#questionTab").addClass("activeContentTab");
            $("#responderTab").removeClass("activeContentTab");

        } else if (target == "responderTab") {
            $("#responderTab").addClass("activeContentTab");
            $("#questionTab").removeClass("activeContentTab");

        }


    };

    //取消上传判断参数
    // var uploadCancel=0;
    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) {
            url = window.createObjectURL(file);
        } else if (window.URL != undefined) {
            url = window.URL.createObjectURL(file);
        } else if (window.webkitURL != undefined) {
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }

    function removeObjectURL(url) {//释放资源URL
        if (window.revokeObjectURL != undefined) {
            window.revokeObjectURL(url);
        } else if (window.URL != undefined) {
            window.URL.revokeObjectURL(url);
        } else if (window.webkitURL != undefined) {
            window.webkitURL.revokeObjectURL(url);
        }
    }

    //资源上传函数(参数：resType:资源类型,resTypeClass:资源类型中细分的种类,resLimitSize:资源限制大小)
    function resUpload(files, resType, resTypeClass, resLimitSize) {
        if (files && files.length > 0) {
            var file = files[0];

            var resourceLocalUrl = getObjectURL(file);
            var fileSize = (file.size / (1024 * 1024)).toFixed(2);
            resAudioSize = fileSize;
            //如果是正式音频
            if (resTypeClass == "Audio1") {
                fileAudio1Size = fileSize;
                var resourceElement = document.getElementById("resourceTime");
                resourceElement.src = resourceLocalUrl;
                resourceElement.onload = function() {
                    removeObjectURL(resourceLocalUrl);
                };
            }
            //如果是音频
            if (resType == 'audio') {
                console.log($('#audioURL'+resTypeClass));
                $('#audioURL'+resTypeClass).hide();
                $(".uploadBox"+resTypeClass).fadeIn(300);
                // 初始化文件以及进度条
                $(".upload" + resTypeClass + "Name").html(file.name);
                $(".upload" + resTypeClass + "Size span").html(fileSize);
                $(".uploadPLine" + resTypeClass).show()
                    .find(".uploadPLineActive" + resTypeClass).css("width", '0');
                $(".uploadPersent" + resTypeClass).html('');
                if(resTypeClass == "Audio1"){//正式音频
                    var AudioName = file.name;
                }
            }
            // 限制资源在*MB内
            if ($uploadFile.checkFileSize(file, resLimitSize)) {
                modal.isAudioUploading = true;
                $uploadFile.uploadRes(file, resType, function (data) {
                        if (resType == 'audio') {
                            var progress = parseInt(data * 100);
                            //console.log(progress);
                            $(".uploadPLineActive" + resTypeClass).css("width", progress + '%');
                            $(".uploadPersent" + resTypeClass).text(progress + "%");
                        }
                    },
                    // 上传成功回调
                    function (data) {
                        modal.isAudioUploading = false;
                        console.log(data);
                        baseUtils.show.blueTip("上传成功！");
                        $(".uploadPersent" + resTypeClass).text("已上传");
                        var resUrl = data.data.access_url;
                        $("#uploadAudioUrl").val(resUrl);

                    },
                    // 上传失败回调
                    function (data) {
                        modal.isAudioUploading  = false;
                        console.error("上传失败!!!");
                        console.log(data);
                        baseUtils.show.redTip("上传失败！");
                    });
            } else {
                baseUtils.show.redTip("上传资源限制在" + resLimitSize + "MB内！");
                $(".upLoad"+resTypeClass).val("");

            }
        } else {
            baseUtils.show.redTip("网络错误，请稍后再试！");
            // console.log(files)
        }
    }

    return modal;

})();












