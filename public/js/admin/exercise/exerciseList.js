$(document).ready(function(){$currentObj.init()});$currentObj=function(){var e={monitorAudioObj:-1};var t={};var n={playingAudioDom:0};e.init=function(){$(".operateList > li.operate").click(function(){var e=$(this),n=e.parents(".exerciseOperateArea"),i=e.parents(".singleExerciseItem"),a=e.data("type"),o=n.data("text_content"),d=n.data("original_img_urls"),l=n.data("audio_urls"),c=n.data("exercise_id"),u=n.data("exercise_title");t={textContent:o,originalImgUrlsArr:d,audioUrlsArr:l,exerciseTitle:u};switch(a){case"look_exercise_content":r();break;case"delete_exercise":s(c,i);break;default:console.log("参数错误");break}});$("#closeExeDetailWindow").click(function(){baseUtils.hideWindow("lookExerciseDetailWindow");$("#audioContentArea").html("")});$("#audioContentArea").on("click",".audioPlayStateIcon",function(){var e=$(this),t=e.parents(".audioController").siblings(".audioDom")[0],n=e.hasClass("playing");if(n){t.pause()}else{$.each(document.getElementsByTagName("audio"),function(e,t){t.pause()});t.play()}})};function i(t,n){clearInterval(e.monitorAudioObj);e.monitorAudioObj=setInterval(o,500,t,n);$("#audioContentArea").find(".audioPlayStateIcon").eq(t).removeClass("paused").addClass("playing")}function a(t,n,i){if(i){n.currentTime=0;$("#audioContentArea").find(".finishedProgress").eq(t).css({width:"0%"})}clearInterval(e.monitorAudioObj);$("#audioContentArea").find(".audioPlayStateIcon").eq(t).removeClass("playing").addClass("paused")}function o(e,t){var n=t.currentTime,i=t.duration,a=100*(n/i);$("#audioContentArea").find(".finishedProgress").eq(e).css({width:a+"%"});$("#audioContentArea").find(".progressBarDot").eq(e).css({left:a+"%"})}function r(){$("#windowHeader").text(t.exerciseTitle);try{var e=t.textContent.replace(/\n/g,"<br>");$("#textContentArea").html(e)}catch(e){$("#textContentArea").html("");console.log(e)}try{if(t.originalImgUrlsArr.length>0){var n="";$.each(t.originalImgUrlsArr,function(e,t){n+='<li><img src="'+t+'" alt="作业图片'+e+'"></li>'});$("#imgContentArea > ul").html(n)}else{$("#imgContentArea > ul").html("")}}catch(e){$("#imgContentArea > ul").html("");console.log(e)}try{if(t.audioUrlsArr.length>0){var i="";$.each(t.audioUrlsArr,function(e,t){i+='<div class="singleExeAudio">'+'<audio class="audioDom" src="'+t.audio_url+'"></audio>'+'<div class="audioController">'+'<div class="audioPlayStateIcon paused"></div>'+'<div class="progressBar">'+'<span class="finishedProgress"></span>'+"</div>"+'<div class="audioLengthSecond"><span>'+t.audio_length+'</span>"</div>'+"</div>"+"</div>"});$("#audioContentArea").html(i)}else{$("#audioContentArea").html("")}}catch(e){$("#audioContentArea").html("");console.log(e)}baseUtils.showWindow("lookExerciseDetailWindow");t.viewer=new Viewer(document.getElementById("dowebok"));l()}function s(e,t){$.alert("确认删除，删除后讲师和学员将无法看到该作业，且该操作不可撤回",{title:"删除作业",btn:3,oktext:"删除",onOk:function(){d(e,t)}})}function d(e,t){$.ajax("/exercise/change_exercise_state",{type:"POST",dataType:"json",data:{state:2,exercise_id:e,exercise_book_id:GetQueryString("exercise_book_id")},success:function(e){if(e.code==0){t.css({height:"0",padding:"0","border-bottom":"none"});setTimeout(function(){baseUtils.show.blueTip("删除成功");t.remove();var e=$("#tableContent").children(".singleExerciseItem").length;if(e==0){$("#tableContent").before('<div class="contentNoData">老师暂未布置作业，您可以引导老师在手机端店铺 [我的-我的作业] 发布课程作业</div>')}},300)}else{baseUtils.show.redTip("网络问题，请稍后再试")}},error:function(e,t,n){console.log(n);alert("服务器出小差了，请稍后再试！")}})}function l(){$.each(document.getElementsByTagName("audio"),function(e,t){t.addEventListener("playing",function(){i(e,t)});t.addEventListener("play",function(){i(e,t)});t.addEventListener("pause",function(){a(e,t)});t.addEventListener("ended",function(){a(e,t,true)});t.addEventListener("canplay",function(){var n=parseInt(t.duration);if(n&&n>0){$("#audioContentArea").find(".audioLengthSecond>span").eq(e).text(n)}})})}return e}();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImV4ZXJjaXNlTGlzdC5qcyJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJyZWFkeSIsIiRjdXJyZW50T2JqIiwiaW5pdCIsIm1vbml0b3JBdWRpb09iaiIsIiRleGVyY2lzZUluZm8iLCIkYXVkaW9JbmZvIiwicGxheWluZ0F1ZGlvRG9tIiwiY2xpY2siLCIkc2VsZiIsInRoaXMiLCIkcGFyZW50IiwicGFyZW50cyIsIiRleGVyY2lzZUl0ZW0iLCJ0eXBlIiwiZGF0YSIsInRleHRDb250ZW50Iiwib3JpZ2luYWxJbWdVcmxzQXJyIiwiYXVkaW9VcmxzQXJyIiwiZXhlcmNpc2VJZCIsImV4ZXJjaXNlVGl0bGUiLCJsb29rRXhlcmNpc2VDb250ZW50Iiwic2hvd0NvbmZpcm1XaW5kb3ciLCJjb25zb2xlIiwibG9nIiwiYmFzZVV0aWxzIiwiaGlkZVdpbmRvdyIsImh0bWwiLCJvbiIsIiR0YXJnZXQiLCJzaWJsaW5ncyIsImlzQXVkaW9QbGF5aW5nIiwiaGFzQ2xhc3MiLCJwYXVzZSIsImVhY2giLCJnZXRFbGVtZW50c0J5VGFnTmFtZSIsImsiLCJ2IiwicGxheSIsInBsYXlBdWRpb0V2ZW50IiwiY2xlYXJJbnRlcnZhbCIsInNldEludGVydmFsIiwibW9uaXRvckF1ZGlvIiwiZmluZCIsImVxIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsInBhdXNlQXVkaW9FdmVudCIsImlzRW5kZWQiLCJjdXJyZW50VGltZSIsImNzcyIsIndpZHRoIiwiY3VycmVudCIsImR1cmF0aW9uIiwicHJvZ3Jlc3NXaWR0aCIsImxlZnQiLCJ0ZXh0IiwicmVwbGFjZSIsImUiLCJsZW5ndGgiLCJpbWdDb250ZW50SHRtbCIsImF1ZGlvQ29udGVudEh0bWwiLCJhdWRpb191cmwiLCJhdWRpb19sZW5ndGgiLCJzaG93V2luZG93Iiwidmlld2VyIiwiVmlld2VyIiwiZ2V0RWxlbWVudEJ5SWQiLCJpbml0QXVkaW9QbGF5RXZlbnQiLCJhbGVydCIsInRpdGxlIiwiYnRuIiwib2t0ZXh0Iiwib25PayIsImRlbGV0ZUV4ZXJjaXNlIiwiYWpheCIsImRhdGFUeXBlIiwic3RhdGUiLCJleGVyY2lzZV9pZCIsImV4ZXJjaXNlX2Jvb2tfaWQiLCJHZXRRdWVyeVN0cmluZyIsInN1Y2Nlc3MiLCJyZXN1bHQiLCJjb2RlIiwiaGVpZ2h0IiwicGFkZGluZyIsImJvcmRlci1ib3R0b20iLCJzZXRUaW1lb3V0Iiwic2hvdyIsImJsdWVUaXAiLCJyZW1vdmUiLCJpdGVtTnVtIiwiY2hpbGRyZW4iLCJiZWZvcmUiLCJyZWRUaXAiLCJlcnJvciIsInhociIsInN0YXR1cyIsImVyciIsImFkZEV2ZW50TGlzdGVuZXIiLCJhdWRpb0R1cmF0aW9uIiwicGFyc2VJbnQiXSwibWFwcGluZ3MiOiJBQUtBQSxFQUFFQyxVQUFVQyxNQUFNLFdBQ2RDLFlBQVlDLFFBR2hCRCxhQUFjLFdBQ1YsR0FBSUEsSUFDQUUsaUJBQWtCLEVBRXRCLElBQUlDLEtBQ0osSUFBSUMsSUFDQUMsZ0JBQWlCLEVBRXJCTCxHQUFZQyxLQUFPLFdBR2ZKLEVBQUUsNkJBQTZCUyxNQUFNLFdBQ2pDLEdBQUlDLEdBQVFWLEVBQUVXLE1BQ1ZDLEVBQVVGLEVBQU1HLFFBQVEsd0JBQ3hCQyxFQUFnQkosRUFBTUcsUUFBUSx1QkFDOUJFLEVBQU9MLEVBQU1NLEtBQUssUUFDbEJDLEVBQWNMLEVBQVFJLEtBQUssZ0JBQzNCRSxFQUFxQk4sRUFBUUksS0FBSyxxQkFDbENHLEVBQWVQLEVBQVFJLEtBQUssY0FDNUJJLEVBQWFSLEVBQVFJLEtBQUssZUFDMUJLLEVBQWdCVCxFQUFRSSxLQUFLLGlCQUVqQ1YsSUFDSVcsWUFBYUEsRUFDYkMsbUJBQW9CQSxFQUNwQkMsYUFBY0EsRUFDZEUsY0FBZUEsRUFHbkIsUUFBUU4sR0FDSixJQUFLLHdCQUNETyxHQUNBLE1BQ0osS0FBSyxrQkFDREMsRUFBa0JILEVBQVlOLEVBQzlCLE1BQ0osU0FDSVUsUUFBUUMsSUFBSSxPQUNaLFNBS1p6QixHQUFFLHlCQUF5QlMsTUFBTSxXQUM3QmlCLFVBQVVDLFdBQVcs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bGUiOiJleGVyY2lzZUxpc3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogQ3JlYXRlZCBieSBBZG1pbmlzdHJhdG9yIG9uIDIwMTcvOC8yLlxyXG4gKi9cclxuXHJcblxyXG4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbiAoKSB7XHJcbiAgICAkY3VycmVudE9iai5pbml0KCk7XHJcbn0pO1xyXG5cclxuJGN1cnJlbnRPYmogPSAoZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyICRjdXJyZW50T2JqID0ge1xyXG4gICAgICAgIG1vbml0b3JBdWRpb09iajogLTEsXHJcbiAgICB9O1xyXG4gICAgdmFyICRleGVyY2lzZUluZm8gPSB7fTtcclxuICAgIHZhciAkYXVkaW9JbmZvID0ge1xyXG4gICAgICAgIHBsYXlpbmdBdWRpb0RvbTogMCxcclxuICAgIH07XHJcbiAgICAkY3VycmVudE9iai5pbml0ID0gZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICAvL+S9nOS4mueahOaTjeS9nFxyXG4gICAgICAgICQoXCIub3BlcmF0ZUxpc3QgPiBsaS5vcGVyYXRlXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcclxuICAgICAgICAgICAgICAgICRwYXJlbnQgPSAkc2VsZi5wYXJlbnRzKFwiLmV4ZXJjaXNlT3BlcmF0ZUFyZWFcIiksXHJcbiAgICAgICAgICAgICAgICAkZXhlcmNpc2VJdGVtID0gJHNlbGYucGFyZW50cyhcIi5zaW5nbGVFeGVyY2lzZUl0ZW1cIiksXHJcbiAgICAgICAgICAgICAgICB0eXBlID0gJHNlbGYuZGF0YShcInR5cGVcIiksXHJcbiAgICAgICAgICAgICAgICB0ZXh0Q29udGVudCA9ICRwYXJlbnQuZGF0YShcInRleHRfY29udGVudFwiKSxcclxuICAgICAgICAgICAgICAgIG9yaWdpbmFsSW1nVXJsc0FyciA9ICRwYXJlbnQuZGF0YShcIm9yaWdpbmFsX2ltZ191cmxzXCIpLFxyXG4gICAgICAgICAgICAgICAgYXVkaW9VcmxzQXJyID0gJHBhcmVudC5kYXRhKFwiYXVkaW9fdXJsc1wiKSxcclxuICAgICAgICAgICAgICAgIGV4ZXJjaXNlSWQgPSAkcGFyZW50LmRhdGEoXCJleGVyY2lzZV9pZFwiKSxcclxuICAgICAgICAgICAgICAgIGV4ZXJjaXNlVGl0bGUgPSAkcGFyZW50LmRhdGEoXCJleGVyY2lzZV90aXRsZVwiKTtcclxuXHJcbiAgICAgICAgICAgICRleGVyY2lzZUluZm8gPSB7XHJcbiAgICAgICAgICAgICAgICB0ZXh0Q29udGVudDogdGV4dENvbnRlbnQsXHJcbiAgICAgICAgICAgICAgICBvcmlnaW5hbEltZ1VybHNBcnI6IG9yaWdpbmFsSW1nVXJsc0FyciwvL+m7mOiupOS4uuacquWOi+e8qeWbvueJh+WGheWuuVxyXG4gICAgICAgICAgICAgICAgYXVkaW9VcmxzQXJyOiBhdWRpb1VybHNBcnIsXHJcbiAgICAgICAgICAgICAgICBleGVyY2lzZVRpdGxlOiBleGVyY2lzZVRpdGxlXHJcbiAgICAgICAgICAgIH07XHJcblxyXG4gICAgICAgICAgICBzd2l0Y2ggKHR5cGUpIHtcclxuICAgICAgICAgICAgICAgIGNhc2UgXCJsb29rX2V4ZXJjaXNlX2NvbnRlbnRcIjpcclxuICAgICAgICAgICAgICAgICAgICBsb29rRXhlcmNpc2VDb250ZW50KCk7XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBjYXNlIFwiZGVsZXRlX2V4ZXJjaXNlXCI6XHJcbiAgICAgICAgICAgICAgICAgICAgc2hvd0NvbmZpcm1XaW5kb3coZXhlcmNpc2VJZCwgJGV4ZXJjaXNlSXRlbSk7XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBkZWZhdWx0OlxyXG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKFwi5Y+C5pWw6ZSZ6K+vXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8qKioqKioqKioqKioqKioqKioqKioqKiDlpITnkIbor6bmg4Xnqpflj6MgKioqKioqKioqKioqKioqKioqKioqKioqKi9cclxuICAgICAgICAkKFwiI2Nsb3NlRXhlRGV0YWlsV2luZG93XCIpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgYmFzZVV0aWxzLmhpZGVXaW5kb3coXCJsb29rRXhlcmNpc2VEZXRhaWxXaW5kb3dcIik7XHJcbiAgICAgICAgICAgICQoXCIjYXVkaW9Db250ZW50QXJlYVwiKS5odG1sKFwiXCIpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyAg5pKt5pS+L+aaguWBnCDpn7PpopFcclxuICAgICAgICAkKFwiI2F1ZGlvQ29udGVudEFyZWFcIikub24oXCJjbGlja1wiLCBcIi5hdWRpb1BsYXlTdGF0ZUljb25cIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxyXG4gICAgICAgICAgICAgICAgJHRhcmdldCA9ICRzZWxmLnBhcmVudHMoXCIuYXVkaW9Db250cm9sbGVyXCIpLnNpYmxpbmdzKFwiLmF1ZGlvRG9tXCIpWzBdLFxyXG4gICAgICAgICAgICAgICAgaXNBdWRpb1BsYXlpbmcgPSAkc2VsZi5oYXNDbGFzcyhcInBsYXlpbmdcIik7XHJcblxyXG4gICAgICAgICAgICBpZiAoaXNBdWRpb1BsYXlpbmcpIHtcclxuICAgICAgICAgICAgICAgICR0YXJnZXQucGF1c2UoKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIC8v5YWI5pqC5YGc5omA5pyJIGF1ZGlvXHJcbiAgICAgICAgICAgICAgICAkLmVhY2goZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoXCJhdWRpb1wiKSwgZnVuY3Rpb24gKGssIHYpIHtcclxuICAgICAgICAgICAgICAgICAgICB2LnBhdXNlKCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgICR0YXJnZXQucGxheSgpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgfTtcclxuICAgIGZ1bmN0aW9uIHBsYXlBdWRpb0V2ZW50KGssIHYpIHtcclxuICAgICAgICBjbGVhckludGVydmFsKCRjdXJyZW50T2JqLm1vbml0b3JBdWRpb09iaik7XHJcbiAgICAgICAgJGN1cnJlbnRPYmoubW9uaXRvckF1ZGlvT2JqID0gc2V0SW50ZXJ2YWwobW9uaXRvckF1ZGlvLCA1MDAsIGssIHYpO1xyXG4gICAgICAgICQoXCIjYXVkaW9Db250ZW50QXJlYVwiKS5maW5kKFwiLmF1ZGlvUGxheVN0YXRlSWNvblwiKVxyXG4gICAgICAgICAgICAuZXEoaykucmVtb3ZlQ2xhc3MoXCJwYXVzZWRcIikuYWRkQ2xhc3MoXCJwbGF5aW5nXCIpO1xyXG4gICAgfTtcclxuICAgIGZ1bmN0aW9uIHBhdXNlQXVkaW9FdmVudChrLCB2LCBpc0VuZGVkKSB7XHJcbiAgICAgICAgaWYgKGlzRW5kZWQpIHtcclxuICAgICAgICAgICAgdi5jdXJyZW50VGltZSA9IDA7XHJcbiAgICAgICAgICAgICQoXCIjYXVkaW9Db250ZW50QXJlYVwiKS5maW5kKFwiLmZpbmlzaGVkUHJvZ3Jlc3NcIikuZXEoaykuY3NzKHtcIndpZHRoXCI6IFwiMCVcIn0pO1xyXG4gICAgICAgIH1cclxuICAgICAgICBjbGVhckludGVydmFsKCRjdXJyZW50T2JqLm1vbml0b3JBdWRpb09iaik7XHJcbiAgICAgICAgJChcIiNhdWRpb0NvbnRlbnRBcmVhXCIpLmZpbmQoXCIuYXVkaW9QbGF5U3RhdGVJY29uXCIpXHJcbiAgICAgICAgICAgIC5lcShrKS5yZW1vdmVDbGFzcyhcInBsYXlpbmdcIikuYWRkQ2xhc3MoXCJwYXVzZWRcIik7XHJcbiAgICB9O1xyXG4gICAgZnVuY3Rpb24gbW9uaXRvckF1ZGlvKGssIHYpIHtcclxuICAgICAgICB2YXIgY3VycmVudCA9IHYuY3VycmVudFRpbWUsXHJcbiAgICAgICAgICAgIGR1cmF0aW9uID0gdi5kdXJhdGlvbixcclxuICAgICAgICAgICAgcHJvZ3Jlc3NXaWR0aCA9IDEwMCAqIChjdXJyZW50IC8gZHVyYXRpb24pO1xyXG5cclxuICAgICAgICAkKFwiI2F1ZGlvQ29udGVudEFyZWFcIikuZmluZChcIi5maW5pc2hlZFByb2dyZXNzXCIpLmVxKGspLmNzcyh7XCJ3aWR0aFwiOiBwcm9ncmVzc1dpZHRoK1wiJVwifSk7XHJcbiAgICAgICAgJChcIiNhdWRpb0NvbnRlbnRBcmVhXCIpLmZpbmQoXCIucHJvZ3Jlc3NCYXJEb3RcIikuZXEoaykuY3NzKHtcImxlZnRcIjogcHJvZ3Jlc3NXaWR0aCtcIiVcIn0pO1xyXG4gICAgfTtcclxuICAgIGZ1bmN0aW9uIGxvb2tFeGVyY2lzZUNvbnRlbnQoKSB7XHJcblxyXG4gICAgICAgIC8v5aGr5YWF5pWw5o2uXHJcbiAgICAgICAgJChcIiN3aW5kb3dIZWFkZXJcIikudGV4dCgkZXhlcmNpc2VJbmZvLmV4ZXJjaXNlVGl0bGUpO1xyXG5cclxuICAgICAgICB0cnkge1xyXG4gICAgICAgICAgICB2YXIgdGV4dENvbnRlbnQgPSAkZXhlcmNpc2VJbmZvLnRleHRDb250ZW50LnJlcGxhY2UoL1xcbi9nLCAnPGJyPicpO1xyXG4gICAgICAgICAgICAkKFwiI3RleHRDb250ZW50QXJlYVwiKS5odG1sKHRleHRDb250ZW50KTtcclxuICAgICAgICB9ICBjYXRjaCAoZSkge1xyXG4gICAgICAgICAgICAkKFwiI3RleHRDb250ZW50QXJlYVwiKS5odG1sKFwiXCIpO1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhlKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHRyeSB7XHJcbiAgICAgICAgICAgIGlmICgkZXhlcmNpc2VJbmZvLm9yaWdpbmFsSW1nVXJsc0Fyci5sZW5ndGggPiAwKSB7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW1nQ29udGVudEh0bWwgPSBcIlwiO1xyXG4gICAgICAgICAgICAgICAgJC5lYWNoKCRleGVyY2lzZUluZm8ub3JpZ2luYWxJbWdVcmxzQXJyLCBmdW5jdGlvbiAoaywgdikge1xyXG4gICAgICAgICAgICAgICAgICAgIGltZ0NvbnRlbnRIdG1sICs9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICc8bGk+PGltZyBzcmM9XCInK3YrJ1wiIGFsdD1cIuS9nOS4muWbvueJhycraysnXCI+PC9saT4nO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICAkKFwiI2ltZ0NvbnRlbnRBcmVhID4gdWxcIikuaHRtbChpbWdDb250ZW50SHRtbCk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAkKFwiI2ltZ0NvbnRlbnRBcmVhID4gdWxcIikuaHRtbChcIlwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0gY2F0Y2ggKGUpIHtcclxuICAgICAgICAgICAgJChcIiNpbWdDb250ZW50QXJlYSA+IHVsXCIpLmh0bWwoXCJcIik7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGUpO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0cnkge1xyXG4gICAgICAgICAgICBpZiAoJGV4ZXJjaXNlSW5mby5hdWRpb1VybHNBcnIubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICAgICAgdmFyIGF1ZGlvQ29udGVudEh0bWwgPSBcIlwiO1xyXG4gICAgICAgICAgICAgICAgJC5lYWNoKCRleGVyY2lzZUluZm8uYXVkaW9VcmxzQXJyLCBmdW5jdGlvbiAoaywgdikge1xyXG4gICAgICAgICAgICAgICAgICAgIGF1ZGlvQ29udGVudEh0bWwgKz1cclxuICAgICAgICAgICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJzaW5nbGVFeGVBdWRpb1wiPicrXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnPGF1ZGlvIGNsYXNzPVwiYXVkaW9Eb21cIiBzcmM9XCInK3YuYXVkaW9fdXJsKydcIj48L2F1ZGlvPicrXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cImF1ZGlvQ29udHJvbGxlclwiPicrXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJhdWRpb1BsYXlTdGF0ZUljb24gcGF1c2VkXCI+PC9kaXY+JytcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cInByb2dyZXNzQmFyXCI+JytcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxzcGFuIGNsYXNzPVwiZmluaXNoZWRQcm9ncmVzc1wiPjwvc3Bhbj4nK1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyAnPHNwYW4gY2xhc3M9XCJwcm9ncmVzc0JhckRvdFwiPjwvc3Bhbj4nK1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICc8L2Rpdj4nK1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwiYXVkaW9MZW5ndGhTZWNvbmRcIj48c3Bhbj4nK3YuYXVkaW9fbGVuZ3RoKyc8L3NwYW4+XCI8L2Rpdj4nK1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJzwvZGl2PicrXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICc8L2Rpdj4nO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICAkKFwiI2F1ZGlvQ29udGVudEFyZWFcIikuaHRtbChhdWRpb0NvbnRlbnRIdG1sKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICQoXCIjYXVkaW9Db250ZW50QXJlYVwiKS5odG1sKFwiXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSBjYXRjaCAoZSkge1xyXG4gICAgICAgICAgICAkKFwiI2F1ZGlvQ29udGVudEFyZWFcIikuaHRtbChcIlwiKTtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coZSk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBiYXNlVXRpbHMuc2hvd1dpbmRvdyhcImxvb2tFeGVyY2lzZURldGFpbFdpbmRvd1wiKTtcclxuXHJcbiAgICAgICAgLy/mj5Lku7YgVmlld2VyLmpzIOWIneWni+WMllxyXG4gICAgICAgICRleGVyY2lzZUluZm8udmlld2VyID0gbmV3IFZpZXdlcihkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZG93ZWJvaycpKTtcclxuXHJcbiAgICAgICAgLy9hdWRpbyDmkq3mlL7kuovku7ZcclxuICAgICAgICBpbml0QXVkaW9QbGF5RXZlbnQoKTtcclxuICAgIH07XHJcbiAgICBmdW5jdGlvbiBzaG93Q29uZmlybVdpbmRvdyhleGVyY2lzZUlkLCAkZXhlcmNpc2VJdGVtKSB7XHJcbiAgICAgICAgJC5hbGVydChcIuehruiupOWIoOmZpO+8jOWIoOmZpOWQjuiusuW4iOWSjOWtpuWRmOWwhuaXoOazleeci+WIsOivpeS9nOS4mu+8jOS4lOivpeaTjeS9nOS4jeWPr+aSpOWbnlwiLCB7XHJcbiAgICAgICAgICAgIHRpdGxlOiBcIuWIoOmZpOS9nOS4mlwiLFxyXG4gICAgICAgICAgICBidG46IDMsXHJcbiAgICAgICAgICAgIG9rdGV4dDogXCLliKDpmaRcIixcclxuICAgICAgICAgICAgb25PazogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgZGVsZXRlRXhlcmNpc2UoZXhlcmNpc2VJZCwgJGV4ZXJjaXNlSXRlbSk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSk7XHJcbiAgICB9O1xyXG4gICAgZnVuY3Rpb24gZGVsZXRlRXhlcmNpc2UoZXhlcmNpc2VJZCwgJGV4ZXJjaXNlSXRlbSkge1xyXG4gICAgICAgICQuYWpheChcIi9leGVyY2lzZS9jaGFuZ2VfZXhlcmNpc2Vfc3RhdGVcIiwge1xyXG4gICAgICAgICAgICB0eXBlOiBcIlBPU1RcIixcclxuICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxyXG4gICAgICAgICAgICBkYXRhOiB7XHJcbiAgICAgICAgICAgICAgICBzdGF0ZTogMixcclxuICAgICAgICAgICAgICAgIGV4ZXJjaXNlX2lkOiBleGVyY2lzZUlkLFxyXG4gICAgICAgICAgICAgICAgZXhlcmNpc2VfYm9va19pZDogR2V0UXVlcnlTdHJpbmcoXCJleGVyY2lzZV9ib29rX2lkXCIpLFxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgICAgICAgICBpZiAocmVzdWx0LmNvZGUgPT0gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgICRleGVyY2lzZUl0ZW0uY3NzKHtcImhlaWdodFwiOiBcIjBcIiwgXCJwYWRkaW5nXCI6IFwiMFwiLCBcImJvcmRlci1ib3R0b21cIjogXCJub25lXCJ9KTtcclxuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChcIuWIoOmZpOaIkOWKn1wiKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJGV4ZXJjaXNlSXRlbS5yZW1vdmUoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGl0ZW1OdW0gPSAkKFwiI3RhYmxlQ29udGVudFwiKS5jaGlsZHJlbihcIi5zaW5nbGVFeGVyY2lzZUl0ZW1cIikubGVuZ3RoO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaXRlbU51bSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKFwiI3RhYmxlQ29udGVudFwiKS5iZWZvcmUoJzxkaXYgY2xhc3M9XCJjb250ZW50Tm9EYXRhXCI+6ICB5biI5pqC5pyq5biD572u5L2c5Lia77yM5oKo5Y+v5Lul5byV5a+86ICB5biI5Zyo5omL5py656uv5bqX6ZO6IFvmiJHnmoQt5oiR55qE5L2c5LiaXSDlj5HluIPor77nqIvkvZzkuJo8L2Rpdj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0sIDMwMCk7XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIue9kee7nOmXrumimO+8jOivt+eojeWQjuWGjeivlVwiKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uICh4aHIsIHN0YXR1cywgZXJyKSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhlcnIpO1xyXG4gICAgICAgICAgICAgICAgYWxlcnQoXCLmnI3liqHlmajlh7rlsI/lt67kuobvvIzor7fnqI3lkI7lho3or5XvvIFcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH07XHJcbiAgICBmdW5jdGlvbiBpbml0QXVkaW9QbGF5RXZlbnQoKSB7XHJcblxyXG4gICAgICAgIC8v54K55Ye76Z+z6aKR5LqL5Lu2XHJcbiAgICAgICAgJC5lYWNoKGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKFwiYXVkaW9cIiksIGZ1bmN0aW9uIChrLCB2KSB7XHJcbiAgICAgICAgICAgIC8v5byA5aeL5pKt5pS+XHJcbiAgICAgICAgICAgIHYuYWRkRXZlbnRMaXN0ZW5lcihcInBsYXlpbmdcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgcGxheUF1ZGlvRXZlbnQoaywgdik7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB2LmFkZEV2ZW50TGlzdGVuZXIoXCJwbGF5XCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHBsYXlBdWRpb0V2ZW50KGssIHYpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgdi5hZGRFdmVudExpc3RlbmVyKFwicGF1c2VcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgcGF1c2VBdWRpb0V2ZW50KGssIHYpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgdi5hZGRFdmVudExpc3RlbmVyKFwiZW5kZWRcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgcGF1c2VBdWRpb0V2ZW50KGssIHYsIHRydWUpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgLy/liJ3lp4vljJZhdWRpb+aXtumVv1xyXG4gICAgICAgICAgICB2LmFkZEV2ZW50TGlzdGVuZXIoXCJjYW5wbGF5XCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHZhciBhdWRpb0R1cmF0aW9uID0gcGFyc2VJbnQodi5kdXJhdGlvbik7XHJcbiAgICAgICAgICAgICAgICBpZiAoYXVkaW9EdXJhdGlvbiAmJiBhdWRpb0R1cmF0aW9uID4gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgICQoXCIjYXVkaW9Db250ZW50QXJlYVwiKS5maW5kKFwiLmF1ZGlvTGVuZ3RoU2Vjb25kPnNwYW5cIikuZXEoaykudGV4dChhdWRpb0R1cmF0aW9uKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8vICQoXCIjYXVkaW9Db250ZW50QXJlYVwiKS5maW5kKFwiLnByb2dyZXNzQmFyRG90XCIpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIC8vXHJcbiAgICAgICAgLy9cclxuICAgICAgICAvLyB9KTtcclxuXHJcbiAgICB9O1xyXG5cclxuICAgIHJldHVybiAkY3VycmVudE9iajtcclxufSkoKTtcclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuIl19