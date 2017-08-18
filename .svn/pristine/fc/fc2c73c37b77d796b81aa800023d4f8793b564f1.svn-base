/**
 * Created by Jervis on 2017/6/21.
 */

$(function(){
    functionSet.init();
});

var functionSet=(function(){

    functionSet={};

    functionSet.init=function(){
        console.log('functionSet is already')
        var init_feed=$('input[name=dynamic]:checked').val();
        var init_comment=$('input[name=remind]:checked').val();

        //点击事件
        $(":radio").on('click',function(e){
            console.log('test radio click');
            var params={};//生成数组传回给后端
            var targetId = e.target.id;

            params['id'] = $('#admin_data').data('id');
            params['is_feeds_push'] = $('input[name=dynamic]:checked').val();
            params['is_comment_push'] = $('input[name=remind]:checked').val();

            console.log(targetId);
            console.log(params);
            if($("#industry").data("hasIndustry")) {                                       //判断页面值是否为 1--第一次未开启  2--非第一次未开启
                if (init_feed != params['is_feeds_push'] || init_comment != params['is_comment_push']) {
                    if (targetId == 'dynamic' || targetId == 'remind') {

                        var txt = "您需要到微信公众号后台将您的服务号所在行业设置为“教育/培训”，设置完成后，开启服务号通知方可正常发送模板消息。" +
                            "<br/><a target='_blank' href='https://admin.xiaoe-tech.com/help_document?id=d_5954e90310557_MHBzvybc' style='margin-top:10px;'>什么是模板消息？</a>";
                        var option = {
                            title: "提示", //弹出框标题
                            btn: 3, //确定&&取消
                            oktext: '我已设置',
                            canceltext: '关闭',
                            icon: 'blue',
                            onOk: function () {//发送请求，判断用户是否设置服务号行业
                                $.ajax('/has_industry', {
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {},
                                    success: function (data) {
                                        var code = data.ret;
                                        if (code == 0) {
                                            commit(params, targetId);
                                            /*消息推送开启*/
                                            // baseUtils.show.blueTip("模板消息推送开启");
                                        } else {
                                            if (targetId == 'dynamic') {
                                                $("#dynamicClose").prop('checked', true);
                                            } else {
                                                $("#remindClose").prop('checked', true);
                                            }
                                            baseUtils.show.redTip("无法开启消息推送，请按照提示修改设置");
                                        }
                                    },
                                    error: function (xhr, text, err) {
                                        console.error(err);
                                        baseUtils.show.redTip("网络错误，请稍后再试");
                                    }
                                });
                            },
                            onCancel:function(){
                                if (targetId == 'dynamic') {
                                    $("#dynamicClose").prop('checked', true);
                                } else {
                                    $("#remindClose").prop('checked', true);
                                }
                            },
                            onClose:function(){
                                if (targetId == 'dynamic') {
                                    $("#dynamicClose").prop('checked', true);
                                } else {
                                    $("#remindClose").prop('checked', true);
                                }
                            }
                        }
                        $.alert(txt, "custom", option);
                    } else {
                        commit(params, targetId);
                    }
                }
            }else{
                commit(params, targetId);
            }
        });

        //将消息提交给后台的ajax
        function　commit(params,targetId){
            $.ajax("/smallCommunity/set", {
                type: "POST",
                dataType: "json",
                data: {
                    params: params
                },
                success: function (result) {
                    // console.log(result);
                    if (result.code == 0) {
                        alertBox(targetId);
                        setTimeout(function(){window.location.reload()},2000);
                    } else {
                        baseUtils.show.redTip("保存失败");
                        window.location.reload();
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                }
            });
        }

        //消息提醒弹窗公用判断方法
        function alertBox(targetId){
            var outputText = "";
            switch (targetId) {
                case "dynamic":
                    outputText = "群主动态提醒已开启";
                    break;
                case "dynamicClose":
                    outputText = "群主动态提醒已关闭";
                    break;
                case "remind":
                    outputText = "点赞与评论提醒已开启";
                    break;
                case "remindClose":
                    outputText = "点赞与评论提醒已关闭";
                    break;
                default:
                    console.log("参数错误。");
                    break;
            }
            baseUtils.show.blueTip(outputText);
        }

    };
    return functionSet;
})();