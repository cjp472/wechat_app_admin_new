/**
 * Created by Hugh on 2017-01-10.
 */

$(document).ready(function()
{
    init();
    paraBack();

    //搜索按钮
    $("#searchButton").click(function()
    {
        var search_time=$("#search_time").val();
        // var search_attr=$("#search_attr").val();
        // var top=$("#top").val();
        var search_url=encodeURIComponent($("#search_url").val());
        window.location.href="/pvuvsearch?search_time="+search_time+"&search_url="+search_url;//+"&search_attr="+search_attr+"&top="+top;
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
    exportTimepickerconfig($("#search_time"));
}

//数据回显
function paraBack()
{
    // $("#search_time").val(getUrlParam("search_time"));
    // $("#top").val(getUrlParam("top"));
    // $("#search_attr").val( (getUrlParam("search_attr")==null || getUrlParam("search_attr")=='')?'PV':getUrlParam("search_attr"));
    $("#search_url").val(decodeURIComponent(getUrlParam("search_url")? getUrlParam("search_url") : ""));
}
