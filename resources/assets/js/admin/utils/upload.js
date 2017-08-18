// 上传文件
// 依赖腾讯云V3上传组件/获取md5组件 swfobject.js/cloud_sdk.js/browser-md5-file.js
var $uploadFile = (function() {
    var uploadFile = {};
    // cos上传对象
    //var cosObj = new CosCloud(window.cos_app_id);
    //// 上传bucket
    //var bucketName = window.cos_bucket_name;
    var cosObj = new InitCosCloud();

    //// 插入flash，大文件用
    //function appendFlash() {
    //    // flash对象（分片上传用）
    //    if (!$('#qs').length) {
    //        $('body').append('<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="sdk/Somethingtest.swf" style="visibility: visible;"></object>');
    //    }
    //
    //}

    // 获取文件目录
    function get_cos_image_path() {
        var app_id = $("#xcx_app_id").val();
        return '/' + app_id + '/image/';
    }

    // 上传图片
    uploadFile.uploadPic = function(file, successCallBack, failCallBack) {
        // 插入flash，大文件用
        //appendFlash();

        // 成功回调
        var success = function(data) {
            if (successCallBack) {
                successCallBack(data);
            }
        };
        // 失败回调
        var error = function(data) {
            if (failCallBack) {
                failCallBack(data);
            }
        };
        // 目录路径
        var uploadPath = get_cos_image_path();

        //获取文件的MD5值
        browserMD5File(file, function(err, md5) {
            // 文件后缀
            var filePostfix = file.name.split(".")[1];
            // 上传路径
            uploadPath = uploadPath + md5 + "." + filePostfix;
            // 上传到腾讯云
            cosObj.uploadFileWithoutPro(success, error, uploadPath, file, 0);
        })
    }


    // 文件大小校验(MB）
    uploadFile.checkFileSize = function(file, limitSize) {
        var fileSize = (file.size / (1024 * 1024)).toFixed(3);
        if (fileSize > limitSize) {
            return false;
        }
        return true;
    };

    //kevin
    function getCosResPath(resType) {  //获取appid，拼接url
        var appId = $.trim( $("#xcx_app_id").val() );
        // console.log(appId);
        return '/' + appId + '/' + resType + '/';
    }
    // 上传音视频抽出kevin
    uploadFile.uploadRes = function(file, resType, progressCallBack, successCallBack, failCallBack) {
        // 插入flash，大文件用
        // console.log(resType);
        //appendFlash();
        // 进度回调
        var progress = function(data) {
            if (progressCallBack) {
                progressCallBack(data);
            }
        }
        // 成功回调
        var success = function(data) {
            if (successCallBack) {
                successCallBack(data);
            }
        };
        // 失败回调
        var error = function(data) {
            if (failCallBack) {
                failCallBack(data);
            }
        };
        // 目录路径
        var uploadPath = getCosResPath(resType);
        //获取文件的MD5值
        browserMD5File(file, function(err, md5) {
            // 文件后缀
            //var filePostfix = file.name.split(".")[1];
            var filePostfix = /\.(\w+)$/g.exec(file.name)[1];
            // 上传路径
            uploadPath = uploadPath + md5 + "." + filePostfix;
            console.log(uploadPath);
            // 上传到腾讯云
            cosObj.uploadFile(success, error, progressCallBack, uploadPath, file, 0);
        })
    }



    return uploadFile;
})();
