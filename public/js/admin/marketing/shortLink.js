$(function(){shortLink()});function shortLink(){var s;var o=new Clipboard(".copyHref");o.on("success",function(s){baseUtils.show.blueTip("复制成功！请在微信内打开哦。");s.clearSelection()});$(".compress").on("click",function(){s=$(".shortInput").val();$.ajax({type:"POST",url:"/assist/st",data:{url:s},success:function(s){if(s.code==0){e(1,s.data.url);i(s.data.url);t();baseUtils.show.blueTip("压缩成功")}else if(s.code==-1){e(2);baseUtils.show.redTip(s.msg)}},error:function(s,o,e){console.log(s);console.error(e);console.error(o);baseUtils.show.redTip("网络错误，请稍后再试！")}})});function e(s,o){var e=$("<div>");if(s==1){e.html("<span class='success'>压缩成功</span><div class='shortContent'>生成的短链接：<span class='shortUrl'>"+o+"</span><a data-clipboard-text='"+o+"'class='clip copyHref'>复制链接</a></div><div class='shortContent'>链接二维码："+"<div class='frame'><div id='miniCode'></div></div><a class='clip downPic' download='qsCode.jpeg'>下载二维码</a></div>")}else{e.html("<span class='error'>压缩失败：您输入的原链接不是小鹅通链接</span>")}$(".displayVessel").html(e)}function i(s){var o=new QRCode(document.getElementById("miniCode"),{text:s,width:100,height:100,colorDark:"#000000",colorLight:"#ffffff",correctLevel:QRCode.CorrectLevel.L})}function t(){setTimeout(function(){var s=$("#miniCode img").prop("src");$(".downPic").prop("href",s)})}}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL21hcmtldGluZy9zaG9ydExpbmsuanMiXSwibmFtZXMiOlsiJCIsInNob3J0TGluayIsImlucHV0VXJsIiwiY2xpcGJvYXJkIiwiQ2xpcGJvYXJkIiwib24iLCJlIiwiYmFzZVV0aWxzIiwic2hvdyIsImJsdWVUaXAiLCJjbGVhclNlbGVjdGlvbiIsInZhbCIsImFqYXgiLCJ0eXBlIiwidXJsIiwiZGF0YSIsInN1Y2Nlc3MiLCJjb2RlIiwiY3JlYXRlRG9tIiwiUVNjcmVhdGUiLCJkb3duTG9hZEV2ZW50IiwicmVkVGlwIiwibXNnIiwiZXJyb3IiLCJ4aHIiLCJzdGF0dXMiLCJlcnIiLCJjb25zb2xlIiwibG9nIiwibnVtIiwic2hvcnRVcmwiLCJyb290RGl2IiwiaHRtbCIsInFyY29kZSIsIlFSQ29kZSIsImRvY3VtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJ0ZXh0Iiwid2lkdGgiLCJoZWlnaHQiLCJjb2xvckRhcmsiLCJjb2xvckxpZ2h0IiwiY29ycmVjdExldmVsIiwiQ29ycmVjdExldmVsIiwiTCIsInNldFRpbWVvdXQiLCJTcmMiLCJwcm9wIl0sIm1hcHBpbmdzIjoiQUFJQUEsRUFBRSxXQUNFQyxhQUdKLFNBQVNBLGFBQ0wsR0FBSUMsRUFFSixJQUFJQyxHQUFZLEdBQUlDLFdBQVUsWUFDOUJELEdBQVVFLEdBQUcsVUFBVyxTQUFTQyxHQUM3QkMsVUFBVUMsS0FBS0MsUUFBUSxpQkFDdkJILEdBQUVJLGtCQUVOVixHQUFFLGFBQWFLLEdBQUcsUUFBUSxXQUd0QkgsRUFBU0YsRUFBRSxlQUFlVyxLQUMxQlgsR0FBRVksTUFDRUMsS0FBSyxPQUNMQyxJQUFJLGFBQ0pDLE1BQU1ELElBQUlaLEdBQ1ZjLFFBQVEsU0FBU0QsR0FDYixHQUFHQSxFQUFLRSxNQUFNLEVBQUUsQ0FFWkMsRUFBVSxFQUFFSCxFQUFLQSxLQUFLRCxJQUN0QkssR0FBU0osRUFBS0EsS0FBS0QsSUFDbkJNLElBQ0FiLFdBQVVDLEtBQUtDLFFBQVEsWUFDckIsSUFBR00sRUFBS0UsT0FBTyxFQUFFLENBRW5CQyxFQUFVLEVBQ1ZYLFdBQVVDLEtBQUthLE9BQU9OLEVBQUtPLE9BR25DQyxNQUFPLFNBQVNDLEVBQUtDLEVBQVFDLEdBQ3pCQyxRQUFRQyxJQUFJSixFQUNaRyxTQUFRSixNQUFNRyxFQUNkQyxTQUFRSixNQUFNRSxFQUNkbEIsV0FBVUMsS0FBS2EsT0FBTyxtQkFNbEMsU0FBU0gsR0FBVVcsRUFBSUMsR0FDbkIsR0FBSUMsR0FBUS9CLEVBQUUsUUFDZCxJQUFHNkIsR0FBSyxFQUFFLENBQ05FLEVBQVFDLEtBQUssNEZBQTRGRixFQUNwRyxrQ0FBa0NBLEVBQVMsd0VBQzNDLHdIQUNKLENBQ0RDLEVBQVFDLEtBQUssa0RBRWpCaEMsRUFBRSxrQkFBa0JnQyxLQUFLRCxHQUc3QixRQUFTWixHQUFTTCxHQUNkLEdBQUltQixHQUFTLEdBQUlDLFFBQU9DLFNBQVNDLGVBQWUsYUFDNUNDLEtBQU12QixFQUNOd0IsTUFBTyxJQUNQQyxPQUFRLElBQ1JDLFVBQVksVUFDWkMsV0FBYSxVQUNiQyxhQUFlUixPQUFPUyxhQUFhQyxJQUszQyxRQUFTeEIsS0FDTHlCLFdBQVcsV0FDUCxHQUFJQyxHQUFJOUMsRUFBRSxpQkFBaUIrQyxLQUFLLE1BRWhDL0MsR0FBRSxZQUFZK0MsS0FBSyxPQUFPRCIsImZpbGUiOiJhZG1pbi9tYXJrZXRpbmcvc2hvcnRMaW5rLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqIENyZWF0ZWQgYnkgSmVydmlzX2NlbiBvbiAyMDE3LzYvMTQuXHJcbiAqL1xyXG5cclxuJChmdW5jdGlvbigpe1xyXG4gICAgc2hvcnRMaW5rKCk7XHJcbn0pO1xyXG5cclxuZnVuY3Rpb24gc2hvcnRMaW5rKCl7XHJcbiAgICB2YXIgaW5wdXRVcmw7Ly/ovpPlhaXnmoR1cmxcclxuICAgIC8v5Yid5aeL5YyW5Ymq6LS05p2/XHJcbiAgICB2YXIgY2xpcGJvYXJkID0gbmV3IENsaXBib2FyZCgnLmNvcHlIcmVmJyk7XHJcbiAgICBjbGlwYm9hcmQub24oJ3N1Y2Nlc3MnLCBmdW5jdGlvbihlKSB7XHJcbiAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChcIuWkjeWItuaIkOWKn++8geivt+WcqOW+ruS/oeWGheaJk+W8gOWTpuOAglwiKTtcclxuICAgICAgICBlLmNsZWFyU2VsZWN0aW9uKCk7XHJcbiAgICB9KTtcclxuICAgICQoXCIuY29tcHJlc3NcIikub24oJ2NsaWNrJyxmdW5jdGlvbigpey8v5Y6L57yp5LiA5LiLXHJcbiAgICAgICAgLy8gY29uc29sZS5sb2coJ2NvbXByZXNzIHRlc3QnKVxyXG5cclxuICAgICAgICBpbnB1dFVybD0kKFwiLnNob3J0SW5wdXRcIikudmFsKCk7XHJcbiAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgdHlwZTonUE9TVCcsXHJcbiAgICAgICAgICAgIHVybDonL2Fzc2lzdC9zdCcsXHJcbiAgICAgICAgICAgIGRhdGE6e3VybDppbnB1dFVybH0sXHJcbiAgICAgICAgICAgIHN1Y2Nlc3M6ZnVuY3Rpb24oZGF0YSl7XHJcbiAgICAgICAgICAgICAgICBpZihkYXRhLmNvZGU9PTApe1xyXG4gICAgICAgICAgICAgICAgICAgIC8vIGNvbnNvbGUubG9nKGRhdGEuZGF0YS51cmwpXHJcbiAgICAgICAgICAgICAgICAgICAgY3JlYXRlRG9tKDEsZGF0YS5kYXRhLnVybCk7XHJcbiAgICAgICAgICAgICAgICAgICAgUVNjcmVhdGUoZGF0YS5kYXRhLnVybCk7XHJcbiAgICAgICAgICAgICAgICAgICAgZG93bkxvYWRFdmVudCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLljovnvKnmiJDlip9cIik7XHJcbiAgICAgICAgICAgICAgICB9ZWxzZSBpZihkYXRhLmNvZGU9PS0xKXtcclxuICAgICAgICAgICAgICAgICAgICAvLyBjb25zb2xlLmxvZyhkYXRhKTtcclxuICAgICAgICAgICAgICAgICAgICBjcmVhdGVEb20oMik7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKGRhdGEubXNnKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uKHhociwgc3RhdHVzLCBlcnIpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKHhocik7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGVycik7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKHN0YXR1cyk7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoJ+e9kee7nOmUmeivr++8jOivt+eojeWQjuWGjeivle+8gScpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSlcclxuICAgIH0pO1xyXG5cclxuXHJcbiAgICBmdW5jdGlvbiBjcmVhdGVEb20obnVtLHNob3J0VXJsKXtcclxuICAgICAgICB2YXIgcm9vdERpdj0kKFwiPGRpdj5cIik7XHJcbiAgICAgICAgaWYobnVtPT0xKXtcclxuICAgICAgICAgICAgcm9vdERpdi5odG1sKFwiPHNwYW4gY2xhc3M9J3N1Y2Nlc3MnPuWOi+e8qeaIkOWKnzwvc3Bhbj48ZGl2IGNsYXNzPSdzaG9ydENvbnRlbnQnPueUn+aIkOeahOefremTvuaOpe+8mjxzcGFuIGNsYXNzPSdzaG9ydFVybCc+XCIrc2hvcnRVcmxcclxuICAgICAgICAgICAgICAgICtcIjwvc3Bhbj48YSBkYXRhLWNsaXBib2FyZC10ZXh0PSdcIitzaG9ydFVybCtcIidjbGFzcz0nY2xpcCBjb3B5SHJlZic+5aSN5Yi26ZO+5o6lPC9hPjwvZGl2PjxkaXYgY2xhc3M9J3Nob3J0Q29udGVudCc+6ZO+5o6l5LqM57u056CB77yaXCJcclxuICAgICAgICAgICAgICAgICtcIjxkaXYgY2xhc3M9J2ZyYW1lJz48ZGl2IGlkPSdtaW5pQ29kZSc+PC9kaXY+PC9kaXY+PGEgY2xhc3M9J2NsaXAgZG93blBpYycgZG93bmxvYWQ9J3FzQ29kZS5qcGVnJz7kuIvovb3kuoznu7TnoIE8L2E+PC9kaXY+XCIpO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICByb290RGl2Lmh0bWwoXCI8c3BhbiBjbGFzcz0nZXJyb3InPuWOi+e8qeWksei0pe+8muaCqOi+k+WFpeeahOWOn+mTvuaOpeS4jeaYr+Wwj+m5hemAmumTvuaOpTwvc3Bhbj5cIilcclxuICAgICAgICB9XHJcbiAgICAgICAgJCgnLmRpc3BsYXlWZXNzZWwnKS5odG1sKHJvb3REaXYpO1xyXG4gICAgfVxyXG4gICAgLy/nlJ/miJDkuoznu7TnoIHmlrnms5VcclxuICAgIGZ1bmN0aW9uIFFTY3JlYXRlKHVybCl7XHJcbiAgICAgICAgdmFyIHFyY29kZSA9IG5ldyBRUkNvZGUoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJtaW5pQ29kZVwiKSwge1xyXG4gICAgICAgICAgICB0ZXh0OiB1cmwsXHJcbiAgICAgICAgICAgIHdpZHRoOiAxMDAsXHJcbiAgICAgICAgICAgIGhlaWdodDogMTAwLFxyXG4gICAgICAgICAgICBjb2xvckRhcmsgOiBcIiMwMDAwMDBcIixcclxuICAgICAgICAgICAgY29sb3JMaWdodCA6IFwiI2ZmZmZmZlwiLFxyXG4gICAgICAgICAgICBjb3JyZWN0TGV2ZWwgOiBRUkNvZGUuQ29ycmVjdExldmVsLkxcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvL+WNleWHu+S4i+i9veS6i+S7tlxyXG4gICAgZnVuY3Rpb24gZG93bkxvYWRFdmVudCgpe1xyXG4gICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIFNyYz0kKFwiI21pbmlDb2RlIGltZ1wiKS5wcm9wKCdzcmMnKTtcclxuICAgICAgICAgICAgLy8gY29uc29sZS5sb2coU3JjKTtcclxuICAgICAgICAgICAgJChcIi5kb3duUGljXCIpLnByb3AoJ2hyZWYnLFNyYyk7XHJcbiAgICAgICAgfSlcclxuXHJcbiAgICB9XHJcbn07Il19
