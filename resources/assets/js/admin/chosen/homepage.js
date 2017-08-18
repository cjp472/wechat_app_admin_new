

$(document).ready(function () {
    $chosenShop.init();
});

$chosenShop = {

    tabState: "goods",   //"goods", "record"

    init: function () {
        // 开关
        $("._switchOperateArea").on("click", "._functionSwitch", function (e) {
            var switchState = $(e.delegateTarget).data("switch_state");//当前状态

            if (switchState) {
                baseUtils.show.redTip("开关已开启，暂不支持关闭");
                return false;
            }
            $chosenShop.showOpenChosenTip();
        });
        //tab切换
        $("#_contentTabWrapper").on("click", "li", function () {
            var state = $(this).val();
            if (state == 1) {
                history.replaceState(null, "", "/chosen/homepage?page_type=goods");
                $chosenShop.getNewPage("goods");
                $chosenShop.tabState = 'goods';
            } else {
                history.replaceState(null, "", "/chosen/homepage?page_type=record");
                $chosenShop.getNewPage("record");
                $chosenShop.tabState = 'record';
                $recordList.search = ''; // 清空搜索回填数值
            }
        });

        // 分页请求
        $("#_mainContentPart").on("click"," .pagination li a", function(e){
            var el = $(e.target),
                url = el.data('url');
            $chosenShop.getNewPage($chosenShop.tabState, {}, url);
        });

    },
    /* 更新分页的初始状态
     * 参数说明：
     *  pagingClass 分页外层的div
     */
    upDatePaging: function(pagingClass){
        var pagingClass = pagingClass || '.list-page';
        $(pagingClass + ' .pagination li a').each(function(){
           var el = $(this);
           el.attr('data-url', el.attr('href'));
           el.removeAttr('href');
        });
    },

    /* 更新页面数据
     * 参数说明：
     *  targetTab : 要加载的目标页
     *  params : 参数对象
     *  postUrl : 通过a标签获得的链接：如：http://localhost/chosen/get_record_list?search=&page=3
     *  注意：params 和 postUrl 二选一，调用方式分别如下：
     *      $chosenShop.getNewPage(targetTab,{},url); // 只传递targetTab和url，用于分页
     *      $chosenShop.getNewPage(targetTab,params); // 只传递targetTab和param，用于搜索
     */
    getNewPage: function (targetTab, params, postUrl) {  //"goods", "record"
        var params = params || {};
        var postUrl = postUrl || "";

        // 初始化状态
        if(!postUrl){
            if (targetTab == "goods") {
                postUrl = "/chosen/get_goods_list";
            } else if (targetTab == "record") {
                postUrl = "/chosen/get_record_list";
            } else {
                console.log("参数错误.");
                return false;
            }
        }else{
            // 解析url
            var url = postUrl,
                porturl_pattern = /(\/chosen\/.*)\?/, // 匹配路由
                param_pattern = /(\?|&)(.+?)=/g, // 匹配参数
                param_value_pattern = /=([^&]*)(&|$)/g; // 匹配参数值
            postUrl = url.match(porturl_pattern)[1];
            var param_arr = url.match(param_pattern),
                param_value_arr = url.match(param_value_pattern);
            var len = param_arr.length;
            // 组合params对象
            for(var i = 0; i < len; i++){
                var param = param_arr[i];
                var param_value = param_value_arr[i];
                param_value = decodeURI(param_value.slice(1,param_value.length));
                if(param_value[param_value.length - 1] === '&'){
                    params[param.slice(1,param.length - 1)] = param_value.slice(0,param_value.length-1);
                }else{
                    params[param.slice(1,param.length - 1)] = param_value;
                }
            }
        }

        baseUtils.showLoading("mainContentLoading");
        $.ajax(postUrl, {
            type: "POST",
            dataType: "html",
            data: params,
            success: function (result) {
                baseUtils.hideLoading("mainContentLoading");
                if (result.length > 0) {
                    $("#_mainContentPart").html(result);
                    $chosenShop.switchContentTab(targetTab);
                    if(targetTab !== 'goods'){ // 除了推广商品，其他的都分页
                        $chosenShop.upDatePaging();
                    } else {
                        $goodsList.goodsCount = $("#addDistributeGoods").data("count");
                    }
                    if(targetTab === 'record'){ // 推广订单回填
                        $('#search_content').val($recordList.search);
                    }
                } else {
                    baseUtils.show.redTip('没有数据，请稍后再试');
                }
            },
            error: function (xhr, status, err) {
                baseUtils.hideLoading("mainContentLoading");
                console.log(err);
                alert("服务器出小差了，请稍后再试！");
            }
         });

    },
    //弹窗提醒是否打开精选，
    showOpenChosenTip: function () {
        $.alert("是否允许小鹅通分销市场平台选取您的课程？一旦选取成功，工作人员将与您联系（本功能一经开启不可关闭）", "info", {
            oktext: "立即开启",
            onOk: function () {
                $.ajax("/chosen/chosen_enable", {
                    type: "POST",
                    dataType: "json",
                    data: {
                        is_enable_chosen: 1,//0-关闭， 1-开启
                    },
                    success: function (result) {
                        if (result.code == 0) {
                            baseUtils.show.blueTip("开启成功");
                            $chosenShop.changeSwitchState();
                        } else {
                            alert("服务器出小差了，请稍后再试！");
                        }
                    },
                    error: function (xhr, status, err) {
                        console.log(err);
                        alert("服务器出小差了，请稍后再试！");
                    }
                });
            },
        });
    },
    switchContentTab: function (targetState) {  //"goods", "record"
        if (targetState == "goods") {
            $("#_goodsListTab").addClass("_activeTab");
            $("#_recordListTab").removeClass("_activeTab");
        } else if (targetState == "record") {
            $("#_recordListTab").addClass("_activeTab");
            $("#_goodsListTab").removeClass("_activeTab");
        }

    },
    /**
     * 切换按钮的状态
     */
    changeSwitchState: function () {

        var $target = $("._switchOperateArea ._functionSwitch"),
            state = !$("._switchOperateArea").data("switch_state");
        if (state) {
            $target.css({"background-color": "#2FCE6F"});
            $target.children("._switchDescText").css({"left": "9px"});
            $target.children("._switchDescText").text("开启");
            $target.children("._switchButtonIcon").css({"left": "40px"});
        } else {
            $target.css({"background-color": "#c2c2c2"});
            $target.children("._switchDescText").css({"left": "44px"});
            $target.children("._switchDescText").text("关闭");
            $target.children("._switchButtonIcon").css({"left": "0px"});
        }
        $("._switchOperateArea").data("switch_state", state);

    },

};














