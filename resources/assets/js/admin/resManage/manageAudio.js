/**
 * Created by jserk on 2017/3/23.
 */
$(function () {

    manageAudio.init();

});
var manageAudio = function () {
    var manageAudio = {};

    var resUrl;

    //检查音频提交表单
    function checkAudioForm(info) {
        if ($formCheck.emptyString(info.title)) {
            baseUtils.show.redTip("音频名称不能为空！");
            return false;
        }
        if ($formCheck.emptyString(info.audio_url)) {
            baseUtils.show.redTip("请上传正式音频资源！");
            return false;
        }
        if ($formCheck.emptyString(info.img_url)) {
            baseUtils.show.redTip("请填入您的音频图片！");
            return false;
        }
        if ($formCheck.emptyString(info.start_at)) {
            baseUtils.show.redTip("请填入您的音频上架时间！");
            return false;
        }
        if (!$formCheck.checkTime(info.start_at)) {
            baseUtils.show.redTip("音频上架时间错误");
            return false;
        }
        if(uploadChannelType==1&&info.payment_type==2&&$formCheck.emptyString(info.piece_price)){
            baseUtils.show.redTip("您选择的付费形式为付费，请输入价格！");
            return false;
        }
        if(uploadChannelType==1&&info.payment_type==2&&!$formCheck.checkNum(info.piece_price)){
            baseUtils.show.redTip("您的价格输入格式有误，请重新输入！");
            return false;
        }
        if (uploadChannelType==1&&info.payment_type==2 && info.piece_price > baseUtils.maxInputPrice * 100) {
            baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
            return false;
        }
        return true;
    }

    //提交信息
    function submitForm(url, allParams) {
        showLoading();
        $.ajax(url, {
            type: 'POST',
            dataType: 'json',
            data:allParams,
            success:function (data) {
                if (parseInt(data.code) === 0) {
                    baseUtils.show.blueTip("保存成功");
                    setTimeout(function () {
                        window.location.href = backURL;
                    },700);
                }
                else {
                    hideLoading();
                    baseUtils.show.redTip(data.msg);
                    submitLimt = false;
                }
            },
            error: function(xhr,status,err) {
                hideLoading();
                console.error(err);
                baseUtils.show.redTip('网络错误，请稍后再试！');
                submitLimt = false;
            }
        });
    }


    manageAudio.init = function () {

        //新版消息推送拉取数据
        var p_id =GetQueryString('package_id');
        var selectTime = $('#dateInput').val();
        //监控时间弹窗组件
        $('#dateInput').on('change', function(e) {
            selectTime = $(this).val();
            console.log(currentDate);
            if(selectTime>currentDate){
                $('#nextDay').html('上架当日');
            }else{
                $('#nextDay').html('今日');
            }
            pushFunction();
            console.log(selectTime);
        });
        console.log(packageId);
        pushFunction();
        function pushFunction() {
            $.get('/check_goods_message_push/' + p_id + '/' + selectTime, function (data) {
                console.log(data);
                $('#valid_push').html(data.data.valid_push);
                if (data.data.has_push >= 3) {
                    $('#fBtn1').attr('disabled', true);
                    $('#has_push').html(3);//写死已发3条
                }else{
                    $('#has_push').html(data.data.has_push);
                    $('#fBtn1').attr('disabled', false);
                }
            });
        }

        // 提交音频表单
        $(".completeBtn").click(function () {

            //获取音频表单数据
            //音频名称
            var title = $.trim($(".resName").val());

            //正式音频地址
            var audio1Url = $.trim($("#Audio1Url").val());

            //音频封面地址
            var image1Url = $.trim($("#Image1Url").val());

            //音频原始内容详情
            var ue = UE.getEditor('resource_desc');
            var orgContent = ue.getContent();

            //音频内容描述
            var describ = ue.getPlainTxt();
            if (describ.length == 0) {
                baseUtils.show.redTip('描述不能为空!');
                return false;
            }
            //试听音频地址
            var audio2Url = $.trim($("#Audio2Url").val());

            //日签图片地址
            var image2Url = $.trim($("#Image2Url").val());



            //付费价格
            var resPrize = $.trim($(".resPrize").val())*100;

            //直播选择分类
            $('input[name="aliveClass"]:checked').each(function(){
                classArray.push($(this).attr('id'));
            });
            console.log(classArray);

            //上架时间
            var startAt = $.trim($("#dateInput").val());


            //音频单品上传信息
            var resInfo = {
                // 音频名称
                title: title,
                //  正式音频地址
                audio_url: audio1Url,
                //   音频封面地址
                img_url: image1Url,
                // 正式音频大小
                audio_size:fileAudio1Size,
                //音频名
                file_name: AudioName,
                //正式音频时长
                audio_length:resAudio1Length,
                //音频详情
                descrb:describ,
                //原始文本
                org_content:orgContent,
                //试听音频地址
                try_audio_url:audio2Url,
                //试听音频时长
                try_audio_length:resAudio2Length,
                //试听音频名
                try_file_name: tryAudioName,
                //   日签图片地址
                sign_url: image2Url,
                //付费价格
                piece_price: resPrize,
                //上架时间
                start_at: startAt,
                //id
                id: GetQueryString('id') || null
            };

            //消息推送
            var pushState = $('#serviceToggle :radio:checked').val();
            if(pushState){//如果有消息推送权限
                resInfo.push_state = pushState;
            }
            //是否付费（1表示免费，2表示单笔）
            if(resourceFree) {
                resInfo.payment_type = resourceFree;
            }

            //console.log(resInfo);
            //获取表单中的数据

            if (checkAudioForm(resInfo)) {
                var allParams = {
                    params: resInfo,
                    resource_type: resourceType,
                    upload_channel_type: uploadChannelType,
                    package_id: packageId,
                    resource_params: resourceParams,
                    package_name: packageName,
                    roleParams: roleParams,
                    category_type:classArray
                };
                submitForm(golbalUrl,allParams);

            } else {
                return false;
            }
        })
    }

    return manageAudio;
}();