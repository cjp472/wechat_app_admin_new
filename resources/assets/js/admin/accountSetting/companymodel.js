/**
 * Created by Stuphin on 2016/9/26.
 */
$(document).ready(function()
{
    init();
    authHandle();
});

//初始化
function init()
{
    /*//  Content区域小标题
    showContentTitle();
    if (is_huidu == 1) {
        appendContentHeader("账户一览", "/accountview", false);
    }
    appendContentHeader("账号管理", "/accountmanage", false);
    appendContentHeader("运营模式设置", "/companymodel", true);*/
    // appendContentHeader("小程序设置", "/smallprogramsetting", false);
}

//授权相关
function authHandle()
{
    var app_id=$("#xcx_app_id").val();

    //点击后先把授权态变为1,再跳转到新的页面
    $("#bindNow").click(function()
    {
        var newWin = window.open('','_blank');

        $.get("/updateifauth",{"app_id":app_id},function(data)
        {
            if(data.ret==0)
            {
                // window.open(authUrl+app_id);
                newWin.location.href = authUrl+app_id;
                $("#bindModal").modal("show");
            }
            else
            {
                baseUtils.show.redTip("系统繁忙");
            }
        });

        // window.open(authUrl+app_id);
        // $("#bindModal").modal("show");
    });

    //授权成功按钮点击验证扫码授权结果
    $("#bindSuccess").click(function()
    {
        $.get("/check_auth_result",{"app_id":app_id},function(data)
        {
            if(data.ret==0){
                $("#bindModal").modal("hide");
                window.location.href = '/companymodel?change=1';
            }
            else
            {
                window.location.reload();
            }
        });
    });

    //授权失败，继续弹出
    $("#bindFail").click(function()
    {
        var app_id=$("#xcx_app_id").val();
        window.open(authUrl+app_id);
        $("#bindModal").modal("show");
    });

    //关闭顶部提示
    $('#closebtn').on('click', function() {
        $(this).parent().hide();
    })

/*
    //选择个人运营模式,跳出弹出框
   $("#choiceModel").click(function()
    {
        $("#explainModal").modal("show");
    });

    //确认选择个人运营模式,提交
    $("#confirmChoice").click(function () {

        var app_id=$("#xcx_app_id").val();

        $.post("/updateCollection",{"app_id":app_id},function(data)
        {
            if(data.ret==0)
            {
                window.location.href = '/h5setting';
            }
            else
            {
                baseUtils.show.redTip("系统繁忙,请稍后重试!");
            }
        });
    });
*/

}
