/**
 * Created by Administrator on 2017/7/31.
 */

$(document).ready(function () {
    $manageExercise.init();
});

$manageExercise = (function () {
    var $manageExercise = {};
    var $private = {
        submitLimit: false,
        pageType: $("#admin_data").data("page_type"),       //0-创建；1-编辑
    }

    $manageExercise.init = function () {

        //点击侧边栏离开时的弹框
        changeSaveFlag(true);

        if ($private.pageType == 1) {
            $private.resType = $("#admin_data").data("resource_type");    //已关联课程的 type
            $private.resId = $("#admin_data").data("resource_id");          //已关联课程的 id
            $private.resName = $("#admin_data").data("resource_name");    //已关联课程的 name
            $private.resPrice = $("#admin_data").data("resource_price");    //已关联课程 price
        }

        $(".characterNumLimit>span").text($("#exerciseBookName").val().length);

        $("#exerciseBookName").on("input", function () {
            checkInputName();
        });

        //关联课程
        $("#resTypeSelector").on("change", function () {
            var resType = $(this).val();
            if (resType == -1) {
                $("#resItemSelector").html(
                    '<option data-res_id="-1">请选择具体课程</option>'
                );
            } else {
                $.ajax("/exercise/get_resource_list", {
                    type: "POST",
                    dataType: "json",
                    data: {
                        resource_type: resType
                    },
                    success: function (result) {
                        if (result.code == 0) {
                            var resourceList = result.data.resource_list,
                                htmlStr = "";

                            if ($private.pageType == 1 && $private.resType == resType) {
                                htmlStr +=
                                    '<option data-res_id="'+$private.resId+'" data-res_price="'+$private.resPrice+'" selected>'+$private.resName+'</option>';
                            }
                            $.each(resourceList, function (k, v) {
                                var resName = (resType==5?v.name:v.title),
                                    resPrice = (resType==5?v.price:v.piece_price);
                                htmlStr +=
                                    '<option data-res_id="'+v.id+'" data-res_price="'+resPrice+'" >'+resName+'</option>';
                            });
                            if (htmlStr.length == 0) {
                                htmlStr =
                                    '<option data-res_id="-1">暂无数据</option>';
                            }
                            $("#resItemSelector").html(htmlStr);

                        } else {
                            baseUtils.show.redTip("网络问题，请稍后再试");
                        }
                    },
                    error: function (xhr, status, err) {
                        console.log(err);
                        alert("服务器出小差了，请稍后再试！");
                    }
                });
            }

        });

        //选择课程
        $("#resItemSelector").on("change", function () {
            var $this = $(this).children("option:selected"),
                resId = $this.data("res_id"),
                resPrice = $this.data("res_price"),
                isRemind = $(".circleRadio.radioActive").data("is_remind");

            if (resId != -1 && resPrice == 0 && isRemind == 1) {
                baseUtils.show.redTip("您关联的课程为免费课程，暂不能开启作业推送功能");
                $('.remindRadio').children('.circleRadio').removeClass('radioActive');
                $('#noRemindMsg').addClass('radioActive');
                return false;
            }
        });

        // radio 按钮点击切换效果
        $('.remindRadio').on('click', function() {

            var isRemind = $(".circleRadio.radioActive").data("is_remind"),     //当前状态
                resId = $("#resItemSelector > option:selected").data("res_id"),
                resPrice = $("#resItemSelector > option:selected").data("res_price"),
                $this = $(this);

            if ($this.children(".circleRadio").hasClass("radioActive")) {    //不处理
                return false;
            }
            if (isRemind == 0) {    //不提醒 => 提醒
                if (resId != -1 && resPrice == 0) {
                    baseUtils.show.redTip("您关联的课程为免费课程，暂不能开启作业推送功能");
                    return false;
                }
            }
            $('.remindRadio').children('.circleRadio').removeClass('radioActive');
            $this.children('.circleRadio').addClass('radioActive');
        });

        $("#cancelSaveExercise").click(function () {
            window.history.back();
        });

        $("#confirmSaveExercise").click(function () {
            var $selectedResItem = $("#resItemSelector > option:selected"),
                exerciseBookName = $.trim($("#exerciseBookName").val()),
                resName = $.trim($selectedResItem.text()),
                resId = $selectedResItem.data("res_id"),
                resPrice = $selectedResItem.data("res_price"),
                resType = $("#resTypeSelector").val(),
                communityId = $("#communitySelector").val(),
                isRemind = $(".circleRadio.radioActive").data("is_remind"), // 1 - 提醒， 0 - 不提醒<默认不提醒>
                postUrl = "";

            if (exerciseBookName.length == 0) {
                baseUtils.show.redTip("还没有输入作业本名称");
                return false;
            }
            if (exerciseBookName.length > 14) {
                baseUtils.show.redTip("作业本名称长度不能超过14字");
                return false;
            }
            if (resId == -1) {
                baseUtils.show.redTip("还没有选择具体课程");
                return false;
            }
            if (resPrice == 0 && isRemind == 1) {
                baseUtils.show.redTip("开启提醒的作业本不能关联免费课程");
                return false;
            }
            var params = {
                title: exerciseBookName,
                resource_id: resId,
                resource_type: resType,
                resource_name: resName,
                is_enable_notify: isRemind
            }
            if (communityId != -1 && communityId != undefined) {
                params.community_id = communityId;
            }
            if ($private.pageType == 0) {            //创建
                postUrl = "/exercise/upload_exercise_book";
            } else if ($private.pageType == 1) {     //编辑
                postUrl = "/exercise/update_exercise_book";
                params.exercise_book_id = GetQueryString("exercise_book_id");
            } else {
                console.log(" pageType 参数有误。");
                baseUtils.show.redTip("网络错误，请稍后再试");
                return false;
            }
            if ($private.submitLimit) {
                baseUtils.show.redTip("正在提交中，请稍后再试");
                return false;
            }
            $private.submitLimit = true;
            $.ajax(postUrl, {
                type: "POST",
                dataType: "json",
                data: {
                    params: params
                },
                success: function (result) {
                    $private.submitLimit = false;
                    if (result.code == 0) {
                        baseUtils.show.blueTip($private.pageType==0?"创建成功":"编辑成功");
                        if ($private.pageType == 0) {   //创建
                            var exerciseBookId = result.data.exerciseBookId;
                            sessionStorage.setItem("lastExerciseBookId", exerciseBookId);
                            sessionStorage.setItem("lastResourceType", resType);

                        }
                        setTimeout(function () {
                            var pageIndex = GetQueryString("page_index") || 1;
                            window.location.href = "/exercise/exercise_book_list?page=" + pageIndex;
                        }, 1000);
                    } else {
                        baseUtils.show.redTip(result.msg);
                    }
                },
                error: function (xhr, status, err) {
                    $private.submitLimit = false;
                    console.log(err);
                    alert("服务器出小差了，请稍后再试！");
                }
            });

        });

    };
    function checkInputName() {
        var $self = $("#exerciseBookName"),
            exerciseName = $.trim($self.val());

        if (exerciseName.length > 14) {
            // exerciseName = exerciseName.substr(0, 14);
            // $self.val(exerciseName);
            $(".characterNumLimit").css({"color": "red"});
            $("#exerciseBookName").css({"border-color": "red"});
        } else {
            $(".characterNumLimit").css({"color": "#b2b2b2"});
            $("#exerciseBookName").css({"border-color": "#dcdcdc"});
        }
        $(".characterNumLimit>span").text(exerciseName.length);
    }

    return $manageExercise;
})();




















