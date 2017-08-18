$(document).ready(function(){manageQA.init()});var manageQA=function(){var e={};var i=false;e.init=function(){$("#uploadImage").on("change",function(){s(this.files,"image",5)});$("#createQA").click(function(){if(i){baseUtils.show.redTip("正在提交中，请稍后再试");return false}var e=$.trim($("#QA_title").val()),s=$.trim($("#QA_summary").val()),t=$.trim($("#imgUrl").val()),n=$.trim($("#eavesdropPrice").val()),r=$.trim($("#sharerTrader").val()),a=$.trim($("#sharerResponder").val()),l=$.trim($("#sharerAskPerson").val()),o=$("input[name='isQAShow']:checked").val();if(e.length==0){baseUtils.show.redTip("问答区名称不能为空");return false}if(s.length==0){baseUtils.show.redTip("问答区简介不能为空");return false}if(t.length==0){baseUtils.show.redTip("问答封面不能为空");return false}if(n.length==0){baseUtils.show.redTip("偷听价格不能为空");return false}if(n<.1){baseUtils.show.redTip("偷听价格不能低于0.1元");return false}if(n>1e4){baseUtils.show.redTip("价格超出上限，偷听价格不可设置高于10000元");return false}if(r.length==0||a.length==0||l.length==0){baseUtils.show.redTip("偷听分成比例不能为空");return false}var d=+r+ +a+ +l;if(d!=100){baseUtils.show.redTip("商家、答主、提问者三者分成总和必须等于100%");return false}var f={title:e,desc:s,img_url:t,price:n*100,state:o,listen_for_business:r,listen_for_answer:a,listen_for_questioner:l};if($("#page_type").val()==1){f.id=GetQueryString("id")}i=true;$.ajax("saveQuestionAndAnswer",{type:"POST",dataType:"json",data:f,success:function(e){if(e.code==0){baseUtils.show.blueTip("保存成功！");window.location.href="/QA/questionAndAnswerDetail"}else{i=false;baseUtils.show.redTip("保存失败，请稍后再试！")}},error:function(e,s,t){i=false;console.log(t);baseUtils.show.redTip("网络错误，请稍后再试！")}})})};function s(e,i,s){console.log(e);if(e&&e.length>0){var r=e[0],a=t(r);var l=r.name;var o,d;d=l.lastIndexOf(".");if(d!=-1){o=l.substr(d+1).toUpperCase();o=o.toLowerCase();if(o!="jpg"&&o!="png"&&o!="jpeg"&&o!="gif"){baseUtils.show.blueTip("请上传图片类型的文件哦~");return}}else{document.all.submit_upload.disabled=true;baseUtils.show.blueTip("请上传图片类型的文件哦~");return}if($uploadFile.checkFileSize(r,s)){$uploadFile.uploadRes(r,i,function(e){},function(e){console.log(e);baseUtils.show.blueTip("上传成功！");var s=e.data.access_url;console.log(s);$("#imgUrl").val(s);if(i=="image"){$("#previewCoverImg").load(function(){n(a)}).attr("src",a)}},function(e){console.error("上传失败!!!");console.log(e);baseUtils.show.redTip("上传失败！")})}else{baseUtils.show.redTip("上传资源限制在"+s+"MB内！");$("#upLoadImage").val("")}}else{baseUtils.show.redTip("网络错误，请稍后再试！")}}function t(e){var i=null;if(window.createObjectURL!=undefined){i=window.createObjectURL(e)}else if(window.URL!=undefined){i=window.URL.createObjectURL(e)}else if(window.webkitURL!=undefined){i=window.webkitURL.createObjectURL(e)}return i}function n(e){if(window.revokeObjectURL!=undefined){window.revokeObjectURL(e)}else if(window.URL!=undefined){window.URL.revokeObjectURL(e)}else if(window.webkitURL!=undefined){window.webkitURL.revokeObjectURL(e)}}return e}();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1hbmFnZVF1ZXN0aW9uQW5kQW5zd2VyLmpzIl0sIm5hbWVzIjpbIiQiLCJkb2N1bWVudCIsInJlYWR5IiwibWFuYWdlUUEiLCJpbml0Iiwic3VibWl0TGltaXQiLCJvbiIsInJlc1VwbG9hZCIsInRoaXMiLCJmaWxlcyIsImNsaWNrIiwiYmFzZVV0aWxzIiwic2hvdyIsInJlZFRpcCIsIlFBX3RpdGxlIiwidHJpbSIsInZhbCIsIlFBX3N1bW1hcnkiLCJRQV9jb3Zlcl9pbWciLCJRQV9lYXZlc2Ryb3BfcHJpY2UiLCJzaGFyZXJUcmFkZXIiLCJzaGFyZXJSZXNwb25kZXIiLCJzaGFyZXJBc2tQZXJzb24iLCJpc1FBU2hvdyIsImxlbmd0aCIsInN1bSIsImRhdGEiLCJ0aXRsZSIsImRlc2MiLCJpbWdfdXJsIiwicHJpY2UiLCJzdGF0ZSIsImxpc3Rlbl9mb3JfYnVzaW5lc3MiLCJsaXN0ZW5fZm9yX2Fuc3dlciIsImxpc3Rlbl9mb3JfcXVlc3Rpb25lciIsImlkIiwiR2V0UXVlcnlTdHJpbmciLCJhamF4IiwidHlwZSIsImRhdGFUeXBlIiwic3VjY2VzcyIsImNvZGUiLCJibHVlVGlwIiwid2luZG93IiwibG9jYXRpb24iLCJocmVmIiwiZXJyb3IiLCJ4aHIiLCJzdGF0dXMiLCJlcnIiLCJjb25zb2xlIiwibG9nIiwicmVzVHlwZSIsInJlc0xpbWl0U2l6ZSIsImZpbGUiLCJyZXNvdXJjZUxvY2FsVXJsIiwiZ2V0T2JqZWN0VVJMIiwiaW1nTmFtZSIsIm5hbWUiLCJleHQiLCJpZHgiLCJsYXN0SW5kZXhPZiIsInN1YnN0ciIsInRvVXBwZXJDYXNlIiwidG9Mb3dlckNhc2UiLCJhbGwiLCJzdWJtaXRfdXBsb2FkIiwiZGlzYWJsZWQiLCIkdXBsb2FkRmlsZSIsImNoZWNrRmlsZVNpemUiLCJ1cGxvYWRSZXMiLCJyZXNVcmwiLCJhY2Nlc3NfdXJsIiwibG9hZCIsInJlbW92ZU9iamVjdFVSTCIsImF0dHIiLCJ1cmwiLCJjcmVhdGVPYmplY3RVUkwiLCJ1bmRl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dENGLEVBQU1qQyxPQUFPcUMsVUFBVUgsZ0JBQWdCdkIsR0FFM0MsTUFBT3NCLEdBR1gsUUFBU0YsR0FBZ0JFLEdBQ3JCLEdBQUlqQyxPQUFPc0MsaUJBQW1CSCxVQUFXLENBQ3JDbkMsT0FBT3NDLGdCQUFnQkwsT0FDcEIsSUFBSWpDLE9BQU9vQyxLQUFPRCxVQUFXLENBQ2hDbkMsT0FBT29DLElBQUlFLGdCQUFnQkwsT0FDeEIsSUFBSWpDLE9BQU9xQyxXQUFhRixVQUFXLENBQ3RDbkMsT0FBT3FDLFVBQVVDLGdCQUFnQkwsSUFJekMsTUFBT3pFIiwiZmlsZSI6Im1hbmFnZVF1ZXN0aW9uQW5kQW5zd2VyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiXHJcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuICAgIG1hbmFnZVFBLmluaXQoKTtcclxufSk7XHJcblxyXG5cclxudmFyIG1hbmFnZVFBID0gZnVuY3Rpb24gKCkge1xyXG5cclxuICAgIHZhciBtYW5hZ2VRQSA9IHt9O1xyXG5cclxuICAgIHZhciBzdWJtaXRMaW1pdCA9IGZhbHNlOyAgICAgICAgLy/mj5DkuqTpmZDliLZcclxuXHJcbiAgICBtYW5hZ2VRQS5pbml0ID0gZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICAkKFwiI3VwbG9hZEltYWdlXCIpLm9uKFwiY2hhbmdlXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcmVzVXBsb2FkKHRoaXMuZmlsZXMsIFwiaW1hZ2VcIiwgNSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG5cclxuICAgICAgICAkKFwiI2NyZWF0ZVFBXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuXHJcbiAgICAgICAgICAgIGlmIChzdWJtaXRMaW1pdCkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5q2j5Zyo5o+Q5Lqk5Lit77yM6K+356iN5ZCO5YaN6K+VXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB2YXIgUUFfdGl0bGUgPSAkLnRyaW0oJChcIiNRQV90aXRsZVwiKS52YWwoKSksXHJcbiAgICAgICAgICAgICAgICBRQV9zdW1tYXJ5ID0gJC50cmltKCQoXCIjUUFfc3VtbWFyeVwiKS52YWwoKSksXHJcbiAgICAgICAgICAgICAgICBRQV9jb3Zlcl9pbWcgPSAkLnRyaW0oJChcIiNpbWdVcmxcIikudmFsKCkpLFxyXG4gICAgICAgICAgICAgICAgUUFfZWF2ZXNkcm9wX3ByaWNlID0gJC50cmltKCQoXCIjZWF2ZXNkcm9wUHJpY2VcIikudmFsKCkpLFxyXG5cclxuICAgICAgICAgICAgICAgIHNoYXJlclRyYWRlciA9ICQudHJpbSgkKFwiI3NoYXJlclRyYWRlclwiKS52YWwoKSksXHJcbiAgICAgICAgICAgICAgICBzaGFyZXJSZXNwb25kZXIgPSAkLnRyaW0oJChcIiNzaGFyZXJSZXNwb25kZXJcIikudmFsKCkpLFxyXG4gICAgICAgICAgICAgICAgc2hhcmVyQXNrUGVyc29uID0gJC50cmltKCQoXCIjc2hhcmVyQXNrUGVyc29uXCIpLnZhbCgpKSxcclxuXHJcbiAgICAgICAgICAgICAgICBpc1FBU2hvdyA9ICQoXCJpbnB1dFtuYW1lPSdpc1FBU2hvdyddOmNoZWNrZWRcIikudmFsKCk7XHJcblxyXG4gICAgICAgICAgICBpZiAoUUFfdGl0bGUubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIumXruetlOWMuuWQjeensOS4jeiDveS4uuepulwiKTtcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZiAoUUFfc3VtbWFyeS5sZW5ndGggPT0gMCkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi6Zeu562U5Yy6566A5LuL5LiN6IO95Li656m6XCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmIChRQV9jb3Zlcl9pbWcubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIumXruetlOWwgemdouS4jeiDveS4uuepulwiKTtcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZiAoUUFfZWF2ZXNkcm9wX3ByaWNlLmxlbmd0aCA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLlgbflkKzku7fmoLzkuI3og73kuLrnqbpcIik7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYgKFFBX2VhdmVzZHJvcF9wcmljZSA8IDAuMSkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5YG35ZCs5Lu35qC85LiN6IO95L2O5LqOMC4x5YWDXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmIChRQV9lYXZlc2Ryb3BfcHJpY2UgPiAxMDAwMCkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5Lu35qC86LaF5Ye65LiK6ZmQ77yM5YG35ZCs5Lu35qC85LiN5Y+v6K6+572u6auY5LqOMTAwMDDlhYNcIik7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGlmIChzaGFyZXJUcmFkZXIubGVuZ3RoID09IDAgfHwgc2hhcmVyUmVzcG9uZGVyLmxlbmd0aCA9PSAwIHx8IHNoYXJlckFza1BlcnNvbi5sZW5ndGggPT0gMCkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5YG35ZCs5YiG5oiQ5q+U5L6L5LiN6IO95Li656m6XCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB2YXIgc3VtID0gK3NoYXJlclRyYWRlciArICtzaGFyZXJSZXNwb25kZXIgKyArc2hhcmVyQXNrUGVyc29uO1xyXG4gICAgICAgICAgICBpZiAoc3VtICE9IDEwMCkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5ZWG5a6244CB562U5Li744CB5o+Q6Zeu6ICF5LiJ6ICF5YiG5oiQ5oC75ZKM5b+F6aG7562J5LqOMTAwJVwiKTtcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgdmFyIGRhdGEgPSB7XHJcbiAgICAgICAgICAgICAgICB0aXRsZTpRQV90aXRsZSxcclxuICAgICAgICAgICAgICAgIGRlc2M6UUFfc3VtbWFyeSxcclxuICAgICAgICAgICAgICAgIGltZ191cmw6UUFfY292ZXJfaW1nLFxyXG4gICAgICAgICAgICAgICAgcHJpY2U6UUFfZWF2ZXNkcm9wX3ByaWNlICogMTAwLFxyXG4gICAgICAgICAgICAgICAgc3RhdGU6aXNRQVNob3csXHJcbiAgICAgICAgICAgICAgICBsaXN0ZW5fZm9yX2J1c2luZXNzOnNoYXJlclRyYWRlcixcclxuICAgICAgICAgICAgICAgIGxpc3Rlbl9mb3JfYW5zd2VyOnNoYXJlclJlc3BvbmRlcixcclxuICAgICAgICAgICAgICAgIGxpc3Rlbl9mb3JfcXVlc3Rpb25lcjpzaGFyZXJBc2tQZXJzb25cclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgaWYoJCgnI3BhZ2VfdHlwZScpLnZhbCgpID09IDEpIHsgICAgLy8g6K+05piO5piv57yW6L6RXHJcbiAgICAgICAgICAgICAgICBkYXRhLmlkID0gR2V0UXVlcnlTdHJpbmcoXCJpZFwiKTtcclxuXHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHN1Ym1pdExpbWl0ID0gdHJ1ZTtcclxuICAgICAgICAgICAgJC5hamF4KFwic2F2ZVF1ZXN0aW9uQW5kQW5zd2VyXCIsIHtcclxuICAgICAgICAgICAgICAgIHR5cGU6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxyXG4gICAgICAgICAgICAgICAgZGF0YTogZGF0YSxcclxuICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKGRhdGEuY29kZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLkv53lrZjmiJDlip/vvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvUUEvcXVlc3Rpb25BbmRBbnN3ZXJEZXRhaWxcIjtcclxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBzdWJtaXRMaW1pdCA9IGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkv53lrZjlpLHotKXvvIzor7fnqI3lkI7lho3or5XvvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbiAoeGhyLCBzdGF0dXMsIGVycikge1xyXG4gICAgICAgICAgICAgICAgICAgIHN1Ym1pdExpbWl0ID0gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coZXJyKTtcclxuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLnvZHnu5zplJnor6/vvIzor7fnqI3lkI7lho3or5XvvIFcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgfTtcclxuXHJcbiAgICAvKipcclxuICAgICAqIOi1hOa6kOS4iuS8oOWHveaVsCjlj4LmlbDvvJpyZXNUeXBlOui1hOa6kOexu+WeiyxyZXNUeXBlQ2xhc3M66LWE5rqQ57G75Z6L5Lit57uG5YiG55qE56eN57G7LHJlc0xpbWl0U2l6ZTrotYTmupDpmZDliLblpKflsI8pXHJcbiAgICAgKi9cclxuICAgIGZ1bmN0aW9uIHJlc1VwbG9hZChmaWxlcywgcmVzVHlwZSwgcmVzTGltaXRTaXplKSB7XHJcbiAgICAgICAgY29uc29sZS5sb2coZmlsZXMpO1xyXG4gICAgICAgIGlmIChmaWxlcyAmJiBmaWxlcy5sZW5ndGggPiAwKSB7XHJcbiAgICAgICAgICAgIHZhciBmaWxlID0gZmlsZXNbMF0sXHJcbiAgICAgICAgICAgICAgICByZXNvdXJjZUxvY2FsVXJsID0gZ2V0T2JqZWN0VVJMKGZpbGUpO1xyXG4gICAgICAgICAgICB2YXIgaW1nTmFtZSA9IGZpbGUubmFtZTtcclxuICAgICAgICAgICAgLy9hbGVydChpbWdOYW1lKTtcclxuICAgICAgICAgICAgdmFyIGV4dCxpZHg7XHJcbiAgICAgICAgICAgIGlkeCA9IGltZ05hbWUubGFzdEluZGV4T2YoXCIuXCIpO1xyXG4gICAgICAgICAgICBpZiAoaWR4ICE9IC0xKXtcclxuICAgICAgICAgICAgICAgIGV4dCA9IGltZ05hbWUuc3Vic3RyKGlkeCsxKS50b1VwcGVyQ2FzZSgpO1xyXG4gICAgICAgICAgICAgICAgZXh0ID0gZXh0LnRvTG93ZXJDYXNlKCApO1xyXG4gICAgICAgICAgICAgICAgLy9hbGVydChmaWxlKTtcclxuICAgICAgICAgICAgICAgIC8vYWxlcnQoXCLlkI7nvIA9XCIrZXh0K1wi5L2N572uPVwiK2lkeCtcIui3r+W+hD1cIityZXNvdXJjZUxvY2FsVXJsKTtcclxuICAgICAgICAgICAgICAgIGlmIChleHQgIT0gJ2pwZycgJiYgZXh0ICE9ICdwbmcnICYmIGV4dCAhPSAnanBlZycgJiYgZXh0ICE9ICdnaWYnKXtcclxuICAgICAgICAgICAgICAgICAgICAvL2RvY3VtZW50LmFsbC5zdWJtaXRfdXBsb2FkLmRpc2FibGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChcIuivt+S4iuS8oOWbvueJh+exu+Wei+eahOaWh+S7tuWTpn5cIik7XHJcbiAgICAgICAgICAgICAgICAgICAgLy9hbGVydChcIjIu5Y+q6IO95LiK5LygLmpwZyAgLnBuZyAgLmpwZWcgIC5naWbnsbvlnovnmoTmlofku7YhXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIGRvY3VtZW50LmFsbC5zdWJtaXRfdXBsb2FkLmRpc2FibGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi6K+35LiK5Lyg5Zu+54mH57G75Z6L55qE5paH5Lu25ZOmflwiKTtcclxuICAgICAgICAgICAgICAgIC8vYWxlcnQoXCLlj6rog73kuIrkvKAuanBnICAucG5nICAuanBlZyAgLmdpZuexu+Wei+eahOaWh+S7tiFcIik7XHJcbiAgICAgICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIC8vIOmZkOWItui1hOa6kOWcqCpNQuWGhVxyXG4gICAgICAgICAgICBpZiAoJHVwbG9hZEZpbGUuY2hlY2tGaWxlU2l6ZShmaWxlLCByZXNMaW1pdFNpemUpKSB7XHJcbiAgICAgICAgICAgICAgICAkdXBsb2FkRmlsZS51cGxvYWRSZXMoZmlsZSwgcmVzVHlwZSwgZnVuY3Rpb24gKGRhdGEpIHtcclxuICAgICAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgICAgIC8vIOS4iuS8oOaIkOWKn+Wbnuiwg1xyXG4gICAgICAgICAgICAgICAgICAgIGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGRhdGEpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi5LiK5Lyg5oiQ5Yqf77yBXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgcmVzVXJsID0gZGF0YS5kYXRhLmFjY2Vzc191cmw7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKHJlc1VybCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIjaW1nVXJsXCIpLnZhbChyZXNVcmwpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyDlpoLmnpzmmK/lm77niYfotYTmupDvvIzliJnlsZXnpLrlm77niYfpooTop4hcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlc1R5cGUgPT0gJ2ltYWdlJykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy/nm7TmjqXliqDovb3mnKzlnLDlm77niYfov5vooYzpooTop4hcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoXCIjcHJldmlld0NvdmVySW1nXCIpXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLmxvYWQoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZW1vdmVPYmplY3RVUkwocmVzb3VyY2VMb2NhbFVybCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cihcInNyY1wiLCByZXNvdXJjZUxvY2FsVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgICAgIC8vIOS4iuS8oOWksei0peWbnuiwg1xyXG4gICAgICAgICAgICAgICAgICAgIGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoXCLkuIrkvKDlpLHotKUhISFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGRhdGEpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkuIrkvKDlpLHotKXvvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkuIrkvKDotYTmupDpmZDliLblnKhcIiArIHJlc0xpbWl0U2l6ZSArIFwiTULlhoXvvIFcIik7XHJcbiAgICAgICAgICAgICAgICAkKFwiI3VwTG9hZEltYWdlXCIpLnZhbChcIlwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIue9kee7nOmUmeivr++8jOivt+eojeWQjuWGjeivle+8gVwiKTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIGZ1bmN0aW9uIGdldE9iamVjdFVSTChmaWxlKSB7XHJcbiAgICAgICAgdmFyIHVybCA9IG51bGw7XHJcbiAgICAgICAgaWYgKHdpbmRvdy5jcmVhdGVPYmplY3RVUkwgIT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgICAgIHVybCA9IHdpbmRvdy5jcmVhdGVPYmplY3RVUkwoZmlsZSk7XHJcbiAgICAgICAgfSBlbHNlIGlmICh3aW5kb3cuVVJMICE9IHVuZGVmaW5lZCkge1xyXG4gICAgICAgICAgICB1cmwgPSB3aW5kb3cuVVJMLmNyZWF0ZU9iamVjdFVSTChmaWxlKTtcclxuICAgICAgICB9IGVsc2UgaWYgKHdpbmRvdy53ZWJraXRVUkwgIT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgICAgIHVybCA9IHdpbmRvdy53ZWJraXRVUkwuY3JlYXRlT2JqZWN0VVJMKGZpbGUpO1xyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gdXJsO1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiByZW1vdmVPYmplY3RVUkwodXJsKSB7Ly/ph4rmlL7otYTmupBVUkxcclxuICAgICAgICBpZiAod2luZG93LnJldm9rZU9iamVjdFVSTCAhPSB1bmRlZmluZWQpIHtcclxuICAgICAgICAgICAgd2luZG93LnJldm9rZU9iamVjdFVSTCh1cmwpO1xyXG4gICAgICAgIH0gZWxzZSBpZiAod2luZG93LlVSTCAhPSB1bmRlZmluZWQpIHtcclxuICAgICAgICAgICAgd2luZG93LlVSTC5yZXZva2VPYmplY3RVUkwodXJsKTtcclxuICAgICAgICB9IGVsc2UgaWYgKHdpbmRvdy53ZWJraXRVUkwgIT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgICAgIHdpbmRvdy53ZWJraXRVUkwucmV2b2tlT2JqZWN0VVJMKHVybCk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiBtYW5hZ2VRQTtcclxufSgpOyJdfQ==