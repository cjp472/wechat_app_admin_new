var search_content;var resource_attr;$(document).ready(function(){$.cookie("content_create","");changeTab();reBack();reSearch();(function(){var e=new Clipboard(".linkCopy");e.on("success",function(e){baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");e.clearSelection()})})()});function changeTab(){$("#tab_audio").click(function(){window.location.href="/audio_list"});$("#tab_video").click(function(){window.location.href="/video_list"});$("#tab_article").click(function(){window.location.href="/article_list"});$("#tab_package").click(function(){window.location.href="/package_list"});$("#tab_alive").click(function(){window.location.href="/alive"});$("#tab_member").click(function(){window.location.href="/member_list"})}function updateResourceState(e,t,i){var n={};var o={};o["state"]=t;n["resource_type"]=e;n["id"]=i;n["params"]=o;$.post("/edit_package_save",n,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function deleteResource(e,t){window.wxc.xcConfirm("您确定要删除吗?","confirm",{onOk:function(){updateResourceState(e,2,t)}})}function updatePackageFinishedState(e,t){var i={};var n={};i["id"]=t;n["finished_state"]=e;i["params"]=n;$.post("/edit_package_finished",i,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function updatePackageFinishedStater(e,t,i){var n=$.fixedWidth(i.parent().parent().find(".item_title").text(),28);var o="提示： 专栏 "+n;o=e==0?o+" 已完结":o+" 更新中";var a=e==0?"将专栏状态设置为 更新中？":"将专栏状态设置为 已完结？";window.wxc.xcConfirm(a,"confirm",{title:o,onOk:function(){updatePackageFinishedState(e,t)}})}function updatePackageWeight(e,t){var i={};i["id"]=t;i["order_weight"]=e;$.post("/edit_package_weight",i,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function h5newestHide(e,t){var i={};i["id"]=t;i["hide_state"]=e;$.post("/h5newest_hide",i,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function h5newestHider(e,t,i){var n="提示： 专栏 "+i.parent().parent().find(".item_title").text();var o="设置为"+i.attr("title")+"？";window.wxc.xcConfirm(o,"confirm",{title:n,onOk:function(){h5newestHide(e,t)}})}function searchResource(e){var t=$("#resource_attr").val();var i=$("#resource_search_content").val().trim();var n="";if(e=="audio"){n="/audio_list?&resource_attr="+encodeURI(t)+"&search_content="+encodeURI(i)}else if(e=="video"){n="/video_list?&resource_attr="+encodeURI(t)+"&search_content="+encodeURI(i)}else if(e=="article"){n="/article_list?&resource_attr="+encodeURI(t)+"&search_content="+encodeURI(i)}window.location=n}function reBack(){if(search_content!=undefined){var e=document.getElementById("resource_search_content");if(search_content!=""){e.value=search_content;var t=document.getElementById("resource_attr");if(t.length>0){for(var i=0;i<t.options.length;i++){if(t.options[i].value==resource_attr){t.options[i].selected=true;break}}}}}}function reSearch(){$(document).keypress(function(e){if(e.which==13){$("#resource_search_btn").trigger("click")}})}(function(e){e.extend(e,{fixedWidth:function(e,t,i){e=e.toString();if(!i)i="...";var n=t-a(e);if(n<0){e=o(e,t-a(i))+i}return e;function o(e,t){var i=0,n=e.length,o="";if(n){for(var a=0;a<n;a++){if(i>t)break;if(e.charCodeAt(a)>255){i+=2;o+=e.charAt(a)}else{i++;o+=e.charAt(a)}}return o}else{return null}}function a(e){var t=0,i=e.length;if(i){for(var n=0;n<i;n++){if(e.charCodeAt(n)>255){t+=2}else{t++}}return t}else{return 0}}}})})(jQuery);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL3BhY2thZ2VMaXN0LmpzIl0sIm5hbWVzIjpbInNlYXJjaF9jb250ZW50IiwicmVzb3VyY2VfYXR0ciIsIiQiLCJkb2N1bWVudCIsInJlYWR5IiwiY29va2llIiwiY2hhbmdlVGFiIiwicmVCYWNrIiwicmVTZWFyY2giLCJjbGlwYm9hcmQiLCJDbGlwYm9hcmQiLCJvbiIsImUiLCJiYXNlVXRpbHMiLCJzaG93IiwiYmx1ZVRpcCIsImNsZWFyU2VsZWN0aW9uIiwiY2xpY2siLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJ1cGRhdGVSZXNvdXJjZVN0YXRlIiwicmVzb3VyY2VfdHlwZSIsIm5ld19zdGF0ZSIsImlkIiwiYWxsUGFyYW1zIiwicGFyYW1zIiwicG9zdCIsInJlc3VsdCIsImhpZGVMb2FkaW5nIiwiY29kZSIsIm1zZyIsInJlbG9hZCIsInJlZFRpcCIsImRlbGV0ZVJlc291cmNlIiwid3hjIiwieGNDb25maXJtIiwib25PayIsInVwZGF0ZVBhY2thZ2VGaW5pc2hlZFN0YXRlIiwidXBkYXRlUGFja2FnZUZpbmlzaGVkU3RhdGVyIiwidGhpc09iaiIsIml0ZW1UaXRsZSIsImZpeGVkV2lkdGgiLCJwYXJlbnQiLCJmaW5kIiwidGV4dCIsInJlc291cmNlVGl0bGUiLCJ0aGlzSW5mb3MiLCJ0aXRsZSIsInVwZGF0ZVBhY2thZ2VXZWlnaHQiLCJuZXdfd2VpZ2h0IiwiaDVuZXdlc3RIaWRlIiwiaGlkZV9zdGF0ZSIsImg1bmV3ZXN0SGlkZXIiLCJpbmZvcyIsImF0dHIiLCJzZWFyY2hSZXNvdXJjZSIsInZhbCIsInRyaW0iLCJ1cmwiLCJlbmNvZGVVUkkiLCJ1bmRlZmluZWQiLCJyZXNvdXJjZV9zZWFyY2hfY29udGVudCIsImdldEVsZW1lbnRCeUlkIiwidmFsdWUiLCJyZXNvdXJjZV9hdHRyX2VsZW1lbnQiLCJsZW5ndGgiLCJpIiwib3B0aW9ucyIsInNlbGVjdGVkIiwia2V5cHJlc3MiLCJ3aGljaCIsInRyaWdnZXIiLCJleHRlbmQiLCJzdHIiLCJjaGFyIiwidG9TdHJpbmciLCJudW0iLCJsZW5ndGhCIiwic3Vic3RyaW5nQiIsImxlbiIsInRlbnAiLCJjaGFyQ29kZUF0IiwiY2hhckF0IiwialF1ZXJ5Il0sIm1hcHBpbmdzIjoiQUFDQSxHQUFJQSxlQUNKLElBQUlDLGNBRUpDLEdBQUVDLFVBQVVDLE1BQU0sV0FDZEYsRUFBRUcsT0FBTyxpQkFBaUIsR0FDMUJDLFlBQ0FDLFNBQ0FDLGFBRUEsV0FDSSxHQUFJQyxHQUFZLEdBQUlDLFdBQVUsWUFDOUJELEdBQVVFLEdBQUcsVUFBVyxTQUFTQyxHQUM3QkMsVUFBVUMsS0FBS0MsUUFBUSxrQkFDdkJILEdBQUVJLHdCQXFDZCxTQUFTVixhQUNMSixFQUFFLGNBQWNlLE1BQU0sV0FDbEJDLE9BQU9DLFNBQVNDLEtBQU8sZUFFM0JsQixHQUFFLGNBQWNlLE1BQU0sV0FDbEJDLE9BQU9DLFNBQVNDLEtBQU8sZUFFM0JsQixHQUFFLGdCQUFnQmUsTUFBTSxXQUNwQkMsT0FBT0MsU0FBU0MsS0FBTyxpQkFFM0JsQixHQUFFLGdCQUFnQmUsTUFBTSxXQUNwQkMsT0FBT0MsU0FBU0MsS0FBTyxpQkFFM0JsQixHQUFFLGNBQWNlLE1BQU0sV0FDbEJDLE9BQU9DLFNBQVNDLEtBQU8sVUFFM0JsQixHQUFFLGVBQWVlLE1BQU0sV0FDbkJDLE9BQU9DLFNBQVNDLEtBQU8saUJBSy9CLFFBQVNDLHFCQUFvQkMsRUFBY0MsRUFBVUMsR0FDakQsR0FBSUMsS0FDSixJQUFJQyxLQUNKQSxHQUFPLFNBQVdILENBQ2xCRSxHQUFVLGlCQUFtQkgsQ0FDN0JHLEdBQVUsTUFBUUQsQ0FDbEJDLEdBQVUsVUFBWUMsQ0FDdEJ4QixHQUFFeUIsS0FBSyxxQkFBc0JGLEVBQVcsU0FBVUcsR0FDOUNDLGFBQ0EsSUFBSUMsR0FBT0YsRUFBT0UsSUFDbEIsSUFBSUMsR0FBTUgsRUFBT0csR0FDakIsSUFBSUQsR0FBUSxFQUNaLENBQ0lqQixVQUFVQyxLQUFLQyxRQUFRZ0IsRUFBSSxXQUFXYixPQUFPQyxTQUFTYSxVQUFVLFVBRXBFLENBQ0luQixVQUFVQyxLQUFLbUIsT0FBT0YsTUFLbEMsUUFBU0csZ0JBQWVaLEVBQWNFLEdBQ2xDTixPQUFPaUIsSUFBSUMsVUFBVSxXQUFXLFdBQVdDLEtBQUssV0FFNUNoQixvQkFBb0JDLEVBQWMsRUFBRUUsTUFJNUMsUUFBU2MsNEJBQTJCZixFQUFVQyxHQUMxQyxHQUFJQyxLQUNKLElBQUlDLEtBQ0pELEdBQVUsTUFBUUQsQ0FDbEJFLEdBQU8sa0JBQW9CSCxDQUMzQkUsR0FBVSxVQUFZQyxDQUN0QnhCLEdBQUV5QixLQUFLLHlCQUEwQkYsRUFBVyxTQUFVRyxHQUNsREMsYUFDQSxJQUFJQyxHQUFPRixFQUFPRSxJQUNsQixJQUFJQyxHQUFNSCxFQUFPRyxHQUNqQixJQUFJRCxHQUFRLEVBQUcsQ0FDWGpCLFVBQVVDLEtBQUtDLFFBQVFnQixFQUFJLFdBQWFiLE9BQU9DLFNBQVNhLFVBQVUsVUFFdEUsQ0FDSW5CLFVBQVVDLEtBQUttQixPQUFPRixNQUlsQyxRQUFTUSw2QkFBNEJoQixFQUFVQyxFQUFHZ0IsR0FDOUMsR0FBSUMsR0FBWXZDLEVBQUV3QyxXQUFXRixFQUFRRyxTQUFTQSxTQUFTQyxLQUFLLGVBQWVDLE9BQU8sR0FDbEYsSUFBSUMsR0FBZ0IsVUFBVUwsQ0FDOUJLLEdBQWdCdkIsR0FBVyxFQUFHdUIsRUFBYyxPQUFTQSxFQUFlLE1BQ3BFLElBQUlDLEdBQVl4QixHQUFXLEVBQUcsZ0JBQWtCLGVBQ2hETCxRQUFPaUIsSUFBSUMsVUFBVVcsRUFBVyxXQUFXQyxNQUFNRixFQUFjVCxLQUFLLFdBRWhFQywyQkFBMkJmLEVBQVVDLE1BSTdDLFFBQVN5QixxQkFBb0JDLEVBQVcxQixHQUNwQyxHQUFJQyxLQUNKQSxHQUFVLE1BQVFELENBQ2xCQyxHQUFVLGdCQUFrQnlCLENBQzVCaEQsR0FBRXlCLEtBQUssdUJBQXdCRixFQUFXLFNBQVVHLEdBQ2hEQyxhQUNBLElBQUlDLEdBQU9GLEVBQU9FLElBQ2xCLElBQUlDLEdBQU1ILEVBQU9HLEdBQ2pCLElBQUlELEdBQVEsRUFBRyxDQUNYakIsVUFBVUMsS0FBS0MsUUFBUWdCLEVBQUksV0FBYWIsT0FBT0MsU0FBU2EsVUFBVSxVQUV0RSxDQUNJbkIsVUFBVUMsS0FBS21CLE9BQU9GLE1BS2xDLFFBQVNvQixjQUFhQyxFQUFXNUIsR0FDN0IsR0FBSUMsS0FDSkEsR0FBVSxNQUFRRCxDQUNsQkMsR0FBVSxjQUFnQjJCLENBQzFCbEQsR0FBRXlCLEtBQUssaUJBQWtCRixFQUFXLFNBQVVHLEdBQzFDQyxhQUNBLElBQUlDLEdBQU9GLEVBQU9FLElBQ2xCLElBQUlDLEdBQU1ILEVBQU9HLEdBQ2pCLElBQUlELEdBQU8sRUFBRyxDQUNWakIsVUFBVUMsS0FBS0MsUUFBUWdCLEVBQUksV0FBYWIsT0FBT0MsU0FBU2EsVUFBVSxVQUMvRCxDQUNIbkIsVUFBVUMsS0FBS21CLE9BQU9GLE1BSWxDLFFBQVNzQixlQUFjRCxFQUFXNUIsRUFBR2dCLEdBQ2pDLEdBQUlNLEdBQWdCLFVBQVVOLEVBQVFHLFNBQVNBLFNBQVNDLEtBQUssZUFBZUMsTUFDNUUsSUFBSVMsR0FBUSxNQUFRZCxFQUFRZSxLQUFLLFNBQVcsR0FDNUNyQyxRQUFPaUIsSUFBSUMsVUFBVWtCLEVBQU8sV0FBV04sTUFBTUYsRUFBY1QsS0FBSyxXQUU1RGMsYUFBYUMsRUFBVzVCLE1BS2hDLFFBQVNnQyxnQkFBZWxDLEdBRXBCLEdBQUlyQixHQUFnQkMsRUFBRSxrQkFBa0J1RCxLQUN4QyxJQUFJekQsR0FBaUJFLEVBQUUsNEJBQTRCdUQsTUFBTUMsTUFDekQsSUFBSUMsR0FBTSxFQUNWLElBQUdyQyxHQUFlLFFBQVEsQ0FDdEJxQyxFQUFNLDhCQUFnQ0MsVUFBVTNELEdBQWlCLG1CQUFxQjJELFVBQVU1RCxPQUM5RixJQUFHc0IsR0FBZSxRQUFRLENBQzVCcUMsRUFBTSw4QkFBZ0NDLFVBQVUzRCxHQUFpQixtQkFBcUIyRCxVQUFVNUQsT0FDOUYsSUFBR3NCLEdBQWUsVUFBVSxDQUM5QnFDLEVBQU0sZ0NBQWtDQyxVQUFVM0QsR0FBaUIsbUJBQXFCMkQsVUFBVTVELEdBRXRHa0IsT0FBT0MsU0FBV3dDLEVBSXRCLFFBQVNwRCxVQUVMLEdBQUdQLGdCQUFnQjZELFVBQVUsQ0FDekIsR0FBSUMsR0FBMEIzRCxTQUFTNEQsZUFBZSwwQkFFdEQsSUFBSS9ELGdCQUFrQixHQUFJLENBQ3RCOEQsRUFBd0JFLE1BQVFoRSxjQUNoQyxJQUFJaUUsR0FBd0I5RCxTQUFTNEQsZUFBZSxnQkFDcEQsSUFBSUUsRUFBc0JDLE9BQVMsRUFBRyxDQUNsQyxJQUFLLEdBQUlDLEdBQUksRUFBR0EsRUFBSUYsRUFBc0JHLFFBQVFGLE9BQVFDLElBQUssQ0FDM0QsR0FBSUYsRUFBc0JHLFFBQVFELEdBQUdILE9BQVMvRCxjQUFlLENBQ3pEZ0UsRUFBc0JHLFFBQVFELEdBQUdFLFNBQVcsSUFDNUMsWUFVeEIsUUFBUzdELFlBRUxOLEVBQUVDLFVBQVVtRSxTQUFTLFNBQVMxRCxHQUUxQixHQUFHQSxFQUFFMkQsT0FBUyxHQUNkLENBQ0lyRSxFQUFFLHdCQUF3QnNFLFFBQVEsYUFLOUMsU0FBVXRFLEdBQ05BLEVBQUV1RSxPQUFPdkUsR0FDTHdDLFdBQVcsU0FBU2dDLEVBQUlSLEVBQU9TLEdBQzNCRCxFQUFJQSxFQUFJRSxVQUNSLEtBQUlELEVBQU1BLEVBQUssS0FDZixJQUFJRSxHQUFJWCxFQUFPWSxFQUFRSixFQUN2QixJQUFHRyxFQUFJLEVBQ1AsQ0FDSUgsRUFBSUssRUFBV0wsRUFBSVIsRUFBT1ksRUFBUUgsSUFBT0EsRUFFN0MsTUFBT0QsRUFDUCxTQUFTSyxHQUFXTCxFQUFJUixHQUVwQixHQUFJVyxHQUFJLEVBQUVHLEVBQUlOLEVBQUlSLE9BQU9lLEVBQUssRUFDOUIsSUFBR0QsRUFDSCxDQUNJLElBQUksR0FBSWIsR0FBRSxFQUFFQSxFQUFFYSxFQUFJYixJQUNsQixDQUNJLEdBQUdVLEVBQUlYLEVBQVEsS0FDZixJQUFHUSxFQUFJUSxXQUFXZixHQUFHLElBQ3JCLENBQ0lVLEdBQUssQ0FDTEksSUFBTVAsRUFBSVMsT0FBT2hCLE9BR3JCLENBQ0lVLEdBQ0FJLElBQU1QLEVBQUlTLE9BQU9oQixJQUd6QixNQUFPYyxPQUdYLENBQ0ksTUFBTyxPQUdmLFFBQVNILEdBQVFKLEdBRWIsR0FBSUcsR0FBSSxFQUFFRyxFQUFJTixFQUFJUixNQUNsQixJQUFHYyxFQUNILENBQ0ksSUFBSSxHQUFJYixHQUFFLEVBQUVBLEVBQUVhLEVBQUliLElBQ2xCLENBQ0ksR0FBR08sRUFBSVEsV0FBV2YsR0FBRyxJQUNyQixDQUNJVSxHQUFLLE1BR1QsQ0FDSUEsS0FHUixNQUFPQSxPQUdYLENBQ0ksTUFBTyxVQUt4Qk8iLCJmaWxlIjoiYWRtaW4vcGFja2FnZUxpc3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyJcclxudmFyIHNlYXJjaF9jb250ZW50O1xyXG52YXIgcmVzb3VyY2VfYXR0cjtcclxuXHJcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuICAgICQuY29va2llKCdjb250ZW50X2NyZWF0ZScsJycpO1xyXG4gICAgY2hhbmdlVGFiKCk7XHJcbiAgICByZUJhY2soKTtcclxuICAgIHJlU2VhcmNoKCk7XHJcbiAgICAvL+WkjeWItuWIsOWJqui0tOadv1xyXG4gICAgKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgY2xpcGJvYXJkID0gbmV3IENsaXBib2FyZCgnLmxpbmtDb3B5Jyk7XHJcbiAgICAgICAgY2xpcGJvYXJkLm9uKCdzdWNjZXNzJywgZnVuY3Rpb24oZSkge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi5aSN5Yi25oiQ5Yqf77yB6K+35Zyo5b6u5L+h5YaF5omT5byA5ZOmIOOAglwiKTtcclxuICAgICAgICAgICAgZS5jbGVhclNlbGVjdGlvbigpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfSkoKTtcclxuICAgIC8v5aSN5Yi25Yiw5Ymq6LS05p2/XHJcbiAgICAvLyAkKCcjY29weUJ0bjAnKS56Y2xpcCh7cGF0aDogXCJqcy9leHRlcm5hbC9aZXJvQ2xpcGJvYXJkLnN3ZlwiLFxyXG4gICAgLy8gICAgIGNvcHk6IGZ1bmN0aW9uKCl7cmV0dXJuICQoJyNwYWdlMCcpLnZhbCgpO31cclxuICAgIC8vIH0pO1xyXG4gICAgLy8gJCgnI2NvcHlCdG4xJykuemNsaXAoe3BhdGg6IFwianMvZXh0ZXJuYWwvWmVyb0NsaXBib2FyZC5zd2ZcIixcclxuICAgIC8vICAgICBjb3B5OiBmdW5jdGlvbigpe3JldHVybiAkKCcjcGFnZTEnKS52YWwoKTt9XHJcbiAgICAvLyB9KTtcclxuICAgIC8vICQoJyNjb3B5QnRuMicpLnpjbGlwKHtwYXRoOiBcImpzL2V4dGVybmFsL1plcm9DbGlwYm9hcmQuc3dmXCIsXHJcbiAgICAvLyAgICAgY29weTogZnVuY3Rpb24oKXtyZXR1cm4gJCgnI3BhZ2UyJykudmFsKCk7fVxyXG4gICAgLy8gfSk7XHJcbiAgICAvLyAkKCcjY29weUJ0bjMnKS56Y2xpcCh7cGF0aDogXCJqcy9leHRlcm5hbC9aZXJvQ2xpcGJvYXJkLnN3ZlwiLFxyXG4gICAgLy8gICAgIGNvcHk6IGZ1bmN0aW9uKCl7cmV0dXJuICQoJyNwYWdlMycpLnZhbCgpO31cclxuICAgIC8vIH0pO1xyXG4gICAgLy8gJCgnI2NvcHlCdG40JykuemNsaXAoe3BhdGg6IFwianMvZXh0ZXJuYWwvWmVyb0NsaXBib2FyZC5zd2ZcIixcclxuICAgIC8vICAgICBjb3B5OiBmdW5jdGlvbigpe3JldHVybiAkKCcjcGFnZTQnKS52YWwoKTt9XHJcbiAgICAvLyB9KTtcclxuICAgIC8vICQoJyNjb3B5QnRuNScpLnpjbGlwKHtwYXRoOiBcImpzL2V4dGVybmFsL1plcm9DbGlwYm9hcmQuc3dmXCIsXHJcbiAgICAvLyAgICAgY29weTogZnVuY3Rpb24oKXtyZXR1cm4gJCgnI3BhZ2U1JykudmFsKCk7fVxyXG4gICAgLy8gfSk7XHJcbiAgICAvLyAkKCcjY29weUJ0bjYnKS56Y2xpcCh7cGF0aDogXCJqcy9leHRlcm5hbC9aZXJvQ2xpcGJvYXJkLnN3ZlwiLFxyXG4gICAgLy8gICAgIGNvcHk6IGZ1bmN0aW9uKCl7cmV0dXJuICQoJyNwYWdlNicpLnZhbCgpO31cclxuICAgIC8vIH0pO1xyXG4gICAgLy8gJCgnI2NvcHlCdG43JykuemNsaXAoe3BhdGg6IFwianMvZXh0ZXJuYWwvWmVyb0NsaXBib2FyZC5zd2ZcIixcclxuICAgIC8vICAgICBjb3B5OiBmdW5jdGlvbigpe3JldHVybiAkKCcjcGFnZTcnKS52YWwoKTt9XHJcbiAgICAvLyB9KTtcclxuICAgIC8vICQoJyNjb3B5QnRuOCcpLnpjbGlwKHtwYXRoOiBcImpzL2V4dGVybmFsL1plcm9DbGlwYm9hcmQuc3dmXCIsXHJcbiAgICAvLyAgICAgY29weTogZnVuY3Rpb24oKXtyZXR1cm4gJCgnI3BhZ2U4JykudmFsKCk7fVxyXG4gICAgLy8gfSk7XHJcbiAgICAvLyAkKCcjY29weUJ0bjknKS56Y2xpcCh7cGF0aDogXCJqcy9leHRlcm5hbC9aZXJvQ2xpcGJvYXJkLnN3ZlwiLFxyXG4gICAgLy8gICAgIGNvcHk6IGZ1bmN0aW9uKCl7cmV0dXJuICQoJyNwYWdlOScpLnZhbCgpO31cclxuICAgIC8vIH0pO1xyXG5cclxufSk7XHJcblxyXG5mdW5jdGlvbiBjaGFuZ2VUYWIoKSB7XHJcbiAgICAkKFwiI3RhYl9hdWRpb1wiKS5jbGljayhmdW5jdGlvbiAoKSB7IC8vIOmfs+mikeWIl+ihqFxyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvYXVkaW9fbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl92aWRlb1wiKS5jbGljayhmdW5jdGlvbiAoKSB7IC8vIOinhumikeWIl+ihqFxyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvdmlkZW9fbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9hcnRpY2xlXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHsvLyDlm77mlofliJfooahcclxuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL2FydGljbGVfbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9wYWNrYWdlXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHsvLyDkuJPmoI/liJfooahcclxuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL3BhY2thZ2VfbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9hbGl2ZVwiKS5jbGljayhmdW5jdGlvbiAoKSB7Ly8g55u05pKt5YiX6KGoXHJcbiAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBcIi9hbGl2ZVwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9tZW1iZXJcIikuY2xpY2soZnVuY3Rpb24gKCkgey8vIOS8muWRmOmhtVxyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvbWVtYmVyX2xpc3RcIjtcclxuICAgIH0pO1xyXG59XHJcblxyXG4vL+abtOaWsOi1hOa6kOi3r+W+hFxyXG5mdW5jdGlvbiB1cGRhdGVSZXNvdXJjZVN0YXRlKHJlc291cmNlX3R5cGUsbmV3X3N0YXRlLGlkKSB7XHJcbiAgICB2YXIgYWxsUGFyYW1zID0ge307XHJcbiAgICB2YXIgcGFyYW1zID0ge307XHJcbiAgICBwYXJhbXNbJ3N0YXRlJ10gPSBuZXdfc3RhdGU7XHJcbiAgICBhbGxQYXJhbXNbJ3Jlc291cmNlX3R5cGUnXSA9IHJlc291cmNlX3R5cGU7XHJcbiAgICBhbGxQYXJhbXNbJ2lkJ10gPSBpZDtcclxuICAgIGFsbFBhcmFtc1sncGFyYW1zJ10gPSBwYXJhbXM7XHJcbiAgICAkLnBvc3QoJy9lZGl0X3BhY2thZ2Vfc2F2ZScsIGFsbFBhcmFtcywgZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgIGhpZGVMb2FkaW5nKCk7XHJcbiAgICAgICAgdmFyIGNvZGUgPSByZXN1bHQuY29kZTtcclxuICAgICAgICB2YXIgbXNnID0gcmVzdWx0Lm1zZztcclxuICAgICAgICBpZiAoY29kZSA9PSAwKVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChtc2csZnVuY3Rpb24oKXt3aW5kb3cubG9jYXRpb24ucmVsb2FkKCl9LDE1MDApO1xyXG4gICAgICAgIH0gZWxzZVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKG1zZyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbn1cclxuLy/liKDpmaTotYTmupDmk43kvZxcclxuZnVuY3Rpb24gZGVsZXRlUmVzb3VyY2UocmVzb3VyY2VfdHlwZSxpZCl7XHJcbiAgICB3aW5kb3cud3hjLnhjQ29uZmlybShcIuaCqOehruWumuimgeWIoOmZpOWQlz9cIixcImNvbmZpcm1cIix7b25PazpmdW5jdGlvbigpXHJcbiAgICB7XHJcbiAgICAgICAgdXBkYXRlUmVzb3VyY2VTdGF0ZShyZXNvdXJjZV90eXBlLDIsaWQpO1xyXG4gICAgfX0pO1xyXG59XHJcbi8v5pu05paw5LiT5qCP5a6M57uT54q25oCBXHJcbmZ1bmN0aW9uIHVwZGF0ZVBhY2thZ2VGaW5pc2hlZFN0YXRlKG5ld19zdGF0ZSxpZCkge1xyXG4gICAgdmFyIGFsbFBhcmFtcyA9e307XHJcbiAgICB2YXIgcGFyYW1zID0ge307XHJcbiAgICBhbGxQYXJhbXNbJ2lkJ10gPSBpZDtcclxuICAgIHBhcmFtc1snZmluaXNoZWRfc3RhdGUnXSA9IG5ld19zdGF0ZTtcclxuICAgIGFsbFBhcmFtc1sncGFyYW1zJ10gPSBwYXJhbXM7XHJcbiAgICAkLnBvc3QoJy9lZGl0X3BhY2thZ2VfZmluaXNoZWQnLCBhbGxQYXJhbXMsIGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICBoaWRlTG9hZGluZygpO1xyXG4gICAgICAgIHZhciBjb2RlID0gcmVzdWx0LmNvZGU7XHJcbiAgICAgICAgdmFyIG1zZyA9IHJlc3VsdC5tc2c7XHJcbiAgICAgICAgaWYgKGNvZGUgPT0gMCkge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKG1zZyxmdW5jdGlvbiAoKSB7d2luZG93LmxvY2F0aW9uLnJlbG9hZCgpfSwxNTAwKTtcclxuICAgICAgICB9IGVsc2VcclxuICAgICAgICB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChtc2cpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59XHJcbmZ1bmN0aW9uIHVwZGF0ZVBhY2thZ2VGaW5pc2hlZFN0YXRlcihuZXdfc3RhdGUsaWQsdGhpc09iaikge1xyXG4gICAgdmFyIGl0ZW1UaXRsZSA9ICQuZml4ZWRXaWR0aCh0aGlzT2JqLnBhcmVudCgpLnBhcmVudCgpLmZpbmQoXCIuaXRlbV90aXRsZVwiKS50ZXh0KCksMjgpO1xyXG4gICAgdmFyIHJlc291cmNlVGl0bGUgPSBcIuaPkOekuu+8miDkuJPmoI8gXCIraXRlbVRpdGxlO1xyXG4gICAgcmVzb3VyY2VUaXRsZSA9IG5ld19zdGF0ZT09MD8gcmVzb3VyY2VUaXRsZStcIiDlt7Llroznu5NcIiA6IHJlc291cmNlVGl0bGUrIFwiIOabtOaWsOS4rVwiO1xyXG4gICAgdmFyIHRoaXNJbmZvcyA9IG5ld19zdGF0ZT09MD8gXCLlsIbkuJPmoI/nirbmgIHorr7nva7kuLog5pu05paw5Lit77yfXCIgOiBcIuWwhuS4k+agj+eKtuaAgeiuvue9ruS4uiDlt7Llroznu5PvvJ9cIjtcclxuICAgIHdpbmRvdy53eGMueGNDb25maXJtKHRoaXNJbmZvcywgJ2NvbmZpcm0nLHt0aXRsZTpyZXNvdXJjZVRpdGxlLG9uT2s6ZnVuY3Rpb24oKVxyXG4gICAge1xyXG4gICAgICAgIHVwZGF0ZVBhY2thZ2VGaW5pc2hlZFN0YXRlKG5ld19zdGF0ZSxpZCk7XHJcbiAgICB9fSk7XHJcbn1cclxuLy/mm7TmlrDkuJPmoI/mjpLluo/mk43kvZxcclxuZnVuY3Rpb24gdXBkYXRlUGFja2FnZVdlaWdodChuZXdfd2VpZ2h0LGlkKSB7XHJcbiAgICB2YXIgYWxsUGFyYW1zID17fTtcclxuICAgIGFsbFBhcmFtc1snaWQnXSA9IGlkO1xyXG4gICAgYWxsUGFyYW1zWydvcmRlcl93ZWlnaHQnXSA9IG5ld193ZWlnaHQ7XHJcbiAgICAkLnBvc3QoJy9lZGl0X3BhY2thZ2Vfd2VpZ2h0JywgYWxsUGFyYW1zLCBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICB2YXIgY29kZSA9IHJlc3VsdC5jb2RlO1xyXG4gICAgICAgIHZhciBtc2cgPSByZXN1bHQubXNnO1xyXG4gICAgICAgIGlmIChjb2RlID09IDApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChtc2csZnVuY3Rpb24gKCkge3dpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKX0sMTUwMCk7XHJcbiAgICAgICAgfSBlbHNlXHJcbiAgICAgICAge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAobXNnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufVxyXG4vL+aYvuekuuaIlumakOiXj+S4k+agj+eahOacgOaWsOaYvuekulxyXG5mdW5jdGlvbiBoNW5ld2VzdEhpZGUoaGlkZV9zdGF0ZSxpZCl7XHJcbiAgICB2YXIgYWxsUGFyYW1zID17fTtcclxuICAgIGFsbFBhcmFtc1snaWQnXSA9IGlkO1xyXG4gICAgYWxsUGFyYW1zWydoaWRlX3N0YXRlJ10gPSBoaWRlX3N0YXRlOy8vXHJcbiAgICAkLnBvc3QoJy9oNW5ld2VzdF9oaWRlJywgYWxsUGFyYW1zLCBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICB2YXIgY29kZSA9IHJlc3VsdC5jb2RlO1xyXG4gICAgICAgIHZhciBtc2cgPSByZXN1bHQubXNnO1xyXG4gICAgICAgIGlmIChjb2RlID09MCkge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKG1zZyxmdW5jdGlvbiAoKSB7d2luZG93LmxvY2F0aW9uLnJlbG9hZCgpfSwxNTAwKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAobXNnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufVxyXG5mdW5jdGlvbiBoNW5ld2VzdEhpZGVyKGhpZGVfc3RhdGUsaWQsdGhpc09iail7XHJcbiAgICB2YXIgcmVzb3VyY2VUaXRsZSA9IFwi5o+Q56S677yaIOS4k+agjyBcIit0aGlzT2JqLnBhcmVudCgpLnBhcmVudCgpLmZpbmQoXCIuaXRlbV90aXRsZVwiKS50ZXh0KCk7XHJcbiAgICB2YXIgaW5mb3MgPSBcIuiuvue9ruS4ulwiICsgdGhpc09iai5hdHRyKCd0aXRsZScpICsgXCLvvJ9cIjtcclxuICAgIHdpbmRvdy53eGMueGNDb25maXJtKGluZm9zLCAnY29uZmlybScse3RpdGxlOnJlc291cmNlVGl0bGUsb25PazpmdW5jdGlvbigpXHJcbiAgICB7XHJcbiAgICAgICAgaDVuZXdlc3RIaWRlKGhpZGVfc3RhdGUsaWQpO1xyXG4gICAgfX0pO1xyXG59XHJcblxyXG4vL+i1hOa6kOaQnOe0olxyXG5mdW5jdGlvbiBzZWFyY2hSZXNvdXJjZShyZXNvdXJjZV90eXBlKXtcclxuICAgIC8v5Y+W6YCJ5oup5a2X5q615ZKM5YaF5a65XHJcbiAgICB2YXIgcmVzb3VyY2VfYXR0ciA9ICQoJyNyZXNvdXJjZV9hdHRyJykudmFsKCk7XHJcbiAgICB2YXIgc2VhcmNoX2NvbnRlbnQgPSAkKCcjcmVzb3VyY2Vfc2VhcmNoX2NvbnRlbnQnKS52YWwoKS50cmltKCk7XHJcbiAgICB2YXIgdXJsID0gJyc7XHJcbiAgICBpZihyZXNvdXJjZV90eXBlPT0nYXVkaW8nKXtcclxuICAgICAgICB1cmwgPSBcIi9hdWRpb19saXN0PyZyZXNvdXJjZV9hdHRyPVwiICsgZW5jb2RlVVJJKHJlc291cmNlX2F0dHIpICsgXCImc2VhcmNoX2NvbnRlbnQ9XCIgKyBlbmNvZGVVUkkoc2VhcmNoX2NvbnRlbnQpO1xyXG4gICAgfWVsc2UgaWYocmVzb3VyY2VfdHlwZT09J3ZpZGVvJyl7XHJcbiAgICAgICAgdXJsID0gXCIvdmlkZW9fbGlzdD8mcmVzb3VyY2VfYXR0cj1cIiArIGVuY29kZVVSSShyZXNvdXJjZV9hdHRyKSArIFwiJnNlYXJjaF9jb250ZW50PVwiICsgZW5jb2RlVVJJKHNlYXJjaF9jb250ZW50KTtcclxuICAgIH1lbHNlIGlmKHJlc291cmNlX3R5cGU9PSdhcnRpY2xlJyl7XHJcbiAgICAgICAgdXJsID0gXCIvYXJ0aWNsZV9saXN0PyZyZXNvdXJjZV9hdHRyPVwiICsgZW5jb2RlVVJJKHJlc291cmNlX2F0dHIpICsgXCImc2VhcmNoX2NvbnRlbnQ9XCIgKyBlbmNvZGVVUkkoc2VhcmNoX2NvbnRlbnQpO1xyXG4gICAgfVxyXG4gICAgd2luZG93LmxvY2F0aW9uID0gdXJsO1xyXG59XHJcblxyXG4vL+WbnuaYvuaQnOe0ouahhuWGheeahOWAvFxyXG5mdW5jdGlvbiByZUJhY2soKSB7XHJcblxyXG4gICAgaWYoc2VhcmNoX2NvbnRlbnQhPXVuZGVmaW5lZCl7IC8v5LiT5qCP5Lya5Ye6546w6L+Z56eN5oOF5Ya1flxyXG4gICAgICAgIHZhciByZXNvdXJjZV9zZWFyY2hfY29udGVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwicmVzb3VyY2Vfc2VhcmNoX2NvbnRlbnRcIik7XHJcblxyXG4gICAgICAgIGlmIChzZWFyY2hfY29udGVudCAhPSBcIlwiKSB7XHJcbiAgICAgICAgICAgIHJlc291cmNlX3NlYXJjaF9jb250ZW50LnZhbHVlID0gc2VhcmNoX2NvbnRlbnQ7XHJcbiAgICAgICAgICAgIHZhciByZXNvdXJjZV9hdHRyX2VsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcInJlc291cmNlX2F0dHJcIik7XHJcbiAgICAgICAgICAgIGlmIChyZXNvdXJjZV9hdHRyX2VsZW1lbnQubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCByZXNvdXJjZV9hdHRyX2VsZW1lbnQub3B0aW9ucy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgIGlmIChyZXNvdXJjZV9hdHRyX2VsZW1lbnQub3B0aW9uc1tpXS52YWx1ZSA9PSByZXNvdXJjZV9hdHRyKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc291cmNlX2F0dHJfZWxlbWVudC5vcHRpb25zW2ldLnNlbGVjdGVkID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxufVxyXG5cclxuLy/liJfooajmkJzntKLmoYblm57ovabop6blj5HmkJzntKJcclxuZnVuY3Rpb24gcmVTZWFyY2goKSB7XHJcbiAgICAvL+Wbnui9puaQnOe0olxyXG4gICAgJChkb2N1bWVudCkua2V5cHJlc3MoZnVuY3Rpb24oZSlcclxuICAgIHtcclxuICAgICAgICBpZihlLndoaWNoID09IDEzKVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgJCgnI3Jlc291cmNlX3NlYXJjaF9idG4nKS50cmlnZ2VyKFwiY2xpY2tcIik7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbn1cclxuXHJcbihmdW5jdGlvbigkKXtcclxuICAgICQuZXh0ZW5kKCQse1xyXG4gICAgICAgIGZpeGVkV2lkdGg6ZnVuY3Rpb24oc3RyLGxlbmd0aCxjaGFyKXtcclxuICAgICAgICAgICAgc3RyPXN0ci50b1N0cmluZygpO1xyXG4gICAgICAgICAgICBpZighY2hhcikgY2hhcj1cIi4uLlwiO1xyXG4gICAgICAgICAgICB2YXIgbnVtPWxlbmd0aC1sZW5ndGhCKHN0cik7XHJcbiAgICAgICAgICAgIGlmKG51bTwwKVxyXG4gICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICBzdHI9c3Vic3RyaW5nQihzdHIsbGVuZ3RoLWxlbmd0aEIoY2hhcikpK2NoYXI7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgcmV0dXJuIHN0cjtcclxuICAgICAgICAgICAgZnVuY3Rpb24gc3Vic3RyaW5nQihzdHIsbGVuZ3RoKVxyXG4gICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICB2YXIgbnVtPTAsbGVuPXN0ci5sZW5ndGgsdGVucD1cIlwiO1xyXG4gICAgICAgICAgICAgICAgaWYobGVuKVxyXG4gICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8bGVuO2krKylcclxuICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKG51bT5sZW5ndGgpIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihzdHIuY2hhckNvZGVBdChpKT4yNTUpXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG51bSs9MjtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRlbnArPXN0ci5jaGFyQXQoaSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgZWxzZVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBudW0rKztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRlbnArPXN0ci5jaGFyQXQoaSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRlbnA7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBlbHNlXHJcbiAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG51bGw7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgZnVuY3Rpb24gbGVuZ3RoQihzdHIpXHJcbiAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgIHZhciBudW09MCxsZW49c3RyLmxlbmd0aDtcclxuICAgICAgICAgICAgICAgIGlmKGxlbilcclxuICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPGxlbjtpKyspXHJcbiAgICAgICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihzdHIuY2hhckNvZGVBdChpKT4yNTUpXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG51bSs9MjtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICBlbHNlXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG51bSsrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBudW07XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBlbHNlXHJcbiAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIDA7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufSkoalF1ZXJ5KTtcclxuXHJcbi8vIHZhciBzdHI9XCLluIzmnJvmgqjlnKjov5nph4zog73lpJ/mnInkuIDlrprnmoTmlLbojrdcIjtcclxuLy8gZG9jdW1lbnQud3JpdGUoJC5maXhlZFdpZHRoKHN0ciwyMCkpOyJdfQ==