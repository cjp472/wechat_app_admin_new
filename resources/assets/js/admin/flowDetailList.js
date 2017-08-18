/**
 * Created by Administrator on 2017/2/23.
 */

$(document).ready(function () {

    //  回主页
    $(".home_page").click(function () {
        window.location.href="/accountview";
    });

    $(".resource_size_wrapper").hover(function () {

        var offset = $(this).offset();
        $(".hover_prompt").css('top',offset.top + 25);
        $(".hover_prompt").css('left',offset.left + 50);

        var original_size = $(this).data("original_size");
        var compressed_size = $(this).data("compressed_size");
        var image_size = $(this).data("image_size");
        var resource_type = $(this).data("resource_type");  // 1,2,3,4,: 音频， 视频， 直播， 图文

        if (resource_type == "1") {/*优化后音频：50M（原音频：100M） 图片：5M*/
            $(".compressed_size").html("音频：" + compressed_size);
            $(".original_size").html("（原大小：" + original_size + "）");
            $(".image_size").html("图片：" + image_size);

        } else if(resource_type == "2" || resource_type == "3" || resource_type == '5' || resource_type == '6'){
            $(".compressed_size").html("视频：" + compressed_size);
            $(".original_size").html("（原大小：" + original_size + "）");
            $(".image_size").html("图片：" + image_size);

        } else if(resource_type == "4"){
            $(".compressed_size").html("文字：" + original_size);
            $(".original_size").html("");
            $(".image_size").html("图片：" + image_size);

        }
        $(".hover_prompt").show();

    }, function () {
        $(".hover_prompt").hide();
    });


})