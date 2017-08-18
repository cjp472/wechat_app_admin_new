/** 
 * Created by Jervis 2017-05-25 
*/
// selectBar


(function(){
    var nameArr=[];
    var dateArr=[];
    var couponName;
    var dateStart;
    var dateEnd;


    //日期选择器初始化
    // aliveTimeConfig(".dateSetInput");
    // function select(){
    //     $().on('click',function(){

    //     })
    // }
    var dataRangeInstance = new pickerDateRange('SelectData', { //初始化时间插件
        isTodayValid : true,
        defaultText : ' ~ ',
        stopToday:false,//不让选择时间范围止于当日
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
    $('.editCoupon').on('click',function(){
        var editId=$(this).attr('data-edit-id');
        console.log("/coupon/edit?id="+editId);
        window.location.href="/coupon/edit?id="+editId;
    })
    setTime();
    function setTime(){
        $('#optional').click(function() { //时间选择器下拉
            $('#dropdown-toggle').dropdown('toggle');
        });
        $('#SelectData').off('click').text('全部优惠券');  //设置开始结束时间
        reBack();
        updateTime(timeRange);
    }
    +function(){
    $('#searchButton').on('click',function(){
        console.log('clicktest');
        var select_state = $(".couponSelect ").val();
        var search_content = $.trim($('.searchInputBox').val());
        var start_time=$('#startTime').val();
        var end_time=$('#endTime').val();

        var url="/index?coupon_name=" + search_content + "&start_time=" + start_time + "&end_time=" + end_time +"$select_state=" + select_state;
            window.location.href = url;
        // reBack(search_content);
        })
    }()


    function updateTime (time) {
        $('#startTime').val(time.start);
        $('#endTime').val(time.end);
        if(time.start && time.start!='2016'){
            $('#SelectData').text(time.start + ' ~ ' + time.end);
        } else {
            $('#SelectData').text('全部优惠券');
        }
    }
    
    //回显搜索框内的值
    function reBack(input) {

        var select_state = GetQueryString("select_state");
        if( select_state ){
            $(".couponSelect option[value="+select_state+"]").prop('selected', true);
        }

        // var coupon_name = GetQueryString('coupon_name');
        // if( coupon_name ){
        //     $(".searchOutBox .searchInputBox").prop('value', coupon_name);
        // }
        // var search_content = input;
        // if(search_content){
        //     alert('reback test');
        //     alert(search_content)
        //     windows.onload=$('.searchInputBox').val(search_content);
        // }
        var startTime = GetQueryString('start_time'),
            endTime = GetQueryString('end_time');

        timeRange = {
            start: startTime || '',
            end: endTime || ''
        }
    }
    //复制到剪贴板
    var clipboard = new Clipboard('.copyhref');
    clipboard.on('success', function(e) {
        baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
        e.clearSelection();
    });

    //结束优惠券
    (function (){
        $('.overCoupon').on('click',function(){
            // console.log("overCoupon is exist")
            var id=$(this).attr('data-style');
            // console.log(id);
            $.alert( "确认结束，结束后用户将不能领取该优惠券，已领取的用户可继续使用。","info",{
                icon:  "blue",
                onOk: function() {
                    showLoading();
                    $.ajax('end/'+id,{
                        type: 'POST',
                        dataType: 'json',
                        success:function(data){

                            if(data.code==0){
                                baseUtils.show.blueTip(data.msg);
                                setTimeout(function() {
                                    reloadPage();
                                }, 300);
                            }else{
                                baseUtils.show.redTip(data.msg);
                                hideLoading();
                            }

                        }

                    })
                }
            })
        })
        
    })();
})();