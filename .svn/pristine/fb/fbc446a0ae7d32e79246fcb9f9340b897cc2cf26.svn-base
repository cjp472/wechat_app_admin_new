$(function() {
    addDynamic.init();
});

var addDynamic = function() {

    var addDynamic = {};

    addDynamic.init = function() {
        $('.completeBtn').click(function() {
            var DynamicName = $.trim($('.dynamicName').val());
            if ($formCheck.emptyString(DynamicName)) {
                baseUtils.show.redTip("动态名称不能为空！");
                return false;
            }
            //动态原始内容详情
            var ue = UE.getEditor('dynamicDescirb');
            var orgContent = ue.getContent();

            //动态内容描述
            var describ = ue.getPlainTxt();
            if (describ.length == 0) {
                baseUtils.show.redTip('描述不能为空!');
                return false;
            }
            var dynamicInfo = {
                //动态标题
                name: DynamicName,
                //原始文本
                org_content: orgContent,
                //动态详情
                content: describ,
            }
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
    };

    return addDynamic
}();
