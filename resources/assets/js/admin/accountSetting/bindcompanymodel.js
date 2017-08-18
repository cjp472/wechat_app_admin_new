/**
 * Created by Stuphin on 2016/9/26.
 */

var nowChange = GetQueryString('change'); //获取当前状态
var payDirRid = '';
var iTime = null;
var i = 0;
$(document).ready(function()
{
    init();
    authHandle();

    //编辑图片点击
    $("#editImg").click(function()
    {
        $("#wx_mchid").removeClass("disEdit");
        $("#wx_mchkey").removeClass("disEdit");
        $("#wx_mchid").attr("disabled",false);
        $("#wx_mchkey").attr("disabled",false);
        $("#saveMer").parent().removeClass("hide");
    });

    if(nowChange == 1){
        $("#editImg").click();
        $('#qrcodeArea').hide();
        $('#showPhone').hide();
    }

    //商户配置保存按钮点击
    $("#saveMer").click(function()
    {
        //商户号
        var wx_mchid=$("#wx_mchid").val();
        if(wx_mchid.length==0)
        {
            baseUtils.show.redTip("亲,请输入微信商户号哦~");
            return false;
        }
        var reg1=/^[1-9][0-9]{7,9}$/;
        if(!reg1.test(wx_mchid))
        {
            baseUtils.show.redTip('亲！请输入正确的微信商户号哦~');
            return false;
        }

        //商户API密钥
        var wx_mchkey=$("#wx_mchkey").val();
        if(wx_mchkey.length==0)
        {
            baseUtils.show.redTip("亲,请输入商户API密钥哦~");
            return false;
        }
        var reg2=/^[0-9A-Za-z]{32}$/;
        if(!reg2.test(wx_mchkey))
        {
            baseUtils.show.redTip("亲！请输入正确的商户API密钥哦~");
            return false;
        }

        //验证支付配置是否正确
        //发送请求https://app.inside.xiaoe-tech.com/platform/check_merchant/{app_id}/{app_type}
        /*$.post("",
            {
                'app_id': $("#xcx_app_id").val(),
                '
            },function (data) {
                if(data.code == 0){
                    $.post("/updatemerchant",{"wx_mchid":wx_mchid,"wx_mchkey":wx_mchkey,"app_id":$("#xcx_app_id").val()},
                        function(data)
                        {
                            if(data.ret==0)
                            {
                                baseUtils.show.blueTip("保存成功",function()
                                {
                                    window.location.reload();
                                });
                            }
                            else
                            {
                                baseUtils.show.redTip("保存失败");
                            }
                        });
                }else{
                    baseUtils.show.redTip("支付配置信息有误,请核对后重新提交");
                }
            });*/

        $.post("/updatemerchant",{"wx_mchid":wx_mchid,"wx_mchkey":wx_mchkey,"app_id":$("#xcx_app_id").val()},
        function(data){
            if(data.ret==0){
                if(nowChange == 1) {

                    var text = '您已成功切换为企业模式，您的店铺地址已变更为<br/>'+
                        '<div style="color: #337ab7;">'+data.url+'</div>'+
                        '(该地址可在手机预览页面查看到)请尽快完成手机端入口的地址替换。';
                    // var text = '第二步已成功,请进行支付目录授权验证';
                    $.alert(text,'info',{
                        oktext: '我知道了',
                        btn: 2,
                        onOk: function() {
                            window.location.href='/companymodel';
                        },
                        onClose:function () {

                            window.location.href='/companymodel';
                        }
                    });
                } else {
                    baseUtils.show.blueTip("保存成功",function(){
                        window.location.href='/companymodel';
                    });
                }
            } else {
                baseUtils.show.redTip(data.msg);
            }
        });
    });


    // 点击 验证支付信息 按钮
    $("#confirmButton").click(function(){
        //生成一条0.05元的 图文 资源参数
        $("#confirmButton").attr("disabled","disabled");
        createTestImageText();
        i = 0;

    });

    $("#setModal").on('hide.bs.modal', function () {
        clearTimeout(iTime);
        $("#confirmButton").removeAttr("disabled");
    });

});


//生成 测试图文资源 参数
function createTestImageText() {

    // 生成资源url和二维码
    $.ajax('/make_page_url', {
        type: 'GET',
        dataType: 'json',
        data:{ },
        success:function (data) {
            if(data.code == 0){
                // console.log('---资源链接---：');
                // console.log(data['pageurl']);
                // console.log('---资源id---：');
                // console.log(data['r_id']);
                payDirRid = data['r_id'];
                $("#setModal").modal({backdrop:'static'});//弹窗

                $("#confirmPayDirCode").empty();

                // 生成二维码
                var qrcodeConfirmPayDirCode = new QRCode(document.getElementById("confirmPayDirCode"),
                    {
                        text:data['pageurl'],
                        width: 150,
                        height: 150,
                        colorDark : "#000000",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.L
                    });
                //ajax请求用户是否配置  实际是查看用户的支付方式
                remainTime();

            } else  {
                baseUtils.show.redTip('服务器繁忙，请稍后再试！');
            }


        },
        error: function(xhr,status,err) {
            hideLoading();
            console.error(err);
            baseUtils.show.redTip('网络错误，请稍后再试！');
            submitLimt = false;
        }
    });

}



function  remainTime() {

    //检测支付方式

    $.ajax('/confirm_order_get_paytype', {
        type: 'GET',
        dataType: 'json',
        data:{'r_id':payDirRid},
        success:function (data) {
            i += 1;
            // console.log('---请求次数---：');
            // console.log(i);
            //
            // console.log('---检测支付方式返回值---:');
            // console.log(data);


            if(i >= 300){
                i = 0;
                clearTimeout(iTime);
                $("#setModal").modal("hide");//弹窗隐藏

                var text = '您长时间未支付  请重新验证';

                $.alert(text,'info',{
                    oktext: '我知道了',
                    btn: 2,
                    onOk: function() {
                        window.location.href='/companymodel';
                    },
                    onClose:function () {
                        window.location.href='/companymodel';
                    }
                });

            }


            if((data.type == 0) && (data.use_collection == 0)){
                clearTimeout(iTime);
                $("#setModal").modal("hide");//弹窗隐藏

                //验证成功 更改请求将支付授权目录字段

                $.ajax('/confirm_order', {
                    type: 'GET',
                    dataType: 'json',
                    data:{},
                    success:function (data) {
                        if (data.code === 0){

                            var text = '恭喜您 您的公众号支付授权目录已经正确配置';
                            $.alert(text,'success',{
                                oktext: '我知道了',
                                btn: 2,
                                onOk: function() {

                                    window.location.href='/companymodel';
                                },
                                onClose:function () {

                                    window.location.href='/companymodel';
                                }
                            });
                        } else {
                            hideLoading();
                            console.error(err);
                            baseUtils.show.redTip('网络错误，请稍后再试！');
                            submitLimt = false;
                        }
                    },
                    error: function(xhr,status,err) {
                        hideLoading();
                        console.error(err);
                        baseUtils.show.redTip('网络错误，请稍后再试！');
                        submitLimt = false;
                    }
                });


            } else if(data.type === 1){

                clearTimeout(iTime);
                $("#setModal").modal("hide");//弹窗隐藏

                var text = '您的公众号支付授权目录配置不正确 <br>'+
                    ' 请前往微信公众平台配置正确再重新验证 <br>' +
                    '<a style="color: #337ab7;" target="_blank" href="https://admin.xiaoe-tech.com/help#hp4">如何配置支付授权目录？</a>';
                $.alert(text,'error', {
                    oktext: '重新设置',
                    btn: 2,
                    onOk: function () {

                        window.location.href = '/companymodel#payset';
                    },
                    onClose:function () {

                        window.location.href='/companymodel#payset';
                    }
                });

            } else if ((data.type == 0) && (data.use_collection == 1)){


                clearTimeout(iTime);
                $("#setModal").modal("hide");//弹窗隐藏

                var text = '您的商户信息配置不正确 <br>'+
                    ' 请前往上一步配置正确再重新验证 <br>';
                $.alert(text,'error', {
                    oktext: '重新设置',
                    btn: 2,
                    onOk: function () {

                        window.location.href = '/companymodel#payset';
                    },
                    onClose:function () {

                        window.location.href='/companymodel#payset';
                    }
                });

            } else if(data.type === null){
                //用户未进行支付 不进行操作
            }
        },
        error: function(xhr,status,err) {
            hideLoading();
            console.error(err);
            baseUtils.show.redTip('网络错误，请稍后再试！');
            submitLimt = false;
        }
    });

    iTime = setTimeout("remainTime()",2000);
}


//初始化
function init()
{
    //  Content区域小标题
    /*showContentTitle();
    if (is_huidu == 1) {
        appendContentHeader("账户一览", "/accountview", false);
    }
    appendContentHeader("账号管理", "/accountmanage", false);
    appendContentHeader("运营模式设置", "/companymodel", true);*/
    // appendContentHeader("小程序设置", "/smallprogramsetting", false);

    //生成二维码
    var qrcode = new QRCode(document.getElementById("h5qrcode"),
    {
        text: $(".copyButton").eq(0).prev().html(),
        width: 130,
        height: 130,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });



    //复制按钮
    //复制到剪贴板
    (function () {
        var clipboard = new Clipboard('.copyHref');
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    })();
    // $(".copyButton").zclip
    // ({
    //     path: "js/external/ZeroClipboard.swf",
    //     copy: function()
    //     {
    //         return $(this).prev().html();
    //     },
    //     beforeCopy:function()
    //     {},
    //     afterCopy:function()
    //     {
    //         baseUtils.show.blueTip("复制成功");
    //     }
    // });

    //上传按钮
    $('#wx_bus_verify_txt').uploadifive
    ({
        'auto'         : true,
        'buttonText'   : '选择文件',
        'buttonClass'  : 'greenChooseFile',
        'fileType': '.txt',
        'uploadScript' : '/remote_uploadVerifyFile?app_id='+$("#xcx_app_id").val(),
        'uploader' : '/remote_uploadVerifyFile?app_id='+$("#xcx_app_id").val(),
        'uploadLimit' : 1,
        'itemTemplate' : false,
        'method'   : 'post',
        'fileSizeLimit' : '50',
        'onCancel'     : function()
        {
            baseUtils.show.blueTip("取消文件上传!");
        },
        'onUploadComplete' : function(file, data)
        {
            data = JSON.parse(data);
            if (data.ret == 0)
            {
                baseUtils.show.blueTip(data.msg,function()
                {
                    window.location.reload();
                });
            }
            else
            {
                baseUtils.show.redTip(data.msg);
            }
        },
        'onError' : function(errmsg)
        {
            baseUtils.show.redTip(errmsg);
        }
    });
}

function getFormatTime() {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth()+1;
    var day = date.getDate();
    var hour = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();
    var time = year + '-' + month + '-' + day + ' '+ hour + ':' + minute + ':' + second;
    return time;

}
//授权相关
function authHandle()
{
    //重新授权点击
    $("#reAuth").click(function()
    {
        var newWin = window.open('','_blank');
        var app_id=$("#xcx_app_id").val();
        $.get("/updateifauth",{"app_id":app_id},function(data)
        {
            if(data.ret==0)
            {
                newWin.location.href = authUrl+app_id;
                $("#bindModal").modal("show");
            }
            else
            {
                baseUtils.show.redTip("系统繁忙");
            }
        });
    });

    //授权成功按钮点击刷新
    $("#bindSuccess").click(function()
    {
        window.location.reload();
    });

    //授权失败，继续弹出
    $("#bindFail").click(function()
    {
        var app_id=$("#xcx_app_id").val();
        window.open(authUrl+app_id);
        $("#bindModal").modal("show");
    });
}




