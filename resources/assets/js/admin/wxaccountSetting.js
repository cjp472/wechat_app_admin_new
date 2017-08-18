$(function() {
	var $reBackImg = $('#reBackImg'),
		file = $reBackImg.attr('src'),
		imgUrl = null,
		params = {},
		cos = new InitCosCloud();

	$('#save').on('click', function() {
		var name = $.trim( $('#wx_app_name').val() );

		if(name == '') {
			baseUtils.show.redTip('亲,请输入公众号名称哦!');
			return false;
		}

		/*if(file == null || file == '') {
			baseUtils.show.redTip('亲,请设置公众号二维码哦');
			return false;
		}*/
		params['wx_app_name_define'] = name;
		showLoading();
		uploadImg();
	});



	$('#uploadImage').on('change', function(e) {
		var newFile = this.files[0];

        var imgName = newFile.name;
        //alert(imgName);
        var ext,idx;
        idx = imgName.lastIndexOf(".");
        if (idx != -1){
            ext = imgName.substr(idx+1).toUpperCase();
            ext = ext.toLowerCase( );
            //alert(file);
            //alert("后缀="+ext+"位置="+idx+"路径="+resourceLocalUrl);
            if (ext != 'jpg' && ext != 'png' && ext != 'jpeg' && ext != 'gif'){
                //document.all.submit_upload.disabled=true;
                baseUtils.show.blueTip("请上传图片类型的文件哦~");
                //alert("2.只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                return;
            }
        } else {
            document.all.submit_upload.disabled=true;
            baseUtils.show.blueTip("请上传图片类型的文件哦~");
            //alert("只能上传.jpg  .png  .jpeg  .gif类型的文件!");
            return;
        }

		if(newFile){
			file = newFile;
			imgUrl = getObjectURL(newFile);
			$reBackImg.prop('src', imgUrl);
		}
		//removeObjectURL(imgUrl);

	});



	function getObjectURL(file) {
	    var url = null;
	    if (window.createObjectURL != undefined) {
	        url = window.createObjectURL(file);
	    } else if (window.URL != undefined) {
	        url = window.URL.createObjectURL(file);
	    } else if (window.webkitURL != undefined) {
	        url = window.webkitURL.createObjectURL(file);
	    }
	    return url;
	}

	function removeObjectURL(url) {//释放资源URL
	    if (window.revokeObjectURL != undefined) {
	        window.revokeObjectURL(url);
	    } else if (window.URL != undefined) {
	        window.URL.revokeObjectURL(url);
	    } else if (window.webkitURL != undefined) {
	        window.webkitURL.revokeObjectURL(url);
	    }
	}

	function getSuffix(filename){ //获取文件后缀名
    	var names = filename.split('.');
    	return names[names.length - 1];
	}

	function uploadImg() {
		if( imgUrl ) {
			removeObjectURL(imgUrl);
			imgUrl = null;
			browserMD5File(file,function(err,md5) {
	            var remotePath = get_cos_image_path() + md5 + "." + getSuffix(file['name']);
	            cos.uploadFileWithoutPro(function (result) {
	                params['wx_qr_url']=result.data.access_url;
	                updateInfo();
	            },function (result){     //失败回调
	                baseUtils.show.redTip("上传失败!");
	            }, remotePath, file, 0);
	        });
		} else {
			updateInfo();
		}

	}

	function updateInfo() {
		$.ajax("/updatewxaccountinfo",{
			type: 'POST',
			dataType: 'json',
			data: {"params":params},
			success: function(data) {
	        	hideLoading();
		        if(data.code==0) {
		            baseUtils.show.blueTip(data.msg,function() {
		                window.location.reload();
		            });
		        } else {
		            baseUtils.show.redTip(data.msg);
		        }
		    }
		});
	}

});

