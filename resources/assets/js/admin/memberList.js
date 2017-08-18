/**
 * Created by Administrator on 2017/3/9.
 */

$(document).ready(function () {
    changeTab();


});


function changeTab() {
    $("#tab_audio").click(function () { // 音频列表
        window.location.href = "/audio_list";
    });
    $("#tab_video").click(function () { // 视频列表
        window.location.href = "/video_list";
    });
    $("#tab_article").click(function () {// 图文列表
        window.location.href = "/article_list";
    });
    $("#tab_package").click(function () {// 专栏列表
        window.location.href = "/package_list";
    });
    $("#tab_alive").click(function () {// 直播列表
        window.location.href = "/alive";
    });
    $("#tab_member").click(function () {// 会员页
        window.location.href = "/member_list";
    });
}