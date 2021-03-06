/**
 * Created by jserk on 2017/3/23.
 */
/**
 * Created by jserk on 2017/3/23.
 */
$(function () {

    manageVideo.init();

});
var manageVideo = function () {
    var manageVideo = {};

    var resUrl;

    //检查视频提交表单
    function checkVideoForm(info) {
        if ($formCheck.emptyString(info.title)) {
            baseUtils.show.redTip("视频名称不能为空！");
            return false;
        }
        if (fileId==null) {
            baseUtils.show.redTip("请上传视频资源！");
            return false;
        }
        if (is_edit) {
            baseUtils.show.redTip("视频资源上传中！");
            return false;
        }
        if ($formCheck.emptyString(info.img_url)) {
            baseUtils.show.redTip("请上传您的视频封面！");
            return false;
        }
        // if ($formCheck.emptyString(info.patch_img_url)) {
        //     baseUtils.show.redTip("请上传您的视频贴片！");
        //     return false;
        // }
        if ($formCheck.emptyString(info.start_at)) {
            baseUtils.show.redTip("请填入您的视频上架时间！");
            return false;
        }
        if (!$formCheck.checkTime(info.start_at)) {
            baseUtils.show.redTip("视频上架时间错误");
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
        if(uploadChannelType==1&&info.payment_type==2 && info.piece_price > baseUtils.maxInputPrice * 100){
            baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
            return false;
        }
        return true;
    }

    //提交信息
    function submitForm(url, allParams) {
        showLoading();
        $.post(url, allParams, function (data) {
            if (parseInt(data.code) === 0) {
                baseUtils.show.blueTip("保存成功");
                window.location.href = backURL;
            }
            else {
                hideLoading();
                baseUtils.show.redTip(data.msg);
                submitLimt = false;
            }
        });
    }


    manageVideo.init = function () {

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
        // 提交视频表单

        $(".completeBtn").click(function () {

            //获取视频表单数据
            //视频名称
            var title = $.trim($(".resName").val());

            //视频点播id
            var videoFileId = fileId;

            //视频大小
            var videoSize=videoGsize;

            //视频封面地址
            var image1Url = $.trim($("#Image1Url").val());

            //视频贴片地址
            var image4Url = $.trim($("#Image4Url").val());

            //视频原始内容详情
            var ue = UE.getEditor('resource_desc');
            var orgContent = ue.getContent();

            //视频内容描述
            var describ = ue.getPlainTxt();
            if (describ.length == 0) {
                baseUtils.show.redTip('描述不能为空!');
                return false;
            }



            //付费价格
            var resPrize = $.trim($(".resPrize").val())*100;

            //直播选择分类
            $('input[name="aliveClass"]:checked').each(function(){
                classArray.push($(this).attr('id'));
            });
            console.log(classArray);

            //上架时间
            var startAt = $.trim($("#dateInput").val());

            //视频单品上传信息
            var resInfo = {
                // 视频名称
                title: title,
                //   视频封面地址
                img_url: image1Url,
                //   贴片图片地址
                patch_img_url: image4Url,
                ////云点播文件id
                //file_id:videoFileId,
                //// 视频大小
                //video_size:videoSize,
                //视频名称
                file_name: VideoName,
                //视频详情
                descrb:describ,
                //原始文本
                org_content:orgContent,
                //付费价格
                piece_price: resPrize,
                //上架时间
                start_at: startAt,
                //id
                id: GetQueryString('id') || null
            };
            //视频直播额外信息
            var resourceParams={
                 //云点播视频id
                public_video:videoFileId,
                //视频大小
                public_size_text:videoSize
            };
            //消息推送
            var pushState = $('#serviceToggle :radio:checked').val();
            if(pushState){//如果有消息推送权限
                resInfo.push_state = pushState;
            }
            //是否付费（1表示免费，2表示单笔）
            var resFree = resourceFree;
            if(resFree) {
                resInfo.payment_type = resFree;
            }

            //获取表单中的数据

            if (checkVideoForm(resInfo)) {
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

    return manageVideo;
}();