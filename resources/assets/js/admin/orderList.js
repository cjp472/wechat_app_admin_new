
var search_content;
var order_attr;
var generate_type;
var timeRange = {
    start: '',
    end: ''
}

$(document).ready(function () {
    setTopUrlCookie('payadmin_listop','财务管理');
    keyEnter($('#pay_search_btn'));

    $("select[name=order_type], select[name=distribute]").on("change", function () {
        $('#pay_search_btn').click();
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

    //申请导出订单按钮
    $('#applyExport').on('click', function() {
        $("#ExportModal").modal('show');
    });
    //点击申请导出
    $('#applyOrderToExcel').on('click', function() {
        showLoading();
        var export_time = $.trim( $('#export_time').val() );

        var chkObjs=null; 
        var Office2003=0;
        var obj=document.getElementsByName("selectOffice");
        for (var i=0;i<obj.length;i++){ //遍历Radio 
            if(obj[i].checked){ 
                chkObjs=obj[i].value;
                if(chkObjs==1){
                    Office2003=2003;
                }
            } 
        } 
        // alert(Office2003);
        

        
       

        //导出数据
        //if(start_time != '' && end_time != '' && start_time < end_time){
        if(export_time != ''){
            //var url = '/export_excel?start_time='+start_time+'&end_time='+end_time;
            // var url = '/export_order_excel?export_time='+export_time;
            // window.location.href = url;
            var url = '/excel/order?export_time='+export_time+"&&version="+Office2003;
            console.log(url);
            $("#ExportModal").modal('hide');
            $.ajax(url, {
                type: 'GET',
                // data: {
                //     export_time: export_time
                // },
                // dataType: 'json',
                success: function(json) {
                    hideLoading();
                    window.location.href=url;
                    // if(json==1) {
                    //     baseUtils.show.blueTip('申请成功，请稍后前往申请列表下载');
                    // } else if(json==0) {
                    //     window.location.href = '/excel/order?export_time='+export_time;
                    // } else if(json==-1) {
                    //     baseUtils.show.redTip('申请失败，请稍后重试！');
                    // }
                },
                error: function(xhr, status, err) {
                    hideLoading();
                    console.error(err);
                    baseUtils.show.redTip('下载失败，请稍后重试！');
                }
            });
        }else{
            hideLoading();
            baseUtils.show.redTip('请检查时间选择是否正确');
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
    $('#optional').click(function() { //时间选择器下拉
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


//回显搜索框内的值
function reBack() {
//     var select_content = GetQueryString("select_content");
//     $("#select_content")
//     .val(select_content || '');

    var select_type = GetQueryString("select_type");
    if( select_type ){
        $("#select_type option[value="+select_type+"]").prop('selected', true);
    }

    var order_type = GetQueryString('order_type');
    if( order_type ){
        $("#order_type option[value="+order_type+"]").prop('selected', true);
    }

    var distribute = GetQueryString('distribute');
    if( order_type ){
        $("#distribute_type option[value="+distribute+"]").prop('selected', true);
    }

    var startTime = GetQueryString('start_time'),
        endTime = GetQueryString('end_time');

    timeRange = {
        start: startTime || '',
        end: endTime || ''
    }
}

//调到详情页
function jumpDetail(info)
{
    var userId=info;
    resetUrl('/customerdetail?userId='+userId);
}

//订单记录弹框
function exportOrderRecords() {
    $("#ExportModal").modal('show');
}

//导出订单记录
function exportorderToExcel() {
    showLoading();
    var export_time = $('#export_time').val();
    var Params = {};
    Params['export_time'] = export_time;

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
