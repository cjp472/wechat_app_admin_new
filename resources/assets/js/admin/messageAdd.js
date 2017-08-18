/**
 * Created by Stuphin on 2016/9/30.
 */

// 输入网址是否正确
var $is_address_right = true;

$(document).ready(function()
{

    $.cookie('content_create')? setTopUrlInfo('content_create') : setTopUrlInfo('message_listop');
    ////初始化时间选择器
    //$("input[name='send_at']").datetimepicker({
    //    //初始化时间选择器
    //    startDate:new Date(),
    //    weekStart: 1,
    //    minView: "day",
    //    autoclose: true//选择日期后自动关闭
    //});

    bindElement();

    /*检测外链网址的输入错误时，添加文字提醒*/
    $('#sub_input.form-control').bind('input propertychange', function() {
        var input_address = document.getElementById("sub_input").value;
        if (input_address == '' || !input_address.indexOf("http://") || !input_address.indexOf("https://")) {
            // 网址正确
            $('.http_error_tip').addClass("hide");
            $is_address_right = true;
        } else {
            // 网址错误
            $('.http_error_tip').removeClass("hide");
            $is_address_right = false;
        }
    });
    $("#type_selector").change(function(){
        var skip_type = $(this).children(":selected").val();
        if (skip_type !== 'h5'){
            // 网址正确
            $('.http_error_tip').addClass("hide");
            $(".modal-footer").css({"margin":"45px 15px 0"}).css({"padding":"30px 0px"});
            $is_address_right = true;
        } else {
            $("#sub_input").val("");
        }
    })

    datetimepickerconfig("input[name='send_at']");

    //获取当前时间
    function getNowFormatDate() {
        var date = new Date();
        var seperator1 = "-";
        var seperator2 = ":";
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var hour=date.getHours();
        var minute=date.getMinutes();
        var second=date.getSeconds();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (day >= 0 && day <= 9) {
            day = "0" + day;
        }
        if (hour >= 0 && hour <= 9) {
            hour = "0" + hour;
        }
        if (minute >= 0 && minute <= 9) {
            minute = "0" + minute;
        }
        if (second >= 0 && second <= 9) {
            second = "0" + second;
        }
        var currentdate = date.getFullYear() + seperator1 + month + seperator1 + day
            + " " + hour + seperator2 + minute + seperator2 + second;
        return currentdate;
    }
    var now=getNowFormatDate();
    $("input[name='send_at']").val(now);

    //推送按钮
    $(".addButton").click(function()
    {


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
        if (!$is_address_right) {
            window.wxc.xcConfirm('外部链接输入有问题','error');
            return false;
        }

        //跳转链接
        var params = {};
        params['skip_target'] = $('#sub_selector').find('option:selected').val();
        params['skip_title'] = $('#sub_selector').find('option:selected').text();
        var skip_type = $('#type_selector').find('option:selected').val();

        if($('#message_selector').length>0){
            params['message_audio'] = $('#message_selector').find('option:selected').val();
        }else{
            params['message_audio'] = '';
        }

        if (skip_type == "audio") {
            skip_type = 2;
        } else if (skip_type == "video") {
            skip_type = 3;
        } else if(skip_type == "alive"){
            skip_type = 7;
        } else if (skip_type == "image_text") {
            skip_type = 1;
        } else if (skip_type == "package") {
            skip_type = 6;
        }else if (skip_type == "h5") {
            skip_type = 5;
            params['skip_target'] = $('#sub_input').val().trim();
        } else {
            skip_type = 0;
            params['skip_target'] = '';
        }
        params['skip_type'] = skip_type;

        if (skip_type == 2 || skip_type == 3 || skip_type == 7 || skip_type == 1 || skip_type ==6) {
            if (params['skip_title'] == "") {
                baseUtils.show.blueTip("请选择跳转资源链接!");
                return false;
            }
        }
        params['skip_title'] = $('#url_title').val();
        // console.log(params);
        // return false;
        $.post('/messagesave',{"sendAt":sendAt,"sendNickName":sendNickName,"content":content,'params':params},function(data)
        {
            if(data.ret == 0)
            {
                window.wxc.xcConfirm("推送成功","success");
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

function bindElement() {
    $("#type_selector").on('change', function() {
        var type = $(this).find('option:selected').val();

        if (type == 'h5') {
            $('#sub_selector').empty();
            $('#sub_selector').addClass('hide');
            $('#sub_input').removeClass('hide');
            return;
        } else if (type == 'no_jump') {
            $('#sub_selector').empty();
            $('#sub_selector').removeClass('hide');
            $('#sub_input').addClass('hide');
            return;
        } else {
            $('#sub_selector').empty();
            $('#sub_selector').removeClass('hide');
            $('#sub_input').addClass('hide');
        }

        $.get('/banner/getResourceList?type=' + type , function (result) {
            $('#sub_selector').empty();
            result = JSON.parse(result);

            var count = result.length;

            var htmlStr = "";

            for (var i = 0; i < count; i++) {
                htmlStr+="<option value='"+result[i].id+"'>"+result[i].title+"</option>";
            }
            $('#sub_selector').append(htmlStr);
        });
    });
}