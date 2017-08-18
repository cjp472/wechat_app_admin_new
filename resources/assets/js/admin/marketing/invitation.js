$(function () {

    invitation.init();

});

var invitation = function () {
    var invitation = {};

    var id, //商品id
        name, //商品名称
        price, //商品价格
        isInvite, //是否启用邀请卡
        goodType, //商品类型
        isShowInfo, //自定义邀请卡海报是否显示用户头像和昵称
        invitePoster, //邀请卡封面地址
        distributePercent; //奖励比例

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
                $uploadFile.uploadRes(file, resType, function (data) {
                        // if (resType == 'audio') {
                        //     var progress = parseInt(data * 100);
                        //     console.log(progress);
                        //     $(".uploadPLineActive" + resTypeClass).css("width", progress + '%');
                        //     $(".uploadPersent" + resTypeClass).text(progress + "%");
                        // }
                    },
                    // 上传成功回调
                    function (data) {
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
                                .load(function () {
                                    removeObjectURL(resourceLocalUrl);
                                })
                                .attr("src", resourceLocalUrl);
                        }

                    },
                    // 上传失败回调
                    function (data) {
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

    invitation.init = function () {
        // 点击按钮上传图片
        $("#uploadImage").on("change", function () {
            resUpload(this.files, "image", "reBackImg", 5);
        });

        // 删除上传图片
        $(".deleteImg1").click(function () {
            $("#reBackImg").attr("src", "");
            $("#imgUrl").val("");
            $("#uploadImage").val("");
        });

        //为keyListener方法注册按键事件
        document.onkeydown = keyListener;

        function keyListener(e) {
            // 当按下回车键，执行我们的代码
            if (e.keyCode == 13) {
                $(".goodsSearchBtn").click();
            }
        }

        //邀请卡设置
        $(".salerContent").on("click", ".inviteOperate", function () {
            id = $(this).data("id");
            name = $(this).data("name");
            price = $(this).data("price");
            isInvite = $(this).data("is_invite");
            goodType = $(this).data("good_type");
            invitePoster = $(this).data("inviteposter");
            isShowInfo = $(this).data("isshowinfo");
            distributePercent = $(this).data("distribute_percent");
            $(".saleGoodsName").text(name);
            $("#reBackImg").attr("src", invitePoster);
            $("#imgUrl").val(invitePoster);
            if (invitePoster != "") {
                $(".deleteImg").show();
                $(".deleteImg1").show();
            } else {
                $(".deleteImg").hide();
                $(".deleteImg1").hide();
            }
            if(isShowInfo==1){
                $('.showInfo').click();
            } else {
                $('.hideInfo').click();
            }
            // if(isInvite){
            //     $(".label1").click();
            //     $(".setPercent").show();
            // }else {
            //     $(".label2").click();
            //     $(".setPercent").hide();
            // }
            $(".inputRadioValue").val(distributePercent);
            if (price <= 1) {
                $(".inputRadioValue").attr("readonly", "readonly");
                $(".inputRadioValue").attr("disabled", "disabled");
                $(".inputRadioValue").addClass("inputDisabled");

            } else {
                $(".inputRadioValue").removeClass("inputDisabled");
                $(".inputRadioValue").removeAttr("readonly");
                $(".inputRadioValue").removeAttr("disabled");
            }
            $(".setSaleRatioWindow").fadeIn(200);
        });

        $(".setSaleRatioWindow").on("click", ".label1", function () {
            $(".setPercent").slideDown(300);
            $(".inputRadioValue").removeAttr("readonly");
            $(".inputRadioValue").removeAttr("disabled");
            $(".inputRadioValue").css("color", "#243042");
            isInvite = 1;
        });

        $(".setSaleRatioWindow").on("click", ".label2", function () {
            $(".setPercent").slideUp(300);
            $(".inputRadioValue").attr("readonly", "readonly");
            $(".inputRadioValue").attr("disabled", "disabled");
            $(".inputRadioValue").css("color", "#b2b2b2");
            $(".inputRadioValue").val("");
            isInvite = 0;
        });


        $(".setSaleRatioWindow").on("click", ".closeIcon,.cancelSaleBtn", function () {
            $(".setSaleRatioWindow").fadeOut(200);
        });

        //商品搜索
        $(".salerContent").on('click', '.goodsSearchBtn', function () {
            var goodSearchContent = $(".goodsSearchInput").val();
            window.location = '/invite/index?name=' + goodSearchContent;
        });

        $(".setSaleRatioWindow").on("click", ".confirmSaleBtn", function () {
            distributePercent = $(".inputRadioValue").val();
            invitePoster = $("#imgUrl").val();
            if ($("#showInfo").is(':checked')) {
                isShowInfo = 1;
            } else {
                isShowInfo = 0;
            }
            var inviteInfo = {
                id: id,   //商品id
                goods_type: goodType,   //商品类型
                distribute_percent: distributePercent,    //邀请比例
                invite_poster: invitePoster,    //邀请海报图片地址
                is_show_userinfo: isShowInfo,     //海报是否显示用户信息
            };

            if (isInvite && $formCheck.emptyString(inviteInfo.distribute_percent)) {
                baseUtils.show.blueTip("请输入要设置的比例！");
                return false;
            }

            /*console.log(price * (distributePercent/100))
            if(price * (distributePercent/100) < 0.01) {
                baseUtils.show.redTip("预计邀请奖励小于0.01元，请重新设置！");
                return false;
            }*/
            $.post('set', inviteInfo, function (data) {
                if (data.code === 0) {
                    baseUtils.show.blueTip("保存成功");
                    location.reload();
                } else {
                    baseUtils.show.redTip(data.msg);
                }
            });
        });


    };

    return invitation;
}();

function rateCheckNum1(value, dom) {
    //清除"数字"以外的字符
    value = value.replace(/[^\d]/g, "");

    value = value.replace(/^[5][1-9]$|^[6-9]\d$|^[1-9][0-9]\d{1,}$|^0\d{1,}$/, "");

    $(dom).val(value);
}
