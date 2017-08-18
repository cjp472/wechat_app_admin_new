/**
 * Created by Peter on 2016/12/13 0013.
 */
var search_content;
var packet_attr;
var state;


$(document).ready(function () {
    reBack();

});

//模糊查询红包
function searchComment(){
    showLoading();
    var packet_attr = $("#packet_attr").val(); //获取到选中的值
    //获取搜索内容
    var search_content = $("#redPacket_search_content").val(); //获取到选中的值
    //红包状态
    var state = $("#state").val(); //获取所选状态
    if (search_content.length == 0) {
        var url = "/redPacket_admin?packet_attr=" + encodeURI(packet_attr)+'&state='+state;
    } else {
        var url = "/redPacket_admin?packet_attr=" + encodeURI(packet_attr) + "&search_content=" + encodeURI(search_content)+'&state='+state;
    }
    window.location = url;
}

//回显输入框和选择框的值
function reBack() {
    var comment_search_content = document.getElementById("redPacket_search_content");

    if (search_content != "") {
        comment_search_content.value = search_content;
        var comment_attr_element = document.getElementById("packet_attr");
        if (comment_attr_element.length > 0) {
            for (var i = 0; i < comment_attr_element.options.length; i++) {
                if (comment_attr_element.options[i].value == packet_attr) {
                    comment_attr_element.options[i].selected = true;
                    break;
                }
            }
        }
    }

    var comment_state_element = document.getElementById("state");
    if (comment_state_element.length > 0) {
        for (var i = 0; i < comment_state_element.options.length; i++) {
            if (comment_state_element.options[i].value == state) {
                comment_state_element.options[i].selected = true;
                break;
            }
        }
    }
}

//跳转到用户详情
function jumpDetail(info)
{
    var info=info;
    var appId=info.split("|")[0];
    var userId=info.split("|")[1];
    window.location.href='/customerdetail?appId='+appId+'&userId='+userId;
}

//导出红包弹框
function exportRedpacket() {
    $("#ExportModal").modal('show');
    //初始化时间选择器
    datetimepickerconfig('#start_time');
    datetimepickerconfig('#end_time');
}

//导出消费记录
function exportToExcel() {
    var start_time = $('#start_time').val();
    var end_time = $('#end_time').val();
    var Params = {};
    Params['start_time'] = start_time;
    Params['end_time'] = end_time;

    //导出数据
    if(start_time != '' && end_time != '' && start_time < end_time){
        var url = '/export_Redpacket?start_time='+start_time+'&end_time='+end_time;
        window.location.href = url;
    }else{
        baseUtils.show.redTip('请检查时间选择是否正确');
    }
}