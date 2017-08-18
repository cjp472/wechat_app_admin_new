(function(win, $, doc, undefined){
	$(function() {
		new Person();
	});


	function Person(options) {
		this.options = options || {};
		this.app_id = $('#app_id').val();
		// this.hasShowCode = false;
		this.clickSwitch = false; //开关的可点击态
		this.init();
	}


	Person.prototype = {
		init: function() {
			var self = this, 
				$switch = this.miniSwitch = $('#miniPersonToggle');
			
			this.SwitchState = $switch.data('toggle');

			$switch.on('click', function() {
				self.clickSwitch || self.toggleSwitch.call(self);
			});

			// if(this.SwitchState == 1) {
			// 	this.showCode();
			// }

			$(doc).ajaxError(function(event, request, settings){
				console.error(settings);
				baseUtils.show.redTip('服务器开小差啦！');
			});
		},
		toggleSwitch: function(e) {
			var self = this;
			self.clickSwitch = true;
			if(this.SwitchState == 1) {
				this.SwitchState = 0;
				this.switchAjax(this.SwitchState, function() {
					self.miniSwitch.removeClass('opening').addClass('closing')
						.find('span').text('关闭');
					$('#notminiCode').show();
					$('#hasminiCode').hide();
				});
			} else {
				this.SwitchState = 1;
				this.switchAjax(this.SwitchState, function() {
					self.miniSwitch.removeClass('closing').addClass('opening')
						.find('span').text('开启');
					/*if(!this.hasShowCode) {
						this.showCode();
					}*/
					$('#notminiCode').hide();
					$('#hasminiCode').show();
				});
			}

		},
		switchAjax:function(state, callback) {
			var self = this;
			$.post('/mini/switch',{
				switch: state
			},function(json) {
				if(json.code == 0) {
					var successText = '';
					if(state == 1) {
						successText = '已开启，您的商店将在“小鹅通+”小程序中显示';
					} else {
						successText = '已关闭，您的商店将不继续在“小鹅通+”小程序中显示';
					}
					baseUtils.show.blueTip(successText);
					callback.call(self);
					//将开关置为可点击态
					self.clickSwitch = false;
				} else {
					baseUtils.show.redTip(json.msg);
				}
			},'json');
		}
		/*showCode: function() {
			this.hasShowCode = true;
			var qrcode = new QRCode(document.getElementById("miniCode"), {
		        text: miniPerson + this.app_id,
		        width: 150,
		        height: 150,
		        colorDark : "#000000",
		        colorLight : "#ffffff",
		        correctLevel : QRCode.CorrectLevel.L
		    });
		    setTimeout(function() {
			    var src = $('#miniCode img').prop('src');
			    $('#downloadCode').attr('href', src);
		    });
		}*/
	};
})(window, window.jQuery, document);