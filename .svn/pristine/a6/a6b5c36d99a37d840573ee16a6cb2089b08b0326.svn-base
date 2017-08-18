var width;
var height;

var params = {};
var allParams = {};

var pay_type; //付费类型,1表示免费、2表示单个付费,3表示专栏。

var resource_type;//创建类型

var resource_url;//资源url

//判断配图是否修改了
var isEditPic = false;
//判断资源是否修改了
var isEditResource = false;
//判断贴片是否修改了
var isEditPatchPic = false;


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

//是否单卖
var is_single_sale = 0;

var prevVal;

$(document).ready(function () {
    prevVal = +$("#serviceContent :radio:checked").val() || null; //当前单选框的值
    params['push_state'] = prevVal;

    //替换选择文件样式框
    $(".nicefile").niceFileInput({
        'width': '300', //width of button - minimum 150
        'height': '40',  //height of text
        'btnText': '选择文件', //text of the button
        'btnWidth': '100',  // width of button
        'margin': '20',	// gap between textbox and button - minimum 14
        'left':'0'
    });
    $('.fileWrapper').css('width','500px');
    $('.fileInputText').css('width','500px');
    $('.fileInputText').val(resource_url);
    $('.name-progress').css('border','1px solid #ccc');

    var checkedState = $("#checkbox-img").attr("checked-state");
    if (checkedState == "checked"){
        pay_type = 3;
        $('#serviceContent').show();
    }
    if( $('#package_select').hasClass('border_blue') ){
        $('#serviceContent').show();
    }

    //checkbox"专栏外单卖"
    $("#checkbox-img").click(function () {

        var checkedState = $("#checkbox-img").attr("checked-state");
        if (checkedState == "unchecked") {
            $("#checkbox-img").attr("checked-state", "checked");
            $("#checkbox-img").css("backgroundPosition", "-104px 0");
            $("#single_price_div").removeClass('hide');
            pay_type = 3;

        } else if (checkedState == "checked") {
            $("#checkbox-img").attr("checked-state", "unchecked");
            $("#checkbox-img").css("backgroundPosition", "-78px 0");
            $("#single_price_div").addClass('hide');

        }
    });

    //选择文件 -- 取消操作
    $('.btn_cancel').click(function () {
        $(this).css('display','none');
        $('.progress').css('display','none');
        $('.fileInputText').css('width','500px');
        $('.fileInputText').val(resource_url);
    })

    $('#resource_title').on('blur keyup change',function () {
        setMsgDim(this);
    });
    $('#resource_price').on('blur keyup', function () {
        setMsgDim(this);
    });
    $('#free_select').on('click', function(){
        $(this).parents('.upload_input_div').find('.upload_input_msg').css('color','');
    });
    $('#package_select').on('click', function () {
        $(this).parents('.upload_input_div').find('.upload_input_msg').css('color','');
    });

    datetimepickerconfig("#start_time");

    changeSingleAndPackage();

    //监听输入框价格的变化
    monitorPriceChange();

    initUpload();

    isShowService();
    openService();
    $("#resource_package").on('change', function() {
        isShowService();
    });
});


function isShowService() { //判断能否开启推送
    if( $('#package_select').hasClass('border_blue') ){ //如果当前选中的是专栏
        var packagePrice = $('#resource_package option:selected').attr('about');
        if( packagePrice > 0 ) { //如果专栏价格大于0
            $('#serviceContent').show();
            params['push_state'] = prevVal;
        } else {
            $('#serviceContent').hide();
            delete params['push_state'];
        }
    } else {
        $('#serviceContent').hide();
        delete params['push_state'];
    }
}

function openService() {
    if( $('#serviceContent').data('setting') == 1){
        $('#serviceContent').on('click', ':radio', function(e) {
            var ele = $(this);
            var currVal = +ele.val();

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
                    onOk: function(){
                        $.ajax('/is_industry',{
                            type: 'GET',
                            dataType: 'json',
                            data: {}
                        }).done(function(data) {
                            var code = data.ret;
                            if(code == 0){
                                ele.prop('checked', true);
                                prevVal = currVal;
                                /*消息推送开启*/
                                params['push_state'] = currVal;
                                $('#serviceContent').data('state',currVal);
                                baseUtils.show.blueTip("模板消息推送开启");
                            } else {
                                baseUtils.show.redTip("无法开启消息推送，请按照提示修改设置");
                            }
                        }).fail(function(xhr, text, err) {
                            console.error(err);
                            baseUtils.show.redTip("服务器出差啦，请稍后重试");

                        });
                    }
                }
                $.alert(txt, "custom", option);
                return false;
            } else {
                /*关闭消息推送*/
                prevVal = currVal;
                params['push_state'] = currVal;
                $('#serviceContent').data('state',currVal);
            }
        });
    } else {
        $('#serviceContent').on('click', ':radio', function(e) {
            var ele = $(this);
            var currVal = ele.val();

            if( currVal !== prevVal && currVal == 1 ) {
                prevVal = currVal;
                params['push_state'] = currVal;
                $('#serviceContent').data('state',currVal);
                baseUtils.show.blueTip("模板消息推送开启");
            } else {
                prevVal = currVal;
                params['push_state'] = currVal;
                $('#serviceContent').data('state',currVal);
            }
        });
    }
}

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

//监听价格变化,防止出现负数
function monitorPriceChange() {
    inputLimitPrice("#resource_price");
}

//切换单个和产品包选择
function changeSingleAndPackage() {
    $('.upload_type').on('click', function () {
        $('.upload_type').removeClass('border_blue');
        $("#resource_price").removeAttr('readonly');
        $("#resource_price").val('');
        $(this).addClass('border_blue');
        isShowService();
        if ($('#single_select').hasClass('border_blue')) { //单个
            $('#price_div').removeClass('hide');
            $('.searchSelectArea').addClass('hide');
            $('#resource_package').addClass('hide');

            if(!($('.package_side_pay').hasClass('hide')))
            {
                $('.package_side_pay').addClass('hide');
            }

            pay_type = 2;
        } else if($('#package_select').hasClass('border_blue')){ //产品包
            $('#price_div').addClass('hide');
            $('.searchSelectArea').removeClass('hide');
            $('#resource_package').removeClass('hide');

            if(($('.package_side_pay').hasClass('hide')))
            {
                $('.package_side_pay').removeClass('hide');
            }

            pay_type = 3;
        }else{ //免费
            $('#price_div').addClass('hide');
            $('.searchSelectArea').addClass('hide');
            $('#resource_package').addClass('hide');

            if(!($('.package_side_pay').hasClass('hide')))
            {

                $('.package_side_pay').addClass('hide');
            }


            //设置
            $("#resource_price").attr('readonly','readonly');
            $("#resource_price").val('0.00');
            pay_type = 1;
        }

    });
}

$(function () {
    $('#start_time').on('change', function(){
        setMsgDim('#start_time');
    });

    $("#resource_pic").on("change", function () {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        var fileSize = this.files[0].size;
        if($(this).attr("id")=='resource_pic'&&fileSize>4*1000*1000){
            baseUtils.show.redTip('图片太大!请不要超过4M!');
            return;
        }

        try {
            //获取图片的宽高
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onload = function () {
                    width = this.width;
                    height = this.height;
                };
                img.src = srcs;
            }
        } catch (e) {

        }

        $(this).nextAll(".pic_add").addClass('hide');   //this指的是input
        $(this).nextAll('.pic_close').removeClass('hide');   //this指的是input
        $(this).nextAll(".pic_show").removeClass('hide');
        $(this).nextAll(".pic_show").attr("src", srcs);    //this指的是input
        $(".pic_close").on("click", function () {
            $(this).addClass('hide');     //this指的是span
            $(this).nextAll(".pic_show").addClass('hide');
            $(this).nextAll(".pic_add").removeClass('hide');
            $(this).prevAll('#resource_pic').val('');
        });
    });
    $("#patch_pic").on("change", function () {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        var fileSize = this.files[0].size;
        if($(this).attr("id")=='patch_pic'&&fileSize>4*1000*1000){
            baseUtils.show.redTip('图片太大!请不要超过4M!');
            return;
        }

        try {
            //获取图片的宽高
            var file, img;
            if ((file = this.files[0])) {
                img = new Image();
                img.onload = function () {
                    width = this.width;
                    height = this.height;
                };
                img.src = srcs;
            }
        } catch (e) {

        }

        $(this).nextAll(".pic_add").addClass('hide');   //this指的是input
        $(this).nextAll('.pic_close').removeClass('hide');   //this指的是input
        $(this).nextAll(".pic_show").removeClass('hide');
        $(this).nextAll(".pic_show").attr("src", srcs);    //this指的是input
    });
})

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

//上传资源  resource_type:audio、video
function saveEditResource(resource_type) {
    this.resource_type = resource_type;

    if(!checkForm()){
        return;
    }

    showLoading();

    if(isEditPic){
        //上传图片
        sliceUpload('#resource_pic', get_cos_image_path(), pic_successCallBack, pic_errorCallBack, 'picture');
    }else if(isEditPatchPic){
        //上传图片
        sliceUpload('#patch_pic', get_cos_image_path(), patch_pic_successCallBack, patch_pic_errorCallBack, 'picture');
    }else if(isEditResource){
        //触发视频上传
        // qcVideo.uploader.startUpload();
        videoUpload();
    }else{
        //不需要上传资源
        doUpLoad();
    }

}

//核对表单
function checkForm() {
    //清空之前的数据
    allParams = {};
    //资源名称
    var resource_title = $('#resource_title').val().trim();
    if (resource_title.length != 0) {
        params['title'] = resource_title;
    }

    //上架时间
    var start_time = $('#start_time').val().trim();
    if (start_time.length != 0) {
        params['start_at'] = start_time;
    }

    //资源封面
    var resource_pic = $('#resource_pic').val();
    if(resource_pic.length!=0){
        isEditPic = true;
    }

    //资源封面
    var patch_pic = $('#patch_pic').val();
    if(patch_pic.length!=0){
        isEditPatchPic = true;
    }


    //资源内容
    if (files.length < 1) {
        isEditResource = false;
    }else{
        isEditResource = true;
        params['video_state'] = 1;
        params['is_transcode'] = 0;
    }



    if (pay_type != 3) { //单品
        //价格
        var resource_price = $('#resource_price').val().trim();
        if(pay_type==2){
            if (resource_price.length == 0) {
                baseUtils.show.redTip('价格不能为空!');
                return false;
            }else if(resource_price<=0){
                baseUtils.show.redTip('价格需高于0.00!');
                return false;
            }
        }
        params['piece_price'] = resource_price * 100;
    }else { //产品包
        var resource_package = $('#resource_package').val();
        if (resource_package == undefined || resource_package.length == 0) {
            baseUtils.show.redTip('请选择产品包!');
            return false;
        }
        params['product_id'] = resource_package;
        //获取产品包名字
        var package_name = $("#resource_package").find("option:selected").text().trim();
        var piece_price= $("#resource_package").find("option:selected").attr("about");
        params['product_name'] = package_name;
        params['piece_price'] = piece_price;
        params['product_state'] = $("#resource_package").find("option:selected").attr("state");

        //TODO:是否单卖
        var checkedState = $("#checkbox-img").attr("checked-state");
        if (checkedState == "unchecked") {
            is_single_sale = 0;//不单卖

        } else if (checkedState == "checked") {
            is_single_sale = 1;//单卖
            var reg = /^((0)|([1-9]{1}\d*))(\.\d{1,2})?$/;

            var single_price = $("#single_price").val().trim();

            if(single_price.length>0)
            {
                if(reg.test(single_price))
                {
                    if (single_price == 0 || single_price == 0.0 || single_price == 0.00) {
                        // util.showError('#withdraw_amount-err', '提现金额不能为0！', '#withdraw_amount', true);
                        baseUtils.show.redTip('金额不能为0!');
                        return false;
                    }
                    params['piece_price'] = single_price*100;
                }else{
                    baseUtils.show.redTip( '请输入正确的金额！');
                    return false;
                }
            }else{
                baseUtils.show.redTip('请输入单价!');
                return false;
            }

        }

    }

    var ue = UE.getEditor('resource_desc');
    var html=ue.getContent();
    params['org_content'] = html;

    //资源描述
    var resource_desc = ue.getPlainTxt();
    if (resource_desc.length != 0) {
        params['descrb'] = resource_desc;
    }

    //付费类型
    params['payment_type']= pay_type;

    //资源id
    var resource_id = $('#data').data('id');
    allParams['id']= resource_id;

    allParams['resource_type'] = resource_type;

    allParams['params'] = params;
    allParams['is_single_sale'] = is_single_sale;


    return true;

}


function sliceUpload(identity, remotePath, successCallBack, errorCallBack, file_type) {

    //上传(直接通过js上传cdn)
    var files = $(identity).prop('files');
    if (files && files.length == 1) {
        //if (!cos.hasFlashVersionOrBetter(20, 0)) {
        //    hideLoading();
        //    if (confirm("当前浏览器不支持Flash，无法上传文件，要下载flash吗?")) {
        //        window.open("https://get.adobe.com/cn/flashplayer/", "_blank");
        //        return;
        //    }
        //    return;
        //}
        //if (!$('#qs').length) {
        //    $('body').append('<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="sdk/Somethingtest.swf" style="visibility: visible;"></object>');
        //}


        //获取文件的MD5值
        browserMD5File(files[0], function (err, md5) {
            //获取文件内容的MD5
            console.log('MD5:' + md5);
            //取文件名后缀
            var file_name = files[0]['name'];
            var names = file_name.split('.');
            var suffix = names[names.length - 1];
            console.log('suffix:' + suffix);
            remotePath = remotePath + md5 + "." + suffix;
            console.log('remotePath:' + remotePath);
            cos.uploadFileWithoutPro(successCallBack, errorCallBack, remotePath, files[0], 0);
        });

    }
    else {
        baseUtils.show.redTip("请选择一个文件");
    }
}

//配图成功上传回调
var pic_successCallBack = function (result) {

    //var jsonResult = $.parseJSON(result);
    console.log('图片上传成功!');

    //获取到资源cdn访问连接
    var pic_url = result.data.access_url;
    console.log('图片链接!' + pic_url);

    params['img_url'] = pic_url;
    params['img_url_compressed'] = pic_url;

    if(isEditPatchPic){ //判断是否需要上传贴片
        //上传图片
        sliceUpload('#patch_pic', get_cos_image_path(), patch_pic_successCallBack, patch_pic_errorCallBack, 'picture');
    }else if(isEditResource){
        //启动上传视频
        // qcVideo.uploader.startUpload();
        videoUpload();
    }else{
        doUpLoad();
    }

};

//资源配图失败上传回调
var pic_errorCallBack = function (result) {
    //var jsonResult = $.parseJSON(result);
    hideLoading();
    baseUtils.show.redTip('配图上传失败!请重新上传!');
    uploadErrorShow(result.responseText);
}


//视频贴图成功上传回调
var patch_pic_successCallBack = function (result) {

    //var jsonResult = $.parseJSON(result);
    console.log('图片上传成功!');

    //获取到资源cdn访问连接
    var patch_pic_url = result.data.access_url;
    console.log('贴片图链接!' + patch_pic_url);

    params['patch_img_url'] = patch_pic_url;
    params['patch_img_url_compressed'] = patch_pic_url;

    if(isEditResource){
        //启动上传视频
        // qcVideo.uploader.startUpload();
        videoUpload();
    }else{
        doUpLoad();
    }
};


//视频贴图失败上传回调
var patch_pic_errorCallBack = function (result) {
    //var jsonResult = $.parseJSON(result);
    hideLoading();
    baseUtils.show.redTip('视频贴图上传失败!请重新上传!');
    uploadErrorShow(result.responseText);
}



//上传数据到自己后台
function doUpLoad(){

    $.post('/update_video', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        var re_url = GetQueryString('reurl');
        if (code == 0) {

            baseUtils.show.blueTip(msg, function() {
                if(re_url){
                    window.location.href = re_url;
                }
                else{
                    window.location.href = '/video_list';
                }
            });

        } else {
            baseUtils.show.redTip(msg);
        }
    });
}


//调到新增专栏
function toAddPackage()
{
    $.alert.xcConfirm('您确定要去新增专栏页面吗?','confirm',{onOk:function()
    {
        window.location.href='/package_list';
    }});
}


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

    var isTranscode = 1; //开启转码
    var isWatermark = 1;// 水印

    accountDone('video_file', isTranscode, isWatermark, '');
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
            web_upload_url: 'https://vod.qcloud.com/v2/index.php',
            secretId: secretId, // 云api secretId

            getSignature: function (argStr, done) {
                //注意：出于安全考虑， 服务端接收argStr这个参数后，需要校验其中的Action参数是否为 "MultipartUploadVodFile",用来证明该参数标识上传请求
                $.ajax({
                    'dataType': 'json',
                    'url': '/getsig?args=' + encodeURIComponent(argStr),
                    'success': function (d) {
                        done(d['result']);
                    }
                });
            },
            upBtnId: upBtnId, //上传按钮ID（任意页面元素ID）
            isTranscode: isTranscode,//是否转码
            isWatermark: isWatermark,//是否设置水印
            after_sha_start_upload: false,//sha计算完成后，开始上传 (默认关闭立即上传)
            sha1js_path: '/calculator_worker_sha1.js', //计算sha1的位置
            disable_multi_selection: false, //禁用多选 ，默认为false
            transcodeNotifyUrl: transcodeNotifyUrl,//(转码成功后的回调地址)isTranscode==true,时开启； 回调url的返回数据格式参考  http://www.qcloud.com/wiki/v2/MultipartUploadVodFile
            classId: classId,
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

                //判断上传视频大小
                var videoSize = args.size;
                var videoGsize = videoSize/(1024*1024);
                if(videoGsize>1024){
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

                    console.log("添加了一个新的文件:" + "id:" + args.id + "size:" + args.size);
                }

                if(args.code==2){

                    files[0]['state'] = 2;
                }

                if (args.code == 5 && args.percent > 0) { //上传进度发生变化
                    if($('#progress_bar_'+args.id).is(':hidden')){
                        $('#progress_bar_'+args.id).css('display','inline-block');
                    }
                    $('#bar_' + args.id).css('width', args.percent + "%");
                }
                if (args.code == 6) { //上传成功
                    $('#bar_' + args.id).css('width', "100%");
                    $("#upload_finish_" + args.id).show(); //上传完成

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
                        allParams['resource_params'] = externalParams;
                        doUpLoad();
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
                console.log(msg);
            },

        }
    );


    $('#progress_show').on('click', '[data-act="del"]', function (e) {
        var $line = $(this).parent();
        var fileId = $line.get(0).id;
        Log.debug('delete', fileId);
        $line.remove();
        //@api 删除文件
        qcVideo.uploader.deleteFile(fileId);
        deleteFromFile(fileId); //从文件数组中删除该文件

    });

}


//删除某个元素
function deleteFromFile(id) {
    for (var i = 0; i < files.length; i++) {
        var item = files[i];
        if (id == item['id']) {
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



//对于大文件,计算shar1值比较慢
function uploadServerInterval(externalParams) {
    setTimeout(
        function () {
            if(files.length>0&&files[0]['state']==2){ //如果解析完了的话
                console.log('解析完成');
                uploadToServer(externalParams);
            }else{
                console.log('解析还未完成');
                uploadServerInterval(externalParams);
            }
        },1000
    )
}

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

