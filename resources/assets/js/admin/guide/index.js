$(function() {
    /*$('#myTabs a').click(function (e) {
     e.preventDefault()
     $(this).tab('show')
     })*/

    (function () {  //复制链接
        var clipboard = new Clipboard('#ShopUrl');
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    })();

    (function () {
        var value = $(".customBalance").text();

        value = value.replace(/,/g, '');

        var app_balance = parseFloat(value);
        // console.log(app_balance);
        // if (app_balance >= 0 && app_balance < 50) {
        //     $(".red_prompt").show();
        //     return false;
        // }
        if (app_balance < 0) {//欠费弹出框
            $(".window_prompt").show();
            return false;
        }
    })();

    var qrcode = new QRCode(document.getElementById("shopCode"),  //生成二维码
        {
            text: $("#ShopUrl").data('clipboard-text'),
            width: 120,
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });


    $('#notShowCase').click(function() {
        $.alert('点击确定后，您将不会再看到精彩案例呦！','info',{
            onOk: function() {
                $.ajax('/closeMessageReminder',{
                    type: 'GET',
                    dataType: 'json',
                    data:{status: 1},
                    success: function(json) {
                        if(json.code == 0) {
                            $('#goodCase').fadeOut();
                        } else {
                            baseUtils.show.redTip(json.msg);
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        baseUtils.show.redTip('网络错误，请稍后再试！');
                    }
                })
            }
        })


    });

    $("#shopSet").on('click',function(){
       window.location.href='/interfacesetting'
    });

    var shopTimer = null;
    $('#showShop').mouseenter(function () {
        $('#qrcodeArea').fadeIn();
    }).mouseleave(function () {
        shopTimer = setTimeout(function () {
            $('#qrcodeArea').fadeOut();
        }, 300);
    });
    $('#qrcodeArea').mouseenter(function () {
        clearTimeout(shopTimer);
    }).mouseleave(function () {
        shopTimer = setTimeout(function () {
            $('#qrcodeArea').fadeOut();
        }, 300);
    });

    $('.subClose').on('click',function(){//临时关闭按钮，下次迭代调整
        console.log(newGuide.lockKey);
        $('#qrcodeArea').fadeOut();
    });
    // 优惠券概况弹窗
    //关闭推广介绍框
    $(".welcomeBoxClose").click(function () {
        console.log('close click');
        $(".darkScreen2").fadeOut(200);
        $.ajax('',function(){

        })
    });
    $(".exerciseBook").click(function(){
        var versionType=$("#versionType").val();
        if(versionType==1||versionType==2) {
            baseUtils.show.redTip('当前版本不支持作业本，如需开启请升级至专业版');
        }else{
            window.location='/exercise/exercise_book_list';
        }
    });
    $(".Q_A").click(function(){
        var versionType=$("#versionType").val();
        if(versionType==1) {
            baseUtils.show.redTip('当前版本不支持问答，如需开启请升级至成长版或专业版');
        }else{
            window.location='/QA/questionAndAnswerDetail';
        }
    });
    $(".member_vip").click(function(){
        var versionType=$("#versionType").val();
        if(versionType==1) {
            baseUtils.show.redTip('当前版本不支持会员，如需开启请升级至成长版或专业版');
        }else{
            window.location='/member_list_page';
        }
    });
    /*$('#caseContent').on('mouseenter', '.item', function(e) {
     $(e.target).find('.codeContent').fadeIn();
     }).on('mouseleave', '.item', function(e) {
     $(e.target).find('.codeContent').fadeOut();
     });;*/


//立即升级
    $("body").on("click", '.cm_upgrade', function () {//百度事件追踪测试
        // _hmt.push(['_trackEvent', '立即升级', 'clickEvent', 'aboutTheTest']);

    });

//访问店铺
    $("body").on("click", '.cm_shop', function () {
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '访问店铺', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//概况_数据分析
    $("body").on("click", '.cm_dataAnalysis', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '概况_数据分析', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//提现
    $("body").on("click", '.cm_withDrawCash', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '提现', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//充值
    $("body").on("click", '.cm_recharge', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '充值', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//查看结算记录
    $("body").on("click", '.cm_record', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '查看结算记录', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//新增图文
    $("body").on("click", '.cm_addArticle', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '新增图文', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//新增音频
    $("body").on("click", '.cm_addMusic', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '新增音频', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//新增视频
    $("body").on("click", '.cm_addVideo', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '新增视频', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//新增直播
    $("body").on("click", '.cm_addAlive', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '新增直播', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//新增社群
    $("body").on("click", '.cm_addCommunity', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '新增社群', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//收入/提现
    $("body").on("click", '.cm_income', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '收入/提现', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//设置轮播图
    $("body").on("click", '.cm_bannerPic', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '设置轮播图', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//群发消息
    $("body").on("click", '.cm_message', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '群发消息', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//活动
    $("body").on("click", '.cm_activity', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '活动', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

//推广员
    $("body").on("click", '.cm_saler', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '推广员', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

    //邀请卡
    $("body").on("click", '.cm_inviteCard', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        // _hmt.push(['_trackEvent', '邀请卡', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

    //小社群
    $("body").on("click", '.cm_smallCommunity', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        //_hmt.push(['_trackEvent', '小社群', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

    //概况——帮助中心
    $("body").on("click", '.cm_indexHelpCenter', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        //_hmt.push(['_trackEvent', '概况——帮助中心', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

    //左侧菜单-帮助中心
    $("body").on("click", '.cm_leftHelpCenter', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        //_hmt.push(['_trackEvent', '左侧菜单-帮助中心', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });

    //不再显示
    $("body").on("click", '.cm_noShowMore', function () {//百度事件追踪测试
        // console.log("test test test");
        // alert("test");
        //_hmt.push(['_trackEvent', '不再显示', 'clickEvent', 'aboutTheTest']);
        // console.log(_hmt);
        // alert(_hmt);
    });
    $(document).ready(function(){
        $('.darkScreen2.indWel').addClass('ready');
        $('.darkScreen2.indWel').addClass('active');
    });
    $('.indexWelcomeCloseBtn,.indexWelcomeCont a').on('click',function(){
        $('.darkScreen2.indWel').removeClass('active');
        $.ajax('/closeMessageReminder',{
            type: 'get',
            dataType: 'json',
            data:{status:1,place:12},
            success: function(json) {
                if(json.code == 0) {

                } else {

                }
            },
            error: function(err) {
                console.error(err);
            }

        });
    });

});

//新手引导弹窗

$(function(){
    //增加限制条件 GetQueryString(first) == 1

  newGuide.init();
});

var newGuide={
    init:function(){
        var left = $('.bannerBox');
        var itemIndex = 0;//初始值
        var title = $('.title');
        var content = $('.txtMsg');
        var littleDot = $('.static');
        var nexEvent = $('.guideBtn');
        var guidMsg = $('.guideMsg');
        var moveValue;
        var moveBanner;//定时器

        //关闭弹窗
        $('.closeGuide').on('click',function(){
            $('.guideBox').fadeOut();
        });

        //btn下方小圆圈事件
        $('.markBox').on('click','.static',function(){
            var itemIndex = $(this).index();
            console.log(itemIndex);
            change(itemIndex);
            clearInterval(moveBanner);//受事件影响后重新计时
            setTimer();
        });

        //下一步事件；
        nexEvent.on('click',function(){
           itemIndex = $('.markBox .active').index();
           itemIndex++;
           clearInterval(moveBanner);
           setTimer();
           if(itemIndex > 3){
               $('.guideBox').fadeOut();
               //显示店铺二维码
               $('#qrcodeArea').fadeIn();
           }else{
               change(itemIndex);
           }
        });

        //轮播定时器
        setTimer();
        function setTimer() {
            moveBanner = setInterval(function () {
                if(itemIndex >= 3){
                    clearInterval(moveBanner);
                }else{
                    itemIndex++;
                    change(itemIndex);
                }
            }, 8000);
        }

        //行动方法
        function moving(){
            setTimeout(function(){
                itemIndex = $('.markBox .active').index();
                moveValue = itemIndex*622;
                left.stop().animate({left:-moveValue});
            },300)
        }

        function change(itemIndex){
            if(itemIndex == 0){
                guidMsg.stop().animate({opacity:0},'slow',function(){
                    title.html("扫码访问店铺");
                    content.html("您可以扫描此二维码进入您的知识店铺");
                    guidMsg.stop().animate({opacity:1},'slow');
                });
                nexEvent.html('下一步');
                // left.css('left','0px')
                moving();
            }else if(itemIndex == 1){
                guidMsg.stop().animate({opacity:0},'slow',function(){
                    title.html("嵌入公众号");
                    content.html("将店铺链接添加至已认证微信公众号自定义菜单栏，完成店铺与公众号的连接");
                    guidMsg.stop().animate({opacity:1},'slow');
                });
                nexEvent.html('下一步');
                moving();
            }else if(itemIndex == 2){
                guidMsg.stop().animate({opacity:0},'slow',function(){
                    title.html("管理知识商品");
                    content.html("您可以在这里开始创建并管理您的知识商品");
                    guidMsg.stop().animate({opacity:1},'slow');
                });
                nexEvent.html('下一步');
                moving();
            }else if(itemIndex == 3){
                guidMsg.stop().animate({opacity:0},'slow',function(){
                    title.html("查看帮助中心");
                    content.html("您可以在这里查看相关功能的教程说明和帮助文档");
                    guidMsg.stop().animate({opacity:1},'slow');
                });
                nexEvent.html('立即体验');
                moving();
            }
            littleDot.removeClass('active');
            littleDot.eq(itemIndex).addClass('active');
        }

        //新手引导显示条件
        if(GetQueryString('first') == 1){
            $('.guideBox').fadeIn();
        }
    }
};



