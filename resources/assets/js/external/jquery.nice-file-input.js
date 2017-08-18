(function(){
	
			var uMatch = navigator.userAgent.match(/Firefox\/(.*)$/);
			var ffVersion;
			var ffSize = 4.3 ;
			if (uMatch && uMatch.length > 1) {
					ffVersion = uMatch[1];
				}
			
			$.fn.niceFileInput = function(options){
				var settings = $.extend( {
				  'width'         : '500', //width of button
				  'height'		  : '40',  //height of text
				  'btnText'       : 'Browse', //text of the button     
				  'btnWidth'	  : '60' ,  // width of button
				  'margin'        : '20',	// gap between textbox and button
				}, options);
				     			 			
			for(var i= 150 ; i <= settings.width ; i += 5)
			{
				 ffSize = ffSize + 0.715; 				 
			}
			
			this.css({
						'height':settings.height, 
						'width' :settings.btnWidth ,
						'zIndex': '99',
						'opacity': '0', 
						'position':'absolute', 
						'right':'0',
						'left':'0',
						'top':'0',
						'font-size' : '16px'
					})
					.wrap('<div class="fileWrapper" />')
					.parent()
					.css({
						   width : settings.width
				     })
					.append("<input type='button' class='fileInputButton' value='"+settings.btnText+"' style='float: left;height:"+settings.height+"px ; width:"+settings.btnWidth+"px;background-color: white' />")
					.append("<div class='name-progress' style='display: block;border-radius: 4px;border: 0px solid #ccc;float: left;width: 500px;height: 34px'>"
							+"<input type='text' class='fileInputText' readonly='readonly' style='background: none;border: none;width: 500px;height: 34px;margin-top: 0px' />"
							+"<div class='progress' style='display: none;margin-left: 0px;width: 50%;height: 10px;vertical-align: top;margin-top: 12px;'>"
							+"<div id='progress_audio' class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%;'></div>"
							+"</div>"
							+"<button class='progress-state' style='display:none;line-height:34px;margin-left: 1%;height: 34px;border: none;padding: 0;background: none;color: blue;outline: none'>上传中</button>"
							+"</div>"
					)

				    $(".fileInputText").css('border-radius','4px');
					//Stuphin修改的样式
					$(".fileInputButton").css({'border-radius':'4px','border':'1px solid #ccc'});
					$("#public_audio").mouseover(function ()
					{
						$(".fileInputButton").css({'background-color':'#e6e6e6',
						'border-color':'#adadad','color':'#000'});
					}).mouseout(function ()
					{
						$(".fileInputButton").css('background-color', 'white');
						$(".fileInputButton").css('color', 'black');
					});

					if(ffVersion < 22)
						{
							this.attr('size',ffSize);							
						}														
					this.parent().find('input[type="text"].fileInputText').css({
						'height' : "34px" ,
						'width'  : function(){
									return settings.width - settings.btnWidth - settings.margin + "px";
									},
						'line-height' : "34px"
					});
				
				$(this).change(function()
				{
						// var textPath = $(this).val().replace("C:\\fakepath\\", "");
						// // $(this).closest('.fileWrapper').find('.name-progress').css('display','block');
						// $('.name-progress').css({'border':'1px solid #ccc','display':'block'});
						// $(".fileInputText").css('width','40%');
						// $('.progress').css('display','inline-block');
						// $('.btn_cancel').css('display','inline-block');
                        //
						// $(this).closest('.fileWrapper').find(".fileInputText").val(textPath);
					}			
				)};
				
				return this;					
		})();
	
