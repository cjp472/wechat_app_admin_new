$(function() {
    activeBusiness.init();
});

//工具类
var activeUtils = (function() {

})();
//业务类
var activeBusiness = (function() {
    var activeBusiness = {};

    activeBusiness.init = function() {
        //点击态事件绑定
        (function() {
            //普通按钮
            $(".xeBtnDefault").mousedown(function() {
                $(this).addClass("xeBtnDefaultDown")
            });
            $(".xeBtnDefault").mouseup(function() {
                $(this).removeClass("xeBtnDefaultDown")
            });
            //蓝色按钮1
            $(".btnBlue").mousedown(function() {
                $(this).addClass("btnBlueDown")
            });
            $(".btnBlue").mouseup(function() {
                $(this).removeClass("btnBlueDown")
            })
            //红色按钮
            $(".btnRed").mousedown(function() {
                $(this).addClass("btnRedDown")
            });
            $(".btnRed").mouseup(function() {
                $(this).removeClass("btnRedDown")
            })
        })()
    };
    return activeBusiness;
})();
