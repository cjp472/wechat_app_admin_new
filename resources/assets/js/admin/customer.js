/**
 * Created by Stuphin on 2016/9/28.
 */

var sms_name;
var sms_content;
var model_id;
var needRefresh = false;
// 输入网址是否正确
var $is_address_right = true;

$(document).ready(function() {

    refresh();

    initMsgModel();

    bindElement();

    //搜索
    $("#searchUser").click(function () {
        showLoading();
        var is_pay = $("select[name='is_pay']").val();
        var ruler = $("select[name='ruler']").val();
        var search = $.trim($("#searchContent").val());
        window.location.href="/customer?is_pay="+is_pay+"&ruler="+ruler+"&search="+search;
    });

    //回车搜索
    $("#searchContent").keypress(function(e) {
        if(e.which == 13) {
            $('#searchUser').click();
        }
    });

    $("select[name='is_pay']").on("change", function () {
        $('#searchUser').click();
    });

    //  检测外链
    checkUrl();

});
//调到详情页
function jumpDetail(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    window.location.href='/customerdetail?appId='+appId+'&userId='+userId;
}

//发消息,先走这个js再走弹框
function jumpMsg(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    $("#SmsModal").modal('show');

    //  将链接类型设置为 外部链接
    document.getElementById("link_type_selector")[0].selected=true;
    //  将右侧设置为输入框
    $('#skip_target_selector').empty();
    $('#skip_target_selector').addClass('hide');
    $('#skip_target_input').removeClass('hide');

    //确认按钮点击
    $(".modal-footer").children("button").eq(0).unbind("click").bind("click" , function ()
    {
        var nickname=$("#sms_nickname").val();
        var content=$("#sms_content").val();
        if(nickname.length == 0) {
            $.alert("亲，还未输入发送人昵称哦~", "error", {btn: 2});
            return false;
        }
        if(content.length == 0) {
            $.alert("亲，还未输入消息内容哦~", "error", {btn: 2});
            return false;
        }
        if (!$is_address_right) {
            $.alert("亲，外部链接输入有问题哦~", "error", {btn: 2});
            return false;
        }

        $("#SmsModal").modal('hide');
        showLoading();

        //  获取链接名称+链接地址(根据类型确定)
        var params = {};
        var skip_type = $('#link_type_selector').find('option:selected').val();

        if (skip_type == "audio") {
            skip_type = 2;
        } else if (skip_type == "video") {
            skip_type = 3;
        } else if (skip_type == "image_text") {
            skip_type = 1;
        } else if(skip_type == "alive"){
            skip_type = 7;
        } else if (skip_type == "package") {
            skip_type = 6;
        }else if (skip_type == "h5") {
            skip_type = 5;
        } else {
            skip_type = 0;
        }
        params['skip_type'] = skip_type;

        if (skip_type == 5) {
            params['skip_target'] = $('#skip_target_input').val().trim();
        } else if (skip_type == 0) {
            params['skip_target'] = '';
        } else {
            params['skip_target'] = $('#skip_target_selector').find('option:selected').val();
        }

        if (skip_type == 2 || skip_type == 3 || skip_type == 1 || skip_type ==6) {
            if (params['skip_target'] == "") {
                baseUtils.show.blueTip("请选择跳转资源链接!");
                return false;
            }
        }
        if (skip_type != 0) {
            params['skip_title'] = $('#link_name').val();
        } else {
            params['skip_title'] = '';
        }

        $.post("/customermsg",{"appId":appId,"userId":userId,"nickname":nickname,"content":content,"params":params},function(data)
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
//判断是否需要刷新
function refresh() {
    $("#SmsModal").on('hide.bs.modal', function () {
        //判断是否需要刷新界面
        if (needRefresh) {
            location.reload(); //重新加载页面
        }
    });
}

//  将链接类型 和 输入框关联
function bindElement() {
    $("#link_type_selector").on('change', function() {
        var type = $(this).find('option:selected').val();

        if (type == 'h5') {
            $('#skip_target_selector').empty();
            $('#skip_target_selector').addClass('hide');
            $('#skip_target_input').removeClass('hide');
            return;
        } else if (type == 'no_jump') {
            $('#skip_target_selector').empty();
            $('#skip_target_selector').removeClass('hide');
            $('#skip_target_input').addClass('hide');
            return;
        } else {
            $('#skip_target_selector').empty();
            $('#skip_target_selector').removeClass('hide');
            $('#skip_target_input').addClass('hide');
        }

        $.get('/banner/getResourceList?type=' + type , function (result) {
            $('#skip_target_selector').empty();
            result = JSON.parse(result);

            var count = result.length;

            var htmlStr = "";

            for (var i = 0; i < count; i++) {
                htmlStr+="<option value='"+result[i].id+"'>"+result[i].title+"</option>";
            }
            $('#skip_target_selector').append(htmlStr);
        });
    });
}

function checkUrl() {
    /*检测网址的输入错误时，添加文字提醒*/
    $('#skip_target_input.form-control').bind('input propertychange', function () {
        var input_address = document.getElementById("skip_target_input").value;
        if (input_address == '' || !input_address.indexOf("http://") || !input_address.indexOf("https://")) {
            // 网址正确
            $('.http_error_tip').addClass("hide");
            $(".modal-footer").css({"margin": "45px 15px 0"}).css({"padding": "30px 0px"});
            $is_address_right = true;
        } else {
            // 网址错误
            $('.http_error_tip').removeClass("hide");
            $(".modal-footer").css({"margin": "0 15px 0"}).css({"padding": "15px 0 0 30px"});
            $is_address_right = false;
        }
    });
    $("#link_type_selector").change(function () {
        var skip_type = $(this).children(":selected").val();
        if (skip_type !== 'h5') {
            // 网址正确
            $('.http_error_tip').addClass("hide");
            $(".modal-footer").css({"margin": "45px 15px 0"}).css({"padding": "30px 0px"});
            $is_address_right = true;
        } else {
            $("#skip_target_input").val("");
        }
    })
}