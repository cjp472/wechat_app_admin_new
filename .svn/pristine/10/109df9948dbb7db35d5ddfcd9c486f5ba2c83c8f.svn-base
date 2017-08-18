$(function () {
    noticeList.init();
});

var noticeList=function () {
    var noticeList={};

    noticeList.init=function () {
         $('.noticeListPart').click(function () {
             $(".noticeListPart").removeClass('active');
             $(this).addClass('active');
             $(".noticePartContent").removeClass('active');
             $(this).children(".noticePartContent").addClass('active');
             $(".noticePartContent:not(.active)").hide(300);
             $(".noticeListPart").find('.listSdIcon').css('transform','rotate(0deg)')
             if($(this).children(".noticePartContent").is(":hidden")){
                 $(this).children(".noticePartContent").show(300);
                 $(this).find('.listSdIcon').css('transform','rotate(-90deg)')
             }else{
                 $(this).children(".noticePartContent").hide(300);
                 $(this).find('.listSdIcon').css('transform','rotate(0deg)')
             }


             var id=$(this).data('id');
             var viewState=$(this).data('viewstate');
             // console.log(viewState);
             if(viewState==0) {
               changeViewState(id);
                 var unReadNum = $('.noticeUnreadNum').text();
                 unReadNum--;

                 if (unReadNum > 0) {
                     $('.noticeUnreadNum').text(unReadNum);
                 } else {
                     $('.noticeUnreadNum').hide();
                 }
               $(this).find(".unreadPoint").hide(300);
               $(this).data('viewstate','1') ;
             }
         })
    };

    return noticeList;
}();