/**
 * Created by Stuphin on 2016/12/8.
 */

$(document).ready(function()
{
    init();
    paraBack();

    //搜索按钮
    $("#searchButton").click(function()
    {
        var start=$("#start").val();
        var end=$("#end").val();
        var ruler=$("#ruler").val();
        var search=$("#search").val();
        window.location.href="/data_usage?ruler="+ruler+"&search="+search+"&start="+start+"&end="+end;
    });

    //回车搜索
    $(document).keypress(function(e)
    {
        if(e.which == 13)
        {
            $('#searchButton').trigger("click");//模拟点击
        }
    });
});

//初始化
function init()
{
    setTopUrlCookie('payadmin_listop','财务管理');
    datetimepickerconfig($("#start"));
    datetimepickerconfig($("#end"));
}

//数据回显
function paraBack()
{
    $("#start").val(getUrlParam("start"));
    $("#end").val(getUrlParam("end"));
    $("#ruler").val( (getUrlParam("ruler")==null || getUrlParam("ruler")==0)?0:getUrlParam("ruler"));
    $("#search").val(getUrlParam("search"));
}
