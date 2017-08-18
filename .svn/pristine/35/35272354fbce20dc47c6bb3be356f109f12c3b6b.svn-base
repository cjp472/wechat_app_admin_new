/**
 * Created by Stuphin on 2016/9/26.
 */

$(document).ready(function()
{



    init();

    //编辑按钮点击
    $("#edit").click(function()
    {
        $(".titleShow").addClass("hide");
        $("#edit").addClass("hide");
        $("#home_title").removeClass("hide");
        $("#save").removeClass("hide");
    });

    //保存按钮点击
    $("#save").click(function()
    {
        var home_title=$("#home_title").val();
        if(home_title.length==0)
        {
            baseUtils.show.redTip("首页名称不能为空");
            return false;
        }

        $.get("/sethometitle",{"home_title":home_title},function(data)
        {
            if(data.code==0)
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
    });

    //商户名修改与保存
    $("#editWxName").click(function()
    {
        $(".wxTitleShow").addClass("hide");
        $("#editWxName").addClass("hide");
        $("#wxnameInput").removeClass("hide");
        $("#saveWx").removeClass("hide");
    });
    $("#saveWx").click(function()
    {
        var wxNameInput=$("#wxnameInput").val();
        if(wxNameInput.length==0)
        {
            baseUtils.show.redTip("商户名称不能为空");
            return false;
        }
        $.ajax('/edit_wx_name', {
            type: 'GET',
            dataType: 'json',
            data: {name: wxNameInput},
            success: function (json) {
                console.log(json);
                if (json.code == 1) {
                    baseUtils.show.blueTip('保存成功',function(){
                        window.location.reload();
                    });
                } else {
                    baseUtils.show.redTip(json.msg);
                }
            },
            error: function (err) {
                console.error(err);
                baseUtils.show.redTip('网络错误，请稍后再试！');
            }
        })
    });

});

//初始化
function init()
{

    //  显示小黄条   <小鹅通内容列表全面升级为知识商品，获取更多高级功能使用教程请点击【知识商品教程】>
    if (GetQueryString("first_login") == 1) {
        $(".red_prompt_word").html("您的知识店铺已创建成功，点击查看新手入门教程<a href='https://admin.xiaoe-tech.com/help_document' target='_blank'>查看教程</a>");
        $(".red_prompt").show();
    }
    //生成二维码
    if(document.getElementById("h5qrcode"))
    {
        var qrcode = new QRCode(document.getElementById("h5qrcode"),
            {
                text: $(".copyButton").prev().html(),
                width: 120,
                height: 120,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
    }

    //复制到剪贴板
    (function () {
        var clipboard = new Clipboard('.copyHref');
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    })();


    if( GetQueryString('first')==1 ){
        var top = $('#qrcodeArea').offset().top-20,
            left = $('#qrcodeArea').offset().left-20,
            right = document.documentElement.clientWidth - 300 - $('#qrcodeArea').offset().left,
            bottom = document.documentElement.clientHeight - 160 - $('#qrcodeArea').offset().top;
        $('body').on('mousewheel', function() {
            return false;
        });
        $('#mask').show().animate({
            'border-top-width': top,
            'border-right-width': right,
            'border-bottom-width': bottom,
            'border-left-width': left
        },1000,'swing',function() {
            $('#alertAppend').fadeIn();
        });
        $('#iKnow,#mask').click(function() {
            $('#mask').hide();
            $('#alertAppend').hide();
            $('body').off('mousewheel');
            var href = window.location.origin+window.location.pathname;
            try{
                window.history.replaceState({title:0},null,href);
            } catch(e) {
                window.href = href;
            }
        });
    }
}




