var width;
var height;

var params = {};
var allParams = {};

var category_type;

var resource_type;//创建类型

//判断配图是否修改了
var isEditPic = false;

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
    var loadtext = $("#resource_summary");
    var len = $(loadtext).val().replace(/[^\x00-\xff]/g,"**").length;
    if(len > maxlen){
        $(loadtext).val($(loadtext).val().substring(0,maxlen));
    }
    var Ennum = len > maxlen? 0 : maxlen - len;
    var Cnnum = Math.floor(Ennum / 2);
    $("#letter").text(Ennum);$("#word").text(Cnnum);

    $("#resource_title").on("blur keyup", function () {
        setMsgDim(this);
    });

    $("#resource_summary").on("blur keyup", function () {
        setMsgDim(this);
    });

    //  回专栏列表页面
    $(".header_level.left").click(function () {
        window.location.href="/package_list";
    });

});


$(function () {
    $("#resource_summary").keyup(function(){
        var len = $(this).val().replace(/[^\x00-\xff]/g,"**").length;
        var Ennum = len > maxlen? 0 : maxlen - len;
        var Cnnum = Math.floor(Ennum / 2);
        $("#letter").text(Ennum);$("#word").text(Cnnum);
        if(len > maxlen){
            $(this).val($(this).val().substring(0,maxlen));
        }
    });

    $(".pic_input").on("change", function () {
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


//上传资源  resource_type:audio、video
function saveEditResource(resource_type) {
    this.resource_type = resource_type;

    showLoading();

    checkForm();

    if(isEditPic){
        //上传图片
        sliceUpload('.pic_input', get_cos_image_path(), pic_successCallBack, pic_errorCallBack);
    }else{
        //不需要上传资源
        doUpLoad();
    }

}

//核对表单
function checkForm() {
    //清空之前的数据
    allParams = {};
    params = {};

    //资源名称
    var resource_title = $('#resource_title').val().trim();
    if (resource_title.length != 0) {
        params['name'] = resource_title;
    }else{
        baseUtils.show.redTip('名称不能为空!');
        setHighLightMsg('#resource_title');
        $('#resource_title').focus();
        return false;
    }

    // 资源简介
    var resource_summary = $('#resource_summary').val().trim();
    if (resource_summary.length != 0) {
        params['summary'] = resource_summary;
    }else{
        baseUtils.show.redTip('简介不能为空!');
        setHighLightMsg('#resource_summary');
        $('#resource_summary').focus();
        return false;
    }


    //资源封面
    var pic_input = $('.pic_input').val();
    if(pic_input.length!=0){
        isEditPic = true;
    }

    //有效时长
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
    }
    params['price'] = resource_price*100;


    var ue = UE.getEditor('resource_desc');

    var html=ue.getContent();
    params['org_content'] = html;

    //资源描述
    var resource_desc = ue.getPlainTxt();
    if (resource_desc.length == 0) {
        baseUtils.show.redTip('描述不能为空!');
        return false;
    }
    params['descrb'] = resource_desc;


    /*分类导航 - */
    category_type = [];

    var ii = 0;
    $(".checkBoxWrapper :checkbox:checked").each(function () {
        var value = $(this).attr("value");
        category_type[ii++] = value;
        // alert($(this).next().next().text());
    });


    //资源id
    var resource_id = $('#data').data('id');
    allParams['id']= resource_id;

    allParams['resource_type'] = resource_type;

    allParams['params'] = params;

    allParams['category_type'] = category_type;

}


function sliceUpload(identity, remotePath, successCallBack, errorCallBack) {

    //上传(直接通过js上传cdn)
    var files = $(identity).prop('files');
    if (files && files.length == 1) {

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

    doUpLoad();

};

//资源配图失败上传回调
var pic_errorCallBack = function (result) {
    //var jsonResult = $.parseJSON(result);
    hideLoading();
    baseUtils.show.redTip('配图上传失败!请重新上传!');
    uploadErrorShow(result.responseText);
}


//audio加载完毕回调,获取音频时长
function getResourceDuration(element) {
    console.log('时长' + element.duration);
    if(resource_type=='audio'){
        params['audio_length'] = element.duration;
    }else{
        params['video_length'] = element.duration;
    }

}


//上传数据到自己后台
function doUpLoad(){
    $.post('/edit_package_save', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code == 0) {
            baseUtils.show.blueTip(msg, function() {
                window.location.href = '/package_list';
            });
        } else {
            baseUtils.show.redTip(msg);
        }
    });
}


