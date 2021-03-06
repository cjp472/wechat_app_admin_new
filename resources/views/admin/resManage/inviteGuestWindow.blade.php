

{{--两步操作
    1、初始化：$inviteGuest.init();
    2、打开窗口： $inviteGuest.openGuestSettingWindow(aliveId);
--}}

<div class="guestSettingWindow" id="guestSettingWindow">
    <div class="windowHeader">
        <span class="headerText1">嘉宾设置</span>
        <span class="headerText2">标签为讲师的嘉宾在直播间可以接受用户的打赏</span>
        <div class="windowCloseIcon" id="windowCloseIcon1">
            <img src="/images/icon_Pop-ups_close.svg">
        </div>
    </div>
    <div class="windowContentRegion">
        <div class="windowContentWrapper1">
            <div class="windowContent1 clearfix" id="windowContent1">
                {{-- 嘉宾信息 --}}
            </div>
        </div>
        @include("admin.functionManage.localLoading", ["id" => "guestSettingLoading"])
    </div>
    <div class="windowBtnWrapper clearfix">
        <div class="btnBlue btnMid addGuestBtn" id="addGuestBtn">添加嘉宾</div>
        <div class="xeBtnDefault btnMid confirmAddGuestBtn" id="confirmAddGuestBtn">确定</div>
    </div>
</div>

<div class="searchGuestWindow" id="searchGuestWindow">
    <div class="windowHeader">
        <span class="headerText1">搜索用户</span>
        <span class="headerText2">标签为讲师的嘉宾在直播间可以接受用户的打赏</span>
        <div class="windowCloseIcon" id="windowCloseIcon2">
            <img src="/images/icon_Pop-ups_close.svg">
        </div>
    </div>
    <div class="windowSearchArea">
        <input class="inputDefault windowSearchInput" id="windowSearchInput" placeholder="请输入用户昵称">
        <img src="/images/search.png">
        <div class="xeBtnDefault btnMid windowSearchBtn" id="windowSearchBtn">搜索</div>
    </div>
    <div class="windowContentWrapper2">
        <div class="windowContent2 clearfix" id="windowContent2">
            {{--可添加嘉宾信息--}}
        </div>

        @include("admin.functionManage.localLoading", ["id" => "searchGuestLoading"])
    </div>
    <div class="windowBtnWrapper clearfix">
        <div class="qrCodeHoverBoxWrapper">
            <div class="xeBtnDefault btnMid addGuestBtn" id="inviteWeiXinFriend">邀请微信好友</div>
            <div class="qrCodeHoverBox">
                <img class="_closeHoverBox" src="/images/icon_Pop-ups_close.svg" >
                <div class="_hoverBoxText">微信扫描二维码<br>邀请微信好友为嘉宾</div>
                <div class="_qrCodeImgWrapper" id="_qrCodeImgWrapper">

                </div>
            </div>
        </div>
        <div class="btnBlue btnMid confirmAddGuestBtn" id="confirmSaveAddedGuest">确定</div>
    </div>
</div>

<script type="text/javascript">
    var $inviteGuest = (function () {
        var $inviteGuest = {
            guestListPageIndex: 1,
            isSearchGuestWindowToBottom: false,
            isSearchGuestWindowLoading: false,
            clickAliveId: -1,
            guestListSearchContent: "",
            isQrCodeBoxOpen: false
        }
        var tempGuestInfo = {};//临时添加嘉宾信息列表

        $inviteGuest.init= function () {

            /*********************** 嘉宾设置窗口 *************************/

            $("#guestSettingWindow").on("click", "#windowCloseIcon1", function () {
                baseUtils.hideWindow("guestSettingWindow");
            });
            $("#guestSettingWindow").on("click", "#addGuestBtn", function () {
                baseUtils.hideWindow("guestSettingWindow");
                $inviteGuest.guestListPageIndex = 1;
                $inviteGuest.isSearchGuestWindowToBottom = false;
                $("#windowContent2").html("");
                $("#windowSearchInput").val("");
                baseUtils.showWindow("searchGuestWindow");
            });
            //移除嘉宾
            $("#guestSettingWindow").on("click", ".deleteSingleGuest", function () {
                var $parent = $(this).parents(".singleGuestInfo1"),
                    userId = $parent.data("user_id");
                $parent.css({"height": "0", "padding": "0"});
                setTimeout(function () {
                    delete tempGuestInfo[userId];   //删除临时数据
                    $parent.remove();
                    var count = $("#windowContent1").find(".singleGuestInfo1").length;
                    if (count == 0) {
                        $("#windowContent1").html(
                            '<div class="windowNoData" id="windowNoData">暂无嘉宾，请点击下方按钮快速添加嘉宾</div>'
                        );
                    }
                }, 300);
            });
            //确定按钮
            $("#confirmAddGuestBtn").click(function () {
                var $guestList = $("#windowContent1").find(".singleGuestInfo1"),
                    params = [],
                    user_arr = [],
                    isDataEmpty = false;
                $guestList.each(function () {
                    var userId = $(this).data("user_id"),
                        userName = $(this).children(".singleGuestName1").text(),
                        roleName = $.trim($(this).children(".singleGuestRole").val());

                    if (roleName.length==0) {
                        isDataEmpty = true;
                        return false;
                    }

                    if(user_arr.indexOf(userId) < 0){
                        params.push({
                            alive_id: $inviteGuest.clickAliveId,
                            role_name: roleName,
                            user_name: userName,
                            user_id: userId
                        });
                        user_arr.push(userId);
                    }

                });
                if (isDataEmpty) {
                    baseUtils.show.redTip("邀请的嘉宾，没有设置标签，不能保存");
                    return false;
                }
                baseUtils.showLoading("guestSettingLoading");
                $.ajax("/save_added_guest", {
                    type: "POST",
                    dataType: "json",
                    data: {
                        params: params,
                        alive_id: $inviteGuest.clickAliveId
                    },
                    success: function (result) {
                        baseUtils.hideLoading("guestSettingLoading");
                        if (result.code == 0) {
                            baseUtils.hideWindow("guestSettingWindow");
                            baseUtils.show.blueTip("保存成功");
                        } else {
                            alert("网络问题，请稍后再试");
                        }
                    },
                    error: function (xhr, status, err) {
                        baseUtils.hideLoading("guestSettingLoading");
                        console.log(err);
                        alert("服务器出小差了，请稍后再试！");
                    }
                });


            });


            /*********************** 搜索嘉宾窗口 *************************/
            //点×
            $("#searchGuestWindow").on("click", "#windowCloseIcon2", function () {
                baseUtils.hideWindow("searchGuestWindow");
                baseUtils.showWindow("guestSettingWindow");
                $inviteGuest.getAddedGuestList(showNewlyAddedGuest, false);
            });
            //搜索
            $("#searchGuestWindow").on("click", "#windowSearchBtn", function () {
                if ($inviteGuest.isSearchGuestWindowLoading) {
                    baseUtils.show.redTip("正在搜索中，请稍后再试吧");
                    return false;
                }
                $inviteGuest.guestListSearchContent = $.trim($("#windowSearchInput").val());
                $inviteGuest.guestListPageIndex = 1;
                $inviteGuest.isSearchGuestWindowToBottom = false;
                $('#searchGuestWindow .windowContentWrapper2').scrollTop(0);
                getAllGuestList();
            });
            $("#windowSearchInput").on("keypress", function (e) {
                if (e.keyCode == 13) {
                    $("#windowSearchBtn").click();
                }
            });
            //滑动加载更多
            $('#searchGuestWindow .windowContentWrapper2').scroll(function(e) {
                var DivHeight = $('#windowContent2').height(),
                    ScrollHeight = $(this).height(),
                    ScrollTop = $(this).scrollTop();
                if ((ScrollTop + ScrollHeight >= DivHeight - 5)
                    && !$inviteGuest.isSearchGuestWindowLoading
                    && !$inviteGuest.isSearchGuestWindowToBottom) {
                    getAllGuestList();
                }
            });
            //邀请微信好友
            $("#searchGuestWindow").on("click", "#inviteWeiXinFriend", function () {
                if ($inviteGuest.isQrCodeBoxOpen) {
                    $(".qrCodeHoverBox").css({"height": "0", "opacity": "0"});
                    $inviteGuest.isQrCodeBoxOpen = false;
                    return false;
                }
                $(".qrCodeHoverBox").css({"height": "240px", "opacity": "1"});
                $inviteGuest.isQrCodeBoxOpen = true;
                //生成二维码
                $.ajax("/invite_guest_url", {
                    type: "POST",
                    dataType: "json",
                    data: {
                        alive_id: $inviteGuest.clickAliveId
                    },
                    success: function (result) {
                        if (result.code == 0) {
                            $("#_qrCodeImgWrapper").html("");
                            createQrCode("_qrCodeImgWrapper", result.url, 116, 116);
                        } else {
                            alert("网络问题，请稍后再试");
                        }
                    },
                    error: function (xhr, status, err) {
                        console.log(err);
                        alert("服务器出小差了，请稍后再试！");
                    }
                });

            });
            //隐藏-邀请微信好友
            document.getElementById("searchGuestWindow").addEventListener("click", function (e) {
                if ($inviteGuest.isQrCodeBoxOpen) {
                    var $target = $(e.target);
                    if ($target.hasClass("_closeHoverBox") ||
                        (!$target.hasClass("qrCodeHoverBox") && $target.parents(".qrCodeHoverBox").length==0)) {
                        $(".qrCodeHoverBox").css({"height": "0", "opacity": "0"});
                        $inviteGuest.isQrCodeBoxOpen = false;
                        e.stopPropagation();
                    }
                }
            }, true);

            //保存添加的嘉宾
            $("#searchGuestWindow").on("click", "#confirmSaveAddedGuest", function () {
                baseUtils.hideWindow("searchGuestWindow");
                baseUtils.showWindow("guestSettingWindow");
                $inviteGuest.getAddedGuestList(showNewlyAddedGuest, true);
            });

        };
        $inviteGuest.openGuestSettingWindow = function (aliveId) {
            $inviteGuest.clickAliveId = aliveId;
            tempGuestInfo = {};
            $("#windowContent1").html("");
            baseUtils.showWindow("guestSettingWindow");
            $inviteGuest.getAddedGuestList();
        };
        $inviteGuest.getAddedGuestList = function(callBack, args) {
            baseUtils.showLoading("guestSettingLoading");
            $.ajax("/get_added_guest_list", {
                type: "POST",
                dataType: "json",
                data: {
                    id: $inviteGuest.clickAliveId,
                },
                success: function (result) {
                    baseUtils.hideLoading("guestSettingLoading");

                    if (result.code == 0) {
                        var htmlStr = "";
                        $.each(result.data, function (k, v) {
                            htmlStr +=
                                '<div class="singleGuestInfo1" data-user_id="'+v.user_id+'">'+
                                    '<img src="'+v.wx_avatar+'" class="singleGuestAvatar1">'+
                                    '<div class="singleGuestName1" title="'+v.user_name+'">'+v.user_name+'</div>'+
                                    '<input class="inputDefault singleGuestRole" value="'+v.role_name+'">'+
                                    '<div class="deleteSingleGuest">移除</div>'+
                                '</div>';
                        });
                        if (htmlStr.length == 0) {
                            htmlStr =
                                '<div class="windowNoData" id="windowNoData">暂无嘉宾，请点击下方按钮快速添加嘉宾</div>';
                        }
                        $("#windowContent1").html(htmlStr);

                        if (callBack) {
                            callBack.apply(window, [args]);
                        }

                    } else {
                        baseUtils.show.redTip("网络问题，请稍后再试");
                    }
                },
                error: function (xhr, status, err) {
                    baseUtils.hideLoading("guestSettingLoading");
                    console.log(err);
                    alert("服务器出小差了，请稍后再试！");
                }
            });
        };
        /**
         * @param isAdd boolean 是否添加窗口二中新加的嘉宾数据
         */
        function showNewlyAddedGuest(isAdd) {

            // 1、刷新 窗口一<guestSettingWindow>，
            // 2、判断参数<isAdd>, 添加嘉宾信息到 临时数组：tempGuestInfo[]，
            //
            // 4、遍历 tempGuestInfo[]， 生成新添加的嘉宾页面数据，
            // 5、有 - 添加页面数据；无 - 不做处理。
            if (isAdd) {
                var checkList = $("#windowContent2").find(".selectSingleGuest:checked:not('.disabled')"),
                    userIdArr = [];
                $.each(tempGuestInfo, function (k, v) {  //生成 user_id 数组
                    userIdArr.push(v.userId);
                });
                checkList.each(function () {
                    var $parent = $(this).parents(".singleGuestInfo2"),
                        wx_avatar = $parent.data("wx_avatar"),
                        wx_nickname = $parent.data("wx_nickname"),
                        user_id = $parent.data("user_id");

                    if (userIdArr.indexOf(user_id) == -1) { //排除重复
                        tempGuestInfo[user_id] = {
                            wxAvatar: wx_avatar,
                            wxNickname: wx_nickname,
                            userId: user_id
                        };
                    }
                });
            }
            var htmlStr = "";
            $.each(tempGuestInfo, function (k, v) {
                htmlStr +=
                    '<div class="singleGuestInfo1" data-user_id="'+v.userId+'">'+
                        '<img src="'+v.wxAvatar+'" class="singleGuestAvatar1">'+
                        '<div class="singleGuestName1" title="'+v.wxNickname+'">'+v.wxNickname+'</div>'+
                        '<input class="inputDefault singleGuestRole" value="讲师">'+
                        '<div class="deleteSingleGuest">移除</div>'+
                    '</div>';
            });
            if (htmlStr.length>0) {
                $("#windowContent1").find("#windowNoData").remove();
                $("#windowContent1").append(htmlStr);
            }

        };
        function getAllGuestList() {
            baseUtils.showLoading("searchGuestLoading");
            $inviteGuest.isSearchGuestWindowLoading = true;
            $.ajax("/get_all_guest_list", {
                type: "POST",
                dataType: "json",
                data: {
                    alive_id: $inviteGuest.clickAliveId,
                    search: $inviteGuest.guestListSearchContent,
                    page: $inviteGuest.guestListPageIndex,
                },
                success: function (result) {
                    baseUtils.hideLoading("searchGuestLoading");
                    $inviteGuest.isSearchGuestWindowLoading = false;

                    var $area = $("#windowContent2"),
                        htmlStr = "",
                        offset = result.page_offset;

                    $.each(result.data, function (k, v) {
                        htmlStr +=
                            '<div class="singleGuestInfo2" data-user_id="'+v.user_id+
                                    '" data-wx_avatar="'+v.wx_avatar+'" data-wx_nickname="'+v.wx_nickname+'">'+
                                '<input type="checkbox" '+(v.state==0?'checked disabled':'')+' class="selectSingleGuest '+(v.state==0?'disabled':'')+'" id="'+v.user_id+'">'+
                                '<label for="'+v.user_id+'" class="singleGuestLabel">'+
                                    '<img src="'+v.wx_avatar+'" class="singleGuestAvatar2">'+
                                    '<div class="singleGuestName2" title="'+v.wx_nickname+'">'+v.wx_nickname+'</div>'+
                                '</label>'+
                            '</div>';
                    });
                    if (htmlStr.length == 0) {
                        htmlStr =
                            '<div class="windowNoData" id="windowNoData">暂无数据</div>';
                    }
                    if ($inviteGuest.guestListPageIndex == 1) {
                        $area.html(htmlStr);
                        $area.append('<div class="isDown">更多数据加载中</div>');
                    } else {
                        $area.find('.isDown').before(htmlStr);
                    }
                    if (offset.current_page >= offset.total_pages) {      //数据加载完毕后的操作
                        if (offset.total_count > 10) {
                            $area.find('.isDown').text("数据已加载完毕");
                        } else {
                            $area.find('.isDown').hide();
                        }
                        $inviteGuest.isSearchGuestWindowToBottom = true;
                    }
                    $inviteGuest.guestListPageIndex ++;

                },
                error: function (xhr, status, err) {
                    baseUtils.hideLoading("searchGuestLoading");
                    $inviteGuest.isSearchGuestWindowLoading = false;
                    console.log(err);
                    alert("服务器出小差了，请稍后再试！");
                }
            });
        };


        return $inviteGuest;
    })();

</script>

<style type="text/css">
    /*********************** 嘉宾设置 - start *************************/
    .guestSettingWindow {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 520px;
        height: 480px;
        margin-top: -260px;
        margin-left: -240px;
        z-index: 10000;
        background-color: #fff;
        display: none;
        position: fixed;
    }
    .windowHeader {
        height: 52px;
        line-height: 52px;
        background-color: #fafbfc;
        border-bottom: solid 1px #f2f2f2;
    }
    .headerText1 {
        font-size: 16px;
        color: #353535;
        margin-left: 20px;
    }
    .headerText2 {
        font-size: 14px;
        color: #888888;
        margin-left: 10px;
    }
    .windowCloseIcon {
        width: 12px;
        height: 12px;
        margin: 20px 20px 0;
        float: right;
    }
    .windowCloseIcon>img {
        width: 100%;
        height: 100%;
        float: left;
        cursor: pointer;
    }
    .windowContentRegion {
        width: 100%;
        height: 350px;
        position: relative;
    }
    .windowContentWrapper1 {
        width: 100%;
        height: 350px;
        overflow-y: scroll;
    }
    .windowContent1 {
        width: 100%;
        padding: 15px 20px 30px 20px;
    }
    .windowNoData {
        width: 100%;
        height: 20px;
        font-size: 14px;
        text-align: center;
        color: #888888;
        margin-top: 156px;
    }
    ._localContent {
        top: initial;
        bottom: 130px;
    }
    .singleGuestInfo1 {
        width: 100%;
        height: 70px;
        line-height: 70px;
        padding: 15px 0;
        overflow: hidden;
        -webkit-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    .singleGuestAvatar1 {
        width: 40px;
        height: 40px;
        float: left;
        margin-left: 20px;
        border-radius: 20px;
    }
    .singleGuestName1 {
        width: 115px;
        margin-left: 15px;
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        color: #353535;
        float: left;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .singleGuestRole {
        width: 160px;
        margin-top: 2px;
        text-align: center;
    }
    .deleteSingleGuest {
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        float: right;
        color: #e64340;
        cursor: pointer;
    }

    .windowBtnWrapper {
        height: auto;
        margin: 0 30px 0;
        padding-top: 20px;
        border-top: 1px solid #f2f2f2;
    }
    .qrCodeHoverBoxWrapper {
        position: relative;
        float: left;
    }
    .addGuestBtn {
        margin-left: 120px;
    }
    .qrCodeHoverBox {
        position: absolute;
        bottom: 36px;
        left: 70px;
        width: 200px;
        /*height: 240px;*//*#BB743D*/
        height: 0;
        opacity: 0;
        border-radius: 2px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        border: solid 1px #dbdbdb;
        overflow: hidden;
        -webkit-transition: all 0.4s ease;
        transition: all 0.4s ease;
    }
    ._closeHoverBox {
        width: 12px;
        height: 12px;
        float: right;
        margin: 10px;
        cursor: pointer;
    }
    ._hoverBoxText {
        width: 126px;
        height: 40px;
        margin: 30px auto 0;
        line-height: 20px;
        font-size: 14px;
        text-align: center;
        color: #353535;
    }
    ._qrCodeImgWrapper {
        width: 120px;
        height: 120px;
        margin: 20px auto 0;
        padding: 2px;
    }
    .confirmAddGuestBtn {
        margin-left: 20px;
    }
    /*********************** 嘉宾设置 - end *************************/

    /*********************** 搜索嘉宾 - start *************************/
    .searchGuestWindow {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 520px;
        height: 480px;
        margin-top: -260px;
        margin-left: -240px;
        z-index: 10000;
        background-color: #fff;
        display: none;
        position: fixed;
    }
    .windowSearchArea {
        width: 100%;
        height: 56px;
        padding: 20px 0 0 20px;
        position: relative;
    }
    .windowSearchArea>img {
        width: 16px;
        height: 16px;
        position: absolute;
        top: 30px;
        left: 30px;
    }
    .windowSearchInput {
        width: 390px;
        padding-left: 30px;
    }
    .windowSearchBtn {
        width: 80px;
        margin-right: 10px;
        float: left;
    }
    .windowContentWrapper2 {
        width: 100%;
        height: 295px;
        position: relative;
        overflow-y: scroll;
    }
    .windowContent2 {
        width: 100%;
        padding: 10px 20px 30px 20px;
    }
    .singleGuestInfo2 {
        width: 100%;
        height: 60px;
        line-height: 60px;
        padding: 10px 0;
        overflow: hidden;
        -webkit-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    .selectSingleGuest {
        width: 14px;
        height: 14px;
        margin: 12px 20px 0 0 !important;
        float: left;
    }
    .singleGuestLabel {
        height: 40px;
        line-height: 40px;
        float: left;
        margin: 0;
    }
    .singleGuestAvatar2 {
        width: 40px;
        height: 40px;
        float: left;
        border-radius: 20px;
    }
    .singleGuestName2 {
        width: 340px;
        margin-left: 15px;
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        color: #353535;
        float: left;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    /*********************** 搜索嘉宾 - end *************************/

    .isDown {
        line-height: 50px;
        text-align: center;
        color: #666;
        -webkit-animation: shake 1s linear infinite alternate;
        animation: shake 1s linear infinite alternate;
    }

    @-webkit-keyframes shake{
        from{
            opacity: 0;
        }
        to{
            opacity: 1;
        }
    }
    @keyframes shake{
        from{
            opacity: 0;
        }
        to{
            opacity: 1;
        }
    }

    /*********************** materialize *************************/

</style>















