/**
 * Created by Stuphin on 2016/9/30.
 */

$(document).ready(function() {
    $.cookie('content_create','');
    setTopUrlCookie('message_listop', '消息列表');

    //回车搜索
    keyEnter($("#searchButton"));

    $("select[name=typer]").on("change", function () {
        $("#searchButton").click();
    });

});

//编辑功能
function messageEdit(id) {
    window.location.href="/messageedit?id="+id;
}

//撤回功能
function messageDelete(id) {
    // window.wxc.xcConfirm("您确定要撤回这条消息吗?","confirm",{onOk:function() {
    //     $.get('/messagedelete',{"id":id},function(data) {
    //         if(data.ret==0) {
    //             window.wxc.xcConfirm("消息撤回成功","success",{onOk:function() {
    //                 setTimeout(window.location.href='/message',2000);
    //             }});
    //         } else {
    //             window.wxc.xcConfirm("消息撤回失败","error");
    //         }
    //     });
    // }});

    $.alert("您确定要撤回这条消息吗?", "info", {
        btn: 3,
        onOk: function () {
            $.ajax("/messagedelete", {
                type: "GET",
                dataType: "json",
                data: {
                    id: id,
                },
                success: function (result) {
                    if (result.ret == 0) {
                        $.alert("消息撤回成功", "success", {
                            btn: 2,
                            onOk: function () {
                                window.location.reload();
                            }
                        });
                    } else {
                        $.alert("消息撤回失败", "error", {btn: 2});
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                }
             });

        }
    });


}


