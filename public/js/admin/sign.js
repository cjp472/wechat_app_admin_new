var wait=60,submitFlag=true;function count(){if(wait>0){$("#sendMsg").html(wait+"s后重新获取");wait--;$("#sendMsg").attr("disabled",true);$("#sendMsg").css({cursor:"not-allowed","background-color":"#b2b2b2"});setTimeout("count()",1e3)}else{$("#sendMsg").html("获取验证码");$("#sendMsg").attr("disabled",false);$("#sendMsg").css({cursor:"pointer","background-color":"#00a0e9"});wait=60}}$(document).ready(function(){$("#sendMsg").click(function(){var e=$("input[name='phoneInIdentify']").val();if(e.length==0){window.wxc.xcConfirm("请输入手机号码","error");return false}if(!/^1[34578]\d{9}$/.test(e)){window.wxc.xcConfirm("请输入正确的手机号码","error");return false}$.get("/sendmsg",{phone:e},function(e){if(e.ret==0){count()}else{window.wxc.xcConfirm("系统繁忙，请稍后再试","error")}})});$("input[name='checkCode']").keyup(function(){var e=$("input[name='checkCode']").val();if(e.length==0){return}$.get("/identify",{phoneInIdentify:$("input[name='phoneInIdentify']").val(),code:e},function(e){if(e.ret==0){$(".checkImg").eq(0).css({display:"block"});$(".checkImg").eq(1).css({display:"none"});$("#finish").attr("disabled",false);$("#finish").css({cursor:"pointer"})}else{$(".checkImg").eq(0).css({display:"none"});$(".checkImg").eq(1).css({display:"block"});$("#finish").attr("disabled",true);$("#finish").css({cursor:"not-allowed"})}})});$("#finish").click(function(){var e=GetQueryString("version_type");var n=$("input[name='contactPerson']").val();var i=$("input[name='officialAccount']").val();var r=$("input[name='phoneInIdentify']").val();var t=$("input[name='checkCode']").val();var o=$("input[type='checkbox']").is(":checked");if(i.length==0){window.wxc.xcConfirm("亲，还没输入公众号哦！~","error");return false}if(n.length==0){window.wxc.xcConfirm("亲，还没输入联系人姓名哦！~","error");return false}if(r.length==0){window.wxc.xcConfirm("亲，还没输入手机号码哦！~","error");return false}if(!/^1[34578]\d{9}$/.test(r)){window.wxc.xcConfirm("亲，没有正常输入手机号码哦！~","error");return false}if(t.length==0){window.wxc.xcConfirm("亲，还未输入验证码哦！~","error");return false}if(o==false){window.wxc.xcConfirm("亲，需要仔细阅读协议哦！~","error");return false}if(submitFlag){submitFlag=false;$.ajax("/identifysubmit",{type:"POST",dataType:"json",data:{officialAccount:i,contactPerson:n,phoneInIdentify:r,checkCode:t},success:function(n){submitFlag=true;if(n.ret==0){window.wxc.xcConfirm("注册成功","success",{onOk:function(){if(e==1){window.location.href="/index?first=1&first_login=1"}else if(e==2){window.location.href="/open_growUp_version_page?first=1&first_login=1"}else if(e==3){window.location.href="/open_vip_version_page?first=1&first_login=1"}else{window.location.href="/index?first=1&first_login=1"}}})}else{submitFlag=true;alert("系统繁忙")}},error:function(e,n,i){submitFlag=true;console.error(i);alert("系统繁忙")}})}});$(".agree").children("span").click(function(){$(".seperate").css({display:"block"});$(".agreeModal").css({display:"block"})});$("#iAgree").click(function(){$(".seperate").css({display:"none"});$(".agreeModal").css({display:"none"});$("input[type='checkbox']").prop("checked",true)})});function GetQueryString(e){var n=new RegExp("(^|&)"+e+"=([^&]*)(&|$)");var i=window.location.search.substr(1).match(n);if(i!=null)return unescape(i[2]);return null}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNpZ24uanMiXSwibmFtZXMiOlsid2FpdCIsInN1Ym1pdEZsYWciLCJjb3VudCIsIiQiLCJodG1sIiwiYXR0ciIsImNzcyIsImN1cnNvciIsImJhY2tncm91bmQtY29sb3IiLCJzZXRUaW1lb3V0IiwiZG9jdW1lbnQiLCJyZWFkeSIsImNsaWNrIiwicGhvbmVJbklkZW50aWZ5IiwidmFsIiwibGVuZ3RoIiwid2luZG93Iiwid3hjIiwieGNDb25maXJtIiwidGVzdCIsImdldCIsInBob25lIiwiZGF0YSIsInJldCIsImtleXVwIiwiY2hlY2tDb2RlIiwiY29kZSIsImVxIiwiZGlzcGxheSIsInZlcnNpb25UeXBlIiwiR2V0UXVlcnlTdHJpbmciLCJjb250YWN0UGVyc29uIiwib2ZmaWNpYWxBY2NvdW50IiwiY2hlY2tlZEZsYWciLCJpcyIsImFqYXgiLCJ0eXBlIiwiZGF0YVR5cGUiLCJzdWNjZXNzIiwib25PayIsImxvY2F0aW9uIiwiaHJlZiIsImFsZXJ0IiwiZXJyb3IiLCJ4aHIiLCJzdGF0dXMiLCJjb25zb2xlIiwiY2hpbGRyZW4iLCJwcm9wIiwibmFtZSIsInJlZyIsIlJlZ0V4cCIsInIiLCJzZWFyY2giLCJzdWJzdHIiLCJtYXRjaCIsInVuZXNjYXBlIl0sIm1hcHBpbmdzIjoiQUFJQSxHQUFJQSxNQUFPLEdBQ1BDLFdBQWEsSUFFakIsU0FBU0MsU0FDTCxHQUFJRixLQUFPLEVBQUcsQ0FDVkcsRUFBRSxZQUFZQyxLQUFLSixLQUFPLFNBQzFCQSxPQUNBRyxHQUFFLFlBQVlFLEtBQUssV0FBWSxLQUMvQkYsR0FBRSxZQUFZRyxLQUFNQyxPQUFVLGNBQWVDLG1CQUFvQixXQUNqRUMsWUFBVyxVQUFXLFNBQ25CLENBQ0hOLEVBQUUsWUFBWUMsS0FBSyxRQUNuQkQsR0FBRSxZQUFZRSxLQUFLLFdBQVksTUFDL0JGLEdBQUUsWUFBWUcsS0FBTUMsT0FBVSxVQUFXQyxtQkFBb0IsV0FDN0RSLE1BQU8sSUFLZkcsRUFBRU8sVUFBVUMsTUFBTSxXQUVkUixF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T0FBT3dCLFNBQVNDLEtBQU8sdUNBSWhDLENBQ0h4QyxXQUFhLElBQ2J5QyxPQUFNLFVBR2RDLE1BQU8sU0FBU0MsRUFBS0MsRUFBUUYsR0FDekIxQyxXQUFhLElBQ2I2QyxTQUFRSCxNQUFNQSxFQUNkRCxPQUFNLGFBUXRCdkMsR0FBRSxVQUFVNEMsU0FBUyxRQUFRbkMsTUFBTSxXQUMvQlQsRUFBRSxhQUFhRyxLQUFNc0IsUUFBVyxTQUNoQ3pCLEdBQUUsZUFBZUcsS0FBTXNCLFFBQVcsV0FJdEN6QixHQUFFLFdBQVdTLE1BQU0sV0FDZlQsRUFBRSxhQUFhRyxLQUFNc0IsUUFBVyxRQUNoQ3pCLEdBQUUsZUFBZUcsS0FBTXNCLFFBQVcsUUFDbEN6QixHQUFFLDBCQUEwQjZDLEtBQUssVUFBVyxTQU1wRCxTQUFTbEIsZ0JBQWVtQixHQUNwQixHQUFJQyxHQUFNLEdBQUlDLFFBQU8sUUFBVUYsRUFBTyxnQkFDdEMsSUFBSUcsR0FBSXBDLE9BQU93QixTQUFTYSxPQUFPQyxPQUFPLEdBQUdDLE1BQU1MLEVBQy9DLElBQUlFLEdBQUssS0FBTSxNQUFPSSxVQUFTSixFQUFFLEdBQ2pDLE9BQU8iLCJmaWxlIjoic2lnbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBDcmVhdGVkIGJ5IFN0dXBoaW4gb24gMjAxNi8xMC8wNy5cclxuICovXHJcbi8v6K6h5pWwXHJcbnZhciB3YWl0ID0gNjAsXHJcbiAgICBzdWJtaXRGbGFnID0gdHJ1ZTtcclxuXHJcbmZ1bmN0aW9uIGNvdW50KCkge1xyXG4gICAgaWYgKHdhaXQgPiAwKSB7XHJcbiAgICAgICAgJChcIiNzZW5kTXNnXCIpLmh0bWwod2FpdCArICdz5ZCO6YeN5paw6I635Y+WJyk7XHJcbiAgICAgICAgd2FpdC0tO1xyXG4gICAgICAgICQoXCIjc2VuZE1zZ1wiKS5hdHRyKFwiZGlzYWJsZWRcIiwgdHJ1ZSk7XHJcbiAgICAgICAgJChcIiNzZW5kTXNnXCIpLmNzcyh7ICdjdXJzb3InOiAnbm90LWFsbG93ZWQnLCAnYmFja2dyb3VuZC1jb2xvcic6ICcjYjJiMmIyJyB9KTtcclxuICAgICAgICBzZXRUaW1lb3V0KFwiY291bnQoKVwiLCAxMDAwKTtcclxuICAgIH0gZWxzZSB7XHJcbiAgICAgICAgJChcIiNzZW5kTXNnXCIpLmh0bWwoXCLojrflj5bpqozor4HnoIFcIik7XHJcbiAgICAgICAgJChcIiNzZW5kTXNnXCIpLmF0dHIoXCJkaXNhYmxlZFwiLCBmYWxzZSk7XHJcbiAgICAgICAgJChcIiNzZW5kTXNnXCIpLmNzcyh7ICdjdXJzb3InOiAncG9pbnRlcicsICdiYWNrZ3JvdW5kLWNvbG9yJzogJyMwMGEwZTknIH0pO1xyXG4gICAgICAgIHdhaXQgPSA2MDtcclxuICAgIH1cclxuXHJcbn1cclxuXHJcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCkge1xyXG4gICAgLy/lj5HpgIHpqozor4HnoIFcclxuICAgICQoXCIjc2VuZE1zZ1wiKS5jbGljayhmdW5jdGlvbigpIHtcclxuICAgICAgICB2YXIgcGhvbmVJbklkZW50aWZ5ID0gJChcImlucHV0W25hbWU9J3Bob25lSW5JZGVudGlmeSddXCIpLnZhbCgpO1xyXG4gICAgICAgIGlmIChwaG9uZUluSWRlbnRpZnkubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgd2luZG93Lnd4Yy54Y0NvbmZpcm0oXCLor7fovpPlhaXmiYvmnLrlj7fnoIFcIiwgXCJlcnJvclwiKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAoISgvXjFbMzQ1NzhdXFxkezl9JC8udGVzdChwaG9uZUluSWRlbnRpZnkpKSkge1xyXG4gICAgICAgICAgICB3aW5kb3cud3hjLnhjQ29uZmlybShcIuivt+i+k+WFpeato+ehrueahOaJi+acuuWPt+eggVwiLCBcImVycm9yXCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgICAgICQuZ2V0KFwiL3NlbmRtc2dcIiwgeyBcInBob25lXCI6IHBob25lSW5JZGVudGlmeSB9LCBmdW5jdGlvbihkYXRhKSB7XHJcbiAgICAgICAgICAgIGlmIChkYXRhLnJldCA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICBjb3VudCgpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgd2luZG93Lnd4Yy54Y0NvbmZpcm0oXCLns7vnu5/nuYHlv5nvvIzor7fnqI3lkI7lho3or5VcIiwgXCJlcnJvclwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/moKHpqozpqozor4HnoIFcclxuICAgICQoXCJpbnB1dFtuYW1lPSdjaGVja0NvZGUnXVwiKS5rZXl1cChmdW5jdGlvbigpIHtcclxuICAgICAgICB2YXIgY2hlY2tDb2RlID0gJChcImlucHV0W25hbWU9J2NoZWNrQ29kZSddXCIpLnZhbCgpO1xyXG4gICAgICAgIGlmIChjaGVja0NvZGUubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICAkLmdldCgnL2lkZW50aWZ5JywgeyBcInBob25lSW5JZGVudGlmeVwiOiAkKFwiaW5wdXRbbmFtZT0ncGhvbmVJbklkZW50aWZ5J11cIikudmFsKCksIFwiY29kZVwiOiBjaGVja0NvZGUgfSxcclxuICAgICAgICAgICAgZnVuY3Rpb24oZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgaWYgKGRhdGEucmV0ID09IDApIHtcclxuICAgICAgICAgICAgICAgICAgICAkKFwiLmNoZWNrSW1nXCIpLmVxKDApLmNzcyh7ICdkaXNwbGF5JzogJ2Jsb2NrJyB9KTtcclxuICAgICAgICAgICAgICAgICAgICAkKFwiLmNoZWNrSW1nXCIpLmVxKDEpLmNzcyh7ICdkaXNwbGF5JzogJ25vbmUnIH0pO1xyXG4gICAgICAgICAgICAgICAgICAgICQoXCIjZmluaXNoXCIpLmF0dHIoJ2Rpc2FibGVkJywgZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgICAgICQoXCIjZmluaXNoXCIpLmNzcyh7ICdjdXJzb3InOiAncG9pbnRlcicgfSk7XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICQoXCIuY2hlY2tJbWdcIikuZXEoMCkuY3NzKHsgJ2Rpc3BsYXknOiAnbm9uZScgfSk7XHJcbiAgICAgICAgICAgICAgICAgICAgJChcIi5jaGVja0ltZ1wiKS5lcSgxKS5jc3MoeyAnZGlzcGxheSc6ICdibG9jaycgfSk7XHJcbiAgICAgICAgICAgICAgICAgICAgJChcIiNmaW5pc2hcIikuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICAgICAgICAgICAgICAkKFwiI2ZpbmlzaFwiKS5jc3MoeyAnY3Vyc29yJzogJ25vdC1hbGxvd2VkJyB9KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+iupOivgeaPkOS6pFxyXG4gICAgJChcIiNmaW5pc2hcIikuY2xpY2soZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgLy/ojrflj5bmlbDmja5cclxuICAgICAgICB2YXIgdmVyc2lvblR5cGUgPSBHZXRRdWVyeVN0cmluZyhcInZlcnNpb25fdHlwZVwiKTtcclxuICAgICAgICB2YXIgY29udGFjdFBlcnNvbiA9ICQoXCJpbnB1dFtuYW1lPSdjb250YWN0UGVyc29uJ11cIikudmFsKCk7XHJcbiAgICAgICAgdmFyIG9mZmljaWFsQWNjb3VudCA9ICQoXCJpbnB1dFtuYW1lPSdvZmZpY2lhbEFjY291bnQnXVwiKS52YWwoKTtcclxuICAgICAgICB2YXIgcGhvbmVJbklkZW50aWZ5ID0gJChcImlucHV0W25hbWU9J3Bob25lSW5JZGVudGlmeSddXCIpLnZhbCgpO1xyXG4gICAgICAgIHZhciBjaGVja0NvZGUgPSAkKFwiaW5wdXRbbmFtZT0nY2hlY2tDb2RlJ11cIikudmFsKCk7XHJcbiAgICAgICAgdmFyIGNoZWNrZWRGbGFnID0gJChcImlucHV0W3R5cGU9J2NoZWNrYm94J11cIikuaXMoJzpjaGVja2VkJyk7XHJcbiAgICAgICAgLy/moKHpqoxcclxuICAgICAgICBpZiAob2ZmaWNpYWxBY2NvdW50Lmxlbmd0aCA9PSAwKSB7XHJcbiAgICAgICAgICAgIHdpbmRvdy53eGMueGNDb25maXJtKFwi5Lqy77yM6L+Y5rKh6L6T5YWl5YWs5LyX5Y+35ZOm77yBflwiLCBcImVycm9yXCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmIChjb250YWN0UGVyc29uLmxlbmd0aCA9PSAwKSB7XHJcbiAgICAgICAgICAgIHdpbmRvdy53eGMueGNDb25maXJtKFwi5Lqy77yM6L+Y5rKh6L6T5YWl6IGU57O75Lq65aeT5ZCN5ZOm77yBflwiLCBcImVycm9yXCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmIChwaG9uZUluSWRlbnRpZnkubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgd2luZG93Lnd4Yy54Y0NvbmZpcm0oXCLkurLvvIzov5jmsqHovpPlhaXmiYvmnLrlj7fnoIHlk6bvvIF+XCIsIFwiZXJyb3JcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKCEoL14xWzM0NTc4XVxcZHs5fSQvLnRlc3QocGhvbmVJbklkZW50aWZ5KSkpIHtcclxuICAgICAgICAgICAgd2luZG93Lnd4Yy54Y0NvbmZpcm0oXCLkurLvvIzmsqHmnInmraPluLjovpPlhaXmiYvmnLrlj7fnoIHlk6bvvIF+XCIsIFwiZXJyb3JcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKGNoZWNrQ29kZS5sZW5ndGggPT0gMCkge1xyXG4gICAgICAgICAgICB3aW5kb3cud3hjLnhjQ29uZmlybShcIuS6su+8jOi/mOacqui+k+WFpemqjOivgeeggeWTpu+8gX5cIiwgXCJlcnJvclwiKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAoY2hlY2tlZEZsYWcgPT0gZmFsc2UpIHtcclxuICAgICAgICAgICAgd2luZG93Lnd4Yy54Y0NvbmZpcm0oXCLkurLvvIzpnIDopoHku5Tnu4bpmIXor7vljY/orq7lk6bvvIF+XCIsIFwiZXJyb3JcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy/mj5DkuqRcclxuICAgICAgICBpZiggc3VibWl0RmxhZyApe1xyXG4gICAgICAgICAgICBzdWJtaXRGbGFnID0gZmFsc2U7XHJcbiAgICAgICAgICAgICQuYWpheChcIi9pZGVudGlmeXN1Ym1pdFwiLCB7XHJcbiAgICAgICAgICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxyXG4gICAgICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgICAgIFwib2ZmaWNpYWxBY2NvdW50XCI6IG9mZmljaWFsQWNjb3VudCxcclxuICAgICAgICAgICAgICAgICAgICBcImNvbnRhY3RQZXJzb25cIjogY29udGFjdFBlcnNvbixcclxuICAgICAgICAgICAgICAgICAgICBcInBob25lSW5JZGVudGlmeVwiOiBwaG9uZUluSWRlbnRpZnksXHJcbiAgICAgICAgICAgICAgICAgICAgXCJjaGVja0NvZGVcIjogY2hlY2tDb2RlXHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24oZGF0YSkgeyAvL3JldFxyXG4gICAgICAgICAgICAgICAgICAgIHN1Ym1pdEZsYWcgPSB0cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmIChkYXRhLnJldCA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy53eGMueGNDb25maXJtKFwi5rOo5YaM5oiQ5YqfXCIsIFwic3VjY2Vzc1wiLCB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbk9rOiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodmVyc2lvblR5cGUgPT0gMSkgey8v5Z+656GA54mIXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvaW5kZXg/Zmlyc3Q9MSZmaXJzdF9sb2dpbj0xXCI7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2UgaWYgKHZlcnNpb25UeXBlID09IDIpIHsvL+aIkOmVv+eJiFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL29wZW5fZ3Jvd1VwX3ZlcnNpb25fcGFnZT9maXJzdD0xJmZpcnN0X2xvZ2luPTFcIjtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZSBpZiAodmVyc2lvblR5cGUgPT0gMykgey8v5LiT5Lia54mIXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvb3Blbl92aXBfdmVyc2lvbl9wYWdlP2ZpcnN0PTEmZmlyc3RfbG9naW49MVwiO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL2luZGV4P2ZpcnN0PTEmZmlyc3RfbG9naW49MVwiO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgc3VibWl0RmxhZyA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGFsZXJ0KFwi57O757uf57mB5b+ZXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24oeGhyLCBzdGF0dXMsIGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc3VibWl0RmxhZyA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnJvcik7XHJcbiAgICAgICAgICAgICAgICAgICAgYWxlcnQoXCLns7vnu5/nuYHlv5lcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+aJk+W8gOWNj+iurlxyXG4gICAgJChcIi5hZ3JlZVwiKS5jaGlsZHJlbihcInNwYW5cIikuY2xpY2soZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgJChcIi5zZXBlcmF0ZVwiKS5jc3MoeyAnZGlzcGxheSc6ICdibG9jaycgfSk7XHJcbiAgICAgICAgJChcIi5hZ3JlZU1vZGFsXCIpLmNzcyh7ICdkaXNwbGF5JzogJ2Jsb2NrJyB9KTtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v5YWz6Zet5Y2P6K6uXHJcbiAgICAkKFwiI2lBZ3JlZVwiKS5jbGljayhmdW5jdGlvbigpIHtcclxuICAgICAgICAkKFwiLnNlcGVyYXRlXCIpLmNzcyh7ICdkaXNwbGF5JzogJ25vbmUnIH0pO1xyXG4gICAgICAgICQoXCIuYWdyZWVNb2RhbFwiKS5jc3MoeyAnZGlzcGxheSc6ICdub25lJyB9KTtcclxuICAgICAgICAkKFwiaW5wdXRbdHlwZT0nY2hlY2tib3gnXVwiKS5wcm9wKFwiY2hlY2tlZFwiLCB0cnVlKTtcclxuICAgIH0pO1xyXG59KTtcclxuXHJcbi8v6I635Y+W5Zyw5Z2A5qCP5Y+C5pWwXHJcblxyXG5mdW5jdGlvbiBHZXRRdWVyeVN0cmluZyhuYW1lKSB7XHJcbiAgICB2YXIgcmVnID0gbmV3IFJlZ0V4cChcIihefCYpXCIgKyBuYW1lICsgXCI9KFteJl0qKSgmfCQpXCIpO1xyXG4gICAgdmFyIHIgPSB3aW5kb3cubG9jYXRpb24uc2VhcmNoLnN1YnN0cigxKS5tYXRjaChyZWcpO1xyXG4gICAgaWYgKHIgIT0gbnVsbCkgcmV0dXJuIHVuZXNjYXBlKHJbMl0pO1xyXG4gICAgcmV0dXJuIG51bGw7XHJcbn1cclxuIl19
