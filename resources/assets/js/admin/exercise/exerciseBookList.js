/**
 * Created by Administrator on 2017/7/31.
 */

$(document).ready(function () {
    $exeBookList.init();
});

$exeBookList = (function () {
    var $exeBookList = {
        addUserType: 0, //0-老师；1-助教
        exerciseBookId: -1, //点击的作业本条目的id值
    };
    var $private = {
        userListPageIndex: 1,
        userListSearchContent: "",
        isSearchUserWindowLoading: false,
        isSearchUserWindowToBottom: false,
    };

    $exeBookList.init = function () {

        var contentNoData = $("#admin_data").val(),
            currentPageIndex = GetQueryString("page") || 1;

        var lastExerciseBookId = sessionStorage.getItem("lastExerciseBookId"),
            lastResourceType = sessionStorage.getItem("lastResourceType");

        //检查是否刚刚创建完作业本
        if (lastExerciseBookId && lastExerciseBookId != "") {
            sessionStorage.setItem("lastExerciseBookId", "");
            sessionStorage.setItem("lastResourceType", "");
            $exeBookList.exerciseBookId = lastExerciseBookId;

            if (lastResourceType == 4) {
                $.alert("直播作业请前往 [直播列表-嘉宾设置] 进行老师/助教设置。标签为讲师的嘉宾为老师，其余为助教", {
                    title: "直播作业人员管理",
                    btn: 2,
                });
            } else {
                //打开初始化人员管理窗口
                baseUtils.showWindow("roleManageWindow");
                $("#windowContent1").html(
                    '<div class="windowNoData" id="windowNoData1">暂无老师和助教，请点击下方按钮快速添加人员</div>'
                );
            }
        }

        $("._switchOperateArea").on("click", "._functionSwitch", function (e) {

            var $delegateDom = $(e.delegateTarget),
                switchState = $delegateDom.data("switch_state"),
                is_show_exercise_system = 0;

            var currentInfo = {
                $currentDom: $(this),
                $delegateDom: $delegateDom,
                switchState: switchState,
                onText: $delegateDom.data("on_text"),
                offText: $delegateDom.data("off_text")
            }

            if (switchState) {//当前为打开状态
                is_show_exercise_system = 0;
            } else {
                is_show_exercise_system = 1;
            }

            $.ajax("/exercise/set_exercise_book_system_state", {
                type: "POST",
                dataType: "json",
                data: {
                    is_show_exercise_system: is_show_exercise_system,//(0-不开启;1-开启)
                },
                success: function (result) {
                    if (result.code == 0) {
                        baseUtils.show.blueTip(is_show_exercise_system == 1 ?  "开启成功" : "功能已关闭，所有作业内容将不继续在店铺内显示");
                        changeSwitchBtnState(currentInfo);
                        if (is_show_exercise_system == 0) {
                            if (contentNoData) {
                                $("#contentNoData").remove();
                            } else {
                                $("#tableContent").hide();
                                $("#listPage").hide();
                            }
                            $("#tableHeader").after(
                                '<div class="contentNoData" id="functionHasClose">功能已关闭, 作业内容暂时不显示</div>'
                            );
                        } else {
                            $("#functionHasClose").remove();
                            if (contentNoData) {
                                $("#tableHeader").after(
                                    '<div class="contentNoData" id="contentNoData">暂无数据</div>'
                                );
                            } else {
                                $("#tableContent").show();
                                $("#listPage").show();
                            }
                        }
                    } else {
                        baseUtils.show.redTip("网络问题，请稍后再试");
                    }
                },
                error: function (xhr, status, err) {
                    console.log(err);
                    baseUtils.show.redTip("服务器出小差了，请稍后再试！");
                }
            });

        });

        //新建作业本
        $("#createExerciseBook").click(function () {
            window.location.href = "/exercise/create_exercise_book?page_index="+currentPageIndex;
        });

        //作业本的操作
        $(".operateList > li.operate").click(function () {
            var $self = $(this),
                $parent = $self.parents(".exerciseBookOperateArea"),
                type = $self.data("type"),
                resId = $parent.data("resource_id"),
                resType = $parent.data("resource_type"),
                exerciseBookId = $parent.data("exercise_book_id"),
                roleListArr = $parent.data("role_list");

            $exeBookList.exerciseBookId = exerciseBookId;

            switch (type) {
                case "role_manage":
                    openRoleManageWindow(resType, roleListArr);
                    break;
                case "exercise_list":
                    window.location.href =
                        "/exercise/exercise_list?exercise_book_id="+exerciseBookId+"&page_index="+currentPageIndex;
                    break;
                case "edit_exercise_book":
                    window.location.href =
                        "/exercise/edit_exercise_book?exercise_book_id="+exerciseBookId+"&page_index="+currentPageIndex;
                    break;
                default:
                    console.log("参数错误");
                    break;
            }

        });

        /*********************** 人员设置窗口 *************************/
        $("#windowCloseIcon1").click(function () {
            baseUtils.hideWindow("roleManageWindow");
        });
        //添加人员
        $("#windowHoverBox").on("click", "li", function () {
            var type = $(this).data("type");
            if (type == "add_teacher") {//需要加限制
                if (hasSetTeacher()) {
                    baseUtils.show.redTip("一个作业本仅能设置1位老师，请先将已设置的老师改为助教");
                    return false;
                }
                $exeBookList.addUserType = 0;
            } else if (type == "add_assistant") {
                $exeBookList.addUserType = 1;
            }
            openSearchUserWindow();
        });
        // radio 按钮点击切换效果
        $('#windowContent1').on('click', '.roleTypeRadio', function(e) {
            var $self = $(this),
                clickRoleType = $self.children(".circleRadio").data("role_type");

            if ($self.children(".circleRadio").hasClass("radioActive")) {
                return false;
            }
            if (clickRoleType == 0 && hasSetTeacher()) {   //设置为老师
                baseUtils.show.redTip("一个作业本仅能设置1位老师，请先将已设置的老师改为助教");
                return false;
            }
            $self.parents(".roleTypeRadioWrapper").find(".circleRadio").removeClass('radioActive');
            $self.children('.circleRadio').addClass('radioActive');
        });
        //移除用户
        $("#roleManageWindow").on("click", ".deleteSingleUser", function () {
            var $parent = $(this).parents(".singleUserInfo1");

            $parent.css({"height": "0", "padding": "0"});
            setTimeout(function () {
                $parent.remove();
                var count = $("#windowContent1").find(".singleUserInfo1").length;
                if (count == 0) {
                    $("#windowContent1").html(
                        '<div class="windowNoData" id="windowNoData1">暂无老师和助教，请点击下方按钮快速添加人员</div>'
                    );
                }
            }, 300);
        });
        //保存人员
        $("#confirmSaveRole").click(function () {
            var addedUserList = $("#windowContent1").find(".singleUserInfo1"),
                exerciseBookRoles = {};

            addedUserList.each(function () {
                var userId = $(this).data("user_id"),
                    roleType = $(this).find(".circleRadio.radioActive").data("role_type");
                if (exerciseBookRoles[userId] == undefined) {
                    exerciseBookRoles[userId] = {
                        user_id: userId,
                        role_type: roleType
                    }
                }
            });

            baseUtils.showLoading("roleManageLoading");
            $.ajax("/exercise/set_exercise_book_role", {
                type: "POST",
                dataType: "json",
                data: {
                    exercise_book_roles: exerciseBookRoles,
                    exercise_book_id: $exeBookList.exerciseBookId
                },
                success: function (result) {
                    baseUtils.hideLoading("roleManageLoading");
                    if (result.code == 0) {
                        baseUtils.hideWindow("roleManageWindow");
                        baseUtils.show.blueTip("保存成功");
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        baseUtils.show.redTip("网络问题，请稍后再试");
                    }
                },
                error: function (xhr, status, err) {
                    baseUtils.hideLoading("roleManageLoading");
                    console.log(err);
                    alert("服务器出小差了，请稍后再试！");
                }
            });

        });

        /*********************** 人员搜索窗口 *************************/
        $("#windowCloseIcon2").click(function () {
            baseUtils.hideWindow("searchUserWindow");
            baseUtils.showWindow("roleManageWindow");
        });
        //点击搜索
        $("#searchUserWindow").on("click", "#windowSearchBtn", function () {
            $private.userListPageIndex = 1;
            $private.userListSearchContent = $.trim($("#windowSearchInput").val());
            $private.isSearchUserWindowToBottom = false;
            $('#searchUserWindow .windowContentWrapper2').scrollTop(0);
            getAllUserList();
        });
        $("#windowSearchInput").on("keypress", function (e) {
            if (e.keyCode == 13) {
                $("#windowSearchBtn").click();
            }
        });
        //滑动加载更多
        $('#searchUserWindow .windowContentWrapper2').scroll(function(e) {
            var DivHeight = $('#windowContent2').height(),
                ScrollHeight = $(this).height(),
                ScrollTop = $(this).scrollTop();
            if ((ScrollTop + ScrollHeight >= DivHeight - 5) && !$private.isSearchUserWindowLoading
                    && !$private.isSearchUserWindowToBottom) {
                getAllUserList();
            }
        });
        //添加老师时，添加限制，只能选中一个
        $("#windowContent2").on("change", ".selectSingleGuest", function (e) {
            var $target = $(e.target),
                isChecked = $target.prop("checked");
            if (isChecked && $exeBookList.addUserType == 0) {
                var count = $("#windowContent2")
                    .find(".selectSingleGuest:checked").not('.disabledInput').length;
                if (count > 1) {
                    baseUtils.show.redTip("一个作业本仅能设置1位老师");
                    $target.prop("checked", false);
                }
            }
        });
        //添加人员
        $("#confirmSaveAddedUser").click(function () {
            var checkedUser = $(".selectSingleGuest:checked"),
                userArr = {};
            checkedUser.each(function () {
                var $parent = $(this).parents(".singleGuestInfo2"),
                    userId = $parent.data("user_id"),
                    wxAvatar = $parent.data("wx_avatar"),
                    wxNickname = $parent.data("wx_nickname"),
                    roleType = $exeBookList.addUserType;

                if (userArr[userId] == undefined) {
                    userArr[userId] = {
                        user_id: userId,
                        wx_avatar: wxAvatar,
                        wx_nickname: wxNickname,
                        role_type: roleType
                    };
                }
            });

            var storedUserIdArr = getAddedUserIdArr();

            var htmlStr = "";
            $.each(userArr, function(k, v) {
                if (storedUserIdArr.indexOf(k) == -1) {
                    htmlStr += getUserInfoItem(v);
                }
            });
            if (htmlStr.length>0) {
                $("#windowNoData1").remove();
                $("#windowContent1").append(htmlStr);
            }
            baseUtils.hideWindow("searchUserWindow");
            baseUtils.showWindow("roleManageWindow");

        });
    };
    function openRoleManageWindow(resType, roleListArr) {
        if (resType == 4) {
            $.alert("直播作业请前往 [直播列表-嘉宾设置] 进行老师/助教设置。标签为讲师的嘉宾为老师，其余为助教", {
                title: "直播作业人员管理",
                btn: 2,
            });
            return false;
        }
        baseUtils.showWindow("roleManageWindow");
        var htmlStr = "";
        $.each(roleListArr, function (k, v) {
            htmlStr += getUserInfoItem(v);
        });
        if (htmlStr.length == 0) {
            htmlStr =
                '<div class="windowNoData" id="windowNoData1">暂无老师和助教，请点击下方按钮快速添加人员</div>';
        }
        $("#windowContent1").html(htmlStr);

    }
    function openSearchUserWindow() {
        baseUtils.hideWindow("roleManageWindow");
        
        $("#searchUserWindow").find(".headerText1").text(
            ($exeBookList.addUserType==0)?"添加老师":"添加助教"
        );
        $("#windowContent2").html("");
        $("#windowSearchInput").val("");
        baseUtils.showWindow("searchUserWindow");
        
        $private.isSearchUserWindowToBottom = false;
        $private.userListPageIndex = 1;
        $private.userListSearchContent = "";
        getAllUserList();
    }
    function getUserInfoItem(v) {
        var htmlStr =
            '<div class="singleUserInfo1" data-user_id="'+v.user_id+'">'+
                '<img class="singleGuestAvatar1" src="'+v.wx_avatar+'">'+
                '<div class="singleGuestName1" title="'+v.wx_nickname+'">'+v.wx_nickname+'</div>'+
                '<div class="roleTypeRadioWrapper">'+
                    '<div class="roleTypeRadio">'+
                        '<span class="circleRadio '+(v.role_type==0?'radioActive':'')+'" data-role_type="0"></span><span>老师</span>'+
                    '</div>'+
                    '<div class="roleTypeRadio">'+
                        '<span class="circleRadio '+(v.role_type==1?'radioActive':'')+'" data-role_type="1"></span><span>助教</span>'+
                    '</div>'+
                '</div>'+
                '<div class="deleteSingleUser">移除</div>'+
            '</div>';
        return htmlStr;
    }
    function hasSetTeacher() {
        var hasSetTeacher = false,
            activeRadioArr = $("#windowContent1").find(".circleRadio.radioActive");

        activeRadioArr.each(function () {
            var roleType = $(this).data("role_type");
            if (roleType == 0) {
                hasSetTeacher = true;
                return false;
            }
        });

        return hasSetTeacher;
    }
    function getAllUserList() {
        baseUtils.showLoading("searchUserLoading");
        $private.isSearchUserWindowLoading = true;
        $.ajax("/zbsearch", {
            type: "GET",
            dataType: "json",
            data: {
                page: $private.userListPageIndex,
                search: $private.userListSearchContent
            },
            success: function (result) {

                var addedUserArr = getAddedUserIdArr();

                baseUtils.hideLoading("searchUserLoading");
                $private.isSearchUserWindowLoading = false;

                //填充数据
                var $area = $("#windowContent2"),
                    htmlStr = "",
                    offset = result.page_offset;

                $.each(result.data, function (k, v) {
                    var isAdded = (addedUserArr.indexOf(v.user_id) != -1);
                    htmlStr +=
                        '<div class="singleGuestInfo2" data-user_id="'+v.user_id+
                                '" data-wx_avatar="'+v.wx_avatar+'" data-wx_nickname="'+v.wx_nickname+'">'+
                            '<input type="checkbox" '+(isAdded?'checked disabled':'')+' class="selectSingleGuest '+(isAdded?'disabledInput':'')+'" id="'+v.user_id+'">'+
                            '<label for="'+v.user_id+'" class="singleGuestLabel">'+
                                '<img src="'+v.wx_avatar+'" class="singleGuestAvatar2">'+
                                '<div class="singleGuestName2" title="'+v.wx_nickname+'">'+v.wx_nickname+'</div>'+
                            '</label>'+
                        '</div>';
                });
                if (htmlStr.length == 0) {
                    htmlStr =
                        '<div class="windowNoData" id="windowNoData2">暂无数据</div>';
                }
                if ($private.userListPageIndex == 1) {
                    $area.html(htmlStr);
                    $area.append('<div class="isDown">更多数据加载中</div>');
                } else {
                    $area.find('.isDown').before(htmlStr);
                }
                if (offset.current_page >= offset.total_pages) {      //数据加载完毕后的操作
                    var timeRange = 0;
                    if (offset.total_count > 10) {
                        $area.find('.isDown').text("数据已加载完毕");
                        timeRange = 3000;
                    } else {
                        timeRange = 0;
                    }
                    setTimeout(function () {
                        $area.find('.isDown').hide();
                    }, timeRange);
                    $private.isSearchUserWindowToBottom = true;
                }
                $private.userListPageIndex ++;

            },
            error: function (xhr, status, err) {
                baseUtils.hideLoading("searchUserLoading");
                $private.isSearchUserWindowLoading = false;
                console.log(err);
                alert("服务器出小差了，请稍后再试！");
            }
        });
    };
    /**
     * 获取窗口一<roleManageWindow>的用户 id 数组
     * @returns {Array}
     */
    function getAddedUserIdArr() {
        var addedUserList = $("#windowContent1").find(".singleUserInfo1"),
            addedUserArr = [];

        addedUserList.each(function () {
            var userId = $(this).data("user_id");
            addedUserArr.push(userId);
        });
        return addedUserArr;
    };
    function changeSwitchBtnState(currentInfo) {
        var state = !currentInfo.switchState;    //要转变到的状态
        if (state) {
            currentInfo.$currentDom.css({"background-color": "#2FCE6F"});
            currentInfo.$currentDom.children("._switchDescText").css({"left": "9px"}).text(currentInfo.onText);
            currentInfo.$currentDom.children("._switchButtonIcon").css({"left": "40px"});
        } else {
            currentInfo.$currentDom.css({"background-color": "#c2c2c2"});
            currentInfo.$currentDom.children("._switchDescText").css({"left": "44px"}).text(currentInfo.offText);
            currentInfo.$currentDom.children("._switchButtonIcon").css({"left": "0px"});
        }
        currentInfo.$delegateDom.data("switch_state", state);
    };

    return $exeBookList;
})();



























