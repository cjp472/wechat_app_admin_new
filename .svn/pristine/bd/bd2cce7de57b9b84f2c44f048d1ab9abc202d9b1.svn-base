var width;
var height;

var params = {};

var pay_type = 2; //付费类型,1表示免费、2表示单个付费,3表示专栏。

var resource_type;//创建类型

var file_size;  //上传的文件大小

//腾讯云资源
//var bucketName = window.cos_bucket_name;
//var cos = new CosCloud(window.cos_app_id);
var cos = new InitCosCloud();

//新音频上传逻辑
//var SignAndTry = false;

var ready_pic = false;
var ready_sign = false;
var ready_try = false;
var ready_noEffect = false;
var ready_audio = false;

var pic_url = '';
var sign_url = '';
var audio_url = '';
var try_url = '';
var noEffect_url = '';

var try_length = '';
var audio_length = '';
var audio_size = '';
var noEffect_length = '';
var noEffect_size = '';

//SetInterval返回ID
var SID; //音频上传定时器
var TID; //试听上传定时器
var NID; //无音效上传定时器

//是否单卖
var is_single_sale = 0;

var prevVal;

$(document).ready(function () {
    prevVal = +$("#serviceContent :radio:checked").val(); //当前单选框的值
    params['push_state'] = prevVal; //模板消息默认为关闭
    //替换选择文件样式框
    $(".nicefile").niceFileInput({
        'width': '500', //width of button - minimum 150
        'height': '34',  //height of text
        'btnText': '选择文件', //text of the button
        'btnWidth': '82',  // width of button
        'margin': '20',	// gap between textbox and button - minimum 14
        'left': '0'
    });

    //checkbox"专栏外单卖"
    $("#checkbox-img").click(function () {

        var checkedState = $("#checkbox-img").attr("checked-state");
        if (checkedState == "unchecked") {
            $("#checkbox-img").attr("checked-state", "checked");
            $("#checkbox-img").css("backgroundPosition", "-104px 0");
            $("#single_price_div").removeClass('hide');

        } else if (checkedState == "checked") {
            $("#checkbox-img").attr("checked-state", "unchecked");
            $("#checkbox-img").css("backgroundPosition", "-78px 0");
            $("#single_price_div").addClass('hide');

        }
    });

    //选择文件 -- 取消操作
    $('.btn_cancel').click(function () {
        $('.name-progress').css('display', 'none');
    });

    $('#resource_title').on('blur keyup change', function () {
        setMsgDim(this);
    });
    $('#resource_price').on('blur keyup', function () {
        setMsgDim(this);
    });
    $('#free_select').on('click', function () {
        $(this).parents('.upload_input_div').find('.upload_input_msg').css('color', '');
    });
    $('#package_select').on('click', function () {
        $(this).parents('.upload_input_div').find('.upload_input_msg').css('color', '');
    });


    getInternetExplorerVersion();

    //切换单个和产品包选择
    changeSingleAndPackage();

    //初始化时间选择器
    datetimepickerconfig('#start_time');

    //监听输入框价格的变化
    monitorPriceChange();


    isShowService(); //判断是否显示消息推送按钮
    $('#resource_package').on('change', function(e) {
        isShowService();
    });
    //初始化服务推送单选框
    openService();




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
    if( $('#serviceContent').data('setting') == 1 ){
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
                params['push_state'] = currVal;
                $('#serviceContent').data('state',currVal);
                prevVal = currVal;
            }
        });
    } else {
        $('#serviceContent').on('click', ':radio', function(e) {
            var ele = $(this);
            var currVal = +ele.val();

            if( currVal !== prevVal && currVal == 1 ) {
                params['push_state'] = currVal;
                $('#serviceContent').data('state',currVal);
                prevVal = currVal;
                baseUtils.show.blueTip("模板消息推送开启");
            } else {
                params['push_state'] = currVal;
                $('#serviceContent').data('state',currVal);
                prevVal = currVal;
            }
        });
    }
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
        isShowService(); //判断能不能开启消息推送
        if ($('#single_select').hasClass('border_blue')) { //单个
            $('#price_div').removeClass('hide');
            $('.searchSelectArea').addClass('hide');
            $('#resource_package').addClass('hide');

            if (!($('.package_side_pay').hasClass('hide'))) {

                $('.package_side_pay').addClass('hide');
            }

            pay_type = 2;
        } else if ($('#package_select').hasClass('border_blue')) { //产品包
            $('#price_div').addClass('hide');
            $('.searchSelectArea').removeClass('hide');
            $('.package_side_pay').removeClass('hide');
            $('#resource_package').removeClass('hide');

            pay_type = 3;
        } else { //免费
            $('#price_div').addClass('hide');
            $('.searchSelectArea').addClass('hide');
            $('#resource_package').addClass('hide');

            if (!($('.package_side_pay').hasClass('hide'))) {

                $('.package_side_pay').addClass('hide');
            }


            //$("#resource_price").addClass('hide');
            //设置
            $("#resource_price").attr('readonly', 'readonly');
            $("#resource_price").val('0.00');
            pay_type = 1;
        }
    });
}

$(function () {
    $('#start_time').on('change', function () {
        setMsgDim('#start_time');
    });

    //上传封面操作
    $("#resource_pic").on("change", function () {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        var fileSize = this.files[0].size;

        //判断图片大小
        if ($(this).attr("id") == 'resource_pic' && fileSize > 4 * 1000 * 1000) {
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
        $('#icon_uploadPic').removeClass('hide');   //上传封面的标志

        $(this).nextAll(".pic_show").removeClass('hide');
        $(this).nextAll(".pic_show").attr("src", srcs);    //this指的是input
        $(".pic_close").on("click", function () {
            $(this).addClass('hide');     //this指的是span
            $('#icon_uploadPic').addClass('hide');   //上传封面的标志
            $('#icon_uploadPic_success').addClass('hide');
            $(this).nextAll(".pic_show").addClass('hide');
            $(this).nextAll(".pic_add").removeClass('hide');
            $(this).prevAll('#resource_pic').val('');
        });

        if (!checkPic()) {
            return;
        } else {
            //上传封面
            sliceUpload('#resource_pic', get_cos_image_path(), ResourcePic_successCallBack, ResourcePic_errorCallBack, 'picture');
        }
    });

    //上传日签操作
    $("#sign_pic").on("change", function () {
        //SignAndTry = true;
        var srcs = getObjectURL(this.files[0]);
        var fileSize = this.files[0].size;

        //判断图片大小
        if ($(this).attr("id") == 'sign_pic' && fileSize > 4 * 1000 * 1000) {
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

        $(this).nextAll(".sign_add").addClass('hide');   //this指的是input
        $(this).nextAll('.sign_close').removeClass('hide');   //this指的是input
        $('#icon_uploadSign').removeClass('hide');      //日签上传成功标志
        $(this).nextAll(".sign_show").removeClass('hide');
        $(this).nextAll(".sign_show").attr("src", srcs);    //this指的是input
        $(".sign_close").on("click", function () {
            $(this).addClass('hide');     //this指的是span
            $('#icon_uploadSign').addClass('hide');     //日签上传成功标志
            $('#icon_uploadSign_success').addClass('hide');
            $(this).nextAll(".sign_show").addClass('hide');
            $(this).nextAll(".sign_add").removeClass('hide');
            $(this).prevAll('#sign_pic').val('');
        });
        //开始上传日签
        if (!checkSign()) {
            return;
        } else {
            //上传日签
            sliceUpload('#sign_pic', get_cos_image_path(), SignPic_successCallBack, SignPic_errorCallBack, 'picture');
        }
    });

    //上传音频操作
    $('.nicefile').on("change", function () {
        var textPath = $(this).val().replace("C:\\fakepath\\", "");

        $('.name-progress').css({'border': '1px solid #ccc', 'display': 'block'});
        $('.name-progress').removeClass('hide');
        $(".fileInputText").css('width', '40%');
        $('.progress').css('display', 'inline-block');
        $('.progress-state').css('display', 'inline-block');

        $(this).closest('.fileWrapper').find(".fileInputText").val(textPath);

        if (!checkAudio()) {
            return;
        } else {

            //上传音频
            //设置定时器更新进度条
            //清空进度条
            $('#progress_audio').attr('aria-valuenow', '0');
            $('#progress_audio').css('width', '0%');

            SID = setInterval(function () {
                $(".progress-state").text("上传中");
                $('#progress_audio').addClass('active');
                var pron = parseInt($('#progress_audio').attr('aria-valuenow')) + num;
                $(".upPersent").text(pron+'%');
                if (parseInt($('#progress_audio').attr('aria-valuenow')) < 90) {
                    // var num = Math.floor(Math.random() * Poffset + 1);
                    var num = 2;
                    var pron = parseInt($('#progress_audio').attr('aria-valuenow')) + num;
                    $('#progress_audio').attr('aria-valuenow', '' + pron);
                    $('#progress_audio').css('width', pron + '%');
                } else {
                    //.progress-bar-striped
                    $('#progress_audio').attr('aria-valuenow', '100');
                    clearInterval(SID);
                }
            }, 1000);

            sliceUpload('.nicefile', get_cos_audio_path(), Audio_successCallBack, Audio_errorCallBack, 'audio');
        }

        //取消音频
        $('.btn_cancel').click(function () {
            $('#public_audio').val('');
        });
    });

    //上传试听操作
    $('#public_try').on('change', function () {
        //SignAndTry = true;
        $('.p_tryAudio').css('display', 'inline-block');

        var name = $('#public_try').prop('files')[0]['name'];
        $('.audio_tryUrl').removeClass('hide');
        $('.tyr_Url_tryAudio').val(name);

        if (!checkTryAudio()) {
            return;
        } else {
            //上传试听
            //设置定时器更新进度条
            //清空进度条
            $('#progress_try').attr('aria-valuenow', '0');
            $('#progress_try').css('width', '0%');

            TID = setInterval(function () {
                $(".btn_cancelTry").text("上传中");
                $('#progress_try').addClass('active');
                if (parseInt($('#progress_try').attr('aria-valuenow')) < 90) {
                    var num = 2;
                    var pron = parseInt($('#progress_try').attr('aria-valuenow')) + num;
                    $('#progress_try').attr('aria-valuenow', '' + pron);
                    $('#progress_try').css('width', pron + '%');
                } else {
                    $('#progress_try').attr('aria-valuenow', '100');
                    clearInterval(TID);
                }
            }, 1000);
            sliceUpload('#public_try', get_cos_audio_path(), Try_successCallBack, Try_errorCallBack, 'try');
        }

        //取消试听
        // $('.btn_cancelTry').click(function () {
        //     $('.audio_tryUrl').addClass('hide');
        //     $('#public_try').val('');
        // });
    });

    //上传音频（无音效）操作
    $('#public_noEffect').on('change', function () {
        //SignAndTry = true;
        $('.p_noEffect').css('display', 'inline-block');

        var name = $('#public_noEffect').prop('files')[0]['name'];
        $('.audio_noEffectUrl').removeClass('hide');
        $('.tyr_Url_noEffect').val(name);

        if (!checkNoEffectAudio()) {
            return;
        } else {
            //上传试听
            //设置定时器更新进度条
            //清空进度条
            $('#progress_noEffect').attr('aria-valuenow', '0');
            $('#progress_noEffect').css('width', '0%');

            NID = setInterval(function () {
                $(".btn_cancelNoEffect").text("上传中");
                $('#progress_noEffect').addClass('active');
                if (parseInt($('#progress_noEffect').attr('aria-valuenow')) < 90) {
                    var num = 2;
                    var pron = parseInt($('#progress_noEffect').attr('aria-valuenow')) + num;
                    $('#progress_noEffect').attr('aria-valuenow', '' + pron);
                    $('#progress_noEffect').css('width', pron + '%');
                } else {
                    $('#progress_noEffect').attr('aria-valuenow', '100');
                    clearInterval(NID);
                }
            }, 1000);
            sliceUpload('#public_noEffect', get_cos_audio_path(), NoEffect_successCallBack, NoEffect_errorCallBack, 'noEffect');
        }

        //取消试听
        // $('.btn_cancelNoEffect').click(function () {
        //     $('.audio_noEffectUrl').addClass('hide');
        //     $('#public_noEffect').val('');
        // });
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

//上传资源  resource_type:audio
function uploadResource(resource_type) {
    this.resource_type = resource_type;

    if (!checkForm()) {
        return;
    }
    //上传音频
    if (!checkAudio()) {
        return;
    } else {
        showLoading();
        params['audio_url'] = audio_url;
        params['audio_compress_url'] = audio_url;
        params['audio_length'] = audio_length;
        params['audio_size'] = audio_size;
        params['img_url'] = pic_url;
        params['img_url_compressed'] = pic_url;
        params['sign_url'] = sign_url;
        params['sign_url_compressed'] = sign_url;
        params['try_audio_url'] = try_url;
        params['try_m3u8_url'] = try_url;
        params['try_audio_length'] = try_length;

        //无音效音频
        var noEffectParams = {};
        noEffectParams['audio_url'] = noEffect_url;
        noEffectParams['audio_compress_url'] = noEffect_url;
        noEffectParams['audio_size'] = noEffect_size;
        noEffectParams['audio_length'] = noEffect_length;
        noEffectParams['try_audio_url'] = try_url;
        noEffectParams['try_m3u8_url'] = try_url;
        noEffectParams['try_audio_length'] = try_length;

        var allParams = {};
        allParams['resource_type'] = resource_type;
        allParams['params'] = params;
        allParams['noEffectAudio'] = noEffectParams;
        allParams['is_single_sale'] = is_single_sale;
        console.log(allParams);

        //

        // 上传至服务器
        $.post('/upload_resource', allParams, function (result) {
            //hideLoading();
            var code = result.code;
            var msg = result.msg;
            if (code == 0) {
                baseUtils.show.blueTip(msg, function () {
                    changeSaveFlag(false);
                    if (allParams['resource_type'] == 'audio') {
                        window.location.href = '/audio_list';
                    } else {
                        window.location.href = '/audio_list';
                    }
                });
            } else {
                baseUtils.show.redTip(msg);
                hideLoading();
            }
        });

    }
}

//核对封面图
function checkPic() {
    //资源封面
    var pic_input = $('#resource_pic').val();
    if (pic_input.length == 0) {
        baseUtils.show.redTip('封面不能为空!');
        return false;
    } else {
        var prop_pic = $('#resource_pic').prop('files');
        var pic_type = prop_pic[0].type;
        if (pic_type.indexOf('image') < 0) { // 'image/jpeg'
            baseUtils.show.redTip('封面必须为图片文件!');
            return false;
        }
    }
    return true;
}
//封面图上传成功的回调
var ResourcePic_successCallBack = function (result) {
    $('#icon_uploadPic').addClass('hide');
    $('#icon_uploadPic_success').removeClass('hide');
    //var jsonResult = $.parseJSON(result);
    //获取到资源cdn访问连接
    var picurl = result.data.access_url;
    //确认封面上传成功
    pic_url = picurl;
    ready_pic = true;
    // console.log('封面音频上传成功的回调-》'+params['img_url']);
}
//封面图上传失败的回调
var ResourcePic_errorCallBack = function (result) {
    baseUtils.show.redTip('封面图上传失败，请重新上传!');
}

//核对日签图
function checkSign() {
    //日签
    if ($('#sign_pic').length > 0) {
        var prop_sign = $('#sign_pic').prop('files');
        if (prop_sign.length > 0) {
            var sign_type = prop_sign[0].type;
            if (sign_type.indexOf('image') < 0) { // 'image/jpeg'
                baseUtils.show.redTip('日签必须为图片文件!');
                return false;
            }
        }
        return true;
    }
}
//日签上传成功的回调
var SignPic_successCallBack = function (result) {
    $('#icon_uploadSign').addClass('hide');
    $('#icon_uploadSign_success').removeClass('hide');

    //var jsonResult = $.parseJSON(result);
    //获取到资源cdn访问连接
    var url = result.data.access_url;

    //确认日签上传成功
    ready_sign = true;
    sign_url = url;
    // console.log('日签上传成功的回调-》'+params['sign_url']);
}
//日签上传失败的回调
var SignPic_errorCallBack = function (result) {
    baseUtils.show.redTip('日签上传失败，请重新上传!');
}

//核对无音效
function checkNoEffectAudio() {
    //试听内容
    var resource_input = $('#public_noEffect').val();
    if (resource_input.length == 0) {
        baseUtils.show.redTip('音频（无音效）不能为空!');
        return false;
    } else {
        var resource_file = $('#public_noEffect').prop('files');
        var file_type = resource_file[0].type;
        if (file_type.indexOf('audio') < 0) {
            baseUtils.show.redTip('试听必须为音频文件!');
            $('.audio_noEffectUrl').addClass('hide');
            $('#public_noEffect').val('');
            return false;
        }
    }
    return true;
}
//无音效上传成功的回调
var NoEffect_successCallBack = function (result) {

    //进度条填充满
    clearInterval(NID);
    $(".btn_cancelNoEffect").text("已完成");
    $('#progress_noEffect').removeClass('active');
    $('#progress_noEffect').attr('aria-valuenow', '100');
    $('#progress_noEffect').css('width', '100%');

    //var jsonResult = $.parseJSON(result);
    //获取到资源cdn访问连接
    var resource_url = result.data.access_url;

    //确定音频上传成功
    ready_noEffect = true;
    noEffect_url = resource_url;

    console.log('无音效上传成功的回调-》' + noEffect_url);
}
//无音效上传失败的回调
var NoEffect_errorCallBack = function (result) {
    baseUtils.show.redTip('音频(无音效)上传失败，请重新上传!');
}

//核对试听
function checkTryAudio() {
    //试听内容
    var resource_input = $('#public_try').val();
    if (resource_input.length == 0) {
        baseUtils.show.redTip('试听不能为空!');
        return false;
    } else {
        var resource_file = $('#public_try').prop('files');
        var file_type = resource_file[0].type;
        if (file_type.indexOf('audio') < 0) {
            baseUtils.show.redTip('试听必须为音频文件!');
            $('.audio_tryUrl').addClass('hide');
            $('#public_try').val('');
            return false;
        }
    }
    return true;
}
//试听上传成功的回调
var Try_successCallBack = function (result) {

    //进度条填充满
    clearInterval(TID);
    $(".btn_cancelTry").text("已完成");
    $('#progress_try').removeClass('active');
    $('#progress_try').attr('aria-valuenow', '100');
    $('#progress_try').css('width', '100%');
    //var jsonResult = $.parseJSON(result);
    //获取到资源cdn访问连接
    var try_resource_url = result.data.access_url;

    //确定音频上传成功
    ready_try = true;
    try_url = try_resource_url;

    // console.log('试听上传成功的回调-》'+params['try_audio_url']);
}
//试听上传失败的回调
var Try_errorCallBack = function (result) {
    baseUtils.show.redTip('试听上传失败，请重新上传!');
}

//核对音频
function checkAudio() {
    //资源内容
    var resource_input = $('.nicefile').val();
    if (resource_input.length == 0) {
        baseUtils.show.redTip('音频不能为空!');
        return false;
    } else {
        var resource_file = $('.nicefile').prop('files');
        var file_type = resource_file[0].type;
        if (file_type.indexOf('audio') < 0) {
            baseUtils.show.redTip('上传必须为音频文件!');
            $('.name-progress').addClass('hide');
            $('#public_audio').val('');
            return false;
        }
    }
    return true;
}
//音频上传成功的回调
var Audio_successCallBack = function (result) {

    //进度条填充满
    clearInterval(SID);
    $(".progress-state").text("已完成");
    $('#progress_audio').removeClass('active');
    $('#progress_audio').attr('aria-valuenow', '100');
    $('#progress_audio').css('width', '100%');
    console.log("音频上传成功！");
    //var jsonResult = $.parseJSON(result);
    //获取到资源cdn访问连接
    var resource_url = result.data.access_url;

    //确定音频上传成功
    ready_audio = true;
    audio_url = resource_url;

}
//音频上传失败的回调
var Audio_errorCallBack = function (result) {
    baseUtils.show.redTip('音频上传失败，请重新上传!');
}

//核对表单
function checkForm() {

    //params = {}; //清空之前的数据
    //资源名称
    var resource_title = $.trim($('#resource_title').val());
    if (resource_title.length == 0) {
        baseUtils.show.redTip('名称不能为空!');
        setHighLightMsg('#resource_title');
        $('#resource_title').focus();
        return false;
    }
    params['title'] = resource_title;

    //上架时间
    var start_time = $.trim($('#start_time').val());
    if (start_time.length == 0) {
        baseUtils.show.redTip('上架时间不能为空!');
        setHighLightMsg('#start_time');
        $('#start_time').focus();
        return false;
    }
    params['start_at'] = start_time;

    if (pay_type != 3) { //不是产品包
        //价格
        var resource_price = $.trim($('#resource_price').val());
        if (pay_type == 2) {
            if (resource_price.length == 0) {
                baseUtils.show.redTip('价格不能为空!');
                setHighLightMsg('#resource_price');
                $('#resource_price').focus();
                return false;
            } else if (resource_price <= 0) {
                baseUtils.show.redTip('价格需高于0.00!');
                $('#resource_price').focus();
                return false;
            }
        }
        params['piece_price'] = resource_price * 100;
    } else { //产品包
        var resource_package = $('#resource_package').find("option:selected").val();
        if (resource_package == undefined || resource_package.length == 0) {
            baseUtils.show.redTip('请选择产品包!');
            return false;
        }


        params['product_id'] = resource_package;
        //获取产品包名字
        var package_name = $.trim($("#resource_package").find("option:selected").text());
        params['product_name'] = package_name;
        params['piece_price'] = $("#resource_package").find("option:selected").attr("about");
        params['product_state'] = $("#resource_package").find("option:selected").attr("state");


        //TODO:是否单卖
        var checkedState = $("#checkbox-img").attr("checked-state");
        if (checkedState == "unchecked") {
            is_single_sale = 0;//不单卖

        } else if (checkedState == "checked") {
            is_single_sale = 1;//单卖
            var reg = /^((0)|([1-9]{1}\d*))(\.\d{1,2})?$/;

            var single_price = $.trim($("#single_price").val());

            if (single_price.length > 0) {
                if (reg.test(single_price)) {
                    if (single_price == 0 || single_price == 0.0 || single_price == 0.00) {
                        // util.showError('#withdraw_amount-err', '提现金额不能为0！', '#withdraw_amount', true);
                        baseUtils.show.redTip('金额不能为0!');
                        return false;
                    }
                    params['piece_price'] = single_price * 100;
                } else {
                    baseUtils.show.redTip('请输入正确的金额！');
                    return false;
                }
            } else {
                baseUtils.show.redTip('请输入单价!');
                return false;
            }

        }
    }

    //封面图片
    if (!checkPic()) {
        $('#resource_pic').focus();
        return false;
    }
    //音频文件
    if (!checkAudio()) {
        $('#public_audio').focus();
        return false;
    }

    // if(SignAndTry){
    //     //具备试听和日签功能
    //     //封面 日签 试听 全部上传完毕
    //     if(!ready_sign){
    //         baseUtils.show.redTip('请等待日签上传完成!');
    //         return false;
    //     }
    //     if(!ready_try){
    //         baseUtils.show.redTip('请等待试听音频上传完成!');
    //         return false;
    //     }
    // }

    //不具备试听和日签功能
    if (!ready_pic) {
        baseUtils.show.redTip('请等待封面上传完成!');
        return false;
    }
    if (!ready_audio) {
        baseUtils.show.redTip('请等待音频上传成功!');
        return false;
    }

    var ue = UE.getEditor('resource_desc');

    var html = ue.getContent();
    params['org_content'] = html;

    //资源描述
    var resource_desc = ue.getPlainTxt();
    if (resource_desc.length == 0) {
        baseUtils.show.redTip('描述不能为空!');
        return false;
    }
    params['descrb'] = resource_desc;

    //付费类型
    params['payment_type'] = pay_type;

    return true;

}

function sliceUpload(identity, remotePath, successCallBack, errorCallBack, file_type) {

    //上传(直接通过js上传cdn)
    var files = $(identity).prop('files');
    file_size = files[0]['size'];
    if (files && files.length == 1) {
        //if (!$('#qs').length) {
        //    $('body').append('<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="sdk/Somethingtest.swf" style="visibility: visible;"></object>');
        //}

        //取文件时长
        var resource_local_url = getObjectURL(files[0]);

        //取音视频大小
        if (file_type == 'audio') {
            audio_size = (files[0]['size'] / (1024 * 1024)).toFixed(1);
            var resource_element = document.getElementById("resource_time");
            resource_element.src = resource_local_url;
        } else if (file_type == 'try') {
            //取试听时长
            var resource_element = document.getElementById("try_time");
            resource_element.src = resource_local_url;
        } else if (file_type == 'noEffect') {
            noEffect_size = (files[0]['size'] / (1024 * 1024)).toFixed(1);
            var resource_element = document.getElementById("noEffect_time");
            resource_element.src = resource_local_url;
        }

        //获取文件的MD5值
        browserMD5File(files[0], function (err, md5) {
            //获取文件内容的MD5
            // console.log('MD5:' + md5);
            //取文件名后缀
            var file_name = files[0]['name'];
            var names = file_name.split('.');
            var suffix = names[names.length - 1];
            // console.log('suffix:' + suffix);
            remotePath = remotePath + md5 + "." + suffix;
            // console.log('remotePath:' + remotePath);

            cos.uploadFileWithoutPro(successCallBack, errorCallBack, remotePath, files[0], 0);
        });
    }
    else {
        baseUtils.show.redTip("请选择一个文件");
    }
}

//audio加载完毕回调,获取音频时长
function getResourceDuration(element) {
    audio_length = element.duration;
    console.log(audio_length);
}

//无音效加载完毕回调，获取音频时长
function getNoEffectDuration(element) {
    noEffect_length = element.duration;
    console.log('无音效时长' + noEffect_length);
}

//试听 加载完毕回调，获取试听时长
function getTryDuration(element) {
    try_length = element.duration;
}


//调到新增专栏
function toAddPackage() {
    $.alert.xcConfirm('您确定要去新增专栏页面吗?', 'confirm', {
        onOk: function () {
            window.location.href = '/package_list';
        }
    });
}


