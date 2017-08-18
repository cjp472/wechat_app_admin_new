var needRefresh = false;

var user_id;
var blog_id;

var search_content;
var blog_attr;
var blog_state;

$(document).ready(function () {
    refresh();
    reBack();

    keyEnter($('#btn_search'));

    $("tbody tr").mouseover(function()
    {
        $(this).css({'background-color':'#f5f5f5'});
    }).mouseout(function()
    {
        $(this).css({'background-color':'#fff'});
    });

});

// function keyEnter(target){
//     $("input").keypress(function (e) {//当按下按键时
//         if (e.which == 13) {//回车键的键位序号为13
//             target.trigger("click");//触发登录按钮的点击事件
//         }
//     });
// }

function refresh() {
    $("#replayModal").on('hide.bs.modal', function () {
        //判断是否需要刷新界面
        if (needRefresh) {
            location.reload(); //重新加载页面
        }
    });
}

//改变 置顶 状态
function changeTopState(id ,currentTopState) {
    showLoading();
    var tid = '#state_' + id;
    var btnTopid = '#btnTop_'+id;
    var url = "";
    if(currentTopState==0){
        url = "/update_blog_state?id=" + id + "&state=1" +"&type=top";
    }else{
        url = "/update_blog_state?id=" + id + "&state=0" +"&type=top";
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
                $(btnTopid).html('取消');
                $(btnTopid).val(1);
            }else{
                // $(tid).children().eq(0).attr('class','btn btn-link btn-sm');
                // $(tid).children().eq(0).html('');
                $(btnTopid).html('精选');
                $(btnTopid).val(0);
            }
        }
        baseUtils.show.blueTip(result.msg);
        window.location.reload();
    })
}

// 改变显示/隐藏状态
function changeState(id ,currentState) {

    showLoading();
    var tid = '#state_' + id;
    var btnStateid = '#btn_' + id;
    //var btnTopid = '#btnTop_'+id;

    var url = "";
    if (currentState == 0) {
        // url = "/update_comment_state?id=" + id + "&state=1&user_id=" + user_id + "&audio_id="+audio_id+"&type=" + type;
        url = "/update_blog_state?id=" + id + "&state=1"+"&type=show";
    } else {
        // url = "/update_comment_state?id=" + id + "&state=0&user_id=" + user_id + "&audio_id="+audio_id+"&type=" + type;
        url = "/update_blog_state?id=" + id + "&state=0"+"&type=show";
    }
    //更新界面
    $.get(url, function (result) {
        hideLoading();
        if (result.code == 0) {
            //当前评论状态：显示
            if (currentState == 0) {
                // $(tid).children().eq(0).attr('class', 'btn btn-danger btn-sm');
                // $(tid).children().eq(0).html('隐藏');
                // $(tid).children().eq(0).attr('color', 'white');
                $(btnStateid).html('显示');
                $(btnStateid).val(1);
            } else {
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

// function blogDetail(content , imgArray) {
//     var content=content;
//     console.log(content)
//     // var appId=info.split("|")[0];
//     // var userId=info.split("|")[1];
//     $("#SmsModal").modal('show');
//     //确认按钮点击
//     // $(".modal-footer").children("button").eq(0).click(function()
//     // {
//     //     var nickname=$("#sms_nickname").val();
//     //     var content=$("#sms_content").val();
//     //     $("#SmsModal").modal('hide');
//     //     showLoading();
//     //     $.post("/customermsg",{"appId":appId,"userId":userId,"nickname":nickname,"content":content},function(data)
//     //     {
//     //         hideLoading();
//     //         if(data.ret==0)
//     //         {
//     //             window.wxc.xcConfirm("发送成功","success");
//     //         }
//     //         else
//     //         {
//     //             window.wxc.xcConfirm("系统繁忙，请稍后再试!","error");
//     //         }
//     //     });
//     // });
// }

//模糊查询评论
function  searchBlog(){
    showLoading();
    var blogAttr = $("#blog_attr").val(); //获取到选中的值
    //获取搜索内容
    var search_content = $("#blog_search_content").val(); //获取到选中的值
    //评论状态
    var blog_state = $("#blog_state").val(); //获取所选状态
    if (search_content.length == 0) {
        var url = "/blog_admin?blog_attr=" + encodeURI(blogAttr)+'&blog_state='+blog_state;
    } else {
        var url = "/blog_admin?blog_attr=" + encodeURI(blogAttr) + "&search_content=" + encodeURI(search_content)+'&blog_state='+blog_state;
    }

    window.location = url;
}

//回显输入框和选择框的值
function reBack() {
    var blog_search_content = document.getElementById("blog_search_content");

    if (search_content != "") {
        blog_search_content.value = search_content;
        var blog_attr_element = document.getElementById("blog_attr");
        if (blog_attr_element.length > 0) {
            for (var i = 0; i < blog_attr_element.options.length; i++) {
                if (blog_attr_element.options[i].value == blog_attr) {
                    blog_attr_element.options[i].selected = true;
                    break;
                }
            }
        }
    }

    var blog_state_element = document.getElementById("blog_state");
    if (blog_state_element.length > 0) {
        for (var i = 0; i < blog_state_element.options.length; i++) {
            if (blog_state_element.options[i].value == blog_state) {
                blog_state_element.options[i].selected = true;
                break;
            }
        }
    }
}