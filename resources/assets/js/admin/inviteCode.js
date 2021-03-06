/**
 * Created by Stuphin on 2016/9/28.
 */
$(document).ready(function()
{

    setTopUrlCookie('invitecode_listop','邀请码列表');
    //填入搜索值
    $("input[name='search']").val(getUrlParam("search"));
    $("select[name='ruler']").val((getUrlParam("ruler")==null  || getUrlParam("ruler")=='') ? 0 :getUrlParam("ruler"));

    //搜索
    $("#searchButton").click(function()
    {
        showLoading();
        var ruler=$("select[name='ruler']").val();
        var search=$("input[name='search']").val();
        window.location.href="/invitecode?ruler="+ruler+"&search="+search;
        hideLoading();
    });

    //回车搜索
    $(document).keypress(function(e)
    {
        if(e.which == 13)
        {
            $('#searchButton').trigger("click");//模拟点击
        }
    });

    //新增邀请码
    $("#addInviteCode").click(function()
    {
        window.location.href="/addinvitecode";
    });
    changeTab();

    //引导用户使用优惠券入口
    $("#couponGuide").click(function(){
        var txt = "您可以通过发送无门槛类型的优惠券来批量邀请用户，"+
            "用户通过指定的领取链接领取优惠券后，即可使用优惠券抵扣商品费用。"+
            "<br><a target='_blank' href='https://admin.xiaoe-tech.com/helpCenter/problem?document_id=d_59329384c4304_rLyRPXsG'>优惠券使用教程</a>";
        var option = {
            title: "如何批量邀请用户", //弹出框标题
            btn: 3, //确定&&取消
            oktext: '创建优惠券',
            canceltext: '关闭',
            icon: 'blue',
            onOk: function(){//跳转至优惠券页面
                window.location.href = '/coupon/select';
            }
        };
        $.alert(txt, "custom", option);
    });
});

function changeTab() {
    $("#tab_invite").click(function () { // 音频列表
        window.location.href = "/invitecode";
    });
    $("#tab_group").click(function () { // 视频列表
        window.location.href = "/groupcode";
    });
    $("#tab_gift").click(function () {// 图文列表
        window.location.href = "/giftcode";
    });
}

//下载邀请码
function inviteCodeDown(id)
{
    var id=id;
    window.location.href='/downxls?id='+id;
}

function StartInviteCodeDown(id)
{

}
//获取下载邀请码文件链接
function getInviteCodeDownUri(id)
{
    $("#ExportModal").fadeIn();
    
    $("#applyExcel").on("click",function(){
        var activity_id = $("#activity_id").val();
        // window.location.href = '/activityExportExcle?activity_id=' + activity_id;
        var chkObjs=null; 
        var Office2003=0;
        var obj=$("[name='selectOffice']");                
        for (var i=0;i<obj.length;i++){ //遍历Radio 
            if(obj[i].checked){ 
                chkObjs=obj[i].value;
                    if(chkObjs==1){
                        Office2003=2003;
                    }
             } 
        } 
        // alert(Office2003);
        window.location.href = "/excel/invite?id=" + id + "&version=" + Office2003;
    })
    $(".closePop").click(function () {
        $("#ExportModal").fadeOut();
    })



    // //响应等待时间提示
    // baseUtils.show.blueTip("资源请求中...");
    // showLoading();
    // var timeout=setTimeout(function(){
    //    //超时
    //         hideLoading();
    //         window.wxc.xcConfirm('请求超时,是否重试','confirm',{onOk:function () {
    //             getInviteCodeDownUri(id);
    //         }});
    //     },
    //     3100000 //超时时间，310秒 1000/1s
    // );

    // $.get('/downxls', {'id':id}, function(data)
    // {
    //     if(timeout){ //清除定时器
    //         clearTimeout(timeout);
    //         timeout=null;
    //     }
    //     hideLoading();
    //     var code = data.code;
    //     var url = decodeURIComponent(data.url); //writeObj(data);
    //     if(code == 0) {
    //         if(url.length >0){
    //             window.location.href = url;
    //         }
    //         else{
    //             window.wxc.xcConfirm('请重试','error');
    //         }
    //     }else{
    //         window.wxc.xcConfirm('请重试','error');
    //     }
    // });
}

//调到详情页
function jumpDetail(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    resetUrl('/customerdetail?appId='+appId+'&userId='+userId);
}