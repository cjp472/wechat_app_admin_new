/**
 * Created by Neo on 2017/3/8.
 */
$(function () {
    business.initCommon();

});

//业务类
var business = (function () {

    var business = {};

    // 操作用户名单（防止重复发送）
    var operateList = [];

    // 防止重复发送消息标志
    var sendMsgLimit = false;

    //选中用户的真实姓名
    var userRealName

    //选中用户的手机号
    var userPhone

    //当前业务类型  默认为enrollment,切换后为attendance
    var businessType = 'enrollment';

    business.activity_id = "";      //  活动id
    business.activity_state = "";      //  活动状态
    business.pageIndex = 1;             //  当前所在页面

    business.initCommon = function () {
        business.activity_id = $("#activity_id").val();
        business.activity_state = $("#activity_state").val();

        business.getActivityPage("get_enrollment_page", {
            activity_id: business.activity_id,
            activity_state: business.activity_state
        }, business.initEnrollment);

        //  TAB 切换
        $(".baseManageTab li.attendanceManage").click(function () {

            if ($(".enrollmentManage").hasClass("baseActiveTab")) {     //  切换到右边 - 签到管理
                businessType = 'attendance';
                $('#businessType').html('签到');
                $(".enrollmentManage").removeClass("baseActiveTab");
                $(".attendanceManage").addClass("baseActiveTab");
            }
            business.getActivityPage("get_attendance_page", {
                activity_id: business.activity_id
            }, business.initAttendance);
        });

        $(".baseManageTab li.enrollmentManage").click(function () {

            if ($(".attendanceManage").hasClass("baseActiveTab")) {         //  切换到左边 - 报名管理
                businessType = 'enrollment';
                $('#businessType').html('报名');
                $(".attendanceManage").removeClass("baseActiveTab");
                $(".enrollmentManage").addClass("baseActiveTab");
            }
            business.getActivityPage("get_enrollment_page", {
                activity_id: business.activity_id,
                activity_state: business.activity_state
            }, business.initEnrollment);

        });

        //初始化二维码图片
        var activity_link = $("#activity_link").val();
        createQrCode("qrCodeWrapper", activity_link, 110, 110);

        //点击下载
        $(".downloadQRCode").click(function () {
            var imgUrl = $(".qrCodeWrapper img").attr("src");
            download(imgUrl);
            return false;
        });

        //关闭二维码签到窗口
        $(".close_icon_wrapper").click(function () {
            $(".show_qrCode_window").fadeOut(300);
        });

        //通过用户审核操作(单个）
        $(document).on("click", ".allowUser", function () {
            var userId = $(this).data("userid");
            if (userId) {
                var userIdArr = [userId];
                operateAction(1, userIdArr);
            }
        });

        //拒绝用户审核操作(单个）
        $(document).on("click", ".refuseUser", function () {
            var userId = $(this).data("userid");
            if (userId) {
                var userIdArr = [userId];
                clickRefuse(userIdArr);
            }
        });

        //作废单个用户的票券操作
        $(document).on("click", ".ejectUser", function () {
            var activityId = $("#activity_id").val();
            var userId = $(this).data("userid");
            $.alert('作废后该用户将不可参与本次活动', 'info', {icon:'red',btn:3,onOk:function () {
                if (userId) {
                    $.post('/change_sign_state',
                        {
                            activity_id:activityId,
                            user_id:userId,
                            state:4
                        },
                        function (data) {
                            if(data.code==0){
                                baseUtils.show.blueTip("作废成功!");
                                business.getActivityPage("get_enrollment_page", {
                                    activity_id: business.activity_id,
                                    activity_state: business.activity_state
                                }, business.initEnrollment);
                            }else{
                                baseUtils.show.redTip(data.msg);
                            }
                        })

                }
            }});

        });

        //分页请求
        $(".contentArea").on('click', ' .pagination a', function(e) {
            var ele = $(e.target),
                url = ele.data('url');
            business.pageIndex = ele.text();
            if ($(".enrollmentManage").hasClass("baseActiveTab")) {     //  在左边 - 报名管理
                business.getActivityPage(url, null, business.initEnrollment);
            } else {                                                    //  在右边 - 签名管理
                business.getActivityPage(url, null, business.initAttendance);
            }

        });

        // 隐藏用户信息栏
        $(document).on("click",function (event) {
            var target = $(event.target);
            var memberInfoBox = target.closest(".memberInfoBox");
            var userInfoWrapper = target.closest(".userInfoWrapper");
            if(!memberInfoBox.length && !userInfoWrapper.length){
                $(".memberInfoBox").css("right", "-400px");
                infoShow = false;
            }
        });

        //关闭个人信息框
        $(".memberInfoBoxClose").click(function () {
            $(".memberInfoBox").css("right", "-400px");
        });

        // 发送消息通知
        $("#sendMsg").on("click",function () {
            // 活动id
            var activity_id = $("#activity_id").val();

            // 发送方式
            var methodArr = [];
            $(".sendMethod").each(function () {
                if($(this).prop("checked")){
                    methodArr.push(parseInt($(this).val()));
                }
            });

            if(methodArr && methodArr.length === 0){
                baseUtils.show.redTip("没有选择发送方式！");
                return;
            }


            // 发送范围
            var activity_state = parseInt($("#sendRange").children(":selected").val());
            // 发送用户
            var userIdArr = [];
            if(activity_state == 2){
                userIdArr = getChoosedUser();
                if(userIdArr && userIdArr.length === 0){
                    baseUtils.show.redTip("没有选择用户！");
                    return;
                }
            }
            // 发送内容
            var notify_content = $.trim($("#sendContent").val());

            if(!notify_content){
                baseUtils.show.redTip("没有填写通知内容！");
                return;
            }

            var notifyParm = {
                activity_id: activity_id,
                activity_state: activity_state,
                notify_type: "",
                user_id_list: userIdArr,
                notify_content: notify_content
            };

            if(!sendMsgLimit){
                sendMsgLimit = true;
                // 发送信息请求
                sendMsg(
                    methodArr,
                    notifyParm,
                    function (data) {
                        baseUtils.show.blueTip(data.msg);
                        $('.massageBox,.darkScreen').hide();
                        sendMsgLimit = false;
                    },
                    function (data) {
                        baseUtils.show.redTip(data.msg);
                        $('.massageBox,.darkScreen').hide();
                        sendMsgLimit = false;
                    })
            }
            else{
                baseUtils.show.redTip("请勿重复操作！");
            }

        });

    };

    business.getActivityPage = function (url, sendData, callback, args) {
        $(".loadingS").fadeIn(300);
        $.ajax({
            type: "GET",
            url: url,
            dataType: "html",
            data: sendData,
            success: function (result) {
                $(".loadingS").fadeOut(100);
                console.log(result.msg);
                if (result && result.length > 0) {
                    $(".contentArea").html(result);
                    if (callback) {
                        callback.apply(window, args);
                    }
                } else {
                    baseUtils.show.redTip("获取信息失败！");
                }
            },
            error: function (xhr, status, err) {
                $(".loadingS").fadeOut(100);
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });

    }

    // 通过用户请求
    function allowUser(userIdArr, success, fail) {
        var activity_id = $("#activity_id").val();
        if (!arrUtils.inArr(operateList, userIdArr)) {
            arrUtils.addArr(operateList, userIdArr);

            $.post('/passActivity', {activity_id: activity_id, user_id_list: userIdArr}, function (data) {
                if (data.code == 0) {
                    if (success) {
                        success(data);
                    }
                }
                else {
                    arrUtils.deleteArr(operateList, userIdArr);
                    if (fail) {
                        fail(data);
                    }
                }
            });
        }
        else {
            baseUtils.show.redTip("不能重复操作");
        }
    }

    // 拒绝用户请求
    function refuseUser(userIdArr, success, fail) {
        var activity_id = $("#activity_id").val();
         //获取拒绝弹出框的拒绝理由
        var reason=$("#refuseReasonInput").val();

        if(!reason){
            baseUtils.show.redTip("请输入拒绝理由！");
            return;
        }

        if (!arrUtils.inArr(operateList, userIdArr)) {
            arrUtils.addArr(operateList, userIdArr);

            $.post('/denyActivity', {activity_id: activity_id, user_id_list: userIdArr,refuse_reason:reason}, function (data) {
                if (data.code == 0) {
                    if (success) {
                        success(data);
                    }
                }
                else {
                    arrUtils.deleteArr(operateList, userIdArr);
                    if (fail) {
                        fail(data);
                    }
                }
            });
        }
        else {
            baseUtils.show.redTip("不能重复操作");
        }

    }


    // 发送消息通知请求
    function sendMsg(methodArr,notifyParm,success,fail) {
        for(var i=0,j=methodArr.length;i<j;i++){
            var parmCopy ={};
            parmCopy =$.extend(parmCopy,notifyParm,true);
            parmCopy.notify_type = methodArr[i];
            $.post('/activityNotify',parmCopy,function (data) {
                if(data.code === 0){
                    if(success){
                        success(data);
                    }
                }
                else{
                    if(fail){
                        fail(data)
                    }
                }
            })
        }
    }

    // 通过/拒绝操作封装 type 1为通过，2为拒绝
    function operateAction(type, userIdArr) {
        var searchContent = $.trim($('#searchContent').val());
        if(userIdArr && userIdArr.length >0){
            if (type === 1) {
                allowUser(
                    userIdArr,
                    function (data) {
                        baseUtils.show.blueTip(data.msg);
                        setTimeout(function () {
                            business.getActivityPage("get_enrollment_page", {
                                activity_id: business.activity_id,
                                activity_state: business.activity_state,
                                searchContent: searchContent,
                                page: business.pageIndex
                            }, business.initEnrollment);
                        }, 500)
                    },
                    function (data) {
                        baseUtils.show.redTip(data.msg);
                    }
                );
            }
            else if (type === 2) {
                refuseUser(
                    userIdArr,
                    function (data) {
                        $(".memberRefuseBox,.darkScreen").fadeOut(300);
                        baseUtils.show.blueTip(data.msg);
                        setTimeout(function () {
                            business.getActivityPage("get_enrollment_page", {
                                activity_id: business.activity_id,
                                activity_state: business.activity_state,
                                searchContent: searchContent,
                                page: business.pageIndex
                            }, business.initEnrollment);
                        }, 500)
                    },
                    function (data) {
                        $(".memberRefuseBox,.darkScreen").fadeOut(300);
                        baseUtils.show.redTip(data.msg);
                    }
                );
            }
        }
        else{
            baseUtils.show.redTip("请选择用户！");
        }


    }

    // 获取所有选中的用户id
    function getChoosedUser() {
        var userIdArr = [];
        $(".chooseUser").each(function () {
            var checkedStatus = $(this).prop("checked");
            var userId = $(this).data("userid");
            if (checkedStatus && userId) {
                userIdArr.push(userId);
            }
        });
        return userIdArr;
    }

    // 点击拒绝理由浮层里的拒绝按钮
    function clickRefuse(userIdArr) {
        if(userIdArr.length < 1){
            baseUtils.show.redTip("请选择用户！");
            return;
        }
        else if(userIdArr.length ==1){
            userRealName=$("."+userIdArr[0]).data("realname");
            userPhone=$("."+userIdArr[0]).data("phone");
            $(".refuseMContent").html(userRealName+"&nbsp&nbsp|&nbsp&nbsp"+userPhone);
            $(".memberRefuseBox,.darkScreen").fadeIn(300);
            $(".rRefuse").off("click").on("click",function () {
                operateAction(2, userIdArr);
            })
        }else{
            $(".memberRefuseBox,.darkScreen").fadeIn(300);
            $(".refuseMContent").html("共"+userIdArr.length+"人");
            $(".rRefuse").off("click").on("click",function () {
                operateAction(2, userIdArr);
            })
        }
    }

    function download(src) {
        var $a = $("<a></a>").attr("href", src).attr("download", "活动签到用二维码.png");
        $a[0].click();
    }

    function changeActivitySignState(activityId, userId, signState, callback, args) {

        $.ajax("/change_sign_state", {
            type: "POST",
            dataType: "json",
            data: {
                activity_id: activityId,
                user_id: userId,
                state: signState
            },
            success: function (result) {
                if (result.code == 0) {
                    console.log(result.msg);
                    if (signState == 5) {
                        baseUtils.show.blueTip("签到成功");
                    } else if(signState == 1) {
                        baseUtils.show.blueTip("取消签到成功");
                    }
                    if (callback) {
                        callback.apply(window, args);
                    }
                } else {
                    console.log(result.msg);
                    baseUtils.show.redTip("操作失败，请重试");
                }
            },
            error: function (xhr, status, err) {
                console.log(err);
                baseUtils.show.redTip("网络错误，请稍后再试！");
            }
        });

    }


    /**
     * 初始化报名管理页面js
     */
    business.initEnrollment = function () {


        //  报名管理内部 tab 切换
        $(".activeNavPart").click(function () {
            var searchContent = $.trim($('#searchContent').val());
            if ($(".activeNavPart").hasClass("activeNavPartActive")) {
                business.activity_state = $(this).data("activity_state");
                business.getActivityPage("get_enrollment_page", {
                    activity_id: business.activity_id,
                    activity_state: business.activity_state,
                    searchContent: searchContent
                }, business.initEnrollment);

            }

        });


        // 批量通过用户审核
        $("#batchAllow").on("click", function () {
            var userIdArr = getChoosedUser();
            operateAction(1, userIdArr);
        });

        // 批量拒绝用户审核
        $("#batchRefuse").on("click", function () {
            var userIdArr = getChoosedUser();
            clickRefuse(userIdArr);
        });


        // 选中/取消选中 全部用户
        $("#allChoose").on("click", function () {
            if ($(this).prop("checked")) {
                changeChecked("chooseUser", true)
            }
            else {
                changeChecked("chooseUser", false)
            }
            function changeChecked(className, status) {
                $("." + className).each(function () {
                    $(this).prop("checked", status)
                })
            }
        });


        //根据搜索内容搜索人员
        $('.activeSearchBtn').click(function () {
            var searchContent = $.trim($('#searchContent').val());
            business.getActivityPage("get_enrollment_page", {
                activity_id: business.activity_id,
                activity_state: business.activity_state,
                searchContent: searchContent
            }, business.initEnrollment);
        });

        //  回车搜索
        $(".SearchInput").on('keypress', function (e) {
            if (e.keyCode == 13) {
                $('.activeSearchBtn').click();
            }
        });

        // 点击消息通知
        $(".messageBtn").click(function () {
            $('.massageBox,.darkScreen').fadeIn(300);
        });
        //关闭消息通知的浮层
        $(".msgClose").click(function () {
            $('.massageBox,.darkScreen').fadeOut(300);
        });
        //关闭拒绝弹出框
        $(".rCancel,.RefuseBoxClose").click(function () {
            $(".memberRefuseBox,.darkScreen").fadeOut(300);
        })



        // 用户信息栏的显示状态
        var infoShow = false;
        // 点击的条目标记
        var infoClickId = "";
        // 显示用户信息栏
        $(".infoTbody").on("click","td",function () {
            if(!$(this).hasClass("notShowInfo")){
                var tr = $(this).closest(".userInfoWrapper");
                var imgSrc = tr.data("avatar");
                var memberName = tr.data("nickname");
                var realName = tr.data("realname");
                var memberPhone = tr.data("phone");
                var moreInfo = tr.data("info");
                var state = tr.data("state");
                var userId = tr.data("userid");
                //如果用户不是待审核状态，则隐藏通过拒绝按钮
                if (moreInfo && moreInfo.length > 2) {
                    moreInfo = moreInfo.slice(2);
                }else{
                    moreInfo=[];
                }

                if(userId === infoClickId && infoShow){
                    $(".memberInfoBox").css("right", "-400px");
                    infoClickId = "";
                }
                else{
                    var data = {
                        imgSrc: imgSrc,
                        memberName: memberName,
                        realName: realName,
                        memberPhone: memberPhone,
                        moreInfo: moreInfo,
                        state: state,
                        userId: userId
                    };
                    var content = template('peopleInfo', data);
                    $("#enrollPersonInfo").html(content);
                    $(".memberInfoBox").css("right", "0");
                    infoShow = true;
                    infoClickId = userId;
                }

            }
        });

        //  导出Excel 表格  -   暂时不做选择，导出全部用户
        $('.contentAreaWrapper').on('click','.excelBtn',function () {
            $("#ExportModal").fadeIn();
        });


        $("#applyExcel").on("click",function(){
            var activity_id = $("#activity_id").val();
            // window.location.href = '/activityExportExcle?activity_id=' + activity_id;
            var chkObjs=null; 
            var Office2003=0;
            var obj=$("[name='selectOffice']");                
            for (var i=0;i<obj.length;i++){ //遍历Radio 
                if(obj[i].checked){ 
                    chkObjs=obj[i].value;
                        if(chkObjs==1){
                            Office2003=2003;
                        }
                 } 
            } 

            if(businessType=='enrollment'){
                window.location.href = "/excel/activity" + '?activity_id=' + activity_id + "&version=" + Office2003;
            }else {
                window.location.href = "/excel/attendance" + '?activity_id=' + activity_id + "&version=" + Office2003;
            }



        })
        $(".closePop").click(function () {
            $("#ExportModal").fadeOut();
        })


        // 提交错误处理
        $(document).ajaxError(function() {
            baseUtils.show.redTip("网络错误，操作失败!");
        });

        //初始化分页
        $('.contentArea .list-page .pagination li a').each(function() {
            var $ele = $(this);
            $ele.attr('data-url', $ele.attr('href'));
            $ele.removeAttr('href');
        });

    };

    /**
     * 初始化签到管理页面js
     */
    business.initAttendance = function () {

        //打开二维码签到窗口
        $(".qrCodeAttend").click(function () {
            $(".show_qrCode_window").fadeIn(300);
        });

        //  签到
        $(".changeSignState").on("click", ".confirmSign", function () {
            var $parent = $(this).parent(".changeSignState");
            var user_id = $parent.data('user_id');
            var ticket_type = $parent.data('ticket_type');
            var real_name = $parent.data('real_name');
            var phone = $parent.data('phone');

            $.alert('<span class="alert_real_name">姓名：' + real_name + '</span><span class="alert_phone">手机号：' + phone + '</span>', "success", {
                title: ticket_type == 0 ? "免费票" : "收费票",
                btn: 3,
                oktext: "签到",
                canceltext: "关闭",
                onOk: function () {
                    changeActivitySignState(business.activity_id, user_id, 5, function () {
                        business.getActivityPage("get_attendance_page", {
                            activity_id: business.activity_id, page: business.pageIndex
                        }, business.initAttendance);
                    });
                }
            });

        });

        //  取消签到
        $(".changeSignState").on("click", ".cancelSign", function () {
            var $parent = $(this).parent(".changeSignState");
            var user_id = $parent.data('user_id');
            var ticket_type = $parent.data('ticket_type');
            var real_name = $parent.data('real_name');
            var phone = $parent.data('phone');

            $.alert('<span class="alert_real_name">姓名：' + real_name + '</span><span class="alert_phone">手机号：' + phone + '</span>', "success", {
                title: ticket_type == 0 ? "免费票" : "收费票",
                btn: 3,
                oktext: "取消签到",
                canceltext: "关闭",
                onOk: function () {
                    changeActivitySignState(business.activity_id, user_id, 1, function () {
                        business.getActivityPage("get_attendance_page", {
                            activity_id: business.activity_id, page: business.pageIndex
                        }, business.initAttendance);
                    });
                }
            });

        });

        //  搜索
        $(".searchAttendanceList").click(function () {
            var search_content = $(".phoneNumSearch").val();
            business.getActivityPage("get_attendance_page", {
                activity_id: business.activity_id,
                searchContent: search_content || "",
            }, business.initAttendance);

        });

        //  回车搜索
        $(".phoneNumSearch").on('keypress', function (e) {
            if (e.keyCode == 13) {
                $('.searchAttendanceList').click();
            }
        });

        //初始化分页
        $('.contentArea .list-page .pagination li a').each(function() {
            var $ele = $(this);
            $ele.attr('data-url', $ele.attr('href'));
            $ele.removeAttr('href');
        });

    };


    return business;
})();


// 数组工具函数（可用于防止信息重复提交）
var arrUtils = (function () {
    var arrUtils = {};


    // 从数组中删除指定元素
    arrUtils.removeByValue = function (arr, val) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i] == val) {
                arr.splice(i, 1);
                break;
            }
        }
        return arr;
    };

    // 将B数组元素添加到A数组中（无重复元素）
    arrUtils.addArr = function (arrA, arrB) {
        for (var i = 0, j = arrB.length; i < j; i++) {
            if (arrA.indexOf(arrB[i]) === -1) {
                arrA.push(arrB[i]);
            }
        }
        return arrA;
    };

    // 将B数组元素从A数组中剔除
    arrUtils.deleteArr = function (arrA, arrB) {
        for (var i = 0, j = arrB.length; i < j; i++) {
            if (arrA.indexOf(arrB[i]) !== -1) {
                this.removeByValue(arrA, arrB[i]);
            }
        }
        return arrA;
    };

    // B中元素是否存在A数组中
    arrUtils.inArr = function (arrA, arrB) {
        for (var i = 0, j = arrB.length; i < j; i++) {
            if (arrA.indexOf(arrB[i]) !== -1) {
                return true;
            }
        }
        return false;
    };


    return arrUtils;

})();

