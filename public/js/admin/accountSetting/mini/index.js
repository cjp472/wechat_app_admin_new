$(function(){var i=$("#app_id").val();$("#open_pay_label").click(function(){e(1)});$("#close_pay_label").click(function(){e(0)});function e(i){$.post("/mini/changePayShow",{pay_switch:i},function(e){e=JSON.parse(e);if(e.code==0){if(i==1){baseUtils.show.blueTip("已开启付费内容的显示")}else{baseUtils.show.blueTip("已关闭付费内容的显示")}}else{baseUtils.show.redTip("操作失败")}})}});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL2FjY291bnRTZXR0aW5nL21pbmkvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsImFwcF9pZCIsInZhbCIsImNsaWNrIiwic2V0UGF5U2hvdyIsInBheV9zd2l0Y2giLCJwb3N0IiwianNvbiIsIkpTT04iLCJwYXJzZSIsImNvZGUiLCJiYXNlVXRpbHMiLCJzaG93IiwiYmx1ZVRpcCIsInJlZFRpcCJdLCJtYXBwaW5ncyI6IkFBQ0FBLEVBQUUsV0FDRSxHQUFJQyxHQUFTRCxFQUFFLFdBQVdFLEtBbUIxQkYsR0FBRSxtQkFBbUJHLE1BQU0sV0FDdkJDLEVBQVcsSUFHZkosR0FBRSxvQkFBb0JHLE1BQU0sV0FDeEJDLEVBQVcsSUFJZixTQUFTQSxHQUFXQyxHQUVoQkwsRUFBRU0sS0FBSyx1QkFBd0JELFdBQVlBLEdBQWEsU0FBVUUsR0FDOURBLEVBQU9DLEtBQUtDLE1BQU1GLEVBRWxCLElBQUlBLEVBQUtHLE1BQVEsRUFBRyxDQUNoQixHQUFHTCxHQUFZLEVBQUUsQ0FDYk0sVUFBVUMsS0FBS0MsUUFBUSxrQkFDdEIsQ0FDREYsVUFBVUMsS0FBS0MsUUFBUSxtQkFFeEIsQ0FDSEYsVUFBVUMsS0FBS0UsT0FBTyIsImZpbGUiOiJhZG1pbi9hY2NvdW50U2V0dGluZy9taW5pL2luZGV4LmpzIiwic291cmNlc0NvbnRlbnQiOlsiXHJcbiQoZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyIGFwcF9pZCA9ICQoJyNhcHBfaWQnKS52YWwoKTtcclxuICAgIC8qdmFyIHFyY29kZSA9IG5ldyBRUkNvZGUoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJtaW5pYXBwQ29kZVwiKSwge1xyXG4gICAgIHRleHQ6IG1pbmlhcHBVcmwgKyBhcHBfaWQsXHJcbiAgICAgd2lkdGg6IDE1MCxcclxuICAgICBoZWlnaHQ6IDE1MCxcclxuICAgICBjb2xvckRhcmsgOiBcIiMwMDAwMDBcIixcclxuICAgICBjb2xvckxpZ2h0IDogXCIjZmZmZmZmXCIsXHJcbiAgICAgY29ycmVjdExldmVsIDogUVJDb2RlLkNvcnJlY3RMZXZlbC5NXHJcbiAgICAgfSk7Ki9cclxuXHJcblxyXG4gICAgLy8gJCgnaW5wdXQ6cmFkaW9bbmFtZT1cInBheV9zd2l0Y2hcIl0nKS5jaGFuZ2UoIGZ1bmN0aW9uKCl7XHJcbiAgICAvLyBcdGlmICgkKFwiI29wZW5fcGF5X2xhYmVsXCIpLmlzKCc6Y2hlY2tlZCcpKSB7XHJcbiAgICAvLyBcdFx0YWxlcnQoXCLlvIDkuoZcIik7XHJcbiAgICAvLyBcdH1lbHNle1xyXG4gICAgLy8gXHRcdGFsZXJ0KFwi5YWz5LqGXCIpO1xyXG4gICAgLy8gXHR9XHJcbiAgICAvLyB9KTtcclxuXHJcbiAgICAkKFwiI29wZW5fcGF5X2xhYmVsXCIpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBzZXRQYXlTaG93KDEpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJChcIiNjbG9zZV9wYXlfbGFiZWxcIikuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHNldFBheVNob3coMCk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+iuvue9ruS7mOi0ueS6p+WTgeWMheeahOaYvuekuuWSjOmakOiXj1xyXG4gICAgZnVuY3Rpb24gc2V0UGF5U2hvdyhwYXlfc3dpdGNoKSB7XHJcblxyXG4gICAgICAgICQucG9zdCgnL21pbmkvY2hhbmdlUGF5U2hvdycsIHtwYXlfc3dpdGNoOiBwYXlfc3dpdGNofSwgZnVuY3Rpb24gKGpzb24pIHtcclxuICAgICAgICAgICAganNvbiA9IEpTT04ucGFyc2UoanNvbik7XHJcblxyXG4gICAgICAgICAgICBpZiAoanNvbi5jb2RlID09IDApIHtcclxuICAgICAgICAgICAgICAgIGlmKHBheV9zd2l0Y2g9PTEpe1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LmJsdWVUaXAoXCLlt7LlvIDlkK/ku5jotLnlhoXlrrnnmoTmmL7npLpcIik7XHJcbiAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5ibHVlVGlwKFwi5bey5YWz6Zet5LuY6LS55YaF5a6555qE5pi+56S6XCIpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cucmVkVGlwKFwi5pON5L2c5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG59KTsiXX0=
