$(document).ready(function(){init();channelHandle();wordOverflow();$("#updateUrl").click(function(){var e=$("#agree_id").val();if(e.length==0){baseUtils.show.redTip("未选择分销单号");return false}var a="";var n="";if($("input[name='create_select']:checked").val()==1){a=$("input[name='channel_select']:checked").val();n=$("input[name='channel_select']:checked").attr("channel_id")}$.post("/agreesale",{id:e,sale_url:a,channel_id:n},function(e){if(e.ret==0){baseUtils.show.blueTip("操作成功",function(){window.location.reload()})}else{baseUtils.show.redTip("操作失败")}})});$("#updateRefuse").click(function(){var e=$("#disagree_id").val();if(e.length==0){baseUtils.show.redTip("未选择分销单号");return false}var a=$("#refuse_reason").val();if(a.length==0){baseUtils.show.redTip("请输入拒绝原因");return false}$.post("/disagreesale",{id:e,refuse_reason:a},function(e){if(e.ret==0){baseUtils.show.blueTip("操作成功",function(){window.location.reload()})}else{baseUtils.show.redTip("操作失败")}})})});function wordOverflow(){$(".overomit").each(function(){var e=$(this).text();if(e.length>10){var a=e.substring(0,10)+"...";$(this).text(a)}})}function init(){$("#search").val(getUrlParam("search"));$("#ruler").val(getUrlParam("ruler")==null||getUrlParam("ruler")==""?-1:getUrlParam("ruler"));$("#searchButton").click(function(){var e=$("#search").val();var a=$("#ruler").val();window.location.href="/sale?search="+e+"&ruler="+a});$(document).keypress(function(e){if(e.which==13){$("#searchButton").trigger("click")}});(function(){var e=new Clipboard(".copyHref");e.on("success",function(e){baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");e.clearSelection()})})()}function channelHandle(){$("input[name='create_select']").next().click(function(){if($(this).prev().val()==0){$(".modal-body .tableAreaContainer").hide();$("input[name='channel_select']").attr("disabled",true)}else{$(".modal-body .tableAreaContainer").show();$("input[name='channel_select']").attr("disabled",false)}})}function agreeSale(e){$("#agree_id").val(e);$("#agreeModal").modal("show");$("#channel_list").html("");$.post("/get_channel",{id:e},function(e){if(e.code==0){$("#channel_list").html(e.data);if(!$("#no_data").hasClass("hide")){$("#no_data").addClass("hide")}}else{$("#no_data").removeClass("hide")}})}function disAgreeSale(e){$("#disagree_id").val(e);$("#disAgreeModal").modal("show")}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL3NhbGUuanMiXSwibmFtZXMiOlsiJCIsImRvY3VtZW50IiwicmVhZHkiLCJpbml0IiwiY2hhbm5lbEhhbmRsZSIsIndvcmRPdmVyZmxvdyIsImNsaWNrIiwiaWQiLCJ2YWwiLCJsZW5ndGgiLCJiYXNlVXRpbHMiLCJzaG93IiwicmVkVGlwIiwic2FsZV91cmwiLCJjaGFubmVsX2lkIiwiYXR0ciIsInBvc3QiLCJkYXRhIiwicmV0IiwiYmx1ZVRpcCIsIndpbmRvdyIsImxvY2F0aW9uIiwicmVsb2FkIiwicmVmdXNlX3JlYXNvbiIsImVhY2giLCJvdmVyV29yZHMiLCJ0aGlzIiwidGV4dCIsIm5ld1dvcmRzIiwic3Vic3RyaW5nIiwiZ2V0VXJsUGFyYW0iLCJzZWFyY2giLCJydWxlciIsImhyZWYiLCJrZXlwcmVzcyIsImUiLCJ3aGljaCIsInRyaWdnZXIiLCJjbGlwYm9hcmQiLCJDbGlwYm9hcmQiLCJvbiIsImNsZWFyU2VsZWN0aW9uIiwibmV4dCIsInByZXYiLCJoaWRlIiwiYWdyZWVTYWxlIiwibW9kYWwiLCJodG1sIiwiY29kZSIsImhhc0NsYXNzIiwiYWRkQ2xhc3MiLCJyZW1vdmVDbGFzcyIsImRpc0FncmVlU2FsZSJdLCJtYXBwaW5ncyI6IkFBQUFBLEVBQUVDLFVBQVVDLE1BQU0sV0FFZEMsTUFDQUMsZ0JBQ0FDLGVBRUFMLEdBQUUsY0FBY00sTUFBTSxXQUVsQixHQUFJQyxHQUFHUCxFQUFFLGFBQWFRLEtBQ3RCLElBQUdELEVBQUdFLFFBQVEsRUFDZCxDQUNJQyxVQUFVQyxLQUFLQyxPQUFPLFVBQ3RCLE9BQU8sT0FHWCxHQUFJQyxHQUFTLEVBQUcsSUFBSUMsR0FBVyxFQUMvQixJQUFJZCxFQUFFLHVDQUF1Q1EsT0FBUyxFQUN0RCxDQUNJSyxFQUFTYixFQUFFLHdDQUF3Q1EsS0FDbkRNLEdBQVdkLEVBQUUsd0NBQXdDZSxLQUFLLGNBRzlEZixFQUFFZ0IsS0FBSyxjQUFjVCxHQUFLQSxFQUFHTSxTQUFXQSxFQUFTQyxXQUFhQSxHQUFZLFNBQVNHLEdBRS9FLEdBQUdBLEVBQUtDLEtBQUssRUFDYixDQUNJUixVQUFVQyxLQUFLUSxRQUFRLE9BQU8sV0FFMUJDLE9BQU9DLFNBQVNDLGVBSXhCLENBQ0laLFVBQVVDLEtBQUtDLE9BQU8sWUFNbENaLEdBQUUsaUJBQWlCTSxNQUFNLFdBRXJCLEdBQUlDLEdBQUdQLEVBQUUsZ0JBQWdCUSxLQUN6QixJQUFHRCxFQUFHRSxRQUFRLEVBQ2QsQ0FDSUMsVUFBVUMsS0FBS0MsT0FBTyxVQUN0QixPQUFPLE9BR1gsR0FBSVcsR0FBY3ZCLEVBQUUsa0JBQWtCUSxLQUN0QyxJQUFHZSxFQUFjZCxRQUFRLEVBQ3pCLENBQ0lDLFVBQVVDLEtBQUtDLE9BQU8sVUFDdEIsT0FBTyxPQUdYWixFQUFFZ0IsS0FBSyxpQkFBaUJULEdBQUtBLEVBQUdnQixjQUFnQkEsR0FBZSxTQUFTTixHQUVwRSxHQUFHQSxFQUFLQyxLQUFLLEVBQ2IsQ0FDSVIsVUFBVUMsS0FBS1EsUUFBUSxPQUFPLFdBRTFCQyxPQUFPQyxTQUFTQyxlQUl4QixDQUNJWixVQUFVQyxLQUFLQyxPQUFPLGNBTXRDLFNBQVNQLGdCQUNMTCxFQUFFLGFBQWF3QixLQUFLLFdBQ2hCLEdBQUlDLEdBQVV6QixFQUFFMEIsTUFBTUMsTUFDdEIsSUFBR0YsRUFBVWhCLE9BQU8sR0FBRyxDQUNuQixHQUFJbUIsR0FBU0gsRUFBVUksVUFBVSxFQUFFLElBQUksS0FDdkM3QixHQUFFMEIsTUFBTUMsS0FBS0MsTUFNekIsUUFBU3pCLFFBSUxILEVBQUUsV0FBV1EsSUFBSXNCLFlBQVksVUFDN0I5QixHQUFFLFVBQVVRLElBQUtzQixZQUFZLFVBQVUsTUFBU0EsWUFBWSxVQUFVLElBQU8sRUFBR0EsWUFBWSxTQUc1RjlCLEdBQUUsaUJBQWlCTSxNQUFNLFdBRXJCLEdBQUl5QixHQUFPL0IsRUFBRSxXQUFXUSxLQUN4QixJQUFJd0IsR0FBTWhDLEVBQUUsVUFBVVEsS0FDdEJZLFFBQU9DLFNBQVNZLEtBQUssZ0JBQWdCRixFQUFPLFVBQVVDLEdBSTFEaEMsR0FBRUMsVUFBVWlDLFNBQVMsU0FBU0MsR0FFMUIsR0FBR0EsRUFBRUMsT0FBUyxHQUNkLENBQ0lwQyxFQUFFLGlCQUFpQnFDLFFBQVEsYUFLbkMsV0FDSSxHQUFJQyxHQUFZLEdBQUlDLFdBQVUsWUFDOUJELEdBQVVFLEdBQUcsVUFBVyxTQUFTTCxHQUM3QnpCLFVBQVVDLEtBQUtRLFFBQVEsa0JBQ3ZCZ0IsR0FBRU0sdUJBTWQsUUFBU3JDLGlCQUVMSixFQUFFLCtCQUErQjBDLE9BQU9wQyxNQUFNLFdBQzFDLEdBQUlOLEVBQUUwQixNQUFNaUIsT0FBT25DLE9BQVMsRUFBSSxDQUM1QlIsRUFBRSxtQ0FBbUM0QyxNQUNyQzVDLEdBQUUsZ0NBQWdDZSxLQUFLLFdBQVcsVUFDL0MsQ0FDSGYsRUFBRSxtQ0FBbUNXLE1BQ3JDWCxHQUFFLGdDQUFnQ2UsS0FBSyxXQUFXLFVBTTlELFFBQVM4QixXQUFVdEMsR0FFZlAsRUFBRSxhQUFhUSxJQUFJRCxFQUNuQlAsR0FBRSxlQUFlOEMsTUFBTSxPQUN2QjlDLEdBQUUsaUJBQWlCK0MsS0FBSyxHQUd4Qi9DLEdBQUVnQixLQUFLLGdCQUFnQlQsR0FBS0EsR0FBSSxTQUFVVSxHQUV0QyxHQUFHQSxFQUFLK0IsTUFBUSxFQUNoQixDQUNJaEQsRUFBRSxpQkFBaUIrQyxLQUFLOUIsRUFBS0EsS0FDN0IsS0FBSWpCLEVBQUUsWUFBWWlELFNBQVMsUUFBUSxDQUMvQmpELEVBQUUsWUFBWWtELFNBQVMsYUFJMUIsQ0FFRGxELEVBQUUsWUFBWW1ELFlBQVksV0FNdEMsUUFBU0MsY0FBYTdDLEdBRWxCUCxFQUFFLGdCQUFnQlEsSUFBSUQsRUFDdEJQLEdBQUUsa0JBQWtCOEMsTUFBTSIsImZpbGUiOiJhZG1pbi9zYWxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKVxyXG57XHJcbiAgICBpbml0KCk7XHJcbiAgICBjaGFubmVsSGFuZGxlKCk7XHJcbiAgICB3b3JkT3ZlcmZsb3coKTtcclxuICAgIC8v56Gu6K6k55Sz6K+35oyJ6ZKu54K55Ye7XHJcbiAgICAkKFwiI3VwZGF0ZVVybFwiKS5jbGljayhmdW5jdGlvbigpXHJcbiAgICB7XHJcbiAgICAgICAgdmFyIGlkPSQoXCIjYWdyZWVfaWRcIikudmFsKCk7XHJcbiAgICAgICAgaWYoaWQubGVuZ3RoPT0wKVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5pyq6YCJ5oup5YiG6ZSA5Y2V5Y+3XCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB2YXIgc2FsZV91cmw9XCJcIjt2YXIgY2hhbm5lbF9pZD1cIlwiO1xyXG4gICAgICAgIGlmKCAkKFwiaW5wdXRbbmFtZT0nY3JlYXRlX3NlbGVjdCddOmNoZWNrZWRcIikudmFsKCkgPT0gMSApIC8v5L2/55So5bey5pyJ55qE6ZO+5o6lXHJcbiAgICAgICAge1xyXG4gICAgICAgICAgICBzYWxlX3VybD0kKFwiaW5wdXRbbmFtZT0nY2hhbm5lbF9zZWxlY3QnXTpjaGVja2VkXCIpLnZhbCgpO1xyXG4gICAgICAgICAgICBjaGFubmVsX2lkPSQoXCJpbnB1dFtuYW1lPSdjaGFubmVsX3NlbGVjdCddOmNoZWNrZWRcIikuYXR0cihcImNoYW5uZWxfaWRcIik7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAkLnBvc3QoXCIvYWdyZWVzYWxlXCIse1wiaWRcIjppZCxcInNhbGVfdXJsXCI6c2FsZV91cmwsXCJjaGFubmVsX2lkXCI6Y2hhbm5lbF9pZH0sZnVuY3Rpb24oZGF0YSlcclxuICAgICAgICB7XHJcbiAgICAgICAgICAgIGlmKGRhdGEucmV0PT0wKVxyXG4gICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi5pON5L2c5oiQ5YqfXCIsZnVuY3Rpb24oKVxyXG4gICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGVsc2VcclxuICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5pON5L2c5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+aLkue7neeUs+ivt+aMiemSrueCueWHu1xyXG4gICAgJChcIiN1cGRhdGVSZWZ1c2VcIikuY2xpY2soZnVuY3Rpb24oKVxyXG4gICAge1xyXG4gICAgICAgIHZhciBpZD0kKFwiI2Rpc2FncmVlX2lkXCIpLnZhbCgpO1xyXG4gICAgICAgIGlmKGlkLmxlbmd0aD09MClcclxuICAgICAgICB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuacqumAieaLqeWIhumUgOWNleWPt1wiKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgdmFyIHJlZnVzZV9yZWFzb249JChcIiNyZWZ1c2VfcmVhc29uXCIpLnZhbCgpO1xyXG4gICAgICAgIGlmKHJlZnVzZV9yZWFzb24ubGVuZ3RoPT0wKVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi6K+36L6T5YWl5ouS57ud5Y6f5ZugXCIpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAkLnBvc3QoXCIvZGlzYWdyZWVzYWxlXCIse1wiaWRcIjppZCxcInJlZnVzZV9yZWFzb25cIjpyZWZ1c2VfcmVhc29ufSxmdW5jdGlvbihkYXRhKVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgaWYoZGF0YS5yZXQ9PTApXHJcbiAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLmk43kvZzmiJDlip9cIixmdW5jdGlvbigpXHJcbiAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgZWxzZVxyXG4gICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLmk43kvZzlpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH0pO1xyXG59KTtcclxuLy/ooajmoLzotoXlh7rpg6jliIbmt7vliqDnnIHnlaXlj7dcclxuZnVuY3Rpb24gd29yZE92ZXJmbG93KCkge1xyXG4gICAgJChcIi5vdmVyb21pdFwiKS5lYWNoKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIG92ZXJXb3Jkcz0kKHRoaXMpLnRleHQoKTtcclxuICAgICAgICBpZihvdmVyV29yZHMubGVuZ3RoPjEwKXtcclxuICAgICAgICAgICAgdmFyIG5ld1dvcmRzPW92ZXJXb3Jkcy5zdWJzdHJpbmcoMCwxMCkrXCIuLi5cIjtcclxuICAgICAgICAgICAgJCh0aGlzKS50ZXh0KG5ld1dvcmRzKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufTtcclxuXHJcbi8v5Yid5aeL5YyWXHJcbmZ1bmN0aW9uIGluaXQoKVxyXG57XHJcblxyXG4gICAgLy/lm57loavmkJzntKLlgLxcclxuICAgICQoXCIjc2VhcmNoXCIpLnZhbChnZXRVcmxQYXJhbShcInNlYXJjaFwiKSk7XHJcbiAgICAkKFwiI3J1bGVyXCIpLnZhbCgoZ2V0VXJsUGFyYW0oXCJydWxlclwiKT09bnVsbCAgfHwgZ2V0VXJsUGFyYW0oXCJydWxlclwiKT09JycpID8gLTEgOmdldFVybFBhcmFtKFwicnVsZXJcIikpO1xyXG5cclxuICAgIC8v5pCc57Si5oyJ6ZKuXHJcbiAgICAkKFwiI3NlYXJjaEJ1dHRvblwiKS5jbGljayhmdW5jdGlvbigpXHJcbiAgICB7XHJcbiAgICAgICAgdmFyIHNlYXJjaD0kKFwiI3NlYXJjaFwiKS52YWwoKTtcclxuICAgICAgICB2YXIgcnVsZXI9JChcIiNydWxlclwiKS52YWwoKTtcclxuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZj0nL3NhbGU/c2VhcmNoPScrc2VhcmNoKycmcnVsZXI9JytydWxlcjtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v5Zue6L2m5pCc57SiXHJcbiAgICAkKGRvY3VtZW50KS5rZXlwcmVzcyhmdW5jdGlvbihlKVxyXG4gICAge1xyXG4gICAgICAgIGlmKGUud2hpY2ggPT0gMTMpXHJcbiAgICAgICAge1xyXG4gICAgICAgICAgICAkKCcjc2VhcmNoQnV0dG9uJykudHJpZ2dlcihcImNsaWNrXCIpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG5cclxuICAgIC8v5aSN5Yi25Yiw5Ymq6LS05p2/XHJcbiAgICAoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBjbGlwYm9hcmQgPSBuZXcgQ2xpcGJvYXJkKCcuY29weUhyZWYnKTtcclxuICAgICAgICBjbGlwYm9hcmQub24oJ3N1Y2Nlc3MnLCBmdW5jdGlvbihlKSB7XHJcbiAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLlpI3liLbmiJDlip/vvIHor7flnKjlvq7kv6HlhoXmiZPlvIDlk6Yg44CCXCIpO1xyXG4gICAgICAgICAgICBlLmNsZWFyU2VsZWN0aW9uKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KSgpO1xyXG59XHJcblxyXG4vL+a4oOmBk+eahOWIh+aNolxyXG5mdW5jdGlvbiBjaGFubmVsSGFuZGxlKClcclxue1xyXG4gICAgJChcImlucHV0W25hbWU9J2NyZWF0ZV9zZWxlY3QnXVwiKS5uZXh0KCkuY2xpY2soZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgaWYoICQodGhpcykucHJldigpLnZhbCgpID09IDAgKSB7Ly/oh6rlt7HnlJ/miJDmlrDnmoTmuKDpgZNcclxuICAgICAgICAgICAgJCgnLm1vZGFsLWJvZHkgLnRhYmxlQXJlYUNvbnRhaW5lcicpLmhpZGUoKTtcclxuICAgICAgICAgICAgJChcImlucHV0W25hbWU9J2NoYW5uZWxfc2VsZWN0J11cIikuYXR0cihcImRpc2FibGVkXCIsdHJ1ZSk7XHJcbiAgICAgICAgfSBlbHNlIHsgLy/kvb/nlKjlt7LlrZjlnKjnmoTmuKDpgZNcclxuICAgICAgICAgICAgJCgnLm1vZGFsLWJvZHkgLnRhYmxlQXJlYUNvbnRhaW5lcicpLnNob3coKTtcclxuICAgICAgICAgICAgJChcImlucHV0W25hbWU9J2NoYW5uZWxfc2VsZWN0J11cIikuYXR0cihcImRpc2FibGVkXCIsZmFsc2UpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59XHJcblxyXG4vL+WQjOaEj+eUs+ivt1xyXG5mdW5jdGlvbiBhZ3JlZVNhbGUoaWQpXHJcbntcclxuICAgICQoXCIjYWdyZWVfaWRcIikudmFsKGlkKTtcclxuICAgICQoXCIjYWdyZWVNb2RhbFwiKS5tb2RhbChcInNob3dcIik7XHJcbiAgICAkKFwiI2NoYW5uZWxfbGlzdFwiKS5odG1sKCcnKTtcclxuXHJcblxyXG4gICAgJC5wb3N0KCcvZ2V0X2NoYW5uZWwnLHtcImlkXCI6aWR9LGZ1bmN0aW9uIChkYXRhKSB7XHJcblxyXG4gICAgICAgIGlmKGRhdGEuY29kZSA9PSAwKVxyXG4gICAgICAgIHtcclxuICAgICAgICAgICAgJChcIiNjaGFubmVsX2xpc3RcIikuaHRtbChkYXRhLmRhdGEpO1xyXG4gICAgICAgICAgICBpZighJChcIiNub19kYXRhXCIpLmhhc0NsYXNzKCdoaWRlJykpe1xyXG4gICAgICAgICAgICAgICAgJChcIiNub19kYXRhXCIpLmFkZENsYXNzKCdoaWRlJyk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcblxyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAvLyAkKFwiI2NoYW5uZWxfbGlzdFwiKS5odG1sKGRhdGEubXNnKTtcclxuICAgICAgICAgICAgJChcIiNub19kYXRhXCIpLnJlbW92ZUNsYXNzKCdoaWRlJyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSlcclxufVxyXG5cclxuLy/kuI3lkIzmhI/nlLPor7dcclxuZnVuY3Rpb24gZGlzQWdyZWVTYWxlKGlkKVxyXG57XHJcbiAgICAkKFwiI2Rpc2FncmVlX2lkXCIpLnZhbChpZCk7XHJcbiAgICAkKFwiI2Rpc0FncmVlTW9kYWxcIikubW9kYWwoXCJzaG93XCIpO1xyXG59Il19