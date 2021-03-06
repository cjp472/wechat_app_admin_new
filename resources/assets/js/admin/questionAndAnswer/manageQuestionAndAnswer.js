
$(document).ready(function () {
    manageQA.init();
});


var manageQA = function () {

    var manageQA = {};

    var submitLimit = false;        //提交限制

    manageQA.init = function () {

        $("#uploadImage").on("change", function () {
            resUpload(this.files, "image", 5);
        });


        $("#createQA").click(function () {

            if (submitLimit) {
                baseUtils.show.redTip("正在提交中，请稍后再试");
                return false;
            }

            var QA_title = $.trim($("#QA_title").val()),
                QA_summary = $.trim($("#QA_summary").val()),
                QA_cover_img = $.trim($("#imgUrl").val()),
                QA_eavesdrop_price = $.trim($("#eavesdropPrice").val()),

                sharerTrader = $.trim($("#sharerTrader").val()),
                sharerResponder = $.trim($("#sharerResponder").val()),
                sharerAskPerson = $.trim($("#sharerAskPerson").val()),

                isQAShow = $("input[name='isQAShow']:checked").val();

            if (QA_title.length == 0) {
                baseUtils.show.redTip("问答区名称不能为空");
                return false;
            }
            if (QA_summary.length == 0) {
                baseUtils.show.redTip("问答区简介不能为空");
                return false;
            }
            if (QA_cover_img.length == 0) {
                baseUtils.show.redTip("问答封面不能为空");
                return false;
            }
            if (QA_eavesdrop_price.length == 0) {
                baseUtils.show.redTip("偷听价格不能为空");
                return false;
            }
            if (QA_eavesdrop_price < 0.1) {
                baseUtils.show.redTip("偷听价格不能低于0.1元");
                return false;
            }
            if (QA_eavesdrop_price > 10000) {
                baseUtils.show.redTip("价格超出上限，偷听价格不可设置高于10000元");
                return false;
            }

            if (sharerTrader.length == 0 || sharerResponder.length == 0 || sharerAskPerson.length == 0) {
                baseUtils.show.redTip("偷听分成比例不能为空");
                return false;
            }

            var sum = +sharerTrader + +sharerResponder + +sharerAskPerson;
            if (sum != 100) {
                baseUtils.show.redTip("商家、答主、提问者三者分成总和必须等于100%");
                return false;
            }

            var data = {
                title:QA_title,
                desc:QA_summary,
                img_url:QA_cover_img,
                price:QA_eavesdrop_price * 100,
                state:isQAShow,
                listen_for_business:sharerTrader,
                listen_for_answer:sharerResponder,
                listen_for_questioner:sharerAskPerson
            }

            if($('#page_type').val() == 1) {    // 说明是编辑
                data.id = GetQueryString("id");

            }

            submitLimit = true;
            $.ajax("saveQuestionAndAnswer", {
                type: "POST",
                dataType: "json",
                data: data,
                success: function (data) {
                    if (data.code == 0) {
                        baseUtils.show.blueTip("保存成功！");
                        window.location.href = "/QA/questionAndAnswerDetail";
                    } else {
                        submitLimit = false;
                        baseUtils.show.redTip("保存失败，请稍后再试！");
                    }
                },
                error: function (xhr, status, err) {
                    submitLimit = false;
                    console.log(err);
                    baseUtils.show.redTip("网络错误，请稍后再试！");
                }
             });

        });

    };

    /**
     * 资源上传函数(参数：resType:资源类型,resTypeClass:资源类型中细分的种类,resLimitSize:资源限制大小)
     */
    function resUpload(files, resType, resLimitSize) {
        console.log(files);
        if (files && files.length > 0) {
            var file = files[0],
                resourceLocalUrl = getObjectURL(file);
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

            // 限制资源在*MB内
            if ($uploadFile.checkFileSize(file, resLimitSize)) {
                $uploadFile.uploadRes(file, resType, function (data) {
                    },
                    // 上传成功回调
                    function (data) {
                        console.log(data);
                        baseUtils.show.blueTip("上传成功！");
                        var resUrl = data.data.access_url;
                        console.log(resUrl);
                        $("#imgUrl").val(resUrl);
                        // 如果是图片资源，则展示图片预览
                        if (resType == 'image') {
                            //直接加载本地图片进行预览
                            $("#previewCoverImg")
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
                $("#upLoadImage").val("");
            }
        } else {
            baseUtils.show.redTip("网络错误，请稍后再试！");
        }
    };

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
    };

    function removeObjectURL(url) {//释放资源URL
        if (window.revokeObjectURL != undefined) {
            window.revokeObjectURL(url);
        } else if (window.URL != undefined) {
            window.URL.revokeObjectURL(url);
        } else if (window.webkitURL != undefined) {
            window.webkitURL.revokeObjectURL(url);
        }
    }

    return manageQA;
}();