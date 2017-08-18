/**
 * Created by jserk on 2017/3/23.
 */
$(function () {
    managePackage.init();
});
var managePackage = function () {
    var managePackage = {};

    var resUrl;

    //检查专栏提交表单
    function checkPackageForm(info) {
        if ($formCheck.emptyString(info.name)) {
            baseUtils.show.redTip("专栏名称不能为空！");
            return false;
        }
        if ($formCheck.emptyString(info.img_url)) {
            baseUtils.show.redTip("请上传您的专栏封面！");
            return false;
        }
        if($(".notFreeSelect").is(':checked')&&$formCheck.emptyString(info.price)){
            baseUtils.show.redTip("您选择的付费形式为付费，请输入价格！");
            return false;
        }
        if($(".notFreeSelect").is(':checked')&&!$formCheck.checkNum(info.price)){
            baseUtils.show.redTip("您的价格输入格式有误，请重新输入！");
            return false;
        }
        if(info.price > baseUtils.maxInputPrice * 100) {
            baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
            return false;
        }
        return true;
    }

    //提交信息
    function submitForm(url, allParams) {
        console.log(url);
        console.log(allParams);

        showLoading();
        $.ajax(url, {
            type: 'POST',
            dataType: 'json',
            data:allParams,
            success:function (data) {
                if (parseInt(data.code) === 0) {
                    baseUtils.show.blueTip("保存成功");
                    var id = GetQueryString('id'),
                        memberId = GetQueryString('member_id');
                    if(id) {
                        if( memberId ) {
                            setTimeout(function () {
                                window.location.href = 'member_detail_page?id=' + memberId;
                            },700);
                            return ;
                        }
                        setTimeout(function () {
                            window.location.href = 'package_detail_page?id=' + id;
                        },700);
                    } else {
                        setTimeout(function () {
                            window.location.href = 'package_list_page';
                        },700);
                    }

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


    managePackage.init = function () {

        console.log(resourceFree);

        // 提交专栏表单
        $(".completeBtn").click(function () {
            //置空分类数组
            classArray=[];
            //获取专栏表单数据
            //专栏名称
            var title = $.trim($(".resName").val());

            //专栏简介
            var summary= $.trim($("#packageAbstract").val());

            //专栏封面地址
            var image1Url = $.trim($("#Image1Url").val());

            //专栏原始内容详情
            var ue = UE.getEditor('resource_desc');
            var orgContent = ue.getContent();

            //资源内容描述
            var describ = ue.getPlainTxt();
            if (describ.length == 0) {
                baseUtils.show.redTip('描述不能为空!');
                return false;
            }
            //是否付费（1表示免费，2表示单笔）
            var resFree = resourceFree;

            //付费价格
            var resPrize = $.trim($(".resPrize").val())*100;

            //专栏选择分类
            $('input[name="columnClass"]:checked').each(function(){
                classArray.push($(this).attr('id'));
            });
            //专栏上下架
            var state=columnState;

            // var is_show_resourcecount=show_resourcecount;
            // 专类分类数组
            console.log(classArray);
            //专栏单品上传信息
            var resInfo = {
                // 专栏名称
                name: title,
                // 专栏封面地址
                img_url: image1Url,
                // 封面压缩地址默认为原图地址，防止压缩失败
                img_url_compressed: image1Url,
                //专栏简介
                summary:summary,
                //上下架状态
                state:state,
                //显示期数
                // is_show_resourcecount:is_show_resourcecount,
                //原始文本
                org_content:orgContent,
                //专栏详情
                descrb:describ,
                // //付费形式
                // payment_type: resFree,
                //付费价格
                price: resPrize,
                //id
                id:GetQueryString('id') || null
            };
            // console.log(resInfo);
            //获取表单中的数据

            if (checkPackageForm(resInfo)) {
                var allParams = {
                    params: resInfo,  //专栏信息
                    category_type:classArray,  //专栏分类
                };
                console.log(allParams);
                submitForm(globalUrl,allParams);

            } else {
                return false;
            }
        })
    }

    return managePackage;
}();