$(document).ready(function(){Business.init()});var Business=function(){var e={};e.coverImgUrl="";e.page_type="";e.member_id="";e.page_origin="";e.init=function(){e.page_type=$(".admin_data").data("type");e.member_id=GetQueryString("id");e.page_origin=GetQueryString("page_origin");if(e.page_type==0||e.page_origin=="member_list"){$(".go_back").html("会员列表")}else{$(".go_back").html("会员详情")}$(".cancelBtn, .go_back").click(function(){if(e.page_type==0||e.page_origin=="member_list"){window.location.href="/member_list_page"}else{window.location.href="/member_detail_page?id="+e.member_id}});$(".memberCoverImg").click(function(){$(".uploadCoverInput").click()});$(".uploadCoverInput").on("change",function(i){if(this.files&&this.files.length>0){var r=this.files[0];var t=r.name;var a,o;o=t.lastIndexOf(".");if(o!=-1){a=t.substr(o+1).toUpperCase();a=a.toLowerCase();if(a!="jpg"&&a!="png"&&a!="jpeg"&&a!="gif"){baseUtils.show.redTip("请上传图片类型的文件哦~");return}}else{document.all.submit_upload.disabled=true;baseUtils.show.redTip("请上传图片类型的文件哦~");return}var n=5;if($uploadFile.checkFileSize(r,n)){showLoading();$uploadFile.uploadPic(r,function(i){hideLoading();baseUtils.show.blueTip("上传成功！");e.coverImgUrl=i.data.access_url;if(e.coverImgUrl){$(".memberCoverImg img").attr("src",e.coverImgUrl);$("#coverImgUrl").val(e.coverImgUrl)}},function(e){hideLoading();console.log(e);baseUtils.show.redTip("上传失败！")})}else{baseUtils.show.redTip("上传图片限制在"+n+"MB内！")}}else{console.log(this.files)}});changeSaveFlag(true);$("#preview").on("click",function(){var e=ue.getContent();document.getElementById("preview_content").innerHTML=e;$(".preview_con").addClass("active");$(".preview_box").addClass("active");document.documentElement.style.overflow="hidden"});$(".preview_con").on("click",function(){$(this).removeClass("active");$("#preview_content").html("");$(".preview_box").removeClass("active");document.documentElement.style.overflow="auto"});$(".saveBtn").click(function(){var i=$.trim($(".memberNameInput").val());if($formCheck.emptyString(i)){baseUtils.show.redTip("会员名称不能为空！");return false}var r=$.trim($(".memberSummaryTextArea").val());if($formCheck.emptyString(r)){baseUtils.show.redTip("会员简介不能为空！");return false}var t=$("#coverImgUrl").val();if($formCheck.emptyString(t)){baseUtils.show.redTip("会员封面不能为空！");return false}var a=$.trim($(".memberPriceInput").val());if($formCheck.emptyString(a)){baseUtils.show.redTip("会员价格不能为空！");return false}if(a<=0){baseUtils.show.redTip("会员价格不能为 0 或负数！");return false}if(a>baseUtils.maxInputPrice){baseUtils.show.redTip("价格不能大于 "+baseUtils.maxInputPrice+" 元");return false}var o={};o["name"]=i;o["summary"]=r;o["img_url"]=t;o["img_url_compressed"]=t;o["price"]=a*100;var n=parseInt($(".selectValidPeriod").val());switch(n){case 1:n=2592e3;break;case 2:n=7776e3;break;case 3:n=15811200;break;case 4:n=31622400;break;default:break}o["period"]=n;var s=UE.getEditor("container");o["org_content"]=s.getContent();o["descrb"]=s.getPlainTxt();var l=[];$('input[name="category"]:checked').each(function(){l.push($(this).attr("id"))});o["state"]=$("input[name='showMember']:checked").val();o["is_member"]=1;o["id"]=GetQueryString("id");if(e.page_type==0){o["member_icon_default"]="http://wxresource-10011692.file.myqcloud.com/manual/icon_member_diamond_gray.png";o["member_icon_highlight"]="http://wxresource-10011692.file.myqcloud.com/manual/icon_member_diamond.png";memberUtils.uploadMemberInfo(o,l)}else{memberUtils.editedMemberInfo(o,l)}})};return e}();var memberUtils=function(){var e={};e.editedMemberInfo=function(e,i){showLoading();$.ajax("/goods_edit_package",{type:"POST",dataType:"json",data:{params:e,category_type:i},success:function(e){if(e.code==0){baseUtils.show.blueTip("编辑会员成功！");$(".go_back").click()}else{hideLoading();baseUtils.show.redTip("编辑失败，请稍后再试！")}},error:function(e,i,r){hideLoading();console.log(r);baseUtils.show.redTip("服务器开小差了，请稍后再试！")}})};e.uploadMemberInfo=function(e,i){showLoading();$.ajax("/goods_upload_package",{type:"POST",dataType:"json",data:{params:e,category_type:i},success:function(e){if(e.code==0){baseUtils.show.blueTip("新建会员成功！");window.location.href="/member_list_page"}else{hideLoading();baseUtils.show.redTip("新建失败，请稍后再试！")}},error:function(e,i,r){hideLoading();console.log(r);baseUtils.show.redTip("服务器开小差了，请稍后再试！")}})};return e}();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1hbmFnZU1lbWJlci5qcyJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJyZWFkeSIsIkJ1c2luZXNzIiwiaW5pdCIsImNvdmVySW1nVXJsIiwicGFnZV90eXBlIiwibWVtYmVyX2lkIiwicGFnZV9vcmlnaW4iLCJkYXRhIiwiR2V0UXVlcnlTdHJpbmciLCJodG1sIiwiY2xpY2siLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJvbiIsImUiLCJ0aGlzIiwiZmlsZXMiLCJsZW5ndGgiLCJmaWxlIiwiaW1nTmFtZSIsIm5hbWUiLCJleHQiLCJpZHgiLCJsYXN0SW5kZXhPZiIsInN1YnN0ciIsInRvVXBwZXJDYXNlIiwidG9Mb3dlckNhc2UiLCJiYXNlVXRpbHMiLCJzaG93IiwicmVkVGlwIiwiYWxsIiwic3VibWl0X3VwbG9hZCIsImRpc2FibGVkIiwibGltaXRTaXplIiwiJHVwbG9hZEZpbGUiLCJjaGVja0ZpbGVTaXplIiwic2hvd0xvYWRpbmciLCJ1cGxvYWRQaWMiLCJyZXN1bHQiLCJoaWRlTG9hZGluZyIsImJsdWVUaXAiLCJhY2Nlc3NfdXJsIiwiYXR0ciIsInZhbCIsImNvbnNvbGUiLCJsb2ciLCJjaGFuZ2VTYXZlRmxhZyIsInVlIiwiZ2V0Q29udGVudCIsImdldEVsZW1lbnRCeUlkIiwiaW5uZXJIVE1MIiwiYWRkQ2xhc3MiLCJkb2N1bWVudEVsZW1lbnQiLCJzdHlsZSIsIm92ZXJmbG93IiwicmVtb3ZlQ2xhc3MiLCJtZW1iZXJOYW1lIiwidHJpbSIsIiRmb3JtQ2hlY2siLCJlbXB0eVN0cmluZyIsIm1lbWJlclN1bW1hcnkiLCJtZW1iZXJQcmljZSIsIm1heElucHV0UHJpY2UiLCJwYXJhbXMiLCJtZW1iZXJWYWxpZFBlcmlvZCIsInBhcnNlSW50IiwiVUUiLCJnZXRFZGl0b3IiLCJnZXRQbGFpblR4dCIsIm1lbWJlckNhdGVnb3J5QXJyYXkiLCJlYWNoIiwicHVzaCIsIm1lbWJlclV0aWxzIiwidXBsb2FkTWVtYmVySW5mbyIsImVkaXRlZE1lbWJlckluZm8iLCJ1dGlscyIsImNhdGVnb3J5VHlwZUFyciIsImFqYXgiLCJ0eXBlIiwiZGF0YVR5cGUiLCJjYXRlZ29yeV90eXBlIiwic3VjY2VzcyIsImNvZGUiLCJlcnJvciIsInhociIsInN0YXR1cyIsImVyciJdLCJtYXBwaW5ncyI6IkFBS0FBLEVBQUVDLFVBQVVDLE1BQU0sV0FDZEMsU0FBU0MsUUFJYixJQUFJRCxVQUFXLFdBRVgsR0FBSUEsS0FFSkEsR0FBU0UsWUFBYyxFQUN2QkYsR0FBU0csVUFBWSxFQUNyQkgsR0FBU0ksVUFBWSxFQUNyQkosR0FBU0ssWUFBYyxFQUV2QkwsR0FBU0MsS0FBTyxXQUVaRCxFQUFTRyxVQUFZTixFQUFFLGVBQWVTLEtBQUssT0FFM0NOLEdBQVNJLFVBQVlHLGVBQWUsS0FDcENQLEdBQVNLLFlBQWNFLGVBQWUsY0FHdEMsSUFBSVAsRUFBU0csV0FBYSxHQUFLSCxFQUFTSyxhQUFlLGNBQWUsQ0FDbEVSLEVBQUUsWUFBWVcsS0FBSyxZQUNoQixDQUNIWCxFQUFFLFlBQVlXLEtBQUssUUFJdkJYLEVBQUUsd0JBQXdCWSxNQUFNLFdBQzVCLEdBQUlULEVBQVNHLFdBQWEsR0FBS0gsRUFBU0ssYUFBZSxjQUFlLENBQ2xFSyxPQUFPQyxTQUFTQyxLQUFPLHdCQUNwQixDQUNIRixPQUFPQyxTQUFTQyxLQUFPLDBCQUE0QlosRUFBU0ksWUFNcEVQLEdBQUUsbUJBQW1CWSxNQUFNLFdBQ3ZCWixFQUFFLHFCQUFxQlksU0FJM0JaLEdBQUUscUJBQXFCZ0IsR0FBRyxTQUFTLFNBQVVDLEdBQ3pDLEdBQUdDLEtBQUtDLE9BQVNELEtBQUtDLE1BQU1DLE9BQU8sRUFBRSxDQUNqQyxHQUFJQyxHQUFPSCxLQUFLQyxNQUFNLEVBRXRCLElBQUlHLEdBQVVELEVBQUtFLElBRW5CLElBQUlDLEdBQUlDLENBQ1JBLEdBQU1ILEVBQVFJLFlBQVksSUFDMUIsSUFBSUQsSUFBUSxFQUFFLENBQ1ZELEVBQU1GLEVBQVFLLE9BQU9GLEVBQUksR0FBR0csYUFDNUJKLEdBQU1BLEVB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VUFDdkI5QixRQUFPQyxTQUFTQyxLQUFPLHdCQUNwQixDQUNIMkIsYUFDQVosV0FBVUMsS0FBS0MsT0FBTyxpQkFHOUJzRCxNQUFPLFNBQVVDLEVBQUtDLEVBQVFDLEdBQzFCL0MsYUFDQUssU0FBUUMsSUFBSXlDLEVBQ1ozRCxXQUFVQyxLQUFLQyxPQUFPLHFCQU9sQyxPQUFPOEMiLCJmaWxlIjoibWFuYWdlTWVtYmVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqIENyZWF0ZWQgYnkgQWRtaW5pc3RyYXRvciBvbiAyMDE3LzMvMjEuXHJcbiAqL1xyXG5cclxuXHJcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuICAgIEJ1c2luZXNzLmluaXQoKTtcclxuXHJcbn0pO1xyXG5cclxudmFyIEJ1c2luZXNzID0gKGZ1bmN0aW9uICgpIHtcclxuXHJcbiAgICB2YXIgQnVzaW5lc3MgPSB7fTtcclxuXHJcbiAgICBCdXNpbmVzcy5jb3ZlckltZ1VybCA9IFwiXCI7XHJcbiAgICBCdXNpbmVzcy5wYWdlX3R5cGUgPSBcIlwiO1xyXG4gICAgQnVzaW5lc3MubWVtYmVyX2lkID0gXCJcIjtcclxuICAgIEJ1c2luZXNzLnBhZ2Vfb3JpZ2luID0gXCJcIjtcclxuXHJcbiAgICBCdXNpbmVzcy5pbml0ID0gZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICBCdXNpbmVzcy5wYWdlX3R5cGUgPSAkKFwiLmFkbWluX2RhdGFcIikuZGF0YShcInR5cGVcIik7ICAgICAgLy8gIOiOt+WPluW9k+WJjemhtemdouexu+WeiyDvvIgwIC0+IOaWsOWiniDvvJsgMSAtPiDnvJbovpHvvIlcclxuXHJcbiAgICAgICAgQnVzaW5lc3MubWVtYmVyX2lkID0gR2V0UXVlcnlTdHJpbmcoXCJpZFwiKTtcclxuICAgICAgICBCdXNpbmVzcy5wYWdlX29yaWdpbiA9IEdldFF1ZXJ5U3RyaW5nKFwicGFnZV9vcmlnaW5cIik7XHJcblxyXG4gICAgICAgIC8vICDnoa7lrprpobXpnaLmnaXmupBcclxuICAgICAgICBpZiAoQnVzaW5lc3MucGFnZV90eXBlID09IDAgfHwgQnVzaW5lc3MucGFnZV9vcmlnaW4gPT0gXCJtZW1iZXJfbGlzdFwiKSB7XHJcbiAgICAgICAgICAgICQoXCIuZ29fYmFja1wiKS5odG1sKFwi5Lya5ZGY5YiX6KGoXCIpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICQoXCIuZ29fYmFja1wiKS5odG1sKFwi5Lya5ZGY6K+m5oOFXCIpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLy8g5LiK5LiA5q2lXHJcbiAgICAgICAgJChcIi5jYW5jZWxCdG4sIC5nb19iYWNrXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKEJ1c2luZXNzLnBhZ2VfdHlwZSA9PSAwIHx8IEJ1c2luZXNzLnBhZ2Vfb3JpZ2luID09IFwibWVtYmVyX2xpc3RcIikge1xyXG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSAnL21lbWJlcl9saXN0X3BhZ2UnO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSAnL21lbWJlcl9kZXRhaWxfcGFnZT9pZD0nICsgQnVzaW5lc3MubWVtYmVyX2lkO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyAg54K55Ye75Zu+54mHXHJcbiAgICAgICAgJChcIi5tZW1iZXJDb3ZlckltZ1wiKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICQoXCIudXBsb2FkQ292ZXJJbnB1dFwiKS5jbGljaygpO1xyXG4gICAgICAgIH0pXHJcblxyXG4gICAgICAgIC8vIOS4iuS8oOS8muWRmOWwgemdolxyXG4gICAgICAgICQoXCIudXBsb2FkQ292ZXJJbnB1dFwiKS5vbihcImNoYW5nZVwiLGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIGlmKHRoaXMuZmlsZXMgJiYgdGhpcy5maWxlcy5sZW5ndGg+MCl7XHJcbiAgICAgICAgICAgICAgICB2YXIgZmlsZSA9IHRoaXMuZmlsZXNbMF07XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIGltZ05hbWUgPSBmaWxlLm5hbWU7XHJcbiAgICAgICAgICAgICAgICAvL2FsZXJ0KGltZ05hbWUpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGV4dCxpZHg7XHJcbiAgICAgICAgICAgICAgICBpZHggPSBpbWdOYW1lLmxhc3RJbmRleE9mKFwiLlwiKTtcclxuICAgICAgICAgICAgICAgIGlmIChpZHggIT0gLTEpe1xyXG4gICAgICAgICAgICAgICAgICAgIGV4dCA9IGltZ05hbWUuc3Vic3RyKGlkeCsxKS50b1VwcGVyQ2FzZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgIGV4dCA9IGV4dC50b0xvd2VyQ2FzZSggKTtcclxuICAgICAgICAgICAgICAgICAgICAvL2FsZXJ0KGZpbGUpO1xyXG4gICAgICAgICAgICAgICAgICAgIC8vYWxlcnQoXCLlkI7nvIA9XCIrZXh0K1wi5L2N572uPVwiK2lkeCtcIui3r+W+hD1cIityZXNvdXJjZUxvY2FsVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICBpZiAoZXh0ICE9ICdqcGcnICYmIGV4dCAhPSAncG5nJyAmJiBleHQgIT0gJ2pwZWcnICYmIGV4dCAhPSAnZ2lmJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vZG9jdW1lbnQuYWxsLnN1Ym1pdF91cGxvYWQuZGlzYWJsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi6K+35LiK5Lyg5Zu+54mH57G75Z6L55qE5paH5Lu25ZOmflwiKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy9hbGVydChcIjIu5Y+q6IO95LiK5LygLmpwZyAgLnBuZyAgLmpwZWcgIC5naWbnsbvlnovnmoTmlofku7YhXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5hbGwuc3VibWl0X3VwbG9hZC5kaXNhYmxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuivt+S4iuS8oOWbvueJh+exu+Wei+eahOaWh+S7tuWTpn5cIik7XHJcbiAgICAgICAgICAgICAgICAgICAgLy9hbGVydChcIuWPquiDveS4iuS8oC5qcGcgIC5wbmcgIC5qcGVnICAuZ2lm57G75Z6L55qE5paH5Lu2IVwiKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIGxpbWl0U2l6ZSA9IDU7ICAvLyDpmZDliLbkuIrkvKDlpKflsI8vLyDpmZDliLblm77niYflnKg1TULlhoVcclxuXHJcbiAgICAgICAgICAgICAgICBpZigkdXBsb2FkRmlsZS5jaGVja0ZpbGVTaXplKGZpbGUsbGltaXRTaXplKSl7XHJcbiAgICAgICAgICAgICAgICAgICAgc2hvd0xvYWRpbmcoKTtcclxuICAgICAgICAgICAgICAgICAgICAkdXBsb2FkRmlsZS51cGxvYWRQaWMoZmlsZSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8g5oiQ5Yqf5Zue6LCDXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhpZGVMb2FkaW5nKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi5LiK5Lyg5oiQ5Yqf77yBXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgQnVzaW5lc3MuY292ZXJJbWdVcmwgPSByZXN1bHQuZGF0YS5hY2Nlc3NfdXJsO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoQnVzaW5lc3MuY292ZXJJbWdVcmwpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoXCIubWVtYmVyQ292ZXJJbWcgaW1nXCIpLmF0dHIoXCJzcmNcIiwgQnVzaW5lc3MuY292ZXJJbWdVcmwpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoXCIjY292ZXJJbWdVcmxcIikudmFsKEJ1c2luZXNzLmNvdmVySW1nVXJsKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8g5aSx6LSl5Zue6LCDXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBoaWRlTG9hZGluZygpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coZGF0YSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkuIrkvKDlpLHotKXvvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5LiK5Lyg5Zu+54mH6ZmQ5Yi25ZyoXCIrbGltaXRTaXplK1wiTULlhoXvvIFcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyh0aGlzLmZpbGVzKVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v54K55Ye75L6n6L655qCP56a75byA5pe255qE5by55qGGXHJcbiAgICAgICAgY2hhbmdlU2F2ZUZsYWcodHJ1ZSk7XHJcblxyXG4gICAgICAgIC8v57yW6L6R5Zmo6aKE6KeIXHJcbiAgICAgICAgJCgnI3ByZXZpZXcnKS5vbignY2xpY2snLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciBodG1sID0gdWUuZ2V0Q29udGVudCgpO1xyXG4gICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncHJldmlld19jb250ZW50JykuaW5uZXJIVE1MID0gaHRtbDtcclxuICAgICAgICAgICAgJCgnLnByZXZpZXdfY29uJykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAkKCcucHJldmlld19ib3gnKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5zdHlsZS5vdmVyZmxvdyA9IFwiaGlkZGVuXCI7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgJCgnLnByZXZpZXdfY29uJykub24oJ2NsaWNrJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgJCgnI3ByZXZpZXdfY29udGVudCcpLmh0bWwoJycpO1xyXG4gICAgICAgICAgICAkKCcucHJldmlld19ib3gnKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5zdHlsZS5vdmVyZmxvdyA9IFwiYXV0b1wiO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyAg5L+d5a2Y5oyJ6ZKuXHJcbiAgICAgICAgJChcIi5zYXZlQnRuXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuXHJcbiAgICAgICAgICAgIC8vICAxIC0+IOW8gOWni+ajgOafpeW/heimgei+k+WFpeS/oeaBr1xyXG5cclxuICAgICAgICAgICAgdmFyIG1lbWJlck5hbWUgPSAkLnRyaW0oJChcIi5tZW1iZXJOYW1lSW5wdXRcIikudmFsKCkpO1xyXG4gICAgICAgICAgICBpZiAoJGZvcm1DaGVjay5lbXB0eVN0cmluZyhtZW1iZXJOYW1lKSkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5Lya5ZGY5ZCN56ew5LiN6IO95Li656m677yBXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHZhciBtZW1iZXJTdW1tYXJ5ID0gJC50cmltKCQoXCIubWVtYmVyU3VtbWFyeVRleHRBcmVhXCIpLnZhbCgpKTtcclxuICAgICAgICAgICAgaWYgKCRmb3JtQ2hlY2suZW1wdHlTdHJpbmcobWVtYmVyU3VtbWFyeSkpIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuS8muWRmOeugOS7i+S4jeiDveS4uuepuu+8gVwiKTtcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB2YXIgY292ZXJJbWdVcmwgPSAkKFwiI2NvdmVySW1nVXJsXCIpLnZhbCgpO1xyXG4gICAgICAgICAgICBpZiAoJGZvcm1DaGVjay5lbXB0eVN0cmluZyhjb3ZlckltZ1VybCkpIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuS8muWRmOWwgemdouS4jeiDveS4uuepuu+8gVwiKTtcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgdmFyIG1lbWJlclByaWNlID0gJC50cmltKCQoXCIubWVtYmVyUHJpY2VJbnB1dFwiKS52YWwoKSk7XHJcbiAgICAgICAgICAgIGlmICgkZm9ybUNoZWNrLmVtcHR5U3RyaW5nKG1lbWJlclByaWNlKSkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5Lya5ZGY5Lu35qC85LiN6IO95Li656m677yBXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmIChtZW1iZXJQcmljZSA8PSAwKSB7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLkvJrlkZjku7fmoLzkuI3og73kuLogMCDmiJbotJ/mlbDvvIFcIik7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYgKG1lbWJlclByaWNlID4gYmFzZVV0aWxzLm1heElucHV0UHJpY2UpIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuS7t+agvOS4jeiDveWkp+S6jiBcIiArIGJhc2VVdGlscy5tYXhJbnB1dFByaWNlICsgXCIg5YWDXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB2YXIgcGFyYW1zID0ge307XHJcblxyXG4gICAgICAgICAgICBwYXJhbXNbJ25hbWUnXSA9IG1lbWJlck5hbWU7XHJcbiAgICAgICAgICAgIHBhcmFtc1snc3VtbWFyeSddID0gbWVtYmVyU3VtbWFyeTtcclxuICAgICAgICAgICAgcGFyYW1zWydpbWdfdXJsJ10gPSBjb3ZlckltZ1VybDtcclxuXHJcbiAgICAgICAgICAgIHBhcmFtc1snaW1nX3VybF9jb21wcmVzc2VkJ10gPSBjb3ZlckltZ1VybDsgICAgIC8vIOWwgemdouWOi+e8qeWcsOWdgOm7mOiupOS4uuWOn+WbvuWcsOWdgO+8jOmYsuatouWOi+e8qeWksei0pVxyXG5cclxuICAgICAgICAgICAgcGFyYW1zWydwcmljZSddID0gbWVtYmVyUHJpY2UgKiAxMDA7ICAgIC8vICDljZXkvY3vvJog5YiGXHJcblxyXG4gICAgICAgICAgICAvLyAgMiAtPiDmo4Dmn6Xml6Dor6/vvIzkv53lrZjliankvZnkv6Hmga9cclxuXHJcbiAgICAgICAgICAgIC8vIOS8muWRmOaciemZkOacn++8iOWNleS9je+8miDnp5LvvIlcclxuICAgICAgICAgICAgdmFyIG1lbWJlclZhbGlkUGVyaW9kID0gcGFyc2VJbnQoJChcIi5zZWxlY3RWYWxpZFBlcmlvZFwiKS52YWwoKSk7XHJcbiAgICAgICAgICAgIHN3aXRjaCAobWVtYmVyVmFsaWRQZXJpb2QpIHtcclxuICAgICAgICAgICAgICAgIGNhc2UgMTpcclxuICAgICAgICAgICAgICAgICAgICAvLyAg5LiA5Liq5pyIOjI1OTIwMDAgPSAzMCAqIDI0ICogNjAgKiA2MFxyXG4gICAgICAgICAgICAgICAgICAgIG1lbWJlclZhbGlkUGVyaW9kID0gMjU5MjAwMDtcclxuICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgIGNhc2UgMjpcclxuICAgICAgICAgICAgICAgICAgICAvLyAg5LiJ5Liq5pyIOjc4NjI0MDAgLT4gNzc3NjAwMCA9IDkwICogMjQgKiA2MCAqIDYwXHJcbiAgICAgICAgICAgICAgICAgICAgbWVtYmVyVmFsaWRQZXJpb2QgPSA3Nzc2MDAwO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgY2FzZSAzOlxyXG4gICAgICAgICAgICAgICAgICAgIC8vICDljYrlubQ6MTU3MjQ4MDAgLT4gMTU4MTEyMDAgPSAxODMgKiAyNCAqIDYwICogNjBcclxuICAgICAgICAgICAgICAgICAgICBtZW1iZXJWYWxpZFBlcmlvZCA9IDE1ODExMjAwO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgY2FzZSA0OlxyXG4gICAgICAgICAgICAgICAgICAgIC8vICDkuIDlubQ6MzE2MjI0MDAgPSAzNjYgKiAyNCAqIDYwICogNjBcclxuICAgICAgICAgICAgICAgICAgICBtZW1iZXJWYWxpZFBlcmlvZCA9IDMxNjIyNDAwO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcclxuICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBwYXJhbXNbJ3BlcmlvZCddID0gbWVtYmVyVmFsaWRQZXJpb2Q7XHJcblxyXG5cclxuICAgICAgICAgICAgLy8gIOiOt+WPluS8muWRmOivpuaDhWh0bWzlhoXlrrnvvIzov5Tlm546IDxwPmhlbGxvPC9wPlxyXG4gICAgICAgICAgICB2YXIgdWUgPSBVRS5nZXRFZGl0b3IoJ2NvbnRhaW5lcicpO1xyXG4gICAgICAgICAgICBwYXJhbXNbJ29yZ19jb250ZW50J10gPSB1ZS5nZXRDb250ZW50KCk7ICAgIC8vICDljp/lp4todG1s5YaF5a65XHJcbiAgICAgICAgICAgIHBhcmFtc1snZGVzY3JiJ10gPSB1ZS5nZXRQbGFpblR4dCgpOyAgICAgICAgLy8gIOe6r+aWh+acrFxyXG5cclxuXHJcbiAgICAgICAgICAgIC8vICDojrflj5bkvJrlkZjmiYDlsZ7liIbnsbtcclxuICAgICAgICAgICAgdmFyIG1lbWJlckNhdGVnb3J5QXJyYXkgPSBbXTtcclxuICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImNhdGVnb3J5XCJdOmNoZWNrZWQnKS5lYWNoKGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgICAgICAgbWVtYmVyQ2F0ZWdvcnlBcnJheS5wdXNoKCQodGhpcykuYXR0cignaWQnKSk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgLy8gIOaYr+WQpuS4iuaetiDvvIgwOiDkuIrmnrbnirbmgIHvvJsgMe+8muS4i+aetueKtuaAge+8iVxyXG4gICAgICAgICAgICBwYXJhbXNbJ3N0YXRlJ10gPSAkKFwiaW5wdXRbbmFtZT0nc2hvd01lbWJlciddOmNoZWNrZWRcIikudmFsKCk7XHJcblxyXG4gICAgICAgICAgICAvL+aYr+WQpuaYvuekuuiuoumYheacn+aVsFxyXG4gICAgICAgICAgICAvLyBwYXJhbXNbJ2lzX3Nob3dfcmVzb3VyY2Vjb3VudCddID0gJChcImlucHV0W25hbWU9J3Nob3dNZW1iZXJDb3VudCddOmNoZWNrZWRcIikudmFsKCk7XHJcblxyXG4gICAgICAgICAgICAvLyAgMyAtPiDlj5HpgIHkv53lrZjor7fmsYLvvIhcIi9cIu+8iVxyXG5cclxuXHJcbiAgICAgICAgICAgIC8vICDor6XkuJPmoI/mmK/lkKblhbzlgZrkvJrlkZhcclxuICAgICAgICAgICAgcGFyYW1zWydpc19tZW1iZXInXSA9IDE7XHJcblxyXG4gICAgICAgICAgICAvLyAg6I635Y+W5Lya5ZGYaWRcclxuICAgICAgICAgICAgcGFyYW1zWydpZCddID0gR2V0UXVlcnlTdHJpbmcoJ2lkJyk7XHJcblxyXG5cclxuICAgICAgICAgICAgaWYgKEJ1c2luZXNzLnBhZ2VfdHlwZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAvLyAg6ZK755+z5Zu+5qCHVVJMXHJcbiAgICAgICAgICAgICAgICBwYXJhbXNbJ21lbWJlcl9pY29uX2RlZmF1bHQnXSA9IFwiaHR0cDovL3d4cmVzb3VyY2UtMTAwMTE2OTIuZmlsZS5teXFjbG91ZC5jb20vbWFudWFsL2ljb25fbWVtYmVyX2RpYW1vbmRfZ3JheS5wbmdcIjtcclxuICAgICAgICAgICAgICAgIHBhcmFtc1snbWVtYmVyX2ljb25faGlnaGxpZ2h0J10gPSBcImh0dHA6Ly93eHJlc291cmNlLTEwMDExNjkyLmZpbGUubXlxY2xvdWQuY29tL21hbnVhbC9pY29uX21lbWJlcl9kaWFtb25kLnBuZ1wiO1xyXG4gICAgICAgICAgICAgICAgbWVtYmVyVXRpbHMudXBsb2FkTWVtYmVySW5mbyhwYXJhbXMsIG1lbWJlckNhdGVnb3J5QXJyYXkpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgbWVtYmVyVXRpbHMuZWRpdGVkTWVtYmVySW5mbyhwYXJhbXMsIG1lbWJlckNhdGVnb3J5QXJyYXkpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG5cclxuICAgICAgICB9KTtcclxuXHJcbiAgICB9O1xyXG5cclxuICAgIHJldHVybiBCdXNpbmVzcztcclxuXHJcbn0pKCk7XHJcblxyXG5cclxudmFyIG1lbWJlclV0aWxzID0gKGZ1bmN0aW9uICgpIHtcclxuXHJcbiAgICB2YXIgdXRpbHMgPSB7fTtcclxuXHJcbiAgICAvLyDnvJbovpHkvJrlkZjkv6Hmga9cclxuICAgIHV0aWxzLmVkaXRlZE1lbWJlckluZm8gPSBmdW5jdGlvbiAocGFyYW1zLCBjYXRlZ29yeVR5cGVBcnIpIHtcclxuICAgICAgICBzaG93TG9hZGluZygpO1xyXG4gICAgICAgICQuYWpheChcIi9nb29kc19lZGl0X3BhY2thZ2VcIiwge1xyXG4gICAgICAgICAgICB0eXBlOiBcIlBPU1RcIixcclxuICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxyXG4gICAgICAgICAgICBkYXRhOiB7XHJcbiAgICAgICAgICAgICAgICBwYXJhbXM6IHBhcmFtcywgY2F0ZWdvcnlfdHlwZTogY2F0ZWdvcnlUeXBlQXJyXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICAgICAgICAgIGlmIChyZXN1bHQuY29kZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChcIue8lui+keS8muWRmOaIkOWKn++8gVwiKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgJChcIi5nb19iYWNrXCIpLmNsaWNrKCk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIC8vIGlmIChCdXNpbmVzcy5wYWdlX29yaWdpbiA9PSBcIm1lbWJlcl9saXN0XCIpIHtcclxuICAgICAgICAgICAgICAgICAgICAvLyAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSAnL21lbWJlcl9saXN0X3BhZ2UnO1xyXG4gICAgICAgICAgICAgICAgICAgIC8vIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgLy8gICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gJy9tZW1iZXJfZGV0YWlsX3BhZ2U/aWQ9JyArIEJ1c2luZXNzLm1lbWJlcl9pZDtcclxuICAgICAgICAgICAgICAgICAgICAvLyB9XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIGhpZGVMb2FkaW5nKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi57yW6L6R5aSx6LSl77yM6K+356iN5ZCO5YaN6K+V77yBXCIpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24gKHhociwgc3RhdHVzLCBlcnIpIHtcclxuICAgICAgICAgICAgICAgIGhpZGVMb2FkaW5nKCk7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhlcnIpO1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5pyN5Yqh5Zmo5byA5bCP5beu5LqG77yM6K+356iN5ZCO5YaN6K+V77yBXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcblxyXG5cclxuXHJcbiAgICB9O1xyXG5cclxuICAgIC8vIOaWsOW7uuS8muWRmOS/oeaBr1xyXG4gICAgdXRpbHMudXBsb2FkTWVtYmVySW5mbyA9IGZ1bmN0aW9uIChwYXJhbXMsIGNhdGVnb3J5VHlwZUFycikge1xyXG4gICAgICAgIHNob3dMb2FkaW5nKCk7XHJcbiAgICAgICAgJC5hamF4KFwiL2dvb2RzX3VwbG9hZF9wYWNrYWdlXCIsIHtcclxuICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgIGRhdGFUeXBlOiBcImpzb25cIixcclxuICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgcGFyYW1zOiBwYXJhbXMsIGNhdGVnb3J5X3R5cGU6IGNhdGVnb3J5VHlwZUFyclxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgICAgICAgICBpZiAocmVzdWx0LmNvZGUgPT0gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLmlrDlu7rkvJrlkZjmiJDlip/vvIFcIik7XHJcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSAnL21lbWJlcl9saXN0X3BhZ2UnO1xyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICBoaWRlTG9hZGluZygpO1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuaWsOW7uuWksei0pe+8jOivt+eojeWQjuWGjeivle+8gVwiKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uICh4aHIsIHN0YXR1cywgZXJyKSB7XHJcbiAgICAgICAgICAgICAgICBoaWRlTG9hZGluZygpO1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coZXJyKTtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuacjeWKoeWZqOW8gOWwj+W3ruS6hu+8jOivt+eojeWQjuWGjeivle+8gVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuXHJcbiAgICB9O1xyXG5cclxuICAgIHJldHVybiB1dGlscztcclxuXHJcbn0pKCk7Il19
