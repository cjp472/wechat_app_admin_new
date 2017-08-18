
var search_content;
var order_attr;
var generate_type;
var timeRange = {
    start: '',
    end: ''
}
$(document).ready(function () {
    //筛选参数的回显
    reBack();


    setTopUrlCookie('payadmin_listop','财务管理');
    keyEnter($('#pay_search_btn'));

    $("select[name=generate_type]").on("change", function () {
        $("#pay_search_btn").click();
    });


    $('.datetimepicker-months').find('.month').click(function () {
        return false;
    });

    $('#export_time').on('click', function () {
        if($('.datetimepicker-days').css('display')=='block'){
            $('.datetimepicker-days').find('.switch').trigger("click");
        }

        if($('.datetimepicker').css('display')=='block'){
            //alert('ok!!');
        }

    });


    var dataRangeInstance = new pickerDateRange('SelectData', { //初始化时间插件
        isTodayValid : true,
        defaultText : ' ~ ',
        inputTrigger : 'optional',
        theme : 'ta',
        success : function(obj) {
            timeRange = {
                start: obj.startDate,
                end: obj.endDate
            }
            updateTime(timeRange);
        }
    });
    $('#SelectRange').on('click', 'li', function(e) {
        var ele = $(this),
            type = ele.data('type'),
            text = ele.text();
        if(type == 'all') {
            timeRange = {
                start: '',
                end: ''
            };
        } else if(type == 'nowMonth') {
            timeRange = {
                start: getNowMonth(),
                end: getNowDay()
            };
        }
        updateTime(timeRange);
    });
    $('#optional').click(function() {
        $('#dropdown-toggle').dropdown('toggle');
    });
    $('#SelectData').off('click').text('全部记录');  //设置开始结束时间



    updateTime(timeRange);

});

function updateTime (time) {
    $('#startTime').val(time.start);
    $('#endTime').val(time.end);
    if(time.start && time.start!='2016'){
        $('#SelectData').text(time.start + ' ~ ' + time.end);
    } else {
        $('#SelectData').text('全部记录');
    }
}


//回显时间选择器的值
function reBack(){

    var startTime = GetQueryString('start_time'),
        endTime = GetQueryString('end_time');

    timeRange = {
        start: startTime || '',
        end: endTime || ''
    }
}



//删除订单
function deletePurchase(user_id,payment_type,resource_type,product_id,resource_id){
    $.alert("仅删除记录，不会自动退款，您确定要删除吗?", "info", {
        btn: 3,
        onOk: function () {
            deletePurchaser(user_id,payment_type,resource_type,product_id,resource_id);
        },
    });
}
function deletePurchaser(user_id,payment_type,resource_type,product_id,resource_id){
    showLoading();
    var allParams = {};
    allParams['user_id'] = user_id;
    allParams['payment_type'] = payment_type;
    allParams['resource_type'] = resource_type;
    allParams['product_id'] = product_id;
    allParams['resource_id'] = resource_id;
    //writeObj(allParams);
    $.post('/delete_purchase', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code == 0) {
            baseUtils.show.blueTip(msg,function () {window.location.reload()},1500);
        } else
        {
            baseUtils.show.redTip(msg);
        }
    });
}


//调到详情页
function jumpDetail(info){
    resetUrl('/customerdetail?userId='+info);
}

//订单记录弹框
function exportOrderRecords() {
    $("#ExportModal").modal('show');
}

//导出订单记录
function exportorderToExcel() {
    showLoading();
    var export_time = $('#export_time').val();
    // var start_time = $('#start_time').val();
    // var end_time = $('#end_time').val();
    var Params = {};
    Params['export_time'] = export_time;
    //Params['start_time'] = start_time;
    //Params['end_time'] = end_time;

    //导出数据
    //if(start_time != '' && end_time != '' && start_time < end_time){
    if(export_time != ''){
        //var url = '/export_excel?start_time='+start_time+'&end_time='+end_time;
        var url = '/export_order_excel?export_time='+export_time;
        window.location.href = url;
        hideLoading();
        $("#ExportModal").modal('hide');
    }else{
        baseUtils.show.redTip('请检查时间选择是否正确');
    }
}


//订购记录弹框
function exportRecords() {
    $("#ExportModal").modal('show');
    //初始化时间选择器
    // exportTimepickerconfig('#export_time');
    // datetimepickerconfig('#start_time');
    // datetimepickerconfig('#end_time');
}

//导出订购记录
function exportToExcel() {
    showLoading();
    var export_time = $('#export_time').val();
   // var start_time = $('#start_time').val();
   // var end_time = $('#end_time').val();
    var chkObjs=null; 
    var Office2003=0;
    var obj=$("[name='selectOffice']");    
    if(obj){
        for (var i=0;i<obj.length;i++){ //遍历Radio 
            if(obj[i].checked){ 
                chkObjs=obj[i].value;
                    if(chkObjs==1){
                        Office2003=2003;
                    }
             } 
        } 
    }

    var Params = {};
    Params['export_time'] = export_time;
    //Params['start_time'] = start_time;
    //Params['end_time'] = end_time;

    //导出数据
    //if(start_time != '' && end_time != '' && start_time < end_time){
    if(export_time != ''){
        //var url = '/export_excel?start_time='+start_time+'&end_time='+end_time;

        var url = '/excel/purchase?export_time='+export_time+"&&version="+Office2003;
        window.location.href = url;
        hideLoading();
        $("#ExportModal").modal('hide');
    }else{
        baseUtils.show.redTip('请检查时间选择是否正确');
    }
}