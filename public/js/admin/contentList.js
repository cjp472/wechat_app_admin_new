var search_content;var resource_attr;$(document).ready(function(){$.cookie("content_create","");changeTab();reBack();reSearch();$("#closed").click(function(){$(".xcConfirm").hide()});$(".count_glyphicon").on("mouseover",function(){$(this).find(".dropdown-menu").show()}).on("mouseleave",function(){$(this).find(".dropdown-menu").hide()});(function(){var e=new Clipboard(".copyHref");e.on("success",function(e){baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");e.clearSelection()})})()});function changeTab(){$("#tab_audio").click(function(){window.location.href="/audio_list"});$("#tab_video").click(function(){window.location.href="/video_list"});$("#tab_article").click(function(){window.location.href="/article_list"});$("#tab_package").click(function(){window.location.href="/package_list"});$("#tab_alive").click(function(){window.location.href="/alive"});$("#tab_member").click(function(){window.location.href="/member_list"})}function updateResourceState(e,t,i){var o={};var n={};if(e=="audio"){n["audio_state"]=t}else if(e=="video"){n["video_state"]=t}else if(e=="article"){n["display_state"]=t}else{n["state"]=t}o["resource_type"]=e;o["id"]=i;o["params"]=n;$.post("/edit_resource_save",o,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function deleteResource(e,t){window.wxc.xcConfirm("您确定要删除吗?","confirm",{onOk:function(){updateResourceState(e,2,t)}})}function updatePackageFinishedState(e,t){var i={};var o={};i["id"]=t;o["finished_state"]=e;i["params"]=o;$.post("/edit_package_finished",i,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function updatePackageFinishedStater(e,t,i){var o="提示： 专栏 "+i.parent().parent().find(".item_title").text();o=e==0?o+" 已完结":o+" 更新中";var n=e==0?"将专栏状态设置为 更新中？":"将专栏状态设置为 已完结？";window.wxc.xcConfirm(n,"confirm",{title:o,onOk:function(){updatePackageFinishedState(e,t)}})}function updatePackageWeight(e,t){var i={};i["id"]=t;i["order_weight"]=e;$.post("/edit_package_weight",i,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function h5newestHide(e,t){var i={};i["id"]=t;i["hide_state"]=e;$.post("/h5newest_hide",i,function(e){hideLoading();var t=e.code;var i=e.msg;if(t==0){baseUtils.show.blueTip(i,function(){window.location.reload()},1500)}else{baseUtils.show.redTip(i)}})}function h5newestHider(e,t,i){var o="提示： 专栏 "+i.parent().parent().find(".item_title").text();var n="设置为"+i.attr("title")+"？";window.wxc.xcConfirm(n,"confirm",{title:o,onOk:function(){h5newestHide(e,t)}})}function searchResource(e){var t=$("#resource_attr").val();var i=$("#resource_search_content").val().trim();var o="";if(e=="audio"){o="/audio_list?&resource_attr="+encodeURI(t)+"&search_content="+encodeURI(i)}else if(e=="video"){o="/video_list?&resource_attr="+encodeURI(t)+"&search_content="+encodeURI(i)}else if(e=="article"){o="/article_list?&resource_attr="+encodeURI(t)+"&search_content="+encodeURI(i)}window.location=o}function reBack(){if(search_content!=undefined){var e=document.getElementById("resource_search_content");if(search_content!=""){e.value=search_content;var t=document.getElementById("resource_attr");if(t.length>0){for(var i=0;i<t.options.length;i++){if(t.options[i].value==resource_attr){t.options[i].selected=true;break}}}}}}function reSearch(){$(document).keypress(function(e){if(e.which==13){$("#resource_search_btn").trigger("click")}})}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2NvbnRlbnRMaXN0LmpzIl0sIm5hbWVzIjpbInNlYXJjaF9jb250ZW50IiwicmVzb3VyY2VfYXR0ciIsIiQiLCJkb2N1bWVudCIsInJlYWR5IiwiY29va2llIiwiY2hhbmdlVGFiIiwicmVCYWNrIiwicmVTZWFyY2giLCJjbGljayIsImhpZGUiLCJvbiIsInRoaXMiLCJmaW5kIiwic2hvdyIsImNsaXBib2FyZCIsIkNsaXBib2FyZCIsImUiLCJiYXNlVXRpbHMiLCJibHVlVGlwIiwiY2xlYXJTZWxlY3Rpb24iLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJ1cGRhdGVSZXNvdXJjZVN0YXRlIiwicmVzb3VyY2VfdHlwZSIsIm5ld19zdGF0ZSIsImlkIiwiYWxsUGFyYW1zIiwicGFyYW1zIiwicG9zdCIsInJlc3VsdCIsImhpZGVMb2FkaW5nIiwiY29kZSIsIm1zZyIsInJlbG9hZCIsInJlZFRpcCIsImRlbGV0ZVJlc291cmNlIiwid3hjIiwieGNDb25maXJtIiwib25PayIsInVwZGF0ZVBhY2thZ2VGaW5pc2hlZFN0YXRlIiwidXBkYXRlUGFja2FnZUZpbmlzaGVkU3RhdGVyIiwidGhpc09iaiIsInJlc291cmNlVGl0bGUiLCJwYXJlbnQiLCJ0ZXh0IiwidGhpc0luZm9zIiwidGl0bGUiLCJ1cGRhdGVQYWNrYWdlV2VpZ2h0IiwibmV3X3dlaWdodCIsImg1bmV3ZXN0SGlkZSIsImhpZGVfc3RhdGUiLCJoNW5ld2VzdEhpZGVyIiwiaW5mb3MiLCJhdHRyIiwic2VhcmNoUmVzb3VyY2UiLCJ2YWwiLCJ0cmltIiwidXJsIiwiZW5jb2RlVVJJIiwidW5kZWZpbmVkIiwicmVzb3VyY2Vfc2VhcmNoX2NvbnRlbnQiLCJnZXRFbGVtZW50QnlJZCIsInZhbHVlIiwicmVzb3VyY2VfYXR0cl9lbGVtZW50IiwibGVuZ3RoIiwiaSIsIm9wdGlvbnMiLCJzZWxlY3RlZCIsImtleXByZXNzIiwid2hpY2giLCJ0cmlnZ2VyIl0sIm1hcHBpbmdzIjoiQUFDQSxH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RUFBTSw4QkFBZ0NDLFVBQVUzRCxHQUFpQixtQkFBcUIyRCxVQUFVNUQsT0FDOUYsSUFBR3lCLEdBQWUsVUFBVSxDQUM5QmtDLEVBQU0sZ0NBQWtDQyxVQUFVM0QsR0FBaUIsbUJBQXFCMkQsVUFBVTVELEdBRXRHcUIsT0FBT0MsU0FBV3FDLEVBSXRCLFFBQVNwRCxVQUVMLEdBQUdQLGdCQUFnQjZELFVBQVUsQ0FDekIsR0FBSUMsR0FBMEIzRCxTQUFTNEQsZUFBZSwwQkFFdEQsSUFBSS9ELGdCQUFrQixHQUFJLENBQ3RCOEQsRUFBd0JFLE1BQVFoRSxjQUNoQyxJQUFJaUUsR0FBd0I5RCxTQUFTNEQsZUFBZSxnQkFDcEQsSUFBSUUsRUFBc0JDLE9BQVMsRUFBRyxDQUNsQyxJQUFLLEdBQUlDLEdBQUksRUFBR0EsRUFBSUYsRUFBc0JHLFFBQVFGLE9BQVFDLElBQUssQ0FDM0QsR0FBSUYsRUFBc0JHLFFBQVFELEdBQUdILE9BQVMvRCxjQUFlLENBQ3pEZ0UsRUFBc0JHLFFBQVFELEdBQUdFLFNBQVcsSUFDNUMsWUFVeEIsUUFBUzdELFlBRUxOLEVBQUVDLFVBQVVtRSxTQUFTLFNBQVNyRCxHQUUxQixHQUFHQSxFQUFFc0QsT0FBUyxHQUNkLENBQ0lyRSxFQUFFLHdCQUF3QnNFLFFBQVEiLCJmaWxlIjoiYWRtaW4vY29udGVudExpc3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyJcclxudmFyIHNlYXJjaF9jb250ZW50O1xyXG52YXIgcmVzb3VyY2VfYXR0cjtcclxuXHJcblxyXG5cclxuXHJcblxyXG4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbiAoKSB7XHJcbiAgICAkLmNvb2tpZSgnY29udGVudF9jcmVhdGUnLCcnKTtcclxuICAgIGNoYW5nZVRhYigpO1xyXG4gICAgcmVCYWNrKCk7XHJcbiAgICByZVNlYXJjaCgpO1xyXG5cclxuICAgICQoJyNjbG9zZWQnKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICQoJy54Y0NvbmZpcm0nKS5oaWRlKCk7XHJcbiAgICB9KTtcclxuXHJcblxyXG4gICAgJCgnLmNvdW50X2dseXBoaWNvbicpLm9uKCdtb3VzZW92ZXInLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnNob3coKTtcclxuICAgIH0pLm9uKCdtb3VzZWxlYXZlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5oaWRlKCk7XHJcbiAgICB9KTtcclxuXHJcblxyXG4gICAgLy/lpI3liLbliLDliarotLTmnb9cclxuICAgIChmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGNsaXBib2FyZCA9IG5ldyBDbGlwYm9hcmQoJy5jb3B5SHJlZicpO1xyXG4gICAgICAgIGNsaXBib2FyZC5vbignc3VjY2VzcycsIGZ1bmN0aW9uKGUpIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChcIuWkjeWItuaIkOWKn++8geivt+WcqOW+ruS/oeWGheaJk+W8gOWTpiDjgIJcIik7XHJcbiAgICAgICAgICAgIGUuY2xlYXJTZWxlY3Rpb24oKTtcclxuICAgICAgICB9KTtcclxuICAgIH0pKCk7XHJcblxyXG5cclxufSk7XHJcblxyXG5mdW5jdGlvbiBjaGFuZ2VUYWIoKSB7XHJcbiAgICAkKFwiI3RhYl9hdWRpb1wiKS5jbGljayhmdW5jdGlvbiAoKSB7IC8vIOmfs+mikeWIl+ihqFxyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvYXVkaW9fbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl92aWRlb1wiKS5jbGljayhmdW5jdGlvbiAoKSB7IC8vIOinhumikeWIl+ihqFxyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvdmlkZW9fbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9hcnRpY2xlXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHsvLyDlm77mlofliJfooahcclxuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL2FydGljbGVfbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9wYWNrYWdlXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHsvLyDkuJPmoI/liJfooahcclxuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IFwiL3BhY2thZ2VfbGlzdFwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9hbGl2ZVwiKS5jbGljayhmdW5jdGlvbiAoKSB7Ly8g55u05pKt5YiX6KGoXHJcbiAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBcIi9hbGl2ZVwiO1xyXG4gICAgfSk7XHJcbiAgICAkKFwiI3RhYl9tZW1iZXJcIikuY2xpY2soZnVuY3Rpb24gKCkgey8vIOS8muWRmOmhtVxyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvbWVtYmVyX2xpc3RcIjtcclxuICAgIH0pO1xyXG59XHJcblxyXG4vL+abtOaWsOi1hOa6kOi3r+W+hFxyXG5mdW5jdGlvbiB1cGRhdGVSZXNvdXJjZVN0YXRlKHJlc291cmNlX3R5cGUsbmV3X3N0YXRlLGlkKSB7XHJcbiAgICB2YXIgYWxsUGFyYW1zID0ge307XHJcbiAgICB2YXIgcGFyYW1zID0ge307XHJcbiAgICBpZihyZXNvdXJjZV90eXBlPT0nYXVkaW8nKXtcclxuICAgICAgICBwYXJhbXNbJ2F1ZGlvX3N0YXRlJ10gPSBuZXdfc3RhdGU7XHJcbiAgICB9ZWxzZSBpZihyZXNvdXJjZV90eXBlPT0ndmlkZW8nKXtcclxuICAgICAgICBwYXJhbXNbJ3ZpZGVvX3N0YXRlJ10gPSBuZXdfc3RhdGU7XHJcbiAgICB9ZWxzZSBpZihyZXNvdXJjZV90eXBlPT0nYXJ0aWNsZScpe1xyXG4gICAgICAgIHBhcmFtc1snZGlzcGxheV9zdGF0ZSddID0gbmV3X3N0YXRlO1xyXG4gICAgfVxyXG4gICAgZWxzZXtcclxuICAgICAgICBwYXJhbXNbJ3N0YXRlJ10gPSBuZXdfc3RhdGU7XHJcbiAgICB9XHJcbiAgICBhbGxQYXJhbXNbJ3Jlc291cmNlX3R5cGUnXSA9IHJlc291cmNlX3R5cGU7XHJcbiAgICBhbGxQYXJhbXNbJ2lkJ10gPSBpZDtcclxuICAgIGFsbFBhcmFtc1sncGFyYW1zJ10gPSBwYXJhbXM7XHJcbiAgICAkLnBvc3QoJy9lZGl0X3Jlc291cmNlX3NhdmUnLCBhbGxQYXJhbXMsIGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICBoaWRlTG9hZGluZygpO1xyXG4gICAgICAgIHZhciBjb2RlID0gcmVzdWx0LmNvZGU7XHJcbiAgICAgICAgdmFyIG1zZyA9IHJlc3VsdC5tc2c7XHJcbiAgICAgICAgaWYgKGNvZGUgPT0gMClcclxuICAgICAgICB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAobXNnLGZ1bmN0aW9uKCl7d2luZG93LmxvY2F0aW9uLnJlbG9hZCgpfSwxNTAwKTtcclxuICAgICAgICB9IGVsc2VcclxuICAgICAgICB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChtc2cpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59XHJcbi8v5Yig6Zmk6LWE5rqQ5pON5L2cXHJcbmZ1bmN0aW9uIGRlbGV0ZVJlc291cmNlKHJlc291cmNlX3R5cGUsaWQpe1xyXG4gICAgd2luZG93Lnd4Yy54Y0NvbmZpcm0oXCLmgqjnoa7lrpropoHliKDpmaTlkJc/XCIsXCJjb25maXJtXCIse29uT2s6ZnVuY3Rpb24oKVxyXG4gICAge1xyXG4gICAgICAgIHVwZGF0ZVJlc291cmNlU3RhdGUocmVzb3VyY2VfdHlwZSwyLGlkKTtcclxuICAgIH19KTtcclxufVxyXG4vL+abtOaWsOS4k+agj+WujOe7k+eKtuaAgVxyXG5mdW5jdGlvbiB1cGRhdGVQYWNrYWdlRmluaXNoZWRTdGF0ZShuZXdfc3RhdGUsaWQpIHtcclxuICAgIHZhciBhbGxQYXJhbXMgPXt9O1xyXG4gICAgdmFyIHBhcmFtcyA9IHt9O1xyXG4gICAgYWxsUGFyYW1zWydpZCddID0gaWQ7XHJcbiAgICBwYXJhbXNbJ2ZpbmlzaGVkX3N0YXRlJ10gPSBuZXdfc3RhdGU7XHJcbiAgICBhbGxQYXJhbXNbJ3BhcmFtcyddID0gcGFyYW1zO1xyXG4gICAgJC5wb3N0KCcvZWRpdF9wYWNrYWdlX2ZpbmlzaGVkJywgYWxsUGFyYW1zLCBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICB2YXIgY29kZSA9IHJlc3VsdC5jb2RlO1xyXG4gICAgICAgIHZhciBtc2cgPSByZXN1bHQubXNnO1xyXG4gICAgICAgIGlmIChjb2RlID09IDApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChtc2csZnVuY3Rpb24gKCkge3dpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKX0sMTUwMCk7XHJcbiAgICAgICAgfSBlbHNlXHJcbiAgICAgICAge1xyXG4gICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAobXNnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufVxyXG5mdW5jdGlvbiB1cGRhdGVQYWNrYWdlRmluaXNoZWRTdGF0ZXIobmV3X3N0YXRlLGlkLHRoaXNPYmopIHtcclxuICAgIHZhciByZXNvdXJjZVRpdGxlID0gXCLmj5DnpLrvvJog5LiT5qCPIFwiK3RoaXNPYmoucGFyZW50KCkucGFyZW50KCkuZmluZChcIi5pdGVtX3RpdGxlXCIpLnRleHQoKTtcclxuICAgIHJlc291cmNlVGl0bGUgPSBuZXdfc3RhdGU9PTA/IHJlc291cmNlVGl0bGUrXCIg5bey5a6M57uTXCIgOiByZXNvdXJjZVRpdGxlKyBcIiDmm7TmlrDkuK1cIjtcclxuICAgIHZhciB0aGlzSW5mb3MgPSBuZXdfc3RhdGU9PTA/IFwi5bCG5LiT5qCP54q25oCB6K6+572u5Li6IOabtOaWsOS4re+8n1wiIDogXCLlsIbkuJPmoI/nirbmgIHorr7nva7kuLog5bey5a6M57uT77yfXCI7XHJcbiAgICB3aW5kb3cud3hjLnhjQ29uZmlybSh0aGlzSW5mb3MsICdjb25maXJtJyx7dGl0bGU6cmVzb3VyY2VUaXRsZSxvbk9rOmZ1bmN0aW9uKClcclxuICAgIHtcclxuICAgICAgICB1cGRhdGVQYWNrYWdlRmluaXNoZWRTdGF0ZShuZXdfc3RhdGUsaWQpO1xyXG4gICAgfX0pO1xyXG59XHJcbi8v5pu05paw5LiT5qCP5o6S5bqP5pON5L2cXHJcbmZ1bmN0aW9uIHVwZGF0ZVBhY2thZ2VXZWlnaHQobmV3X3dlaWdodCxpZCkge1xyXG4gICAgdmFyIGFsbFBhcmFtcyA9e307XHJcbiAgICBhbGxQYXJhbXNbJ2lkJ10gPSBpZDtcclxuICAgIGFsbFBhcmFtc1snb3JkZXJfd2VpZ2h0J10gPSBuZXdfd2VpZ2h0O1xyXG4gICAgJC5wb3N0KCcvZWRpdF9wYWNrYWdlX3dlaWdodCcsIGFsbFBhcmFtcywgZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgIGhpZGVMb2FkaW5nKCk7XHJcbiAgICAgICAgdmFyIGNvZGUgPSByZXN1bHQuY29kZTtcclxuICAgICAgICB2YXIgbXNnID0gcmVzdWx0Lm1zZztcclxuICAgICAgICBpZiAoY29kZSA9PSAwKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAobXNnLGZ1bmN0aW9uICgpIHt3aW5kb3cubG9jYXRpb24ucmVsb2FkKCl9LDE1MDApO1xyXG4gICAgICAgIH0gZWxzZVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKG1zZyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbn1cclxuLy/mmL7npLrmiJbpmpDol4/kuJPmoI/nmoTmnIDmlrDmmL7npLpcclxuZnVuY3Rpb24gaDVuZXdlc3RIaWRlKGhpZGVfc3RhdGUsaWQpe1xyXG4gICAgdmFyIGFsbFBhcmFtcyA9e307XHJcbiAgICBhbGxQYXJhbXNbJ2lkJ10gPSBpZDtcclxuICAgIGFsbFBhcmFtc1snaGlkZV9zdGF0ZSddID0gaGlkZV9zdGF0ZTsvL1xyXG4gICAgJC5wb3N0KCcvaDVuZXdlc3RfaGlkZScsIGFsbFBhcmFtcywgZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgIGhpZGVMb2FkaW5nKCk7XHJcbiAgICAgICAgdmFyIGNvZGUgPSByZXN1bHQuY29kZTtcclxuICAgICAgICB2YXIgbXNnID0gcmVzdWx0Lm1zZztcclxuICAgICAgICBpZiAoY29kZSA9PTApIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChtc2csZnVuY3Rpb24gKCkge3dpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKX0sMTUwMCk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKG1zZyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbn1cclxuZnVuY3Rpb24gaDVuZXdlc3RIaWRlcihoaWRlX3N0YXRlLGlkLHRoaXNPYmope1xyXG4gICAgdmFyIHJlc291cmNlVGl0bGUgPSBcIuaPkOekuu+8miDkuJPmoI8gXCIrdGhpc09iai5wYXJlbnQoKS5wYXJlbnQoKS5maW5kKFwiLml0ZW1fdGl0bGVcIikudGV4dCgpO1xyXG4gICAgdmFyIGluZm9zID0gXCLorr7nva7kuLpcIiArIHRoaXNPYmouYXR0cigndGl0bGUnKSArIFwi77yfXCI7XHJcbiAgICB3aW5kb3cud3hjLnhjQ29uZmlybShpbmZvcywgJ2NvbmZpcm0nLHt0aXRsZTpyZXNvdXJjZVRpdGxlLG9uT2s6ZnVuY3Rpb24oKVxyXG4gICAge1xyXG4gICAgICAgIGg1bmV3ZXN0SGlkZShoaWRlX3N0YXRlLGlkKTtcclxuICAgIH19KTtcclxufVxyXG5cclxuLy/otYTmupDmkJzntKJcclxuZnVuY3Rpb24gc2VhcmNoUmVzb3VyY2UocmVzb3VyY2VfdHlwZSl7XHJcbiAgICAvL+WPlumAieaLqeWtl+auteWSjOWGheWuuVxyXG4gICAgdmFyIHJlc291cmNlX2F0dHIgPSAkKCcjcmVzb3VyY2VfYXR0cicpLnZhbCgpO1xyXG4gICAgdmFyIHNlYXJjaF9jb250ZW50ID0gJCgnI3Jlc291cmNlX3NlYXJjaF9jb250ZW50JykudmFsKCkudHJpbSgpO1xyXG4gICAgdmFyIHVybCA9ICcnO1xyXG4gICAgaWYocmVzb3VyY2VfdHlwZT09J2F1ZGlvJyl7XHJcbiAgICAgICAgdXJsID0gXCIvYXVkaW9fbGlzdD8mcmVzb3VyY2VfYXR0cj1cIiArIGVuY29kZVVSSShyZXNvdXJjZV9hdHRyKSArIFwiJnNlYXJjaF9jb250ZW50PVwiICsgZW5jb2RlVVJJKHNlYXJjaF9jb250ZW50KTtcclxuICAgIH1lbHNlIGlmKHJlc291cmNlX3R5cGU9PSd2aWRlbycpe1xyXG4gICAgICAgIHVybCA9IFwiL3ZpZGVvX2xpc3Q/JnJlc291cmNlX2F0dHI9XCIgKyBlbmNvZGVVUkkocmVzb3VyY2VfYXR0cikgKyBcIiZzZWFyY2hfY29udGVudD1cIiArIGVuY29kZVVSSShzZWFyY2hfY29udGVudCk7XHJcbiAgICB9ZWxzZSBpZihyZXNvdXJjZV90eXBlPT0nYXJ0aWNsZScpe1xyXG4gICAgICAgIHVybCA9IFwiL2FydGljbGVfbGlzdD8mcmVzb3VyY2VfYXR0cj1cIiArIGVuY29kZVVSSShyZXNvdXJjZV9hdHRyKSArIFwiJnNlYXJjaF9jb250ZW50PVwiICsgZW5jb2RlVVJJKHNlYXJjaF9jb250ZW50KTtcclxuICAgIH1cclxuICAgIHdpbmRvdy5sb2NhdGlvbiA9IHVybDtcclxufVxyXG5cclxuLy/lm57mmL7mkJzntKLmoYblhoXnmoTlgLxcclxuZnVuY3Rpb24gcmVCYWNrKCkge1xyXG5cclxuICAgIGlmKHNlYXJjaF9jb250ZW50IT11bmRlZmluZWQpeyAvL+S4k+agj+S8muWHuueOsOi/meenjeaDheWGtX5cclxuICAgICAgICB2YXIgcmVzb3VyY2Vfc2VhcmNoX2NvbnRlbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcInJlc291cmNlX3NlYXJjaF9jb250ZW50XCIpO1xyXG5cclxuICAgICAgICBpZiAoc2VhcmNoX2NvbnRlbnQgIT0gXCJcIikge1xyXG4gICAgICAgICAgICByZXNvdXJjZV9zZWFyY2hfY29udGVudC52YWx1ZSA9IHNlYXJjaF9jb250ZW50O1xyXG4gICAgICAgICAgICB2YXIgcmVzb3VyY2VfYXR0cl9lbGVtZW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJyZXNvdXJjZV9hdHRyXCIpO1xyXG4gICAgICAgICAgICBpZiAocmVzb3VyY2VfYXR0cl9lbGVtZW50Lmxlbmd0aCA+IDApIHtcclxuICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgcmVzb3VyY2VfYXR0cl9lbGVtZW50Lm9wdGlvbnMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICBpZiAocmVzb3VyY2VfYXR0cl9lbGVtZW50Lm9wdGlvbnNbaV0udmFsdWUgPT0gcmVzb3VyY2VfYXR0cikge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXNvdXJjZV9hdHRyX2VsZW1lbnQub3B0aW9uc1tpXS5zZWxlY3RlZCA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbn1cclxuXHJcbi8v5YiX6KGo5pCc57Si5qGG5Zue6L2m6Kem5Y+R5pCc57SiXHJcbmZ1bmN0aW9uIHJlU2VhcmNoKCkge1xyXG4gICAgLy/lm57ovabmkJzntKJcclxuICAgICQoZG9jdW1lbnQpLmtleXByZXNzKGZ1bmN0aW9uKGUpXHJcbiAgICB7XHJcbiAgICAgICAgaWYoZS53aGljaCA9PSAxMylcclxuICAgICAgICB7XHJcbiAgICAgICAgICAgICQoJyNyZXNvdXJjZV9zZWFyY2hfYnRuJykudHJpZ2dlcihcImNsaWNrXCIpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59XHJcblxyXG4iXX0=