/**
 * Created by Stuphin on 2016/9/28.
 */
$(document).ready(function()
{

    //搜索
    $("#searchButton").click(function() {
        showLoading();
        var ruler=$("select[name='ruler']").val();
        var apptype = '';
        if(microfunc){
            var apptype=$("select[name='apptype']").val();
        }
        var search=$("input[name='search']").val();
        var forbid = getUrlParam('forbid');
        forbid = forbid? '&forbid=1' : '';
        if(microfunc){
            var apptype=$("select[name='apptype']").val();
            window.location.href="/feedback?ruler="+ruler+"&search="+search+'&apptype='+apptype+forbid;
        }else{
            window.location.href="/feedback?ruler="+ruler+"&search="+search+forbid;
        }
        hideLoading();
    });

    //回车搜索
    keyEnter($("#searchButton"));

    $("select[name=apptype]").on("change", function () {
        $("#searchButton").click();
    });


    initMsgModel();
    $('.msg_glyphicon').on('mouseover', function () {
        $(this).find('.msg_showbox').show();
    }).on('mouseleave', function () {
        $(this).find('.msg_showbox').hide();
    });
    $('#msg_glyphicon').on('mouseover', function () {
        $('.msg_showbox').show();
    }).on('mouseleave', function () {
        $('.msg_showbox').hide();
    }).on('click', function () {
        $('.msg_showbox').toggle();
    });

});
//调到详情页
function jumpDetail(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    resetUrl('/customerdetail?appId='+appId+'&userId='+userId);
}

//user+forbid
function forbid(info)
{
    var info=info;
    var userId=info.split("|")[0];
    var stat=info.split("|")[1];

    showLoading();
    $.post("/forbid",{"userId":userId,"stat":stat},function(data)
    {
        hideLoading();
        if(data.code==0)
        {
            baseUtils.show.blueTip("操作成功!", function () {
                window.location.reload();
            });
        }
        else
        {
            window.wxc.xcConfirm("系统繁忙，请稍后再试!","error");
        }
    });

}

//发消息,先走这个js再走弹框
function jumpMsg(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    var fdId=info.split("|")[2];
    $("#SmsModal").modal('show');
    //确认按钮点击
    $(".modal-footer").children("button").eq(0).unbind("click").bind("click" , function ()
    {
        var nickname=$("#sms_nickname").val();
        var content=$("#sms_content").val();
        if(nickname.length == 0)
        {
            window.wxc.xcConfirm('亲，还未输入发送人昵称哦~','error');
            return false;
        }
        if(content.length == 0)
        {
            window.wxc.xcConfirm('亲，还未输入消息内容哦~','error');
            return false;
        }
        $("#SmsModal").modal('hide');
        showLoading();
        $.post("/customermsg",{"appId":appId,"userId":userId,"feedbackId":fdId,"nickname":nickname,"content":content},function(data)
        {
            hideLoading();
            if(data.ret==0)
            {
                //window.wxc.xcConfirm("发送成功","success",{onOk:function ()
                //{
                //    window.location.reload();
                //}});
                baseUtils.show.blueTip("发送成功!", function () {
                    window.location.reload();
                });
            }
            else
            {
                window.wxc.xcConfirm("系统繁忙，请稍后再试!","error");
            }
        });
    });
}

function initMsgModel() {

    $('.model_type').on("click",function () {
        $('.model_type').removeClass('border_blue');
        $(this).addClass('border_blue');
        $('#sms_nickname').val($(this).attr('data-name'));
        $('#sms_content').val($(this).attr('data-content'));
        if($('#cancel_model').hasClass('border_blue')){
            $('.edit_model').addClass('hide');
            $('.model_type').removeClass('border_blue');
        }
        model_id = $(this).attr('data-id');
        sms_name = $(this).attr('data-name');
        sms_content = $(this).attr('data-content');
    })

    $('#sms_content').keyup(function () {
        if($('.border_blue').length >0){
            $('.edit_model').removeClass('hide');
        }
    });

    $('#sms_nickname').keyup(function () {
        if($('.border_blue').length >0){
            $('.edit_model').removeClass('hide');
        }
    });

    $('.edit_model').on("click",function () {
        //数据库不存在相关模板
        model_id = $('.border_blue').attr('data-id');
        var name = $('#sms_nickname').val();
        var content= $('#sms_content').val();
        if(model_id==""){
            //向后台插入模板
            model_id = 0;
            if(name!="" && content!=""){
                console.log("新增模板："+model_id+">>"+name+">>"+content);
            }else{
                baseUtils.show.redTip('您的发送人昵称/内容为空');
                return;
            }
        }else{
            //更新原有模板
            if(name!="" && content!=""){
                console.log("更新模板："+model_id+">>"+name+">>"+content);
            }else{
                baseUtils.show.redTip('您的发送人昵称/内容为空');
                return;
            }
        }
        if(name == sms_name && content == sms_content){
            baseUtils.show.redTip('您没有做任何模板修改');
        }else{
            showLoading();
            var url = "/modelchange";
            $.post(url, {'model_id': model_id,'send_nick_name':name,'content':content},function (result) {
                hideLoading();
                console.log(result);
                var ReCode = result.code;
                var info = result.msg;
                baseUtils.show.blueTip(info);
                if(ReCode==0){ //新建模板成功 并设置当前最新插入的渠道id
                    $('.border_blue').attr('data-id',result.id);
                    $('.border_blue').attr('data-name',name);
                    $('.border_blue').attr('data-content',content);
                    needRefresh = true;
                }else if(ReCode==1){ //错误
                    console.log(result.errorMsg);
                }else if(ReCode==2){ //更新模板成功
                    $('.border_blue').attr('data-name',name);
                    $('.border_blue').attr('data-content',content);
                    needRefresh = true;
                }
            });
        }
    });
}

