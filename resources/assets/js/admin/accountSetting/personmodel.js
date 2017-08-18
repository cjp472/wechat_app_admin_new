/**
 * Created by Stuphin on 2016/9/26.
 */

$(document).ready(function(){
    init();
    authHandle();
});

//初始化
function init()
{
    /*//  Content区域小标题
    showContentTitle();
    if (is_huidu == 1) {    //  灰度用户，显示
        appendContentHeader("账户一览", "/accountview", false);
    }
    appendContentHeader("账号管理", "/accountmanage", false);
    appendContentHeader("运营模式设置", "/personmodel", true);*/
}

//授权相关
function authHandle()
{
    //初始化剪贴板
    var clipboard = new Clipboard('.copyHref');
    clipboard.on('success', function(e) {
        baseUtils.show.blueTip("复制成功！请在微信内打开哦。");
        e.clearSelection();
    });
/*    //更改支付方式
    $("#bindNow").click(function()
    {
        $("#bindModal").modal("show");
    });

    //立即配置
    $("#toBindWx").click(function()
    {
        $("#bindModal").modal("hide");
        window.location.href = '/h5setting?change=1';
    });

    //正在使用
    $("#toCollection").click(function()
    {
        $("#bindModal").modal("hide");
    });*/
}





