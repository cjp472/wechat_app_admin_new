/**
 * Created by fuhaiwen on 2017/1/26.
 */

/**
 * Created by Stuphin on 2016/9/26.
 */
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
        $("#saveMer").removeClass("hide");
    });

    //商户配置保存按钮点击
    $("#saveMer").click(function()
    {
        //商户号
        var wx_mchid=$("#wx_mchid").val();
        if(wx_mchid.length==0)
        {
            baseUtils.show.redTip("亲,请输入商户号哦~");
            return false;
        }
        var reg1=/^[1-9][0-9]{7,9}$/;
        if(!reg1.test(wx_mchid))
        {
            baseUtils.show.redTip('亲！请输入正确的商户Id哦~');
            return false;
        }

        //商户API密钥
        var wx_mchkey=$("#wx_mchkey").val();
        if(wx_mchkey.length==0)
        {
            baseUtils.show.redTip("亲,请输入商户密钥哦~");
            return false;
        }
        var reg2=/^[0-9A-Za-z]{32}$/;
        if(!reg2.test(wx_mchkey))
        {
            baseUtils.show.redTip("亲！请输入中正确的商户key哦~");
            return false;
        }

        //验证支付配置是否正确
        //发送请求https://app.inside.xiaoe-tech.com/platform/check_merchant/{app_id}/{app_type}
        $.post("https://app.inside.xiaoe-tech.com/platform/check_merchant",
            {
                'app_id': $("#xcx_app_id").val(),
                'app_type':1
            },function (data) {
                if(data.code == 0){
                    $.post("/updatemerchant",{"wx_mchid":wx_mchid,"wx_mchkey":wx_mchkey,"app_id":$("#xcx_app_id").val()},
                        function(data)
                        {
                            if(data.ret==0)
                            {
                                baseUtils.show.blueTip("保存成功",function()
                                {
                                    window.location.href = '/h5setting';
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
            });
    });
});

//初始化
function init()
{
    //  Content区域小标题
    showContentTitle();
    if (is_huidu == 1) {
        appendContentHeader("账户一览", "/accountview", false);
    }
    appendContentHeader("账号管理", "/accountmanage", false);
    appendContentHeader("支付设置", "/h5setting", true);
    // appendContentHeader("小程序设置", "/smallprogramsetting", false);

    //生成二维码
    var qrcode = new QRCode(document.getElementById("h5qrcode"),
        {
            text: $(".copyButton").eq(0).prev().html(),
            width: 120,
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

    //复制按钮
    $(".copyButton").zclip
    ({
        path: "js/external/ZeroClipboard.swf",
        copy: function()
        {
            return $(this).prev().html();
        },
        beforeCopy:function()
        {},
        afterCopy:function()
        {
            baseUtils.show.blueTip("复制成功");
        }
    });

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

//授权相关
function authHandle()
{
    var app_id=$("#xcx_app_id").val();

    //重新授权点击
    $("#reAuth").click(function()
    {
        $.get("/updateifauth",{"app_id":app_id},function(data)
        {
            if(data.ret==0)
            {
                window.open(authUrl+app_id);
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
        $.get("/check_auth_result",{"app_id":app_id},function(data)
        {
            if(data.ret==0)
            {
                //
                $("#bindModal").modal("hide");
                //请求获取编辑支付配置页面
                window.location.href = '/set_wxpay_page';
            }
            else
            {
                window.location.reload();
            }
        });
    });

    //授权失败，继续弹出
    $("#bindFail").click(function()
    {
        var app_id=$("#xcx_app_id").val();
        window.open(authUrl+app_id);
        $("#bindModal").modal("show");
    });
}





