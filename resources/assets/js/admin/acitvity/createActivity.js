/**
 * Created by Neo on 2017/3/8.
 */

$(function () {

    createActivity.init();
});



// 业务函数
var createActivity = (function () {
    var createActivity = {};

    var submitLimit = false;     // 表单提交限制标记

    var activityPicUrl = "";    // 活动图片的路径

    createActivity.type =  parseInt($("#type").val());

    createActivity.isSelectPackage = false;     //  是否选了专栏

    createActivity.init = function () {

        // 时间选择器初始化
        // $(".activityDate").datetimepicker({
        //     weekStart : 1,
        //     minView : "day",
        //     format : 'yyyy-mm-dd hh:00:00',
        //     autoclose : true    //选择日期后自动关闭
        // });
        aliveTimeConfig(".activityDate");
        // 报名时间选项切换
        $("input[name='chooseActivityTime']").change(function(){
            if(parseInt($(this).val())){
                $(".enrollDate").show();
            }
            else{
                $(".enrollDate").hide();
            }
        });

        // 添加其他信息的控件
        $(".addTableArea").click(function () {
            var index = $(".addTableArea").index($(this));
            $("#otherTableWrapper").append($(".activityTableCopy").eq(index).html());

            if($("#otherTableWrapper").children().length){
                $(".otherInfoTips").hide();
            }
            else{
                $(".otherInfoTips").show();
            }

        });

        // 删除其他信息的控件
        $("#otherTableWrapper").on("click",".deleteTable",function () {
            var deleteTable = $("#otherTableWrapper .deleteTable");
            if(deleteTable.length>0){
                var index = deleteTable.index($(this));
                $("#otherTableWrapper .baseInfoWrapper").eq(index).remove();
            }

            if($("#otherTableWrapper").children().length){
                $(".otherInfoTips").hide();
            }
            else{
                $(".otherInfoTips").show();
            }
        });


        // 上传活动海报
        $(".uploadActivityPic").on("change",function (e) {
            console.log("change")
            if(this.files && this.files.length>0){
                var file = this.files[0];
                var imgName = file.name;
                //alert(imgName);
                var ext,idx;
                idx = imgName.lastIndexOf(".");
                if (idx != -1){
                    ext = imgName.substr(idx+1).toUpperCase();
                    ext = ext.toLowerCase( );
                    //alert(file);
                    //alert("后缀="+ext+"位置="+idx+"路径="+resourceLocalUrl);
                    if (ext != 'jpg' && ext != 'png' && ext != 'jpeg' && ext != 'gif'){
                        //document.all.submit_upload.disabled=true;
                        baseUtils.show.blueTip("请上传图片类型的文件哦~");
                        //alert("2.只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                        return;
                    }
                } else {
                    document.all.submit_upload.disabled=true;
                    baseUtils.show.blueTip("请上传图片类型的文件哦~");
                    //alert("只能上传.jpg  .png  .jpeg  .gif类型的文件!");
                    return;
                }


                // 限制上传大小
                var limitSize = 2;

                // 限制图片在2MB内
                if($uploadFile.checkFileSize(file,limitSize)){
                    $uploadFile.uploadPic(file,
                        // 成功回调
                        function (data) {
                            baseUtils.show.blueTip("上传成功！");
                            //data = JSON.parse(data);
                            activityPicUrl = data.data.access_url;
                            if(activityPicUrl){
                                showActivityPic(activityPicUrl);
                            }
                        },
                        // 失败回调
                        function (data) {
                            console.log(data)
                            baseUtils.show.redTip("上传失败！");

                        });
                }
                else{
                    baseUtils.show.redTip("上传图片限制在"+limitSize+"MB内！");
                }
            }
            else{
                console.log(this.files)
            }
        });


        // 根据是否选了专栏判定是否要填上架时间
        $("#package_list").on("change",function () {
            if($(this).children(":selected").val()){    // 选定了专栏
                $(".activityStartTime").show();
                $("#start_at").val("");

                createActivity.isSelectPackage = true;
                $("#payTicketTableWrapper").addClass("hide");   //删除收费票
                $("#payTicketTip").removeClass("hide");

            }
            else{
                $(".activityStartTime").hide();
                $("#start_at").val("");

                createActivity.isSelectPackage = false;
                $("#payTicketTip").addClass("hide");
                $("#payTicketTableWrapper").removeClass("hide");
            }
        });

        // 删除活动海报
        $("#deletePic").on("click",function () {
            hideActivityPic();
            // 清空选择，否则change没法监听
            $('.uploadActivityPic').each(function () {
                $(this).val("");
            });
        });


        modal.initTicketInfo();

        $('#preview').on('click',function(){
            var ue = UE.getEditor('activityDesc'); // 活动详情
            var html = ue.getContent();
            document.getElementById('preview_content').innerHTML = html;
            $('.preview_con').addClass('active');
            $('.preview_box').addClass('active');
            document.documentElement.style.overflow = "hidden";
        });
        $('.preview_con').on('click',function(){
            $(this).removeClass('active');
            $('#preview_content').html('');
            $('.preview_box').removeClass('active');
            document.documentElement.style.overflow = "auto";
        });
        // 保存提交
        $("#saveActivity").on("click",function () {

            if(submitLimit){
                baseUtils.show.redTip("不能重复提交");

            } else {

                var title = $.trim($("#title").val()),// 活动名称
                    place = $.trim($("#place").val()),// 活动地点
                    activity_start_at = $.trim($("#activity_start_at").val()),// 活动开始时间
                    activity_end_at = $.trim($("#activity_end_at").val()),// 活动结束时间
                    is_default_enroll_time = parseInt($("input[name='chooseActivityTime']:checked").val()),// 报名时间选项
                    enroll_start_at,        //报名开始时间
                    enroll_end_at;          // 报名结束时间

                if(is_default_enroll_time) {
                    enroll_start_at = $.trim($("#enroll_start_at").val());
                    enroll_end_at = $.trim($("#enroll_end_at").val());
                } else {
                    enroll_start_at = "";
                    enroll_end_at = "";
                }

                var img_url = $("#img_url").attr("src") || activityPicUrl,// 活动海报
                    actor_num = parseInt($("#actor_num").val()) || 0,// 活动人数

                    ue = UE.getEditor('activityDesc'), // 活动详情
                    descrb = ue.getContent(),

                    form_field = [                  // 报名表单
                        {
                            field_name : '姓名',
                            type : 2,
                            required : 1,
                            placeholder : ""
                        },
                        {
                            field_name : '手机号码',
                            type : 1,
                            required : 1,
                            placeholder : "",
                            need_confirm : 0
                        }
                    ];

                $("#otherTableWrapper .baseInfoWrapper").each(function () {     // 报名表单其他信息的内容
                    var formObj = {},
                        infoTableRequied = $(this).find(".infoTableRequied:checked"),
                        infoTableTitle = $(this).find(".infoTableTitle").eq(0),
                        infoTablePlaceholder = $(this).find(".infoTablePlaceholder").eq(0);

                    formObj.field_name = infoTableTitle.val();
                    formObj.type = parseInt(infoTableTitle.data("type"));
                    formObj.required = infoTableRequied.length>0 ? 1 :0;
                    formObj.placeholder = infoTablePlaceholder.val();

                    if($.trim(formObj.field_name)){
                        form_field.push(formObj);
                    }
                });

                var start_at = $.trim($("#start_at").val()),                // 上架时间
                    is_confirm = $("#is_confirm:checked").length>0 ? 1 : 0,   // 是否需要审核
                    package = $("#package_list option:selected"),           // 所属专栏
                    package_id =  package.val(),
                    package_name = package_id ? package.html() : "";

                var activityInfo = {    // 活动信息

                    title : title,                          //     活动名称
                    place : place,                          //     活动地点
                    activity_start_at : activity_start_at,  //     活动开始时间
                    activity_end_at : activity_end_at,      //     活动结束时间
                    is_default_enroll_time : is_default_enroll_time,        // 默认活动结束前都可以报名
                    enroll_start_at : enroll_start_at,                      //     报名开始时间
                    enroll_end_at : enroll_end_at,          //     报名结束时间
                    img_url : img_url,                      //     活动海报
                    actor_num : actor_num,                  //     活动人数
                    descrb : descrb,                        //     活动详情
                    form_field : JSON.stringify(form_field),//     报名表单
                    start_at : start_at,                    //      上架时间
                    is_confirm : is_confirm                 //      是否需要审核
                };

                if(checkActivityForm(activityInfo)){    // 检查表单

                    // 选定专栏后才验证上架时间
                    if(package_id){
                        if($formCheck.emptyString(activityInfo.start_at)){
                            baseUtils.show.redTip("上架时间不能为空！");
                            return false;
                        }
                        if(!$formCheck.checkTime(activityInfo.start_at)){
                            baseUtils.show.redTip("上架时间格式错误！");
                            return false;
                        }
                        if(!$formCheck.checkTimeOrder(activityInfo.start_at,activityInfo.activity_end_at)){
                            baseUtils.show.redTip("上架时间要小于活动结束时间！");
                            return false;
                        }
                    }

                    //  检查是否有添加票务
                    var ticketCount = $(".payTicketTableBody").length;
                    if (!$("#freeTicketTableBody").hasClass("hide")) {
                        ticketCount ++;
                    }
                    if (ticketCount == 0) {
                        baseUtils.show.redTip("票种不能为空，请添加票种");
                        return false;
                    }

                    //  检查免费票务状态
                    if (modal.freeTicketEditState) {
                        baseUtils.show.redTip("免费票处于编辑状态");
                        return false;
                    }
                    //  保存免费票务信息
                    var ticketParams = new Array();       //

                    if (! $("#freeTicketTableBody").hasClass("hide")) {

                        var freeTicketId = $("#freeTicketTableBody").data("ticket_id"),
                            freeTicketName = $("#freeTicketName").val(),
                            freeTicketCount = $("#freeTicketCount").val(),
                            ticketExplanation = $("#ticketExplanationArea").val(),
                            isNeedCheck = $("#isFreeTicketCheck").data("check_state");

                        var freeTicketItem = {
                            id : freeTicketId,
                            ticket_name : freeTicketName,
                            ticket_price : 0,
                            ticket_count : freeTicketCount,
                            extra : ticketExplanation,
                            is_need_check : isNeedCheck,
                        };

                        ticketParams[0] = freeTicketItem;
                    }

                    if (!createActivity.isSelectPackage) {              //没有选中专栏 或 会员 （频道）
                        //  检查收费票务信息
                        if (modal.payTicketEditState) {
                            baseUtils.show.redTip("收费票处于编辑状态");
                            return false;
                        }
                        //  保存收费票务信息
                        var count = $(".payTicketTableBody").length;
                        if (count > 0) {

                            $(".payTicketTableBody").each(function () {

                                var payTicketId = $(this).attr("id"),
                                    payTicketName = $(this).find(".payTicketName").val(),
                                    payTicketCount = $(this).find(".payTicketCount").val(),
                                    payTicketPrice = $(this).find(".payTicketPrice").val() * 100,
                                    payTicketExtra = $(this).find(".payTicketExplanationArea").val();

                                var payTicketItem = {
                                    id : payTicketId,
                                    ticket_name : payTicketName,
                                    ticket_price : payTicketPrice,
                                    ticket_count : payTicketCount,
                                    extra : payTicketExtra,

                                }
                                ticketParams[ticketParams.length] = payTicketItem;
                            });
                        }
                    }


                    var allParams = {               //请求提交参数
                        params : activityInfo,
                        package_id : package_id,
                        package_name : package_name,
                        ticketParams : ticketParams
                    };

                    submitLimit = true;     // 表单提交限制标记

                    if(createActivity.type === 0){                             // 新增活动
                        saveActivityInfo("/uploadActivity", allParams);

                    } else if(createActivity.type == 1){                       // 编辑活动
                        allParams.params.id = $("#activityId").val();       // 编辑活动要带id
                        saveActivityInfo("/saveActivity", allParams);
                    }

                } else {
                    return false;
                }

            }

        });


        $(document).ajaxError(function(event, xhr, opt) {           // 提交错误处理
            console.info(xhr.status, xhr.statusText);
            console.error(opt);
            baseUtils.show.redTip("网络错误，操作失败!");
            submitLimit = false;
        });

    };

    function saveActivityInfo(url, allParams) {
        console.log(allParams);
        showLoading();
        $.post(url, allParams, function (data) {
            if(parseInt(data.code) === 0){
                hideLoading();
                baseUtils.show.blueTip("保存成功");
                setTimeout(function () {
                    window.location.href = '/activityManage'
                },700);
            } else {
                hideLoading();
                baseUtils.show.redTip(data.msg);
                submitLimit = false;
            }
        })
    }

    // 检查提交表单
    function checkActivityForm(info) {
        if($formCheck.emptyString(info.title)){
            baseUtils.show.redTip("活动名称不能为空！");
            return false;
        }

        if($formCheck.emptyString(info.place)){
            baseUtils.show.redTip("活动地点不能为空！");
            return false;
        }

        if($formCheck.emptyString(info.activity_start_at)){
            baseUtils.show.redTip("活动开始时间不能为空！");
            return false;
        }

        if(!$formCheck.checkTime(info.activity_start_at)){
            baseUtils.show.redTip("活动开始时间格式错误！");
            return false;
        }

        if($formCheck.emptyString(info.activity_end_at)){
            baseUtils.show.redTip("活动结束时间不能为空！");
            return false;
        }

        if(!$formCheck.checkTime(info.activity_end_at)){
            baseUtils.show.redTip("活动结束时间格式错误！");
            return false;
        }

        if(!$formCheck.checkTimeOrder(info.activity_start_at,info.activity_end_at)){
            baseUtils.show.redTip("活动结束时间要大于活动开始时间！");
            return false;
        }

        // 如果是自定义时间
        console.log(info.is_default_enroll_time);
        if(info.is_default_enroll_time === 1 ){
            if($formCheck.emptyString(info.enroll_start_at)){
                baseUtils.show.redTip("报名开始时间不能为空！");
                return false;
            }
            if(!$formCheck.checkTime(info.enroll_start_at)){
                baseUtils.show.redTip("报名开始时间格式错误！");
                return false;
            }
            if($formCheck.emptyString(info.enroll_end_at)){
                baseUtils.show.redTip("报名结束时间不能为空！");
                return false;
            }
            if(!$formCheck.checkTime(info.enroll_end_at)){
                baseUtils.show.redTip("报名结束格式错误！");
                return false;
            }
            if(!$formCheck.checkTimeOrder(info.enroll_start_at,info.enroll_end_at)){
                baseUtils.show.redTip("报名结束时间要大于报名开始时间！");
                return false;
            }
        }
        if(isNaN(info.actor_num)||info.actor_num<0){
            baseUtils.show.redTip("请填写正确的活动人数！");
            return false;
        }
        return true;
    }

    // 显示活动图片
    function showActivityPic(url) {
        if(url){
            $("#img_url").attr("src",url).show();
            $(".noPicPreview").hide();
        }
    }

    // 隐藏并删除活动图片
    function hideActivityPic() {
        $("#img_url").attr("src","").hide();
        $(".noPicPreview").show();
        activityPicUrl = "";
    }

    return createActivity;

})();

var modal = (function () {

    var modal = {};

    modal.freeTicketEditState = false;      //免费票编辑状态

    modal.payTicketEditState = false;   //付费票列表编辑状态， true - 有票处于编辑状态，false - 没有票处于编辑状态
    modal.editedPayTicketName = "";   //编辑状态付费票名称，
    modal.editPayTicketDom = ''         //，编辑状态的节点 <class='editPayTicket'>

    modal.initTicketInfo = function () {

        /*********************** 处理免费票 - start *************************/

        // 添加免费票种
        $("#newFree").click(function () {
            $("#freeTicketTableBody").removeClass("hide");
            $("#freeTicketDesc").removeClass("hide");
            $("#newFree").addClass("hide");

            changeFreeTicketState(0);
        });
        // 删除免费票种
        $("#deleteFreeTicket").click(function () {
            $("#newFree").removeClass("hide");
            $("#freeTicketTableBody").addClass("hide");
            $("#freeTicketTableBody").data("ticket_id" ,"");
            $("input.freeTicketItem").val("");
            $("#ticketExplanationArea").val("");
            $("input#isFreeTicketCheck").removeAttr("checked");
            modal.freeTicketEditState = false;

        });
        // 编辑/保存免费票种
        $("#editFreeTicket").click(function () {
            if (modal.freeTicketEditState) {     //  置为保存状态
                var freeTicketName =  $("#freeTicketName").val();
                if ($formCheck.emptyString(freeTicketName)) {
                    baseUtils.show.redTip("票种名称不能为空，请重新输入");
                    return false;
                }
                var freeTicketCount =  $("#freeTicketCount").val();
                if ($formCheck.emptyString(freeTicketCount)) {
                    baseUtils.show.redTip("请输入正确的免费票数量");
                    return false;
                }
                if (!$formCheck.isNumPositiveAndInteger(freeTicketCount)) {
                    baseUtils.show.redTip("请输入正确的免费票数量");
                    return false;
                }
                var freeTicketExtra = $("#ticketExplanationArea").val();
                if (freeTicketExtra.length > 198) {
                    baseUtils.show.redTip("票种说明字数不能超过198个字符");
                    return false;
                }
                changeFreeTicketState(1);

            } else {    //  置为编辑状态
                if (modal.payTicketEditState) {
                    baseUtils.show.redTip("收费票处于编辑状态，请先保存收费票");
                    return false;
                }
                changeFreeTicketState(0);

            }
        });

        //  是否审核免费票
        $("input#isFreeTicketCheck").click(function () {
            var state = $("input#isFreeTicketCheck").data("check_state");
            if (state == "1") {
                $("input#isFreeTicketCheck").data("check_state", "0");
                return;
            } else {
                $("input#isFreeTicketCheck").data("check_state", "1");
                return;
            }

        });

        /*********************** 处理免费票 - end *************************/

        /*********************** 处理收费票 - start *************************/
        // 添加收费票种
        $("#newPayTicket").click(function () {
            if (modal.payTicketEditState) {
                baseUtils.show.redTip("[" + modal.editedPayTicketName + "]处于编辑状态，请先保存");
                return false;
            }
            modal.payTicketEditState = true;
            modal.editedPayTicketName = "新建收费票";

            var $payTicketList = $("#payTicketBodyWrapper").children(".payTicketTableBody");
            $("#payTicketBodyWrapper").append(getPayTicketBody($payTicketList.length));

            modal.editPayTicketDom = $payTicketList.eq($payTicketList.length).find(".editPayTicket");

        });

        // 编辑/保存收费票种
        $("#payTicketBodyWrapper").on("click", ".editPayTicket", function (e) {

            var $this = $(e.target),
                id = $this.attr("id");

            if ($this.attr("edit_state") == 1) {//置为编辑状态

                if (modal.freeTicketEditState) {
                    baseUtils.show.redTip("免费票处于编辑状态，请先保存免费票");
                    return false;
                }
                if (modal.payTicketEditState) {
                    $.alert("[" + modal.editedPayTicketName + "]处于编辑状态,是否进行保存?", "info", {
                        btn: 3,
                        onOk: function () {
                            modal.editPayTicketDom.click();
                        }
                    });
                    return false;
                }
                modal.payTicketEditState = true;
                var payTicketName = $this.parents("#payTicketOperate").prevAll(".payTicketName").val();
                modal.editedPayTicketName = payTicketName;
                modal.editPayTicketDom = $this;

                changePayTicketState($this, 0);

            } else {//置为保存状态

                var payTicketName =  $.trim($this.parents("#payTicketOperate").prevAll(".payTicketName").val());
                if ($formCheck.emptyString(payTicketName)) {
                    baseUtils.show.redTip("票种名称不能为空，请重新输入");
                    return false;
                }

                var payTicketCount =  $.trim($this.parents("#payTicketOperate").prevAll(".payTicketCount").val());
                if (! $formCheck.isNumPositiveAndInteger(payTicketCount)) {
                    baseUtils.show.redTip("票数应大于0，且不能为空，请重新输入");
                    return false;
                }

                var payTicketPrice =  $.trim($this.parents("#payTicketOperate").prevAll(".payTicketPrice").val()) * 100;    //  单位：分
                if ($formCheck.emptyString(payTicketPrice) || payTicketPrice <= 0) {
                    baseUtils.show.redTip("票价不可为空且必须大于0，请重新输入");
                    return false;
                }
                if (payTicketPrice > baseUtils.maxInputPrice * 100) {
                    baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 元");
                    return false;
                }

                var payTicketExtra = $this.parents(".payTicketTableBody").find(".payTicketExplanationArea").val();
                if (payTicketExtra.length > 198) {
                    baseUtils.show.redTip("票种说明字数不能超过198个字符");
                    return false;
                }

                var isNameExist = false;
                $("input.payTicketName").each(function () {
                    if (id != $(this).data("index") && payTicketName == $.trim($(this).val())) {
                        isNameExist = true;
                        return false;
                    }
                });

                if (isNameExist) {
                    baseUtils.show.redTip("票种名称已存在，请重新输入");
                    return false;
                }

                modal.payTicketEditState = false;
                modal.editedPayTicketName = "新建收费票";

                changePayTicketState($this, 1);
            }

        });

        //删除收费票
        $("#payTicketBodyWrapper").on("click", ".deletePayTicket", function (e) {
            var $this = $(e.target);
            var edit_state = $this.prev(".editPayTicket").attr("edit_state");
            if (edit_state == 0) {
                modal.payTicketEditState = false;
                modal.editedPayTicketName = "新建收费票";
            }
            $this.parents(".payTicketTableBody").remove();

        });


        /*********************** 处理收费票 - end *************************/
    };

    /**
     * @param state 0-置为编辑状态, 1-置为保存状态
     */
    function changeFreeTicketState(state) {
        if (state == 0) {
            modal.freeTicketEditState = true;
            $("#editFreeTicket").attr("edit_state", 0);
            $("#editFreeTicket").html("保存此票");
            $(".freeTicketItem").removeAttr("readOnly");
            $(".freeTicketItem").css("border", "1px solid #dcdcdc");
            $(".freeTicketDesc").removeClass("hide");

        } else  {
            modal.freeTicketEditState = false;
            $("#editFreeTicket").attr("edit_state", 1);
            $("#editFreeTicket").html("编辑此票");
            $(".freeTicketItem").attr("readOnly", "true");
            $(".freeTicketItem").css("border", "none");
            $(".freeTicketDesc").addClass("hide");

        }
    }

    function changePayTicketState(currentDom, state) {

        if (state == 0) {                                       //置为编辑状态
            currentDom.attr("edit_state", 0);
            currentDom.html("保存此票");
            currentDom.parents("#payTicketOperate").prevAll(".payTicketItem").removeAttr("readOnly");
            currentDom.parents("#payTicketOperate").prevAll(".payTicketItem").css("border", "1px solid #dcdcdc");
            currentDom.parents(".payTicketInfo").next(".payTicketDesc").removeClass("hide");

        } else  {                                               //置为保存状态
            currentDom.attr("edit_state", 1);
            currentDom.html("编辑此票");
            currentDom.parents("#payTicketOperate").prevAll(".payTicketItem").attr("readOnly", "true");
            currentDom.parents("#payTicketOperate").prevAll(".payTicketItem").css("border", "none");
            currentDom.parents(".payTicketInfo").next(".payTicketDesc").addClass("hide");

        }
    }

    function getPayTicketBody(index) {
        var payTicketBody =
            '<li class="payTicketTableBody" id="">' +
                '<div class="payTicketInfo">' +
                    '<input class="payTicketItem inputDefault payTicketName" data-index="' + index + '" placeholder="vip票" />' +
                    '<input class="payTicketItem inputDefault payTicketCount" type="text" placeholder="0(不限)" />' +
                    '<input class="payTicketItem inputDefault payTicketPrice" type="number" placeholder="请输入价格" />' +
                    '<div class="ticketItem" id="payTicketOperate">' +
                        '<div class="toolBox">' +
                            '<ul>' +
                                '<li class="editPayTicket" edit_state="0" id="' + index + '" >保存此票</li>' +
                                '<li class="deletePayTicket">删除此票</li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="payTicketDesc">' +
                    '<div class="ticketExplanationWrapper">' +
                        '<span class="ticketExplanation">票种说明(选填)</span>' +
                        '<textarea class="payTicketExplanationArea ticketTextArea" placeholder="您可以在这里说明此票的特点"></textarea>' +
                    '</div>' +
                '</div>' +
            '</li>';

        return payTicketBody;

    }

    return modal;

})();
