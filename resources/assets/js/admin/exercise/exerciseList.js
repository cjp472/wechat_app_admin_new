/**
 * Created by Administrator on 2017/8/2.
 */


$(document).ready(function () {
    $currentObj.init();
});

$currentObj = (function () {
    var $currentObj = {
        monitorAudioObj: -1,
    };
    var $exerciseInfo = {};
    var $audioInfo = {
        playingAudioDom: 0,
    };
    $currentObj.init = function () {

        //作业的操作
        $(".operateList > li.operate").click(function () {
            var $self = $(this),
                $parent = $self.parents(".exerciseOperateArea"),
                $exerciseItem = $self.parents(".singleExerciseItem"),
                type = $self.data("type"),
                textContent = $parent.data("text_content"),
                originalImgUrlsArr = $parent.data("original_img_urls"),
                audioUrlsArr = $parent.data("audio_urls"),
                exerciseId = $parent.data("exercise_id"),
                exerciseTitle = $parent.data("exercise_title");

            $exerciseInfo = {
                textContent: textContent,
                originalImgUrlsArr: originalImgUrlsArr,//默认为未压缩图片内容
                audioUrlsArr: audioUrlsArr,
                exerciseTitle: exerciseTitle
            };

            switch (type) {
                case "look_exercise_content":
                    lookExerciseContent();
                    break;
                case "delete_exercise":
                    showConfirmWindow(exerciseId, $exerciseItem);
                    break;
                default:
                    console.log("参数错误");
                    break;
            }
        });

        /*********************** 处理详情窗口 *************************/
        $("#closeExeDetailWindow").click(function () {
            baseUtils.hideWindow("lookExerciseDetailWindow");
            $("#audioContentArea").html("");
        });

        //  播放/暂停 音频
        $("#audioContentArea").on("click", ".audioPlayStateIcon", function () {
            var $self = $(this),
                $target = $self.parents(".audioController").siblings(".audioDom")[0],
                isAudioPlaying = $self.hasClass("playing");

            if (isAudioPlaying) {
                $target.pause();
            } else {
                //先暂停所有 audio
                $.each(document.getElementsByTagName("audio"), function (k, v) {
                    v.pause();
                });
                $target.play();
            }
        });

    };
    function playAudioEvent(k, v) {
        clearInterval($currentObj.monitorAudioObj);
        $currentObj.monitorAudioObj = setInterval(monitorAudio, 500, k, v);
        $("#audioContentArea").find(".audioPlayStateIcon")
            .eq(k).removeClass("paused").addClass("playing");
    };
    function pauseAudioEvent(k, v, isEnded) {
        if (isEnded) {
            v.currentTime = 0;
            $("#audioContentArea").find(".finishedProgress").eq(k).css({"width": "0%"});
        }
        clearInterval($currentObj.monitorAudioObj);
        $("#audioContentArea").find(".audioPlayStateIcon")
            .eq(k).removeClass("playing").addClass("paused");
    };
    function monitorAudio(k, v) {
        var current = v.currentTime,
            duration = v.duration,
            progressWidth = 100 * (current / duration);

        $("#audioContentArea").find(".finishedProgress").eq(k).css({"width": progressWidth+"%"});
        $("#audioContentArea").find(".progressBarDot").eq(k).css({"left": progressWidth+"%"});
    };
    function lookExerciseContent() {

        //填充数据
        $("#windowHeader").text($exerciseInfo.exerciseTitle);

        try {
            var textContent = $exerciseInfo.textContent.replace(/\n/g, '<br>');
            $("#textContentArea").html(textContent);
        }  catch (e) {
            $("#textContentArea").html("");
            console.log(e);
        }

        try {
            if ($exerciseInfo.originalImgUrlsArr.length > 0) {
                var imgContentHtml = "";
                $.each($exerciseInfo.originalImgUrlsArr, function (k, v) {
                    imgContentHtml +=
                        '<li><img src="'+v+'" alt="作业图片'+k+'"></li>';
                });
                $("#imgContentArea > ul").html(imgContentHtml);
            } else {
                $("#imgContentArea > ul").html("");
            }
        } catch (e) {
            $("#imgContentArea > ul").html("");
            console.log(e);
        }
        try {
            if ($exerciseInfo.audioUrlsArr.length > 0) {
                var audioContentHtml = "";
                $.each($exerciseInfo.audioUrlsArr, function (k, v) {
                    audioContentHtml +=
                        '<div class="singleExeAudio">'+
                            '<audio class="audioDom" src="'+v.audio_url+'"></audio>'+
                            '<div class="audioController">'+
                                '<div class="audioPlayStateIcon paused"></div>'+
                                '<div class="progressBar">'+
                                    '<span class="finishedProgress"></span>'+
                                    // '<span class="progressBarDot"></span>'+
                                '</div>'+
                                '<div class="audioLengthSecond"><span>'+v.audio_length+'</span>"</div>'+
                            '</div>'+
                        '</div>';
                });
                $("#audioContentArea").html(audioContentHtml);
            } else {
                $("#audioContentArea").html("");
            }
        } catch (e) {
            $("#audioContentArea").html("");
            console.log(e);
        }

        baseUtils.showWindow("lookExerciseDetailWindow");

        //插件 Viewer.js 初始化
        $exerciseInfo.viewer = new Viewer(document.getElementById('dowebok'));

        //audio 播放事件
        initAudioPlayEvent();
    };
    function showConfirmWindow(exerciseId, $exerciseItem) {
        $.alert("确认删除，删除后讲师和学员将无法看到该作业，且该操作不可撤回", {
            title: "删除作业",
            btn: 3,
            oktext: "删除",
            onOk: function () {
                deleteExercise(exerciseId, $exerciseItem);
            },
        });
    };
    function deleteExercise(exerciseId, $exerciseItem) {
        $.ajax("/exercise/change_exercise_state", {
            type: "POST",
            dataType: "json",
            data: {
                state: 2,
                exercise_id: exerciseId,
                exercise_book_id: GetQueryString("exercise_book_id"),
            },
            success: function (result) {
                if (result.code == 0) {
                    $exerciseItem.css({"height": "0", "padding": "0", "border-bottom": "none"});
                    setTimeout(function () {
                        baseUtils.show.blueTip("删除成功");
                        $exerciseItem.remove();
                        var itemNum = $("#tableContent").children(".singleExerciseItem").length;
                        if (itemNum == 0) {
                            $("#tableContent").before('<div class="contentNoData">老师暂未布置作业，您可以引导老师在手机端店铺 [我的-我的作业] 发布课程作业</div>');
                        }
                    }, 300);
                } else {
                    baseUtils.show.redTip("网络问题，请稍后再试");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                alert("服务器出小差了，请稍后再试！");
            }
        });
    };
    function initAudioPlayEvent() {

        //点击音频事件
        $.each(document.getElementsByTagName("audio"), function (k, v) {
            //开始播放
            v.addEventListener("playing", function () {
                playAudioEvent(k, v);
            });
            v.addEventListener("play", function () {
                playAudioEvent(k, v);
            });
            v.addEventListener("pause", function () {
                pauseAudioEvent(k, v);
            });
            v.addEventListener("ended", function () {
                pauseAudioEvent(k, v, true);
            });
            //初始化audio时长
            v.addEventListener("canplay", function () {
                var audioDuration = parseInt(v.duration);
                if (audioDuration && audioDuration > 0) {
                    $("#audioContentArea").find(".audioLengthSecond>span").eq(k).text(audioDuration);
                }
            });
        });

        // $("#audioContentArea").find(".progressBarDot").each(function () {
        //
        //
        // });

    };

    return $currentObj;
})();












