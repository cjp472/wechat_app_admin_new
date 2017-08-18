$(function () {
    resAdd.init();
});

//公共变量开始
//判定是编辑还是新建

//资源标识
var id;

//编辑或是修改
var pageType;

//资源类型
var resourceType;

//新增的渠道  默认为单品
var uploadChannelType = GetQueryString("upload_channel_type") || 1;

//付费形式
var resourceFree = uploadChannelType==1 ? 2 : null;

//专栏ID
var packageId = GetQueryString("package_id");

//(当资源为视频、直播时,有值)
var resourceParams;

//专栏名称
var packageName;

//专栏上下架状态
var columnState = 1;

//专栏期数显示
var show_resourcecount = 1;

//专栏分类数组
var classArray = [];

//音频参数开始
//正式音频大小
var fileAudio1Size;

//正式音频长度
var resAudio1Length;

//正式音频名
var AudioName;

//试听音频大小
var fileAudio2Size;

//试听音频长度
var resAudio2Length;

//试听音频名
var tryAudioName;

//音频参数结束

//图文参数开始
//图文参数结束

//视频参数开始
//视频名称
var VideoName;
//视频参数结束

//直播参数开始
//直播状态
var aliveState;
// 直播讲师列表
var roleParams = [];

//直播类型(默认为音频)
var aliveT = 0;

//直播参数结束


//成功后返回的URL
var backURL = '';

//推送消息的值
var prevVal;

var if_push, //是否开启推送
    push_ahead; //提前推送时间

//直播的播放状态（0未开始，1、4已开始，2,3结束）
var playState=0;

var is_distribute = $('#is_distribute').val();

var currentDate;//当前的时间

//公共变量结束

var resAdd = (function () {
    var resAdd = {};

    //文件大小
    var fileSize;

    //取消上传判断参数
    // var uploadCancel=0;
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


    //资源上传函数(参数：resType:资源类型,resTypeClass:资源类型中细分的种类,resLimitSize:资源限制大小)
    function resUpload(files, resType, resTypeClass, resLimitSize) {
        if (files && files.length > 0) {
            var file = files[0];
            //console.log(file);

            var resourceLocalUrl = getObjectURL(file);
            fileSize = (file.size / (1024 * 1024)).toFixed(2);
            resAudio1Lengh = file.duration;
            //如果是正式音频
            if (resTypeClass == "Audio1") {
                fileAudio1Size = fileSize;
                var resourceElement = document.getElementById("resourceTime");
                resourceElement.src = resourceLocalUrl;
                resourceElement.onload = function() {
                    removeObjectURL(resourceLocalUrl);
                };
                //获取音频时长
                //  resAudio1Lengh=resourceElement.duration;
                //  console.log(resAudio1Lengh);
                //  console.log($("#resourceTime"));
            }
            //如果是试听音频
            if (resTypeClass == "Audio2") {
                fileAudio2Size = fileSize;
                var tryElement = document.getElementById("tryTime");
                tryElement.src = resourceLocalUrl;
                tryElement.onload = function() {
                    removeObjectURL(resourceLocalUrl);
                };
            }
            //console.log(file.name);
            //console.log(fileSize);
            //如果是音频
            if (resType == 'audio') {
                var audioName = file.name;
                //alert(imgName);
                var ext,idx;
                idx = audioName.lastIndexOf(".");
                if (idx != -1){
                    ext = audioName.substr(idx+1).toUpperCase();
                    ext = ext.toLowerCase( );
                    //alert(file);
                    //alert("后缀="+ext+"位置="+idx+"路径="+resourceLocalUrl);
                    if (ext != 'mp3' && ext != 'm4a'){
                        //document.all.submit_upload.disabled=true;
                        baseUtils.show.blueTip("请上传音频类型的文件哦~!");
                        if(resTypeClass == 'Audio1'){
                            $('.upLoadAudio1').val('');
                        }else {
                            $('.upLoadAudio2').val('');
                        }

                        //alert("2.只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                        return;
                    }
                } else {
                    document.all.submit_upload.disabled=true;
                    if(resTypeClass == 'Audio1'){
                        $('.upLoadAudio1').val('');
                    }else {
                        $('.upLoadAudio2').val('');
                    }
                    baseUtils.show.blueTip("请上传音频类型的文件哦~!");
                    //alert("只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                    return;
                }
                $('#audioURL'+resTypeClass).hide();
                $(".uploadBox"+resTypeClass).fadeIn(300);
                // 初始化文件以及进度条
                $(".upload" + resTypeClass + "Name").html(file.name);
                $(".upload" + resTypeClass + "Size span").html(fileSize);
                $(".uploadPLine" + resTypeClass).show()
                    .find(".uploadPLineActive" + resTypeClass).css("width", '0');
                $(".uploadPersent" + resTypeClass).html('');
                if(resTypeClass == "Audio1"){//正式音频
                    AudioName = file.name;
                } else if(resTypeClass == "Audio2") {//试听音频
                    tryAudioName = file.name;
                }
            }

            // 限制资源在*MB内
            if ($uploadFile.checkFileSize(file, resLimitSize)) {
                $uploadFile.uploadRes(file, resType, function (data) {

                        if (resType == 'audio') {
                            var progress = parseInt(data * 100);
                            //console.log(progress);
                            $(".uploadPLineActive" + resTypeClass).css("width", progress + '%');
                            $(".uploadPersent" + resTypeClass).text(progress + "%");
                        }
                    },
                    // 上传成功回调
                    function (data) {
                        console.log(data);
                        baseUtils.show.blueTip("上传成功！");
                        $(".uploadPersent" + resTypeClass).text("完成");
                        //data = JSON.parse(data);
                        resUrl = data.data.access_url;
                        console.log(resUrl);
                        $("#" + resTypeClass + "Url").val(resUrl);
                        if (resTypeClass == "Audio2"){
                            $(".deleteTryAudio").show();
                        }
                        // 如果是图片资源，则展示图片预览
                        if (resType == 'image') {

                            var imgName = file.name;
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
                                    baseUtils.show.redTip("请上传图片类型的文件哦~");
                                    //alert("2.只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                                    return;
                                }
                            } else {
                                document.all.submit_upload.disabled=true;
                                baseUtils.show.redTip("请上传图片类型的文件哦~");
                                //alert("只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                                return;
                            }

                            //$(".previewPic" + resTypeClass + " img").attr("src", resUrl);
                            //直接加载本地图片进行预览
                            $(".previewPic" + resTypeClass + " img")
                            .load(function() {
                                removeObjectURL(resourceLocalUrl);
                            })
                            .attr("src", resourceLocalUrl);
                        }

                        // console.log(resUrl);
                    },
                    // 上传失败回调
                    function (data) {
                        console.error("上传失败!!!");
                        console.log(data);
                        baseUtils.show.redTip("上传失败！");
                    });
            } else {
                baseUtils.show.redTip("上传资源限制在" + resLimitSize + "MB内！");
                $(".upLoad"+resTypeClass).val("");

            }
        } else {
            baseUtils.show.redTip("网络错误，请稍后再试！");
            // console.log(files)
        }
    }

    resAdd.init = function () {
        //选择组件样式
        // $('select').material_select();
        //时间选择器初始化
        // $("#dateInput").datetimepicker({
        //     weekStart: 1, //一周从星期一开始
        //     minView: "day",
        //     format: 'yyyy-mm-dd hh:00:00',
        //     autoclose: true, //选择日期后自动关闭
        //     pickerPosition:'top-right'  //弹窗显示在输入框上方
        // });
        // $("#dateInput1").datetimepicker({
        //     weekStart: 1,
        //     minView: "day",
        //     format: 'yyyy-mm-dd hh:00:00',
        //     autoclose: true, //选择日期后自动关闭
        //     pickerPosition:'top-right'
        // });
        // $("#dateInput2").datetimepicker({
        //     weekStart: 1,
        //     minView: "day",
        //     format: 'yyyy-mm-dd hh:00:00',
        //     autoclose: true, //选择日期后自动关闭
        //     pickerPosition:'top-right'
        // })
        resourceType = GetQueryString("type");
        aliveTimeConfig(".dateSetInput");
        aliveTimeConfig(".dateSetInputBottom", "top-right");
        //获取资源类型

        //资源类型：图片：image,音频：audio
        //资源类型子类：
            //正式音频：Audio1,试听音频：Audio2,
            //封面:Image1,日签：Image2,直播宣传封面:Image3,贴片:Image4
        // 点击按钮上传图片
        $(".upLoadImage1").on("change", function () {
            resUpload(this.files, "image", "Image1", 5);
        });
        $(".previewPicImage1").click(function () {
            $(".upLoadImage1").click();
        })
        $(".upLoadImage2").on("change", function () {
            resUpload(this.files, "image", "Image2", 5);
        });
        $(".previewPicImage2").click(function () {
            $(".upLoadImage2").click();
        })
        $(".upLoadImage3").on("change", function () {
            resUpload(this.files, "image", "Image3", 5);
        });
        $(".previewPicImage3").click(function () {
            $(".upLoadImage3").click();
        })
        $(".upLoadImage4").on("change", function () {
            resUpload(this.files, "image", "Image4", 5);
        });
        $(".previewPicImage4").click(function () {
            $(".upLoadImage4").click();
        })

        // 点击按钮上传音频
        //正式
        $(".upLoadAudio1").on("change", function () {
            resUpload(this.files, "audio", "Audio1", 200);
        });
        //试听
        $(".upLoadAudio2").on("change", function () {
            resUpload(this.files, "audio", "Audio2", 200);
        });

        //试听音频置空
        $(".deleteTryAudio").click(function () {
            $(".upLoadAudio2").val("");
            $(".uploadBoxAudio2").hide();
            $("#Audio2Url").val("");
            $(this).hide();
            $("#audioURLAudio2").remove();
        });
        //取消上传
        // $(".uploadCancelA1").click(function () {
        //     $(".uploadBoxAudio1").remove();
        //     uploadCancel=1;
        // })
        // 选择付费与免费
        if(is_distribute != 1){
            $(".notFreeSelect").click(function () {
                $(".resPrize").removeAttr("readonly");
                $(".resPrize").removeAttr("disabled");
                resourceFree = 2;
                $('#courseRemind').show();
            });
            $(".FreeSelect").click(function () {
                $(".resPrize").attr("readonly", "readonly");
                $(".resPrize").attr("disabled", "disabled");
                $(".resPrize").val(0);
                resourceFree = 1;
                $('#courseRemind').hide();
                $('#courseRemind option[value=-1]').prop('selected',true);
                $('#push_ahead').change();
            })
        }


        //开课提醒
        push_ahead = $('#push_ahead option:selected').val();
        if_push = $('#if_push').val() || 1;
        function setIfPush() {
            if( push_ahead == -1 ) {
                if_push = 1;
            } else{
                if_push = 0;
            }
        }
        var $pushAhead = $('#push_ahead');
        if($pushAhead.length>0){
            console.log(push_ahead);
            setIfPush();
            console.log(if_push);
            if( $pushAhead.data('setting') == 1 ) { //用户未将服务号设置为教育行业
                $pushAhead.on('change', function(e) {
                    var ele = $(this),
                        curSelected = +ele.val();

                    ele.find('option[value='+ push_ahead +']').prop('selected',true);

                    if( push_ahead == -1 && curSelected != -1 ) {
                        var txt=  "您需要到微信公众号后台将您的服务号所在行业设置为“教育/培训”，设置完成后，开启服务号通知方可正常发送模板消息。"+
                        "<br/><a target='_blank' href='/help/instructions#hp5_wx_service' style='margin-top:10px;'>什么是模板消息？</a>";
                        var option = {
                            title: "提示", //弹出框标题
                            btn: 3, //确定&&取消
                            oktext: '我已设置',
                            canceltext: '关闭',
                            icon: 'blue',
                            onOk: function(){//发送请求，判断用户是否设置服务号行业
                                $.ajax('/has_industry',{
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {}
                                }).done(function(data) {
                                    var code = data.ret;
                                    if(code == 0){
                                        ele.find('option[value='+ curSelected +']').prop('selected',true);
                                        push_ahead = curSelected;
                                        setIfPush();
                                        /*消息推送开启*/
                                        baseUtils.show.blueTip("模板消息推送开启");
                                    } else {
                                        baseUtils.show.redTip("无法开启消息推送，请按照提示修改设置");
                                    }
                                }).fail(function(xhr, text, err) {
                                    console.error(err);
                                    baseUtils.show.redTip("网络错误，请稍后再试");
                                });
                            }
                        }
                        $.alert(txt, "custom", option);
                        return false;
                    } else {
                        /*关闭消息推送*/
                        push_ahead = curSelected;
                        setIfPush();
                        ele.find('option[value='+ curSelected +']').prop('selected',true);
                    }
                });
            } else {
                $pushAhead.on('change', function(e) {
                    var ele = $(this);

                    push_ahead = +ele.val();

                    setIfPush();
                })
            }
        }
        //编辑器预览
        $('#preview').on('click',function(){
            var html = ue.getContent();
            document.getElementById('preview_content').innerHTML = html;
            $('.preview_con').addClass('active');
            $('.preview_box').addClass('active');
            document.documentElement.style.overflow = "hidden";
        });

        $('.preview_con').on('click',function(){
            $(this).removeClass('active');
            $('#preview_content').html('');
            $('.preview_box').removeClass('active');
            document.documentElement.style.overflow = "auto";
        });

        //   选择语音或者音频（直播）
        $(".videosSelect").click(function () {
            $(".aliveVideoBox").slideDown(300);
            aliveT = 1;
            console.log(aliveT);
            VideoName = null;
        });
        $(".voiceSelect").click(function () {
            $(".aliveVideoBox").slideUp(300);
            aliveT = 0;
            console.log(aliveT);
        });
        $(".aliveSelect").click(function () {
            $(".aliveVideoBox").slideUp(300);
            aliveT = 2;
            console.log(aliveT);
        });
        $(".pptAliveSelect").click(function () {
            $(".aliveVideoBox").slideUp(300);
            aliveT = 3;
            console.log(aliveT);
        });

        //选择专栏上下架
        $(".columnShow").click(function () {
            columnState = 0;
        });
        $(".columnHide").click(function () {
            columnState = 1;
        });

        //选择显示期数
        $(".countShow").click(function () {
            show_resourcecount = 1;
        });
        $(".countHide").click(function () {
            show_resourcecount = 0;
        });


        //点击上一步返回
        $('.lastStepBtn').click(function() {
            window.history.back();
        });

        var $getBack = $('#getBack');
        if(uploadChannelType == 1) {
            if(resourceType == 4){
                backURL = '/resource_list_page?resource_type=4';
                $getBack.attr('href', backURL);
            }else{
                backURL = '/resource_list_page';
                $getBack.attr('href', backURL);
            }
        } else if(uploadChannelType == 2) {
            backURL = '/package_detail_page?id=' + packageId;
            $getBack.attr('href', backURL);
        } else if(uploadChannelType == 3) {
            backURL = '/member_detail_page?id=' + packageId;
            $getBack.attr('href', backURL);
        }

        //点击侧边栏离开时的弹框
        changeSaveFlag(true);


        //服务号通知弹出的逻辑判断
        var $serviceToggle = $('#serviceToggle');
        var collection =$serviceToggle.data('collection');// 0企业模式  1个人模式、
        prevVal = +$("#serviceToggle :radio:checked").val() || null; //当前单选框的值
        if($serviceToggle.length>0 ){
            if( collection == 0 && $serviceToggle.data('setting') == 1) { //用户未将服务号设置为教育行业,而且模式为企业模式才会显示
                $serviceToggle.on('click', ':radio', function(e) {
                    console.log('hahaha')
                    console.log(collection);
                    var ele = $(this);
                    var currVal = +ele.val();//触发选择事件后单选框的值

                    if( currVal !== prevVal && currVal == 1 ) {
                        e.preventDefault();
                        var txt=  "您需要到微信公众号后台将您的服务号所在行业设置为“教育/培训”，设置完成后，开启服务号通知方可正常发送模板消息。"+
                        "<br/><a target='_blank' href='/help/instructions#hp5_wx_service' style='margin-top:10px;'>什么是模板消息？</a>";
                        var option = {
                            title: "提示", //弹出框标题
                            btn: 3, //确定&&取消
                            oktext: '我已设置',
                            canceltext: '关闭',
                            icon: 'blue',
                            onOk: function(){//发送请求，判断用户是否设置服务号行业
                                $.ajax('/has_industry',{
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {}
                                }).done(function(data) {
                                    var code = data.ret;
                                    if(code == 0){
                                        ele.prop('checked', true);
                                        prevVal = currVal;
                                        /*消息推送开启*/
                                        baseUtils.show.blueTip("模板消息推送开启");
                                    } else {
                                        baseUtils.show.redTip("无法开启消息推送，请按照提示修改设置");
                                    }
                                }).fail(function(xhr, text, err) {
                                    console.error(err);
                                    baseUtils.show.redTip("网络错误，请稍后再试！");
                                });
                            }
                        }
                        $.alert(txt, "custom", option);
                        return false;
                    } else {
                        /*关闭消息推送*/
                        prevVal = currVal;
                        params['push_state'] = currVal;
                        $('#serviceToggle').data('state',currVal);
                    }
                });
            } else {
                $serviceToggle.on('click', ':radio', function(e) {
                    e.preventDefault();
                    // console.log('lalala')
                    // console.log(collection);
                    var ele = $(this);
                    var currVal = ele.val();
                    var p_id =GetQueryString('package_id');//product_id
                    var selectTime = $('#dateInput').val();//发布时间
                    $.get('/check_goods_message_push/' + p_id + '/' + selectTime, function (data) {//发送请求判断是否有开启消息通知开关
                        console.log(data);
                        $('#valid_push').html(data.data.valid_push);
                        if (data.data.has_push >= 3) {
                            $('#fBtn1').attr('disabled', true);
                            $('#has_push').html(3);//写死已发3条
                        }else{
                            $('#has_push').html(data.data.has_push);
                            $('#fBtn1').attr('disabled', false);
                        }
                        if (data.code == -1) {//如果关闭了功能模块的消息通知开关
                            if( currVal !== prevVal && currVal == 1 ) {

                                var txt = "您尚未开启服务号通知，是否立即开启服务号通知？";
                                var option = {
                                    title: "提示", //弹出框标题
                                    btn: 3, //确定&&取消
                                    oktext: '开启通知',
                                    canceltext: '关闭',
                                    icon: 'blue',
                                    onOk: function(){//发送请求，打开消息推送开关
                                        $.ajax('/set_service_notification',{
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {status:1}
                                        }).done(function(result) {
                                            if (result.code == 0) {
                                                ele.prop('checked', true);
                                                prevVal = currVal;
                                                baseUtils.show.blueTip("已开启服务号消息通知");
                                                // baseUtils.show.blueTip("模板消息推送开启");
                                            } else {
                                                baseUtils.show.redTip("操作失败，请稍后再试。");
                                            }
                                        }).fail(function(xhr, text, err) {
                                            console.error(err);
                                            baseUtils.show.redTip("网络错误，请稍后再试！");
                                        });
                                    }
                                };
                                $.alert(txt, "custom", option);
                            } else {
                                prevVal = currVal;
                                ele.prop('checked', true);
                            }

                        }else{
                            if( currVal !== prevVal && currVal == 1 ) {
                                prevVal = currVal;
                                baseUtils.show.blueTip("模板消息推送开启");
                                ele.prop('checked', true);
                            } else {
                                prevVal = currVal;
                                ele.prop('checked', true);
                            }
                        }
                    });

                });
            }
        }
    };
    return resAdd;
})();

//公共方法

//获取当前日期格式
getNowFormatDate();
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    currentDate = year + seperator1 + month + seperator1 + strDate
        + " " + date.getHours() + seperator2 + date.getMinutes()
        + seperator2 + date.getSeconds();
    return currentDate;
}
