/**
 * Created by Stuphin on 2016/9/26.
 */

$(document).ready(function()
{
    //  Content区域小标题
    showContentTitle();
    if (is_huidu == 1) {    //  灰度用户，显示
        appendContentHeader("账户一览", "/accountview", false);
    }
    appendContentHeader("账号管理", "/accountmanage", false);
    appendContentHeader("接入配置", "/h5setting", false);
    appendContentHeader("小程序设置", "/smallprogramsetting", true);

    //小程序点击保存
    /*$("#smallProgramAuto").click(function()
    {
        var params={};
        //小程序AppId
        params['wx_app_id']=$("#sm_wx_app_id").val().trim();
        if(params['wx_app_id'].length == 0)
        {
            //window.wxc.xcConfirm('亲！请输入小程序AppId哦~','error');
            baseUtils.show.redTip('亲！请输入小程序AppId哦~');
            $("#sm_wx_app_id").focus();
            return false;
        }

        updateSmallProgram(params);
    });*/
});

//更新小程序的录入信息,暂时未使用
/*function updateSmallProgram(params)
{
    //if (upload_file_name != "") {
    //    params['wx_bus_verify_txt'] = upload_file_name;
    //}

    $.post("/updateSmallProgram",{"params":params},function (data)
    {
        hideLoading();
        if (data.ret == 0)
        {
            baseUtils.show.blueTip("保存成功!", function ()
            {
                window.location.href = '/accountmanage';
                if (data.url && data.url.length > 0) {
                    window.open(data.url);
                }
            });
        }
        else
        {
            baseUtils.show.redTip("上传失败!");
        }
    });
}*/




