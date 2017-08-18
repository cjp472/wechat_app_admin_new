$(function () {
    manageCommunity.init();
});


var manageCommunity = function () {

    var manageCommunity = {};

    var submitLimit = false;     // 表单提交限制标记

    manageCommunity.isCommunityShow = false;    //  社群是否上下架

    var fileSize,
        resAudio1Lengh;

    //获取地址栏参数
    function GetQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
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

    function removeObjectURL(url) {//释放资源URL
        if (window.revokeObjectURL != undefined) {
            window.revokeObjectURL(url);
        } else if (window.URL != undefined) {
            window.URL.revokeObjectURL(url);
        } else if (window.webkitURL != undefined) {
            window.webkitURL.revokeObjectURL(url);
        }
    }


    //检查社群提交表单
    function checkCommunityForm(info, singleSale, relevance,product_id) {
        if ($formCheck.emptyString(info.title)) {
            baseUtils.show.redTip("社群名称不能为空");
            return false;
        }
        if (info.title.length > 15) {
            baseUtils.show.redTip("社群名称不能超过15个字符");
            return false;
        }

        if ($formCheck.emptyString(info.describe)) {
            baseUtils.show.redTip("社群简介不能为空！");
            return false;
        }
        if (info.describe.length > 40) {
            baseUtils.show.redTip("社群简介不能超过40个字符");
            return false;
        }

        if ($formCheck.emptyString(info.img_url)) {
            baseUtils.show.redTip("请上传社群封面！");
            return false;
        }

        if (!singleSale && !relevance) {
            baseUtils.show.redTip("请选择至少一种加入形式");
            return false;
        }

        if (singleSale && $formCheck.emptyString(info.piece_price)) {
            baseUtils.show.redTip("请填入单卖价格！");
            return false;
        }
        if (singleSale && info.piece_price > baseUtils.maxInputPrice * 100) {
            baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
            return false;
        }

        if (relevance && product_id.length == 0) {
            baseUtils.show.redTip("请选择您要关联的专栏或会员！");
            return false;
        }

        return true;
    }

    //资源上传函数(参数：resType:资源类型,resTypeClass:资源类型中细分的种类,resLimitSize:资源限制大小)
    function resUpload(files, resType, resTypeClass, resLimitSize) {
        console.log(files);
        if (files && files.length > 0) {
            var file = files[0];
            var resourceLocalUrl = getObjectURL(file);
            fileSize = (file.size / (1024 * 1024)).toFixed(2);
            resAudio1Lengh = file.duration;
            // 限制资源在*MB内
            if ($uploadFile.checkFileSize(file, resLimitSize)) {
                $uploadFile.uploadRes(file, resType, function (data) {
                    },
                    // 上传成功回调
                    function (data) {
                        console.log(data);
                        baseUtils.show.blueTip("上传成功！");
                        resUrl = data.data.access_url;
                        console.log(resUrl);
                        $("#imgUrl").val(resUrl);
                        // 如果是图片资源，则展示图片预览
                        if (resType == 'image') {

                            var imgName = file.name;
                            //alert(imgName);
                            var ext,idx;
                            idx = imgName.lastIndexOf(".");
                            if (idx != -1){
                                ext = imgName.substr(idx+1).toUpperCase();
                                ext = ext.toLowerCase( );
                                //alert(file);
                                //alert("后缀="+ext+"位置="+idx+"路径="+resourceLocalUrl);
                                if (ext != 'jpg' && ext != 'png' && ext != 'jpeg' && ext != 'gif'){
                                    //document.all.submit_upload.disabled=true;
                                    baseUtils.show.blueTip("请上传图片类型的文件哦~");
                                    //alert("2.只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                                    return;
                                }
                            } else {
                                document.all.submit_upload.disabled=true;
                                baseUtils.show.blueTip("请上传图片类型的文件哦~");
                                //alert("只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                                return;
                            }

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

    manageCommunity.init = function () {

        // 点击按钮上传图片
        $("#uploadImage").on("change", function () {
            resUpload(this.files, "image", "reBackImg", 5);
        });

        //单卖设置
        $(".resAddSectionC").on("click", ".setFee", function () {
            if ($("#setFee").is(':checked')) {
                $("#setPrice").attr("readonly", "readonly");
                $("#setPrice").attr("disabled", "disabled");
            } else {
                $("#setPrice").removeAttr("readonly");
                $("#setPrice").removeAttr("disabled");
            }
        });

        //  关联部分的开启和关闭
        $(".resAddSectionC").on("click", ".setRelevance", function () {
            if ($("#setRelevance").is(':checked')) {
                $("#relevanceContent").fadeOut(200);
            } else {
                $("#relevanceContent").fadeIn(200);
            }
        });

        //关联内容切换
        $("#packageType").on("change", function () {
            if ($("#package").is(':selected')) {
                $("#memberSel").val("none");
                $(this).next().show();
                $(this).next().next().hide();
            } else {
                $("#packageSel").val("none");
                $(this).next().hide();
                $(this).next().next().show();
            }
        });

        //添加关联
        $("#newRelevance").click(function () {
            $("#newRelevance").hide();
            $(".selectRelevance").show();
        });

        //取消选中的关联
        $("#removeRelevance").click(function () {
            $("#newRelevance").show();
            $(".selectRelevance").hide();

            $("#packageType").val("package");
            $("#packageSel").val("none");
            $("#memberSel").val("none");
            $("#memberSel").hide()
            $("#packageSel").show();
        });

        //保存选中的关联
        $("#addRelevance").click(function () {
            var productId = $(".packageSelect:visible option:selected").data("product_id");

            for (var i = 0; i < $(".oneRelevance").length; i++) {       //判定是否重复关联

                var currentId = $(".oneRelevance").eq(i).data('product_id');
                if (productId == currentId) {
                    baseUtils.show.redTip('请勿重复选择');
                    return false;
                }
            }
            if ($(".packageSelect:visible").val() == 'none') {
                baseUtils.show.redTip('请选择您当前要关联的会员或专栏');

            } else {

                var FirstSelected = $("#packageType option:selected").text(),
                    SecondDetail = $(".packageSelect:visible option:selected").text(),
                    htmlString =
                    '<div class="oneRelevance clearfix" data-product_id="' + productId + '">' +
                        '<div class="packageClass">' + FirstSelected + '</div>' +
                        '<div class="packageName" title="' + SecondDetail + '">' + SecondDetail + '</div>' +
                        '<button type="cancel" class="xeBtnDefault btnMid cancelRelevance">取消关联</button>' +
                    '</div>';

                $("#relevanceContent").append(htmlString);

                $("#removeRelevance").click();

            }

        });


        //取消已关联的专栏或会员
        $(".secondCheckbox").on("click", ".cancelRelevance", function () {
            $(this).parent().remove();
        });


        //提交表单
        $(".base_mainContent").on("click", '#saveBtn', function () {

            if (submitLimit) {
                baseUtils.show.redTip("不能重复提交");

            } else {


                var pageType = $.trim($("#pageType").val()),    //  获取页面类型
                    title = $.trim($("#title").val()),          //  获取表单数据//  获取社群名称
                    describe = $.trim($("#intro").val()),       //  获取社群简介
                    img_url = $.trim($("#imgUrl").val()),       //  获取封面图片
                    singleSale = $("#setFee").is(':checked'),   //  获取单卖check状态
                    piece_price = $.trim($("#setPrice").val()) * 100;   //  获取单卖价格

                if (!singleSale) {
                    piece_price = null;
                }

                var relevance = $("#setRelevance").is(':checked');      //  获取关联check状态

                var product_id = [];    //  获取关联id
                if (relevance) {
                    for (var i = 0, j = 0; i < $(".oneRelevance").length; i++) {
                        //关联专栏和会员的id值
                        var idArray = {};
                        idArray[i] = $(".oneRelevance").eq(i).data('product_id');
                        if (idArray[i]) {
                            product_id[j] = idArray;
                            j++;
                        }
                    }
                } else {
                    product_id = null;
                }

                //  获取社群是否上架
                var isCommunityShow = $("input[name='isCommunityShow']:checked").val();

                //  社群信息

                if (pageType == 0) {    //  新建社群
                    var params = {
                        title: title,
                        describe: describe,
                        img_url: img_url,
                        piece_price: piece_price,
                        product_id: JSON.stringify(product_id),
                        community_state: isCommunityShow        //0 - 上架  1- 下架
                    }
                } else {
                    var id = GetQueryString("id");
                    var params = {
                        id: id,
                        title: title,
                        describe: describe,
                        img_url: img_url,
                        piece_price: piece_price,
                        product_id: JSON.stringify(product_id),
                        community_state: isCommunityShow
                    }
                }

                if (checkCommunityForm(params, singleSale, relevance, product_id)) {

                    submitLimit = true;     // 表单提交限制标记

                    if (pageType == 0) {
                        var ajaxUrl = 'uploadCommunity';
                    } else {
                        var ajaxUrl = 'updateCommunity';
                    }
                    console.log(ajaxUrl);
                    $.ajax(ajaxUrl, {
                        type: 'POST',
                        dataType: 'json',
                        data: params,
                        success: function (data) {
                            if (parseInt(data.code) == 0) {
                                    if (pageType == 1) {
                                        if(data.free == 1){

                                            var text = "将免费社群修改为付费社群，已加入的用户需要重新付费进入，或您可以生成邀请码邀请用户进入。";
                                            $.alert(text,'info',{
                                                oktext: '我知道了',
                                                btn: 2,
                                                onOk: function() {
                                                    window.location.href='/smallCommunity/communityList';
                                                },
                                                onClose:function () {

                                                    window.location.href='/smallCommunity/communityList';
                                                }
                                            });
                                            return false;
                                            // baseUtils.show.blueTip(");
                                        } else {
                                            baseUtils.show.blueTip("保存成功");
                                        }
                                    } else {
                                        baseUtils.show.blueTip("创建成功");
                                    }

                                if (data.is_new == 1) {
                                    window.location.href = '/smallCommunity/communityList?is_new=' + 1;
                                } else {
                                    window.location.href = '/smallCommunity/communityList';
                                }
                            } else {
                                submitLimit = false;
                                baseUtils.show.redTip(data.msg);
                            }
                        },
                        error: function (xhr, status, err) {
                            submitLimit = false;
                            console.error(err);
                            baseUtils.show.redTip('网络错误，请稍后再试！');
                        }
                    })
                }
            }

        });
    };

    return manageCommunity;
}();