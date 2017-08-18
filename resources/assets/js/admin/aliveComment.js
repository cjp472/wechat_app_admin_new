$(document).ready(function ()
{
    init();
});

//初始化
function init()
{
    //填入搜索值
    $("#search").val(getUrlParam("search"));
    $("#ruler").val((getUrlParam("ruler")==null  || getUrlParam("ruler")=='') ? 0 :getUrlParam("ruler"));

    //搜索
    $("#searchButton").click(function()
    {
        showLoading();
        var alive_id=getUrlParam("alive_id");
        var ruler=$("#ruler").val();
        var search=$("#search").val();
        window.location.href="/alivecomment?alive_id="+alive_id+"&ruler="+ruler+"&search="+search;
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
}

//显示隐藏评论
function changeAliveComment(id,targetState)
{
    var alive_id=getUrlParam("alive_id");
    $.get("/changealivecomment",{"alive_id":alive_id,"id":id,"targetState":targetState},function(data)
    {
        if(data.ret==0)
        {
            baseUtils.show.blueTip("修改成功",function()
            {
                window.location.reload();
            });
        }
        else
        {
            baseUtils.show.redTip("修改失败");
        }
    });
}

//用户详情
function jumpDetail(appId,userId)
{
    window.location.href="/customerdetail?appId="+appId+"&userId="+userId;
}



