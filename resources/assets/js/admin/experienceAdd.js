/**
 * Created by Stuphin on 2016/10/28.
 */


$(document).ready(function () {

    //提交
    $("#finish").click(function () {
        var params = {};
        //批次名称
        params['purchase_name'] = $("#purchase_name").val().trim();
        if (params['purchase_name'] == 0) {
            baseUtils.show.redTip("亲！请输入链接名称哦~");
            return false;
        }


        //生效时间
        params['period'] = $("#period").val();
        if (params['period'] == 0) {
            baseUtils.show.redTip("亲！请输入允许试听时间哦~");
            return false;
        }
        console.log(parseInt(params['period']));
        if(!(/(^[1-9]\d*$)/.test(params['period']))){
            baseUtils.show.redTip("亲！请输入正确的时间哦~");
            return false;
        }




        //响应等待时间提示
        baseUtils.show.blueTip("试听链接生成中...");
        showLoading();

        $.post('/doaddExperience', {'params': params}, function (data) {
            console.log(params);
            console.log(data);
            // return false;
            var ret = data.ret;
            var url = data.url;
            if (ret == 0) {
                hideLoading();
                $.alert("试听链接生成成功！"+url,"info",{
                    onOk:function(){
                        window.location.href='/experience'
                    }
                });
            }
            else {
                hideLoading();//alert(ret);
                baseUtils.show.redTip("生成失败");
            }
        });
    });
});