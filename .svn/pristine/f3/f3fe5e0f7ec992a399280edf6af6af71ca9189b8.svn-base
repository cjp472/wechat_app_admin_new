var width;
var height;

var params = {};

var pay_type = 2; //付费类型,1表示免费、2表示单个付费,3表示专栏。

var resource_type;//创建类型

//腾讯云资源
//var bucketName = window.cos_bucket_name;
//var cos = new CosCloud(window.cos_app_id);
var cos = new InitCosCloud();

//SetInterval返回ID
var SID;
//死进度条每次变化量
var Poffset;

var files = []; //存放文件
var transcodeNotifyUrl;// 转码成功回调地址
var secretId;
var fileId;
var videoGsize;

var is_edit = false;

$(document).ready(function () {
    //初始化上传空间
    initUpload();
});


/**
 * 获取uri参数
 * @param name
 * @param url
 * @returns {*}
 */
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function getObjectURL(file) {
    var url = null;
    if (window.createObjectURL != undefined) {
        url = window.createObjectURL(file)
    } else if (window.URL != undefined) {
        url = window.URL.createObjectURL(file)
    } else if (window.webkitURL != undefined) {
        url = window.webkitURL.createObjectURL(file)
    }
    return url
}


// 上传视频初始化
function initUpload() {
    var $ = qcVideo.get('$');
    var Version = qcVideo.get('Version');
    if (!qcVideo.uploader.supportBrowser()) {
        if (Version.IS_MOBILE) {
            baseUtils.show.redTip('当前浏览器不支持上传，请升级系统版本或者下载最新的chrome浏览器');
        } else {
            getInternetExplorerVersion();
            // baseUtils.show.redTip('当前浏览器不支持上传，请升级浏览器或者下载最新的chrome浏览器');
        }
        return;
    }
    //取消上传并删除文件
    var isTranscode = 1; //开启转码
    var isWatermark = 0;// 水印

    accountDone('video_file', isTranscode, isWatermark, "");
}

var accountDone = function (upBtnId, isTranscode, isWatermark, classId) {

    var $ = qcVideo.get('$')
        , ErrorCode = qcVideo.get('ErrorCode')
        , Log = qcVideo.get('Log')
        , JSON = qcVideo.get('JSON')
        , util = qcVideo.get('util')
        , Code = qcVideo.get('Code')
        , Version = qcVideo.get('Version')
        ;
    qcVideo.uploader.init(
        {
            web_upload_url: 'https://vod2.qcloud.com/v3/index.php',
            //secretId: secretId, // 云api secretId

            getSignature: function (argObj, done) {
                console.log(1);
                console.log(argObj.ft);

                //argObj['s'] = secretId;
                //argObj['uid'] = 100;
                //var argStr = [];
                //for (var a in argObj)
                //    argStr.push(a + '=' + encodeURIComponent(argObj[a]));
                //argStr = argStr.join('&');
                ////var argStr = 'f=' + encodeURIComponent(argObj.f)
                ////+ '&ft=' + encodeURIComponent(argObj.ft)
                ////+ '&fs=' + encodeURIComponent(argObj.fs);
                //var sign_url = '/getsig?' + argStr;
                //
                ////注意：出于安全考虑， 服务端接收argStr这个参数后，需要校验其中的Action参数是否为 "MultipartUploadVodFile",用来证明该参数标识上传请求
                //$.ajax({
                //    'dataType': 'json',
                //    'url': sign_url,
                //    'success': function (d) {
                //        done(d['result']);
                //    }
                //});

                argObj['s'] = 'AKIDTdzn2PhgeuKvr2ue4dSbMHWSDMjK8d7v';
                argObj['uid'] = '必填';
                var argStr = [];
                for (var a in argObj)
                    argStr.push(a + '=' + encodeURIComponent(argObj[a]));
                argStr = argStr.join('&');
                var sha = CryptoJS.HmacSHA1(argStr, 'O4gThhgoyWCCZ3mp44CyovASyh96hmy4');
                sha.concat(CryptoJS.enc.Utf8.parse(argStr));
                done(CryptoJS.enc.Base64.stringify(sha));
            },
            upBtnId: upBtnId, //上传按钮ID（任意页面元素ID）
            isTranscode: isTranscode,//是否转码
            isWatermark: isWatermark,//是否设置水印
            after_sha_start_upload: true,//sha计算完成后，开始上传 (默认关闭立即上传)
            sha1js_path: '/calculator_worker_sha1.js', //计算sha1的位置
            disable_multi_selection: false, //禁用多选 ，默认为false
            transcodeNotifyUrl: transcodeNotifyUrl,//(转码成功后的回调地址)isTranscode==true,时开启； 回调url的返回数据格式参考  http://www.qcloud.com/wiki/v2/MultipartUploadVodFile
            classId: classId,
            filters: {max_file_size: '2gb', mime_types: ['MP4'], video_only: true},
            //forceH5Worker: !!parseInt(getParameterByName('forceH5Worker')) || false,
            forceH5Worker: true
        }
        , {

            /**
             * 更新文件状态和进度
             * code:1、准备计算SHA 2、等待上传 3、SHA计算中 4、即将上传 5、上传进度更新 6、上传完成
             * @param args { id: 文件ID, size: 文件大小, name: 文件名称, status: 状态, percent: 进度 speed: 速度, errorCode: 错误码,serverFileId: 后端文件ID }
             */
            onFileUpdate: function (args) {
                //获取上传文件id
                console.log(transcodeNotifyUrl);
                $(".videoUploadCancle").data('videoid',args.id);
                $(".videoUploadBox").fadeIn(300);
                // fileId=args.id;
                // console.log(fileId);
                //判断上传视频大小
                var videoSizeS = args.size;
                videoGsize = videoSizeS/(1024*1024);
                is_edit = true;
                // 如果视频大小大于1G，则在上传流程中删除当前文件
                if(videoGsize>1000){
                    setTimeout(function () {
                        alert("请将视频文件压缩到 1G 以内!")
                    },100);
                    qcVideo.uploader.deleteFile(args.id);
                    deleteFromFile(args.id); //从文件数组中删除该文件
                    $('#' + args.id).remove();
                    console.log('文件超过1G,提示重新选择视频文件:' + args.id);
                    args = '';
                    return;
                }

                if (args.code == 1) {

                    if (files.length == 1) {
                        //删除之前的添加新的
                        if (files[0] != undefined) {
                            var old_id = files[0].id;
                            qcVideo.uploader.deleteFile(old_id);
                            deleteFromFile(old_id); //从文件数组中删除该文件
                            $('#' + old_id).remove();
                            console.log('删除了一个文件:' + old_id);
                        }
                    }

                    var $line = $("#" + args.id);
                    if (!$line.get(0)) {
                        $('#progress_show').append("<div class='progress_div' id = '" + args.id
                            + "'><span style='font-size: 12px'>" + args.name + ":</span><div class='progress_bar' id = 'progress_bar_" + args.id
                            + "'><div class='bar' id='bar_" + args.id
                            + "'></div></div><span class='upload_finish' id='upload_finish_" + args.id
                            + "'>上传完成</span><span data-act='del' data-type='0' class='delete'>删除</span></div>");
                    }


                    var info_item = {
                        'id': args.id,
                        'size': args.size,
                        'state': 0,
                        'file_id': '',
                        'size_text': args.size_text,
                        'type': 1
                    };

                    files[files.length] = info_item;
                    $(".videoBoxTitle").text(args.name);
                    $(".videoSize").text(args.size_text);
                    $(".uploadRatio span:nth-of-type(2)").text(args.size_text);
                    //进度百分比置为0
                    $(".videoPercent").text("视频正在解析中");
                    $(".uploadRatio span").text("");
                    $(".videoUploadLineA").css("width","0%");

                    console.log("添加了一个新的文件:" + "id:" + args.id + "size:" + args.size);
                }

                if(args.code==2){
                //qcVideo.uploader.startUpload();
                    files[0]['state'] = 2;
                    //videoUpload();
                }

                if (args.code == 5 && args.percent > 0) //上传进度发生变化
                {
                    var uploadCompleteSize=parseFloat(args.size_text)*parseFloat(args.percent/100);
                    uploadCompleteSize=uploadCompleteSize.toFixed(2);
                    $(".videoUploadLineA").css("width",args.percent+"%");
                    $(".videoPercent").text(args.percent+"%");
                    $(".uploadRatio span:nth-of-type(1)").text(uploadCompleteSize);
                    $(".uploadRatio span:nth-of-type(2)").text(args.size_text);
                    if(!args.speed){
                        $(".uploadSpeed span").text("");
                        $(".videoTimeLeft span").text("");
                    }else{
                        $(".uploadSpeed span").text(args.speed);
                    }

                }
                if (args.code == 6) //上传成功
                {
                    is_edit = false;
                    var uploadCompleteSize=args.size_text*args.percent;
                    $(".videoUploadLineA").css('width', "100%");
                    $(".videoPercent").text("已完成");
                    $(".uploadRatio span:nth-of-type(1)").text(args.size_text);
                    $(".uploadRatio span:nth-of-type(2)").text(args.size_text);
                    fileId=args.serverFileId;
                    //修改files中文件的状态
                    for (var i = 0; i < files.length; i++) {
                        if (args.id == files[i]['id']) {
                            files[i]['state'] = 6;
                            files[i]['file_id'] = args.serverFileId;
                            break;
                        }
                    }

                    if (isUploadFinish()) {
                        //提交文件
                        var externalParams = generateParams();
                        // uploadToServer(externalParams);
                    }

                }

                console.log(args);
            },

            /**
             * 文件状态发生变化
             * @param info  { done: 完成数量 , fail: 失败数量 , sha: 计算SHA或者等待计算SHA中的数量 , wait: 等待上传数量 , uploading: 上传中的数量 }
             */
            onFileStatus: function (info) {
                console.log('各状态总数-->' + JSON.stringify(info));
            },

            /**
             *  上传时错误文件过滤提示
             * @param args {code:{-1: 文件类型异常,-2: 文件名异常} , message: 错误原因 ， solution: 解决方法}
             */
            onFilterError: function (args) {
                var msg = 'message:' + args.message + (args.solution ? (';solution==' + args.solution) : '');
               if(args.code==-1){
                   baseUtils.show.redTip("请上传正确格式的文件");
               }
            },

        }
    );


    // $('.videoUploadCancle').on('click', '[data-act="del"]', function (e) {
    //     var $line = $(this).parent();
    //     var fileId = $line.get(0).id;
    //     Log.debug('delete', fileId);
    //     $line.remove();
    //     //@api 删除文件
    //     qcVideo.uploader.deleteFile(fileId);
    //     deleteFromFile(fileId); //从文件数组中删除该文件
    // });
    $(".videoUploadCancle").click(function(){
        $(".videoUploadBox").fadeOut(300);
        Log.debug('delete', fileId);
        // $line.remove();
        //@api 删除文件
        qcVideo.uploader.deleteFile(fileId);
        deleteFromFile(fileId); //从文件数组中删除该文件
        fileId=null;
    });
}

//删除某个元素
function deleteFromFile(id) {
    for (var i = 0; i < files.length; i++) {
        var item = files[i];
        if (id == item['id']) {
            //删除项目
            files.splice(i, 1);
            console.log('从files中删除了一个文件:' + id);
        }
    }
}

//判断两个视频是否都上传完成了
function isUploadFinish() {
    var isFinish = true;
    for (var i = 0; i < files.length; i++) {
        if (files[i]['state'] != 6) { //有一个没完成。
            isFinish = false;
        }
    }
    return isFinish;
}
//生成参数
function generateParams() {
    var file_id_params = {};
    if (files.length == 1) {

        file_id_params = {
            'public_video': files[0]['file_id'], 'public_size_text': (files[0]['size']/(1024*1024))
        };
    }
    return file_id_params;
}


//上传至我们服务器
// function uploadToServer(externalParams) {
//     var allParams = {};
//     allParams['resource_type'] = resource_type;
//     allParams['resource_params'] = externalParams;
//     allParams['params'] = params;
//     allParams['is_single_sale'] = is_single_sale;
//     //上传至服务器
//     $.post('/upload_video', allParams, function (result) {
//         hideLoading();
//         var code = result.code;
//         var msg = result.msg;
//         if (code == 0) {
//             baseUtils.show.blueTip(msg, function () {
//                 Is_Save = false;
//                 if (allParams['resource_type'] == 'audio') {
//                     window.location.href = '/audio_list';
//                 } else if (allParams['resource_type'] == 'video') {
//                     window.location.href = '/video_list';
//                 } else {
//                     window.location.href = '/audio_list';
//                 }
//             });
//         } else {
//             baseUtils.show.redTip(msg);
//         }
//     });

// }

var try_count=0;

//对于大文件,计算shar1值比较慢
function videoUpload() {
    if (try_count >= 300) {
        hideLoading();
        baseUtils.show.redTip("视频解析失败,请更新浏览器版本重新上传");
        return;
    }
    setTimeout(
        function () {
            if(files.length>0&&files[0]['state']==2){ //如果解析完了的话
                console.log('解析完成');
                qcVideo.uploader.startUpload();
            }else{
                try_count++;
                console.log('解析还未完成');
                videoUpload();
            }
        },1000
    )
}



