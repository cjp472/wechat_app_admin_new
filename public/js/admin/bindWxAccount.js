var app_id;var bind_account_wx_id;var create_qr_flag;var qt_http;$(document).ready(function(){create_qr_flag=true;setTopUrlCookie("payadmin_listop","财务管理");keyEnter($("#pay_search_btn"));$("tbody tr").mouseover(function(){$(this).css({"background-color":"#f5f5f5"})}).mouseout(function(){$(this).css({"background-color":"#fff"})})});function createQR(){app_id=$("#bind_wxaccount").data("app_id");var e=new QRCode(document.getElementById("qr_code"),{text:qt_http+bind_account_wx_id,width:200,height:200,colorDark:"#000000",colorLight:"#ffffff",correctLevel:QRCode.CorrectLevel.H});create_qr_flag=false}var create_recode=true;function ShowQRCode(){$("#ExportModal").modal("show");if(create_recode){$.get("/create_wx_account_by_appid",{app_id:app_id},function(e){if(e.code==0){bind_account_wx_id=e.data;if(create_qr_flag){createQR()}clear=setInterval(queryresult,5e3)}else{baseUtils.show.redTip(e.msg)}})}create_recode=false}var clear;var max_time=300*1e3;var current_time=0;function queryresult(){$.get("/query_saomiao_result",function(e){if(e.code==0){clearInterval(clear);create_recode=true;create_qr_flag=true;$("#ExportModal").modal("hide");$("#bind_wx_account").html(e.data)}else{current_time+=1e3;if(current_time==max_time){create_recode=true;create_qr_flag=true;clearInterval(clear)}}})}function confirm_bind_wx(){var e=$("#wx_avatar")[0].src;var r=$("#wx_nickname").text();var t=$("#wx_avatar").data("wx_open_id");var a=$("#sms_code").val();if(t==""||r==""||e==""){baseUtils.show.redTip("请重新扫码!");return}if(a==""){baseUtils.show.redTip("验证码不能为空");return}$.post("/bind_wx_account",{wx_avatar:e,wx_nickname:r,wx_open_id:t,sms_code:a},function(e){if(e.code==0){window.location.href="/apply_withdraw_page"}else{baseUtils.show.redTip(e.msg)}})}var send_sms_flag=true;function sendsms(){if(send_sms_flag){register.sendCoder()}else{return false}}var timer;var register={count:60,clear:null,flag:false,loginFlag:false,coderTick:function(e){timer=setInterval(register.tick,1e3)},tick:function(){var e=$("#get_sms_code");if(register.count==0){clearInterval(timer);register.count=60;e.html("获取验证码");$("#get_sms_code").removeClass("disabled");register.flag=false;e.click(function(){register.sendCoder()})}else{register.count--;e.unbind("click").html(register.count+"s后重新发送");$("#get_sms_code").addClass("disabled");register.flag=true;send_sms_flag=false}},sendCoder:function(e){var r={};r.code_type=6;r.phone=e;if(!register.flag){register.flag=true;$.get("/send_sms",function(e){if(e.code==0){register.coderTick()}else{baseUtils.show.redTip("验证码发送失败,请重试!");register.flag=false}})}}};
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2JpbmRXeEFjY291bnQuanMiXSwibmFtZXMiOlsiYXBwX2lkIiwiYmluZF9hY2NvdW50X3d4X2lkIiwiY3JlYXRlX3FyX2ZsYWciLCJxdF9odHRwIiwiJCIsImRvY3VtZW50IiwicmVhZHkiLCJzZXRUb3BVcmxDb29raWUiLCJrZXlFbnRlciIsIm1vdXNlb3ZlciIsInRoaXMiLCJjc3MiLCJiYWNrZ3JvdW5kLWNvbG9yIiwibW91c2VvdXQiLCJjcmVhdGVRUiIsImRhdGEiLCJxcmNvZGUiLCJRUkNvZGUiLCJnZXRFbGVtZW50QnlJZCIsInRleHQiLCJ3aWR0aCIsImhlaWdodCIsImNvbG9yRGFyayIsImNvbG9yTGlnaHQiLCJjb3JyZWN0TGV2ZWwiLCJDb3JyZWN0TGV2ZWwiLCJIIiwiY3JlYXRlX3JlY29kZSIsIlNob3dRUkNvZGUiLCJtb2RhbCIsImdldCIsImNvZGUiLCJjbGVhciIsInNldEludGVydmFsIiwicXVlcnlyZXN1bHQiLCJiYXNlVXRpbHMiLCJzaG93IiwicmVkVGlwIiwibXNnIiwibWF4X3RpbWUiLCJjdXJyZW50X3RpbWUiLCJjbGVhckludGVydmFsIiwiaHRtbCIsImNvbmZpcm1fYmluZF93eCIsInd4X2F2YXRhciIsInNyYyIsInd4X25pY2tuYW1lIiwid3hfb3Blbl9pZCIsInNtc19jb2RlIiwidmFsIiwicG9zdCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsInNlbmRfc21zX2ZsYWciLCJzZW5kc21zIiwicmVnaXN0ZXIiLCJzZW5kQ29kZXIiLCJ0aW1lciIsImNvdW50IiwiZmxhZyIsImxvZ2luRmxhZyIsImNvZGVyVGljayIsIiRwaG9uZSIsInRpY2siLCIkY29kZXIiLCJyZW1vdmVDbGFzcyIsImNsaWNrIiwidW5iaW5kIiwiYWRkQ2xhc3MiLCJwaG9uZSIsInBhcmFtIiwiY29kZV90eXBlIl0sIm1hcHBpbmdzIjoiQUFHQSxHQUFJQSxPQUNKLElBQUlDLG1CQUNKLElBQUlDLGVBQ0osSUFBSUMsUUFDSkMsR0FB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YyxTQUFTRyxNQUFRLFNBQzdDdkQsR0FBRSxpQkFBaUJpRSxTQUFTLFdBQzVCYixVQUFTSSxLQUFPLElBQ2hCTixlQUFnQixRQUd4QkcsVUFBVyxTQUFVYSxHQUNqQixHQUFJQyxLQUVKQSxHQUFNQyxVQUFZLENBQ2xCRCxHQUFNRCxNQUFRQSxDQUVkLEtBQUtkLFNBQVNJLEtBQU0sQ0FDaEJKLFNBQVNJLEtBQU8sSUFFaEJ4RCxHQUFFMEIsSUFBSSxZQUFjLFNBQVVmLEdBQzFCLEdBQUlBLEVBQUtnQixNQUFRLEVBQUcsQ0FDaEJ5QixTQUFTTSxnQkFDTixDQUNIM0IsVUFBVUMsS0FBS0MsT0FBTyxlQUN0Qm1CLFVBQVNJLEtBQU8iLCJmaWxlIjoiYWRtaW4vYmluZFd4QWNjb3VudC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogQ3JlYXRlZCBieSBmdWhhaXdlbiBvbiAyMDE3LzEvMTcuXG4gKi9cbnZhciBhcHBfaWQ7XG52YXIgYmluZF9hY2NvdW50X3d4X2lkO1xudmFyIGNyZWF0ZV9xcl9mbGFnIDtcbnZhciBxdF9odHRwIDtcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcbiAgICAvL+eUn+aIkOS6jOe7tOeggVxuICAgIGNyZWF0ZV9xcl9mbGFnID0gdHJ1ZTtcbiAgICAvLyBjcmVhdGVRUigpO1xuICAgIC8v5aGr5YWl5pCc57Si5YC8XG4gICAgLy8gJChcInNlbGVjdFtpZD0nZ2VuZXJhdGVfdHlwZSddXCIpLnZhbCgoZ2V0VXJsUGFyYW0oXCJnZW5lcmF0ZV90eXBlXCIpPT1udWxsICB8fCBnZXRVcmxQYXJhbShcImdlbmVyYXRlX3R5cGVcIik9PScnKSA/ICcnIDpnZXRVcmxQYXJhbShcImdlbmVyYXRlX3R5cGVcIikpO1xuXG4gICAgc2V0VG9wVXJsQ29va2llKCdwYXlhZG1pbl9saXN0b3AnLCfotKLliqHnrqHnkIYnKTtcbiAgICBrZXlFbnRlcigkKCcjcGF5X3NlYXJjaF9idG4nKSk7XG4gICAgJChcInRib2R5IHRyXCIpLm1vdXNlb3ZlcihmdW5jdGlvbigpXG4gICAge1xuICAgICAgICAkKHRoaXMpLmNzcyh7J2JhY2tncm91bmQtY29sb3InOicjZjVmNWY1J30pO1xuICAgIH0pLm1vdXNlb3V0KGZ1bmN0aW9uKClcbiAgICB7XG4gICAgICAgICQodGhpcykuY3NzKHsnYmFja2dyb3VuZC1jb2xvcic6JyNmZmYnfSk7XG4gICAgfSk7XG5cbiAgICAvLyAkKFwiI3Ntc19jb2RlXCIpLmJsdXIoZnVuY3Rpb24gKCkge1xuICAgIC8vICAgICBqdWRnZVNtc2NvZGUoXCIjc21zX2NvZGVcIiwgXCIjc21zX2NvZGVfZXJyXCIpO1xuICAgIC8vICAgICB9XG4gICAgLy8gKTtcblxufSk7XG5cbi8vIGZ1bmN0aW9uIGp1ZGdlU21zY29kZSgpIHtcbi8vXG4vLyB9XG5cbmZ1bmN0aW9uIGNyZWF0ZVFSKCkge1xuICAgIC8v55Sf5oiQ5LqM57u056CBXG4gICAgYXBwX2lkID0gJChcIiNiaW5kX3d4YWNjb3VudFwiKS5kYXRhKFwiYXBwX2lkXCIpO1xuICAgIHZhciBxcmNvZGUgPSBuZXcgUVJDb2RlKGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwicXJfY29kZVwiKSxcbiAgICAgICAge1xuICAgICAgICAgICAgdGV4dDogcXRfaHR0cCtiaW5kX2FjY291bnRfd3hfaWQsXG4gICAgICAgICAgICB3aWR0aDogMjAwLFxuICAgICAgICAgICAgaGVpZ2h0OiAyMDAsXG4gICAgICAgICAgICBjb2xvckRhcmsgOiBcIiMwMDAwMDBcIixcbiAgICAgICAgICAgIGNvbG9yTGlnaHQgOiBcIiNmZmZmZmZcIixcbiAgICAgICAgICAgIGNvcnJlY3RMZXZlbCA6IFFSQ29kZS5Db3JyZWN0TGV2ZWwuSFxuICAgICAgICB9KTtcbiAgICBjcmVhdGVfcXJfZmxhZyA9IGZhbHNlO1xufVxuLy/miavnoIHlvLnmoYbmmL7npLpcbnZhciBjcmVhdGVfcmVjb2RlID0gdHJ1ZTtcbmZ1bmN0aW9uIFNob3dRUkNvZGUoKSB7XG4gICAgJChcIiNFeHBvcnRNb2RhbFwiKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgaWYoY3JlYXRlX3JlY29kZSkge1xuICAgICAgICAgLy/lj5HpgIFhamF46K+35rGC55Sf5oiQ5LiA5p2h6K6w5b2V5Zyo6KGodF9iaW5kX2FjY291bnRfd3jkuK1cbiAgICAgICAgICQuZ2V0KCcvY3JlYXRlX3d4X2FjY291bnRfYnlfYXBwaWQnLCB7J2FwcF9pZCc6IGFwcF9pZH0sIGZ1bmN0aW9uIChkYXRhKSB7XG4gICAgICAgICAgICAgaWYgKGRhdGEuY29kZSA9PSAwKSB7XG4gICAgICAgICAgICAgICAgIC8v5pi+56S65by55qGGXG4gICAgICAgICAgICAgICAgIC8vICQoXCIjYmluZF93eGFjY291bnRcIikuZGF0YSgnYmluZF9hY2NvdW50X3d4X2lkJykudmFsKGRhdGEuZGF0YSk7XG4gICAgICAgICAgICAgICAgIGJpbmRfYWNjb3VudF93eF9pZCA9IGRhdGEuZGF0YTtcbiAgICAgICAgICAgICAgICAgaWYgKGNyZWF0ZV9xcl9mbGFnKSB7XG4gICAgICAgICAgICAgICAgICAgICBjcmVhdGVRUigpO1xuICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgIC8vIHF1ZXJ5cmVzdWx0KCk7XG4gICAgICAgICAgICAgICAgIGNsZWFyID0gc2V0SW50ZXJ2YWwocXVlcnlyZXN1bHQsNTAwMCk7XG5cbiAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoZGF0YS5tc2cpO1xuICAgICAgICAgICAgIH1cbiAgICAgICAgIH0pO1xuICAgICB9XG4gICAgY3JlYXRlX3JlY29kZSA9IGZhbHNlO1xuXG5cbn1cbi8v5p+l6K+i5omr56CB57uT5p6cXG52YXIgY2xlYXI7XG52YXIgbWF4X3RpbWUgPSAzMDAqMTAwMDtcbnZhciBjdXJyZW50X3RpbWUgPSAwO1xuZnVuY3Rpb24gcXVlcnlyZXN1bHQoKSB7XG5cbiAgICAkLmdldCgnL3F1ZXJ5X3Nhb21pYW9fcmVzdWx0JyxmdW5jdGlvbiAoZGF0YSkge1xuICAgICAgICBpZihkYXRhLmNvZGUgPT0gMCl7XG4gICAgICAgICAgICBjbGVhckludGVydmFsKGNsZWFyKTtcbiAgICAgICAgICAgIGNyZWF0ZV9yZWNvZGUgPSB0cnVlO1xuICAgICAgICAgICAgY3JlYXRlX3FyX2ZsYWcgPSB0cnVlO1xuICAgICAgICAgICAgJChcIiNFeHBvcnRNb2RhbFwiKS5tb2RhbCgnaGlkZScpO1xuICAgICAgICAgICAgJChcIiNiaW5kX3d4X2FjY291bnRcIikuaHRtbChkYXRhLmRhdGEpO1xuICAgICAgICB9ZWxzZXtcbiAgICAgICAgICAgIC8vIGNsZWFyID0gc2V0SW50ZXJ2YWwocXVlcnlyZXN1bHQsNTAwMCk7XG5cbiAgICAgICAgICAgIGN1cnJlbnRfdGltZSArPSAxMDAwO1xuXG4gICAgICAgICAgICBpZihjdXJyZW50X3RpbWUgPT0gbWF4X3RpbWUpe1xuICAgICAgICAgICAgICAgIGNyZWF0ZV9yZWNvZGUgPSB0cnVlO1xuICAgICAgICAgICAgICAgIGNyZWF0ZV9xcl9mbGFnID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICBjbGVhckludGVydmFsKGNsZWFyKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0pO1xuXG59XG5cbmZ1bmN0aW9uIGNvbmZpcm1fYmluZF93eCgpIHtcblxuICAgIC8v5Y+R6YCB56Gu6K6k57uR5a6a55qE6K+35rGCOmFqYXjor7fmsYJcbiAgICAvL+S8oOeahOWPguaVsOaciTrlpLTlg4/jgIHmmLXnp7DjgIFvcGVuX2lk5Lul5Y+K6aqM6K+B56CBXG5cbiAgICB2YXIgd3hfYXZhdGFyID0gJChcIiN3eF9hdmF0YXJcIilbMF0uc3JjO1xuICAgIHZhciB3eF9uaWNrbmFtZSA9ICQoXCIjd3hfbmlja25hbWVcIikudGV4dCgpO1xuICAgIHZhciB3eF9vcGVuX2lkID0gJChcIiN3eF9hdmF0YXJcIikuZGF0YSgnd3hfb3Blbl9pZCcpO1xuICAgIHZhciBzbXNfY29kZSA9ICQoXCIjc21zX2NvZGVcIikudmFsKCk7XG5cbiAgICAvLyBhbGVydChcInd4X29wZW5faWRcIit3eF9vcGVuX2lkKTtcbiAgICBpZiAod3hfb3Blbl9pZCA9PSAnJyB8fCB3eF9uaWNrbmFtZSA9PSAnJyB8fCB3eF9hdmF0YXIgPT0gJycgKSB7XG4gICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn6K+36YeN5paw5omr56CBIScpO1xuICAgICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmIChzbXNfY29kZSA9PSAnJykge1xuICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoJ+mqjOivgeeggeS4jeiDveS4uuepuicpO1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJC5wb3N0KCcvYmluZF93eF9hY2NvdW50JyxcbiAgICAgICAge1xuICAgICAgICAgICAgJ3d4X2F2YXRhcic6d3hfYXZhdGFyLFxuICAgICAgICAgICAgJ3d4X25pY2tuYW1lJzp3eF9uaWNrbmFtZSxcbiAgICAgICAgICAgICd3eF9vcGVuX2lkJzp3eF9vcGVuX2lkLFxuICAgICAgICAgICAgJ3Ntc19jb2RlJzpzbXNfY29kZVxuICAgICAgICB9LFxuICAgICAgICBmdW5jdGlvbihkYXRhKXtcbiAgICAgICAgICAgIGlmKGRhdGEuY29kZSA9PSAwKXtcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL2FwcGx5X3dpdGhkcmF3X3BhZ2VcIjtcbiAgICAgICAgICAgIH1lbHNle1xuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChkYXRhLm1zZyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xufVxudmFyIHNlbmRfc21zX2ZsYWcgPSB0cnVlO1xuZnVuY3Rpb24gc2VuZHNtcygpIHtcblxuICAgIGlmKHNlbmRfc21zX2ZsYWcpIHtcbiAgICAgICAgcmVnaXN0ZXIuc2VuZENvZGVyKCk7XG4gICAgfWVsc2V7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG59XG52YXIgdGltZXI7XG52YXIgcmVnaXN0ZXIgPSB7XG4gICAgY291bnQ6IDYwLFxuICAgIGNsZWFyOiBudWxsLFxuICAgIGZsYWc6IGZhbHNlLFxuICAgIGxvZ2luRmxhZzogZmFsc2UsXG4gICAgY29kZXJUaWNrOiBmdW5jdGlvbiAoJHBob25lKSB7XG4gICAgICAgIC8v5YCS6K6h5pe2XG4gICAgICAgIHRpbWVyID0gc2V0SW50ZXJ2YWwocmVnaXN0ZXIudGljaywgMTAwMCk7XG4gICAgfSxcbiAgICB0aWNrOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciAkY29kZXIgPSAkKCcjZ2V0X3Ntc19jb2RlJyk7XG4gICAgICAgIC8vdXRpbC5sYXllcigkcGhvbmUpO1xuICAgICAgICBpZiAocmVnaXN0ZXIuY291bnQgPT0gMCkge1xuICAgICAgICAgICAgY2xlYXJJbnRlcnZhbCh0aW1lcik7XG4gICAgICAgICAgICByZWdpc3Rlci5jb3VudCA9IDYwO1xuICAgICAgICAgICAgJGNvZGVyLmh0bWwoJ+iOt+WPlumqjOivgeeggScpO1xuICAgICAgICAgICAgJChcIiNnZXRfc21zX2NvZGVcIikucmVtb3ZlQ2xhc3MoXCJkaXNhYmxlZFwiKTtcbiAgICAgICAgICAgIC8vIHNlbmRfc21zX2ZsYWcgPSB0cnVlO1xuICAgICAgICAgICAgcmVnaXN0ZXIuZmxhZyA9IGZhbHNlO1xuICAgICAgICAgICAgJGNvZGVyLmNsaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICByZWdpc3Rlci5zZW5kQ29kZXIoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcmVnaXN0ZXIuY291bnQtLTtcbiAgICAgICAgICAgICRjb2Rlci51bmJpbmQoJ2NsaWNrJykuaHRtbChyZWdpc3Rlci5jb3VudCArICdz5ZCO6YeN5paw5Y+R6YCBJyk7XG4gICAgICAgICAgICAkKFwiI2dldF9zbXNfY29kZVwiKS5hZGRDbGFzcyhcImRpc2FibGVkXCIpO1xuICAgICAgICAgICAgcmVnaXN0ZXIuZmxhZyA9IHRydWU7XG4gICAgICAgICAgICBzZW5kX3Ntc19mbGFnID0gZmFsc2U7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIHNlbmRDb2RlcjogZnVuY3Rpb24gKHBob25lKSB7XG4gICAgICAgIHZhciBwYXJhbSA9IHt9O1xuICAgICAgICAvLzE655+t5L+hXG4gICAgICAgIHBhcmFtLmNvZGVfdHlwZSA9IDY7XG4gICAgICAgIHBhcmFtLnBob25lID0gcGhvbmU7XG4gICAgICAgIC8v6Ziy5q2i5aSa5qyh54K55Ye75pe277yM5aSa5qyh5o+Q5Lqk6K+35rGCXG4gICAgICAgIGlmICghcmVnaXN0ZXIuZmxhZykge1xuICAgICAgICAgICAgcmVnaXN0ZXIuZmxhZyA9IHRydWU7XG4gICAgICAgICAgICAvL+WPkemAgemqjOivgeeggVxuICAgICAgICAgICAgJC5nZXQoJy9zZW5kX3NtcycsICBmdW5jdGlvbiAoZGF0YSkge1xuICAgICAgICAgICAgICAgIGlmIChkYXRhLmNvZGUgPT0gMCkge1xuICAgICAgICAgICAgICAgICAgICByZWdpc3Rlci5jb2RlclRpY2soKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLpqozor4HnoIHlj5HpgIHlpLHotKUs6K+36YeN6K+VIVwiKTtcbiAgICAgICAgICAgICAgICAgICAgcmVnaXN0ZXIuZmxhZyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuXG59Il19