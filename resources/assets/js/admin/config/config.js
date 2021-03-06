/**
 * Created by breeze on 9/29/16.
 */

/**腾讯云对象存储cos的id*/
//window.cos_app_id = '10011692';
//window.cos_bucket_name = 'wechatappdev';
// region,//地域信息 必填参数 华南地区填gz 华东填sh 华北填tj
// window.cos_region = 'sh';

function get_cos_image_path()
{
    var app_id=$("#xcx_app_id").val();
    return '/'+app_id+'/image/';
}

function get_cos_audio_path()
{
    var app_id=$("#xcx_app_id").val();
    return '/'+app_id+'/audio/';
}

function get_cos_video_path()
{
    var app_id=$("#xcx_app_id").val();
    return '/'+app_id+'/video/';
}


/**生成二维码的配置*/
/*正式环境*/
/*
qrcode_app_id='wxd3ab22331c3b1470';
new_redirect_url = 'https://admin.xiaoe-tech.com/admin/changeWxAccount';
qrcode_redirect_url='https://admin.xiaoe-tech.com/codeinfo';
qrcode_href='https://admin.xiaoe-tech.com/css/admin/wechatCode.css';
qrcode_href_new='https://admin.xiaoe-tech.com/css/admin/wechatCodeNew.css';
homepage='https://www.xiaoe-tech.com/';
aliveTransUrl= 'https://admin.xiaoe-tech.com/alivetranscode';
secretId='AKIDHUZgdQnZt34thKv5INITXRDqKdzCfWn0';
authUrl='https://app.xiaoe-tech.com/platform/request_auth/';
miniauthUrl='https://app.xiaoe-tech.com/platform/request_auth/';
miniPerson = 'https://app.xiaoe-tech.com/wxa/get_collection_qr_code?app_id=';
miniexperUrl = 'https://app.xiaoe-tech.com/wxa/get_qr_code?app_id='
miniappUrl = 'https://app.xiaoe-tech.com/wxa/get_app_code?app_id='

*/

/*测试环境*/
qrcode_app_id='wxd321e17e386ddbca';
new_redirect_url = 'https://admin.inside.xiaoe-tech.com/admin/changeWxAccount';
qrcode_redirect_url='https://admin.inside.xiaoe-tech.com/codeinfo';
qrcode_href='https://admin.inside.xiaoe-tech.com/css/admin/wechatCode.css';
qrcode_href_new='https://admin.xiaoe-tech.com/css/admin/wechatCodeNew.css';
homepage='http://119.29.7.130:7088/';
aliveTransUrl= 'https://admin.inside.xiaoe-tech.com/alivetranscode';
secretId = 'AKIDTdzn2PhgeuKvr2ue4dSbMHWSDMjK8d7v';
authUrl='https://app.inside.xiaoe-tech.com/platform/request_auth/';

miniauthUrl='https://app.inside.xiaoe-tech.com/platform/request_auth/';//小程序跳转授权链接
miniPerson = 'https://app.inside.xiaoe-tech.com/wxa/get_collection_qr_code?app_id=';//小程序集二维码
miniexperUrl = 'https://app.inside.xiaoe-tech.com/wxa/get_qr_code?app_id='//体验二维码
miniappUrl = 'https://app.inside.xiaoe-tech.com/wxa/get_app_code?app_id=' //小程序二维码

/*文本编辑器*/
ueditor_config={
    toolbars: [
        [
            'undo', 'redo', '|',
            'fontsize', 'forecolor', 'bold', 'italic', 'underline', '|',

            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'lineheight', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',

            'simpleupload', 'insertframe','emotion', '|',

            'backcolor', 'autotypeset',
            'link', 'spechars', 'source', '|',
            'searchreplace',  '|',
            'fullscreen',      //全屏

        ]
    ],
    initialFrameWidth:700,
    initialFrameHeight :320,
    autoHeightEnabled: false,
}

/*是否保存当前修改*/
!function() {
    var Is_Save = false;
    changeSaveFlag = function (flag) {
        Is_Save = flag;
    }
    getSaveFlag = function () {
        return Is_Save;
    }
}()


//用于取消编辑时：获取当前页或者当前来路页url并跳回
function relistUrl(listUrl){
    var reUrl = GetQueryString('reurl');
    if(reUrl) {
        window.location.href = reUrl;
    }
    else{
        window.location.href = listUrl;
    }
}


///**js异常上报 start已废弃**/
////定义一个上报开关
//var ENABLE_NETLOG = true;
//
////自动监控异常，并上报
//$watcher = {
//    init: function () {
//        BJ_REPORT.init({id: 4, url: "https://jsreport.xiaoe-tech.com:8080/badjs"});
//        BJ_REPORT.tryJs().spyAll()
//    }
//};
//if (ENABLE_NETLOG) {
//    $watcher.init();
//}
//
////封装在线系统日志，可手动调用，然后管理台看实时日志
//$NetLog = {
//    error: function (message) {
//        var _target = arguments[1] ? arguments[1] : "";
//        var _rowNum = arguments[2] ? arguments[2] : "";
//        var _colNum = arguments[3] ? arguments[3] : "";
//        if (ENABLE_NETLOG) {
//            BJ_REPORT.report({msg: message, target: _target, rowNum: _rowNum, colNum: _colNum});
//        }
//    }, debug: function (message) {
//        var _target = arguments[1] ? arguments[1] : "";
//        var _rowNum = arguments[2] ? arguments[2] : "";
//        var _colNum = arguments[3] ? arguments[3] : "";
//        if (ENABLE_NETLOG) {
//            BJ_REPORT.debug({msg: message, target: _target, rowNum: _rowNum, colNum: _colNum});
//        }
//    }, log: function (message) {
//        var _target = arguments[1] ? arguments[1] : "";
//        var _rowNum = arguments[2] ? arguments[2] : "";
//        var _colNum = arguments[3] ? arguments[3] : "";
//        if (ENABLE_NETLOG) {
//            BJ_REPORT.info({msg: message, target: _target, rowNum: _rowNum, colNum: _colNum});
//        }
//    }
//};
///**js异常上报 end**/
