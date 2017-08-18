


// $(".charge_btn").click(function(){
//       $(".scan_screen").fadeIn(300);
// });
// $(".scan_screen").click(function() {
//         $(".scan_screen").fadeOut(300);
//         $(".scan_status_fail").hide();
// })
    //打开协议
$(".agree").children("span").click(function() {
    // window.location.href = "/charge_protocol_page";
    window.open("/charge_protocol_page");
});

//关闭协议
$("#iAgree").click(function() {
    $(".seperate").css({ 'display': 'none' });
    $(".agreeModal").css({ 'display': 'none' });
    $("input[type='checkbox']").prop("checked", true);
});
// 关闭弹出框
$(".pop-up_close").click(function() {
    $(".scan_screen").fadeOut(300);
    $(".scan_screen_content").fadeOut(300);
    $(".scan_status_success").fadeOut(300);
    $(".scan_status_fail").fadeOut(300);
    queryresult();
})

//支付按钮点击态
$("#pay_by_wechat").mousedown(function(){
    $(this).css("background","#148b13");
});
$("#pay_by_wechat").mouseup(function(){
    $(this).css("background","rgb(26, 174, 24)");
})
$(".scan_status_return").click(function () {
    $(".scan_screen").fadeOut(300);
    $(".scan_status_fail").hide();
    current_time = 0;
})


$("#pay_by_wechat").click(function () {
    var price = 100 * 100;          //  单位:分
    var type = TYPE_GROW_UP_VERSION;
    pre_pay_wechat(price, type);

});

