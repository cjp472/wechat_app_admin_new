/**
 * Created by Neo on 2017/1/5.
 */



/**
 * 资源错误上报
 * reportParm
 * @param limitTag 限制发送标志（false只上报一次(后续调用无效)；true为只上报不同类型错误(下列四个参数完全相同的不再上报)
 * @param errorEvent 错误事件
 * @param errorCode 错误码
 * @param srcType 资源类型
 * @param srcHref 资源地址
 */
$errorReprot = {
    // 限制上报，同种情况只上报一次
    reportedArr:[],
    // 上报次数
    reportNum:0,
    report:function (reportParm) {
        try {
            // 限制发送标志
            limitTag = reportParm.limitTag || false;
            // 资源类型
            srcType = reportParm.srcType||'';
            // 资源地址
            srcHref = reportParm.srcHref||'';
            // 错误事件
            errorEvent = reportParm.errorEvent||'';
            // 错误码
            errorCode = reportParm.errorCode||'';

            if(!limitTag && $errorReprot.reportNum>=1){
                console.log("超过错误上报次数限制");
                return;
            }

            $errorReprot.reportNum += 1;

            // 页面链接
            var pageUrl = location.href;
            // 请求头信息
            var userAgent = navigator.userAgent;


            // 系统类型
            var systemType = "";
            if(userAgent.indexOf("iPhone") !== -1 || userAgent.indexOf("iphone") !== -1){
                systemType = "iPhone";
            }
            else if(userAgent.indexOf("Android") !== -1 || userAgent.indexOf("android") !== -1){
                systemType = "Android";
            }


            // 系统版本
            var systemVer = "";
            if(systemType === "iPhone"){
                var systemPatt = /iPhone OS (.*?) /g;
                systemVer = systemPatt.exec(userAgent);
                if(systemVer && systemVer.length>1){
                    systemVer = systemVer[1];
                }
            }
            else if(systemType === "Android"){
                var systemPatt = /Android (.*?);/g;
                systemVer = systemPatt.exec(userAgent);
                if(systemVer && systemVer.length>1){
                    systemVer = systemVer[1];
                }
            }


            // 手机型号
            var phoneType = "";
            if(systemType === "iPhone"){
                phoneType = systemType;
            }
            else if(systemType === "Android"){
                var phoneTypePatt = /; (.*?)\) AppleWebKit/g;
                phoneType = phoneTypePatt.exec(userAgent);
                if(phoneType && phoneType.length>1){
                    phoneType = phoneType[1];
                    var num = phoneType.indexOf(";");
                    if(num !== -1){
                        phoneType = phoneType.substring(num+2)
                    }
                }
            }


            // 微信版本
            var wxVerPatt = /MicroMessenger\/(.*?) /g;
            var wxVer = wxVerPatt.exec(userAgent);
            if(wxVer && wxVer.length>1){
                wxVer = wxVer[1];
            }
            else{
                wxVer = "";
            }

            // 网络类型
            var networkTypePatt = /NetType\/(.*?) /g;
            var networkType = networkTypePatt.exec(userAgent);
            if(networkType && networkType.length>1){
                networkType = networkType[1];
            }
            else if(navigator.connection && navigator.connection.type){
                networkType = navigator.connection.type;
            }
            else{
                networkType = "";
            }


            var params = {
                // 资源类型
                src_type:srcType,
                // 资源地址
                src_href:srcHref,
                // 错误事件
                error_event:errorEvent,
                // 错误码
                error_code:errorCode,
                // 页面链接
                page_href:pageUrl,
                // 请求头信息
                user_agent:userAgent,
                // 手机型号
                phone_type:phoneType,
                // 系统类型
                system_type:systemType,
                // 系统版本
                system_ver:systemVer,
                // 微信版本
                wx_ver:wxVer,
                // 网络类型
                network_type:networkType
            };

            // 限制上报，同种情况只上报一次
            var reportTag = srcType+srcHref+errorEvent+errorCode;
            if($errorReprot.reportedArr.indexOf(reportTag) > -1){
                console.log("上报过该类型错误了");
                return;
            }
            $errorReprot.reportedArr.push(reportTag);
            NetWork.request("error_report", params, function (data) {});
            console.log("error_report")
        }
        catch (err){
            console.log(err)
        }
    }
};

/**
 * 音频断点续听（首页/专栏页/音频详情页)
 * @param audioId
 * @param audioDom
 */
$audioContinue = {
    // 已断点续听的音频
    readedArr:[],
    readTime:function (audioId,audioDom) {
        try {
            if(localStorage && $audioContinue.readedArr.indexOf(audioId) == -1){
                var audioTime = localStorage.getItem("audioTime");
                if(audioTime){
                    audioTime = JSON.parse(audioTime);
                    var nowTime = audioTime[audioId];
                    if(nowTime && audioDom.currentTime< 3){
                        nowTime = nowTime-3;
                        if(nowTime >0){
                            audioDom.currentTime =nowTime;
                        }
                        else{
                            audioDom.currentTime =0.01;
                        }
                        $audioContinue.readedArr.push(audioId);
                        console.log($audioContinue.readedArr);
                    }
                }
            }
        }
        catch (err){
            console.log(err);
        }
    },
    saveTime:function (audioId,audioDom) {
        try {
            if(localStorage){
                var audioTime = localStorage.getItem("audioTime");
                if(audioTime){
                    audioTime = JSON.parse(audioTime);
                }
                else{
                    audioTime = {};
                }
                var currentTime = audioDom.currentTime;
                var duration = audioDom.duration;
                if(audioTime[audioId] !== currentTime && currentTime>3){
                    if(duration - currentTime<=3){
                        audioTime[audioId] = 0;
                    }
                    else{
                        audioTime[audioId] = currentTime;
                    }
                    audioTime = JSON.stringify(audioTime);
                    localStorage.setItem("audioTime",audioTime);
                }
            }
        }
        catch (err){
            console.log(err);
        }
    }
};

/**
 * 音频列表播放（首页/专栏页)
 */
$audioListPlay = {
    // 正在播放的audio
    $playingAudio:null,
    $playingAudioIndex:0,
    $playingAudioId:null,
    $playingAudioM3u8:null,
    $audioType:null,
    $playNum:[],
    init:function () {
        $audioListPlay.clickPlayButton();
        $audioListPlay.updatePlayInfo();
        $audioListPlay.playNextAuto();
    },
    clickPlayButton:function () {
        // 事件代理
        $(".content-list").on("click",".audioPlayButton",function () {
            // 播放点击区域
            var audioPlayButton =$(this);
            if(audioPlayButton){
                // 为audio赋src
                var audioDom = $audioListPlay.getAudioSrc(audioPlayButton);
                // 播放暂停音频
                if(audioPlayButton.hasClass("audioStopStatus")){
                    $audioListPlay.playAudio(audioPlayButton,audioDom);
                    // $audioRecover.recoverStatus = true;
                }
                else if(audioPlayButton.hasClass("audioPlayStatus")){
                    $audioListPlay.pauseAudio(audioPlayButton,audioDom);
                    // $audioRecover.recoverStatus = false;
                }
            }
        })
    },
    // 更新播放进度
    updatePlayInfo:function () {
        setInterval(function () {
            if($audioListPlay.$playingAudio && !$audioListPlay.$playingAudio[0].paused){
                var currentTime = $audioListPlay.$playingAudio[0].currentTime;
                var duration = $audioListPlay.$playingAudio[0].duration;
                var percent = parseInt((currentTime /duration)*100);
                if(!isNaN(percent) && percent >0 && percent <=100){
                    $(".audioPlayPercent").eq($audioListPlay.$playingAudioIndex).html(percent)
                }
                else if(!isNaN(percent)  && percent >=100){
                    $(".audioPlayPercent").eq($audioListPlay.$playingAudioIndex).html(100)
                }
                // 保存播放进度
                var audioId = $audioListPlay.$playingAudioId;
                $audioContinue.saveTime(audioId,$audioListPlay.$playingAudio[0]);

                if(duration - currentTime <=30){
                    if($audioListPlay.$audioType){
                        $audioActionAnalyse.$type = 1;
                    }
                    else{
                        $audioActionAnalyse.$type = 0;
                    }
                    $audioActionAnalyse.$audioId = audioId;
                    $audioActionAnalyse.finishCount();
                }

                if(currentTime>0.0001){
                    $audioContinue.readTime(audioId,$audioListPlay.$playingAudio[0]);
                }
            }


        },500)
    },
    // 为audio赋src
    getAudioSrc:function (audioPlayButton) {
        // 播放audio
        var audioDom = audioPlayButton.children("audio").eq(0);

        // 各种格式
        var m3u8 = audioPlayButton.data("m3u8");
        var mp3compress = audioPlayButton.data("mp3compress");
        var mp3 = audioUrl = audioPlayButton.data("mp3");

        // 播放url
        if(!audioDom.attr("src")){
            var audioUrl = "";

            if(mp3compress){
                audioUrl = mp3compress;
            }
            else if(m3u8){
                audioUrl = m3u8
            }
            else if(mp3){
                audioUrl = mp3;
            }


            audioDom.attr("src",audioUrl);
        }
        return audioDom;
    },
    // 开始播放
    playAudio:function (audioPlayButton,audioDom) {
        if($audioListPlay.$playingAudio){
            $audioListPlay.$playingAudio[0].pause();
        }
        // audioDom[0].load();
        audioDom[0].play();

        // 调用微信接口，解决不能自动播放的问题
        try{
            wx.getNetworkType({
                success: function (res) {
                    // audioDom[0].load();
                    audioDom[0].play();
                }
            });
        }
        catch(err){

        }
        console.log("play")

        $audioListPlay.$playingAudioIndex = $(".audioDom").index(audioDom);
        $audioListPlay.$playingAudio = audioDom;
        $audioListPlay.$playingAudioId = $(".audioPlayButton").eq($audioListPlay.$playingAudioIndex).data("id");
        $audioListPlay.$playingAudioM3u8 = $(".audioPlayButton").eq($audioListPlay.$playingAudioIndex).data("m3u8");
        $audioListPlay.$audioType = $(".audioPlayButton").eq($audioListPlay.$playingAudioIndex).data("try");

        $audioListPlay.$playingAudio.on("canplay",function () {
            $audioContinue.readTime($audioListPlay.$playingAudioId,$audioListPlay.$playingAudio[0]);
            $audioListPlay.$playingAudio.off("canplay");
        });


        $(".audioPlayPercentWrapper").eq($audioListPlay.$playingAudioIndex).show();
        $(".audioPlayButton").removeClass("audioPlayStatus").addClass("audioStopStatus");
        $(".audioTitle").removeClass("c7").addClass("c2");
        $(".audioTitle").eq($audioListPlay.$playingAudioIndex).removeClass("c2").addClass("c7");
        audioPlayButton.removeClass("audioStopStatus").addClass("audioPlayStatus");

        // 统计上报，不重复基数
        if ($audioListPlay.$playNum.indexOf($audioListPlay.$playingAudioIndex) === -1) {
            $audioListPlay.$playNum.push($audioListPlay.$playingAudioIndex);

            NetWork.request("addAudioViewCount/" + audioPlayButton.data("id"), {}, function (data) {});

            if($audioListPlay.$audioType){
                $audioActionAnalyse.$type = 1;
            }
            else{
                $audioActionAnalyse.$type = 0;
            }
            $audioActionAnalyse.$audioId = $audioListPlay.$playingAudioId;
            $audioActionAnalyse.playCount();

            var viewCount = $(".audioViewCount").eq($audioListPlay.$playingAudioIndex).html();
            if(viewCount && viewCount.indexOf("W") === -1){
                viewCount = parseInt(viewCount) +1;
                $(".audioViewCount").eq($audioListPlay.$playingAudioIndex).html(viewCount);
            }
        }
    },
    // 暂停播放
    pauseAudio:function (audioPlayButton,audioDom) {
        audioDom[0].pause();
        audioPlayButton.removeClass("audioPlayStatus").addClass("audioStopStatus");
        $(".audioTitle").removeClass("c7").addClass("c2");
    },
    // 根据索引播放audio
    playByIndex:function (index) {
        var audioPlayButtonArr = $(".audioPlayButton");
        var audioPlayButton = null;
        var audioDom =null;
        if(index < audioPlayButtonArr.length){
            // 播放点击区域
            audioPlayButton= audioPlayButtonArr.eq(index);
            // 播放audio
            audioDom = $audioListPlay.getAudioSrc(audioPlayButton);
            $audioListPlay.playAudio(audioPlayButton,audioDom);
        }
        // 播完最后一个暂停
        else if(index-1 < audioPlayButtonArr.length){
            // 播放点击区域
            audioPlayButton= audioPlayButtonArr.eq(index-1);
            // 播放audio
            audioDom = $audioListPlay.getAudioSrc(audioPlayButton);
            $audioListPlay.pauseAudio(audioPlayButton,audioDom);
        }
    },
    // 自动播放下一首逻辑
    playNextAuto:function () {
        $(".audioDom").each(function () {
            $(this).off("ended").on("ended",function () {
                $audioActionAnalyse.$type = 0;
                $audioActionAnalyse.$audioId = $(".audioPlayButton").eq($audioListPlay.$playingAudioIndex).data("id");
                $(".audioPlayPercent").eq($audioListPlay.$playingAudioIndex).html(100);
                var index = $audioListPlay.$playingAudioIndex + 1;
                $audioListPlay.playByIndex(index);
            })
        })
    }
};

/**
 * 音频行为上报（首页/专栏页/音频详情页)
 */
$audioActionAnalyse = {
    $type:"",
    $audioId:"",
    $reported:[],
    playCount:function () {
        $audioActionAnalyse.reportToServer("play_count")
    },
    finishCount:function () {
        $audioActionAnalyse.reportToServer("finish_count")
    },
    reportToServer:function (action) {
        var buzData = {
            "type":$audioActionAnalyse.$type,
            "audio_id":$audioActionAnalyse.$audioId,
            "action":action
        };
        var reportTag = JSON.stringify(buzData);
        if($audioActionAnalyse.$reported.indexOf(reportTag) === -1){
            $audioActionAnalyse.$reported.push(reportTag);
            NetWork.request("audio_action_analyse", buzData, function (data) {
            });
            console.log(action);

        }
    }
};


/**
 * 买赠功能（专栏页，各详情页）
 */
$giftBuy = {
    minLimit: 1,
    maxLimit : 100,
    buyCount: parseInt($("#selector_show").val()),
    buying:false,
    piece_price:parseFloat($("#payPrice").val()),
    init: function () {
        $(".giftBuyButton").each(function () {
            $(this).on("click",function () {
                $(".payManyPart").show();
                $('body').on('touchmove', function (event) {
                    event.preventDefault();
                });
            });
        });

        $("#closePayMany").on("click",function () {
            $(".payManyPart").hide();
            $('body').off('touchmove');
        });

        $giftBuy.buyCardAction();
    },
    // 点击立即购买的用户行为
    buyCardAction:function () {
        //数量减少按钮点击（限制最低3）
        $('#reduce_count').click(function (e) {
            e.stopPropagation();
            $giftBuy.buyCount = $giftBuy.buyCount - 1;
            if ($giftBuy.buyCount < $giftBuy.minLimit) {
                $giftBuy.buyCount = $giftBuy.minLimit;
            }
            $giftBuy.updatePrice();
        });
        //数量增加按钮点击
        $('#add_count').click(function (e) {
            e.stopPropagation();
            $giftBuy.buyCount = $giftBuy.buyCount + 1;
            if ($giftBuy.buyCount > $giftBuy.maxLimit) {
                $giftBuy.buyCount = $giftBuy.maxLimit;
            }
            $giftBuy.updatePrice();
        });
        //手动修改数量
        $('#selector_show').change(function () {
            var count = parseInt($(this).val());
            if(!isNaN(count)){
                $giftBuy.buyCount = count;
                if ($giftBuy.buyCount < $giftBuy.minLimit) {
                    $giftBuy.buyCount = $giftBuy.minLimit;
                }
                else if ($giftBuy.buyCount > $giftBuy.maxLimit) {
                    $giftBuy.buyCount = $giftBuy.maxLimit;
                }
                $giftBuy.updatePrice();
            }
            else{
                $giftBuy.updatePrice();
            }
        });


        //确认支付
        $('#giftBuyButton').click(function () {
            if ($giftBuy.buying) {
                return;
            }
            $giftBuy.buying = true;
            var params = {
                count: $giftBuy.buyCount,
                buy_gift:1
            };

            NetWork.request('pay/get_info', params, function (data) {
                var $conf = $.parseJSON(data);

                // 保存预支付订单号
                if($conf && $conf.package){
                    var prepayId = ($conf.package.split('='))[1];

                    if (typeof(WeixinJSBridge) == 'undefined') {
                        $giftBuy.buying = false;
                        return;
                    }

                    if(WeixinJSBridge){
                        WeixinJSBridge.invoke(
                            'getBrandWCPayRequest', {
                                "appId": $conf.appId,     //公众号名称，由商户传入
                                "timeStamp": $conf.timeStamp,         //时间戳，自1970年以来的秒数
                                "nonceStr": $conf.nonceStr, //随机串
                                "package": $conf.package,
                                "signType": $conf.signType,         //微信签名方式：
                                "paySign": $conf.paySign //微信签名
                            },
                            function (res) {
                                // 检查错误码
                                if (res.err_code) {
                                    $giftBuy.buying = false;
                                    // 前端失败
                                    if (res.err_code == 3) {
                                        //取url类型(-1代表新的url)
                                        var urlType = window.location.hostname.indexOf(".h5.");
                                        // 跨号支付
                                        if (urlType === -1) {
                                            //新url，加appid
                                            $giftBuy.scanUrl = '/' + window.APPID + '/pay/scan_image/' + prepayId;
                                        }else {
                                            //旧url，不用加
                                            $giftBuy.scanUrl = '/pay/scan_image/' + prepayId;
                                        }

                                        // 跨号支付

                                        $('.scan_window_image').attr('src', $giftBuy.scanUrl);
                                        $('.scan_layer').css('display', 'block');
                                        $('.navigation_menu').css('display', 'none');
                                    } else {
                                        alert(res.err_code + " " + res.err_msg);
                                    }
                                } else if (res.err_msg == 'get_brand_wcpay_request:ok') {
                                    // 支付成功刷新一下
                                    var date = new Date();
                                    eJump('/gift_list?' + date.getTime(),true);
                                } else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                                    // 前端取消
                                    $giftBuy.buying = false;
                                } else {
                                    $giftBuy.buying = false;
                                }
                            }
                        );
                    }
                }
            });

        });
    },
    // 更新价格
    updatePrice: function () {
        $('#selector_show').val($giftBuy.buyCount);
        $('#total_price').html("￥" + ($giftBuy.buyCount * $giftBuy.piece_price).toFixed(2));
    }
};


$(function () {
    // 激活active伪类样式
    document.body.addEventListener('touchstart', function () {});
});
