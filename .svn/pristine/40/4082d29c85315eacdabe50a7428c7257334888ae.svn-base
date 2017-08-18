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
// var appid = window.cos_app_id;
// var region = window.cos_region;
// //初始化逻辑
// var cos = new CosCloud({
//     appid: appid,
//     bucket: bucketName,
//     region: region
// });

var ready_pic = false;

var pic_url = '';
var sign_url = '';
var try_url = '';
var noEffect_url = '';

var try_length = '';
var audio_length = '';
var noEffect_length = '';
var noEffect_size = '';

//是否单卖
var is_single_sale = 0;

//SetInterval返回ID

var prevVal;

$(document).ready(function ()
{
    prevVal = +$("#serviceContent :radio:checked").val(); //当前单选框的值
    params['push_state'] = prevVal; //模板消息默认为关闭

    getInternetExplorerVersion();

    //切换单个和产品包选择
    changeSingleAndPackage();

    //初始化时间选择器
    datetimepickerconfig('#start_time');

    //监听输入框价格的变化
    monitorPriceChange();

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
        isShowService();//判断能不能开启消息推送
        if ($('#single_select').hasClass('border_blue')) { //单个
            $('#price_div').removeClass('hide');
            $('.searchSelectArea').addClass('hide');
            $('#resource_package').addClass('hide');

            if(!($('.package_side_pay').hasClass('hide')))
            {
                $('.package_side_pay').addClass('hide');
            }

            pay_type = 2;
        } else if ($('#package_select').hasClass('border_blue')) { //产品包
            $('#price_div').addClass('hide');
            $('.searchSelectArea').removeClass('hide');
            $('#resource_package').removeClass('hide');
            $('.package_side_pay').removeClass('hide');

            pay_type = 3;
        } else { //免费
            $('#price_div').addClass('hide');
            $('.searchSelectArea').addClass('hide');
            $('#resource_package').addClass('hide');
            //$("#resource_price").addClass('hide');
            //设置
            $("#resource_price").attr('readonly', 'readonly');
            $("#resource_price").val('0.00');

            if(!($('.package_side_pay').hasClass('hide')))
            {

                $('.package_side_pay').addClass('hide');
            }
            pay_type = 1;
        }
    });
}

$(function () {
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

        if(!checkPic()){
            return;
        }else{
            //上传封面
            sliceUpload('#resource_pic', get_cos_image_path(), ResourcePic_successCallBack, ResourcePic_errorCallBack, 'picture');
        }
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
    var url = result.data.access_url;
    //确认封面上传成功
    pic_url = url;
    ready_pic = true;
    // console.log('封面音频上传成功的回调-》'+params['img_url']);
}
//封面图上传失败的回调
var ResourcePic_errorCallBack = function (result) {
    baseUtils.show.redTip('封面图上传失败，请重新上传!');
}

function sliceUpload(identity, remotePath, successCallBack, errorCallBack, file_type) {

    //上传(直接通过js上传cdn)
    var files = $(identity).prop('files');
    file_size = files[0]['size'];
    if (files && files.length == 1) {
        // if (!$('#qs').length) {
        //     $('body').append('<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="sdk/Somethingtest.swf" style="visibility: visible;"></object>');
        // }

        //取文件时长
        var resource_local_url = getObjectURL(files[0]);

        //取大小
        if (file_type == 'noEffect'){
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

//进度回调
var progressCallBack = function (curr) {
    console.log('uploading... curr progress is ' + curr)
    //$("#result").val('uploading... curr progress is ' + curr);
};

//上传图文
function uploadArticle() {
    //资源名称
    var resource_title = $('#resource_title').val().trim();
    if (resource_title.length == 0) {
        baseUtils.show.redTip('名称不能为空!');
        return false;
    }

    //上架时间
    var start_time = $('#start_time').val().trim();
    if (start_time.length == 0) {
        baseUtils.show.redTip('上架时间不能为空!');
        return false;
    }

    var resource_price = $('#resource_price').val().trim();

    var resource_package = '';
    var package_name = '';
    var piece_price = '';
    var product_state ='' ;
    //类型金钱
    if (pay_type != 3) {
        if (pay_type == 2) {
            if (resource_price.length == 0) {
                baseUtils.show.redTip('价格不能为空!');
                return false;
            }
            else if (resource_price <= 0) {
                baseUtils.show.redTip('价格需高于0.00!');
                return false;
            }
        }
        params['piece_price'] = $('#resource_price').val().trim() * 100;
    }
    else {
        resource_package = $('#resource_package').find("option:selected").val();
        if (resource_package == undefined || resource_package.length == 0) {
            baseUtils.show.redTip('请选择产品包!');
            return false;
        }
        package_name = $("#resource_package").find("option:selected").text().trim();
        params['piece_price'] = $("#resource_package").find("option:selected").attr("about");
        product_state=$("#resource_package").find("option:selected").attr("state");


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

    //资源封面
    var pic_input = $('#resource_pic').val();
    if (pic_input.length == 0) {
        baseUtils.show.redTip('封面不能为空!');
        return false;
    }

    //获取原生html内容
    var ue = UE.getEditor('container');
    var html = ue.getContent();
    if (html.length == 0) {
        baseUtils.show.redTip('请输入图文内容!');
        return false;
    }

    //获取文本
    var content = ue.getPlainTxt();

    showLoading();
    // console.log(resource_price+">>>"+piece_price);

    //上传(直接通过js上传cdn)
    var files = $('#resource_pic').prop('files');
    //if (!$('#qs').length) {
    //    $('body').append('<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="sdk/Somethingtest.swf" style="visibility: visible;"></object>');
    //}
    //获取文件的MD5值
    browserMD5File(files[0], function (err, md5) {
        //取文件名后缀
        var file_name = files[0]['name'];
        var names = file_name.split('.');
        var suffix = names[names.length - 1];
        var remotePath = get_cos_image_path() + md5 + "." + suffix;
        cos.uploadFileWithoutPro(function (result) //成功回调
        {
            //var jsonResult = $.parseJSON(result);
            var pic_url = result.data.access_url;

            var allParams={};
            params['title'] = resource_title;
            params['start_at'] = start_time;
            params['img_url'] = pic_url;
            params['img_url_compressed'] = pic_url;
            params['payment_type'] = pay_type;
            params['product_id'] = resource_package;
            params['product_name'] = package_name;
            params['product_state'] = product_state;
            params['content'] = content;
            params['org_content'] = html;
            allParams['params'] = params;
            allParams['is_single_sale'] = is_single_sale;


            //插数据
            $.post("/addarticle", allParams, function (data) {
                hideLoading();
                    Is_Save = false;
                    if (data.ret == 0) {
                        baseUtils.show.blueTip("上传成功!", function () {
                            window.location.href = '/article_list'
                        });
                    }
                    else {
                        // console.log('添加到本地服务器失败');
                        baseUtils.show.redTip("上传失败!");
                    }
                });
        }, function (result)     //失败回调
        {
            // console.log('上传到腾讯云失败');
            baseUtils.show.redTip("上传失败!");
            uploadErrorShow(result.responseText);
        }, remotePath, files[0], 0)
    });


}

//调到新增专栏
function toAddPackage() {
    $.alert.xcConfirm('您确定要去新增专栏页面吗?', 'confirm', {
        onOk: function () {
            window.location.href = '/package_list';
        }
    });
}


