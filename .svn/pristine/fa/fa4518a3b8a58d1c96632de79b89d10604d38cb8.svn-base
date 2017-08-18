/**
 * Created by breeze on 15/03/2017.
 */
var appid = '1252524126';
var bucket = 'wechatapppro';

//初始化逻辑
//特别注意: JS-SDK使用之前请先到console.qcloud.com/cos 对相应的Bucket进行跨域设置
function InitCosCloud() {
    var cos = new CosCloud({
        appid: appid,// APPID 必填参数
        bucket: bucket,//bucketName 必填参数
        region: 'sh',//地域信息 必填参数 华南地区填gz 华东填sh 华北填tj
        getAppSign: function (callback) {   //获取签名 必填参数
            //1.搭建一个鉴权服务器，自己构造请求参数获取签名，推荐实际线上业务使用，优点是安全性好，不会暴露自己的私钥
            //拿到签名之后记得调用callback
            $.ajax('/getUploadSign?sign_type=appSign').done(function (data) {
                var sig = JSON.parse(data).data.sign;
                callback(encodeURIComponent(sig));
            });
        },
        getAppSignOnce: function (callback) {//单次签名，必填参数，参考上面的注释即可
            //    //填上获取单次签名的逻辑
            $.ajax('/getUploadSign?sign_type=appSign_once&path=').done(function (data) {
                var sig = JSON.parse(data).data.sign;
                callback(encodeURIComponent(sig));
            });
        }
    });

    /**
     * 上传文件 分片上传直接调用uploadFile方法，内部会判断是否需要分片
     * @param successCallBack 成功回调
     * @param errorCallBack 失败回调
     * @param progressCallBack  进度回调
     * @param remotePath    相对路径
     * @param file      文件
     * @param insertOnly insertOnly==0 表示允许覆盖文件 1表示不允许
     */
    this.uploadFile = function (successCallBack, errorCallBack, progressCallBack, remotePath, file, insertOnly) {
        cos.uploadFile(successCallBack, errorCallBack, progressCallBack, bucket, remotePath, file, insertOnly);
    };

    //没有进度回调的上传
    this.uploadFileWithoutPro = function (successCallBack, errorCallBack, remotePath, file, insertOnly) {
        cos.uploadFile(successCallBack, errorCallBack, null, bucket, remotePath, file, insertOnly);
    };


}
