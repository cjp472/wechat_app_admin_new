$(function() {
	var _app_id = $("#app_id").val(),
		_auth = $("#auth").val();
	$("#bindNow").click(function(){
	    var newWin = window.open('','_blank');

	    $.post("/mini/authority",{"app_id":_app_id},function(json) {
	        if(json.code==0) {
	            newWin.location.href = miniauthUrl + _app_id;
	            $("#bindModal").modal("show");
	        } else {
				newWin.close();
	            baseUtils.show.redTip("系统繁忙");
	        }
	    }, 'json');



	});

	//授权成功按钮点击验证扫码授权结果
	$("#bindSuccess").click(function() {
	    $.post("/mini/checkAuth",{"app_id":_app_id},function(json){
	        if(json.code==0){
	            $("#bindModal").modal("hide");
	            window.location.href = '/mini/info?change=1';
	        } else {
	            window.location.reload();
	        }
	    },'json');
	});

	//授权失败，继续弹出
	$("#bindFail").click(function() {
	    var newWin = window.open('','_blank');
	    newWin.location.href = miniauthUrl + _app_id;
	    $("#bindModal").modal("show");
	});
});
