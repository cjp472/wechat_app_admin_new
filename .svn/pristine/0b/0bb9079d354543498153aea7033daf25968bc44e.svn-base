/**
 * Created by Stuphin on 2016/9/29.
 */
$(document).ready(function()
{
    
    //提交
    $(".editSaveButton").click(function()
    {
        var appId=getUrlParam("appId");
        var userId=getUrlParam("userId");
        
        var name=$("input[name='name']").val();
        var address=$("input[name='address']").val();
        var company=$("input[name='company']").val();
        var job=$("input[name='job']").val();
        var industry=$("input[name='industry']").val();
        $.get("/customerupdate",{"name":name,"address":address,"company":company,"job":job,"industry":industry,
        "appId":appId,"userId":userId},
        function(data)
        {
            if(data.ret==0)
            {
                window.wxc.xcConfirm("修改成功","success",{onOk:function()
                {
                    setTimeout(window.location.href='/customerdetail?appId='+appId+'&userId='+userId,2000);
                }});
            }
            else 
            {
                window.wxc.xcConfirm("系统繁忙，请稍后再试!","error");
            }
        });
    });
});