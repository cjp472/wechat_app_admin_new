
$(function () {
    var app_id = $('#app_id').val();
    /*var qrcode = new QRCode(document.getElementById("miniappCode"), {
     text: miniappUrl + app_id,
     width: 150,
     height: 150,
     colorDark : "#000000",
     colorLight : "#ffffff",
     correctLevel : QRCode.CorrectLevel.M
     });*/


    // $('input:radio[name="pay_switch"]').change( function(){
    // 	if ($("#open_pay_label").is(':checked')) {
    // 		alert("开了");
    // 	}else{
    // 		alert("关了");
    // 	}
    // });

    $("#open_pay_label").click(function () {
        setPayShow(1);
    });

    $("#close_pay_label").click(function () {
        setPayShow(0);
    });

    //设置付费产品包的显示和隐藏
    function setPayShow(pay_switch) {

        $.post('/mini/changePayShow', {pay_switch: pay_switch}, function (json) {
            json = JSON.parse(json);

            if (json.code == 0) {
                if(pay_switch==1){
                    baseUtils.show.blueTip("已开启付费内容的显示");
                }else{
                    baseUtils.show.blueTip("已关闭付费内容的显示");
                }
            } else {
                baseUtils.show.redTip("操作失败");
            }
        });
    }

});