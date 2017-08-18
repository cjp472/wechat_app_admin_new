/**
 * Created by breeze on 9/29/16.
 */

var search_content;
var resource_attr;
var search_status;

var isEditPicture = false;

var params = {};
var allParams = {};

$(document).ready(function () {
    //bindElement();
    keyEnter($('#resource_search_btn'));
    $.cookie('content_create','');
    setTopUrlCookie('banner_listop','轮播图列表');
    reBack();
});

function searchBanner() {
    //取选择字段和内容
    search_status = $('#search_status').val();
    resource_attr = $('#resource_attr').val();
    search_content = $('#resource_search_content').val().trim();

    window.location = "/getBannerList?&resource_attr=" + encodeURI(resource_attr)
        + "&search_content=" + encodeURI(search_content)
        + "&search_status=" + encodeURI(search_status);
}

//上传数据到自己后台
function doUpLoad() {
//writeObj(allParams.params);
    //上传至服务器
    $.post('/save_edit_banner', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code == 0) {
            baseUtils.show.blueTip("更新成功!");//alert(code);
            window.location.reload();
        } else {
            baseUtils.show.redTip('更新失败!');
            if(code ==1){
                window.wxc.xcConfirm(msg+',请重新编辑！', 'error', {btn: parseInt("0001",2),onOk:function(){}});
            }
        }
    });
}

function updateBannerState(new_state, id) {
    params = {};
    allParams = {};

    params['state_offline'] = new_state;
    allParams['id'] = id;
    allParams['params'] = params;
    //params['state_change'] = 1;

    doUpLoad();
}


//回显搜索框内的值
function reBack() {

    if(search_content!=undefined){ //专栏会出现这种情况~
        var resource_search_content = document.getElementById("resource_search_content");

        if (search_content != "") {
            resource_search_content.value = search_content;
            var resource_attr_element = document.getElementById("resource_attr");
            if (resource_attr_element.length > 0) {
                for (var i = 0; i < resource_attr_element.options.length; i++) {
                    if (resource_attr_element.options[i].value == resource_attr) {
                        resource_attr_element.options[i].selected = true;
                        break;
                    }
                }
            }
        }
    }

    var search_status_element = document.getElementById('search_status');
    if (search_status_element.length > 0) {
        for (var i = 0; i < search_status_element.options.length; i++) {
            if (search_status_element.options[i].value == search_status) {
                search_status_element.options[i].selected = true;
                break;
            }
        }
    }

}

//获取当前页完整url用于回跳
function setreUrl(objUrl){
    //获取当前页url
    var reurl = encodeURIComponent(window.location.href);
    //转向目标地址
    window.location.href = objUrl + '&reurl=' + reurl;
}