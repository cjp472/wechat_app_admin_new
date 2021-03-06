/**
 * Created by Administrator on 2017/3/21.
 */


$(document).ready(function () {
    Business.init();

});

var Business = (function () {

    var Business = {};

    Business.coverImgUrl = "";
    Business.page_type = "";
    Business.member_id = "";
    Business.page_origin = "";

    Business.init = function () {

        Business.page_type = $(".admin_data").data("type");      //  获取当前页面类型 （0 -> 新增 ； 1 -> 编辑）

        Business.member_id = GetQueryString("id");
        Business.page_origin = GetQueryString("page_origin");

        //  确定页面来源
        if (Business.page_type == 0 || Business.page_origin == "member_list") {
            $(".go_back").html("会员列表");
        } else {
            $(".go_back").html("会员详情");
        }

        // 上一步
        $(".cancelBtn, .go_back").click(function () {
            if (Business.page_type == 0 || Business.page_origin == "member_list") {
                window.location.href = '/member_list_page';
            } else {
                window.location.href = '/member_detail_page?id=' + Business.member_id;
            }

        });

        //  点击图片
        $(".memberCoverImg").click(function () {
            $(".uploadCoverInput").click();
        })

        // 上传会员封面
        $(".uploadCoverInput").on("change",function (e) {
            if(this.files && this.files.length>0){
                var file = this.files[0];

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
                        baseUtils.show.redTip("请上传图片类型的文件哦~");
                        //alert("2.只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                        return;
                    }
                } else {
                    document.all.submit_upload.disabled=true;
                    baseUtils.show.redTip("请上传图片类型的文件哦~");
                    //alert("只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                    return;
                }

                var limitSize = 5;  // 限制上传大小// 限制图片在5MB内

                if($uploadFile.checkFileSize(file,limitSize)){
                    showLoading();
                    $uploadFile.uploadPic(file,
                        // 成功回调
                        function (result) {
                            hideLoading();
                            baseUtils.show.blueTip("上传成功！");
                            Business.coverImgUrl = result.data.access_url;
                            if(Business.coverImgUrl){
                                $(".memberCoverImg img").attr("src", Business.coverImgUrl);
                                $("#coverImgUrl").val(Business.coverImgUrl);
                            }
                        },
                        // 失败回调
                        function (data) {
                            hideLoading();
                            console.log(data);
                            baseUtils.show.redTip("上传失败！");
                        });

                } else {
                    baseUtils.show.redTip("上传图片限制在"+limitSize+"MB内！");
                }
            } else {
                console.log(this.files)
            }
        });

        //点击侧边栏离开时的弹框
        changeSaveFlag(true);

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

        //  保存按钮
        $(".saveBtn").click(function () {

            //  1 -> 开始检查必要输入信息

            var memberName = $.trim($(".memberNameInput").val());
            if ($formCheck.emptyString(memberName)) {
                baseUtils.show.redTip("会员名称不能为空！");
                return false;
            }
            var memberSummary = $.trim($(".memberSummaryTextArea").val());
            if ($formCheck.emptyString(memberSummary)) {
                baseUtils.show.redTip("会员简介不能为空！");
                return false;
            }
            var coverImgUrl = $("#coverImgUrl").val();
            if ($formCheck.emptyString(coverImgUrl)) {
                baseUtils.show.redTip("会员封面不能为空！");
                return false;
            }

            var memberPrice = $.trim($(".memberPriceInput").val());
            if ($formCheck.emptyString(memberPrice)) {
                baseUtils.show.redTip("会员价格不能为空！");
                return false;
            }
            if (memberPrice <= 0) {
                baseUtils.show.redTip("会员价格不能为 0 或负数！");
                return false;
            }
            if (memberPrice > baseUtils.maxInputPrice) {
                baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
                return false;
            }

            var params = {};

            params['name'] = memberName;
            params['summary'] = memberSummary;
            params['img_url'] = coverImgUrl;

            params['img_url_compressed'] = coverImgUrl;     // 封面压缩地址默认为原图地址，防止压缩失败

            params['price'] = memberPrice * 100;    //  单位： 分

            //  2 -> 检查无误，保存剩余信息

            // 会员有限期（单位： 秒）
            var memberValidPeriod = parseInt($(".selectValidPeriod").val());
            switch (memberValidPeriod) {
                case 1:
                    //  一个月:2592000 = 30 * 24 * 60 * 60
                    memberValidPeriod = 2592000;
                    break;
                case 2:
                    //  三个月:7862400 -> 7776000 = 90 * 24 * 60 * 60
                    memberValidPeriod = 7776000;
                    break;
                case 3:
                    //  半年:15724800 -> 15811200 = 183 * 24 * 60 * 60
                    memberValidPeriod = 15811200;
                    break;
                case 4:
                    //  一年:31622400 = 366 * 24 * 60 * 60
                    memberValidPeriod = 31622400;
                    break;
                default:
                    break;
            }
            params['period'] = memberValidPeriod;


            //  获取会员详情html内容，返回: <p>hello</p>
            var ue = UE.getEditor('container');
            params['org_content'] = ue.getContent();    //  原始html内容
            params['descrb'] = ue.getPlainTxt();        //  纯文本


            //  获取会员所属分类
            var memberCategoryArray = [];
            $('input[name="category"]:checked').each(function() {
                memberCategoryArray.push($(this).attr('id'));
            });

            //  是否上架 （0: 上架状态； 1：下架状态）
            params['state'] = $("input[name='showMember']:checked").val();

            //是否显示订阅期数
            // params['is_show_resourcecount'] = $("input[name='showMemberCount']:checked").val();

            //  3 -> 发送保存请求（"/"）


            //  该专栏是否兼做会员
            params['is_member'] = 1;

            //  获取会员id
            params['id'] = GetQueryString('id');


            if (Business.page_type == 0) {
                //  钻石图标URL
                params['member_icon_default'] = "http://wxresource-10011692.file.myqcloud.com/manual/icon_member_diamond_gray.png";
                params['member_icon_highlight'] = "http://wxresource-10011692.file.myqcloud.com/manual/icon_member_diamond.png";
                memberUtils.uploadMemberInfo(params, memberCategoryArray);
            } else {
                memberUtils.editedMemberInfo(params, memberCategoryArray);
            }


        });

    };

    return Business;

})();


var memberUtils = (function () {

    var utils = {};

    // 编辑会员信息
    utils.editedMemberInfo = function (params, categoryTypeArr) {
        showLoading();
        $.ajax("/goods_edit_package", {
            type: "POST",
            dataType: "json",
            data: {
                params: params, category_type: categoryTypeArr
            },
            success: function (result) {
                if (result.code == 0) {
                    baseUtils.show.blueTip("编辑会员成功！");

                    $(".go_back").click();

                    // if (Business.page_origin == "member_list") {
                    //     window.location.href = '/member_list_page';
                    // } else {
                    //     window.location.href = '/member_detail_page?id=' + Business.member_id;
                    // }
                } else {
                    hideLoading();
                    baseUtils.show.redTip("编辑失败，请稍后再试！");
                }
            },
            error: function (xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("服务器开小差了，请稍后再试！");
            }
        });



    };

    // 新建会员信息
    utils.uploadMemberInfo = function (params, categoryTypeArr) {
        showLoading();
        $.ajax("/goods_upload_package", {
            type: "POST",
            dataType: "json",
            data: {
                params: params, category_type: categoryTypeArr
            },
            success: function (result) {
                if (result.code == 0) {
                    baseUtils.show.blueTip("新建会员成功！");
                    window.location.href = '/member_list_page';
                } else {
                    hideLoading();
                    baseUtils.show.redTip("新建失败，请稍后再试！");
                }
            },
            error: function (xhr, status, err) {
                hideLoading();
                console.log(err);
                baseUtils.show.redTip("服务器开小差了，请稍后再试！");
            }
        });


    };

    return utils;

})();