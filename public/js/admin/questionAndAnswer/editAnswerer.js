var newaAvatar="";function getObjectURL(e){var n=null;if(window.createObjectURL!=undefined){n=window.createObjectURL(e)}else if(window.URL!=undefined){n=window.URL.createObjectURL(e)}else if(window.webkitURL!=undefined){n=window.webkitURL.createObjectURL(e)}return n}function removeObjectURL(e){if(window.revokeObjectURL!=undefined){window.revokeObjectURL(e)}else if(window.URL!=undefined){window.URL.revokeObjectURL(e)}else if(window.webkitURL!=undefined){window.webkitURL.revokeObjectURL(e)}}function resUpload(e,n,i){console.log(e);if(e&&e.length>0){var r=e[0],s=getObjectURL(r);if($uploadFile.checkFileSize(r,i)){$uploadFile.uploadRes(r,n,function(e){},function(e){console.log(e);baseUtils.show.blueTip("上传成功！");var i=e.data.access_url;newaAvatar=i;console.log(i);$("#imgUrl").val(i);if(n=="image"){var t=r.name;var a,o;o=t.lastIndexOf(".");if(o!=-1){a=t.substr(o+1).toUpperCase();a=a.toLowerCase();if(a!="jpg"&&a!="png"&&a!="jpeg"&&a!="gif"){baseUtils.show.blueTip("请上传图片类型的文件哦~");return}}else{document.all.submit_upload.disabled=true;baseUtils.show.blueTip("请上传图片类型的文件哦~");return}$(".avatarIcon").load(function(){removeObjectURL(s)}).attr("src",s)}},function(e){console.error("上传失败!!!");console.log(e);baseUtils.show.redTip("上传失败！")})}else{baseUtils.show.redTip("上传资源限制在"+i+"MB内！");$("#upLoadImage").val("")}}else{baseUtils.show.redTip("网络错误，请稍后再试！")}}$(function(){var e=false;$("#uploadImage").on("change",function(){resUpload(this.files,"image",5)});$("#saveAnswererInfo").click(function(){if(e){console.log("正在提交中，不能重复提交");return false}var r=$.trim($("#responderName").val()),s=$.trim($("#responderPhone").val()),t=$.trim($("#responderPosition").val()),a=$.trim($("#responderSummary").val()),o=$.trim($("#responderPrice").val()),l=$.trim($("#sharerTrader").val()),d=$.trim($("#sharerResponder").val());if(r.length==0){baseUtils.show.redTip("答主姓名不能为空");return false}if(s.length==0){baseUtils.show.redTip("答主手机号码不能为空");return false}if(t.length==0){baseUtils.show.redTip("答主职位/头衔不能为空");return false}if(a.length==0){baseUtils.show.redTip("答主简介不能为空");return false}if(i(a)>128){baseUtils.show.redTip("答主简介输入字符长度不能超过128字符（1个中文按两个字符计算）");return false}if(o.length==0){baseUtils.show.redTip("提问价格不能为空");return false}if(o>1e3){baseUtils.show.redTip("价格不能大于 "+1e4+" 元");return false}if(l.length==0||d.length==0){baseUtils.show.redTip("提问分成不能为空");return false}var w=+l+ +d;if(w!=100){baseUtils.show.redTip("商家、答主，二者分成总和必须等于100%");return false}var f={answerer_id:GetQueryString("answerer_id"),answerer_name:r,phone:s,position:t,summary:a,price:o*100,profit_business:l,profit_answer:d};if(newaAvatar){f.answerer_avatar=newaAvatar}showLoading();$.ajax("/QA/saveAnswerer",{type:"POST",dataType:"json",data:f,success:function(e){var i=Date.parse(new Date);hideLoading();if(e.code==0){var r=GetQueryString("state");if(r==1){$.alert("已保存，是否上线答主？","info",{btn:3,oktext:"立即上线",canceltext:"暂不上线",onCancel:function(){window.location.href="/QA/questionAndAnswerDetail?page_type=1&set=answerer"},onClose:function(){window.location.href="/QA/questionAndAnswerDetail?page_type=1&set=answerer"},onOk:function(){n()}})}else{baseUtils.show.blueTip("保存成功");window.location.href="/QA/questionAndAnswerDetail?page_type=1&set=answerer"}}else{console.log(e);baseUtils.show.redTip("网络错误，请稍后再试！")}},error:function(e,n,i){hideLoading();console.log(i);baseUtils.show.redTip("网络错误，请稍后再试！")}})});function n(){$.ajax("/QA/changeAnswererState",{type:"POST",dataType:"json",data:{answerer_id:GetQueryString("answerer_id"),state:0},success:function(e){if(e.code==0){baseUtils.show.blueTip("答主上线成功");window.location.href="/QA/questionAndAnswerDetail?page_type=1"}else{baseUtils.show.redTip("网络错误，请稍后再试！");return false}},error:function(e,n,i){console.log(i);baseUtils.show.redTip("网络错误，请稍后再试！")}})}function i(e){var n=e.replace(/[^\x00-\xff]/g,"**").length;return n}});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVkaXRBbnN3ZXJlci5qcyJdLCJuYW1lcyI6WyJuZXdhQXZhdGFyIiwiZ2V0T2JqZWN0VVJMIiwiZmlsZSIsInVybCIsIndpbmRvdyIsImNyZWF0ZU9iamVjdFVSTCIsInVuZGVmaW5lZCIsIlVSTCIsIndlYmtpdFVSTCIsInJlbW92ZU9iamVjdFVSTCIsInJldm9rZU9iamVjdFVSTCIsInJlc1VwbG9hZCIsImZpbGVzIiwicmVzVHlwZSIsInJlc0xpbWl0U2l6ZSIsImNvbnNvbGUiLCJsb2ciLCJsZW5ndGgiLCJyZXNvdXJjZUxvY2FsVXJsIiwiJHVwbG9hZEZpbGUiLCJjaGVja0ZpbGVTaXplIiwidXBsb2FkUmVzIiwiZGF0YSIsImJhc2VVdGlscyIsInNob3ciLCJibHVlVGlwIiwicmVzVXJsIiwiYWNjZXNzX3VybCIsIiQiLCJ2YWwiLCJpbWdOYW1lIiwibmFtZSIsImV4dCIsImlkeCIsImxhc3RJbmRleE9mIiwic3Vic3RyIiwidG9VcHBlckNhc2UiLCJ0b0xvd2VyQ2FzZSIsImRvY3VtZW50IiwiYWxsIiwic3VibWl0X3VwbG9hZCIsImRpc2FibGVkIiwibG9hZCIsImF0dHIiLCJlcnJvciIsInJlZFRpcCIsInN1Ym1pdExpbWl0Iiwib24iLCJ0aGlzIiwiY2xpY2siLCJ0cmltIiwicGhvbmVOdW0iLCJwb3NpdGlvbiIsInN1bW1hcnkiLCJwcmljZSIsInNoYXJlclRyYWRlciIsInNoYXJlclJlc3BvbmRlciIsImNoRW5Xb3JkQ291bnQiLCJzdW0iLCJwYXJhbXMiLCJhbnN3ZXJlcl9pZCIsIkdldFF1ZXJ5U3RyaW5nIiwiYW5zd2VyZXJfbmFtZSIsInBob25lIiwicHJvZml0X2J1c2luZXNzIiwicHJvZml0X2Fuc3dlciIsImFuc3dlcmVyX2F2YXRhciIsInNob3dMb2FkaW5nIiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsInN1Y2Nlc3MiLCJ0aW1lc3RhbXAiLCJEYXRlIiwicGFyc2UiLCJoaWRlTG9hZGluZyIsImNvZGUiLCJzdGF0ZSIsImFsZXJ0IiwiYnRuIiwib2t0ZXh0IiwiY2FuY2VsdGV4dCIsIm9uQ2FuY2VsIiwibG9jYXRpb24iLCJocmVmIiwib25DbG9zZSIsIm9uT2siLCJzaG93QW5zd2VyZXIiLCJ4aHIiLCJzdGF0dXMiLCJlcnIiLCJzdHIiLCJjb3VudCIsInJlcGxhY2UiXSwibWFwcGluZ3MiOiJBQUlBLEdBQUlBLFlBQWEsRUFDakIsU0FBU0MsY0FBYUMsR0FDbEIsR0FBSUMsR0FBTSxJQUNWLElBQUlDLE9BQU9DLGlCQUFtQkMsVUFBVyxDQUNyQ0gsRUFBTUMsT0FBT0MsZ0JBQWdCSCxPQUMxQixJQUFJRSxPQUFPRyxLQUFPRCxVQUFXLENBQ2hDSCxFQUFNQyxPQUFPRyxJQUFJRixnQkFBZ0JILE9BQzlCLElBQUlFLE9BQU9JLFdBQWFGLFVBQVcsQ0FDdENILEVBQU1DLE9BQU9JLFVBQVVILGdCQUFnQkgsR0FFM0MsTUFBT0MsR0FHWCxRQUFTTSxpQkFBZ0JOLEdBQ3JCLEdBQUlDLE9BQU9NLGlCQUFtQkosVUFBVyxDQUNyQ0YsT0FBT00sZ0JBQWdCUCxPQUNwQixJQUFJQyxPQUFPRyxLQUFPRCxVQUFXLENBQ2hDRixPQUFPRyxJQUFJRyxnQkFBZ0JQLE9BQ3hCLElBQUlDLE9BQU9JLFdBQWFGLFVBQVcsQ0FDdENGLE9BQU9JLFVBQVVFLGdCQUFnQlAsSUFPekMsUUFBU1EsV0FBVUMsRUFBT0MsRUFBU0MsR0FDL0JDLFFBQVFDLElBQUlKLEVBQ1osSUFBSUEsR0FBU0EsRUFBTUssT0FBUyxFQUFHLENBQzNCLEdBQUlmLEdBQU9VLEVBQU0sR0FDYk0sRUFBbUJqQixhQUFhQyxFQUVwQyxJQUFJaUIsWUFBWUMsY0FBY2xCLEVBQU1ZLEdBQWUsQ0FDL0NLLFlBQVlFLFVBQVVuQixFQUFNVyxFQUFTLFNBQVVTLEtBRzNDLFNBQVVBLEdBQ05QLFFBQVFDLElBQUlNLEVBQ1pDLFdBQVVDLEtBQUtDLFFBQVEsUUFDdkIsSUFBSUMsR0FBU0osRUFBS0EsS0FBS0ssVUFDdkIzQixZQUFhMEIsQ0FDYlgsU0FBUUMsSUFBSVUsRUFDWkUsR0FBRSxXQUFXQyxJQUFJSCxFQUVqQixJQUFJYixHQUFXLFFBQVMsQ0FFcEIsR0FBSWlCLEdBQVU1QixFQUFLNkIsSUFFbkIsSUFBSUMsR0FBSUMsQ0FDUkEsR0FBTUgsRUFBUUksWUFBWSxJQUMxQixJQUFJRCxJQUFRLEVBQUUsQ0FDVkQsRUFBTUYsRUFBUUssT0FBT0YsRUFBSSxHQUFHRyxhQUM1QkosR0FBTUEsRUFBSUssYUFHVixJQUFJTCxHQUFPLE9BQVNBLEdBQU8sT0FBU0EsR0FBTyxRQUFVQSxHQUFPLE1BQU0sQ0FFOURULFVBQVVDLEtBQUtDLFFBQVEsZUFFdkIsYUFFRCxDQUNIYSxTQUFTQyxJQUFJQyxjQUFjQyxTQUFTLElBQ3BDbEIsV0FBVUMsS0FBS0MsUUFBUSxlQUV2QixRQUlKRyxFQUFFLGVBQ0djLEtBQUssV0FDRmpDLGdCQUFnQlMsS0FFbkJ5QixLQUFLLE1BQU96QixLQU16QixTQUFVSSxHQUNOUCxRQUFRNkIsTUFBTSxVQUNkN0IsU0FBUUMsSUFBSU0sRUFDWkMsV0FBVUMsS0FBS3FCLE9BQU8sZUFFM0IsQ0FDSHRCLFVBQVVDLEtBQUtxQixPQUFPLFVBQVkvQixFQUFlLE9BQ2pEYyxHQUFFLGdCQUFnQkMsSUFBSSxTQUV2QixDQUNITixVQUFVQyxLQUFLcUIsT0FBTyxnQkFJOUJqQixFQUFFLFdBRUUsR0FBSWtCLEdBQWMsS0FHbEJsQixHQUFFLGdCQUFnQm1CLEdBQUcsU0FBVSxXQUMzQnBDLFVBQVVxQyxLQUFLcEMsTUFBTyxRQUFTLElBSW5DZ0IsR0FBRSxxQkFBcUJxQixNQUFNLFdBRXpCLEdBQUlILEVBQWEsQ0FDYi9CLFFBQVFDLElBQUksZUFDWixPQUFPLE9BR1gsR0FBSWUsR0FBT0gsRUFBRXNCLEtBQUt0QixFQUFFLGtCQUFrQkMsT0FDbENzQixFQUFXdkIsRUFBRXNCLEtBQUt0QixFQUFFLG1CQUFtQkMsT0FDdkN1QixFQUFXeEIsRUFBRXNCLEtBQUt0QixFQUFFLHNCQUFzQkMsT0FDMUN3QixFQUFVekIsRUFBRXNCLEtBQUt0QixFQUFFLHFCQUFxQkMsT0FDeEN5QixFQUFRMUIsRUFBRXNCLEtBQUt0QixFQUFFLG1CQUFtQkMsT0FFcEMwQixFQUFlM0IsRUFBRXNCLEtBQUt0QixFQUFFLGlCQUFpQkMsT0FDekMyQixFQUFrQjVCLEVBQUVzQixLQUFLdEIsRUFBRSxvQkFBb0JDLE1BRW5ELElBQUlFLEVBQUtkLFFBQVUsRUFBRyxDQUNsQk0sVUFBVUMsS0FBS3FCLE9BQU8sV0FDdEIsT0FBTyxPQUVYLEdBQUlNLEVBQVNsQyxRQUFVLEVBQUcsQ0FDdEJNLFVBQVVDLEtBQUtxQixPQUFPLGFBQ3RCLE9BQU8sT0FFWCxHQUFJTyxFQUFTbkMsUUFBVSxFQUFHLENBQ3RCTSxVQUFVQyxLQUFLcUIsT0FBTyxjQUN0QixPQUFPLE9BRVgsR0FBSVEsRUFBUXBDLFFBQVUsRUFBRyxDQUNyQk0sVUFBVUMsS0FBS3FCLE9BQU8sV0FDdEIsT0FBTyxPQUVYLEdBQUlZLEVBQWNKLEdBQVcsSUFBSyxDQUM5QjlCLFVBQVVDLEtBQUtxQixPQUFPLG1DQUN0QixPQUFPLE9BRVgsR0FBSVMsRUFBTXJDLFFBQVUsRUFBRyxDQUNuQk0sVUFBVUMsS0FBS3FCLE9BQU8sV0FDdEIsT0FBTyxPQU1YLEdBQUlTLEVBQVEsSUFBTSxDQUNkL0IsVUFBVUMsS0FBS3FCLE9BQU8sVUFBWSxJQUFRLEtBQzFDLE9BQU8sT0FFWCxHQUFJVSxFQUFhdEMsUUFBVSxHQUFLdUMsRUFBZ0J2QyxRQUFVLEVBQUcsQ0FDekRNLFVBQVVDLEtBQUtxQixPQUFPLFdBQ3RCLE9BQU8sT0FFWCxHQUFJYSxJQUFPSCxJQUFnQkMsQ0FDM0IsSUFBSUUsR0FBTyxJQUFLLENBQ1puQyxVQUFVQyxLQUFLcUIsT0FBTyx1QkFDdEIsT0FBTyxPQUdYLEdBQUljLElBQ0FDLFlBQVlDLGVBQWUsZUFDM0JDLGNBQWUvQixFQUNmZ0MsTUFBT1osRUFDUEMsU0FBVUEsRUFDVkMsUUFBU0EsRUFDVEMsTUFBT0EsRUFBUSxJQUVmVSxnQkFBaUJULEVBQ2pCVSxjQUFlVCxFQUduQixJQUFHeEQsV0FBVyxDQUNWMkQsRUFBT08sZ0JBQWtCbEUsV0FHN0JtRSxhQUNBdkMsR0FBRXdDLEtBQUssb0JBQ0hDLEtBQU0sT0FDTkMsU0FBVSxPQUNWaEQsS0FBTXFDLEVBQ05ZLFFBQVMsU0FBVWpELEdBQ2YsR0FBSWtELEdBQVlDLEtBQUtDLE1BQU0sR0FBSUQsTUFDL0JFLGNBQ0EsSUFBSXJELEVBQUtzRCxNQUFRLEVBQUcsQ0FDaEIsR0FBSUMsR0FBUWhCLGVBQWUsUUFDM0IsSUFBSWdCLEdBQVMsRUFBRyxDQUNaakQsRUFBRWtELE1BQU0sY0FBZSxRQUNuQkMsSUFBSyxFQUNMQyxPQUFRLE9BQ1JDLFdBQVksT0FDWkMsU0FBVSxXQUNOOUUsT0FBTytFLFNBQVNDLEtBQU8sd0RBRTNCQyxRQUFTLFdBQ0xqRixPQUFPK0UsU0FBU0MsS0FBTyx3REFFM0JFLEtBQU0sV0FDRkMsV0FJTCxDQUNIaEUsVUFBVUMsS0FBS0MsUUFBUSxPQUN2QnJCLFFBQU8rRSxTQUFTQyxLQUFPLDREQUd4QixDQUNIckUsUUFBUUMsSUFBSU0sRUFDWkMsV0FBVUMsS0FBS3FCLE9BQU8saUJBRzlCRCxNQUFPLFNBQVU0QyxFQUFLQyxFQUFRQyxHQUMxQmYsYUFDQTVELFNBQVFDLElBQUkwRSxFQUNabkUsV0FBVUMsS0FBS3FCLE9BQU8sbUJBT2xDLFNBQVMwQyxLQUVMM0QsRUFBRXdDLEtBQUssMkJBQ0hDLEtBQU0sT0FDTkMsU0FBVSxPQUNWaEQsTUFDSXNDLFlBQWFDLGVBQWUsZUFDNUJnQixNQUFPLEdBRVhOLFFBQVMsU0FBVWpELEdBQ2YsR0FBSUEsRUFBS3NELE1BQVEsRUFBRyxDQUNoQnJELFVBQVVDLEtBQUtDLFFBQVEsU0FDdkJyQixRQUFPK0UsU0FBU0MsS0FBTyw4Q0FDcEIsQ0FDSDdELFVBQVVDLEtBQUtxQixPQUFPLGNBQ3RCLE9BQU8sU0FHZkQsTUFBTyxTQUFVNEMsRUFBS0MsRUFBUUMsR0FDMUIzRSxRQUFRQyxJQUFJMEUsRUFDWm5FLFdBQVVDLEtBQUtxQixPQUFPLGtCQVNsQyxRQUFTWSxHQUFja0MsR0FDbkIsR0FBSUMsR0FBUUQsRUFBSUUsUUFBUSxnQkFBZ0IsTUFBTTVFLE1BQzlDLE9BQU8yRSIsImZpbGUiOiJlZGl0QW5zd2VyZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogQ3JlYXRlZCBieSBBZG1pbmlzdHJhdG9yIG9uIDIwMTcvNS8xMC5cclxuICovXHJcblxyXG52YXIgbmV3YUF2YXRhciA9ICcnO1xyXG5mdW5jdGlvbiBnZXRPYmplY3RVUkwoZmlsZSkge1xyXG4gICAgdmFyIHVybCA9IG51bGw7XHJcbiAgICBpZiAod2luZG93LmNyZWF0ZU9iamVjdFVSTCAhPSB1bmRlZmluZWQpIHtcclxuICAgICAgICB1cmwgPSB3aW5kb3cuY3JlYXRlT2JqZWN0VVJMKGZpbGUpO1xyXG4gICAgfSBlbHNlIGlmICh3aW5kb3cuVVJMICE9IHVuZGVmaW5lZCkge1xyXG4gICAgICAgIHVybCA9IHdpbmRvdy5VUkwuY3JlYXRlT2JqZWN0VVJMKGZpbGUpO1xyXG4gICAgfSBlbHNlIGlmICh3aW5kb3cud2Via2l0VVJMICE9IHVuZGVmaW5lZCkge1xyXG4gICAgICAgIHVybCA9IHdpbmRvdy53ZWJraXRVUkwuY3JlYXRlT2JqZWN0VVJMKGZpbGUpO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIHVybDtcclxufTtcclxuXHJcbmZ1bmN0aW9uIHJlbW92ZU9iamVjdFVSTCh1cmwpIHsvL+mHiuaUvui1hOa6kFVSTFxyXG4gICAgaWYgKHdpbmRvdy5yZXZva2VPYmplY3RVUkwgIT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgd2luZG93LnJldm9rZU9iamVjdFVSTCh1cmwpO1xyXG4gICAgfSBlbHNlIGlmICh3aW5kb3cuVVJMICE9IHVuZGVmaW5lZCkge1xyXG4gICAgICAgIHdpbmRvdy5VUkwucmV2b2tlT2JqZWN0VVJMKHVybCk7XHJcbiAgICB9IGVsc2UgaWYgKHdpbmRvdy53ZWJraXRVUkwgIT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgd2luZG93LndlYmtpdFVSTC5yZXZva2VPYmplY3RVUkwodXJsKTtcclxuICAgIH1cclxufVxyXG5cclxuLyoqXHJcbiAqIOi1hOa6kOS4iuS8oOWHveaVsCjlj4LmlbDvvJpyZXNUeXBlOui1hOa6kOexu+WeiyxyZXNUeXBlQ2xhc3M66LWE5rqQ57G75Z6L5Lit57uG5YiG55qE56eN57G7LHJlc0xpbWl0U2l6ZTrotYTmupDpmZDliLblpKflsI8pXHJcbiAqL1xyXG5mdW5jdGlvbiByZXNVcGxvYWQoZmlsZXMsIHJlc1R5cGUsIHJlc0xpbWl0U2l6ZSkge1xyXG4gICAgY29uc29sZS5sb2coZmlsZXMpO1xyXG4gICAgaWYgKGZpbGVzICYmIGZpbGVzLmxlbmd0aCA+IDApIHtcclxuICAgICAgICB2YXIgZmlsZSA9IGZpbGVzWzBdLFxyXG4gICAgICAgICAgICByZXNvdXJjZUxvY2FsVXJsID0gZ2V0T2JqZWN0VVJMKGZpbGUpO1xyXG4gICAgICAgIC8vIOmZkOWItui1hOa6kOWcqCpNQuWGhVxyXG4gICAgICAgIGlmICgkdXBsb2FkRmlsZS5jaGVja0ZpbGVTaXplKGZpbGUsIHJlc0xpbWl0U2l6ZSkpIHtcclxuICAgICAgICAgICAgJHVwbG9hZEZpbGUudXBsb2FkUmVzKGZpbGUsIHJlc1R5cGUsIGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgLy8g5LiK5Lyg5oiQ5Yqf5Zue6LCDXHJcbiAgICAgICAgICAgICAgICBmdW5jdGlvbiAoZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGRhdGEpO1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLkuIrkvKDmiJDlip/vvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc1VybCA9IGRhdGEuZGF0YS5hY2Nlc3NfdXJsO1xyXG4gICAgICAgICAgICAgICAgICAgIG5ld2FBdmF0YXIgPSByZXNVcmw7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2cocmVzVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICAkKFwiI2ltZ1VybFwiKS52YWwocmVzVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICAvLyDlpoLmnpzmmK/lm77niYfotYTmupDvvIzliJnlsZXnpLrlm77niYfpooTop4hcclxuICAgICAgICAgICAgICAgICAgICBpZiAocmVzVHlwZSA9PSAnaW1hZ2UnKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgaW1nTmFtZSA9IGZpbGUubmFtZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy9hbGVydChpbWdOYW1lKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGV4dCxpZHg7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkeCA9IGltZ05hbWUubGFzdEluZGV4T2YoXCIuXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaWR4ICE9IC0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV4dCA9IGltZ05hbWUuc3Vic3RyKGlkeCsxKS50b1VwcGVyQ2FzZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZXh0ID0gZXh0LnRvTG93ZXJDYXNlKCApO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9hbGVydChmaWxlKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vYWxlcnQoXCLlkI7nvIA9XCIrZXh0K1wi5L2N572uPVwiK2lkeCtcIui3r+W+hD1cIityZXNvdXJjZUxvY2FsVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChleHQgIT0gJ2pwZycgJiYgZXh0ICE9ICdwbmcnICYmIGV4dCAhPSAnanBlZycgJiYgZXh0ICE9ICdnaWYnKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL2RvY3VtZW50LmFsbC5zdWJtaXRfdXBsb2FkLmRpc2FibGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChcIuivt+S4iuS8oOWbvueJh+exu+Wei+eahOaWh+S7tuWTpn5cIik7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9hbGVydChcIjIu5Y+q6IO95LiK5LygLmpwZyAgLnBuZyAgLmpwZWcgIC5naWbnsbvlnovnmoTmlofku7YhXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRvY3VtZW50LmFsbC5zdWJtaXRfdXBsb2FkLmRpc2FibGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi6K+35LiK5Lyg5Zu+54mH57G75Z6L55qE5paH5Lu25ZOmflwiKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vYWxlcnQoXCLlj6rog73kuIrkvKAuanBnICAucG5nICAuanBlZyAgLmdpZuexu+Wei+eahOaWh+S7tiFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8v55u05o6l5Yqg6L295pys5Zyw5Zu+54mH6L+b6KGM6aKE6KeIXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICQoXCIuYXZhdGFySWNvblwiKVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmxvYWQoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlbW92ZU9iamVjdFVSTChyZXNvdXJjZUxvY2FsVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cihcInNyY1wiLCByZXNvdXJjZUxvY2FsVXJsKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICAvLyDkuIrkvKDlpLHotKXlm57osINcclxuICAgICAgICAgICAgICAgIGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihcIuS4iuS8oOWksei0pSEhIVwiKTtcclxuICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhkYXRhKTtcclxuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkuIrkvKDlpLHotKXvvIFcIik7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkuIrkvKDotYTmupDpmZDliLblnKhcIiArIHJlc0xpbWl0U2l6ZSArIFwiTULlhoXvvIFcIik7XHJcbiAgICAgICAgICAgICQoXCIjdXBMb2FkSW1hZ2VcIikudmFsKFwiXCIpO1xyXG4gICAgICAgIH1cclxuICAgIH0gZWxzZSB7XHJcbiAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi572R57uc6ZSZ6K+v77yM6K+356iN5ZCO5YaN6K+V77yBXCIpO1xyXG4gICAgfVxyXG59O1xyXG5cclxuJChmdW5jdGlvbiAoKSB7XHJcblxyXG4gICAgdmFyIHN1Ym1pdExpbWl0ID0gZmFsc2U7XHJcblxyXG5cclxuICAgICQoXCIjdXBsb2FkSW1hZ2VcIikub24oXCJjaGFuZ2VcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJlc1VwbG9hZCh0aGlzLmZpbGVzLCBcImltYWdlXCIsIDUpO1xyXG4gICAgfSk7XHJcblxyXG5cclxuICAgICQoXCIjc2F2ZUFuc3dlcmVySW5mb1wiKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcblxyXG4gICAgICAgIGlmIChzdWJtaXRMaW1pdCkge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhcIuato+WcqOaPkOS6pOS4re+8jOS4jeiDvemHjeWkjeaPkOS6pFwiKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgdmFyIG5hbWUgPSAkLnRyaW0oJChcIiNyZXNwb25kZXJOYW1lXCIpLnZhbCgpKSxcclxuICAgICAgICAgICAgcGhvbmVOdW0gPSAkLnRyaW0oJChcIiNyZXNwb25kZXJQaG9uZVwiKS52YWwoKSksXHJcbiAgICAgICAgICAgIHBvc2l0aW9uID0gJC50cmltKCQoXCIjcmVzcG9uZGVyUG9zaXRpb25cIikudmFsKCkpLFxyXG4gICAgICAgICAgICBzdW1tYXJ5ID0gJC50cmltKCQoXCIjcmVzcG9uZGVyU3VtbWFyeVwiKS52YWwoKSksXHJcbiAgICAgICAgICAgIHByaWNlID0gJC50cmltKCQoXCIjcmVzcG9uZGVyUHJpY2VcIikudmFsKCkpLFxyXG5cclxuICAgICAgICAgICAgc2hhcmVyVHJhZGVyID0gJC50cmltKCQoXCIjc2hhcmVyVHJhZGVyXCIpLnZhbCgpKSxcclxuICAgICAgICAgICAgc2hhcmVyUmVzcG9uZGVyID0gJC50cmltKCQoXCIjc2hhcmVyUmVzcG9uZGVyXCIpLnZhbCgpKTtcclxuXHJcbiAgICAgICAgaWYgKG5hbWUubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi562U5Li75aeT5ZCN5LiN6IO95Li656m6XCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmIChwaG9uZU51bS5sZW5ndGggPT0gMCkge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLnrZTkuLvmiYvmnLrlj7fnoIHkuI3og73kuLrnqbpcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKHBvc2l0aW9uLmxlbmd0aCA9PSAwKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuetlOS4u+iBjOS9jS/lpLTooZTkuI3og73kuLrnqbpcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKHN1bW1hcnkubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi562U5Li7566A5LuL5LiN6IO95Li656m6XCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmIChjaEVuV29yZENvdW50KHN1bW1hcnkpID4gMTI4KSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuetlOS4u+eugOS7i+i+k+WFpeWtl+espumVv+W6puS4jeiDvei2hei/hzEyOOWtl+espu+8iDHkuKrkuK3mlofmjInkuKTkuKrlrZfnrKborqHnrpfvvIlcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKHByaWNlLmxlbmd0aCA9PSAwKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuaPkOmXruS7t+agvOS4jeiDveS4uuepulwiKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvLyBpZiAocHJpY2UgPCAwLjEpIHtcclxuICAgICAgICAvLyAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5o+Q6Zeu5Lu35qC85LiN6IO95L2O5LqOMC4x5YWDXCIpO1xyXG4gICAgICAgIC8vICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgLy8gfVxyXG4gICAgICAgIGlmIChwcmljZSA+IDEwMDApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5Lu35qC85LiN6IO95aSn5LqOIFwiICsgMTAwMDAgKyBcIiDlhYNcIik7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKHNoYXJlclRyYWRlci5sZW5ndGggPT0gMCB8fCBzaGFyZXJSZXNwb25kZXIubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5o+Q6Zeu5YiG5oiQ5LiN6IO95Li656m6XCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBzdW0gPSArc2hhcmVyVHJhZGVyICsgK3NoYXJlclJlc3BvbmRlcjtcclxuICAgICAgICBpZiAoc3VtICE9IDEwMCkge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLllYblrrbjgIHnrZTkuLvvvIzkuozogIXliIbmiJDmgLvlkozlv4XpobvnrYnkuo4xMDAlXCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB2YXIgcGFyYW1zID0ge1xyXG4gICAgICAgICAgICBhbnN3ZXJlcl9pZDpHZXRRdWVyeVN0cmluZyhcImFuc3dlcmVyX2lkXCIpLFxyXG4gICAgICAgICAgICBhbnN3ZXJlcl9uYW1lOiBuYW1lLFxyXG4gICAgICAgICAgICBwaG9uZTogcGhvbmVOdW0sXHJcbiAgICAgICAgICAgIHBvc2l0aW9uOiBwb3NpdGlvbixcclxuICAgICAgICAgICAgc3VtbWFyeTogc3VtbWFyeSxcclxuICAgICAgICAgICAgcHJpY2U6IHByaWNlICogMTAwLFxyXG5cclxuICAgICAgICAgICAgcHJvZml0X2J1c2luZXNzOiBzaGFyZXJUcmFkZXIsXHJcbiAgICAgICAgICAgIHByb2ZpdF9hbnN3ZXI6IHNoYXJlclJlc3BvbmRlcixcclxuICAgICAgICB9O1xyXG5cclxuICAgICAgICBpZihuZXdhQXZhdGFyKXtcclxuICAgICAgICAgICAgcGFyYW1zLmFuc3dlcmVyX2F2YXRhciA9IG5ld2FBdmF0YXI7XHJcbiAgICAgICAgICAgIC8vIGNvbnNvbGUubG9nKHBhcmFtcyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHNob3dMb2FkaW5nKCk7XHJcbiAgICAgICAgJC5hamF4KFwiL1FBL3NhdmVBbnN3ZXJlclwiLCB7XHJcbiAgICAgICAgICAgIHR5cGU6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICBkYXRhVHlwZTogXCJqc29uXCIsXHJcbiAgICAgICAgICAgIGRhdGE6IHBhcmFtcyxcclxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKGRhdGEpIHtcclxuICAgICAgICAgICAgICAgIHZhciB0aW1lc3RhbXAgPSBEYXRlLnBhcnNlKG5ldyBEYXRlKCkpO1xyXG4gICAgICAgICAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICAgICAgICAgIGlmIChkYXRhLmNvZGUgPT0gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciBzdGF0ZSA9IEdldFF1ZXJ5U3RyaW5nKFwic3RhdGVcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0YXRlID09IDEpIHsgICAvL+S4i+e6v+eKtuaAgVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAkLmFsZXJ0KFwi5bey5L+d5a2Y77yM5piv5ZCm5LiK57q/562U5Li777yfXCIsIFwiaW5mb1wiLCB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBidG46IDMsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBva3RleHQ6IFwi56uL5Y2z5LiK57q/XCIsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYW5jZWx0ZXh0OiBcIuaaguS4jeS4iue6v1wiLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgb25DYW5jZWw6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL1FBL3F1ZXN0aW9uQW5kQW5zd2VyRGV0YWlsP3BhZ2VfdHlwZT0xJnNldD1hbnN3ZXJlclwiO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9uQ2xvc2U6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL1FBL3F1ZXN0aW9uQW5kQW5zd2VyRGV0YWlsP3BhZ2VfdHlwZT0xJnNldD1hbnN3ZXJlclwiO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9uT2s6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93QW5zd2VyZXIoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoJ+S/neWtmOaIkOWKnycpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL1FBL3F1ZXN0aW9uQW5kQW5zd2VyRGV0YWlsP3BhZ2VfdHlwZT0xJnNldD1hbnN3ZXJlclwiO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGRhdGEpO1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIue9kee7nOmUmeivr++8jOivt+eojeWQjuWGjeivle+8gVwiKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uICh4aHIsIHN0YXR1cywgZXJyKSB7XHJcbiAgICAgICAgICAgICAgICBoaWRlTG9hZGluZygpO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coZXJyKTtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIue9kee7nOmUmeivr++8jOivt+eojeWQjuWGjeivle+8gVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuXHJcbiAgICB9KTtcclxuXHJcbiAgICBmdW5jdGlvbiBzaG93QW5zd2VyZXIoKSB7XHJcblxyXG4gICAgICAgICQuYWpheChcIi9RQS9jaGFuZ2VBbnN3ZXJlclN0YXRlXCIsIHtcclxuICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgIGRhdGFUeXBlOiBcImpzb25cIixcclxuICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgYW5zd2VyZXJfaWQ6IEdldFF1ZXJ5U3RyaW5nKFwiYW5zd2VyZXJfaWRcIiksXHJcbiAgICAgICAgICAgICAgICBzdGF0ZTogMFxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAoZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgaWYgKGRhdGEuY29kZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcCgn562U5Li75LiK57q/5oiQ5YqfJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBcIi9RQS9xdWVzdGlvbkFuZEFuc3dlckRldGFpbD9wYWdlX3R5cGU9MVwiO1xyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLnvZHnu5zplJnor6/vvIzor7fnqI3lkI7lho3or5XvvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24gKHhociwgc3RhdHVzLCBlcnIpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGVycik7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLnvZHnu5zplJnor6/vvIzor7fnqI3lkI7lho3or5XvvIFcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuXHJcbiAgICB9O1xyXG5cclxuICAgIC8qKlxyXG4gICAgICog5Lit6Iux5paH57uf6K6hKOS4gOS4quS4reaWh+eul+S4pOS4quWtl+espilcclxuICAgICAqL1xyXG4gICAgZnVuY3Rpb24gY2hFbldvcmRDb3VudChzdHIpe1xyXG4gICAgICAgIHZhciBjb3VudCA9IHN0ci5yZXBsYWNlKC9bXlxceDAwLVxceGZmXS9nLFwiKipcIikubGVuZ3RoO1xyXG4gICAgICAgIHJldHVybiBjb3VudDtcclxuICAgIH07XHJcblxyXG5cclxufSk7XHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcbiJdfQ==
