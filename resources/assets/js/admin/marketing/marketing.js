/**
 * Created by jserk on 2017/3/30.
 */

var nowNavIndex = sessionStorage.nowNavIndex || 5;
var export_times = []; //导出excel的时间列表
$(function() {
    marketing.init();
    $('.salerNav .salerNavPart').eq(nowNavIndex).click();
});


//全局变量

var editorId = 0; //编辑器获取参数
var lastURL = {
    url: '',
    data: null
}; //用来保存上次页面的url

var marketing = function() {

    var goodsIdArr = [],    //新添加内容分销商品id值暂存
        existedGoodsIdArr = []; //已经添加到推广商城的商品id


    var marketing = {};

    //上一次选择的导航栏子页面
    var ele1 = 6;

    var $toggle = $('#salerToggle');
    var salerOpen = $toggle.data('toggle'); //推广员开关参数

    var $salerContent = $('#salerAllContent');


    //console.log(salerOpen);
    //参数
    var id; //商品id
    var title; //页面标题
    var period; //推广比例有效期
    var describ; //详情描述（图文分离）
    var orgContent; // 详情描述（html）
    var goodsType; //商品类型
    var goodsPrice; //商品价格
    var hasDistribute; //是否分销
    var distributeImgUrl; //推广海报
    var distributeDefault; //是否使用默认佣金比例
    var distributePercent; //当前佣金比例
    var distributeDefaultPercent; //默认佣金比例
    var isDistributeShowUserInfo;  //是否显示头像昵称
    var superiorDistributeDefault; //是否使用默认邀请比例
    var superiorDistributePercent; //当前邀请比例
    var distributeDefaultDefaultPercent; //默认邀请比例
    var midNum;//中间值


    //开关动画
    function salerSwitch() {
        if (salerOpen == 1) {
            salerOpen = 0;
            $toggle.removeClass('opening').addClass('closing');
            $toggle.find("span").text("关闭");
        } else {
            salerOpen = 1;
            $toggle.removeClass('closing').addClass('opening');
            $toggle.find("span").text("开启");
        }
        //console.log(salerOpen);
    }

    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) {
            url = window.createObjectURL(file);
        } else if (window.URL != undefined) {
            url = window.URL.createObjectURL(file);
        } else if (window.webkitURL != undefined) {
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }

    function removeObjectURL(url) { //释放资源URL
        if (window.revokeObjectURL != undefined) {
            window.revokeObjectURL(url);
        } else if (window.URL != undefined) {
            window.URL.revokeObjectURL(url);
        } else if (window.webkitURL != undefined) {
            window.webkitURL.revokeObjectURL(url);
        }
    }

    //检查提交表单
    function checkSetForm(info) {
        if ($formCheck.emptyString(info.persent)) {
            baseUtils.show.redTip("佣金比例不能为空！");
            return false;
        }

        if ($formCheck.emptyString(info.period)) {
            baseUtils.show.redTip("请设置推广有效期！");
            return false;
        }

        if (info.period>999) {
            baseUtils.show.redTip("有效期不能超过999天，请重新输入！");
            return false;
        }

        if (!$formCheck.checkNum(info.persent)) {
            baseUtils.show.redTip("您的佣金比例输入格式有误，请重新输入！");
            return false;
        }

        if ($formCheck.emptyString(info.superior_persent)) {
            baseUtils.show.redTip("邀请比例不能为空！");
            return false;
        }

        if (!$formCheck.checkNum(info.persent)) {
            baseUtils.show.redTip("您的邀请比例输入格式有误，请重新输入！");
            return false;
        }

        return true;
    }

    //copy初始化
    function initCopy(className) {
        var clipboard = new Clipboard('.' + className); //加载复制链接插件
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    }

    //为keyListener方法注册按键事件
    document.onkeydown = keyListener;

    function keyListener(e) {
        // 当按下回车键，执行我们的代码
        if (e.keyCode == 13) {
            $(".salerSetsubMit").click();
            $(".salerPlansubMit").click();
        }
    }

    //ajax请求
    function submitForm(url, info, callback) {
        $.post(url, info, function(data) {
            console.log(data);
            if (data.code == 0) {
                //baseUtils.show.blueTip(successWord);
                if (callback) callback.call(data);
            } else {
                baseUtils.show.redTip(data.msg);
            }
        })
    }

    //资源上传函数(参数：resType:资源类型,resTypeClass:资源类型中细分的种类,resLimitSize:资源限制大小)
    function resUpload(files, resType, resTypeClass, resLimitSize) {
        $(".loadingPartial").show();
        console.log(files);
        if (files && files.length > 0) {
            var file = files[0];
            var resourceLocalUrl = getObjectURL(file);
            fileSize = (file.size / (1024 * 1024)).toFixed(2);
            resAudio1Lengh = file.duration;
            // 限制资源在*MB内
            if ($uploadFile.checkFileSize(file, resLimitSize)) {
                $uploadFile.uploadRes(file, resType, function(data) {
                        // if (resType == 'audio') {
                        //     var progress = parseInt(data * 100);
                        //     console.log(progress);
                        //     $(".uploadPLineActive" + resTypeClass).css("width", progress + '%');
                        //     $(".uploadPersent" + resTypeClass).text(progress + "%");
                        // }
                    },
                    // 上传成功回调
                    function(data) {
                        $(".loadingPartial").hide();
                        console.log(data);
                        baseUtils.show.blueTip("上传成功！");
                        $(".deleteImg").show();
                        resUrl = data.data.access_url;
                        console.log(resUrl);
                        $("#imgUrl").val(resUrl);
                        // 如果是图片资源，则展示图片预览
                        if (resType == 'image') {
                            //直接加载本地图片进行预览
                            $("#reBackImg")
                                .load(function() {
                                    removeObjectURL(resourceLocalUrl);
                                })
                                .attr("src", resourceLocalUrl);
                        }

                    },
                    // 上传失败回调
                    function(data) {
                        $(".loadingPartial").hide();
                        console.error("上传失败!!!");
                        console.log(data);
                        baseUtils.show.redTip("上传失败！");
                    });
            } else {
                baseUtils.show.redTip("上传资源限制在" + resLimitSize + "MB内！");
                $(".upLoad" + resTypeClass).val("");

            }
        } else {
            baseUtils.show.redTip("网络错误，请稍后再试！");
            // console.log(files)
        }
    }
    function changeSelectorState(type) {
        $(".resourceSelector").addClass("hide");
        $(".resourceSelector[name=" + type + "]").removeClass("hide");
        $(".resourceSelector[name=" + type + "]").val("none");
    }
    function initChosenPage() {
        goodsIdArr = [];
        existedGoodsIdArr = [];
        $salerContent.find("._goodsItem").each(function () {
            var resId = $(this).data("res_id");
            existedGoodsIdArr.push(resId);
        });
    }

    marketing.init = function() {
        if(salerOpen == 0){
            $(".darkScreen2").fadeIn(200);
        }
        //关闭推广介绍框
        $(".welcomeBoxClose").click(function () {
            $(".darkScreen2").fadeOut(200);
        });
        // 点击按钮上传图片
        $("#uploadImage").on("change", function() {
            resUpload(this.files, "image", "reBackImg", 5);
        });

        // 删除上传图片
        $(".deleteImg").click(function () {
            $("#reBackImg").attr("src", "");
            $("#imgUrl").val("");
            $("#uploadImage").val("");
        });
        //console.log(salerOpen);
        $(".pageTopTitle span").click(function() {
            window.location = '/marketing';
        });

        //   推广员开关
        $(".salerSwitch").click(function() {

            $.ajax("/distribute/switch", {
                type: "POST",
                dataType: "json",
                data: {
                    switch: salerOpen == 1 ? 0 : 1,
                },
                success: function (result) {
                    if (result.code == 0) {
                        salerSwitch();
                        if (salerOpen == 0) {
                            $.alert("推广员功能已关闭，用户不可申请成为推广员，现有推广员不可继续推广商品", 'info', {
                                btn: 2,
                                oktext: '我知道了'
                            });
                        } else {
                            $.alert("推广员功能已开启，您可招募推广员帮助您推广商品", 'info', {
                                btn: 2,
                                oktext: '我知道了'
                            });
                        }
                    } else if (result.code == -4) {
                        baseUtils.show.redTip("已经开启小鹅通精选，推广员暂不支持关闭");
                    } else {
                        baseUtils.show.redTip("操作失败，请稍后再试");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    alert("服务器出小差了，请稍后再试！");
                }
             });

        });

        //   导航栏内容切换
        $(".salerNav").on("click", ".salerNavPart", function() {
            var ele = $(this),
                index = ele.index();
            if (ele != ele1) {
                $(".loadingS").fadeIn(200);
                ele1 = ele;
            }
            changeSaveFlag(false);
            $(".navLine").css("transform", "translateX(" + (index * 100 - 500) + "px)");
            var salerclassurl = $(this).data("classurl");
            timeRange = { //页面跳转后，清空搜索时间
                start: '',
                end: ''
            }
            sessionStorage.nowNavIndex = index;

            if (salerclassurl == '/distribute/goods') {
                getNewPage(salerclassurl, null, initGoodsList);
            } else if (salerclassurl == '/distribute/recruit') {
                getNewPage(salerclassurl, null, initPlan);
            } else if (salerclassurl == '/distribute/set') {
                getNewPage(salerclassurl);
            } /*else if (salerclassurl == '/distribute/chosen') { //内容分销
                getNewPage(salerclassurl, null, initChosenPage);
            }*/ else if (salerclassurl == '/distribute/saler') {
                if (sessionStorage.checkPage == 1) {
                    getNewPage('/distribute/audit', null, initSalerPage);
                    return;
                } else {
                    getNewPage(salerclassurl, null, initSalerPage);
                }
            } else {
                getNewPage(salerclassurl, null, initSalerPage);
            }

            sessionStorage.checkPage = 0;
        });

        $(".salerNavPart").click(function() {
            $(".salerNavPart").not(".salerNavChosen").css("color", "#353535");
            if ($(this).hasClass("salerNavChosen")) {
                $(".navLine").css({"background": "#ff6d28"});
            } else {
                $(this).css("color", "#2a75ed");
                $(".navLine").css({"background": "#2a75ed"});
            }
        });
        /*$('#salerAllContent').on('change', '#zBtn2', function() {
            var $ele = $(this);
            console.log($ele.prop('checked'))
            if($ele.prop('checked')){
                baseUtils.show.blueTip('关闭推广员招募功能后，用户无法申请成为推广员');
            }
        });*/
        //设置模块
        $salerContent.on("click", ".salerSetsubMit", function() {
            var hasRecruit, //推广员招募开关
                hasCheck, //是否审核开关
                invite, //是否可以邀请好友
                choose;
            if ($("#cBtn1").is(':checked')) {
                choose = 1;
            } else {
                choose = 0;
            }
            if ($("#zBtn1").is(':checked')) {
                hasRecruit = 1;
            } else {
                hasRecruit = 0;
            }
            if ($("#sBtn1").is(':checked')) {
                hasCheck = 1;
            } else {
                hasCheck = 0;
            }
            if ($("#fBtn1").is(':checked')) {
                invite = 1;
            } else {
                invite = 0;
            }
            period = $("#durationTime").val()
            distributePercent = $("#selfprize").val();
            distributeDefaultDefaultPercent = $("#inviteprize").val();
            var setInfo = {
                has_choose:choose,
                recruit: hasRecruit,
                audit: hasCheck,
                period: period,
                persent: distributePercent,
                superior_persent: distributeDefaultDefaultPercent,
                has_invite: invite
            }
            if (checkSetForm(setInfo)) {
                $.get('/distribute/judge/chosen/'+0+'/'+distributePercent,function(data) {
                    console.log(data);
                    if (data.code == 0) {
                        $.alert('根据规则，内容分销第一级分成比例不得低于推广员比例。如果更改，精选第一级分成比例将与推广员分成比例自动同步。', 'info', {
                            title: '提示',
                            icon: 'red',
                            btn: 3,
                            onOk: function () {
                                $(".loadingS").fadeIn(300);
                                submitForm("setEdit", setInfo, function () {
                                    //console.log(1);
                                    baseUtils.show.blueTip("保存成功！");
                                    //$(".loadingS").fadeOut(200);
                                    getNewPage("/distribute/set");
                                })
                            }
                        });
                    } else {
                        $(".loadingS").fadeIn(300);
                        submitForm("setEdit", setInfo, function () {
                            //console.log(1);
                            baseUtils.show.blueTip("保存成功！");
                            //$(".loadingS").fadeOut(200);
                            getNewPage("/distribute/set");
                        })
                    }
                })
            }
        });

        $salerContent.on("mouseover", ".rateTip1", function() {
            $(".rateTipContent1").fadeIn(300);
        });
        $salerContent.on("mouseleave", ".rateTip1", function() {
            $(".rateTipContent1").fadeOut(300);
        });
        $salerContent.on("mouseover", ".rateTip2", function() {
            $(".rateTipContent2").fadeIn(300);
        });
        $salerContent.on("mouseleave", ".rateTip2", function() {
            $(".rateTipContent2").fadeOut(300);
        });
        $salerContent.on("mouseover", ".rateTip3", function() {
            $(".rateTipContent3").fadeIn(300);
        });
        $salerContent.on("mouseleave", ".rateTip3", function() {
            $(".rateTipContent3").fadeOut(300);
        });
        $salerContent.on("click", ".rateTip2", function() {
            $(".rateTipContent2").toggle();
        });
        //招募计划模块
        $salerContent.on("click", ".copyHref", function() {
            initCopy('copyHref'); //加载复制链接插件
        });

        $salerContent.on('click', '.checkModelBtn', function() {
            $(".darkScreen1").fadeIn(200);
        });

        $(".darkScreen1").on('click', '.modalBoxCancle,.modalBoxClose', function() {
            $(".darkScreen1").fadeOut(200);
        });

        $(".darkScreen1").on('click', '.modalBoxConfirm', function() {
            ue.setContent($(".modalBoxContent").html());
            $(".darkScreen1").fadeOut(200);
        });

        $salerContent.on("click", ".salerPlansubMit", function() {
            title = $(".planNameInput").val();
            //原始内容详情
            var ue = UE.getEditor("resource_desc" + editorId);
            orgContent = ue.getContent();
            describ = ue.getPlainTxt();
            var planInfo = {
                title: title,
                descrb: describ,
                content: orgContent,
            }
            if (describ.length == 0) {
                baseUtils.show.redTip("详情描述不能为空！");
                return false;
            }
            if ($formCheck.emptyString(title)) {
                baseUtils.show.redTip("页面名称不能为空！");
                return false;
            } else {
                $(".loadingS").fadeIn(300);
                submitForm("recruitEdit", planInfo, function() {
                    baseUtils.show.blueTip("保存成功！");
                    $(".loadingS").fadeIn(200);
                    getNewPage("/distribute/recruit", null, initPlan);
                })
            }
        });

        // 商品列表模块

        //操作
        $salerContent.on('click', '.salerGoodsOperate', function() {
            id = $(this).data("id");
            goodsType = $(this).data("good_type");
            goodsPrice = $(this).data("price");
            hasDistribute = $(this).data("has_distribute");
            distributeDefault = $(this).data("distribute_default");
            distributePercent = $(this).data("distribute_percent");
            distributeDefaultPercent = $("#distributePercent").val();
            isDistributeShowUserInfo = $(this).data("is_distribute_show_userinfo");
            superiorDistributeDefault = $(this).data("superior_distribute_default");
            superiorDistributePercent = $(this).data("superior_distribute_percent");
            distributeImgUrl = $(this).data("distribute_poster");
            distributeDefaultDefaultPercent = $("#superiorDistributePercent").val();
            $(".yjrate").text(distributeDefaultPercent);
            $(".selfRateInput").val(distributePercent);
            $("#reBackImg").attr("src", distributeImgUrl);
            $("#imgUrl").val(distributeImgUrl);
            $("#reBackImg").val(distributeImgUrl);
            //console.log(distributePercent);
            $(".yqrate").text(distributeDefaultDefaultPercent);
            $(".otherRateInput").val(superiorDistributePercent);
            if(distributeImgUrl!=""){
                $(".deleteImg").show();
            }else{
                $(".deleteImg").hide();
            }
            //判断是否推广
            if (hasDistribute == 1) {
                //console.log("推广");
                $(".distributeY").click();
            } else {
                //console.log("不推广");
                $(".distributeN").click();
            }
            //判断佣金比例
            if (distributeDefault == 0) {
                $(".yjBtn1").click();
            } else {
                $(".yjBtn2").click();
            }

            //判断奖金比例
            if (superiorDistributeDefault == 0) {
                $(".yqBtn1").click();
            } else {
                $(".yqBtn2").click();
            }
            if (distributeDefaultPercent <= 0) {
                $.alert("您还未设置默认佣金比例，暂时无法修改", "custom", {
                    btn: 3,
                    icon: "red",
                    oktext: '立即设置',
                    onOk: function() {
                        $(".salerNavSet").click();
                    }
                });
            } else {
                $(".darkScreen").fadeIn(300);
            }
            //判断是否显示推广卡头像昵称
            if(isDistributeShowUserInfo==1){
               $("#showInfo").click();
            } else{
                $("#hideInfo").click();
            }
        });

        //关闭比例设置弹出框
        $(".darkScreen").on('click', '.setBoxClose,.salerBoxCancel', function() {
            $(".darkScreen").fadeOut(200);
            $(".deleteImg").hide();
        });

        $(".darkScreen").on('click', '.distributeY', function() {
            hasDistribute = 1;
        });

        $(".darkScreen").on('click', '.distributeN', function() {
            hasDistribute = 0;
        });

        $(".darkScreen").on('click', '.yjBtn1', function() {
            $(".selfRateInput").attr("readonly", "readonly");
            $(".selfRateInput").attr("disabled", "disabled");
            $(".yjBtn1").css("color", "#243042");
            $(".yjBtn2").css("color", "#b2b2b2");
            $(".selfRateInput").css("color", "#b2b2b2");
            distributeDefault = 0;
        });

        $(".darkScreen").on('click', '.yjBtn2', function() {
            $(".selfRateInput").removeAttr("readonly");
            $(".selfRateInput").removeAttr("disabled");
            $(".yjBtn1").css("color", "#b2b2b2");
            $(".yjBtn2").css("color", "#243042");
            $(".selfRateInput").css("color", "#243042");
            distributeDefault = 1;
        });

        $(".darkScreen").on('click', '.yqBtn1', function() {
            $(".otherRateInput").attr("readonly", "readonly");
            $(".otherRateInput").attr("disabled", "disabled");
            $(".yqBtn1").css("color", "#243042");
            $(".yqBtn2").css("color", "#b2b2b2");
            $(".otherRateInput").css("color", "#b2b2b2");
            superiorDistributeDefault = 0;
        });

        $(".darkScreen").on('click', '.yqBtn2', function() {
            $(".otherRateInput").removeAttr("readonly");
            $(".otherRateInput").removeAttr("disabled");
            $(".yqBtn1").css("color", "#b2b2b2");
            $(".yqBtn2").css("color", "#243042");
            $(".otherRateInput").css("color", "#243042");
            superiorDistributeDefault = 1;
        });

        //商品设置保存
        $(".darkScreen").on('click', '.salerBoxConfirm', function() {
            distributePercent = $(".selfRateInput").val();
            superiorDistributePercent = $(".otherRateInput").val();
            distributeImgUrl = $("#imgUrl").val();
            if ($("#showInfo").is(':checked')) {
                isDistributeShowUserInfo = 1;
            } else {
                isDistributeShowUserInfo = 0;
            }
            goodsListInfo = {
                id: id,
                goods: goodsType,
                has_distribute: hasDistribute,
                default: distributeDefault,
                persent: distributePercent,
                distribute_poster: distributeImgUrl,
                superior_default: superiorDistributeDefault,
                superior_persent: superiorDistributePercent,
                is_distribute_show_userinfo:isDistributeShowUserInfo
            };
            if (distributeDefault == 1 && $formCheck.emptyString(goodsListInfo.persent)) {
                baseUtils.show.redTip("请输入要设置的比例！");
                return false;
            }
            if (superiorDistributeDefault == 1 && $formCheck.emptyString(goodsListInfo.superior_persent)) {
                baseUtils.show.redTip("请输入要设置的比例！");
                return false
            }
            if(!!hasDistribute){//参与推广才判断佣金
                if( distributeDefault == 1 && (goodsPrice * (distributePercent/100) < 0.01) ) {
                    baseUtils.show.redTip("预计佣金小于0.01元，请重新设置！");
                    return false;
                }
                if( distributeDefault != 1 && (goodsPrice * (distributeDefaultPercent/100) < 0.01) ) {
                    baseUtils.show.redTip("预计佣金小于0.01元，请重新设置！");
                    return false;
                }
            }
            if(distributeDefault == 1){
                midNum = 0;
            }else{
                midNum = 1;
            }
            $.get('/distribute/judge/chosen/'+midNum+'/'+distributePercent,function(data){
                console.log(data);
                if(data.code == 0){
                    $.alert('根据规则，内容分销第一级分成比例不得低于推广员比例。如果更改，精选第一级分成比例将与推广员分成比例自动同步。','info',{
                        title:'提示',
                        icon:'red',
                        btn:3,
                        onOk:function(){
                            $.post('goodsSetting', goodsListInfo, function(data) {
                                if (data.code == 0) {
                                    baseUtils.show.blueTip("保存成功");
                                    $(".darkScreen").fadeOut(300);

                                    if (lastURL.url.indexOf('goods') >= 0) {
                                        getNewPage(lastURL.url, lastURL.data, initGoodsList);
                                    } else {
                                        getNewPage('/distribute/goods', null, initGoodsList);
                                    }
                                } else {
                                    baseUtils.show.redTip(data.msg);
                                }
                            })
                        }
                    });
                }else{
                    $.post('goodsSetting', goodsListInfo, function(data) {
                        if (data.code == 0) {
                            baseUtils.show.blueTip("保存成功");
                            $(".darkScreen").fadeOut(300);

                            if (lastURL.url.indexOf('goods') >= 0) {
                                getNewPage(lastURL.url, lastURL.data, initGoodsList);
                            } else {
                                getNewPage('/distribute/goods', null, initGoodsList);
                            }
                        } else {
                            baseUtils.show.redTip(data.msg);
                        }
                    })
                }
            })

        });

        $salerContent.on('click', '#showExcel', function() { //订单导出
            //showLoading();
            var ele = $(this),
                
                url = ele.data('url');
                // console.log(url);
            
            if(url=="/distribute/excel/records"){
                $("#myModalLabel").text("导出推广记录");
                $("#timeIsGood").text("订单产生时间");

            }else if(url=="/distribute/excel/achieve"){
                $("#myModalLabel").text("导出业绩统计");
                $("#timeIsGood").text("统计时间范围");
            }
            // 导出Excel的时间
            var timeStr = '',
                $exTime = $('#export_time');

            $.ajax('/distribute/date', {
                type: 'GET',
                dataType: 'json'
            }).done(function(json) {
                // console.log(json);

                $.each(json, function(i, item) {
                    timeStr += '<option value="' + this.yearMonth + '">' + this.yearMonth + '</option>';
                });

                $exTime.html(timeStr);
                //hideLoading();
                $("#ExportModal").modal('show');

                $("#exportExcel").off('click').on('click', function() {
                    var chkObjs=null; 
                    var Office2003=0;
                    var obj=document.getElementsByName("selectOffice");                
                    for (var i=0;i<obj.length;i++){ //遍历Radio 
                        if(obj[i].checked){ 
                            chkObjs=obj[i].value;
                                if(chkObjs==1){
                                    Office2003=2003;
                                }
                         } 
                    } 
                    // alert(Office2003);
                    window.location.href = url + '?export_time=' + $exTime.val() + "&&version=" + Office2003;
                    // hideLoading();
                    // $("#ExportModal").modal('hide');
                });
            }).fail(function(xhr, status, err) {
                console.error(err);
                baseUtils.show.redTip('订单时间初始化错误，请重试！');
            });
        });

        $salerContent.on('click', ' .pagination a', function(e) { //分页请求
            var ele = $(e.target),
                url = ele.data('url');
            //$salerContent.html('');
            // $(".loadingS").fadeIn(200);
            //getNewPage(url);
            // showLoading();
            if (url.indexOf('/distribute/achieve') >= 0) {
                getNewPage(url, null, initSalerPage);
            } else if (url.indexOf('/distribute/records') >= 0) {
                getNewPage(url, null, initSalerPage);
            } else if (url.indexOf('/distribute/saler') >= 0) {
                getNewPage(url, null, initSalerPage);
            } else if (url.indexOf('/distribute/audit') >= 0) {
                getNewPage(url, null, initSalerPage);
            } else if (url.indexOf('/distribute/goods') >= 0) {
                getNewPage(url, null, initGoodsList);
            } else {
                getNewPage(url, null, initPageHref);
            }
        });

        $salerContent.on('click', '#searchBtn', function(e) { //搜索
            var $ele = $(e.target),
                url = $ele.data('url'),
                phone = $.trim($('#phone').val()),
                searchData = {
                    phone: phone,
                    start_time: timeRange.start,
                    end_time: timeRange.end
                };

            //showLoading();
            $(".loadingS").fadeIn(200);
            if (url == '/distribute/achieve') { //业绩统计
                getNewPage(url, searchData, initSalerPage);
            } else if (url == '/distribute/records') { //推广记录
                getNewPage(url, searchData, initSalerPage);
            } else if (url == '/distribute/saler') { //推广员
                getNewPage(url, searchData, initSalerPage);
            } else if (url == '/distribute/audit') { //审核
                searchData.status = $.trim($('#checkStatus').val());
                getNewPage(url, searchData, initSalerPage);
            } else if (url == '/distribute/goods') { //商品列表
                getNewPage(url, {
                    name: phone
                }, initGoodsList);
            } else {
                getNewPage(url, searchData, initPageHref);
            }
        });

        $salerContent.on('click', '#myTabs li a', function(e) { //推广员页面切换
            var $ele = $(e.target),
                url = $ele.data('url');
            if (!$ele.parent().hasClass('active')) {
                getNewPage(url, null, initSalerPage);

                if (url == '/distribute/saler') { //推广员
                    sessionStorage.checkPage = 0;
                }
                if (url == '/distribute/audit') { //审核
                    sessionStorage.checkPage = 1;
                }
            }
        });
        $salerContent.on('keyup','#in1',function () {
            var temp = parseInt($('#in1').val()) + 1;
            if(temp > 0)
            $('#in2').val(temp);

            console.log(temp);

            });
        $salerContent.on('keyup','#in3',function () {
            var temp = parseInt($('#in3').val()) + 1;
            if(temp > 0)
                $('#in4').val(temp);

            console.log(temp);

        });

        /*********************** 内容分销 *************************/

        $salerContent.on('click', '#cBtn1', function() { //梯度比例提交 <参与上架>
            var isOpen = $("#salerToggle").hasClass("opening");
            if (isOpen) {
                var isChecked = $(this).data("is_checked");
                if (isChecked == "no") {
                    enableChosen(1);
                }
            } else {
                $.alert("您未开启推广员总开关，无法参与上架小鹅通精选。请在页面右上角开启总开关后勾选“参与上架”按钮。", "info", {btn: 2});
                $('#salerAllContent').find("._forbidChosenShop").prop("checked", true);
            }
        });

        $salerContent.on('click', '.radioBtn2', function() { //梯度比例提交 <暂不参与> <先暂时隐藏该功能。>
            baseUtils.show.redTip("上架设置开启后，暂不支持撤销。");
        });

        $salerContent.on("click", "#_addGoodsOperate", function () {
            if (goodsIdArr.length + existedGoodsIdArr.length >= 20) {
                baseUtils.show.redTip("您已经添加20个商品，不能继续添加");
                return false;
            } else {
                $(this).hide();
                $("#_selectGoodsSelector").show();
            }
        });
        $salerContent.on("click", "#cancelSelectGoods", function () {
            $("#_addGoodsOperate").show();
            $("#_selectGoodsSelector").hide();
            $("#resTypeSel").val("package");
            changeSelectorState("package");
        });
        //选择器联动
        $salerContent.on("change", "#resTypeSel", function () {
            var resType = $(this).val();
            changeSelectorState(resType);
        });

        $salerContent.on("mouseenter", "#_selectGoodsContent", function(){
            $('body').css('overflow', 'hidden');
        });
        $salerContent.on("mouseleave", "#_selectGoodsContent", function(){
            $('body').css('overflow', 'auto');
        });

        $salerContent.on("click", "#addSelectGoods", function () {
            var $target = $(".resourceSelector:visible>option:selected"),
                value = $target.val();

            if (value == "none") {
                baseUtils.show.redTip("请先选择具体的推广商品");
                return false;
            }
            var resId = $target.data("res_id"),
                resType = $target.data("res_type"),
                resName = $target.text();

            if (goodsIdArr.indexOf(resId) != -1 || existedGoodsIdArr.indexOf(resId) != -1) {
                baseUtils.show.redTip("该商品已经存在了，请选择其他商品。");
                return false;
            }
            if (goodsIdArr.length + existedGoodsIdArr.length >= 20) {
                baseUtils.show.redTip("您已经添加20个商品，不能继续添加");
                return false;
            }
            $.ajax("/distribute/addResourceChosen", {
                type: "POST",
                dataType: "json",
                data: {
                    id: resId,
                    type: resType
                },
                success: function (result) {
                    if (result.code == 0) {
                        switch (resType) {
                            case 1:
                                resType = "图文";
                                break;
                            case 2:
                                resType = "音频";
                                break;
                            case 3:
                                resType = "视频";
                                break;
                            case 4:
                                resType = "直播";
                                break;
                            case 5:
                                resType = "专栏";
                                break;
                            case 6:
                                resType = "会员";
                                break;
                            default:
                                break;
                        }

                        var htmlStr =
                            '<div class="_goodsItem" title="' + resName + '" data-res_id="' + resId + '">' + resType + '：' + resName + '</div>';

                        $("#_selectGoodsContent").show();
                        $("#_selectGoodsContent").append(htmlStr);
                        goodsIdArr.push(resId);
                        if (goodsIdArr.length + existedGoodsIdArr.length == 1) {
                            $("#salerAllContent").find("#_ratioSetSection").removeClass("hide");

                            $.alert("添加成功。您最多可以添加20个商品，且商品添加后暂时不支持删除，请谨慎选择。您可以继续添加商品或者设置梯度分成。", "info", {
                                btn: 2,
                            });
                        } else {
                            baseUtils.show.blueTip("商品添加成功");
                        }
                        $("._selectGoodsDesc>span").text(goodsIdArr.length + existedGoodsIdArr.length);

                        $("#_addGoodsOperate").show();
                        $("#_selectGoodsSelector").hide();

                    } else if (result.code == 2) {
                        baseUtils.show.blueTip("该商品已经存在了，请选择其他商品。");
                    } else {
                        baseUtils.show.blueTip("网络失败，请稍后再试。");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    alert("服务器出小差了，请稍后再试！");
                }
             });


        });

        $salerContent.on('click', '#editGradientValue', function() { //编辑梯度比例
            var isEnableEdit = $(this).data("is_enable_edit");
            if (isEnableEdit == "yes") {
                $('#showRatioSet').hide();
                $('#editRatioSet').show();
            } else {
                baseUtils.show.redTip("暂不可编辑");
            }
        });
        $salerContent.on('click', '#cancelEditGradientValue', function() { //取消编辑梯度比例
            $('#showRatioSet').show();
            $('#editRatioSet').hide();
        });
        $salerContent.on("click", "#_saveGradientValue", function () {      //保存梯度编辑
            var in1  = parseInt($('#in1').val()),
                in2  = parseInt($('#in2').val()),
                in3  = parseInt($('#in3').val()),
                in4  = parseInt($('#in4').val()),
                per1 = parseInt($('#per1').val()),
                per2 = parseInt($('#per2').val()),
                per3 = parseInt($('#per3').val());

            if((( in1 + 1) != in2) || (( in3 + 1) != in4) || (in2 >= in3) || (in1 <= 1)){
                baseUtils.show.redTip('区间填写有误，请检查后重试');
                return false;
            }
            if((!per1) || (!per2) || (!per3) || (per1 <= 0) || (per1 > per2) || (per2 > per3)){
                baseUtils.show.redTip('分成比例填写有误，请参考示例');
                return false;
            }
            if (per1>99 || per2 > 99 || per3 > 99) {
                baseUtils.show.redTip("分成比例不能超过100%");
                return false;
            }
            var params = {},
                isEdit = $("#distributeData").val();
            if (isEdit == "edit") {
                var id1 = $("#distributeData").data("id1"),
                    id2 = $("#distributeData").data("id2"),
                    id3 = $("#distributeData").data("id3");

                params = {
                    end_order_num1: in1,
                    start_order_num2: in2,
                    end_order_num2: in3,
                    start_order_num3: in4,
                    distribute_percent1: per1,
                    distribute_percent2: per2,
                    distribute_percent3: per3,
                    edit:'edit',
                    distribute_id1: id1,
                    distribute_id2: id2,
                    distribute_id3: id3
                }
            } else {
                params = {
                    end_order_num1: in1,
                    start_order_num2: in2,
                    end_order_num2: in3,
                    start_order_num3: in4,
                    distribute_percent1: per1,
                    distribute_percent2: per2,
                    distribute_percent3: per3,
                }
            }
            $.alert("保存完成后3个月内暂时不能进⾏修改，确认保存设置？", "info", {
                btn: 3,
                onOk: function () {
                    submitChosenShopData(params);
                }
            });


        });


    };

    return marketing;
}();

/**
 * 设置小鹅通精选上传是否上架ajax请求
 * @param is_enable_chosen          1 - 参与上架 ;       0 - 暂不参与
 */
function enableChosen(is_enable_chosen) {
    $(".loadingS").fadeIn(100);
    $.ajax({
        type: "GET",
        url: 'chosen_enable',
        dataType: "json",
        data: {is_enable_chosen:is_enable_chosen},
        success: function(data) {
            $(".loadingS").fadeOut(300);
            if(data.code == 0) {
                if (is_enable_chosen == 1) {
                    baseUtils.show.blueTip("参与上架成功。");
                    $('#salerAllContent').find("._forbidChosenShop").attr("disabled", "disabled");
                    $('#salerAllContent').find("._enableChosenShop").data("is_checked", "yes");
                    $("#salerAllContent").find(".radioBtn2").addClass("disabledInput");
                    $("#salerAllContent").find("#_selectGoodsSection").removeClass("hide");
                }
            }
            else {
                baseUtils.show.redTip('服务器出小差了 请稍后再试！');
            }
        },
        error: function(xhr, status, err) {
            $(".loadingS").fadeOut(300);
            console.log(xhr);
            baseUtils.show.redTip('网络错误，请稍后再试！');
        }
    })
}


//提交小鹅通数据
function submitChosenShopData(params) {

    $(".loadingS").fadeIn(100);
    $.ajax("/distribute/set_xiaoe_distribute", {
        type: "POST",
        dataType: "json",
        data: params,
        success: function (result) {
            $(".loadingS").fadeOut(300);
            if (result.code == 0) {
                baseUtils.show.blueTip('保存成功，梯度设置将实时生效');
                $.alert("设置完成，我们会尽快审核您的商品。审核通过后，运营人员会在第一时间与您取得联系。", "info", {
                    btn: 2,
                    onOk: function () {
                        $(".salerNav>.salerNavChosen").click();
                    },
                    onClose: function () {
                        $(".salerNav>.salerNavChosen").click();
                    }
                });
            } else {
                baseUtils.show.redTip('服务器出小差了 请稍后再试！');
            }
        },
        error: function (xhr, status, err) {
            $(".loadingS").fadeOut(300);
            console.log(err);
            alert("服务器出小差了，请稍后再试！");
        }
     });

}

//发送请求，获取新的页面
function getNewPage(salerclassurl, sendData, callback, args) {
    lastURL = {
        url: salerclassurl,
        data: sendData
    }
    $.ajax({
        type: "GET",
        url: salerclassurl,
        dataType: "html",
        data: sendData,
        success: function(data) {
            //hideLoading();
            $('#salerAllContent').html(data);
            $(".loadingS").fadeOut(300);
            if (callback) {
                callback.apply(window, args);
            }
        },
        error: function(xhr, status, err) {
            console.log(xhr);
            console.error(err);
            console.error(status);
            baseUtils.show.redTip('网络错误，请稍后再试！');
            //hideLoading();
            $(".loadingS").fadeOut(300);
        }
    })
}

function CheckNumGreaterThanZero(value, dom) {
    //清除"数字"以外的字符
    value = value.replace(/[^\d]/g, "");

    value = value.replace(/^[-][1-9]d*$/, "");

    value = value.replace(/^[0]d*$/, "");

    $(dom).val(value);
};

function rateCheckNum(value, dom) {
    //清除"数字"以外的字符
    value = value.replace(/[^\d]/g, "");

    value = value.replace(/^[5][1-9]$|^[6-9]\d$|^[1-9][0-9]\d{1,}$|^0\d{0,}$/, "");

    $(dom).val(value);
};

function rateCheckNum1(value, dom) {
    //清除"数字"以外的字符
    value = value.replace(/[^\d]/g, "");

    value = value.replace(/^[5][1-9]$|^[6-9]\d$|^[1-9][0-9]\d{1,}$|^0\d{1,}$/, "");

    $(dom).val(value);
};


function updateTime(time) {
    $('#startTime').val(time.start);
    $('#endTime').val(time.end);
    if (time.start && time.start != '2016') {
        $('#SelectData').text(time.start + ' ~ ' + time.end);
    } else {
        $('#SelectData').text('全部时间');
    }
}

function initTimeSelect() { //初始化时间选择插件
    var dataRangeInstance = new pickerDateRange('SelectData', { //初始化时间插件
        isTodayValid: true,
        defaultText: ' ~ ',
        inputTrigger: 'optional',
        theme: 'ta',
        success: function(obj) {
            timeRange = {
                start: obj.startDate,
                end: obj.endDate
            }
            updateTime(timeRange);
        }
    });
    $('#SelectRange').on('click', 'li', function(e) {
        var ele = $(this),
            type = ele.data('type'),
            text = ele.text();
        if (type == 'all') {
            timeRange = {
                start: '',
                end: ''
            };
        } else if (type == 'nowMonth') {
            timeRange = {
                start: getNowMonth(),
                end: getNowDay()
            };
        }
        updateTime(timeRange);
    });
    $('#optional').click(function() { //时间选择器下拉
        $('#dropdown-toggle').dropdown('toggle');
    });
    $('#SelectData').off('click').text('全部时间'); //设置开始结束时间

}

function initPageHref() { //初始化分页
    $('#salerAllContent .list-page .pagination li a').each(function() {
        var $ele = $(this),
            url = $ele.attr('href');
        url = url.replace(/http(s)*:\/\/[\w\.-]*/, ''); //将链接替换为相对路径
        //console.log(url);
        $ele.attr('data-url', url);
        $ele.removeAttr('href');
    });
}

function initSalerPage() {
    initTimeSelect();
    initPageHref();
    updateTime(timeRange);
}

function initGoodsList() {
    initPageHref();
}

function initPlan() {
    $(".loadingS").fadeOut(200);
    ++editorId;
    $(".salerDescribe").html("<script id='resource_desc" + editorId + "' type='text/plain'>" + "</" + "script>")
    $(function() {
        //console.log(editorId);
        ue = UE.getEditor('resource_desc' + editorId, ueditor_config);
        if ($("#rubbish").length > 0) {
            ue.ready(function() {
                ue.setContent($("#rubbish").val());
            });
        }
        //编辑器预览
        $('#preview').on('click',function(){
            var html = ue.getContent();
            document.getElementById('preview_content').innerHTML = html;
            $('.preview_con').addClass('active');
            $('.preview_box').addClass('active');
            document.documentElement.style.overflow = "hidden";
        });

        $('.preview_con').on('click',function(){

            $(this).removeClass('active');
            $('#preview_content').html('');
            $('.preview_box').removeClass('active');
            document.documentElement.style.overflow = "auto";
        });
    });
}

//审核列表
/*function showCheckBox(ele) {//显示弹出
    var $ele = $(ele),
        $alert = $ele.next();
    $alert.fadeIn(function() {
        $(document).on('click.check', function(e) {
            if( !($(e.target).closest($alert).length > 0) ) {
                $alert.fadeOut();
                $(document).off('click.check');
            }
        });
    });
}*/

/*function hideCheckBox(ele) {//隐藏弹出
    $(ele).parent().fadeOut();
    $(document).off('click.check');
}*/
/*
function checkUser(id) {//审核确认
    console.log(id);
    var status = $('input[name='+id+']:checked').val();
    if(status == 1) {
        rejectSaler(id);
    } else if(status == 2) {
        handleSaler({id:id, status:2}, status);
    }
}*/
function agreeSaler(id) { // 通过操作的弹出
    var text = '是否同意该用户成为推广员？';
    $.alert(text, 'info', {
        onOk: function() {
            handleSaler({
                id: id,
                status: 2
            });
        }
    });
}

function rejectSaler(id) { //拒绝操作的弹窗
    $('#disAgreeModal').modal('show');
    $('#rejectBtn').on('click.reject', function() {
        var rejectReason = $.trim($('#reject_reason').val());
        if (rejectReason != '') {
            $('#disAgreeModal').modal('hide');
            handleSaler({
                id: id,
                status: 1,
                reject_reason: rejectReason
            });
            $('#rejectBtn').off('click.reject');
        } else {
            baseUtils.show.redTip('拒绝理由不能为空!');
            return false;
        }
    });
}

function handleSaler(data) {
    //审核： 1表示拒绝，2表示通过
    $(".loadingS").fadeIn(200);
    $.ajax('/distribute/auditing', {
        type: 'GET',
        data: data,
        dataType: 'json',
        success: function(json) {
            var status = data.status;
            if (json.code == 0) {
                if (status == 1) {
                    baseUtils.show.blueTip('审核已拒绝');
                } else if (status == 2) {
                    baseUtils.show.blueTip('审核已通过');
                }
                if (lastURL.url.indexOf('audit') >= 0) {
                    getNewPage(lastURL.url, lastURL.data, initSalerPage);
                } else {
                    getNewPage('/distribute/audit', null, initSalerPage);
                }
            } else {
                $(".loadingS").fadeOut(200);
                baseUtils.show.redTip(json.msg);
            }
        },
        error: function(xhr, sta, err) {
            console.error(err);
            $(".loadingS").fadeOut(200);
            baseUtils.show.redTip('网络错误，请稍后再试！');
        }
    });
}

//清退操作
function clearSaler(id) {
    var txt = '清退后该推广员将不可参与商品推广，确定要清退该推广员吗？';
    $.alert(txt, 'error', {
        title: '',
        btn: 3,
        onOk: function() {
            $(".loadingS").fadeIn(200);
            $.ajax('/distribute/salerdel', {
                type: 'GET',
                data: {
                    user_id: id
                },
                dataType: 'json',
                success: function(json) {
                    if (json.code == 0) {
                        baseUtils.show.blueTip('清退成功');
                        if (lastURL.url.indexOf('saler') >= 0) {
                            getNewPage(lastURL.url, lastURL.data, initSalerPage);
                        } else {
                            getNewPage('/distribute/saler', null, initSalerPage);
                        }
                    } else {
                        $(".loadingS").fadeOut(200);
                        baseUtils.show.redTip(json.msg);
                    }
                },
                error: function(xhr, status, err) {
                    console.error(err);
                    $(".loadingS").fadeOut(200);
                    baseUtils.show.redTip('网络错误，请稍后再试！');
                }
            });
        }
    });
}
