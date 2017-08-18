$(document).ready(function(){$manageExercise.init()});$manageExercise=function(){var e={};var i={submitLimit:false,pageType:$("#admin_data").data("page_type")};e.init=function(){changeSaveFlag(true);if(i.pageType==1){i.resType=$("#admin_data").data("resource_type");i.resId=$("#admin_data").data("resource_id");i.resName=$("#admin_data").data("resource_name");i.resPrice=$("#admin_data").data("resource_price")}$(".characterNumLimit>span").text($("#exerciseBookName").val().length);$("#exerciseBookName").on("input",function(){r()});$("#resTypeSelector").on("change",function(){var e=$(this).val();if(e==-1){$("#resItemSelector").html('<option data-res_id="-1">请选择具体课程</option>')}else{$.ajax("/exercise/get_resource_list",{type:"POST",dataType:"json",data:{resource_type:e},success:function(r){if(r.code==0){var a=r.data.resource_list,t="";if(i.pageType==1&&i.resType==e){t+='<option data-res_id="'+i.resId+'" data-res_price="'+i.resPrice+'" selected>'+i.resName+"</option>"}$.each(a,function(i,r){var a=e==5?r.name:r.title,s=e==5?r.price:r.piece_price;t+='<option data-res_id="'+r.id+'" data-res_price="'+s+'" >'+a+"</option>"});if(t.length==0){t='<option data-res_id="-1">暂无数据</option>'}$("#resItemSelector").html(t)}else{baseUtils.show.redTip("网络问题，请稍后再试")}},error:function(e,i,r){console.log(r);alert("服务器出小差了，请稍后再试！")}})}});$("#resItemSelector").on("change",function(){var e=$(this).children("option:selected"),i=e.data("res_id"),r=e.data("res_price"),a=$(".circleRadio.radioActive").data("is_remind");if(i!=-1&&r==0&&a==1){baseUtils.show.redTip("您关联的课程为免费课程，暂不能开启作业推送功能");$(".remindRadio").children(".circleRadio").removeClass("radioActive");$("#noRemindMsg").addClass("radioActive");return false}});$(".remindRadio").on("click",function(){var e=$(".circleRadio.radioActive").data("is_remind"),i=$("#resItemSelector > option:selected").data("res_id"),r=$("#resItemSelector > option:selected").data("res_price"),a=$(this);if(a.children(".circleRadio").hasClass("radioActive")){return false}if(e==0){if(i!=-1&&r==0){baseUtils.show.redTip("您关联的课程为免费课程，暂不能开启作业推送功能");return false}}$(".remindRadio").children(".circleRadio").removeClass("radioActive");a.children(".circleRadio").addClass("radioActive")});$("#cancelSaveExercise").click(function(){window.history.back()});$("#confirmSaveExercise").click(function(){var e=$("#resItemSelector > option:selected"),r=$.trim($("#exerciseBookName").val()),a=e.text(),t=e.data("res_id"),s=e.data("res_price"),o=$("#resTypeSelector").val(),c=$("#communitySelector").val(),d=$(".circleRadio.radioActive").data("is_remind"),n="";if(r.length==0){baseUtils.show.redTip("还没有输入作业本名称");return false}if(r.length>14){baseUtils.show.redTip("作业本名称长度不能超过14字");return false}if(t==-1){baseUtils.show.redTip("还没有选择具体课程");return false}if(s==0&&d==1){baseUtils.show.redTip("开启提醒的作业本不能关联免费课程");return false}var l={title:r,resource_id:t,resource_type:o,resource_name:a,is_enable_notify:d};if(c!=-1&&c!=undefined){l.community_id=c}if(i.pageType==0){n="/exercise/upload_exercise_book"}else if(i.pageType==1){n="/exercise/update_exercise_book";l.exercise_book_id=GetQueryString("exercise_book_id")}else{console.log(" pageType 参数有误。");baseUtils.show.redTip("网络错误，请稍后再试");return false}if(i.submitLimit){baseUtils.show.redTip("正在提交中，请稍后再试");return false}i.submitLimit=true;$.ajax(n,{type:"POST",dataType:"json",data:{params:l},success:function(e){i.submitLimit=false;if(e.code==0){baseUtils.show.blueTip(i.pageType==0?"创建成功":"编辑成功");if(i.pageType==0){var r=e.data.exerciseBookId;sessionStorage.setItem("lastExerciseBookId",r);sessionStorage.setItem("lastResourceType",o)}setTimeout(function(){var e=GetQueryString("page_index")||1;window.location.href="/exercise/exercise_book_list?page="+e},1e3)}else{baseUtils.show.redTip(e.msg)}},error:function(e,r,a){i.submitLimit=false;console.log(a);alert("服务器出小差了，请稍后再试！")}})})};function r(){var e=$("#exerciseBookName"),i=$.trim(e.val());if(i.length>14){$(".characterNumLimit").css({color:"red"});$("#exerciseBookName").css({"border-color":"red"})}else{$(".characterNumLimit").css({color:"#b2b2b2"});$("#exerciseBookName").css({"border-color":"#dcdcdc"})}$(".characterNumLimit>span").text(i.length)}return e}();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1hbmFnZUV4ZXJjaXNlQm9vay5qcyJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJyZWFkeSIsIiRtYW5hZ2VFeGVyY2lzZSIsImluaXQiLCIkcHJpdmF0ZSIsInN1Ym1pdExpbWl0IiwicGFnZVR5cGUiLCJkYXRhIiwiY2hhbmdlU2F2ZUZsYWciLCJyZXNUeXBlIiwicmVzSWQiLCJyZXNOYW1lIiwicmVzUHJpY2UiLCJ0ZXh0IiwidmFsIiwibGVuZ3RoIiwib24iLCJjaGVja0lucHV0TmFtZSIsInRoaXMiLCJodG1sIiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsInJlc291cmNlX3R5cGUiLCJzdWNjZXNzIiwicmVzdWx0IiwiY29kZSIsInJlc291cmNlTGlzdCIsInJlc291cmNlX2xpc3QiLCJodG1sU3RyIiwiZWFjaCIsImsiLCJ2IiwibmFtZSIsInRpdGxlIiwicHJpY2UiLCJwaWVjZV9wcmljZSIsImlkIiwiYmFzZVV0aWxzIiwic2hvdyIsInJlZFRpcCIsImVycm9yIiwieGhyIiwic3RhdHVzIiwiZXJyIiwiY29uc29sZSIsImxvZyIsImFsZXJ0IiwiJHRoaXMiLCJjaGlsZHJlbiIsImlzUmVtaW5kIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImhhc0NsYXNzIiwiY2xpY2siLCJ3aW5kb3ciLCJoaXN0b3J5IiwiYmFjayIsIiRzZWxlY3RlZFJlc0l0ZW0iLCJleGVyY2lzZUJvb2tOYW1lIiwidHJpbSIsImNvbW11bml0eUlkIiwicG9zdFVybCIsInBhcmFtcyIsInJlc291cmNlX2lkIiwicmVzb3VyY2VfbmFtZSIsImlzX2VuYWJsZV9ub3RpZnkiLCJ1bmRlZmluZWQiLCJjb21tdW5pdHlfaWQiLCJleGVyY2lzZV9ib29rX2lkIiwiR2V0UXVlcnlTdHJpbmciLCJibHVlVGlwIiwiZXhlcmNpc2VCb29rSWQiLCJzZXNzaW9uU3RvcmFnZSIsInNldEl0ZW0iLCJzZXRUaW1lb3V0IiwicGFnZUluZGV4IiwibG9jYXRpb24iLCJocmVmIiwibXNn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TUFBT2IiLCJmaWxlIjoibWFuYWdlRXhlcmNpc2VCb29rLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqIENyZWF0ZWQgYnkgQWRtaW5pc3RyYXRvciBvbiAyMDE3LzcvMzEuXHJcbiAqL1xyXG5cclxuJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xyXG4gICAgJG1hbmFnZUV4ZXJjaXNlLmluaXQoKTtcclxufSk7XHJcblxyXG4kbWFuYWdlRXhlcmNpc2UgPSAoZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyICRtYW5hZ2VFeGVyY2lzZSA9IHt9O1xyXG4gICAgdmFyICRwcml2YXRlID0ge1xyXG4gICAgICAgIHN1Ym1pdExpbWl0OiBmYWxzZSxcclxuICAgICAgICBwYWdlVHlwZTogJChcIiNhZG1pbl9kYXRhXCIpLmRhdGEoXCJwYWdlX3R5cGVcIiksICAgICAgIC8vMC3liJvlu7rvvJsxLee8lui+kVxyXG4gICAgfVxyXG5cclxuICAgICRtYW5hZ2VFeGVyY2lzZS5pbml0ID0gZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICAvL+eCueWHu+S+p+i+ueagj+emu+W8gOaXtueahOW8ueahhlxyXG4gICAgICAgIGNoYW5nZVNhdmVGbGFnKHRydWUpO1xyXG5cclxuICAgICAgICBpZiAoJHByaXZhdGUucGFnZVR5cGUgPT0gMSkge1xyXG4gICAgICAgICAgICAkcHJpdmF0ZS5yZXNUeXBlID0gJChcIiNhZG1pbl9kYXRhXCIpLmRhdGEoXCJyZXNvdXJjZV90eXBlXCIpOyAgICAvL+W3suWFs+iBlOivvueoi+eahCB0eXBlXHJcbiAgICAgICAgICAgICRwcml2YXRlLnJlc0lkID0gJChcIiNhZG1pbl9kYXRhXCIpLmRhdGEoXCJyZXNvdXJjZV9pZFwiKTsgICAgICAgICAgLy/lt7LlhbPogZTor77nqIvnmoQgaWRcclxuICAgICAgICAgICAgJHByaXZhdGUucmVzTmFtZSA9ICQoXCIjYWRtaW5fZGF0YVwiKS5kYXRhKFwicmVzb3VyY2VfbmFtZVwiKTsgICAgLy/lt7LlhbPogZTor77nqIvnmoQgbmFtZVxyXG4gICAgICAgICAgICAkcHJpdmF0ZS5yZXNQcmljZSA9ICQoXCIjYWRtaW5fZGF0YVwiKS5kYXRhKFwicmVzb3VyY2VfcHJpY2VcIik7ICAgIC8v5bey5YWz6IGU6K++56iLIHByaWNlXHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAkKFwiLmNoYXJhY3Rlck51bUxpbWl0PnNwYW5cIikudGV4dCgkKFwiI2V4ZXJjaXNlQm9va05hbWVcIikudmFsKCkubGVuZ3RoKTtcclxuXHJcbiAgICAgICAgJChcIiNleGVyY2lzZUJvb2tOYW1lXCIpLm9uKFwiaW5wdXRcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBjaGVja0lucHV0TmFtZSgpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+WFs+iBlOivvueoi1xyXG4gICAgICAgICQoXCIjcmVzVHlwZVNlbGVjdG9yXCIpLm9uKFwiY2hhbmdlXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyIHJlc1R5cGUgPSAkKHRoaXMpLnZhbCgpO1xyXG4gICAgICAgICAgICBpZiAocmVzVHlwZSA9PSAtMSkge1xyXG4gICAgICAgICAgICAgICAgJChcIiNyZXNJdGVtU2VsZWN0b3JcIikuaHRtbChcclxuICAgICAgICAgICAgICAgICAgICAnPG9wdGlvbiBkYXRhLXJlc19pZD1cIi0xXCI+6K+36YCJ5oup5YW35L2T6K++56iLPC9vcHRpb24+J1xyXG4gICAgICAgICAgICAgICAgKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICQuYWpheChcIi9leGVyY2lzZS9nZXRfcmVzb3VyY2VfbGlzdFwiLCB7XHJcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXHJcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxyXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzb3VyY2VfdHlwZTogcmVzVHlwZVxyXG4gICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0LmNvZGUgPT0gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHJlc291cmNlTGlzdCA9IHJlc3VsdC5kYXRhLnJlc291cmNlX2xpc3QsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaHRtbFN0ciA9IFwiXCI7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCRwcml2YXRlLnBhZ2VUeXBlID09IDEgJiYgJHByaXZhdGUucmVzVHlwZSA9PSByZXNUeXBlKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaHRtbFN0ciArPVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnPG9wdGlvbiBkYXRhLXJlc19pZD1cIicrJHByaXZhdGUucmVzSWQrJ1wiIGRhdGEtcmVzX3ByaWNlPVwiJyskcHJpdmF0ZS5yZXNQcmljZSsnXCIgc2VsZWN0ZWQ+JyskcHJpdmF0ZS5yZXNOYW1lKyc8L29wdGlvbj4nO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJC5lYWNoKHJlc291cmNlTGlzdCwgZnVuY3Rpb24gKGssIHYpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgcmVzTmFtZSA9IChyZXNUeXBlPT01P3YubmFtZTp2LnRpdGxlKSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzUHJpY2UgPSAocmVzVHlwZT09NT92LnByaWNlOnYucGllY2VfcHJpY2UpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGh0bWxTdHIgKz1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxvcHRpb24gZGF0YS1yZXNfaWQ9XCInK3YuaWQrJ1wiIGRhdGEtcmVzX3ByaWNlPVwiJytyZXNQcmljZSsnXCIgPicrcmVzTmFtZSsnPC9vcHRpb24+JztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGh0bWxTdHIubGVuZ3RoID09IDApIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBodG1sU3RyID1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxvcHRpb24gZGF0YS1yZXNfaWQ9XCItMVwiPuaaguaXoOaVsOaNrjwvb3B0aW9uPic7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKFwiI3Jlc0l0ZW1TZWxlY3RvclwiKS5odG1sKGh0bWxTdHIpO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIue9kee7nOmXrumimO+8jOivt+eojeWQjuWGjeivlVwiKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uICh4aHIsIHN0YXR1cywgZXJyKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGVycik7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGFsZXJ0KFwi5pyN5Yqh5Zmo5Ye65bCP5beu5LqG77yM6K+356iN5ZCO5YaN6K+V77yBXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+mAieaLqeivvueoi1xyXG4gICAgICAgICQoXCIjcmVzSXRlbVNlbGVjdG9yXCIpLm9uKFwiY2hhbmdlXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKS5jaGlsZHJlbihcIm9wdGlvbjpzZWxlY3RlZFwiKSxcclxuICAgICAgICAgICAgICAgIHJlc0lkID0gJHRoaXMuZGF0YShcInJlc19pZFwiKSxcclxuICAgICAgICAgICAgICAgIHJlc1ByaWNlID0gJHRoaXMuZGF0YShcInJlc19wcmljZVwiKSxcclxuICAgICAgICAgICAgICAgIGlzUmVtaW5kID0gJChcIi5jaXJjbGVSYWRpby5yYWRpb0FjdGl2ZVwiKS5kYXRhKFwiaXNfcmVtaW5kXCIpO1xyXG5cclxuICAgICAgICAgICAgaWYgKHJlc0lkICE9IC0xICYmIHJlc1ByaWNlID09IDAgJiYgaXNSZW1pbmQgPT0gMSkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5oKo5YWz6IGU55qE6K++56iL5Li65YWN6LS56K++56iL77yM5pqC5LiN6IO95byA5ZCv5L2c5Lia5o6o6YCB5Yqf6IO9XCIpO1xyXG4gICAgICAgICAgICAgICAgJCgnLnJlbWluZFJhZGlvJykuY2hpbGRyZW4oJy5jaXJjbGVSYWRpbycpLnJlbW92ZUNsYXNzKCdyYWRpb0FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgJCgnI25vUmVtaW5kTXNnJykuYWRkQ2xhc3MoJ3JhZGlvQWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy8gcmFkaW8g5oyJ6ZKu54K55Ye75YiH5o2i5pWI5p6cXHJcbiAgICAgICAgJCgnLnJlbWluZFJhZGlvJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XHJcblxyXG4gICAgICAgICAgICB2YXIgaXNSZW1pbmQgPSAkKFwiLmNpcmNsZVJhZGlvLnJhZGlvQWN0aXZlXCIpLmRhdGEoXCJpc19yZW1pbmRcIiksICAgICAvL+W9k+WJjeeKtuaAgVxyXG4gICAgICAgICAgICAgICAgcmVzSWQgPSAkKFwiI3Jlc0l0ZW1TZWxlY3RvciA+IG9wdGlvbjpzZWxlY3RlZFwiKS5kYXRhKFwicmVzX2lkXCIpLFxyXG4gICAgICAgICAgICAgICAgcmVzUHJpY2UgPSAkKFwiI3Jlc0l0ZW1TZWxlY3RvciA+IG9wdGlvbjpzZWxlY3RlZFwiKS5kYXRhKFwicmVzX3ByaWNlXCIpLFxyXG4gICAgICAgICAgICAgICAgJHRoaXMgPSAkKHRoaXMpO1xyXG5cclxuICAgICAgICAgICAgaWYgKCR0aGlzLmNoaWxkcmVuKFwiLmNpcmNsZVJhZGlvXCIpLmhhc0NsYXNzKFwicmFkaW9BY3RpdmVcIikpIHsgICAgLy/kuI3lpITnkIZcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZiAoaXNSZW1pbmQgPT0gMCkgeyAgICAvL+S4jeaPkOmGkiA9PiDmj5DphpJcclxuICAgICAgICAgICAgICAgIGlmIChyZXNJZCAhPSAtMSAmJiByZXNQcmljZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5oKo5YWz6IGU55qE6K++56iL5Li65YWN6LS56K++56iL77yM5pqC5LiN6IO95byA5ZCv5L2c5Lia5o6o6YCB5Yqf6IO9XCIpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAkKCcucmVtaW5kUmFkaW8nKS5jaGlsZHJlbignLmNpcmNsZVJhZGlvJykucmVtb3ZlQ2xhc3MoJ3JhZGlvQWN0aXZlJyk7XHJcbiAgICAgICAgICAgICR0aGlzLmNoaWxkcmVuKCcuY2lyY2xlUmFkaW8nKS5hZGRDbGFzcygncmFkaW9BY3RpdmUnKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgJChcIiNjYW5jZWxTYXZlRXhlcmNpc2VcIikuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB3aW5kb3cuaGlzdG9yeS5iYWNrKCk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICQoXCIjY29uZmlybVNhdmVFeGVyY2lzZVwiKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHZhciAkc2VsZWN0ZWRSZXNJdGVtID0gJChcIiNyZXNJdGVtU2VsZWN0b3IgPiBvcHRpb246c2VsZWN0ZWRcIiksXHJcbiAgICAgICAgICAgICAgICBleGVyY2lzZUJvb2tOYW1lID0gJC50cmltKCQoXCIjZXhlcmNpc2VCb29rTmFtZVwiKS52YWwoKSksXHJcbiAgICAgICAgICAgICAgICByZXNOYW1lID0gJHNlbGVjdGVkUmVzSXRlbS50ZXh0KCksXHJcbiAgICAgICAgICAgICAgICByZXNJZCA9ICRzZWxlY3RlZFJlc0l0ZW0uZGF0YShcInJlc19pZFwiKSxcclxuICAgICAgICAgICAgICAgIHJlc1ByaWNlID0gJHNlbGVjdGVkUmVzSXRlbS5kYXRhKFwicmVzX3ByaWNlXCIpLFxyXG4gICAgICAgICAgICAgICAgcmVzVHlwZSA9ICQoXCIjcmVzVHlwZVNlbGVjdG9yXCIpLnZhbCgpLFxyXG4gICAgICAgICAgICAgICAgY29tbXVuaXR5SWQgPSAkKFwiI2NvbW11bml0eVNlbGVjdG9yXCIpLnZhbCgpLFxyXG4gICAgICAgICAgICAgICAgaXNSZW1pbmQgPSAkKFwiLmNpcmNsZVJhZGlvLnJhZGlvQWN0aXZlXCIpLmRhdGEoXCJpc19yZW1pbmRcIiksIC8vIDEgLSDmj5DphpLvvIwgMCAtIOS4jeaPkOmGkjzpu5jorqTkuI3mj5DphpI+XHJcbiAgICAgICAgICAgICAgICBwb3N0VXJsID0gXCJcIjtcclxuXHJcbiAgICAgICAgICAgIGlmIChleGVyY2lzZUJvb2tOYW1lLmxlbmd0aCA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoXCLov5jmsqHmnInovpPlhaXkvZzkuJrmnKzlkI3np7BcIik7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYgKGV4ZXJjaXNlQm9va05hbWUubGVuZ3RoID4gMTQpIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuS9nOS4muacrOWQjeensOmVv+W6puS4jeiDvei2hei/hzE05a2XXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmIChyZXNJZCA9PSAtMSkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi6L+Y5rKh5pyJ6YCJ5oup5YW35L2T6K++56iLXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmIChyZXNQcmljZSA9PSAwICYmIGlzUmVtaW5kID09IDEpIHtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcChcIuW8gOWQr+aPkOmGkueahOS9nOS4muacrOS4jeiDveWFs+iBlOWFjei0ueivvueoi1wiKTtcclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB2YXIgcGFyYW1zID0ge1xyXG4gICAgICAgICAgICAgICAgdGl0bGU6IGV4ZXJjaXNlQm9va05hbWUsXHJcbiAgICAgICAgICAgICAgICByZXNvdXJjZV9pZDogcmVzSWQsXHJcbiAgICAgICAgICAgICAgICByZXNvdXJjZV90eXBlOiByZXNUeXBlLFxyXG4gICAgICAgICAgICAgICAgcmVzb3VyY2VfbmFtZTogcmVzTmFtZSxcclxuICAgICAgICAgICAgICAgIGlzX2VuYWJsZV9ub3RpZnk6IGlzUmVtaW5kXHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYgKGNvbW11bml0eUlkICE9IC0xICYmIGNvbW11bml0eUlkICE9IHVuZGVmaW5lZCkge1xyXG4gICAgICAgICAgICAgICAgcGFyYW1zLmNvbW11bml0eV9pZCA9IGNvbW11bml0eUlkO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmICgkcHJpdmF0ZS5wYWdlVHlwZSA9PSAwKSB7ICAgICAgICAgICAgLy/liJvlu7pcclxuICAgICAgICAgICAgICAgIHBvc3RVcmwgPSBcIi9leGVyY2lzZS91cGxvYWRfZXhlcmNpc2VfYm9va1wiO1xyXG4gICAgICAgICAgICB9IGVsc2UgaWYgKCRwcml2YXRlLnBhZ2VUeXBlID09IDEpIHsgICAgIC8v57yW6L6RXHJcbiAgICAgICAgICAgICAgICBwb3N0VXJsID0gXCIvZXhlcmNpc2UvdXBkYXRlX2V4ZXJjaXNlX2Jvb2tcIjtcclxuICAgICAgICAgICAgICAgIHBhcmFtcy5leGVyY2lzZV9ib29rX2lkID0gR2V0UXVlcnlTdHJpbmcoXCJleGVyY2lzZV9ib29rX2lkXCIpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCIgcGFnZVR5cGUg5Y+C5pWw5pyJ6K+v44CCXCIpO1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi572R57uc6ZSZ6K+v77yM6K+356iN5ZCO5YaN6K+VXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmICgkcHJpdmF0ZS5zdWJtaXRMaW1pdCkge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5q2j5Zyo5o+Q5Lqk5Lit77yM6K+356iN5ZCO5YaN6K+VXCIpO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICRwcml2YXRlLnN1Ym1pdExpbWl0ID0gdHJ1ZTtcclxuICAgICAgICAgICAgJC5hamF4KHBvc3RVcmwsIHtcclxuICAgICAgICAgICAgICAgIHR5cGU6IFwiUE9TVFwiLFxyXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxyXG4gICAgICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgICAgIHBhcmFtczogcGFyYW1zXHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgICAgICAgICAgICAgICRwcml2YXRlLnN1Ym1pdExpbWl0ID0gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3VsdC5jb2RlID09IDApIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcCgkcHJpdmF0ZS5wYWdlVHlwZT09MD9cIuWIm+W7uuaIkOWKn1wiOlwi57yW6L6R5oiQ5YqfXCIpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJHByaXZhdGUucGFnZVR5cGUgPT0gMCkgeyAgIC8v5Yib5bu6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgZXhlcmNpc2VCb29rSWQgPSByZXN1bHQuZGF0YS5leGVyY2lzZUJvb2tJZDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlc3Npb25TdG9yYWdlLnNldEl0ZW0oXCJsYXN0RXhlcmNpc2VCb29rSWRcIiwgZXhlcmNpc2VCb29rSWQpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2Vzc2lvblN0b3JhZ2Uuc2V0SXRlbShcImxhc3RSZXNvdXJjZVR5cGVcIiwgcmVzVHlwZSk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHBhZ2VJbmRleCA9IEdldFF1ZXJ5U3RyaW5nKFwicGFnZV9pbmRleFwiKSB8fCAxO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBcIi9leGVyY2lzZS9leGVyY2lzZV9ib29rX2xpc3Q/cGFnZT1cIiArIHBhZ2VJbmRleDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMTAwMCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKHJlc3VsdC5tc2cpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24gKHhociwgc3RhdHVzLCBlcnIpIHtcclxuICAgICAgICAgICAgICAgICAgICAkcHJpdmF0ZS5zdWJtaXRMaW1pdCA9IGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGVycik7XHJcbiAgICAgICAgICAgICAgICAgICAgYWxlcnQoXCLmnI3liqHlmajlh7rlsI/lt67kuobvvIzor7fnqI3lkI7lho3or5XvvIFcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICB9KTtcclxuXHJcbiAgICB9O1xyXG4gICAgZnVuY3Rpb24gY2hlY2tJbnB1dE5hbWUoKSB7XHJcbiAgICAgICAgdmFyICRzZWxmID0gJChcIiNleGVyY2lzZUJvb2tOYW1lXCIpLFxyXG4gICAgICAgICAgICBleGVyY2lzZU5hbWUgPSAkLnRyaW0oJHNlbGYudmFsKCkpO1xyXG5cclxuICAgICAgICBpZiAoZXhlcmNpc2VOYW1lLmxlbmd0aCA+IDE0KSB7XHJcbiAgICAgICAgICAgIC8vIGV4ZXJjaXNlTmFtZSA9IGV4ZXJjaXNlTmFtZS5zdWJzdHIoMCwgMTQpO1xyXG4gICAgICAgICAgICAvLyAkc2VsZi52YWwoZXhlcmNpc2VOYW1lKTtcclxuICAgICAgICAgICAgJChcIi5jaGFyYWN0ZXJOdW1MaW1pdFwiKS5jc3Moe1wiY29sb3JcIjogXCJyZWRcIn0pO1xyXG4gICAgICAgICAgICAkKFwiI2V4ZXJjaXNlQm9va05hbWVcIikuY3NzKHtcImJvcmRlci1jb2xvclwiOiBcInJlZFwifSk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgJChcIi5jaGFyYWN0ZXJOdW1MaW1pdFwiKS5jc3Moe1wiY29sb3JcIjogXCIjYjJiMmIyXCJ9KTtcclxuICAgICAgICAgICAgJChcIiNleGVyY2lzZUJvb2tOYW1lXCIpLmNzcyh7XCJib3JkZXItY29sb3JcIjogXCIjZGNkY2RjXCJ9KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgJChcIi5jaGFyYWN0ZXJOdW1MaW1pdD5zcGFuXCIpLnRleHQoZXhlcmNpc2VOYW1lLmxlbmd0aCk7XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuICRtYW5hZ2VFeGVyY2lzZTtcclxufSkoKTtcclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG4iXX0=