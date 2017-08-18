// 显示toast提示消息(用这个）
var $showToastMsg = (function () {
    var $showToastMsg = {};

    // 显示提示( type 分为 notice/success/error/warning
    $showToastMsg.showTips = function (type,msg,callBack) {
        $().toastmessage('showToast', {
            text     : msg,
            sticky   : false,
            stayTime : 1500,
            position : 'top-right',
            type     : type,
            close    : callBack
        });
    };

    // 成功提示
    $showToastMsg.successTips = function (msg,callBack) {
        this.showTips("success",msg,callBack)
    };

    // 错误提示
    $showToastMsg.errorTips = function (msg,callBack) {
        this.showTips("error",msg,callBack)
    };


    // 标识提示
    $showToastMsg.noticeTips = function (msg,callBack) {
        this.showTips("notice",msg,callBack)
    };

    // 警告提示
    $showToastMsg.warnTips = function (msg,callBack) {
        this.showTips("warning",msg,callBack)
    };

    return $showToastMsg;

})();