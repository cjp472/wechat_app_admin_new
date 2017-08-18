/**
 * Created by Neo on 2017/3/8.
 */
$(function () {
    activeManageBusiness.init();
});


//业务类
var activeManageBusiness = (function () {
    var activeManageBusiness = {};
    var activeId;

    //活动状态更新操作（取消活动，上下架活动）
    function statusUpdate(sType, sActivityId) {
        $.post('/updateActivityState', {activity_id: sActivityId, type: sType}, function (data) {
            if (data.code == -1) {
                baseUtils.show.redTip(data.msg);
            } else {
                baseUtils.show.blueTip(data.msg);
                if (sType == 2) {
                    $('.activeCancelBox,.darkScreen').fadeOut(300);
                }
                setTimeout(function () {
                    window.location.reload();
                }, 200)
            }
        });
    }

    activeManageBusiness.init = function () {

        //复制到剪贴板
        (function () {
            var clipboard = new Clipboard('.copyHref');
            clipboard.on('success', function(e) {
                baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
                e.clearSelection();
            });
        })();

        // 点击取消活动
        $(".activeClose").click(function () {
            $('.activeCancelBox,.darkScreen').fadeIn(300);
            activeId = $(this).data("activeid");
        })

        //确认取消活动
        $(".CancelBoxBtnDelete").click(function () {
            statusUpdate(2, activeId);
        });


        //关闭"取消活动"的浮层
        $(".CancelBoxClose,.CancelBoxBtnCancel").click(function () {
            $('.activeCancelBox,.darkScreen').fadeOut(300);
        })


        //根据活动名称搜索活动
        $('.activeSearchBtn').click(function () {
            var searchContent = encodeURI($.trim($('#actSearchInput').val()));
            var url;
            if ($pageParm.pageType === 0) {
                url = "/activityManage?searchContent=" + searchContent;
            }
            else {
                url = "/activityListEnd?searchContent=" + searchContent;
            }
            window.location = url;

        })

        //活动上架
        $('.activeUp').click(function () {
            activeId = $(this).data("activeid");
            statusUpdate(0, activeId);
        });

        //活动下架
        $('.activeDown').click(function () {
            activeId = $(this).data("activeid");
            statusUpdate(1, activeId);
        })

        // 提交错误处理
        $(document).ajaxError(function() {
            baseUtils.show.redTip("网络错误，操作失败!");
        });

    };
    return activeManageBusiness;
})();
