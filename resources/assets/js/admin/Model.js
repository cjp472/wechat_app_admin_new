/**
 *  Created by PhpStorm
 */
$(document).ready(function () {
   individualModel.init();
});

var individualModel = (function () {

    var individualModel = {};

    var timeRange = {
        start_time: '',
        end_time: ''
    }

    var pageIndex = 1,
        isWindowClose = true,
        isScrollToBottom = false,       //数据加载完毕
        searchRefundListContent = "",	//搜索退款列表数据

        questionArr = new Array(),
        totalPrice = 0;

    individualModel.init = function () {

        setTopUrlCookie('payadmin_listop','财务管理');

        var dataRangeInstance = new pickerDateRange('SelectData', { //初始化时间插件
            isTodayValid : true,
            defaultText : ' ~ ',
            inputTrigger : 'optional',
            theme : 'ta',
            success : function(obj) {
                timeRange = {
                    start_time: obj.startDate,
                    end_time: obj.endDate
                }
                updateTime(timeRange);
            }
        });
        $('#SelectRange').on('click', 'li', function(e) {
            var ele = $(this),
                type = ele.data('type'),
                text = ele.text();
            if(type == 'all') {
                timeRange = {
                    start_time: '',
                    end_time: ''
                };
            } else if(type == 'nowMonth') {
                timeRange = {
                    start_time: getNowMonth(),
                    end_time: getNowDay()
                };
            }
            updateTime(timeRange);
        });
        $('#optional').click(function() { //时间选择器下拉
            $('#dropdown-toggle').dropdown('toggle');
        });
        $('#SelectData').off('click').text('全部订单');  //设置开始结束时间

        $('#pay_search_btn').click(function() {
            showLoading();
            spliceSearchParams(timeRange);
        });

        //	处理退款窗口中的操作 + 事件     //已经关闭手动退款
        // handleRefundEvent();

        //筛选参数的回显
        reBack();

        updateTime(timeRange);

    };


    function updateTime (time) {
        $('#startTime').val(time.start_time);
        $('#endTime').val(time.end_time);
        if(time.start_time && time.start_time!='2016'){
            $('#SelectData').text(time.start_time + ' ~ ' + time.end_time);
        } else {
            $('#SelectData').text('全部订单');
        }
    }


    //回显搜索框内的值
    function reBack() {
        var startTime = GetQueryString('start_time'),
            endTime = GetQueryString('end_time');

        timeRange = {
            start_time: startTime || '',
            end_time: endTime || ''
        }
    }

    //	处理退款窗口中的操作 + 事件             //已经关闭手动退款
    // function handleRefundEvent() {
    //
    //     $("#handleRefund").click(function () {			//	点击退款处理按钮
    //         showLoading();
    //         pageIndex = 1;
    //         getRefundList(function () {
    //             hideLoading();
    //             $(".refundListWindowBg").show();
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
    //             baseUtils.show.redTip("请先选择退款用户");
    //             return false;
    //         }
    //         totalPrice = Math.round(totalPrice * 100) / 100;
    //
    //         $(".refundListWindowBg").hide();
    //
    //         $(".confirmRefundWindowBg .confirmRefundWindowText").text("确定给 " + questionArr.length + " 位用户退款 " + totalPrice + " 元");
    //
    //         $(".confirmRefundWindowBg").fadeIn(300);
    //
    //     });
    //
    //     $(".closeConfirmRefundWindow img, .confirmRefundWindowBtn_1").click(function () {   //	点击取消
    //         $(".confirmRefundWindowBg").fadeOut(100);
    //         $(".refundListWindowBg").fadeIn(300);
    //     });
    //     $(".confirmRefundWindowBtn_2").click(function () {   //	点击确认
    //
    //         confirmRefund();
    //     });
    //
    //     $("#searchRefundUser").click(function () {          //  搜索
    //         pageIndex = 1;
    //         searchRefundListContent = $.trim($("#searchRefundUserInput").val());
    //         $(".loadingS").show();
    //         getRefundList(function () {
    //             $(".loadingS").fadeOut(100);
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
    //             $(".loadingS").show();
    //             getRefundList(function () {
    //                 $(".loadingS").fadeOut(100);
    //             });
    //         }
    //     });
    //
    // }

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
    //                     $area.append('<div class="isDown">资源加载中</div>');
    //
    //                 } else {
    //                     $area.find('.isDown').before(htmlString);
    //
    //                 }
    //                 if (pageObj.to >= pageObj.total) {      //数据加载完毕后的操作
    //                     if (pageObj.total > 10) {
    //                         $area.find('.isDown').text("资源已加载完毕");
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
    //      });
    //
    // }

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
    // }

    // function clearRefundWindowData() {
    //     pageIndex = 1;
    //     isWindowClose = true;
    //     searchRefundListContent = '';
    //     $("#searchRefundUserInput").val("");
    //
    // }

    return individualModel;

})();
