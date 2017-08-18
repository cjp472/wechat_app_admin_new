/**
 * Created by hugh 201701-11
 */
$(document).ready(function()
{
    setTopUrlCookie('invitecode_listop','买赠码列表');
    //填入搜索值
    $("input[name='search']").val(getUrlParam("search"));
    $("select[name='ruler']").val((getUrlParam("ruler")==null  || getUrlParam("ruler")=='') ? 0 :getUrlParam("ruler"));

    //搜索
    $("#searchButton").click(function()
    {
        showLoading();
        var ruler=$("select[name='ruler']").val();
        var search=$("input[name='search']").val();
        window.location.href="/giftcode?ruler="+ruler+"&search="+search;
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
    //响应等待时间提示
    baseUtils.show.blueTip("资源请求中...");
    showLoading();
    var timeout=setTimeout(function(){
       //超时
            hideLoading();
            window.wxc.xcConfirm('请求超时,是否重试','confirm',{onOk:function () {
                getInviteCodeDownUri(id);
            }});
        },
        3100000 //超时时间，310秒 1000/1s
    );
    
    $.get('/downxls', {'id':id}, function(data)
    {
        if(timeout){ //清除定时器
            clearTimeout(timeout);
            timeout=null;
        }
        hideLoading();
        var code = data.code;
        var url = decodeURIComponent(data.url); //writeObj(data);
        if(code == 0) {
            if(url.length >0){
                window.location.href = url;
            }
            else{
                window.wxc.xcConfirm('请重试','error');
            }
        }else{
            window.wxc.xcConfirm('请重试','error');
        }
    });
}

//调到详情页
function jumpDetail(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    resetUrl('/customerdetail?appId='+appId+'&userId='+userId);
}