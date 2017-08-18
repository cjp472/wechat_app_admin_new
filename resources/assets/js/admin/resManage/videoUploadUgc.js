$(function () {
    videoUpload.init();

    $(".videoUploadCancle").hide();
});

var is_edit = false;
var fileId;
var videoGsize;
var transcodeNotifyUrl; // 转码成功回调地址
var videoTypes = ["WMV", "WM", "ASF", "ASX", "RM", "RMVB", "RA", "RAM", "MPG", "MPEG", "MPE", "VOB", "DAT", "MOV", "3GP", "MP4", "MP4V", "M4V", "MKV", "AVI", "FLV", "F4V","wmv", "wm", "asf", "asx", "rm", "rmvb", "ra", "ram", "mpg", "mpeg", "mpe", "vob", "dat", "mov", "3gp", "mp4", "mp4v", "m4v", "mkv", "avi", "flv", "f4v"]
var videoUpload=function () {
    var videoUpload={};

    /**
     * 计算签名
     **/
        // http://123.206.83.120:80/interface.php
    var getSignature = function(callback){
            $.ajax({
                url: '/getsigv4',
                data: JSON.stringify({
                    "Action":"GetVodSignatureV2"
                }),
                type: 'POST',
                dataType: 'json',
                success: function(res){
                    console.log(res);
                    if(res.result) {
                        console.log("成功");
                        callback(res.result);
                    } else {
                        console.log("获取签名失败");
                        return '获取签名失败';
                    }

                }
            });
        };

    videoUpload.init=function () {
        $("#video_file").click(function () {
            $("#uploadVideoNow-file").click();
        });

        function contains(arr, obj) {
            var i = arr.length;
            while (i--) {
                if (arr[i] === obj) {
                    return true;
                }
            }
            console.log('找不到');
            return false;
        };

        var getFileMessage = function(file, newname) {
            var fileMsg = {};
            return fileMsg.type = file.name.slice(file.name.match(/\.[^\.]+$/).index + 1),
                fileMsg.name = newname || file.name.slice(0, file.name.match(/\.[^\.]+$/).index),
                fileMsg.size = file.size,
                fileMsg.error = "",
            (void 0 == fileMsg.name || "" == fileMsg.name || /[:*?,<>\'\"\\]/g.test(fileMsg.name)) && (fileMsg.error = '文件名不得包含 / : * ? "  < > 等字符'),
                fileMsg
        }
        $('#uploadVideoNow-file').on('change', function (e) {
            var videoFile = this.files[0];
            var fileMsg = getFileMessage(videoFile,videoFile.name);
            console.log(fileMsg);
            console.log(fileMsg.error);
            //限制视频文件名不能包含特殊符号
            if(fileMsg.error!=''){
                baseUtils.show.redTip(fileMsg.error);
                $('#uploadVideoNow-file').val('');
                return false;
            }
            //限制视频文件类型为mp4文件
            if(!contains(videoTypes,fileMsg.type)){
                baseUtils.show.redTip('上传的文件必须为视频文件哦~');
                $('#uploadVideoNow-file').val('');
                return false;
            }
            //获取文件大小
            var videoSize = videoFile.size;
            videoGsize = Number(videoSize/(1024*1024)).toFixed(2);
            if(videoGsize>1000){
                baseUtils.show.redTip("请将视频文件压缩到 1G 以内");
                return false;
            }
            // videoFile.name
            var resultMsg = qcVideo.ugcUploader.start({
                videoFile: videoFile,
                getSignature: getSignature,
                allowAudio: 0,
                isTranscode: true,//是否转码
                transcodeNotifyUrl: transcodeNotifyUrl, //(转码成功后的回调地址)isTranscode==true,时开启； 回调url的返回数据格式参考  http://www.qcloud.com/wiki/v2/MultipartUploadVodFile
                success: function(result){
                    console.log('上传成功');
                    console.log(result);
                    is_edit = false;
                    $(".videoUploadLineA").css('width', "100%");
                    $(".videoPercent").text("已完成");
                    $(".uploadRatio span:nth-of-type(1)").text(videoGsize+"M");
                    $(".uploadRatio span:nth-of-type(2)").text(videoGsize+"M");
                },
                error: function(result){
                    console.log('上传失败');
                    console.log(result);
                },
                progress: function(result){
                    is_edit = true;
                    $('#videoName').hide();
                    $(".videoUploadBox").fadeIn(300);
                    $(".videoBoxTitle").text(result.name);
                    $(".videoSize").text(videoGsize+'M');
                    //视频正在解析中
                    if(result.shacurr<1){
                        $(".videoPercent").text("视频正在解析中");
                        $(".uploadRatio span").text("");
                        $(".videoUploadLineA").css("width","0%");
                    }else if(result.shacurr==1) {   //视频解析完成，开始上传
                        var uploadCompleteSize=parseFloat(videoGsize)*parseFloat(result.curr);
                        uploadCompleteSize=uploadCompleteSize.toFixed(2);
                        //当前百分比
                        var currPercent=Number(result.curr*100).toFixed(2);
                        $(".videoUploadLineA").css("width",currPercent+"%");
                        $(".videoPercent").text(currPercent+"%");
                        $(".uploadRatio span:nth-of-type(1)").text(uploadCompleteSize+"M");
                        $(".uploadRatio span:nth-of-type(2)").text(videoGsize+"M");
                    }
                    if(result.type == 'video') {
                        console.log(result);
                    }
                },
                finish: function(result){
                    console.log('finish');
                    console.log(result);
                    fileId=result.fileId;
                    VideoName = result.videoName;
                },
            });
        });
    }

    return videoUpload;
}();


