var needRefresh=false;var user_id;var blog_id;var search_content;var blog_attr;var blog_state;$(document).ready(function(){refresh();reBack();keyEnter($("#comment_search_btn"));$("tbody tr").mouseover(function(){$(this).css({"background-color":"#f5f5f5"})}).mouseout(function(){$(this).css({"background-color":"#fff"})})});function refresh(){$("#replayModal").on("hide.bs.modal",function(){if(needRefresh){location.reload()}})}function searchBComment(){showLoading();var e=$("#comment_attr").val();var t=$("#comment_search_content").val();var o=$("#comment_state").val();if(t.length==0){var n="/blogComment_admin?comment_attr="+encodeURI(e)+"&comment_state="+o;console.log(n)}else{var n="/blogComment_admin?comment_attr="+encodeURI(e)+"&search_content="+encodeURI(t)+"&comment_state="+o;console.log(n)}window.location=n}function changeTopState(e,t,o){showLoading();var n="#state_"+e;var a="#btnTop_"+e;var r=o;var c="";if(t==0){c="/update_bComment_state?id="+e+"&state=1"+"&type=top"+"&recordId="+r}else{c="/update_bComment_state?id="+e+"&state=0"+"&type=top"+"&recordId="+r}$.get(c,function(e){hideLoading();if(e.code==0){if(t==0){$(a).html("取消精选");$(a).val(1)}else{$(a).html("精选评论");$(a).val(0)}}baseUtils.show.blueTip(e.msg);window.location.reload()})}function changeState(e,t,o){showLoading();var n="#state_"+e;var a="#btn_"+e;var r=o;var c="";if(t==0){c="/update_bComment_state?id="+e+"&state=1"+"&type=show"+"&recordId="+r}else{c="/update_bComment_state?id="+e+"&state=0"+"&type=show"+"&recordId="+r}$.get(c,function(e){hideLoading();if(e.code==0){if(t==0){$(a).html("显示");$(a).val(1)}else{$(a).html("隐藏");$(a).val(0)}}baseUtils.show.blueTip(e.msg);window.location.reload()})}function reBack(){var e=document.getElementById("comment_search_content");if(search_content!=""){e.value=search_content;var t=document.getElementById("comment_attr");if(t.length>0){for(var o=0;o<t.options.length;o++){if(t.options[o].value==comment_attr){t.options[o].selected=true;break}}}}var n=document.getElementById("comment_state");if(n.length>0){for(var o=0;o<n.options.length;o++){if(n.options[o].value==comment_state){n.options[o].selected=true;break}}}}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2Jsb2dDb21tZW50QWRtaW4uanMiXSwibmFtZXMiOlsibmVlZFJlZnJlc2giLCJ1c2VyX2lkIiwiYmxvZ19pZCIsInNlYXJjaF9jb250ZW50IiwiYmxvZ19hdHRyIiwiYmxvZ19zdGF0ZSIsIiQiLCJkb2N1bWVudCIsInJlYWR5IiwicmVmcmVzaCIsInJlQmFjayIsImtleUVudGVyIiwibW91c2VvdmVyIiwidGhpcyIsImNzcyIsImJhY2tncm91bmQtY29sb3IiLCJtb3VzZW91dCIsIm9uIiwibG9jYXRpb24iLCJyZWxvYWQiLCJzZWFyY2hCQ29tbWVudCIsInNob3dMb2FkaW5nIiwiY29tbWVudEF0dHIiLCJ2YWwiLCJjb21tZW50X3N0YXRlIiwibGVuZ3RoIiwidXJsIiwiZW5jb2RlVVJJIiwiY29uc29sZSIsImxvZyIsIndpbmRvdyIsImNoYW5nZVRvcFN0YXRlIiwiaWQiLCJjdXJyZW50VG9wU3RhdGUiLCJyZWNvcmRfaWQiLCJ0aWQiLCJidG5Ub3BpZCIsInJlY29yZElkIiwiZ2V0IiwicmVzdWx0IiwiaGlkZUxvYWRpbmciLCJjb2RlIiwiaHRtbCIsImJhc2VVdGlscyIsInNob3ciLCJibHVlVGlwIiwibXNnIiwiY2hhbmdlU3RhdGUiLCJjdXJyZW50U3RhdGUiLCJidG5TdGF0ZWlkIiwiY29tbWVudF9zZWFyY2hfY29udGVudCIsImdldEVsZW1lbnRCeUlkIiwidmFsdWUiLCJjb21tZW50X2F0dHJfZWxlbWVudCIsImkiLCJvcHRpb25zIiwiY29tbWVudF9hdHRyIiwic2VsZWN0ZWQiLCJjb21tZW50X3N0YXRlX2VsZW1lbnQiXSwibWFwcGluZ3MiOiJBQUFBLEdBQUlBLGFBQWMsS0FFbEIsSUFBSUMsUUFDSixJQUFJQyxRQUVKLElBQUlDLGVBQ0osSUFBSUMsVUFDSixJQUFJQyxXQUVKQyxHQUFFQyxVQUFVQyxNQUFNLFdBQ2RDLFNBQ0FDLFNBQ0FDLFVBQVNMLEVBQUUsdUJBRVhBLEdBQUUsWUFBWU0sVUFBVSxXQUVwQk4sRUFBRU8s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QUFJSSxFQUFzQkgsUUFBUTlCLE9BQVE2QixJQUFLLENBQzNELEdBQUlJLEVBQXNCSCxRQUFRRCxHQUFHRixPQUFTNUIsY0FBZSxDQUN6RGtDLEVBQXNCSCxRQUFRRCxHQUFHRyxTQUFXLElBQzVDIiwiZmlsZSI6ImFkbWluL2Jsb2dDb21tZW50QWRtaW4uanMiLCJzb3VyY2VzQ29udGVudCI6WyJ2YXIgbmVlZFJlZnJlc2ggPSBmYWxzZTtcclxuXHJcbnZhciB1c2VyX2lkO1xyXG52YXIgYmxvZ19pZDtcclxuXHJcbnZhciBzZWFyY2hfY29udGVudDtcclxudmFyIGJsb2dfYXR0cjtcclxudmFyIGJsb2dfc3RhdGU7XHJcblxyXG4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbiAoKSB7XHJcbiAgICByZWZyZXNoKCk7XHJcbiAgICByZUJhY2soKTtcclxuICAgIGtleUVudGVyKCQoJyNjb21tZW50X3NlYXJjaF9idG4nKSk7XHJcblxyXG4gICAgJChcInRib2R5IHRyXCIpLm1vdXNlb3ZlcihmdW5jdGlvbigpXHJcbiAgICB7XHJcbiAgICAgICAgJCh0aGlzKS5jc3MoeydiYWNrZ3JvdW5kLWNvbG9yJzonI2Y1ZjVmNSd9KTtcclxuICAgIH0pLm1vdXNlb3V0KGZ1bmN0aW9uKClcclxuICAgIHtcclxuICAgICAgICAkKHRoaXMpLmNzcyh7J2JhY2tncm91bmQtY29sb3InOicjZmZmJ30pO1xyXG4gICAgfSk7XHJcblxyXG59KTtcclxuXHJcbmZ1bmN0aW9uIHJlZnJlc2goKSB7XHJcbiAgICAkKFwiI3JlcGxheU1vZGFsXCIpLm9uKCdoaWRlLmJzLm1vZGFsJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIC8v5Yik5pat5piv5ZCm6ZyA6KaB5Yi35paw55WM6Z2iXHJcbiAgICAgICAgaWYgKG5lZWRSZWZyZXNoKSB7XHJcbiAgICAgICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpOyAvL+mHjeaWsOWKoOi9vemhtemdolxyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59XHJcblxyXG5mdW5jdGlvbiAgc2VhcmNoQkNvbW1lbnQoKXtcclxuICAgIHNob3dMb2FkaW5nKCk7XHJcbiAgICB2YXIgY29tbWVudEF0dHIgPSAkKFwiI2NvbW1lbnRfYXR0clwiKS52YWwoKTsgLy/ojrflj5bliLDpgInkuK3nmoTlgLxcclxuICAgIC8v6I635Y+W5pCc57Si5YaF5a65XHJcbiAgICB2YXIgc2VhcmNoX2NvbnRlbnQgPSAkKFwiI2NvbW1lbnRfc2VhcmNoX2NvbnRlbnRcIikudmFsKCk7IC8v6I635Y+W5Yiw6YCJ5Lit55qE5YC8XHJcbiAgICAvL+ivhOiuuueKtuaAgVxyXG4gICAgdmFyIGNvbW1lbnRfc3RhdGUgPSAkKFwiI2NvbW1lbnRfc3RhdGVcIikudmFsKCk7IC8v6I635Y+W5omA6YCJ54q25oCBXHJcbiAgICBpZiAoc2VhcmNoX2NvbnRlbnQubGVuZ3RoID09IDApIHtcclxuICAgICAgICB2YXIgdXJsID0gXCIvYmxvZ0NvbW1lbnRfYWRtaW4/Y29tbWVudF9hdHRyPVwiICsgZW5jb2RlVVJJKGNvbW1lbnRBdHRyKSsnJmNvbW1lbnRfc3RhdGU9Jytjb21tZW50X3N0YXRlO1xyXG4gICAgICAgIGNvbnNvbGUubG9nKHVybCk7XHJcbiAgICB9IGVsc2Uge1xyXG4gICAgICAgIHZhciB1cmwgPSBcIi9ibG9nQ29tbWVudF9hZG1pbj9jb21tZW50X2F0dHI9XCIgKyBlbmNvZGVVUkkoY29tbWVudEF0dHIpICsgXCImc2VhcmNoX2NvbnRlbnQ9XCIgKyBlbmNvZGVVUkkoc2VhcmNoX2NvbnRlbnQpKycmY29tbWVudF9zdGF0ZT0nK2NvbW1lbnRfc3RhdGU7XHJcbiAgICAgICAgY29uc29sZS5sb2codXJsKTtcclxuICAgIH1cclxuICAgIHdpbmRvdy5sb2NhdGlvbiA9IHVybDtcclxufVxyXG5cclxuLy/mlLnlj5gg572u6aG2IOeKtuaAgVxyXG5mdW5jdGlvbiBjaGFuZ2VUb3BTdGF0ZShpZCAsY3VycmVudFRvcFN0YXRlLHJlY29yZF9pZCkge1xyXG4gICAgc2hvd0xvYWRpbmcoKTtcclxuICAgIHZhciB0aWQgPSAnI3N0YXRlXycgKyBpZDtcclxuICAgIHZhciBidG5Ub3BpZCA9ICcjYnRuVG9wXycraWQ7XHJcbiAgICB2YXIgcmVjb3JkSWQgPSByZWNvcmRfaWQ7XHJcbiAgICB2YXIgdXJsID0gXCJcIjtcclxuICAgIGlmKGN1cnJlbnRUb3BTdGF0ZT09MCl7XHJcbiAgICAgICAgdXJsID0gXCIvdXBkYXRlX2JDb21tZW50X3N0YXRlP2lkPVwiICsgaWQgKyBcIiZzdGF0ZT0xXCIgK1wiJnR5cGU9dG9wXCIrXCImcmVjb3JkSWQ9XCIrcmVjb3JkSWQ7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICB1cmwgPSBcIi91cGRhdGVfYkNvbW1lbnRfc3RhdGU/aWQ9XCIgKyBpZCArIFwiJnN0YXRlPTBcIiArXCImdHlwZT10b3BcIitcIiZyZWNvcmRJZD1cIityZWNvcmRJZDtcclxuICAgIH1cclxuICAgIC8v5L+u5pS555WM6Z2i5L+h5oGvXHJcbiAgICAkLmdldCh1cmwsZnVuY3Rpb24ocmVzdWx0KSB7XHJcbiAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuXHJcbiAgICAgICAgaWYocmVzdWx0LmNvZGU9PTApe1xyXG4gICAgICAgICAgICAvL+W9k+WJjeeKtuaAge+8muacque9rumhtlxyXG4gICAgICAgICAgICBpZihjdXJyZW50VG9wU3RhdGU9PTApe1xyXG4gICAgICAgICAgICAgICAgLy8gJCh0aWQpLmNoaWxkcmVuKCkuZXEoMCkuYXR0cignY2xhc3MnLCdidG4gYnRuLXByaW1hcnkgYnRuLXNtJyk7XHJcbiAgICAgICAgICAgICAgICAvLyAkKHRpZCkuY2hpbGRyZW4oKS5lcSgwKS5odG1sKCfnsr7pgIknKTtcclxuICAgICAgICAgICAgICAgIC8vICQodGlkKS5jaGlsZHJlbigpLmVxKDApLmF0dHIoJ2NvbG9yJywnd2hpdGUnKTtcclxuICAgICAgICAgICAgICAgICQoYnRuVG9waWQpLmh0bWwoJ+WPlua2iOeyvumAiScpO1xyXG4gICAgICAgICAgICAgICAgJChidG5Ub3BpZCkudmFsKDEpO1xyXG4gICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgIC8vICQodGlkKS5jaGlsZHJlbigpLmVxKDApLmF0dHIoJ2NsYXNzJywnYnRuIGJ0bi1saW5rIGJ0bi1zbScpO1xyXG4gICAgICAgICAgICAgICAgLy8gJCh0aWQpLmNoaWxkcmVuKCkuZXEoMCkuaHRtbCgnJyk7XHJcbiAgICAgICAgICAgICAgICAkKGJ0blRvcGlkKS5odG1sKCfnsr7pgInor4TorronKTtcclxuICAgICAgICAgICAgICAgICQoYnRuVG9waWQpLnZhbCgwKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKHJlc3VsdC5tc2cpO1xyXG4gICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcclxuICAgIH0pXHJcbn1cclxuXHJcbi8vIOaUueWPmOaYvuekui/pmpDol4/nirbmgIFcclxuZnVuY3Rpb24gY2hhbmdlU3RhdGUoaWQgLGN1cnJlbnRTdGF0ZSAsIHJlY29yZF9pZCkge1xyXG5cclxuICAgIHNob3dMb2FkaW5nKCk7XHJcbiAgICB2YXIgdGlkID0gJyNzdGF0ZV8nICsgaWQ7XHJcbiAgICB2YXIgYnRuU3RhdGVpZCA9ICcjYnRuXycgKyBpZDtcclxuICAgIHZhciByZWNvcmRJZCA9IHJlY29yZF9pZDtcclxuICAgIC8vdmFyIGJ0blRvcGlkID0gJyNidG5Ub3BfJytpZDtcclxuXHJcbiAgICB2YXIgdXJsID0gXCJcIjtcclxuICAgIGlmIChjdXJyZW50U3RhdGUgPT0gMCkge1xyXG4gICAgICAgIC8vIHVybCA9IFwiL3VwZGF0ZV9jb21tZW50X3N0YXRlP2lkPVwiICsgaWQgKyBcIiZzdGF0ZT0xJnVzZXJfaWQ9XCIgKyB1c2VyX2lkICsgXCImYXVkaW9faWQ9XCIrYXVkaW9faWQrXCImdHlwZT1cIiArIHR5cGU7XHJcbiAgICAgICAgdXJsID0gXCIvdXBkYXRlX2JDb21tZW50X3N0YXRlP2lkPVwiICsgaWQgKyBcIiZzdGF0ZT0xXCIrXCImdHlwZT1zaG93XCIrXCImcmVjb3JkSWQ9XCIrcmVjb3JkSWQ7XHJcbiAgICB9IGVsc2Uge1xyXG4gICAgICAgIC8vIHVybCA9IFwiL3VwZGF0ZV9jb21tZW50X3N0YXRlP2lkPVwiICsgaWQgKyBcIiZzdGF0ZT0wJnVzZXJfaWQ9XCIgKyB1c2VyX2lkICsgXCImYXVkaW9faWQ9XCIrYXVkaW9faWQrXCImdHlwZT1cIiArIHR5cGU7XHJcbiAgICAgICAgdXJsID0gXCIvdXBkYXRlX2JDb21tZW50X3N0YXRlP2lkPVwiICsgaWQgKyBcIiZzdGF0ZT0wXCIrXCImdHlwZT1zaG93XCIrXCImcmVjb3JkSWQ9XCIrcmVjb3JkSWQ7XHJcbiAgICB9XHJcbiAgICAvL+abtOaWsOeVjOmdolxyXG4gICAgJC5nZXQodXJsLCBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICBpZiAocmVzdWx0LmNvZGUgPT0gMCkge1xyXG4gICAgICAgICAgICAvL+W9k+WJjeivhOiuuueKtuaAge+8muaYvuekulxyXG4gICAgICAgICAgICBpZiAoY3VycmVudFN0YXRlID09IDApIHtcclxuICAgICAgICAgICAgICAgIC8vICQodGlkKS5jaGlsZHJlbigpLmVxKDApLmF0dHIoJ2NsYXNzJywgJ2J0biBidG4tZGFuZ2VyIGJ0bi1zbScpO1xyXG4gICAgICAgICAgICAgICAgLy8gJCh0aWQpLmNoaWxkcmVuKCkuZXEoMCkuaHRtbCgn6ZqQ6JePJyk7XHJcbiAgICAgICAgICAgICAgICAvLyAkKHRpZCkuY2hpbGRyZW4oKS5lcSgwKS5hdHRyKCdjb2xvcicsICd3aGl0ZScpO1xyXG4gICAgICAgICAgICAgICAgJChidG5TdGF0ZWlkKS5odG1sKCfmmL7npLonKTtcclxuICAgICAgICAgICAgICAgICQoYnRuU3RhdGVpZCkudmFsKDEpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgLy8gJCh0aWQpLmNoaWxkcmVuKCkuZXEoMCkuYXR0cignY2xhc3MnLCAnYnRuIGJ0bi1saW5rIGJ0bi1zbScpO1xyXG4gICAgICAgICAgICAgICAgLy8gJCh0aWQpLmNoaWxkcmVuKCkuZXEoMCkuaHRtbCgnJyk7XHJcbiAgICAgICAgICAgICAgICAkKGJ0blN0YXRlaWQpLmh0bWwoJ+makOiXjycpO1xyXG4gICAgICAgICAgICAgICAgJChidG5TdGF0ZWlkKS52YWwoMCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChyZXN1bHQubXNnKTtcclxuICAgICAgICB3aW5kb3cubG9jYXRpb24ucmVsb2FkKCk7XHJcbiAgICB9KVxyXG59XHJcblxyXG4vL+WbnuaYvui+k+WFpeahhuWSjOmAieaLqeahhueahOWAvFxyXG5mdW5jdGlvbiByZUJhY2soKSB7XHJcbiAgICB2YXIgY29tbWVudF9zZWFyY2hfY29udGVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwiY29tbWVudF9zZWFyY2hfY29udGVudFwiKTtcclxuXHJcbiAgICBpZiAoc2VhcmNoX2NvbnRlbnQgIT0gXCJcIikge1xyXG4gICAgICAgIGNvbW1lbnRfc2VhcmNoX2NvbnRlbnQudmFsdWUgPSBzZWFyY2hfY29udGVudDtcclxuICAgICAgICB2YXIgY29tbWVudF9hdHRyX2VsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcImNvbW1lbnRfYXR0clwiKTtcclxuICAgICAgICBpZiAoY29tbWVudF9hdHRyX2VsZW1lbnQubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGNvbW1lbnRfYXR0cl9lbGVtZW50Lm9wdGlvbnMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgIGlmIChjb21tZW50X2F0dHJfZWxlbWVudC5vcHRpb25zW2ldLnZhbHVlID09IGNvbW1lbnRfYXR0cikge1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbW1lbnRfYXR0cl9lbGVtZW50Lm9wdGlvbnNbaV0uc2VsZWN0ZWQgPSB0cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIHZhciBjb21tZW50X3N0YXRlX2VsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcImNvbW1lbnRfc3RhdGVcIik7XHJcbiAgICBpZiAoY29tbWVudF9zdGF0ZV9lbGVtZW50Lmxlbmd0aCA+IDApIHtcclxuICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGNvbW1lbnRfc3RhdGVfZWxlbWVudC5vcHRpb25zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmIChjb21tZW50X3N0YXRlX2VsZW1lbnQub3B0aW9uc1tpXS52YWx1ZSA9PSBjb21tZW50X3N0YXRlKSB7XHJcbiAgICAgICAgICAgICAgICBjb21tZW50X3N0YXRlX2VsZW1lbnQub3B0aW9uc1tpXS5zZWxlY3RlZCA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgIH1cclxufSJdfQ==
