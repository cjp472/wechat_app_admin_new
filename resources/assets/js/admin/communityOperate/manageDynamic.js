/**
 * Created by Administrator on 2017/5/2.
 */

$(document).ready(function () {

    Business.init();
})

var Business = (function () {

    var Business = {};

    var isPdfFileUploading = false;

    Business.submitLimit = false;   //提交限制
    Business.communityId = -1;
    Business.type = -1;             //  1 - 编辑 ； 0 - 新建
    Business.dynamicId = -1;

    Business.init = function () {

        //点击侧边栏离开时的弹框
        changeSaveFlag(true);

        Business.type = $("#admin_data").data("type");
        Business.communityId = $("#admin_data").data("community_id");
        if (Business.type == 1) {
            Business.dynamicId = GetQueryString("id");
        }

        //选择需要上传的文件
        $("#uploadFile").click(function () {
            $("#selectPdfFile").click();
        });
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
        //删除已经上传的pdf文件
        $("#deleteUploadedPdfFile").click(function () {
            $("#uploadedFileUrl").data("file_url", "");
            $("#selectPdfFile").val("");
            $(".uploadBoxPdf").fadeOut(100);
        });

        //处理选择文件后的上传
        $(".uploadFileBtnWrapper").on("change", "#selectPdfFile", function () {
            if (!isPdfFileUploading) {
                resUpload(this.files, "pdf", "Pdf", 20);
            } else {
                baseUtils.show.redTip("文件正在上传中，请稍后再试");
            }
        });
        //服务号通知单选点击态判断 ---企业模式（是否开通设置经营范围） ---个人模式是否设置 功能管理--服务号通知开关
        var $serviceToggle = $('#service_radio');
        var collection = $serviceToggle.data('collection');//判断模式 0--企业模式 1--个人模式
        var setTemp = $serviceToggle.data('set-temp');//判断是否有设置教育行业 0--已设置  1--未设置
        var messagePush = $serviceToggle.data('message-push');//个人模式是否有打开消息推送开关开关；true 开启 false 关闭
        if(collection == 0 && setTemp == 1){
            $serviceToggle.on('click',':radio',function(e){
                console.log('企业模式')
                e.preventDefault();
                var ele = $(this);
                var txt=  "您需要到微信公众号后台将您的服务号所在行业设置为“教育/培训”，设置完成后，开启服务号通知方可正常发送模板消息。"+
                    "<br/><a target='_blank' href='/help/instructions#hp5_wx_service' style='margin-top:10px;'>什么是模板消息？</a>";
                var option = {
                    title: "提示", //弹出框标题
                    btn: 3, //确定&&取消
                    oktext: '我已设置',
                    canceltext: '关闭',
                    icon: 'blue',
                    onOk: function(){//发送请求，判断用户是否设置服务号行业
                        $.ajax('/has_industry',{
                            type: 'GET',
                            dataType: 'json',
                            data: {}
                        }).done(function(data) {
                            var code = data.ret;
                            if(code == 0){
                                ele.prop('checked', true);
                                prevVal = currVal;
                                /*消息推送开启*/
                                baseUtils.show.blueTip("模板消息推送开启");
                            } else {
                                baseUtils.show.redTip("无法开启消息推送，请按照提示修改设置");
                            }
                        }).fail(function(xhr, text, err) {
                            console.error(err);
                            baseUtils.show.redTip("网络错误，请稍后再试！");
                        });
                    }
                };
                $.alert(txt, "custom", option);
            })
        }else if(collection == 1 && messagePush == false){
            $serviceToggle.on('click',':radio',function(e){
                e.preventDefault();
                console.log('person');
                var ele = $(this);
                var txt = '您尚未开启服务号通知，是否立即开启服务号通知？</br><a target="_blank" href="/helpCenter/problem?first_id=44&second_id=45&document_id=doc_598dcf69a8367_8AjB9">什么是服务号通知？</a>';
                var option = {
                    title: "提示", //弹出框标题
                    btn: 3, //确定&&取消
                    oktext: '开启通知',
                    canceltext: '关闭',
                    icon: 'blue',
                    onOk: function(){//发送请求，打开消息推送开关
                        $.ajax('/set_service_notification',{
                            type: 'POST',
                            dataType: 'json',
                            data: {status:1}
                        }).done(function(result) {
                            if (result.code == 0) {
                                ele.prop('checked', true);
                                baseUtils.show.blueTip("已开启服务号消息通知");
                                // baseUtils.show.blueTip("模板消息推送开启");
                            } else {
                                baseUtils.show.redTip("操作失败，请稍后再试。");
                            }
                        }).fail(function(xhr, text, err) {
                            console.error(err);
                            baseUtils.show.redTip("网络错误，请稍后再试！");
                        });
                    }
                };
                $.alert(txt, "custom", option);
            })
        }
        //发布动态
        $("#releaseDynamic").click(function () {
            var key=$('.selectContent input:checked').attr('value');//发布动态按钮选中状态；
            //  防止重复提交
            if (Business.submitLimit == true) {
                baseUtils.show.redTip("正在提交中，请稍后再试");
                return false;
            }

            //  文件正在上传中
            if (isPdfFileUploading) {
                baseUtils.show.redTip("文件正在上传中，请稍后再提交");
                return false;
            }

            /**
             * 需要的参数 ： community_id - title - org_content - content - file_url - file_name - file_size
             */
            var params = {};

            var dynamicTitle = $("#dynamicTitle").val();
            if (dynamicTitle == '' || dynamicTitle == undefined) {
                baseUtils.show.redTip("请输入动态标题");
                return false;
            }
            params['title'] = dynamicTitle;

            //  获取动态 html内容，返回: <p>hello</p>
            var ue = UE.getEditor('container');
            var org_content = ue.getContent();    //  原始html内容
            var descrb = ue.getPlainTxt();        //  纯文本

            if (org_content == '' || org_content == undefined) {
                baseUtils.show.redTip("请输入动态内容");

                return false;
            }
            params['org_content'] = org_content;
            params['content'] = descrb;

            params['community_id'] = Business.communityId;

            //获取最后服务号通知值
            var push_state = $('.selectItem input:checked').val();
            //文件属性
            var pdfFileUrl = $("#uploadedFileUrl").data("file_url");
            var pdfFileName = $("#uploadedFileUrl").data("file_name");
            var pdfFileSize = $("#uploadedFileUrl").data("file_size");



            if (pdfFileUrl != "" && pdfFileUrl != undefined) {
                params['file_url'] = pdfFileUrl;
                params['file_name'] = pdfFileName;
                params['file_size'] = pdfFileSize;
            } else {
                params['file_url'] = "";
                params['file_name'] = "";
                params['file_size'] = "";
            }

            var postUrl = "";
            if (Business.type == 1) {   //  编辑
                params['id'] = Business.dynamicId;
                postUrl = "/smallCommunity/updateDynamic";
            } else {    //  新建
                postUrl = "/smallCommunity/uploadDynamic";
            }

            Business.submitLimit = true;


            console.log(key);
            if(key == 1){
                setService(Business.communityId,postUrl,params,push_state);
            }else{
                publish(postUrl,params,push_state,key);//发布动态
            }

        });



    };

    function publish(postUrl,params,push_state) {
        $.ajax(postUrl, {
            type: "POST",
            dataType: "json",
            data: {
                params: params,
                push_state: push_state
            },
            success: function (result) {
                if (result.code == 0) {
                    if (Business.type == 1) {
                        baseUtils.show.blueTip("保存编辑成功");
                    } else {
                        baseUtils.show.blueTip("发布动态成功");
                    }
                    setTimeout(function () {
                        window.location.href = "/smallCommunity/dynamicList?community_id=" + Business.communityId;
                    }, 700);
                } else {
                    baseUtils.show.redTip("发布出现问题，请稍后再试");
                    Business.submitLimit = false;
                }
            },
            error: function (xhr, status, err) {
                Business.submitLimit = false;
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });
    }
    //服务号消息通知ajax方法
    function setService(c_id,postUrl,params,push_state){
        console.log(c_id);
        $.ajax('/smallCommunity/checkFeedsMessagePush/'+c_id,{
            type:'POST',
            dataType:'JSON',
            async:false,
            success:function(data){
                console.log(data);
                if(data.code == 0){
                    baseUtils.show.blueTip(data.msg);

                    publish(postUrl,params,push_state);
                }else if(data.code == -1){
                    baseUtils.show.redTip(data.msg);
                    Business.submitLimit = false;
                    // $('#closeItem').attr('checked','true');
                    return false;
                    // $.alert("您需先行启用 功能管理——服务号通知 功能，方可成功开启",{
                    //     link:'个人模式服务号通知说明',
                    //     href:'#',
                    //     oktext:'立即开启',
                    //     onOk:function(){
                    //         $.ajax("/set_service_notification", {
                    //             type: "POST",
                    //             dataType: "json",
                    //             data: {status:1},
                    //             success: function (result) {
                    //                 if (result.code == 0) {
                    //                     baseUtils.show.blueTip("开启成功，您现在可正常使用服务号通知功能");
                    //                 } else {
                    //                     baseUtils.show.redTip("操作失败，请稍后再试。");
                    //                 }
                    //             },
                    //             error: function (xhr, status, err) {
                    //                 console.log(err);
                    //                 baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                    //             }
                    //         });
                    //     }
                    // });
                    // $('#closeItem').prop('checked',true);
                }else{
                    baseUtils.show.redTip(data.msg);
                    Business.submitLimit = false;
                    return false;
                    //失败加入选项返回
                }
            },
            error: function (xhr, status, err) {
                Business.submitLimit = false;
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }

        })
    }
    //取消上传判断参数
    // var uploadCancel=0;
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


    //资源上传函数(参数：resType:资源类型,resTypeClass:资源类型中细分的种类,resLimitSize:资源限制大小)
    function resUpload(files, resType, resTypeClass, resLimitSize) {
        if (files && files.length > 0) {
            var file = files[0];
            var pdfFileName = file.name;
            if (!pdfFileName.toLowerCase().endsWith("pdf")) {
                baseUtils.show.redTip("请选择 pdf 格式的文件");
                $("#selectPdfFile").remove();
                $("#uploadedFileUrl").before('<input class="selectPdfFileInput" id="selectPdfFile" type="file" accept="application/pdf"/>');
                return false;
            }

            // 限制资源在*MB内
            if ($uploadFile.checkFileSize(file, resLimitSize)) {

                var fileSize = (file.size / (1024 * 1024)).toFixed(2);
                $("#uploadedFileUrl").data("file_size", fileSize);
                $('#audioURL'+resTypeClass).hide();
                $(".uploadBox"+resTypeClass).fadeIn(300);
                // 初始化文件以及进度条
                initFileAndProgressBar(pdfFileName, fileSize);

                $("#uploadedFileUrl").data("file_name", pdfFileName);

                isPdfFileUploading = true;
                $uploadFile.uploadRes(file, resType, function (data) {
                        var progress = parseInt(data * 100);
                        //console.log(progress);
                        $(".uploadPLineActive" + resTypeClass).css("width", progress + '%');
                        $(".uploadPercent" + resTypeClass).text(progress + "%");
                    },
                    // 上传成功回调
                    function (data) {
                        isPdfFileUploading = false;
                        console.log(data);
                        baseUtils.show.blueTip("上传成功！");
                        $(".uploadPLine" + resTypeClass).hide();
                        $(".upload" + resTypeClass + "Name").show();
                        $(".uploadPercent" + resTypeClass).text("删除");
                        var resUrl = data.data.access_url;
                        $("#uploadedFileUrl").data("file_url", resUrl);

                    },
                    // 上传失败回调
                    function (data) {
                        isPdfFileUploading  = false;
                        console.error("上传失败!!!");
                        console.log(data);
                        baseUtils.show.redTip("上传失败！");
                    });
            } else {
                baseUtils.show.redTip("上传失败，请勿上传超过 " + resLimitSize + "Mb 的PDF文件");
                $(".upLoad"+resTypeClass).val("");
            }
        } else {
            console.log("网络错误，请稍后再试！");
        }
    };

    // 初始化文件以及进度条
    function initFileAndProgressBar(fileName, fileSize) {

        $(".uploadPdfName").html(fileName).hide();
        $(".uploadPdfSize span").html(fileSize);
        $(".uploadPLinePdf").show()
            .find(".uploadPLineActivePdf").css("width", '0');
        $(".uploadPercentPdf").html('');

    }

    return Business;

})();

