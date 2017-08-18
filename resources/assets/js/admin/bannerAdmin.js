/**
 * Created by breeze on 9/29/16.
 */

var isEditPicture = false;

var params = {};
var allParams = {};

//腾讯云资源
//var bucketName = window.cos_bucket_name;
//var cos = new CosCloud(window.cos_app_id);
var cos = new InitCosCloud();

$(document).ready(function () {
    bindElement();
    $.cookie('content_create')? setTopUrlInfo('content_create') : setTopUrlInfo('banner_listop');

    //初始化时间选择器
    datetimepickerconfig('#stop_time');
});

function bindElement() {

    $('#stop_time').on('change', function(){
        setMsgDim('#stop_time');
    });

    $("#type_selector").on('change', function() {
        var type = $(this).find('option:selected').val();

        if (type == 'h5') {
            $('#sub_selector').empty();
            $('#sub_selector').addClass('hide');
            $('#sub_input').removeClass('hide');
            return;
        } else if (type == 'no_jump') {
            $('#sub_selector').empty();
            $('#sub_selector').removeClass('hide');
            $('#sub_input').addClass('hide');
            return;
        } else {
            $('#sub_selector').empty();
            $('#sub_selector').removeClass('hide');
            $('#sub_input').addClass('hide');
        }

        $.get('/banner/getResourceList?type=' + type , function (result) {
            $('#sub_selector').empty();
            result = JSON.parse(result);

            var count = result.length;

            var htmlStr = "";

            for (var i = 0; i < count; i++) {
                htmlStr+="<option value='"+result[i].id+"'>"+result[i].title+"</option>";
            }
            $('#sub_selector').append(htmlStr);
        });
    });
}


function uploadBanner() {
    showLoading();
    if (!checkBannerForm()) {
        hideLoading();
        return;
    }

    //接着上传图片
    sliceUpload('.pic_input', get_cos_image_path(), pic_successCallBack, pic_errorCallBack);
}

function saveBanner() {
    showLoading();
    if (!checkBannerForm()) {
        hideLoading();
        return;
    }

    if (isEditPicture) {
        //接着上传图片
        sliceUpload('.pic_input', get_cos_image_path(), pic_successCallBack, pic_errorCallBack);
    } else {
        doUpLoad();
    }
}

//核对表单
function checkBannerForm() {

    params = {}; //清空之前的数据
    allParams = {};

    //轮播图
    var pic_input = $('.pic_input').val();
    if (pic_input.length != 0) {
        isEditPicture = true;
    } else {
        baseUtils.show.blueTip("请上传图片");
        $('.pic_input').focus();
        return false;
    }

    //图片名称
    var resource_title = $('#resource_title').val().trim();
    if (resource_title.length != 0) {
        params['title'] = resource_title;
    } else {
        baseUtils.show.blueTip('请输入图片名称');
        $('#resource_title').focus();
        return false;
    }

    //上架时间
    var start_time = $('#start_time').val().trim();
    if (start_time.length != 0) {
        params['start_at'] = start_time;
    } else {
        baseUtils.show.blueTip('请设置上架时间');
        $('#start_time').focus();
        return false;
    }
    //下架时间
    var stop_time = $('#stop_time').val().trim();
    if (stop_time.length != 0) {
        if(start_time>stop_time){
            baseUtils.show.blueTip('下架时间不可早于上架时间哦！');
            $('#stop_time').focus();
            return false;
        }
        params['stop_at'] = stop_time;
    } else {
        //baseUtils.show.blueTip('请设置上架时间');
        //$('#stop_time').focus();
        //return false;
    }

    //跳转链接
    params['skip_target'] = $('#sub_selector').find('option:selected').val();
    params['skip_title'] = $('#sub_selector').find('option:selected').text();
    var skip_type = $('#type_selector').find('option:selected').val();
    if (skip_type == "audio") {
        skip_type = 2;
    } else if (skip_type == "video") {
        skip_type = 3;
    } else if (skip_type == "image_text") {
        skip_type = 1;
    } else if (skip_type == "alive") {
        skip_type = 4;
    } else if (skip_type == "package") {
        skip_type = 6;
    }else if (skip_type == "h5") {
        skip_type = 5;
        params['skip_target'] = $('#sub_input').val().trim();
    } else {
        skip_type = 0;
    }
    params['skip_type'] = skip_type;

    if (skip_type == 2 || skip_type == 3 || skip_type == 1 || skip_type == 4 || skip_type ==6) {
        if (params['skip_title'] == "") {
            baseUtils.show.blueTip("请选择跳转资源链接!");
            return false;
        }
    }

    //显示顺序
    var view_order = $('#view_order').find('option:selected').val();
    if (view_order == "first") {
        view_order = 10;
    } else if (view_order == "second") {
        view_order = 9;
    } else if (view_order == "third") {
        view_order = 8;
    } else {
        view_order = 0;
    }
    params['weight'] = view_order;

    allParams['params'] = params;

    //资源id
    allParams['id']= $('#data').data('id');

    return true;

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
            //cos.out_uploadFile(successCallBack, errorCallBack, bucketName, remotePath, files[0], 0);
            cos.uploadFileWithoutPro(successCallBack, errorCallBack, remotePath, files[0], 0);
        });

    }
    else {
        baseUtils.show.redTip("请选择一个文件!");
    }

}

//配图成功上传回调
var pic_successCallBack = function (result) {

    //var jsonResult = $.parseJSON(result);
    console.log('图片上传成功!');

    //获取到资源cdn访问连接
    var pic_url = result.data.access_url;
    console.log('图片链接!' + pic_url);

    params['image_url'] = pic_url;

    allParams['params'] = params;

    doUpLoad();
};

//资源配图失败上传回调
var pic_errorCallBack = function (result) {
    //var jsonResult = $.parseJSON(result);
    hideLoading();
    baseUtils.show.redTip("配图上传失败!请重新上传!");
    uploadErrorShow(result.responseText);
};

//上传数据到自己后台
function doUpLoad(){

    //上传至服务器
    $.post('/upload_banner', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code == 0) {
            baseUtils.show.blueTip("新增成功", function() {
                window.location.href = '/getBannerList';
            });
        } else {
            baseUtils.show.redTip("新增失败");

        }
    });
}
