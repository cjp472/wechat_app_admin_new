$(function(){var e=new Business;e.init();if(GetQueryString("prompt")==1){$(".red_prompt_word").html("小鹅通内容列表全面升级为知识商品，点击查看更多高级功能<a href='/help/system_update' target='_blank'>查看教程</a>");$(".red_prompt").show()}});function Business(){this.toolBoxBtn=true;this.toDetail=true;this.searchObj={state:GetQueryString("state")||0,search_content:GetQueryString("search_content")||""}}Business.prototype={init:function(){if(sessionStorage.getItem("key")==1){setTimeout(function(){baseUtils.show.blueTip("操作成功")},300);sessionStorage.setItem("key",0)}var e=this;$("#packageList").on("click",".listItem",function(){if(e.toDetail){e.toolBoxBtn=false,e.toDetail=false;var t="/package_detail_page?id="+$(this).data("id");contentDetail(t)}});$("#packageList").on("click",".listItem .toolBox li",function(t){t.stopPropagation();var o=$(t.target),a=o.parentsUntil("#packageList",".listItem").data("id");type=o.data("type");type&&e.toolBoxBtn&&e.toDetail&&(e.tool(type,a),e.toDetail=false)});$("#searchBtn").on("click",function(){var e=$("#selector").val(),t=$.trim($("#searchVal").val());if(!!t){window.location.href="/package_list_page?state="+e+"&search_content="+t}else{window.location.href="/package_list_page?state="+e}});$("#selector").find("option").eq(++this.searchObj.state).attr("selected",true);$("#searchVal").on("keypress",function(e){if(e.keyCode=="13"){$("#searchBtn").click()}}).val(this.searchObj.search_content);$(".selector").on("change",function(){$("#searchBtn").click()})},tool:function(e,t){switch(e){case"edit":contentDetail("/package_detail_page?id="+t);break;case"toup":this.movePackage(t,true);break;case"todown":this.movePackage(t,false);break;case"soldout":this.isShow(t,false);break;case"putaway":this.isShow(t,true);break;default:console.error("参数错误");break}},movePackage:function(e,t){var o=this;showLoading();$.ajax("/change_package_weight",{type:"POST",dataType:"json",data:{package_id:e,order_type:t?0:1},success:function(e){hideLoading();if(e.code==0){sessionStorage.setItem("key",1);console.log(sessionStorage.getItem("key"));reloadPage()}else{o.toolBoxBtn=true;o.toDetail=true;baseUtils.show.redTip(e.msg)}},error:function(e,t,a){console.error(a);hideLoading();o.toolBoxBtn=true;o.toDetail=true;baseUtils.show.redTip("操作失败，请稍后再试")}})},isShow:function(e,t){var o=this;showLoading();$.ajax("/change_goods_state",{type:"POST",dataType:"json",data:{goods_id:e,goods_type:0,operate_type:t?0:1},success:function(e){hideLoading();if(e.code==0){baseUtils.show.blueTip(e.msg);setTimeout(function(){reloadPage()},700)}else{o.toolBoxBtn=true;o.toDetail=true;baseUtils.show.redTip(e.msg)}},error:function(e,t,a){console.error(a);hideLoading();o.toolBoxBtn=true;o.toDetail=true;baseUtils.show.redTip("操作失败，请稍后再试")}})}};
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInBhY2thZ2VMaXN0LmpzIl0sIm5hbWVzIjpbIiQiLCJidXNpbmVzcyIsIkJ1c2luZXNzIiwiaW5pdCIsIkdldFF1ZXJ5U3RyaW5nIiwiaHRtbCIsInNob3ciLCJ0aGlzIiwidG9vbEJveEJ0biIsInRvRGV0YWlsIiwic2VhcmNoT2JqIiwic3RhdGUiLCJzZWFyY2hfY29udGVudCIsInByb3RvdHlwZSIsInNlc3Npb25TdG9yYWdlIiwiZ2V0SXRlbSIsInNldFRpbWVvdXQiLCJiYXNlVXRpbHMiLCJibHVlVGlwIiwic2V0SXRlbSIsInNlbGYiLCJvbiIsImhyZWYiLCJkYXRhIiwiY29udGVudERldGFpbCIsImUiLCJzdG9wUHJvcGFnYXRpb24iLCIkZWxlIiwidGFyZ2V0IiwiaWQiLCJwYXJlbnRzVW50aWwiLCJ0eXBlIiwidG9vbCIsInZhbCIsInRyaW0iLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImZpbmQiLCJlcSIsImF0dHIiLCJrZXlDb2RlIiwiY2xpY2siLCJtb3ZlUGFja2FnZSIsImlzU2hvdyIsImNvbnNvbGUiLCJlcnJvciIsImNoYW5nZVR5cGUiLCJzaG93TG9hZGluZyIsImFqYXgiLCJkYXRhVHlwZSIsInBhY2thZ2VfaWQiLCJvcmRlcl90eXBlIiwic3VjY2VzcyIsImhpZGVMb2FkaW5nIiwiY29kZSIsImxvZyIsInJlbG9hZFBhZ2UiLCJyZWRUaXAiLCJtc2ciLCJ4aHIiLCJzdGF0dXMiLCJlcnIiLCJnb29kc19pZCIsImdvb2RzX3R5cGUiLCJvcGVyYXRlX3R5cGUiXSwibWFwcGluZ3MiOiJBQUtBQSxFQUFFLFdBQ0UsR0FBSUMsR0FBVyxHQUFJQyxTQUNuQkQsR0FBU0UsTUFHVCxJQUFJQyxlQUFlLFdBQWEsRUFBRyxDQUMvQkosRUFBRSxvQkFBb0JLLEtBQUssb0ZBQzNCTCxHQUFFLGVBQWVNLFNBS3pCLFNBQVNKLFlBQ0xLLEtBQUtDLFdBQWEsSUFDbEJELE1BQUtFLFNBQVcsSUFDaEJGLE1BQUtHLFdBQ0RDLE1BQU9QLGVBQWUsVUFBWSxF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V0FBWSxFQUNaQyxhQUFjbEIsRUFBYSxFQUFJLEdBRW5DTSxRQUFTLFNBQVM3QixHQUVkOEIsYUFDQSxJQUFHOUIsRUFBSytCLE1BQVEsRUFBRyxDQUNmckMsVUFBVVgsS0FBS1ksUUFBUUssRUFBS21DLElBQzVCMUMsWUFBVyxXQUNQd0MsY0FDRixTQUNDLENBQ0hwQyxFQUFLWixXQUFhLElBQ2xCWSxHQUFLWCxTQUFXLElBQ2hCUSxXQUFVWCxLQUFLbUQsT0FBT2xDLEVBQUttQyxPQUduQ2IsTUFBTyxTQUFTYyxFQUFLQyxFQUFRQyxHQUN6QmpCLFFBQVFDLE1BQU1nQixFQUNkUixjQUNBakMsR0FBS1osV0FBYSxJQUNsQlksR0FBS1gsU0FBVyxJQUNoQlEsV0FBVVgsS0FBS21ELE9BQU8iLCJmaWxlIjoicGFja2FnZUxpc3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogQ3JlYXRlZCBieSBGcmFuayBvbiAyMDE3LzMvMjEuXHJcbiAqL1xyXG5cclxuXHJcbiQoZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyIGJ1c2luZXNzID0gbmV3IEJ1c2luZXNzKCk7XHJcbiAgICBidXNpbmVzcy5pbml0KCk7XHJcblxyXG4gICAgLy8gIOaYvuekuuWwj+m7hOadoSAgIDzlsI/puYXpgJrlhoXlrrnliJfooajlhajpnaLljYfnuqfkuLrnn6Xor4bllYblk4HvvIzojrflj5bmm7TlpJrpq5jnuqflip/og73kvb/nlKjmlZnnqIvor7fngrnlh7vjgJDnn6Xor4bllYblk4HmlZnnqIvjgJE+XHJcbiAgICBpZiAoR2V0UXVlcnlTdHJpbmcoXCJwcm9tcHRcIikgPT0gMSkge1xyXG4gICAgICAgICQoXCIucmVkX3Byb21wdF93b3JkXCIpLmh0bWwoXCLlsI/puYXpgJrlhoXlrrnliJfooajlhajpnaLljYfnuqfkuLrnn6Xor4bllYblk4HvvIzngrnlh7vmn6XnnIvmm7TlpJrpq5jnuqflip/og708YSBocmVmPScvaGVscC9zeXN0ZW1fdXBkYXRlJyB0YXJnZXQ9J19ibGFuayc+5p+l55yL5pWZ56iLPC9hPlwiKTtcclxuICAgICAgICAkKFwiLnJlZF9wcm9tcHRcIikuc2hvdygpO1xyXG4gICAgfVxyXG5cclxufSk7XHJcblxyXG5mdW5jdGlvbiBCdXNpbmVzcygpIHsvL+WumuS5ieS4gOS6m+WFrOWFseWxnuaAp1xyXG4gICAgdGhpcy50b29sQm94QnRuID0gdHJ1ZTtcclxuICAgIHRoaXMudG9EZXRhaWwgPSB0cnVlO1xyXG4gICAgdGhpcy5zZWFyY2hPYmogPSB7XHJcbiAgICAgICAgc3RhdGU6IEdldFF1ZXJ5U3RyaW5nKCdzdGF0ZScpIHx8IDAsXHJcbiAgICAgICAgc2VhcmNoX2NvbnRlbnQ6IEdldFF1ZXJ5U3RyaW5nKCdzZWFyY2hfY29udGVudCcpIHx8ICcnLFxyXG4gICAgICAgIC8vIGlzX2Rpc3RyaWJ1dGU6R2V0UXVlcnlTdHJpbmcoJ2lzX2Rpc3RyaWJ1dGUnKXx8IC0xXHJcbiAgICB9XHJcbn1cclxuXHJcbkJ1c2luZXNzLnByb3RvdHlwZSA9IHtcclxuICAgIGluaXQ6IGZ1bmN0aW9uICgpIHsgLy/pobXpnaLnmoTliJ3lp4vljJbmk43kvZzvvIznu5Hlrprkuovku7ZcclxuICAgICAgICBpZihzZXNzaW9uU3RvcmFnZS5nZXRJdGVtKCdrZXknKT09MSl7Ly/kuIrkuIvnp7vliqjml7bpl7TmiJDlip/liLfmlrDlkI7lvLnnqpfliJ3lp4vljJZcclxuICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcCgn5pON5L2c5oiQ5YqfJylcclxuICAgICAgICAgICAgfSwzMDApO1xyXG4gICAgICAgICAgICBzZXNzaW9uU3RvcmFnZS5zZXRJdGVtKCdrZXknLDApO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgICQoJyNwYWNrYWdlTGlzdCcpIC8v6Lez6L2s6K+m5oOF6aG1XHJcbiAgICAgICAgLm9uKCdjbGljaycsICcubGlzdEl0ZW0nLCBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgaWYoc2VsZi50b0RldGFpbCl7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnRvb2xCb3hCdG4gPSBmYWxzZSxcclxuICAgICAgICAgICAgICAgIHNlbGYudG9EZXRhaWwgPSBmYWxzZTtcclxuICAgICAgICAgICAgICAgIHZhciBocmVmID0gJy9wYWNrYWdlX2RldGFpbF9wYWdlP2lkPScgKyAkKHRoaXMpLmRhdGEoJ2lkJyk7XHJcbiAgICAgICAgICAgICAgICBjb250ZW50RGV0YWlsKGhyZWYpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICQoJyNwYWNrYWdlTGlzdCcpICAvL+iwg+eUqOW3peWFt+WHveaVsFxyXG4gICAgICAgIC5vbignY2xpY2snLCAnLmxpc3RJdGVtIC50b29sQm94IGxpJyAsZnVuY3Rpb24oZSkge1xyXG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gICAgICAgICAgICB2YXIgJGVsZSA9ICQoZS50YXJnZXQpLFxyXG4gICAgICAgICAgICAgICAgaWQgPSAkZWxlLnBhcmVudHNVbnRpbCgnI3BhY2thZ2VMaXN0JywnLmxpc3RJdGVtJykuZGF0YSgnaWQnKVxyXG4gICAgICAgICAgICAgICAgdHlwZSA9ICRlbGUuZGF0YSgndHlwZScpO1xyXG4gICAgICAgICAgICB0eXBlICYmIHNlbGYudG9vbEJveEJ0biAmJiBzZWxmLnRvRGV0YWlsICYmIChcclxuICAgICAgICAgICAgICAgIHNlbGYudG9vbCh0eXBlLCBpZCksXHJcbiAgICAgICAgICAgICAgICBzZWxmLnRvRGV0YWlsID0gZmFsc2VcclxuICAgICAgICAgICAgKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgJCgnI3NlYXJjaEJ0bicpXHJcbiAgICAgICAgLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkgeyAvL+aQnOe0olxyXG4gICAgICAgICAgICAvLyB2YXIgaXNfZGlzdHJpYnV0ZSA9ICQoJyNzZWxlY3Rvcl9kaXN0cmlidXRlJykudmFsKCksXHJcblxyXG4gICAgICAgICAgICB2YXIgc3RhdGUgPSAkKCcjc2VsZWN0b3InKS52YWwoKSxcclxuICAgICAgICAgICAgICAgIHNlYXJjaF9jb250ZW50ID0gJC50cmltKCAkKCcjc2VhcmNoVmFsJykudmFsKCkgKTtcclxuICAgICAgICAgICAgaWYoISFzZWFyY2hfY29udGVudCl7XHJcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9ICcvcGFja2FnZV9saXN0X3BhZ2U/c3RhdGU9JytzdGF0ZSsnJnNlYXJjaF9jb250ZW50PScrc2VhcmNoX2NvbnRlbnQ7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9ICcvcGFja2FnZV9saXN0X3BhZ2U/c3RhdGU9JytzdGF0ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+WIneWni+WMluaQnOe0ouadoeS7tlxyXG4gICAgICAgICQoJyNzZWxlY3RvcicpLmZpbmQoJ29wdGlvbicpLmVxKCsrdGhpcy5zZWFyY2hPYmouc3RhdGUpLmF0dHIoJ3NlbGVjdGVkJyx0cnVlKTtcclxuICAgICAgICAvLyAkKCcjc2VsZWN0b3JfZGlzdHJpYnV0ZScpLmZpbmQoJ29wdGlvbicpLmVxKCsrdGhpcy5zZWFyY2hPYmouaXNfZGlzdHJpYnV0ZSkuYXR0cignc2VsZWN0ZWQnLHRydWUpO1xyXG4gICAgICAgICQoJyNzZWFyY2hWYWwnKS5vbigna2V5cHJlc3MnLCBmdW5jdGlvbihlKSB7XHJcbiAgICAgICAgICAgIGlmKGUua2V5Q29kZSA9PSBcIjEzXCIpIHtcclxuICAgICAgICAgICAgICAgICQoJyNzZWFyY2hCdG4nKS5jbGljaygpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkudmFsKHRoaXMuc2VhcmNoT2JqLnNlYXJjaF9jb250ZW50KTtcclxuXHJcbiAgICAgICAgLy8gIOebkeWQrOetm+mAieahhumAieaLqeS6i+S7tlxyXG4gICAgICAgICQoXCIuc2VsZWN0b3JcIikub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgJCgnI3NlYXJjaEJ0bicpLmNsaWNrKCk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgfSxcclxuICAgIHRvb2w6IGZ1bmN0aW9uKHR5cGUsaWQpIHtcclxuICAgICAgICBzd2l0Y2godHlwZSkge1xyXG4gICAgICAgICAgICBjYXNlICdlZGl0JzpcclxuICAgICAgICAgICAgICAgIGNvbnRlbnREZXRhaWwoJy9wYWNrYWdlX2RldGFpbF9wYWdlP2lkPScgKyBpZCk7XHJcblxyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ3RvdXAnOlxyXG4gICAgICAgICAgICAgICAgdGhpcy5tb3ZlUGFja2FnZShpZCwgdHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAndG9kb3duJzpcclxuICAgICAgICAgICAgICAgIHRoaXMubW92ZVBhY2thZ2UoaWQsIGZhbHNlKTtcclxuICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICBjYXNlICdzb2xkb3V0JzpcclxuICAgICAgICAgICAgICAgIHRoaXMuaXNTaG93KGlkLCBmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAncHV0YXdheSc6XHJcbiAgICAgICAgICAgICAgICB0aGlzLmlzU2hvdyhpZCwgdHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgZGVmYXVsdDpcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ+WPguaVsOmUmeivrycpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgfVxyXG4gICAgfSxcclxuICAgIG1vdmVQYWNrYWdlOiBmdW5jdGlvbihpZCwgY2hhbmdlVHlwZSkge1xyXG4gICAgICAgIC8qXHJcbiAgICAgICAgICAgIGlkOiDopoHnp7vliqjnmoTkuJPmoI9pZFxyXG4gICAgICAgICAgICBjaGFuZ2VUeXBl77yaIHRydWUt5LiK56e777yMIGZhbHNlLeS4i+enu1xyXG4gICAgICAgICovXHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIHNob3dMb2FkaW5nKCk7XHJcbiAgICAgICAgJC5hamF4KCcvY2hhbmdlX3BhY2thZ2Vfd2VpZ2h0Jywge1xyXG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXHJcbiAgICAgICAgICAgIGRhdGE6IHtcclxuICAgICAgICAgICAgICAgIHBhY2thZ2VfaWQ6IGlkLFxyXG4gICAgICAgICAgICAgICAgLy8w5LiK56e777yMIDHkuIvnp7tcclxuICAgICAgICAgICAgICAgIG9yZGVyX3R5cGU6IGNoYW5nZVR5cGUgPyAwIDogMVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAvL2NvbnNvbGUubG9nKGRhdGEpO1xyXG4gICAgICAgICAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICAgICAgICAgIGlmKGRhdGEuY29kZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2Vzc2lvblN0b3JhZ2Uuc2V0SXRlbSgna2V5JywxKTtcclxuICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhzZXNzaW9uU3RvcmFnZS5nZXRJdGVtKCdrZXknKSk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmVsb2FkUGFnZSgpO1xyXG5cclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi50b29sQm94QnRuID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnRvRGV0YWlsID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoZGF0YS5tc2cpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24oeGhyLCBzdGF0dXMsIGVycikge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnIpO1xyXG4gICAgICAgICAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICAgICAgICAgIHNlbGYudG9vbEJveEJ0biA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnRvRGV0YWlsID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5pON5L2c5aSx6LSl77yM6K+356iN5ZCO5YaN6K+VJyk7XHJcblxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9LFxyXG4gICAgaXNTaG93OiBmdW5jdGlvbihpZCwgY2hhbmdlVHlwZSkge1xyXG4gICAgICAgIC8qXHJcbiAgICAgICAgICAgIGlkOiDopoHkuIrkuIvmnrbnmoTkuJPmoI9pZFxyXG4gICAgICAgICAgICBjaGFuZ2VUeXBl77yaIHRydWUt5LiK5p6277yMIGZhbHNlLeS4i+aetlxyXG4gICAgICAgICovXHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIHNob3dMb2FkaW5nKCk7XHJcbiAgICAgICAgJC5hamF4KCcvY2hhbmdlX2dvb2RzX3N0YXRlJyx7XHJcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcclxuICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcclxuICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgLypcclxuICAgICAgICAgICAgICAgIDEtZ29vZHNfaWQ7XHJcbiAgICAgICAgICAgICAgICAyLWdvb2RzX3R5cGUoMC3kuJPmoI8sMS3lm77mlocsMi3pn7PpopEsMy3op4bpopEsNC3nm7Tmkq0pO1xyXG4gICAgICAgICAgICAgICAgMy1vcGVyYXRlX3R5cGUoMC3kuIrmnrYsMS3kuIvmnrYpXHJcbiAgICAgICAgICAgICAgICAqL1xyXG4gICAgICAgICAgICAgICAgZ29vZHNfaWQ6IGlkLFxyXG4gICAgICAgICAgICAgICAgZ29vZHNfdHlwZTogMCxcclxuICAgICAgICAgICAgICAgIG9wZXJhdGVfdHlwZTogY2hhbmdlVHlwZSA/IDAgOiAxLFxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAvL2NvbnNvbGUubG9nKGRhdGEpO1xyXG4gICAgICAgICAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICAgICAgICAgIGlmKGRhdGEuY29kZSA9PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYmFzZVV0aWxzLnNob3cuYmx1ZVRpcChkYXRhLm1zZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVsb2FkUGFnZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgIH0sNzAwKTtcclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi50b29sQm94QnRuID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnRvRGV0YWlsID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBiYXNlVXRpbHMuc2hvdy5yZWRUaXAoZGF0YS5tc2cpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24oeGhyLCBzdGF0dXMsIGVycikge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnIpO1xyXG4gICAgICAgICAgICAgICAgaGlkZUxvYWRpbmcoKTtcclxuICAgICAgICAgICAgICAgIHNlbGYudG9vbEJveEJ0biA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnRvRGV0YWlsID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgIGJhc2VVdGlscy5zaG93LnJlZFRpcCgn5pON5L2c5aSx6LSl77yM6K+356iN5ZCO5YaN6K+VJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxufVxyXG4iXX0=