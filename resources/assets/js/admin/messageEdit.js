/**
 * Created by Stuphin on 2016/9/30.
 */
$(document).ready(function()
{

    ////初始化时间选择器
    //$("input[name='send_at']").datetimepicker({
    //    startDate:new Date(),
    //    weekStart: 1,
    //    minView: "day",
    //    autoclose: true//选择日期后自动关闭
    //});

    datetimepickerconfig("input[name='send_at']");


    //更新按钮
    $(".editButton").click(function()
    {
        var id=getUrlParam("id");
        var sendAt=$("input[name='send_at']").val();
        var sendNickName=$("input[name='send_nick_name']").val();
        var content=$("textarea[name='content']").val();
        //校验
        if(sendAt.length == 0)
        {
            window.wxc.xcConfirm("请输入推送时间","error");
            return false;
        }
        if(sendNickName.length == 0)
        {
            window.wxc.xcConfirm("请输入发送人昵称","error");
            return false;
        }
        if(content.length == 0)
        {
            window.wxc.xcConfirm("请输入推送内容","error");
            return false;
        }
        //更新
        $.post('/messageupdate',{"id":id,"sendAt":sendAt,"sendNickName":sendNickName,"content":content},
        function(data)
        {
            if(data.ret == 0)
            {
                window.wxc.xcConfirm("保存成功","success");
                setTimeout(function()
                {
                    window.location.href="/message";
                },2000);
            }
            else
            {
                window.wxc.xcConfirm("系统繁忙，请稍后再试","error");
            }
        });
    });

});