var needRefresh = false;

var user_id;
var blog_id;

var search_content;
var blog_attr;
var blog_state;

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

});

function refresh() {
    $("#replayModal").on('hide.bs.modal', function () {
        //判断是否需要刷新界面
        if (needRefresh) {
            location.reload(); //重新加载页面
        }
    });
}

function  searchBComment(){
    showLoading();
    var commentAttr = $("#comment_attr").val(); //获取到选中的值
    //获取搜索内容
    var search_content = $("#comment_search_content").val(); //获取到选中的值
    //评论状态
    var comment_state = $("#comment_state").val(); //获取所选状态
    if (search_content.length == 0) {
        var url = "/blogComment_admin?comment_attr=" + encodeURI(commentAttr)+'&comment_state='+comment_state;
        console.log(url);
    } else {
        var url = "/blogComment_admin?comment_attr=" + encodeURI(commentAttr) + "&search_content=" + encodeURI(search_content)+'&comment_state='+comment_state;
        console.log(url);
    }
    window.location = url;
}

//改变 置顶 状态
function changeTopState(id ,currentTopState,record_id) {
    showLoading();
    var tid = '#state_' + id;
    var btnTopid = '#btnTop_'+id;
    var recordId = record_id;
    var url = "";
    if(currentTopState==0){
        url = "/update_bComment_state?id=" + id + "&state=1" +"&type=top"+"&recordId="+recordId;
    }else{
        url = "/update_bComment_state?id=" + id + "&state=0" +"&type=top"+"&recordId="+recordId;
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
function changeState(id ,currentState , record_id) {

    showLoading();
    var tid = '#state_' + id;
    var btnStateid = '#btn_' + id;
    var recordId = record_id;
    //var btnTopid = '#btnTop_'+id;

    var url = "";
    if (currentState == 0) {
        // url = "/update_comment_state?id=" + id + "&state=1&user_id=" + user_id + "&audio_id="+audio_id+"&type=" + type;
        url = "/update_bComment_state?id=" + id + "&state=1"+"&type=show"+"&recordId="+recordId;
    } else {
        // url = "/update_comment_state?id=" + id + "&state=0&user_id=" + user_id + "&audio_id="+audio_id+"&type=" + type;
        url = "/update_bComment_state?id=" + id + "&state=0"+"&type=show"+"&recordId="+recordId;
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

//回显输入框和选择框的值
function reBack() {
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