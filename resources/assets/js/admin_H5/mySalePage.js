/**
 * Created by xiaoe on 2017/2/15.
 */
/**
 * 网页加载完毕触发
 */
$(document).ready(function () {
    $frame.init();

});

//框架逻辑
$frame = {

    init: function () {

        $("#tab_item_mine").removeClass("mineOff");
        $("#tab_item_mine").addClass("mineOn");

        $("#tab_item_search").removeClass("searchOn");
        $("#tab_item_search").addClass("searchOff");

        /*底部tab 搜索栏*/
        $("#tab_item_search").click(function () {
            $("#tab_item_search").removeClass("searchOff");
            $("#tab_item_search").addClass("searchOn");

            $("#tab_item_mine").removeClass("mineOn");
            $("#tab_item_mine").addClass("mineOff");

            window.location.href = "/saleHomePage";
        })
        /*底部tab 我的分销栏*/
        $("#tab_item_mine").click(function () {
            $("#tab_item_mine").removeClass("mineOff");
            $("#tab_item_mine").addClass("mineOn");

            $("#tab_item_search").removeClass("searchOn");
            $("#tab_item_search").addClass("searchOff");

            window.location.href = "/getSaleList";
        })



        /*复制链接*/
        $(".copyBtn").click(function () {
            $('#sourceUrl').val().clone();
        });


        /*查看内容*/
        $(".loctoBtn").click(function () {
            window.location.href = $('#sourceUrl').val();
        });



        /*查看详情*/
        $(".apply_ok").click(function () {
            $mySaleContent.showMyOkSale();
        });

        /*查看失败原因*/
        $(".apply_fail").click(function () {
            $mySaleContent.showMyfailedApply();
        });

        /*查看申请中*/
        $(".apply_ing").click(function () {
            $mySaleContent.showMyAppling();
        });

    }

};


/*我的分销部分*/
$mySaleContent = {

    // 成功的分销详情展示
    showMyOkSale : function () {

        $(this).after();
    },

    // 失败的分销申请详情
    showMyAppling : function () {

        $(this).after();
    },

    // 失败的分销申请详情
    showMyfailedApply : function () {

        $(this).after();
    }

};