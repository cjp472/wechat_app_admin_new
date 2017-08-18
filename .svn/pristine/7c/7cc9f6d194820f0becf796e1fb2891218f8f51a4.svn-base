
var search_content;
var order_attr;
var ChannelTitle;
var timeRange = {
    start: '',
    end: ''
}

$(document).ready(function () {
    setTopUrlInfo('channel_listop');
    keyEnter($('#pay_search_btn'));
    $("tbody tr").mouseover(function()
    {
        $(this).css({'background-color':'#f5f5f5'});
    }).mouseout(function()
    {
        $(this).css({'background-color':'#fff'});
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
    $('#SelectData').off('click').text('全部订单');  //设置开始结束时间

    //筛选参数的回显
    reBack();

    updateTime(timeRange);

});


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
   var startTime = GetQueryString('start_time'),
       endTime = GetQueryString('end_time');

   timeRange = {
       start: startTime || '',
       end: endTime || ''
   }
}
//调到详情页
function jumpDetail(info) {
    resetUrl('/customerdetail?userId='+info);
}
