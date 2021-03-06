/**
 * Created by Administrator on 2017/3/13.
 */

$(document).ready(function () {
    categoryEdit.init();

});

//  业务类
var categoryEdit = (function () {

    var categoryEdit = {};

    //  分类图片的路径
    var categoryPicUrl = "";

    categoryEdit.init = function () {

        //  回功能管理页面
        $(".header_level.left").click(function () {
            window.location.href="/manage_function";
        });

        //  初始化文案
        for (var i = 0; i < 4; i ++) {
            var src = $(".pic_show").eq(i).attr("src")
            if (src != "" && src != undefined) {
                $(".upload_icon_btn").eq(i).html("更换图标");
            } else {
                $(".upload_icon_btn").eq(i).html("选择图标");
            }

        }

        $(".category_name_input").bind('input propertychange', function() {
            $(this).css("border", "solid 1px #dcdcdc");
            $(this).parent().nextAll(".no_name_tip").hide();
        });

        // 上传分类图标   -   暂时不开放自定义上传图标
        // $(".uploadCategoryPic").on("change",function (e) {
        //     var input = this;
        //     console.log("change")
        //     if(this.files && this.files.length>0){
        //         var file = this.files[0];
        //         // 限制上传大小
        //         var limitSize = 2;
        //
        //         // 限制图片在2MB内
        //         if($uploadFile.checkFileSize(file,limitSize)){
        //             showLoading();
        //             $uploadFile.uploadPic(file,
        //                 // 成功回调
        //                 function (data) {
        //                     hideLoading();
        //                     data = JSON.parse(data);
        //                     categoryPicUrl = data.data.access_url;
        //                     if(categoryPicUrl){
        //                         commonUtils.showCategoryPic(categoryPicUrl, input);//已经修改
        //                     }
        //                     baseUtils.show.blueTip("上传成功！");
        //                 },
        //                 // 失败回调
        //                 function (data) {
        //                     hideLoading();
        //                     console.log(data)
        //                     baseUtils.show.redTip("上传失败！");
        //                 });
        //         }
        //         else{
        //             baseUtils.show.redTip("上传图片限制在"+limitSize+"MB内！");
        //         }
        //     }
        //     else{
        //         console.log(this.files)
        //     }
        // });

        var currentPosition = "";

        //  选择自定义图标
        $(".upload_icon_btn").click(function () {
            currentPosition = this;
            $('.select_icon_window').fadeIn(300);
        });
        $(".pic_add").click(function () {
            currentPosition = this;
            $('.select_icon_window').fadeIn(300);
        });
        $(".pic_show").click(function () {
            currentPosition = this;
            $('.select_icon_window').fadeIn(300);
        });

        // //  点击图片关闭按钮
        // $(".pic_close").click(function () {
        //     $(this).addClass("hide");
        //     $(this).nextAll(".pic_show").attr("src","");
        //     $(this).nextAll(".pic_show").addClass("hide");
        //     $(this).nextAll(".pic_add").removeClass("hide");
        // });

        //  选中图标
        $(".single_icon_wrapper").click(function () {
            $("#selectedIcon").removeClass("active");
            $("#selectedIcon").attr("id","");
            $(this).addClass("active");
            $(this).attr("id","selectedIcon");
        });
        //  点击取消按钮
        $(".cancel_btn").click(function () {
            commonUtils.eraseSelectState();
        });
        //  点击×图标
        $(".close_icon_wrapper").click(function () {
            commonUtils.eraseSelectState();
        });
        //  点击确定按钮
        $(".confirm_btn").click(function () {
            var icon_url = $("#selectedIcon").children(".single_icon").attr("src");
            if (icon_url != "" && icon_url != undefined) {
                $(".select_icon_window").fadeOut(300);
                commonUtils.showCategoryIcon(icon_url, currentPosition);
                $(currentPosition).parent().children(".upload_icon_btn").html("更换图标");

                $(currentPosition).parent().parent().nextAll(".no_url_tip").hide();

            } else {
                baseUtils.show.blueTip("还没有选中任何图标哦！");
            }
        });


        //  点击保存按钮
        $(".save_edit_btn").click(function () {
            showLoading();

            var $category_name_1 = $.trim($(".category_name_input").eq(0).val());
            var $category_name_2 = $.trim($(".category_name_input").eq(1).val());
            var $category_name_3 = $.trim($(".category_name_input").eq(2).val());
            var $category_name_4 = $.trim($(".category_name_input").eq(3).val());

            var $category_url_1 = $(".pic_show").eq(0).attr("src");
            var $category_url_2 = $(".pic_show").eq(1).attr("src");
            var $category_url_3 = $(".pic_show").eq(2).attr("src");
            var $category_url_4 = $(".pic_show").eq(3).attr("src");

            if ($category_name_1 == "") {
                $(".no_name_tip").eq(0).show();
                $(".category_name_input").eq(0).css("border","solid 1px #e64340");
            }
            if ($category_name_2 == "") {
                $(".no_name_tip").eq(1).show();
                $(".category_name_input").eq(1).css("border","solid 1px #e64340");
            }
            if ($category_name_3 == "") {
                $(".no_name_tip").eq(2).show();
                $(".category_name_input").eq(2).css("border","solid 1px #e64340");
            }
            if ($category_name_4 == "") {
                $(".no_name_tip").eq(3).show();
                $(".category_name_input").eq(3).css("border","solid 1px #e64340");
            }
            if ($category_name_1 == "" || $category_name_2 == "" || $category_name_3 == "" || $category_name_4 == "") {
                baseUtils.show.redTip("分类名称不能为空！");
                hideLoading();
                return false;
            }

            if ($category_name_1.length > 4 || $category_name_2.length > 4 || $category_name_3.length > 4 || $category_name_4.length > 4) {
                baseUtils.show.redTip("分类名称长度不能超过4个字符！");
                hideLoading();
                return false;
            }

            if ($category_url_1 == "") {
                $(".no_url_tip").eq(0).show();
            }
            if ($category_url_2 == "") {
                $(".no_url_tip").eq(1).show();
            }
            if ($category_url_3 == "") {
                $(".no_url_tip").eq(2).show();
            }
            if ($category_url_4 == "") {
                $(".no_url_tip").eq(3).show();
            }
            if ($category_url_1 == "" || $category_url_2 == "" || $category_url_3 == "" || $category_url_4 == "") {
                baseUtils.show.redTip("上传图标不能为空！");
                hideLoading();
                return false;
            }

            //  上传分类名 + 分类图标url
            var params_1 = {};
            params_1['name'] = $category_name_1;
            params_1['url'] = $category_url_1;
            var params_2 = {};
            params_2['name'] = $category_name_2;
            params_2['url'] = $category_url_2;
            var params_3 = {};
            params_3['name'] = $category_name_3;
            params_3['url'] = $category_url_3;
            var params_4 = {};
            params_4['name'] = $category_name_4;
            params_4['url'] = $category_url_4;

            var params = [params_1, params_2, params_3, params_4];


            var prompt = setTimeout(commonUtils.overtimePrompt, 5000);

            $.ajax("/update_category_info",{
                type: 'POST',
                dataType: 'json',
                data: {"params": params},
                success:function (data) {
                    hideLoading();
                    clearTimeout(prompt);
                    if (data.ret == 0) {
                        window.location.href="/manage_function?state=finish_edit";
                    } else {
                        hideLoading();
                        baseUtils.show.blueTip("保存失败！");
                    }
                },
                error: function(xhr, status, err) {
                    hideLoading();
                    console.error(err);
                    baseUtils.show.blueTip("网络错误，请稍后再试！");
                }
            });

        });

    };

    return categoryEdit;

})();

var commonUtils = {

    // 显示分类图标
    showCategoryIcon: function (url, input) {
        if(url){
            $(input).parent().children(".pic_show").attr("src",url);
            $(input).parent().children(".pic_show").removeClass("hide");
            $(input).parent().children(".pic_add").addClass("hide");
            // $(input).parent().children(".pic_close").removeClass("hide");
        }
    },

    //  暂时隐藏
    // showCategoryPic: function (url, input) {
    //     if(url){
    //         $(input).nextAll(".pic_show").attr("src",url);
    //         $(input).nextAll(".pic_show").removeClass("hide");
    //         $(input).nextAll(".pic_add").addClass("hide");
    //         $(input).nextAll(".pic_close").removeClass("hide");
    //     }
    // },

    //  超时提示
    overtimePrompt: function () {
        hideLoading();
        baseUtils.show.redTip("网络超时！");
    },

    eraseSelectState: function () {
        $('.select_icon_window').fadeOut(300);
        $("#selectedIcon").removeClass("active");
        $("#selectedIcon").attr("id","");
    }

}



















