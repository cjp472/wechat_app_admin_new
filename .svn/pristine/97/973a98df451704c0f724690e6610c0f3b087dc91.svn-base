

$(function(){

    // 修改表单disabled状态
    var chageIuputDisable = function(selector, bol) {
        console.log('chageIuputDisable')
        if (bol) {
            $(selector).attr("disabled", false);
        } else {
            $(selector).attr("disabled", true);
        }
    };

    // 保存修改
    $('#save').click(function() {
        var params = {};
        // pc链接
        params['web_jump_url'] = $.trim($("#webUrl").val());
        // 推送链接
        params['msg_push_url'] = $.trim($("#pushUrl").val());
        // appSecret
        params['app_secret'] = $.trim($("#appSecret").val());
        // 是否开启pc跳转
        params['need_jump'] = $('#openWebUrl').is(':checked') ? 1 : 0;
        // 是否开启消息推送
        params['need_push'] = $('#openPushUrl').is(':checked') ? 1 : 0;
        var cmd = '/open/modifySetting';
        $.post(cmd, params, function(data) {
            if (data.code === 0) {
                baseUtils.show.blueTip('保存成功');
            } else {
                baseUtils.show.redTip('保存失败，请稍后重试');
            }
        }, 'json');
    });

    // 开启pc跳转
    $("#openWebUrlLabel").click(function () {
        chageIuputDisable('#webUrl', true);
    });

    // 关闭pc跳转
    $("#closeWebUrlLabel").click(function () {
        chageIuputDisable('#webUrl', false);
    });

    // 开启push
    $("#openPushUrlLabel").click(function () {
        chageIuputDisable('#pushUrl', true);
    });

    // 关闭push
    $("#closePushUrlLabel").click(function () {
        chageIuputDisable('#pushUrl', false);
    });




});
