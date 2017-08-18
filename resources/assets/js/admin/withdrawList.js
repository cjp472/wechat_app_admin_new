/**
 * Created by fuhaiwen on 2017/1/17.
 */

var timeRange = {
    start: '',
    end: ''
}

$(document).ready(function () {
    reBack();

    //填入搜索值
    // $("select[id='generate_type']").val((getUrlParam("generate_type")==null  || getUrlParam("generate_type")=='') ? '' :getUrlParam("generate_type"));

    setTopUrlCookie('payadmin_listop','财务管理');
    keyEnter($('#pay_search_btn'));
    $("tbody tr").mouseover(function()
    {
        $(this).css({'background-color':'#f5f5f5'});
    }).mouseout(function()
    {
        $(this).css({'background-color':'#fff'});
    });

    //初始化时间选择器
    /*datetimepickerconfig('#start_time');
    datetimepickerconfig('#end_time');*/

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
    $('#SelectData').off('click').text('全部订单');  //设置开始结束时间

    //筛选参数的回显
    reBack();

    updateTime(timeRange);
});


/*
$(function () {

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

    })

});
*/

function updateTime (time) {
    $('#startTime').val(time.start);
    $('#endTime').val(time.end);
    if(time.start && time.start!='2016'){
        $('#SelectData').text(time.start + ' ~ ' + time.end);
    } else {
        $('#SelectData').text('全部订单');
    }
}

//回显输入框和选择框的值
function reBack(){
    var cash_status = GetQueryString("cash_status");
    if( cash_status ){
        $("#cash_status option[value="+cash_status+"]").prop('selected', true);
    }

    var startTime = GetQueryString('start_time'),
        endTime = GetQueryString('end_time');

    timeRange = {
        start: startTime || '',
        end: endTime || ''
    }
}



//订单记录弹框
function exportOrderRecords() {
    $("#ExportModal").modal('show');
}





//请求提现申请页面
function applyWithdrawPage() {
    window.location.href = '/apply_withdraw_page';
}
//跳转绑定微信联系人页面
function getBindwxPage() {
    window.location.href = '/bind_wx_account_page';
}