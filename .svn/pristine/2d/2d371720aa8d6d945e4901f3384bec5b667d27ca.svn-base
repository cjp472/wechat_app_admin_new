var width;
var height;

var params = {};

var category_type;

var resource_type;
//腾讯云资源
//var bucketName = window.cos_bucket_name;
//var cos = new CosCloud(window.cos_app_id);
var cos = new InitCosCloud();

//输入字符限制
var maxlen = 256;
var cnmaxlen = Math.floor(maxlen / 2);

$(document).ready(function () {

    $("#maxletter").text(maxlen);
    $("#maxword").text(cnmaxlen);
    $("#letter").text(maxlen);
    $("#word").text(cnmaxlen);


    $("#resource_title").on("blur keyup", function () {
        setMsgDim(this);
    });

    $("#resource_summary").on("blur keyup", function () {
        setMsgDim(this);
    });

    //  回专栏列表页面
    $(".header_level.left").click(function () {
        window.location.href="/content_create";
    });

});


$(function () {
    $("#resource_summary").keyup(function(){
        var len = $(this).val().replace(/[^\x00-\xff]/g,"**").length;
        if(len > maxlen){
            $(this).val($(this).val().substring(0,maxlen));
        }
        var Ennum = len > maxlen? 0 : maxlen - len;
        var Cnnum = Math.floor(Ennum / 2);
        $("#letter").text(Ennum);$("#word").text(Cnnum);
    });

    //金额输入限制
    inputLimitPrice("#resource_price");

    $(".pic_input").on("change", function () {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        var fileSize = this.files[0].size;

        //判断图片大小
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
            $(this).prevAll('.pic_input').val('');
        });
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


function uploadResource(resource_type) {

    this.resource_type = resource_type;
    showLoading();
    if (!checkForm()) {
        hideLoading();
        return;
    }

    //接着上传图片
    sliceUpload('.pic_input', get_cos_image_path(), pic_successCallBack, pic_errorCallBack);


}

//核对表单
function checkForm() {

    params = {}; //清空之前的数据
    //资源名称
    var resource_title = $('#resource_title').val().trim();
    if (resource_title.length == 0) {
        baseUtils.show.redTip('名称不能为空!');
        setHighLightMsg('#resource_title');
        $('#resource_title').focus();
        return false;
    }
    params['name'] = resource_title;


    // 资源简介
    var resource_summary = $('#resource_summary').val().trim();
    if (resource_summary.length == 0) {
        baseUtils.show.redTip('简介不能为空!');
        setHighLightMsg('#resource_summary');
        $('#resource_summary').focus();
        return false;
    }
    params['summary'] = resource_summary;


    //资源封面
    var pic_input = $('.pic_input').val();
    if (pic_input.length == 0) {
        baseUtils.show.redTip('封面不能为空!');
        //setHighLightMsg('.pic_div');
        return false;
    }


    var ue = UE.getEditor('resource_desc');

    var html=ue.getContent();
    params['org_content'] = html;

    //资源描述
    var resource_desc = ue.getPlainTxt();
    if (resource_desc.length == 0) {
        baseUtils.show.redTip('描述不能为空!');
        ue.focus();
        return false;
    }
    params['descrb'] = resource_desc;


    //有效时间
    var resource_period = $('#resource_period').find('option:selected').val().trim();
    /*if (resource_period.length == 0) {
        baseUtils.show.redTip('请选择有效时间!');
        setHighLightMsg('#resource_period');
        $('#resource_period').focus();
        return false;
    }*/
    params['period'] = resource_period;

    //价格
    var resource_price = $('#resource_price').val().trim();
    if (resource_price.length == 0) {
        resource_price = 0;
    }else{
        resource_price = resource_price*100;
    }

    params['price'] = resource_price;

    /*分类导航 - */
    category_type = new Array();
    var i = 0;
    $(".checkBoxWrapper :checkbox:checked").each(function () {
        var value = $(this).attr("value");
        category_type[i++] = value;
    });

    return true;

}


function sliceUpload(identity, remotePath, successCallBack, errorCallBack) {

    // //上传(直接通过js上传cdn)
    // var files = $(identity).prop('files');
    // if (files && files.length == 1) {
    //
    //     //获取文件的MD5值
    //     browserMD5File(files[0], function (err, md5) {
    //         //获取文件内容的MD5
    //         console.log('MD5:' + md5);
    //         //取文件名后缀
    //         var file_name = files[0]['name'];
    //         var names = file_name.split('.');
    //         var suffix = names[names.length - 1];
    //         console.log('suffix:' + suffix);
    //         remotePath = remotePath + md5 + "." + suffix;
    //         console.log('remotePath:' + remotePath);
    //         cos.uploadFile(successCallBack, errorCallBack, bucketName, remotePath, files[0], 0);
    //     });
    //
    // }
    // else {
    //     alert("请选择一个文件");
    // }

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

    }else {
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

    var allParams = {};
    allParams['resource_type'] = resource_type;
    allParams['params'] = params;
    allParams['category_type'] = category_type;

    //上传至服务器
    $.post('/upload_package', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code == 0) {
            baseUtils.show.blueTip(msg, function() {
                window.location.href='/package_list';
            });
        } else {
            baseUtils.show.redTip(msg);
        }
    });

};

//资源配图失败上传回调
var pic_errorCallBack = function (result) {
    //var jsonResult = $.parseJSON(result);
    hideLoading();
    baseUtils.show.redTip('配图上传失败!请重新上传!');
    uploadErrorShow(result.responseText);
}



