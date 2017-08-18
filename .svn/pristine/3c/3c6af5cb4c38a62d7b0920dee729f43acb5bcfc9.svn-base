/**
 * Created by Administrator on 2017/3/7.
 */


$(document).ready(function () {

    //  回主页
    $(".home_page").click(function () {
        window.location.href='/accountview';
    });

    $(".avatar_nickname").click(function () {
        var app_id = $(this).data("app_id");
        var user_id = $(this).data("user_id");

        jumpDetail(app_id + "|" + user_id);
    });

});

//调到详情页
function jumpDetail(info) {
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    window.location.href='/customerdetail?appId='+appId+'&userId='+userId;
}