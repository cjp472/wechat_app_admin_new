$(".agree").children("span").click(function(){window.open("/charge_protocol_page")});$("#iAgree").click(function(){$(".seperate").css({display:"none"});$(".agreeModal").css({display:"none"});$("input[type='checkbox']").prop("checked",true)});$(".pop-up_close").click(function(){$(".scan_screen").fadeOut(300);$(".scan_screen_content").fadeOut(300);$(".scan_status_success").fadeOut(300);$(".scan_status_fail").fadeOut(300);queryresult()});$("#pay_by_wechat").mousedown(function(){$(this).css("background","#148b13")});$("#pay_by_wechat").mouseup(function(){$(this).css("background","rgb(26, 174, 24)")});$(".scan_status_return").click(function(){$(".scan_screen").fadeOut(300);$(".scan_status_fail").hide();current_time=0});$("#pay_by_wechat").click(function(){var c=100*100;var e=TYPE_GROW_UP_VERSION;pre_pay_wechat(c,e)});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2dyb3d1cFZlcnNpb25QYWdlLmpzIl0sIm5hbWVzIjpbIiQiLCJjaGlsZHJlbiIsImNsaWNrIiwid2luZG93Iiwib3BlbiIsImNzcyIsImRpc3BsYXkiLCJwcm9wIiwiZmFkZU91dCIsInF1ZXJ5cmVzdWx0IiwibW91c2Vkb3duIiwidGhpcyIsIm1vdXNldXAiLCJoaWRlIiwiY3VycmVudF90aW1lIiwicHJpY2UiLCJ0eXBlIiwiVFlQRV9HUk9XX1VQX1ZFUlNJT04iLCJwcmVfcGF5X3dlY2hhdCJdLCJtYXBwaW5ncyI6IkFBV0FBLEVBQUUsVUFBVUMsU0FBUyxRQUFRQyxNQUFNLFdBRS9CQyxPQUFPQyxLQUFLLDBCQUloQkosR0FBRSxXQUFXRSxNQUFNLFdBQ2ZGLEVBQUUsYUFBYUssS0FBTUMsUUFBVyxRQUNoQ04sR0FBRSxlQUFlSyxLQUFNQyxRQUFXLFFBQ2xDTixHQUFFLDBCQUEwQk8sS0FBSyxVQUFXLE9BR2hEUCxHQUFFLGlCQUFpQkUsTUFBTSxXQUNyQkYsRUFBRSxnQkFBZ0JRLFFBQVEsSUFDMUJSLEdBQUUsd0JBQXdCUSxRQUFRLElBQ2xDUixHQUFFLHdCQUF3QlEsUUFBUSxJQUNsQ1IsR0FBRSxxQkFBcUJRLFFBQVEsSUFDL0JDLGdCQUlKVCxHQUFFLGtCQUFrQlUsVUFBVSxXQUMxQlYsRUFBRVcsTUFBTU4sSUFBSSxhQUFhLFlBRTdCTCxHQUFFLGtCQUFrQlksUUFBUSxXQUN4QlosRUFBRVcsTUFBTU4sSUFBSSxhQUFhLHFCQUU3QkwsR0FBRSx1QkFBdUJFLE1BQU0sV0FDM0JGLEVBQUUsZ0JBQWdCUSxRQUFRLElBQzFCUixHQUFFLHFCQUFxQmEsTUFDdkJDLGNBQWUsR0FJbkJkLEdBQUUsa0JBQWtCRSxNQUFNLFdBQ3RCLEdBQUlhLEdBQVEsSUFBTSxHQUNsQixJQUFJQyxHQUFPQyxvQkFDWEMsZ0JBQWVILEVBQU9DIiwiZmlsZSI6ImFkbWluL2dyb3d1cFZlcnNpb25QYWdlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiXHJcblxyXG5cclxuLy8gJChcIi5jaGFyZ2VfYnRuXCIpLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbi8vICAgICAgICQoXCIuc2Nhbl9zY3JlZW5cIikuZmFkZUluKDMwMCk7XHJcbi8vIH0pO1xyXG4vLyAkKFwiLnNjYW5fc2NyZWVuXCIpLmNsaWNrKGZ1bmN0aW9uKCkge1xyXG4vLyAgICAgICAgICQoXCIuc2Nhbl9zY3JlZW5cIikuZmFkZU91dCgzMDApO1xyXG4vLyAgICAgICAgICQoXCIuc2Nhbl9zdGF0dXNfZmFpbFwiKS5oaWRlKCk7XHJcbi8vIH0pXHJcbiAgICAvL+aJk+W8gOWNj+iurlxyXG4kKFwiLmFncmVlXCIpLmNoaWxkcmVuKFwic3BhblwiKS5jbGljayhmdW5jdGlvbigpIHtcclxuICAgIC8vIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gXCIvY2hhcmdlX3Byb3RvY29sX3BhZ2VcIjtcclxuICAgIHdpbmRvdy5vcGVuKFwiL2NoYXJnZV9wcm90b2NvbF9wYWdlXCIpO1xyXG59KTtcclxuXHJcbi8v5YWz6Zet5Y2P6K6uXHJcbiQoXCIjaUFncmVlXCIpLmNsaWNrKGZ1bmN0aW9uKCkge1xyXG4gICAgJChcIi5zZXBlcmF0ZVwiKS5jc3MoeyAnZGlzcGxheSc6ICdub25lJyB9KTtcclxuICAgICQoXCIuYWdyZWVNb2RhbFwiKS5jc3MoeyAnZGlzcGxheSc6ICdub25lJyB9KTtcclxuICAgICQoXCJpbnB1dFt0eXBlPSdjaGVja2JveCddXCIpLnByb3AoXCJjaGVja2VkXCIsIHRydWUpO1xyXG59KTtcclxuLy8g5YWz6Zet5by55Ye65qGGXHJcbiQoXCIucG9wLXVwX2Nsb3NlXCIpLmNsaWNrKGZ1bmN0aW9uKCkge1xyXG4gICAgJChcIi5zY2FuX3NjcmVlblwiKS5mYWRlT3V0KDMwMCk7XHJcbiAgICAkKFwiLnNjYW5fc2NyZWVuX2NvbnRlbnRcIikuZmFkZU91dCgzMDApO1xyXG4gICAgJChcIi5zY2FuX3N0YXR1c19zdWNjZXNzXCIpLmZhZGVPdXQoMzAwKTtcclxuICAgICQoXCIuc2Nhbl9zdGF0dXNfZmFpbFwiKS5mYWRlT3V0KDMwMCk7XHJcbiAgICBxdWVyeXJlc3VsdCgpO1xyXG59KVxyXG5cclxuLy/mlK/ku5jmjInpkq7ngrnlh7vmgIFcclxuJChcIiNwYXlfYnlfd2VjaGF0XCIpLm1vdXNlZG93bihmdW5jdGlvbigpe1xyXG4gICAgJCh0aGlzKS5jc3MoXCJiYWNrZ3JvdW5kXCIsXCIjMTQ4YjEzXCIpO1xyXG59KTtcclxuJChcIiNwYXlfYnlfd2VjaGF0XCIpLm1vdXNldXAoZnVuY3Rpb24oKXtcclxuICAgICQodGhpcykuY3NzKFwiYmFja2dyb3VuZFwiLFwicmdiKDI2LCAxNzQsIDI0KVwiKTtcclxufSlcclxuJChcIi5zY2FuX3N0YXR1c19yZXR1cm5cIikuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgJChcIi5zY2FuX3NjcmVlblwiKS5mYWRlT3V0KDMwMCk7XHJcbiAgICAkKFwiLnNjYW5fc3RhdHVzX2ZhaWxcIikuaGlkZSgpO1xyXG4gICAgY3VycmVudF90aW1lID0gMDtcclxufSlcclxuXHJcblxyXG4kKFwiI3BheV9ieV93ZWNoYXRcIikuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyIHByaWNlID0gMTAwICogMTAwOyAgICAgICAgICAvLyAg5Y2V5L2NOuWIhlxyXG4gICAgdmFyIHR5cGUgPSBUWVBFX0dST1dfVVBfVkVSU0lPTjtcclxuICAgIHByZV9wYXlfd2VjaGF0KHByaWNlLCB0eXBlKTtcclxuXHJcbn0pO1xyXG5cclxuIl19
