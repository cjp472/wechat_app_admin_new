$(function(){changePassword.init()});var changePassword=function(){var e={};var s=$("#phone").text(),n,t,r,o=1,i=0;function a(){var e=60;o=0;var s=setInterval(function(){if(e>0){$(".getCodeBtn").text(e+"秒");e--}else{$(".getCodeBtn").removeClass("preventClickBtn");$(".getCodeBtn").addClass("btnBlue");$(".getCodeBtn").text("再次获取");o=1;clearInterval(s)}},1e3)}e.init=function(){$(".getCodeBtn").click(function(){if(o){$(this).addClass("preventClickBtn");a();$.get("/sendmsg",{phone:s},function(e){if(e.ret==0){}else{}})}});$(".identifyCodeInput").keyup(function(){r=$(".identifyCodeInput").val();if(r.length==0){$(".successTip,.errorTip").show();$(".identifyCodeInput").removeClass("borderRed");return}$.get("/identify",{phoneInIdentify:s,code:r},function(e){if(e.ret==0){$(".successTip").show();$(".errorTip").hide();$(".identifyCodeInput").removeClass("borderRed");i=1}else{$(".successTip").hide();$(".errorTip").show();$(".identifyCodeInput").addClass("borderRed");i=0}})});$(".passwordInput").keyup(function(){n=$(".passwordInput").val();t=$(".passwordInputAgain").val();if(!$formCheck.checkPassword(n)){$(this).addClass("borderRed");$(".errorMsgFirst").fadeIn(300)}else{$(this).removeClass("borderRed");$(".errorMsgFirst").fadeOut(300)}if(t.length!=0&&t!=n){$(".passwordInputAgain").addClass("borderRed");$(".errorMsgSecond").fadeIn(300)}else{$(".passwordInputAgain").removeClass("borderRed");$(".errorMsgSecond").fadeOut(300)}});$(".passwordInputAgain").keyup(function(){n=$(".passwordInput").val();t=$(".passwordInputAgain").val();if(t.length!=0&&t!=n){$(this).addClass("borderRed");$(".errorMsgSecond").fadeIn(300)}else{$(this).removeClass("borderRed");$(".errorMsgSecond").fadeOut(300)}});$(".confirmBtn").click(function(){s=$("#phone").text();console.log(s);r=$(".identifyCodeInput").val();n=$(".passwordInput").val();t=$(".passwordInputAgain").val();if(r==""){baseUtils.show.redTip("请输入验证码");return false}if(i==0){baseUtils.show.redTip("验证码错误，请重新输入");return false}if(i==0){baseUtils.show.redTip("验证码错误，请重新输入");return false}if(n.length==0||t.length==0){baseUtils.show.redTip("密码不能为空，请输入密码");return false}if(n.length<6||n.length>16){baseUtils.show.redTip("密码长度必须在6位到16位之间哦~");return false}if(!$formCheck.checkPassword){baseUtils.show.redTip("密码格式不正确，请重新输入~");return false}if(n!=t){baseUtils.show.redTip("两次密码输入不一致，请重新输入");return false}$.ajax("/admin/addAdminAccount?only_password=1",{type:"POST",dataType:"json",data:{password:n,phone:s,identify_code:r},success:function(e){if(e.code==0){baseUtils.show.blueTip("修改密码成功！");location.href="/accountmanage"}else{baseUtils.show.blueTip("网络错误，请稍后再试")}},error:function(e,s,n){console.log(n);baseUtils.show.redTip("网络错误，请稍后再试")}})});$(".cancelBtn").click(function(){location.href="/accountmanage"})};return e}();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2FjY291bnRTZXR0aW5nL2NoYW5nZVBhc3N3b3JkLmpzIl0sIm5hbWVzIjpbIiQiLCJjaGFuZ2VQYXNzd29yZCIsImluaXQiLCJwaG9uZSIsInRleHQiLCJwYXNzd29yZEZpcnN0IiwicGFzc3dvcmRTZWNvbmQiLCJjaGVja0NvZGUiLCJzZW5kTXNnIiwiY29kZUNvcnJlY3QiLCJjb3VudCIsInNlY29uZHMiLCJyb3VuZCIsInNldEludGVydmFsIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImNsZWFySW50ZXJ2YWwiLCJjbGljayIsInRoaXMiLCJnZXQiLCJkYXRhIiwicmV0Iiwia2V5dXAiLCJ2YWwiLCJsZW5ndGgiLCJzaG93IiwicGhvbmVJbklkZW50aWZ5IiwiY29kZSIsImhpZGUiLCIkZm9ybUNoZWNrIiwiY2hlY2tQYXNzd29yZCIsImZhZGVJbiIsImZhZGVPdXQiLCJjb25zb2xlIiwibG9nIiwiYmFzZVV0aWxzIiwicmVkVGlwIiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsInBhc3N3b3JkIiwiaWRlbnRpZnlfY29kZSIsInN1Y2Nlc3MiLCJibHVlVGlwIiwibG9jYXRpb24iLCJocmVmIiwiZXJyb3IiLCJ4aHIiLCJzdGF0dXMiLCJlcnIiXSwibWFwcGluZ3MiOiJBQUdBQSxFQUFFLFdBQ0VDLGVBQWVDLFFBR25CLElBQUlELGdCQUFlLFdBQ2YsR0FBSUEsS0FFSixJQUFJRSxHQUFRSCxFQUFFLFVBQVVJLE9BQ3BCQyxFQUNBQyxFQUNBQyxFQUNBQyxFQUFRLEVBQ1JDLEVBQVksQ0FFaEIsU0FBU0MsS0FDTCxHQUFJQyxHQUFRLEVBQ1pILEdBQVEsQ0FDUixJQUFJSSxHQUFNQyxZQUFZLFdBQ2pCLEdBQUdGLEVBQVEsRUFBRSxDQUNUWCxFQUFFLGVBQWVJLEtBQUtPLEVBQVEsSUFDOUJBLFNBQ0MsQ0FDRFgsRUFBRSxlQUFlYyxZQUFZLGtCQUM3QmQsR0FBRSxlQUFlZSxTQUFTLFVBQzFCZixHQUFFLGVBQWVJLEtBQUssT0FDdEJJ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QUFFcUMsS0FBSywwQ0FDSEMsS0FBTSxPQUNOQyxTQUFVLE9BQ1ZuQixNQUNJb0IsU0FBVW5DLEVBQ1ZGLE1BQU1BLEVBQ05zQyxjQUFjbEMsR0FFbEJtQyxRQUFTLFNBQVV0QixHQUNmLEdBQUlBLEVBQUtPLE1BQVEsRUFBRyxDQUNoQlEsVUFBVVYsS0FBS2tCLFFBQVEsVUFDdkJDLFVBQVNDLEtBQUsscUJBQ1gsQ0FDSFYsVUFBVVYsS0FBS2tCLFFBQVEsZ0JBRy9CRyxNQUFPLFNBQVVDLEVBQUtDLEVBQVFDLEdBQzFCaEIsUUFBUUMsSUFBSWUsRUFDWmQsV0FBVVYsS0FBS1csT0FBTyxrQkFLbENwQyxHQUFFLGNBQWNpQixNQUFNLFdBQ2xCMkIsU0FBU0MsS0FBSyxtQkFHdEIsT0FBTzVDIiwiZmlsZSI6ImFkbWluL2FjY291bnRTZXR0aW5nL2NoYW5nZVBhc3N3b3JkLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBDcmVhdGVkIGJ5IGpzZXJrIG9uIDIwMTcvNi8yNi5cbiAqL1xuJChmdW5jdGlvbiAoKSB7XG4gICAgY2hhbmdlUGFzc3dvcmQuaW5pdCgpO1xufSk7XG5cbnZhciBjaGFuZ2VQYXNzd29yZD0oZnVuY3Rpb24gKCkge1xuICAgIHZhciBjaGFuZ2VQYXNzd29yZD17fTtcblxuICAgIHZhciBwaG9uZSA9ICQoXCIjcGhvbmVcIikudGV4dCgpLCAvL+aJi+acuuWPt1xuICAgICAgICBwYXNzd29yZEZpcnN0LCAvL+esrOS4gOasoei+k+WFpeWvhueggVxuICAgICAgICBwYXNzd29yZFNlY29uZCwgLy/nrKzkuozmrKHovpPlhaXlr4bnoIFcbiAgICAgICAgY2hlY2tDb2RlLCAgLy/miYvmnLrpqozor4HnoIFcbiAgICAgICAgc2VuZE1zZz0xLCAgICAvL+aYr+WQpuWPr+iOt+WPlumqjOivgeeggVxuICAgICAgICBjb2RlQ29ycmVjdD0wOyAvL+mqjOivgeeggeaYr+WQpuato+ehrlxuXG4gICAgZnVuY3Rpb24gY291bnQoKSB7XG4gICAgICAgIHZhciBzZWNvbmRzPTYwO1xuICAgICAgICBzZW5kTXNnPTA7XG4gICAgICAgIHZhciByb3VuZD1zZXRJbnRlcnZhbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgaWYoc2Vjb25kcz4wKXtcbiAgICAgICAgICAgICAgICAgJChcIi5nZXRDb2RlQnRuXCIpLnRleHQoc2Vjb25kcysn56eSJyk7XG4gICAgICAgICAgICAgICAgIHNlY29uZHMtLTtcbiAgICAgICAgICAgICB9ZWxzZXtcbiAgICAgICAgICAgICAgICAgJChcIi5nZXRDb2RlQnRuXCIpLnJlbW92ZUNsYXNzKCdwcmV2ZW50Q2xpY2tCdG4nKTtcbiAgICAgICAgICAgICAgICAgJChcIi5nZXRDb2RlQnRuXCIpLmFkZENsYXNzKCdidG5CbHVlJyk7XG4gICAgICAgICAgICAgICAgICQoXCIuZ2V0Q29kZUJ0blwiKS50ZXh0KCflho3mrKHojrflj5YnKTtcbiAgICAgICAgICAgICAgICAgc2VuZE1zZz0xO1xuICAgICAgICAgICAgICAgICBjbGVhckludGVydmFsKHJvdW5kKTtcbiAgICAgICAgICAgICB9XG4gICAgICAgIH0sMTAwMClcbiAgICB9XG5cbiAgICBjaGFuZ2VQYXNzd29yZC5pbml0PWZ1bmN0aW9uICgpIHtcblxuICAgICAgICAvL+iOt+WPlumqjOivgeeggVxuICAgICAgICAkKFwiLmdldENvZGVCdG5cIikuY2xpY2soZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYoc2VuZE1zZyl7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygncHJldmVudENsaWNrQnRuJyk7XG4gICAgICAgICAgICAgICAgY291bnQoKTtcbiAgICAgICAgICAgICQuZ2V0KFwiL3NlbmRtc2dcIiwgeyBcInBob25lXCI6IHBob25lIH0sIGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgICAgICAgICBpZiAoZGF0YS5yZXQgPT0gMCkge1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pXG5cbiAgICAgICAgLy/moKHpqozpqozor4HnoIFcbiAgICAgICAgJChcIi5pZGVudGlmeUNvZGVJbnB1dFwiKS5rZXl1cChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGNoZWNrQ29kZSA9ICQoXCIuaWRlbnRpZnlDb2RlSW5wdXRcIikudmFsKCk7XG5cbiAgICAgICAgICAgIGlmIChjaGVja0NvZGUubGVuZ3RoID09IDApIHtcbiAgICAgICAgICAgICAgICAkKFwiLnN1Y2Nlc3NUaXAsLmVycm9yVGlwXCIpLnNob3coKTtcbiAgICAgICAgICAgICAgICAkKFwiLmlkZW50aWZ5Q29kZUlucHV0XCIpLnJlbW92ZUNsYXNzKCdib3JkZXJSZWQnKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICAkLmdldCgnL2lkZW50aWZ5JywgeyBcInBob25lSW5JZGVudGlmeVwiOiBwaG9uZSwgXCJjb2RlXCI6IGNoZWNrQ29kZSB9LFxuICAgICAgICAgICAgICAgIGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGRhdGEucmV0ID09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIuc3VjY2Vzc1RpcFwiKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKFwiLmVycm9yVGlwXCIpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIuaWRlbnRpZnlDb2RlSW5wdXRcIikucmVtb3ZlQ2xhc3MoJ2JvcmRlclJlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgY29kZUNvcnJlY3Q9MTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIuc3VjY2Vzc1RpcFwiKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKFwiLmVycm9yVGlwXCIpLnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIuaWRlbnRpZnlDb2RlSW5wdXRcIikuYWRkQ2xhc3MoJ2JvcmRlclJlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgY29kZUNvcnJlY3Q9MDtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcblxuICAgICAgICAvL+i+k+WFpeWvhueggVxuICAgICAgICAkKFwiLnBhc3N3b3JkSW5wdXRcIikua2V5dXAoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAvL+esrOS4gOasoei+k+WFpeWvhueggVxuICAgICAgICAgICAgcGFzc3dvcmRGaXJzdD0kKFwiLnBhc3N3b3JkSW5wdXRcIikudmFsKCk7XG4gICAgICAgICAgICAvL+WGjeasoei+k+WFpeWvhueggVxuICAgICAgICAgICAgcGFzc3dvcmRTZWNvbmQ9JChcIi5wYXNzd29yZElucHV0QWdhaW5cIikudmFsKCk7XG4gICAgICAgICAgICBpZighJGZvcm1DaGVjay5jaGVja1Bhc3N3b3JkKHBhc3N3b3JkRmlyc3QpKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdib3JkZXJSZWQnKTtcbiAgICAgICAgICAgICAgICAkKFwiLmVycm9yTXNnRmlyc3RcIikuZmFkZUluKDMwMCk7XG4gICAgICAgICAgICB9ZWxzZXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUNsYXNzKCdib3JkZXJSZWQnKTtcbiAgICAgICAgICAgICAgICAkKFwiLmVycm9yTXNnRmlyc3RcIikuZmFkZU91dCgzMDApO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYocGFzc3dvcmRTZWNvbmQubGVuZ3RoIT0wJiZwYXNzd29yZFNlY29uZCE9cGFzc3dvcmRGaXJzdCl7XG4gICAgICAgICAgICAgICAgJChcIi5wYXNzd29yZElucHV0QWdhaW5cIikuYWRkQ2xhc3MoJ2JvcmRlclJlZCcpO1xuICAgICAgICAgICAgICAgICQoXCIuZXJyb3JNc2dTZWNvbmRcIikuZmFkZUluKDMwMCk7XG4gICAgICAgICAgICB9ZWxzZXtcbiAgICAgICAgICAgICAgICAkKFwiLnBhc3N3b3JkSW5wdXRBZ2FpblwiKS5yZW1vdmVDbGFzcygnYm9yZGVyUmVkJyk7XG4gICAgICAgICAgICAgICAgJChcIi5lcnJvck1zZ1NlY29uZFwiKS5mYWRlT3V0KDMwMCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgJChcIi5wYXNzd29yZElucHV0QWdhaW5cIikua2V5dXAoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAvL+esrOS4gOasoei+k+WFpeWvhueggVxuICAgICAgICAgICAgcGFzc3dvcmRGaXJzdD0kKFwiLnBhc3N3b3JkSW5wdXRcIikudmFsKCk7XG4gICAgICAgICAgICAvL+WGjeasoei+k+WFpeWvhueggVxuICAgICAgICAgICAgcGFzc3dvcmRTZWNvbmQ9JChcIi5wYXNzd29yZElucHV0QWdhaW5cIikudmFsKCk7XG4gICAgICAgICAgICBpZihwYXNzd29yZFNlY29uZC5sZW5ndGghPTAmJnBhc3N3b3JkU2Vjb25kIT1wYXNzd29yZEZpcnN0KXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdib3JkZXJSZWQnKTtcbiAgICAgICAgICAgICAgICAkKFwiLmVycm9yTXNnU2Vjb25kXCIpLmZhZGVJbigzMDApO1xuICAgICAgICAgICAgfWVsc2V7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcygnYm9yZGVyUmVkJyk7XG4gICAgICAgICAgICAgICAgJChcIi5lcnJvck1zZ1NlY29uZFwiKS5mYWRlT3V0KDMwMCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIC8vICAgIOS/neWtmFxuICAgICAgICAkKFwiLmNvbmZpcm1CdG5cIikuY2xpY2soZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgcGhvbmUgPSAkKFwiI3Bob25lXCIpLnRleHQoKTtcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKHBob25lKTtcbiAgICAgICAgICAgIC8v6aqM6K+B56CBXG4gICAgICAgICAgICBjaGVja0NvZGUgPSAkKFwiLmlkZW50aWZ5Q29kZUlucHV0XCIpLnZhbCgpO1xuICAgICAgICAgICAgLy/nrKzkuIDmrKHovpPlhaXlr4bnoIFcbiAgICAgICAgICAgIHBhc3N3b3JkRmlyc3QgPSAkKFwiLnBhc3N3b3JkSW5wdXRcIikudmFsKCk7XG4gICAgICAgICAgICAvL+esrOS6jOasoei+k+WFpeWvhueggVxuICAgICAgICAgICAgcGFzc3dvcmRTZWNvbmQgPSAkKFwiLnBhc3N3b3JkSW5wdXRBZ2FpblwiKS52YWwoKTtcblxuICAgICAgICAgICAgaWYgKGNoZWNrQ29kZSA9PSAnJykge1xuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn6K+36L6T5YWl6aqM6K+B56CBJyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKGNvZGVDb3JyZWN0ID09IDApIHtcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoJ+mqjOivgeeggemUmeivr++8jOivt+mHjeaWsOi+k+WFpScpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChjb2RlQ29ycmVjdCA9PSAwKSB7XG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKCfpqozor4HnoIHplJnor6/vvIzor7fph43mlrDovpPlhaUnKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAocGFzc3dvcmRGaXJzdC5sZW5ndGggPT0gMCB8fCBwYXNzd29yZFNlY29uZC5sZW5ndGggPT0gMCkge1xuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5a+G56CB5LiN6IO95Li656m677yM6K+36L6T5YWl5a+G56CBJyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAocGFzc3dvcmRGaXJzdC5sZW5ndGggPCA2fHxwYXNzd29yZEZpcnN0Lmxlbmd0aCA+IDE2KSB7XG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKCflr4bnoIHplb/luqblv4XpobvlnKg25L2N5YiwMTbkvY3kuYvpl7Tlk6Z+Jyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZighJGZvcm1DaGVjay5jaGVja1Bhc3N3b3JkKXtcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoJ+WvhueggeagvOW8j+S4jeato+ehru+8jOivt+mHjeaWsOi+k+WFpX4nKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAocGFzc3dvcmRGaXJzdCAhPSBwYXNzd29yZFNlY29uZCkge1xuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5Lik5qyh5a+G56CB6L6T5YWl5LiN5LiA6Ie077yM6K+36YeN5paw6L6T5YWlJyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJC5hamF4KCcvYWRtaW4vYWRkQWRtaW5BY2NvdW50P29ubHlfcGFzc3dvcmQ9MScsIHtcbiAgICAgICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgICAgIHBhc3N3b3JkOiBwYXNzd29yZEZpcnN0LFxuICAgICAgICAgICAgICAgICAgICBwaG9uZTpwaG9uZSxcbiAgICAgICAgICAgICAgICAgICAgaWRlbnRpZnlfY29kZTpjaGVja0NvZGVcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChkYXRhKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkYXRhLmNvZGUgPT0gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcCgn5L+u5pS55a+G56CB5oiQ5Yqf77yBJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmPScvYWNjb3VudG1hbmFnZSc7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKCfnvZHnu5zplJnor6/vvIzor7fnqI3lkI7lho3or5UnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uICh4aHIsIHN0YXR1cywgZXJyKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGVycik7XG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIue9kee7nOmUmeivr++8jOivt+eojeWQjuWGjeivlVwiKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KVxuICAgICAgICB9KVxuICAgIC8vICAgIOWPlua2iFxuICAgICAgICAkKFwiLmNhbmNlbEJ0blwiKS5jbGljayhmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBsb2NhdGlvbi5ocmVmPScvYWNjb3VudG1hbmFnZSc7XG4gICAgICAgIH0pXG4gICAgfTtcbiAgICByZXR1cm4gY2hhbmdlUGFzc3dvcmQ7XG59KSgpOyJdfQ==