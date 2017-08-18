/**
 * Created by Stuphin on 2016/11/5.
 */
$(document).ready(function()
{
    init();

    $('#closed').click(function(){
        $('.xcConfirm').hide();
    });

    //复制到剪贴板
    (function () {
        var clipboard = new Clipboard('.copyHref');
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    })();

});

//初始化
function init()
{
    //title点击跳转
    $("#audio").click(function () // 音频列表
    {
        window.location.href = "/audio_list";
    });
    $("#video").click(function () // 视频列表
    {
        window.location.href = "/video_list";
    });
    $("#article").click(function () // 图文列表
    {
        window.location.href = "/article_list";
    });
    $("#package").click(function () // 专栏列表
    {
        window.location.href = "/package_list";
    });
    $("#alive").click(function () // 直播列表
    {
        window.location.href = "/alive";
    });
    $("#tab_member").click(function () {// 会员页
        window.location.href = "/member_list";
    });

    //回填搜索值
    $("#search").val(getUrlParam("search"));
    $("#ruler").val((getUrlParam("ruler")==null  || getUrlParam("ruler")=='') ? 0 :getUrlParam("ruler"));

    //搜索按钮
    $("#searchButton").click(function()
    {
        var search=$("#search").val();
        var ruler=$("#ruler").val();
        window.location.href='/alive?search='+search+'&ruler='+ruler;
    });

    //回车搜索
    $(document).keypress(function(e)
    {
        if(e.which == 13)
        {
            $('#searchButton').trigger("click");
        }
    });

    //  补全显示直播人员列表

    var id;
    $("div.alive_name_wrapper").hover(function () {
        /*鼠标指针浮动在元素上面*/
        id = $("div.alive_name_wrapper:hover").attr("value");

        $("p#" + id + ".alive_name_hide").addClass("hide");
        $("div.alive_name_wrapper").eq(id).css({"max-height":"none"});
    }, function () {
        /*鼠标指针离开元素*/
        $("p#" + id + ".alive_name_hide").removeClass("hide");
        $("div.alive_name_wrapper").eq(id).css({"max-height":"100px"});
    });


    // 设置分销
    modal.initSetSale();

}

var modal = (function () {

    modal = {};

    modal['aliveId'] = -1;

    modal['piecePrice'] = -1;

    modal.initSetSale = function () {

        //  更多 - 设置分销
        $(".set_sale_ratio_btn").click(function () {

            var aliveName = $(this).parents(".tr_body").data("resource_name");
            $(".sale_goods_name").html(aliveName);

            modal["piecePrice"] = $(this).parents(".tr_body").data("piece_price");

            //  1、获取点击条目的直播 aliveId
            modal['aliveId'] = $(this).parents(".tr_body").data("resource_id");


            //  2、根据 aliveId 查询直播资源的分成比例值：（1-50）-开启，0-关闭
            $.ajax("/query_profit_ratio", {
                type: "POST",
                dataType: "json",
                data: {
                    "alive_id": modal["aliveId"]
                },
                success: function (result) {
                    if (result.code == 0) {

                        var distribute_percent = result.data["distribute_percent"];
                        if (distribute_percent == 0) {
                            //  关闭开关 , 清空输入框， 隐藏输入框
                            $("input#set_radio_off").prop("checked",true);
                            $(".input_radio_value").val("");
                            $('.set_percent').hide();

                        } else if (distribute_percent > 0 && distribute_percent <= 50) {
                            //  打开开关， 回显输入框， 显示输入框
                            $("input#set_radio_on").prop("checked", true);
                            $(".input_radio_value").val(distribute_percent);
                            $('.set_percent').show();

                        } else {
                            //  默认状态
                            $("input#set_radio_off").prop("checked", true);
                            $(".input_radio_value").val("");
                            $('.set_percent').hide();

                        }
                        $(".set_sale_ratio_window").fadeIn(300);

                    } else {
                        baseUtils.show.redTip("操作失败，请稍后重试！");

                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                },
            });

        });

        //  点击取消    点击 ×
        $(".cancel_sale_btn, .close_icon_wrapper_3").click(function () {
            $(".set_sale_ratio_window").fadeOut(300);

        });

        //  监控radio值的变化
        $("input[name='set_sale_radio']").on("change", function () {
            var saleSwitch = $("input[name='set_sale_radio']:checked").val();
            if (saleSwitch == 0) {
                if (modal["piecePrice"] < 1) {
                    baseUtils.show.redTip("商品价格不低于1元才可开启分销!");
                    $("input#set_radio_off").prop("checked",true);      //  重置radio

                } else {
                    $('.set_percent').fadeIn(300);

                }

            } else {
                $('.set_percent').fadeOut(300);
            }
        });

        //  实时监控input中的输入变化()
        $(".input_radio_value").bind("input propertychange", function () {
            var inputValue = $(".input_radio_value").val();

            var reg = /^[1-9][\d]*$/;
            if (!reg.test(inputValue)) {
                inputValue = inputValue.replace(/\D/g, "");
                $(".input_radio_value").val(inputValue);
            }

        });


        //  点击确认
        $(".confirm_sale_btn").click(function () {

            var saleSwitch = $("input[name='set_sale_radio']:checked").val();

            if (saleSwitch == 0) {  //  开启
                var inputValue = $(".input_radio_value").val();     //自定义分成比例

                if (inputValue < 1 || inputValue > 50) {
                    baseUtils.show.redTip("分成比例只能设置为1% ~ 50%!");
                    return false;
                }
                //  提交分销设置
                modal.submitSaleSet(modal["aliveId"], inputValue);
            } else {
                var inputValue = 0;     //分成比例 为 0
                //  提交分销设置
                modal.submitSaleSet(modal["aliveId"], inputValue);
            }
        });

    };

    /**
     * 提交分销设置
     *
     * @param aliveId       -   直播资源id
     * @param inputValue    -   设置分成比例： 邀请卡分销开启：（1 ~ 50）； 邀请卡分销关闭：0
     */
    modal.submitSaleSet = function (aliveId, inputValue) {
        $.ajax("/set_profit_ratio", {
            type: "POST",
            dataType: "json",
            data: {
                "alive_id": aliveId,
                "distribute_percent": inputValue
            },
            success: function (result) {
                if (result.code == 0) {

                    $(".set_sale_ratio_window").fadeOut(300);
                    baseUtils.show.blueTip("分销设置成功！");
                } else {
                    baseUtils.show.redTip("分销设置失败！");
                }

            },
            error: function (xhr, status, err) {
                console.error(err);
                baseUtils.show.redTip("服务器开小差啦，请稍后再提交！");
            }
        });
    };


    return modal;

})();

//下架
function offSale(id)
{
    $.get("alive/offsale",{"id":id},function(data)
    {
        if(data.ret==0)
        {
            baseUtils.show.blueTip("修改成功",function()
            {
               window.location.reload();
            });
        }
        else
        {
            baseUtils.show.redTip("修改失败");
        }
    });
}

//上架
function onSale(id)
{
    $.get("alive/onsale",{"id":id},function(data)
    {
        if(data.ret==0)
        {
            baseUtils.show.blueTip("修改成功",function()
            {
                window.location.reload();
            });
        }
        else
        {
            baseUtils.show.redTip("修改失败");
        }
    });
}

//删除
function deleteResource(id)
{
    window.wxc.xcConfirm("您确定要删除吗?","confirm",{onOk:function()
    {
        $.get("alive/delsale",{"id":id},function(data)
        {
            if(data.ret==0)
            {
                baseUtils.show.blueTip("删除成功",function()
                {
                    window.location.reload();
                });
            }
            else
            {
                baseUtils.show.redTip("删除失败");
            }
        });

    }});
}

//编辑
function editAlive(id)
{
    //获取当前页url
    var reUrl = encodeURIComponent(window.location.href);
    window.location.href="/editalive?id="+id+"&reurl="+reUrl;
}

//结束直播
function endAlive(id)
{
    window.wxc.xcConfirm("您确定要结束该直播吗?",'confirm',{onOk:function()
    {
        $.get("/endalive",{"id":id},function(data)
        {
            if(data.ret==0)
            {
                window.wxc.xcConfirm("结束直播成功",'success',{onOk:function ()
                {
                    window.location.href="/alive";
                }});
            }
            else
            {
                window.wxc.xcConfirm("直播结束失败");
            }
        });
    }});
}

//查看评论
function aliveComment(id)
{
    resetUrl("/alivecomment?alive_id="+id);
}



