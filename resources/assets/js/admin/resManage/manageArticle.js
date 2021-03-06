/**
 * Created by jserk on 2017/3/23.
 */
/**
 * Created by jserk on 2017/3/23.
 */
$(function () {

    manageArticle.init();

});
var manageArticle = function () {
    var manageArticle = {};

    var resUrl;

    //检查图文提交表单
    function checkArticleForm(info) {
        if ($formCheck.emptyString(info.title)) {
            baseUtils.show.redTip("图文名称不能为空！");
            return false;
        }
        if ($formCheck.emptyString(info.img_url)) {
            baseUtils.show.redTip("请上传您的图文封面！");
            return false;
        }
        if ($formCheck.emptyString(info.start_at)) {
            baseUtils.show.redTip("请填入您的图文上架时间！");
            return false;
        }
        if (!$formCheck.checkTime(info.start_at)) {
            baseUtils.show.redTip("图文上架时间错误");
            return false;
        }
        if(uploadChannelType==1&&info.payment_type==2 && $formCheck.emptyString(info.piece_price)){
            baseUtils.show.redTip("您选择的付费形式为付费，请输入价格！");
            return false;
        }
        if(uploadChannelType==1&&info.payment_type==2 && !$formCheck.checkNum(info.piece_price)){
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


    manageArticle.init = function () {

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
        pushFunction();
        function pushFunction() {
            $.get('/check_goods_message_push/' + p_id + '/' + selectTime, function (data) {
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

        $('#preview_try').on('click',function(){
            var html = ue1.getContent();
            document.getElementById('preview_content').innerHTML = html;
            $('.preview_con').addClass('active');
            $('.preview_box').addClass('active');
            document.documentElement.style.overflow = "hidden";
        });

        // 提交图文表单
        $(".completeBtn").click(function () {

            //获取图文表单数据
            //图文名称
            var title = $.trim($(".resName").val());

            //图文封面地址
            var image1Url = $.trim($("#Image1Url").val());

            //图文原始内容详情
            var ue = UE.getEditor('resource_desc');
            var orgContent = ue.getContent();
            orgContent = orgContent.replace( /&quot;/gi, "'" );
            //console.log(orgContent);
            //图文试看详情
            var ueTry = UE.getEditor('contentTry');
            var orgContentTry = ue1.getContent();

            //资源内容描述
            var describ = ue.getPlainTxt();
            if (describ.length == 0) {
                baseUtils.show.redTip('描述不能为空!');
                return false;
            }

            //图文试看描述
            var describTry = ueTry.getPlainTxt();
            if (describTry.length == 0) {
                baseUtils.show.redTip('描述不能为空!');
                return false;
            }

            //付费价格
            var resPrize = $.trim($(".resPrize").val())*100;

            //直播选择分类
            $('input[name="aliveClass"]:checked').each(function(){
                classArray.push($(this).attr('id'));
            });
            //console.log(classArray);

            //上架时间
            var startAt = $.trim($("#dateInput").val());

            //图文单品上传信息
            var resInfo = {
                // 图文名称
                title: title,
                // 图文封面地址
                img_url: image1Url,
                //原始文本
                org_content:orgContent,
                //图文详情
                try_content:describTry,
                //试看原始文本
                try_org_content:orgContentTry,
                //试看图文详情
                content:describ,
                //付费形式
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
            // console.log(resInfo);
            //获取表单中的数据

            if (checkArticleForm(resInfo)) {
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

    return manageArticle;
}();