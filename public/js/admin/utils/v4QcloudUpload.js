var appid="1252524126";var bucket="wechatapppro";function InitCosCloud(){var n=new CosCloud({appid:appid,bucket:bucket,region:"sh",getAppSign:function(n){$.ajax("/getUploadSign?sign_type=appSign").done(function(e){var o=JSON.parse(e).data.sign;n(encodeURIComponent(o))})},getAppSignOnce:function(n){$.ajax("/getUploadSign?sign_type=appSign_once&path=").done(function(e){var o=JSON.parse(e).data.sign;n(encodeURIComponent(o))})}});this.uploadFile=function(e,o,p,a,i,t){n.uploadFile(e,o,p,bucket,a,i,t)};this.uploadFileWithoutPro=function(e,o,p,a,i){n.uploadFile(e,o,null,bucket,p,a,i)}}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInY0UWNsb3VkVXBsb2FkLmpzIl0sIm5hbWVzIjpbImFwcGlkIiwiYnVja2V0IiwiSW5pdENvc0Nsb3VkIiwiY29zIiwiQ29zQ2xvdWQiLCJyZWdpb24iLCJnZXRBcHBTaWduIiwiY2FsbGJhY2siLCIkIiwiYWpheCIsImRvbmUiLCJkYXRhIiwic2lnIiwiSlNPTiIsInBhcnNlIiwic2lnbiIsImVuY29kZVVSSUNvbXBvbmVudCIsImdldEFwcFNpZ25PbmNlIiwidGhpcyIsInVwbG9hZEZpbGUiLCJzdWNjZXNzQ2FsbEJhY2siLCJlcnJvckNhbGxCYWNrIiwicHJvZ3Jlc3NDYWxsQmFjayIsInJlbW90ZVBhdGgiLCJmaWxlIiwiaW5zZXJ0T25seSIsInVwbG9hZEZpbGVXaXRob3V0UHJvIl0sIm1hcHBpbmdzIjoiQUFHQSxHQUFJQSxPQUFRLFlBQ1osSUFBSUMsUUFBUyxjQUliLFNBQVNDLGdCQUNMLEdBQUlDLEdBQU0sR0FBSUMsV0FDVkosTUFBT0EsTUFDUEMsT0FBUUEsT0FDUkksT0FBUSxLQUNSQyxXQUFZLFNBQVVDLEdBR2xCQyxFQUFFQyxLQUFLLG9DQUFvQ0MsS0FBSyxTQUFVQyxHQUN0RCxHQUFJQyxHQUFNQyxLQUFLQyxNQUFNSCxHQUFNQSxLQUFLSSxJQUNoQ1IsR0FBU1MsbUJBQW1CSixPQUdwQ0ssZUFBZ0IsU0FBVVYsR0FFdEJDLEVBQUVDLEtBQUssK0NBQStDQyxLQUFLLFNBQVVDLEdBQ2pFLEdBQUlDLEdBQU1DLEtBQUtDLE1BQU1ILEdBQU1BLEtBQUtJLElBQ2hDUixHQUFTUyxtQkFBbUJKLFFBY3hDTSxNQUFLQyxXQUFhLFNBQVVDLEVBQWlCQyxFQUFlQyxFQUFrQkMsRUFBWUMsRUFBTUMsR0FDNUZ0QixFQUFJZ0IsV0FBV0MsRUFBaUJDLEVBQWVDLEVBQWtCckIsT0FBUXNCLEVBQVlDLEVBQU1DLEdBSS9GUCxNQUFLUSxxQkFBdUIsU0FBVU4sRUFBaUJDLEVBQWVFLEVBQVlDLEVBQU1DLEdBQ3BGdEIsRUFBSWdCLFdBQVdDLEVBQWlCQyxFQUFlLEtBQU1wQixPQUFRc0IsRUFBWUMsRUFBTUMiLCJmaWxlIjoidjRRY2xvdWRVcGxvYWQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIENyZWF0ZWQgYnkgYnJlZXplIG9uIDE1LzAzLzIwMTcuXG4gKi9cbnZhciBhcHBpZCA9ICcxMjUyNTI0MTI2JztcbnZhciBidWNrZXQgPSAnd2VjaGF0YXBwcHJvJztcblxuLy/liJ3lp4vljJbpgLvovpFcbi8v54m55Yir5rOo5oSPOiBKUy1TREvkvb/nlKjkuYvliY3or7flhYjliLBjb25zb2xlLnFjbG91ZC5jb20vY29zIOWvueebuOW6lOeahEJ1Y2tldOi/m+ihjOi3qOWfn+iuvue9rlxuZnVuY3Rpb24gSW5pdENvc0Nsb3VkKCkge1xuICAgIHZhciBjb3MgPSBuZXcgQ29zQ2xvdWQoe1xuICAgICAgICBhcHBpZDogYXBwaWQsLy8gQVBQSUQg5b+F5aGr5Y+C5pWwXG4gICAgICAgIGJ1Y2tldDogYnVja2V0LC8vYnVja2V0TmFtZSDlv4Xloavlj4LmlbBcbiAgICAgICAgcmVnaW9uOiAnc2gnLC8v5Zyw5Z+f5L+h5oGvIOW/heWhq+WPguaVsCDljY7ljZflnLDljLrloatneiDljY7kuJzloatzaCDljY7ljJfloat0alxuICAgICAgICBnZXRBcHBTaWduOiBmdW5jdGlvbiAoY2FsbGJhY2spIHsgICAvL+iOt+WPluetvuWQjSDlv4Xloavlj4LmlbBcbiAgICAgICAgICAgIC8vMS7mkK3lu7rkuIDkuKrpibTmnYPmnI3liqHlmajvvIzoh6rlt7HmnoTpgKDor7fmsYLlj4LmlbDojrflj5bnrb7lkI3vvIzmjqjojZDlrp7pmYXnur/kuIrkuJrliqHkvb/nlKjvvIzkvJjngrnmmK/lronlhajmgKflpb3vvIzkuI3kvJrmmrTpnLLoh6rlt7HnmoTnp4HpkqVcbiAgICAgICAgICAgIC8v5ou/5Yiw562+5ZCN5LmL5ZCO6K6w5b6X6LCD55SoY2FsbGJhY2tcbiAgICAgICAgICAgICQuYWpheCgnL2dldFVwbG9hZFNpZ24/c2lnbl90eXBlPWFwcFNpZ24nKS5kb25lKGZ1bmN0aW9uIChkYXRhKSB7XG4gICAgICAgICAgICAgICAgdmFyIHNpZyA9IEpTT04ucGFyc2UoZGF0YSkuZGF0YS5zaWduO1xuICAgICAgICAgICAgICAgIGNhbGxiYWNrKGVuY29kZVVSSUNvbXBvbmVudChzaWcpKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9LFxuICAgICAgICBnZXRBcHBTaWduT25jZTogZnVuY3Rpb24gKGNhbGxiYWNrKSB7Ly/ljZXmrKHnrb7lkI3vvIzlv4Xloavlj4LmlbDvvIzlj4LogIPkuIrpnaLnmoTms6jph4rljbPlj69cbiAgICAgICAgICAgIC8vICAgIC8v5aGr5LiK6I635Y+W5Y2V5qyh562+5ZCN55qE6YC76L6RXG4gICAgICAgICAgICAkLmFqYXgoJy9nZXRVcGxvYWRTaWduP3NpZ25fdHlwZT1hcHBTaWduX29uY2UmcGF0aD0nKS5kb25lKGZ1bmN0aW9uIChkYXRhKSB7XG4gICAgICAgICAgICAgICAgdmFyIHNpZyA9IEpTT04ucGFyc2UoZGF0YSkuZGF0YS5zaWduO1xuICAgICAgICAgICAgICAgIGNhbGxiYWNrKGVuY29kZVVSSUNvbXBvbmVudChzaWcpKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfSk7XG5cbiAgICAvKipcbiAgICAgKiDkuIrkvKDmlofku7Yg5YiG54mH5LiK5Lyg55u05o6l6LCD55SodXBsb2FkRmlsZeaWueazle+8jOWGhemDqOS8muWIpOaWreaYr+WQpumcgOimgeWIhueJh1xuICAgICAqIEBwYXJhbSBzdWNjZXNzQ2FsbEJhY2sg5oiQ5Yqf5Zue6LCDXG4gICAgICogQHBhcmFtIGVycm9yQ2FsbEJhY2sg5aSx6LSl5Zue6LCDXG4gICAgICogQHBhcmFtIHByb2dyZXNzQ2FsbEJhY2sgIOi/m+W6puWbnuiwg1xuICAgICAqIEBwYXJhbSByZW1vdGVQYXRoICAgIOebuOWvuei3r+W+hFxuICAgICAqIEBwYXJhbSBmaWxlICAgICAg5paH5Lu2XG4gICAgICogQHBhcmFtIGluc2VydE9ubHkgaW5zZXJ0T25seT09MCDooajnpLrlhYHorrjopobnm5bmlofku7YgMeihqOekuuS4jeWFgeiuuFxuICAgICAqL1xuICAgIHRoaXMudXBsb2FkRmlsZSA9IGZ1bmN0aW9uIChzdWNjZXNzQ2FsbEJhY2ssIGVycm9yQ2FsbEJhY2ssIHByb2dyZXNzQ2FsbEJhY2ssIHJlbW90ZVBhdGgsIGZpbGUsIGluc2VydE9ubHkpIHtcbiAgICAgICAgY29zLnVwbG9hZEZpbGUoc3VjY2Vzc0NhbGxCYWNrLCBlcnJvckNhbGxCYWNrLCBwcm9ncmVzc0NhbGxCYWNrLCBidWNrZXQsIHJlbW90ZVBhdGgsIGZpbGUsIGluc2VydE9ubHkpO1xuICAgIH07XG5cbiAgICAvL+ayoeaciei/m+W6puWbnuiwg+eahOS4iuS8oFxuICAgIHRoaXMudXBsb2FkRmlsZVdpdGhvdXRQcm8gPSBmdW5jdGlvbiAoc3VjY2Vzc0NhbGxCYWNrLCBlcnJvckNhbGxCYWNrLCByZW1vdGVQYXRoLCBmaWxlLCBpbnNlcnRPbmx5KSB7XG4gICAgICAgIGNvcy51cGxvYWRGaWxlKHN1Y2Nlc3NDYWxsQmFjaywgZXJyb3JDYWxsQmFjaywgbnVsbCwgYnVja2V0LCByZW1vdGVQYXRoLCBmaWxlLCBpbnNlcnRPbmx5KTtcbiAgICB9O1xuXG5cbn1cbiJdfQ==