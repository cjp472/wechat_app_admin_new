$(document).ready(function()
{
    init();
    channelHandle();
    wordOverflow();
    //确认申请按钮点击
    $("#updateUrl").click(function()
    {
        var id=$("#agree_id").val();
        if(id.length==0)
        {
            baseUtils.show.redTip("未选择分销单号");
            return false;
        }

        var sale_url="";var channel_id="";
        if( $("input[name='create_select']:checked").val() == 1 ) //使用已有的链接
        {
            sale_url=$("input[name='channel_select']:checked").val();
            channel_id=$("input[name='channel_select']:checked").attr("channel_id");
        }

        $.post("/agreesale",{"id":id,"sale_url":sale_url,"channel_id":channel_id},function(data)
        {
            if(data.ret==0)
            {
                baseUtils.show.blueTip("操作成功",function()
                {
                    window.location.reload();
                });
            }
            else
            {
                baseUtils.show.redTip("操作失败");
            }
        });
    });

    //拒绝申请按钮点击
    $("#updateRefuse").click(function()
    {
        var id=$("#disagree_id").val();
        if(id.length==0)
        {
            baseUtils.show.redTip("未选择分销单号");
            return false;
        }

        var refuse_reason=$("#refuse_reason").val();
        if(refuse_reason.length==0)
        {
            baseUtils.show.redTip("请输入拒绝原因");
            return false;
        }

        $.post("/disagreesale",{"id":id,"refuse_reason":refuse_reason},function(data)
        {
            if(data.ret==0)
            {
                baseUtils.show.blueTip("操作成功",function()
                {
                    window.location.reload();
                });
            }
            else
            {
                baseUtils.show.redTip("操作失败");
            }
        });
    });
});
//表格超出部分添加省略号
function wordOverflow() {
    $(".overomit").each(function(){
        var overWords=$(this).text();
        if(overWords.length>10){
            var newWords=overWords.substring(0,10)+"...";
            $(this).text(newWords);
        }
    });
};

//初始化
function init()
{

    //回填搜索值
    $("#search").val(getUrlParam("search"));
    $("#ruler").val((getUrlParam("ruler")==null  || getUrlParam("ruler")=='') ? -1 :getUrlParam("ruler"));

    //搜索按钮
    $("#searchButton").click(function()
    {
        var search=$("#search").val();
        var ruler=$("#ruler").val();
        window.location.href='/sale?search='+search+'&ruler='+ruler;
    });

    //回车搜索
    $(document).keypress(function(e)
    {
        if(e.which == 13)
        {
            $('#searchButton').trigger("click");
        }
    });

    //复制到剪贴板
    (function () {
        var clipboard = new Clipboard('.copyHref');
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    })();
}

//渠道的切换
function channelHandle()
{
    $("input[name='create_select']").next().click(function() {
        if( $(this).prev().val() == 0 ) {//自己生成新的渠道
            $('.modal-body .tableAreaContainer').hide();
            $("input[name='channel_select']").attr("disabled",true);
        } else { //使用已存在的渠道
            $('.modal-body .tableAreaContainer').show();
            $("input[name='channel_select']").attr("disabled",false);
        }
    });
}

//同意申请
function agreeSale(id)
{
    $("#agree_id").val(id);
    $("#agreeModal").modal("show");
    $("#channel_list").html('');


    $.post('/get_channel',{"id":id},function (data) {

        if(data.code == 0)
        {
            $("#channel_list").html(data.data);
            if(!$("#no_data").hasClass('hide')){
                $("#no_data").addClass('hide');
            }


        }else{
            // $("#channel_list").html(data.msg);
            $("#no_data").removeClass('hide');
        }
    })
}

//不同意申请
function disAgreeSale(id)
{
    $("#disagree_id").val(id);
    $("#disAgreeModal").modal("show");
}