$(document).ready(function(){$(".home_page").click(function(){window.location.href="/accountview"});$(".avatar_nickname").click(function(){var a=$(this).data("app_id");var i=$(this).data("user_id");jumpDetail(a+"|"+i)})});function jumpDetail(a){var a=a;var i=a.split("|")[0];var t=a.split("|")[1];window.location.href="/customerdetail?appId="+i+"&userId="+t}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluL3Ntc0RldGFpbExpc3QuanMiXSwibmFtZXMiOlsiJCIsImRvY3VtZW50IiwicmVhZHkiLCJjbGljayIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsImFwcF9pZCIsInRoaXMiLCJkYXRhIiwidXNlcl9pZCIsImp1bXBEZXRhaWwiLCJpbmZvIiwiYXBwSWQiLCJzcGxpdCIsInVzZXJJZCJdLCJtYXBwaW5ncyI6IkFBS0FBLEVBQUVDLFVBQVVDLE1BQU0sV0FHZEYsRUFBRSxjQUFjRyxNQUFNLFdBQ2xCQyxPQUFPQyxTQUFTQyxLQUFLLGdCQUd6Qk4sR0FBRSxvQkFBb0JHLE1BQU0sV0FDeEIsR0FBSUksR0FBU1AsRUFBRVEsTUFBTUMsS0FBSyxTQUMxQixJQUFJQyxHQUFVVixFQUFFUSxNQUFNQyxLQUFLLFVBRTNCRSxZQUFXSixFQUFTLElBQU1HLE1BTWxDLFNBQVNDLFlBQVdDLEdBQ2hCLEdBQUlBLEdBQUtBLENBQ1QsSUFBSUMsR0FBTUQsRUFBS0UsTUFBTSxLQUFLLEVBQzFCLElBQUlDLEdBQU9ILEVBQUtFLE1BQU0sS0FBSyxFQUMzQlYsUUFBT0MsU0FBU0MsS0FBSyx5QkFBeUJPLEVBQU0sV0FBV0UiLCJmaWxlIjoiYWRtaW4vc21zRGV0YWlsTGlzdC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBDcmVhdGVkIGJ5IEFkbWluaXN0cmF0b3Igb24gMjAxNy8zLzcuXHJcbiAqL1xyXG5cclxuXHJcbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuXHJcbiAgICAvLyAg5Zue5Li76aG1XHJcbiAgICAkKFwiLmhvbWVfcGFnZVwiKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWY9Jy9hY2NvdW50dmlldyc7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKFwiLmF2YXRhcl9uaWNrbmFtZVwiKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGFwcF9pZCA9ICQodGhpcykuZGF0YShcImFwcF9pZFwiKTtcclxuICAgICAgICB2YXIgdXNlcl9pZCA9ICQodGhpcykuZGF0YShcInVzZXJfaWRcIik7XHJcblxyXG4gICAgICAgIGp1bXBEZXRhaWwoYXBwX2lkICsgXCJ8XCIgKyB1c2VyX2lkKTtcclxuICAgIH0pO1xyXG5cclxufSk7XHJcblxyXG4vL+iwg+WIsOivpuaDhemhtVxyXG5mdW5jdGlvbiBqdW1wRGV0YWlsKGluZm8pIHtcclxuICAgIHZhciBpbmZvPWluZm87XHJcbiAgICB2YXIgYXBwSWQ9aW5mby5zcGxpdChcInxcIilbMF07XHJcbiAgICB2YXIgdXNlcklkPWluZm8uc3BsaXQoXCJ8XCIpWzFdO1xyXG4gICAgd2luZG93LmxvY2F0aW9uLmhyZWY9Jy9jdXN0b21lcmRldGFpbD9hcHBJZD0nK2FwcElkKycmdXNlcklkPScrdXNlcklkO1xyXG59Il19