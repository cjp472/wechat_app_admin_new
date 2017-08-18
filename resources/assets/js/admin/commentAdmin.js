var needRefresh = false;

var user_id;
var comment_id;

var search_content;
var comment_attr;
var comment_state;

var resource_type;

//  排序参数
var orderParameter = '';


$(document).ready(function () {
    refresh();
    reBack();
    keyEnter($('#comment_search_btn'));

    $("tbody tr").mouseover(function()
    {
        $(this).css({'background-color':'#f5f5f5'});
    }).mouseout(function()
    {
        $(this).css({'background-color':'#fff'});
    });
    setTopUrlCookie('comment_listop','内容评论列表');
    var reurlinfo = GetQueryString('reurl');
    if(reurlinfo)
    {
        if(reurlinfo.indexOf('audio_list')>0){
            setTopUrlInfo('audio_listop');
        }
        if(reurlinfo.indexOf('video_list')>0){
            setTopUrlInfo('video_listop');
        }
        if(reurlinfo.indexOf('article_list')>0){
            setTopUrlInfo('article_listop');
        }
    }

    initMsgModel();

    $(".avatar_nickname").click(function () {
        var app_id = $(this).data("app_id");
        var user_id = $(this).data("user_id");

        jumpDetail(app_id + "|" + user_id);
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
//模糊查询评论
function  searchComment(){
    showLoading();
    var commentAttr = $("#comment_attr").val(); //获取到选中的值
    //获取搜索内容
    var search_content = $("#comment_search_content").val(); //获取到选中的值
    //评论状态
    var comment_state = $("#comment_state").val(); //获取所选状态
    var apptype_search = '';
    if(microfunc){
        var apptype=$("select[name='apptype']").val();
        apptype_search = '&apptype='+apptype;
    }
    if (search_content.length == 0) {
        var url = "/comment_admin?comment_attr=" + encodeURI(commentAttr)+'&comment_state='+comment_state+apptype_search + '&order_parameter=' + orderParameter;
    } else {
        var url = "/comment_admin?comment_attr=" + encodeURI(commentAttr) + "&search_content=" + encodeURI(search_content)+'&comment_state='+comment_state+apptype_search + '&order_parameter=' + orderParameter;
    }

    window.location = url;
}

//改变 置顶 状态
function changeTopState(id ,currentTopState , type) {
    showLoading();
    var tid = '#state_' + id;
    var btnTopid = '#btnTop_'+id;
    var resource_type = type;
    var url = "";
    if(currentTopState==0){
        url = "/update_comment_state?id=" + id + "&state=1" +"&type=top&resource_type=" + resource_type;
    }else{
        url = "/update_comment_state?id=" + id + "&state=0" +"&type=top&resource_type=" + resource_type;
    }
    //修改界面信息
    $.get(url,function(result) {
        hideLoading();

        if(result.code==0){
            //当前状态：未置顶
            if(currentTopState==0){
                        // $(tid).children().eq(0).attr('class','btn btn-primary btn-sm');
                        // $(tid).children().eq(0).html('精选');
                        // $(tid).children().eq(0).attr('color','white');
                        $(btnTopid).html('取消精选');
                        $(btnTopid).val(1);
            }else{
                        // $(tid).children().eq(0).attr('class','btn btn-link btn-sm');
                        // $(tid).children().eq(0).html('');
                        $(btnTopid).html('精选评论');
                        $(btnTopid).val(0);
            }
        }
        baseUtils.show.blueTip(result.msg);
        window.location.reload();
    })
}

// 改变显示/隐藏状态
function changeState(id ,currentState,record_id,type)
{
    showLoading();
    var tid = '#state_' + id;
    var btnStateid = '#btn_' + id;
    var record_id=record_id;
    var resource_type = type;
    console.log('>>>'+resource_type);

    var url = "";
    if (currentState == 0)
    {
        url = "/update_comment_state?id=" + id + "&state=1"+"&type=show"+"&record_id="+record_id+"&resource_type="+resource_type;
    }
    else
    {
        url = "/update_comment_state?id=" + id + "&state=0"+"&type=show"+"&record_id="+record_id+"&resource_type="+resource_type;
    }
    //更新界面
    $.get(url, function (result)
    {
        hideLoading();
        if (result.code == 0) {
            //当前评论状态：显示
            if (currentState == 0) {
                // $(tid).children().eq(0).attr('class', 'btn btn-danger btn-sm');
                // $(tid).children().eq(0).html('隐藏');
                // $(tid).children().eq(0).attr('color', 'white');
                $(btnStateid).html('显示');
                $(btnStateid).val(1);
            }
            else
            {
                // $(tid).children().eq(0).attr('class', 'btn btn-link btn-sm');
                // $(tid).children().eq(0).html('');
                $(btnStateid).html('隐藏');
                $(btnStateid).val(0);
            }
        }
        baseUtils.show.blueTip(result.msg);
        window.location.reload();
    })
}

function hideDialog(){

    $(".operate_dialog").hide();

};

function replaySelect(user_id, comment_id,isModel) {
    this.user_id = user_id;
    this.comment_id = comment_id;
    if(isModel){ //模板回复
        //设置好模板内容
        $('#comment_admin_name').val("巴九灵");
        $('#replay_content').text("您如果遇到无法收听或其它异常情况，可以联系我们技术小哥寻求解决（微信或电话：17817812015）。谢谢！");
        $('#replay_content').attr("readonly","readonly");
        $('#replay_content').css("background-color","#fff");
    }else{
        $('#comment_admin_name').val("");
        $('#replay_content').text("");
        $('#replay_content').removeAttr("readonly");

    }
}
//回复
function replay() {
    var replay_content = $("#replay_content").val();
    var comment_admin_name = $("#comment_admin_name").val();
    if (!checkForm(replay_content, comment_admin_name)) {
        return;
    }
    showLoading();

    var url = "/submit_admin_comment";
    $.post(url, {
        'replay_content': replay_content,
        'comment_admin_name': comment_admin_name,
        'user_id': user_id,
        'comment_id': comment_id,
    }, function (result) {
        needRefresh = true;
        hideLoading();
        console.log(result);
        var code = result.code;
        var msg = result.msg;
        setTimeout(function () {
            baseUtils.show.blueTip(msg);
            console.log("code:" + code);
            if (code == 0) {
                $('#replayModal').modal('hide');
            }
        }, 100);
    });
}

//核对表单填写情况
function checkForm(replay_content, comment_admin_name) {

    if (replay_content.length == 0) {
        baseUtils.show.redTip("回复内容不能为空");
        return false;
    }

    if (comment_admin_name.length == 0) {
        baseUtils.show.redTip("管理员昵称不能为空");
        return false;
    }

    return true;
}

function refresh() {
    $("#replayModal").on('hide.bs.modal', function () {
        //判断是否需要刷新界面
        if (needRefresh) {
            location.reload(); //重新加载页面
        }
    });
}

//回显输入框和选择框的值
function reBack() {
    if(microfunc==1){
        $("select[name='apptype']").val((getUrlParam("apptype")==null  || getUrlParam("apptype")=='') ? 0 :getUrlParam("apptype"));
    }

    var comment_search_content = document.getElementById("comment_search_content");

    if (search_content != "") {
        comment_search_content.value = search_content;
        var comment_attr_element = document.getElementById("comment_attr");
        if (comment_attr_element.length > 0) {
            for (var i = 0; i < comment_attr_element.options.length; i++) {
                if (comment_attr_element.options[i].value == comment_attr) {
                    comment_attr_element.options[i].selected = true;
                    break;
                }
            }
        }
    }

    var comment_state_element = document.getElementById("comment_state");
    if (comment_state_element.length > 0) {
        for (var i = 0; i < comment_state_element.options.length; i++) {
            if (comment_state_element.options[i].value == comment_state) {
                comment_state_element.options[i].selected = true;
                break;
            }
        }
    }
}

//发消息,先走这个js再走弹框
function jumpMsg(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    var cmId=info.split("|")[2];
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
        $.post("/customermsg",{"appId":appId,"userId":userId,"commentId":cmId,"nickname":nickname,"content":content},function(data)
        {
            hideLoading();
            if(data.ret==0)
            {
                //window.wxc.xcConfirm("发送成功","success",{onOk:function ()
                //{
                //    // window.location.reload();
                //}});
                baseUtils.show.blueTip("发送成功!");
                window.location.reload();
            }
            else
            {
                //window.wxc.xcConfirm("系统繁忙，请稍后再试!","error");
                baseUtils.show.redTip("系统繁忙，请稍后再试!");
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

// function initMsgModel() {
//
//     $('.model_type').on("click",function () {
//         $('.model_type').removeClass('border_blue');
//         $(this).addClass('border_blue');
//         $('#admin_nickname').val($(this).attr('data-name'));
//         $('#admin_content').val($(this).attr('data-content'));
//         if($('#cancel_model').hasClass('border_blue')){
//             $('.edit_model').addClass('hide');
//             $('.model_type').removeClass('border_blue');
//         }
//         model_id = $(this).attr('data-id');
//         sms_name = $(this).attr('data-name');
//         sms_content = $(this).attr('data-content');
//     })
//
//     $('#admin_content').keyup(function () {
//         if($('.border_blue').length >0){
//             $('.edit_model').removeClass('hide');
//         }
//     });
//
//     $('#admin_nickname').keyup(function () {
//         if($('.border_blue').length >0){
//             $('.edit_model').removeClass('hide');
//         }
//     });
//
//     $('.edit_model').on("click",function () {
//         //数据库不存在相关模板
//         model_id = $('.border_blue').attr('data-id');
//         var name = $('#admin_nickname').val();
//         var content= $('#admin_content').val();
//         if(model_id==""){
//             //向后台插入模板
//             model_id = 0;
//             if(name!="" && content!=""){
//                 console.log("新增模板："+model_id+">>"+name+">>"+content);
//             }else{
//                 baseUtils.show.redTip('您的发送人昵称/内容为空');
//                 return;
//             }
//         }else{
//             //更新原有模板
//             if(name!="" && content!=""){
//                 console.log("更新模板："+model_id+">>"+name+">>"+content);
//             }else{
//                 baseUtils.show.redTip('您的发送人昵称/内容为空');
//                 return;
//             }
//         }
//         if(name == sms_name && content == sms_content){
//             baseUtils.show.redTip('您没有做任何模板修改');
//         }else{
//             showLoading();
//             var url = "/modelchange";
//             $.post(url, {'model_id': model_id,'send_nick_name':name,'content':content},function (result) {
//                 hideLoading();
//                 console.log(result);
//                 var ReCode = result.code;
//                 var info = result.msg;
//                 baseUtils.show.blueTip(info);
//                 if(ReCode==0){ //新建模板成功 并设置当前最新插入的渠道id
//                     $('.border_blue').attr('data-id',result.id);
//                     $('.border_blue').attr('data-name',name);
//                     $('.border_blue').attr('data-content',content);
//                     needRefresh = true;
//                 }else if(ReCode==1){ //错误
//                     console.log(result.errorMsg);
//                 }else if(ReCode==2){ //更新模板成功
//                     $('.border_blue').attr('data-name',name);
//                     $('.border_blue').attr('data-content',content);
//                     needRefresh = true;
//                 }
//             });
//         }
//     });
// }

//管理员回复
function adminMsg(comment_id,user_id,is_admin) {
    var comment_id = comment_id;
    var user_id = user_id;
    var is_admin = is_admin;
    $('#admin_nickname').val('管理员');
    $("#AdminModal").modal('show');
    //确认按钮点击
    $("#adminsend").children("button").eq(0).unbind("click").bind("click" , function ()
    {
        if(is_admin == 1){
            //如果该评论是管理员回复，则提示错误。
            window.wxc.xcConfirm('管理员已经回复过了哦~','error');
            return false;
        }else{
            var nickname=$("#admin_nickname").val();
            var content=$("#admin_content").val();
            if(nickname.length == 0)
            {
                window.wxc.xcConfirm('亲，还未输入管理员昵称哦~','error');
                return false;
            }
            if(content.length == 0)
            {
                window.wxc.xcConfirm('亲，还未输入消息内容哦~','error');
                return false;
            }
            $("#AdminModal").modal('hide');
            showLoading();
            $.post("/submit_admin_comment",{"comment_id":comment_id,"comment_admin_name":nickname,"user_id":user_id,"replay_content":content},function(data)
            {

                hideLoading();
                console.log(data);
                if(data.code==0)
                {
                    // window.wxc.xcConfirm("回复成功","success",{onOk:function ()
                    // {
                    //     window.location.reload();
                    // }});
                    baseUtils.show.blueTip("回复成功");
                    window.location.reload();
                }
                else
                {
                    //window.wxc.xcConfirm("系统繁忙，请稍后再试!","error");
                    baseUtils.show.redTip("系统繁忙，请稍后再试!");
                }
            });
        }
    });

}

//切换排序
function orderByParameter(newParameter) {
    var oldParameter = GetQueryString('order_parameter');
    //获取当前页url
    var objUrl = window.location.href;
    if(oldParameter){   //  oldParameter不为undefined、null、NaN，数字不为0，字符串不为""，
        objUrl = objUrl.replace('order_parameter='+oldParameter,'order_parameter='+newParameter);
    }else{
        if(objUrl.indexOf('order_parameter')>0){
            objUrl = objUrl.replace('order_parameter='+oldParameter,'order_parameter='+newParameter);
        }else{
            var join = objUrl.indexOf('?')>0? '&' : '?';
            objUrl = objUrl + join +'order_parameter=' + newParameter;
        }
    }
    //将页码重置到第一页
    if(objUrl.indexOf('page')>0) {
        objUrl = objUrl.replace('page='+ GetQueryString('page'), 'page=1');
    }
    //转向目标地址
    window.location.href = objUrl;
}
