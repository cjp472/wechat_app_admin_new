$(function(){(function(){var e=new Clipboard("#ShopUrl");e.on("success",function(e){baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");e.clearSelection()})})();(function(){var e=$(".customBalance").text();e=e.replace(/,/g,"");var t=parseFloat(e);if(t<0){$(".window_prompt").show();return false}})();var e=new QRCode(document.getElementById("shopCode"),{text:$("#ShopUrl").data("clipboard-text"),width:120,height:120,colorDark:"#000000",colorLight:"#ffffff",correctLevel:QRCode.CorrectLevel.H});$("#notShowCase").click(function(){$.alert("点击确定后，您将不会再看到精彩案例呦！","info",{onOk:function(){$.ajax("/closeMessageReminder",{type:"GET",dataType:"json",data:{status:1},success:function(e){if(e.code==0){$("#goodCase").fadeOut()}else{baseUtils.show.redTip(e.msg)}},error:function(e){console.error(e);baseUtils.show.redTip("网络错误，请稍后再试！")}})}})});$("#shopSet").on("click",function(){window.location.href="/interfacesetting"});var t=null;$("#showShop").mouseenter(function(){$("#qrcodeArea").fadeIn()}).mouseleave(function(){t=setTimeout(function(){$("#qrcodeArea").fadeOut()},300)});$("#qrcodeArea").mouseenter(function(){clearTimeout(t)}).mouseleave(function(){t=setTimeout(function(){$("#qrcodeArea").fadeOut()},300)});$(".subClose").on("click",function(){console.log(newGuide.lockKey);$("#qrcodeArea").fadeOut()});$(".welcomeBoxClose").click(function(){console.log("close click");$(".darkScreen2").fadeOut(200);$.ajax("",function(){})});$(".exerciseBook").click(function(){var e=$("#versionType").val();if(e==1||e==2){baseUtils.show.redTip("当前版本不支持作业本，如需开启请升级至专业版")}else{window.location="/exercise/exercise_book_list"}});$(".Q_A").click(function(){var e=$("#versionType").val();if(e==1){baseUtils.show.redTip("当前版本不支持问答，如需开启请升级至成长版或专业版")}else{window.location="/QA/questionAndAnswerDetail"}});$(".member_vip").click(function(){var e=$("#versionType").val();if(e==1){baseUtils.show.redTip("当前版本不支持会员，如需开启请升级至成长版或专业版")}else{window.location="/member_list_page"}});$("body").on("click",".cm_upgrade",function(){_hmt.push(["_trackEvent","立即升级","clickEvent","aboutTheTest"])});$("body").on("click",".cm_shop",function(){_hmt.push(["_trackEvent","访问店铺","clickEvent","aboutTheTest"])});$("body").on("click",".cm_dataAnalysis",function(){_hmt.push(["_trackEvent","概况_数据分析","clickEvent","aboutTheTest"])});$("body").on("click",".cm_withDrawCash",function(){_hmt.push(["_trackEvent","提现","clickEvent","aboutTheTest"])});$("body").on("click",".cm_recharge",function(){_hmt.push(["_trackEvent","充值","clickEvent","aboutTheTest"])});$("body").on("click",".cm_record",function(){_hmt.push(["_trackEvent","查看结算记录","clickEvent","aboutTheTest"])});$("body").on("click",".cm_addArticle",function(){_hmt.push(["_trackEvent","新增图文","clickEvent","aboutTheTest"])});$("body").on("click",".cm_addMusic",function(){_hmt.push(["_trackEvent","新增音频","clickEvent","aboutTheTest"])});$("body").on("click",".cm_addVideo",function(){_hmt.push(["_trackEvent","新增视频","clickEvent","aboutTheTest"])});$("body").on("click",".cm_addAlive",function(){_hmt.push(["_trackEvent","新增直播","clickEvent","aboutTheTest"])});$("body").on("click",".cm_addCommunity",function(){_hmt.push(["_trackEvent","新增社群","clickEvent","aboutTheTest"])});$("body").on("click",".cm_income",function(){_hmt.push(["_trackEvent","收入/提现","clickEvent","aboutTheTest"])});$("body").on("click",".cm_bannerPic",function(){_hmt.push(["_trackEvent","设置轮播图","clickEvent","aboutTheTest"])});$("body").on("click",".cm_message",function(){_hmt.push(["_trackEvent","群发消息","clickEvent","aboutTheTest"])});$("body").on("click",".cm_activity",function(){_hmt.push(["_trackEvent","活动","clickEvent","aboutTheTest"])});$("body").on("click",".cm_saler",function(){_hmt.push(["_trackEvent","推广员","clickEvent","aboutTheTest"])});$("body").on("click",".cm_inviteCard",function(){_hmt.push(["_trackEvent","邀请卡","clickEvent","aboutTheTest"])});$("body").on("click",".cm_smallCommunity",function(){_hmt.push(["_trackEvent","小社群","clickEvent","aboutTheTest"])});$("body").on("click",".cm_indexHelpCenter",function(){_hmt.push(["_trackEvent","概况——帮助中心","clickEvent","aboutTheTest"])});$("body").on("click",".cm_leftHelpCenter",function(){_hmt.push(["_trackEvent","左侧菜单-帮助中心","clickEvent","aboutTheTest"])});$("body").on("click",".cm_noShowMore",function(){_hmt.push(["_trackEvent","不再显示","clickEvent","aboutTheTest"])});$(document).ready(function(){$(".darkScreen2.indWel").addClass("ready");$(".darkScreen2.indWel").addClass("active")});$(".indexWelcomeCloseBtn,.indexWelcomeCont a").on("click",function(){$(".darkScreen2.indWel").removeClass("active");$.ajax("/closeMessageReminder",{type:"get",dataType:"json",data:{status:1,place:12},success:function(e){if(e.code==0){}else{}},error:function(e){console.error(e)}})})});$(function(){newGuide.init()});var newGuide={init:function(){var e=$(".bannerBox");var t=0;var c=$(".title");var n=$(".txtMsg");var o=$(".static");var i=$(".guideBtn");var a=$(".guideMsg");var s;var l;$(".closeGuide").on("click",function(){$(".guideBox").fadeOut()});$(".markBox").on("click",".static",function(){var e=$(this).index();console.log(e);d(e);clearInterval(l);u()});i.on("click",function(){t=$(".markBox .active").index();t++;clearInterval(l);u();if(t>3){$(".guideBox").fadeOut();$("#qrcodeArea").fadeIn()}else{d(t)}});u();function u(){l=setInterval(function(){if(t>=3){clearInterval(l)}else{t++;d(t)}},8e3)}function r(){setTimeout(function(){t=$(".markBox .active").index();s=t*622;e.stop().animate({left:-s})},300)}function d(e){if(e==0){a.stop().animate({opacity:0},"slow",function(){c.html("扫码访问店铺");n.html("您可以扫描此二维码进入您的知识店铺");a.stop().animate({opacity:1},"slow")});i.html("下一步");r()}else if(e==1){a.stop().animate({opacity:0},"slow",function(){c.html("嵌入公众号");n.html("将店铺链接添加至已认证微信公众号自定义菜单栏，完成店铺与公众号的连接");a.stop().animate({opacity:1},"slow")});i.html("下一步");r()}else if(e==2){a.stop().animate({opacity:0},"slow",function(){c.html("管理知识商品");n.html("您可以在这里开始创建并管理您的知识商品");a.stop().animate({opacity:1},"slow")});i.html("下一步");r()}else if(e==3){a.stop().animate({opacity:0},"slow",function(){c.html("查看帮助中心");n.html("您可以在这里查看相关功能的教程说明和帮助文档");a.stop().animate({opacity:1},"slow")});i.html("立即体验");r()}o.removeClass("active");o.eq(e).addClass("active")}if(GetQueryString("first")==1){$(".guideBox").fadeIn()}}};
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJjbGlwYm9hcmQiLCJDbGlwYm9hcmQiLCJvbiIsImUiLCJiYXNlVXRpbHMiLCJzaG93IiwiYmx1ZVRpcCIsImNsZWFyU2VsZWN0aW9uIiwidmFsdWUiLCJ0ZXh0IiwicmVwbGFjZSIsImFwcF9iYWxhbmNlIiwicGFyc2VGbG9hdCIsInFyY29kZSIsIlFSQ29kZSIsImRvY3VtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJkYXRhIiwid2lkdGgiLCJoZWlnaHQiLCJjb2xvckRhcmsiLCJjb2xvckxpZ2h0IiwiY29ycmVjdExldmVsIiwiQ29ycmVjdExldmVsIiwiSCIsImNsaWNrIiwiYWxlcnQiLCJvbk9rIiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsInN0YXR1cyIsInN1Y2Nlc3MiLCJqc29uIiwiY29kZSIsImZhZGVPdXQiLCJyZWRUaXAiLCJtc2ciLCJlcnJvciIsImVyciIsImNvbnNvbGUiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJzaG9wVGltZXIiLCJtb3VzZWVudGVyIiwiZmFkZUluIiwibW91c2VsZWF2ZSIsInNldFRpbWVvdXQiLCJjbGVhclRpbWVvdXQiLCJsb2ciLCJuZXdHdWlkZSIsImxvY2tLZXkiLCJ2ZXJzaW9uVHlwZSIsInZhbCIsIl9obXQiLCJwdXNoIiwicmVhZHkiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwicGxhY2UiLCJpbml0IiwibGVmdCIsIml0ZW1JbmRleCIsInRpdGxlIiwiY29udGVudCIsImxpdHRsZURvdCIsIm5leEV2ZW50IiwiZ3VpZE1zZyIsIm1vdmVWYWx1ZSIsIm1vdmVCYW5uZXIiLCJ0aGlzIiwiaW5kZXgiLCJjaGFuZ2UiLCJjbGVhckludGVydmFsIiwic2V0VGltZXIiLCJzZXRJbnRlcnZhbCIsIm1vdmluZyIsInN0b3AiLCJhbmltYXRlIiwib3BhY2l0eSIsImh0bWwiLCJlcSIsIkdldFF1ZXJ5U3RyaW5nIl0sIm1hcHBpbmdzIjoiQUFBQUEsRUFBRSxZ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QUV2Q2IsR0FBU2MsS0FBSyxNQUNkSixTQUNFLElBQUdkLEdBQWEsRUFBRSxDQUNwQkssRUFBUVUsT0FBT0MsU0FBU0MsUUFBUSxHQUFHLE9BQU8sV0FDdENoQixFQUFNaUIsS0FBSyxTQUNYaEIsR0FBUWdCLEtBQUsseUJBQ2JiLEdBQVFVLE9BQU9DLFNBQVNDLFFBQVEsR0FBRyxTQUV2Q2IsR0FBU2MsS0FBSyxPQUNkSixLQUVKWCxFQUFVUCxZQUFZLFNBQ3RCTyxHQUFVZ0IsR0FBR25CLEdBQVdMLFNBQVMsVUFJckMsR0FBR3lCLGVBQWUsVUFBWSxFQUFFLENBQzVCcEYsRUFBRSxhQUFhK0MiLCJmaWxlIjoiaW5kZXguanMiLCJzb3VyY2VzQ29udGVudCI6WyIkKGZ1bmN0aW9uKCkge1xyXG4gICAgLyokKCcjbXlUYWJzIGEnKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgIGUucHJldmVudERlZmF1bHQoKVxyXG4gICAgICQodGhpcykudGFiKCdzaG93JylcclxuICAgICB9KSovXHJcblxyXG4gICAgKGZ1bmN0aW9uICgpIHsgIC8v5aSN5Yi26ZO+5o6lXHJcbiAgICAgICAgdmFyIGNsaXBib2FyZCA9IG5ldyBDbGlwYm9hcmQoJyNTaG9wVXJsJyk7XHJcbiAgICAgICAgY2xpcGJvYXJkLm9uKCdzdWNjZXNzJywgZnVuY3Rpb24oZSkge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi5aSN5Yi25oiQ5Yqf77yB6K+35Zyo5b6u5L+h5YaF5omT5byA5ZOmIOOAglwiKTtcclxuICAgICAgICAgICAgZS5jbGVhclNlbGVjdGlvbigpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfSkoKTtcclxuXHJcbiAgICAoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciB2YWx1ZSA9ICQoXCIuY3VzdG9tQmFsYW5jZVwiKS50ZXh0KCk7XHJcblxyXG4gICAgICAgIHZhbHVlID0gdmFsdWUucmVwbGFjZSgvLC9nLCAnJyk7XHJcblxyXG4gICAgICAgIHZhciBhcHBfYmFsYW5jZSA9IHBhcnNlRmxvYXQodmFsdWUpO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKGFwcF9iYWxhbmNlKTtcclxuICAgICAgICAvLyBpZiAoYXBwX2JhbGFuY2UgPj0gMCAmJiBhcHBfYmFsYW5jZSA8IDUwKSB7XHJcbiAgICAgICAgLy8gICAgICQoXCIucmVkX3Byb21wdFwiKS5zaG93KCk7XHJcbiAgICAgICAgLy8gICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAvLyB9XHJcbiAgICAgICAgaWYgKGFwcF9iYWxhbmNlIDwgMCkgey8v5qyg6LS55by55Ye65qGGXHJcbiAgICAgICAgICAgICQoXCIud2luZG93X3Byb21wdFwiKS5zaG93KCk7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9KSgpO1xyXG5cclxuICAgIHZhciBxcmNvZGUgPSBuZXcgUVJDb2RlKGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwic2hvcENvZGVcIiksICAvL+eUn+aIkOS6jOe7tOeggVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgdGV4dDogJChcIiNTaG9wVXJsXCIpLmRhdGEoJ2NsaXBib2FyZC10ZXh0JyksXHJcbiAgICAgICAgICAgIHdpZHRoOiAxMjAsXHJcbiAgICAgICAgICAgIGhlaWdodDogMTIwLFxyXG4gICAgICAgICAgICBjb2xvckRhcmsgOiBcIiMwMDAwMDBcIixcclxuICAgICAgICAgICAgY29sb3JMaWdodCA6IFwiI2ZmZmZmZlwiLFxyXG4gICAgICAgICAgICBjb3JyZWN0TGV2ZWwgOiBRUkNvZGUuQ29ycmVjdExldmVsLkhcclxuICAgICAgICB9KTtcclxuXHJcblxyXG4gICAgJCgnI25vdFNob3dDYXNlJykuY2xpY2soZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgJC5hbGVydCgn54K55Ye756Gu5a6a5ZCO77yM5oKo5bCG5LiN5Lya5YaN55yL5Yiw57K+5b2p5qGI5L6L5ZGm77yBJywnaW5mbycse1xyXG4gICAgICAgICAgICBvbk9rOiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgICQuYWpheCgnL2Nsb3NlTWVzc2FnZVJlbWluZGVyJyx7XHJcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcclxuICAgICAgICAgICAgICAgICAgICBkYXRhOntzdGF0dXM6IDF9LFxyXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKGpzb24pIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5jb2RlID09IDApIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNnb29kQ2FzZScpLmZhZGVPdXQoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChqc29uLm1zZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbihlcnIpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoJ+e9kee7nOmUmeivr++8jOivt+eojeWQjuWGjeivle+8gScpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KVxyXG5cclxuXHJcbiAgICB9KTtcclxuXHJcbiAgICAkKFwiI3Nob3BTZXRcIikub24oJ2NsaWNrJyxmdW5jdGlvbigpe1xyXG4gICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWY9Jy9pbnRlcmZhY2VzZXR0aW5nJ1xyXG4gICAgfSk7XHJcblxyXG4gICAgdmFyIHNob3BUaW1lciA9IG51bGw7XHJcbiAgICAkKCcjc2hvd1Nob3AnKS5tb3VzZWVudGVyKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAkKCcjcXJjb2RlQXJlYScpLmZhZGVJbigpO1xyXG4gICAgfSkubW91c2VsZWF2ZShmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgc2hvcFRpbWVyID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICQoJyNxcmNvZGVBcmVhJykuZmFkZU91dCgpO1xyXG4gICAgICAgIH0sIDMwMCk7XHJcbiAgICB9KTtcclxuICAgICQoJyNxcmNvZGVBcmVhJykubW91c2VlbnRlcihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgY2xlYXJUaW1lb3V0KHNob3BUaW1lcik7XHJcbiAgICB9KS5tb3VzZWxlYXZlKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBzaG9wVGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgJCgnI3FyY29kZUFyZWEnKS5mYWRlT3V0KCk7XHJcbiAgICAgICAgfSwgMzAwKTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5zdWJDbG9zZScpLm9uKCdjbGljaycsZnVuY3Rpb24oKXsvL+S4tOaXtuWFs+mXreaMiemSru+8jOS4i+asoei/reS7o+iwg+aVtFxyXG4gICAgICAgIGNvbnNvbGUubG9nKG5ld0d1aWRlLmxvY2tLZXkpO1xyXG4gICAgICAgICQoJyNxcmNvZGVBcmVhJykuZmFkZU91dCgpO1xyXG4gICAgfSk7XHJcbiAgICAvLyDkvJjmg6DliLjmpoLlhrXlvLnnqpdcclxuICAgIC8v5YWz6Zet5o6o5bm/5LuL57uN5qGGXHJcbiAgICAkKFwiLndlbGNvbWVCb3hDbG9zZVwiKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgY29uc29sZS5sb2coJ2Nsb3NlIGNsaWNrJyk7XHJcbiAgICAgICAgJChcIi5kYXJrU2NyZWVuMlwiKS5mYWRlT3V0KDIwMCk7XHJcbiAgICAgICAgJC5hamF4KCcnLGZ1bmN0aW9uKCl7XHJcblxyXG4gICAgICAgIH0pXHJcbiAgICB9KTtcclxuICAgICQoXCIuZXhlcmNpc2VCb29rXCIpLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIHZlcnNpb25UeXBlPSQoXCIjdmVyc2lvblR5cGVcIikudmFsKCk7XHJcbiAgICAgICAgaWYodmVyc2lvblR5cGU9PTF8fHZlcnNpb25UeXBlPT0yKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5b2T5YmN54mI5pys5LiN5pSv5oyB5L2c5Lia5pys77yM5aaC6ZyA5byA5ZCv6K+35Y2H57qn6Iez5LiT5Lia54mIJyk7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbj0nL2V4ZXJjaXNlL2V4ZXJjaXNlX2Jvb2tfbGlzdCc7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICAkKFwiLlFfQVwiKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciB2ZXJzaW9uVHlwZT0kKFwiI3ZlcnNpb25UeXBlXCIpLnZhbCgpO1xyXG4gICAgICAgIGlmKHZlcnNpb25UeXBlPT0xKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5b2T5YmN54mI5pys5LiN5pSv5oyB6Zeu562U77yM5aaC6ZyA5byA5ZCv6K+35Y2H57qn6Iez5oiQ6ZW/54mI5oiW5LiT5Lia54mIJyk7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbj0nL1FBL3F1ZXN0aW9uQW5kQW5zd2VyRGV0YWlsJztcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgICQoXCIubWVtYmVyX3ZpcFwiKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciB2ZXJzaW9uVHlwZT0kKFwiI3ZlcnNpb25UeXBlXCIpLnZhbCgpO1xyXG4gICAgICAgIGlmKHZlcnNpb25UeXBlPT0xKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5b2T5YmN54mI5pys5LiN5pSv5oyB5Lya5ZGY77yM5aaC6ZyA5byA5ZCv6K+35Y2H57qn6Iez5oiQ6ZW/54mI5oiW5LiT5Lia54mIJyk7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbj0nL21lbWJlcl9saXN0X3BhZ2UnO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgLyokKCcjY2FzZUNvbnRlbnQnKS5vbignbW91c2VlbnRlcicsICcuaXRlbScsIGZ1bmN0aW9uKGUpIHtcclxuICAgICAkKGUudGFyZ2V0KS5maW5kKCcuY29kZUNvbnRlbnQnKS5mYWRlSW4oKTtcclxuICAgICB9KS5vbignbW91c2VsZWF2ZScsICcuaXRlbScsIGZ1bmN0aW9uKGUpIHtcclxuICAgICAkKGUudGFyZ2V0KS5maW5kKCcuY29kZUNvbnRlbnQnKS5mYWRlT3V0KCk7XHJcbiAgICAgfSk7OyovXHJcblxyXG5cclxuLy/nq4vljbPljYfnuqdcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV91cGdyYWRlJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn56uL5Y2z5Y2H57qnJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG5cclxuICAgIH0pO1xyXG5cclxuLy/orr/pl67lupfpk7pcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV9zaG9wJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKFwidGVzdCB0ZXN0IHRlc3RcIik7XHJcbiAgICAgICAgLy8gYWxlcnQoXCJ0ZXN0XCIpO1xyXG4gICAgICAgIF9obXQucHVzaChbJ190cmFja0V2ZW50JywgJ+iuv+mXruW6l+mTuicsICdjbGlja0V2ZW50JywgJ2Fib3V0VGhlVGVzdCddKTtcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhfaG10KTtcclxuICAgICAgICAvLyBhbGVydChfaG10KTtcclxuICAgIH0pO1xyXG5cclxuLy/mpoLlhrVf5pWw5o2u5YiG5p6QXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fZGF0YUFuYWx5c2lzJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5qaC5Ya1X+aVsOaNruWIhuaekCcsICdjbGlja0V2ZW50JywgJ2Fib3V0VGhlVGVzdCddKTtcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhfaG10KTtcclxuICAgICAgICAvLyBhbGVydChfaG10KTtcclxuICAgIH0pO1xyXG5cclxuLy/mj5DnjrBcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV93aXRoRHJhd0Nhc2gnLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICfmj5DnjrAnLCAnY2xpY2tFdmVudCcsICdhYm91dFRoZVRlc3QnXSk7XHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coX2htdCk7XHJcbiAgICAgICAgLy8gYWxlcnQoX2htdCk7XHJcbiAgICB9KTtcclxuXHJcbi8v5YWF5YC8XHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fcmVjaGFyZ2UnLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICflhYXlgLwnLCAnY2xpY2tFdmVudCcsICdhYm91dFRoZVRlc3QnXSk7XHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coX2htdCk7XHJcbiAgICAgICAgLy8gYWxlcnQoX2htdCk7XHJcbiAgICB9KTtcclxuXHJcbi8v5p+l55yL57uT566X6K6w5b2VXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fcmVjb3JkJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5p+l55yL57uT566X6K6w5b2VJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4vL+aWsOWinuWbvuaWh1xyXG4gICAgJChcImJvZHlcIikub24oXCJjbGlja1wiLCAnLmNtX2FkZEFydGljbGUnLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICfmlrDlop7lm77mlocnLCAnY2xpY2tFdmVudCcsICdhYm91dFRoZVRlc3QnXSk7XHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coX2htdCk7XHJcbiAgICAgICAgLy8gYWxlcnQoX2htdCk7XHJcbiAgICB9KTtcclxuXHJcbi8v5paw5aKe6Z+z6aKRXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fYWRkTXVzaWMnLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICfmlrDlop7pn7PpopEnLCAnY2xpY2tFdmVudCcsICdhYm91dFRoZVRlc3QnXSk7XHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coX2htdCk7XHJcbiAgICAgICAgLy8gYWxlcnQoX2htdCk7XHJcbiAgICB9KTtcclxuXHJcbi8v5paw5aKe6KeG6aKRXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fYWRkVmlkZW8nLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICfmlrDlop7op4bpopEnLCAnY2xpY2tFdmVudCcsICdhYm91dFRoZVRlc3QnXSk7XHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coX2htdCk7XHJcbiAgICAgICAgLy8gYWxlcnQoX2htdCk7XHJcbiAgICB9KTtcclxuXHJcbi8v5paw5aKe55u05pKtXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fYWRkQWxpdmUnLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICfmlrDlop7nm7Tmkq0nLCAnY2xpY2tFdmVudCcsICdhYm91dFRoZVRlc3QnXSk7XHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coX2htdCk7XHJcbiAgICAgICAgLy8gYWxlcnQoX2htdCk7XHJcbiAgICB9KTtcclxuXHJcbi8v5paw5aKe56S+576kXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fYWRkQ29tbXVuaXR5JywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5paw5aKe56S+576kJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4vL+aUtuWFpS/mj5DnjrBcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV9pbmNvbWUnLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICfmlLblhaUv5o+Q546wJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4vL+iuvue9rui9ruaSreWbvlxyXG4gICAgJChcImJvZHlcIikub24oXCJjbGlja1wiLCAnLmNtX2Jhbm5lclBpYycsIGZ1bmN0aW9uICgpIHsvL+eZvuW6puS6i+S7tui/vei4qua1i+ivlVxyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKFwidGVzdCB0ZXN0IHRlc3RcIik7XHJcbiAgICAgICAgLy8gYWxlcnQoXCJ0ZXN0XCIpO1xyXG4gICAgICAgIF9obXQucHVzaChbJ190cmFja0V2ZW50JywgJ+iuvue9rui9ruaSreWbvicsICdjbGlja0V2ZW50JywgJ2Fib3V0VGhlVGVzdCddKTtcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhfaG10KTtcclxuICAgICAgICAvLyBhbGVydChfaG10KTtcclxuICAgIH0pO1xyXG5cclxuLy/nvqTlj5Hmtojmga9cclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV9tZXNzYWdlJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn576k5Y+R5raI5oGvJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4vL+a0u+WKqFxyXG4gICAgJChcImJvZHlcIikub24oXCJjbGlja1wiLCAnLmNtX2FjdGl2aXR5JywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5rS75YqoJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4vL+aOqOW5v+WRmFxyXG4gICAgJChcImJvZHlcIikub24oXCJjbGlja1wiLCAnLmNtX3NhbGVyJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5o6o5bm/5ZGYJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/pgoDor7fljaFcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV9pbnZpdGVDYXJkJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn6YKA6K+35Y2hJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/lsI/npL7nvqRcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV9zbWFsbENvbW11bml0eScsIGZ1bmN0aW9uICgpIHsvL+eZvuW6puS6i+S7tui/vei4qua1i+ivlVxyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKFwidGVzdCB0ZXN0IHRlc3RcIik7XHJcbiAgICAgICAgLy8gYWxlcnQoXCJ0ZXN0XCIpO1xyXG4gICAgICAgIF9obXQucHVzaChbJ190cmFja0V2ZW50JywgJ+Wwj+ekvue+pCcsICdjbGlja0V2ZW50JywgJ2Fib3V0VGhlVGVzdCddKTtcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhfaG10KTtcclxuICAgICAgICAvLyBhbGVydChfaG10KTtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v5qaC5Ya14oCU4oCU5biu5Yqp5Lit5b+DXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21faW5kZXhIZWxwQ2VudGVyJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5qaC5Ya14oCU4oCU5biu5Yqp5Lit5b+DJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/lt6bkvqfoj5zljZUt5biu5Yqp5Lit5b+DXHJcbiAgICAkKFwiYm9keVwiKS5vbihcImNsaWNrXCIsICcuY21fbGVmdEhlbHBDZW50ZXInLCBmdW5jdGlvbiAoKSB7Ly/nmb7luqbkuovku7bov73ouKrmtYvor5VcclxuICAgICAgICAvLyBjb25zb2xlLmxvZyhcInRlc3QgdGVzdCB0ZXN0XCIpO1xyXG4gICAgICAgIC8vIGFsZXJ0KFwidGVzdFwiKTtcclxuICAgICAgICBfaG10LnB1c2goWydfdHJhY2tFdmVudCcsICflt6bkvqfoj5zljZUt5biu5Yqp5Lit5b+DJywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/kuI3lho3mmL7npLpcclxuICAgICQoXCJib2R5XCIpLm9uKFwiY2xpY2tcIiwgJy5jbV9ub1Nob3dNb3JlJywgZnVuY3Rpb24gKCkgey8v55m+5bqm5LqL5Lu26L+96Liq5rWL6K+VXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coXCJ0ZXN0IHRlc3QgdGVzdFwiKTtcclxuICAgICAgICAvLyBhbGVydChcInRlc3RcIik7XHJcbiAgICAgICAgX2htdC5wdXNoKFsnX3RyYWNrRXZlbnQnLCAn5LiN5YaN5pi+56S6JywgJ2NsaWNrRXZlbnQnLCAnYWJvdXRUaGVUZXN0J10pO1xyXG4gICAgICAgIC8vIGNvbnNvbGUubG9nKF9obXQpO1xyXG4gICAgICAgIC8vIGFsZXJ0KF9obXQpO1xyXG4gICAgfSk7XHJcbiAgICAkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpe1xyXG4gICAgICAgICQoJy5kYXJrU2NyZWVuMi5pbmRXZWwnKS5hZGRDbGFzcygncmVhZHknKTtcclxuICAgICAgICAkKCcuZGFya1NjcmVlbjIuaW5kV2VsJykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgfSk7XHJcbiAgICAkKCcuaW5kZXhXZWxjb21lQ2xvc2VCdG4sLmluZGV4V2VsY29tZUNvbnQgYScpLm9uKCdjbGljaycsZnVuY3Rpb24oKXtcclxuICAgICAgICAkKCcuZGFya1NjcmVlbjIuaW5kV2VsJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICQuYWpheCgnL2Nsb3NlTWVzc2FnZVJlbWluZGVyJyx7XHJcbiAgICAgICAgICAgIHR5cGU6ICdnZXQnLFxyXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxyXG4gICAgICAgICAgICBkYXRhOntzdGF0dXM6MSxwbGFjZToxMn0sXHJcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKGpzb24pIHtcclxuICAgICAgICAgICAgICAgIGlmKGpzb24uY29kZSA9PSAwKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuXHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbihlcnIpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZXJyKTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICB9KTtcclxuICAgIH0pO1xyXG5cclxufSk7XHJcblxyXG4vL+aWsOaJi+W8leWvvOW8ueeql1xyXG5cclxuJChmdW5jdGlvbigpe1xyXG4gICAgLy/lop7liqDpmZDliLbmnaHku7YgR2V0UXVlcnlTdHJpbmcoZmlyc3QpID09IDFcclxuXHJcbiAgbmV3R3VpZGUuaW5pdCgpO1xyXG59KTtcclxuXHJcbnZhciBuZXdHdWlkZT17XHJcbiAgICBpbml0OmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIGxlZnQgPSAkKCcuYmFubmVyQm94Jyk7XHJcbiAgICAgICAgdmFyIGl0ZW1JbmRleCA9IDA7Ly/liJ3lp4vlgLxcclxuICAgICAgICB2YXIgdGl0bGUgPSAkKCcudGl0bGUnKTtcclxuICAgICAgICB2YXIgY29udGVudCA9ICQoJy50eHRNc2cnKTtcclxuICAgICAgICB2YXIgbGl0dGxlRG90ID0gJCgnLnN0YXRpYycpO1xyXG4gICAgICAgIHZhciBuZXhFdmVudCA9ICQoJy5ndWlkZUJ0bicpO1xyXG4gICAgICAgIHZhciBndWlkTXNnID0gJCgnLmd1aWRlTXNnJyk7XHJcbiAgICAgICAgdmFyIG1vdmVWYWx1ZTtcclxuICAgICAgICB2YXIgbW92ZUJhbm5lcjsvL+WumuaXtuWZqFxyXG5cclxuICAgICAgICAvL+WFs+mXreW8ueeql1xyXG4gICAgICAgICQoJy5jbG9zZUd1aWRlJykub24oJ2NsaWNrJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAkKCcuZ3VpZGVCb3gnKS5mYWRlT3V0KCk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8vYnRu5LiL5pa55bCP5ZyG5ZyI5LqL5Lu2XHJcbiAgICAgICAgJCgnLm1hcmtCb3gnKS5vbignY2xpY2snLCcuc3RhdGljJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgaXRlbUluZGV4ID0gJCh0aGlzKS5pbmRleCgpO1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhpdGVtSW5kZXgpO1xyXG4gICAgICAgICAgICBjaGFuZ2UoaXRlbUluZGV4KTtcclxuICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChtb3ZlQmFubmVyKTsvL+WPl+S6i+S7tuW9seWTjeWQjumHjeaWsOiuoeaXtlxyXG4gICAgICAgICAgICBzZXRUaW1lcigpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+S4i+S4gOatpeS6i+S7tu+8m1xyXG4gICAgICAgIG5leEV2ZW50Lm9uKCdjbGljaycsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICBpdGVtSW5kZXggPSAkKCcubWFya0JveCAuYWN0aXZlJykuaW5kZXgoKTtcclxuICAgICAgICAgICBpdGVtSW5kZXgrKztcclxuICAgICAgICAgICBjbGVhckludGVydmFsKG1vdmVCYW5uZXIpO1xyXG4gICAgICAgICAgIHNldFRpbWVyKCk7XHJcbiAgICAgICAgICAgaWYoaXRlbUluZGV4ID4gMyl7XHJcbiAgICAgICAgICAgICAgICQoJy5ndWlkZUJveCcpLmZhZGVPdXQoKTtcclxuICAgICAgICAgICAgICAgLy/mmL7npLrlupfpk7rkuoznu7TnoIFcclxuICAgICAgICAgICAgICAgJCgnI3FyY29kZUFyZWEnKS5mYWRlSW4oKTtcclxuICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgY2hhbmdlKGl0ZW1JbmRleCk7XHJcbiAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+i9ruaSreWumuaXtuWZqFxyXG4gICAgICAgIHNldFRpbWVyKCk7XHJcbiAgICAgICAgZnVuY3Rpb24gc2V0VGltZXIoKSB7XHJcbiAgICAgICAgICAgIG1vdmVCYW5uZXIgPSBzZXRJbnRlcnZhbChmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBpZihpdGVtSW5kZXggPj0gMyl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChtb3ZlQmFubmVyKTtcclxuICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgIGl0ZW1JbmRleCsrO1xyXG4gICAgICAgICAgICAgICAgICAgIGNoYW5nZShpdGVtSW5kZXgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LCA4MDAwKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8v6KGM5Yqo5pa55rOVXHJcbiAgICAgICAgZnVuY3Rpb24gbW92aW5nKCl7XHJcbiAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGl0ZW1JbmRleCA9ICQoJy5tYXJrQm94IC5hY3RpdmUnKS5pbmRleCgpO1xyXG4gICAgICAgICAgICAgICAgbW92ZVZhbHVlID0gaXRlbUluZGV4KjYyMjtcclxuICAgICAgICAgICAgICAgIGxlZnQuc3RvcCgpLmFuaW1hdGUoe2xlZnQ6LW1vdmVWYWx1ZX0pO1xyXG4gICAgICAgICAgICB9LDMwMClcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGZ1bmN0aW9uIGNoYW5nZShpdGVtSW5kZXgpe1xyXG4gICAgICAgICAgICBpZihpdGVtSW5kZXggPT0gMCl7XHJcbiAgICAgICAgICAgICAgICBndWlkTXNnLnN0b3AoKS5hbmltYXRlKHtvcGFjaXR5OjB9LCdzbG93JyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRpdGxlLmh0bWwoXCLmiavnoIHorr/pl67lupfpk7pcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgY29udGVudC5odG1sKFwi5oKo5Y+v5Lul5omr5o+P5q2k5LqM57u056CB6L+b5YWl5oKo55qE55+l6K+G5bqX6ZO6XCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIGd1aWRNc2cuc3RvcCgpLmFuaW1hdGUoe29wYWNpdHk6MX0sJ3Nsb3cnKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgbmV4RXZlbnQuaHRtbCgn5LiL5LiA5q2lJyk7XHJcbiAgICAgICAgICAgICAgICAvLyBsZWZ0LmNzcygnbGVmdCcsJzBweCcpXHJcbiAgICAgICAgICAgICAgICBtb3ZpbmcoKTtcclxuICAgICAgICAgICAgfWVsc2UgaWYoaXRlbUluZGV4ID09IDEpe1xyXG4gICAgICAgICAgICAgICAgZ3VpZE1zZy5zdG9wKCkuYW5pbWF0ZSh7b3BhY2l0eTowfSwnc2xvdycsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICB0aXRsZS5odG1sKFwi5bWM5YWl5YWs5LyX5Y+3XCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnRlbnQuaHRtbChcIuWwhuW6l+mTuumTvuaOpea3u+WKoOiHs+W3suiupOivgeW+ruS/oeWFrOS8l+WPt+iHquWumuS5ieiPnOWNleagj++8jOWujOaIkOW6l+mTuuS4juWFrOS8l+WPt+eahOi/nuaOpVwiKTtcclxuICAgICAgICAgICAgICAgICAgICBndWlkTXNnLnN0b3AoKS5hbmltYXRlKHtvcGFjaXR5OjF9LCdzbG93Jyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgIG5leEV2ZW50Lmh0bWwoJ+S4i+S4gOatpScpO1xyXG4gICAgICAgICAgICAgICAgbW92aW5nKCk7XHJcbiAgICAgICAgICAgIH1lbHNlIGlmKGl0ZW1JbmRleCA9PSAyKXtcclxuICAgICAgICAgICAgICAgIGd1aWRNc2cuc3RvcCgpLmFuaW1hdGUoe29wYWNpdHk6MH0sJ3Nsb3cnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdGl0bGUuaHRtbChcIueuoeeQhuefpeivhuWVhuWTgVwiKTtcclxuICAgICAgICAgICAgICAgICAgICBjb250ZW50Lmh0bWwoXCLmgqjlj6/ku6XlnKjov5nph4zlvIDlp4vliJvlu7rlubbnrqHnkIbmgqjnmoTnn6Xor4bllYblk4FcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgZ3VpZE1zZy5zdG9wKCkuYW5pbWF0ZSh7b3BhY2l0eToxfSwnc2xvdycpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICBuZXhFdmVudC5odG1sKCfkuIvkuIDmraUnKTtcclxuICAgICAgICAgICAgICAgIG1vdmluZygpO1xyXG4gICAgICAgICAgICB9ZWxzZSBpZihpdGVtSW5kZXggPT0gMyl7XHJcbiAgICAgICAgICAgICAgICBndWlkTXNnLnN0b3AoKS5hbmltYXRlKHtvcGFjaXR5OjB9LCdzbG93JyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRpdGxlLmh0bWwoXCLmn6XnnIvluK7liqnkuK3lv4NcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgY29udGVudC5odG1sKFwi5oKo5Y+v5Lul5Zyo6L+Z6YeM5p+l55yL55u45YWz5Yqf6IO955qE5pWZ56iL6K+05piO5ZKM5biu5Yqp5paH5qGjXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIGd1aWRNc2cuc3RvcCgpLmFuaW1hdGUoe29wYWNpdHk6MX0sJ3Nsb3cnKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgbmV4RXZlbnQuaHRtbCgn56uL5Y2z5L2T6aqMJyk7XHJcbiAgICAgICAgICAgICAgICBtb3ZpbmcoKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBsaXR0bGVEb3QucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICBsaXR0bGVEb3QuZXEoaXRlbUluZGV4KS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvL+aWsOaJi+W8leWvvOaYvuekuuadoeS7tlxyXG4gICAgICAgIGlmKEdldFF1ZXJ5U3RyaW5nKCdmaXJzdCcpID09IDEpe1xyXG4gICAgICAgICAgICAkKCcuZ3VpZGVCb3gnKS5mYWRlSW4oKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbn07XHJcblxyXG5cclxuXHJcbiJdfQ==