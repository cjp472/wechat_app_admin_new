var needRefresh = false;
var pay_type = 2; //类型、2表示单个付费,3表示专栏。
var re_type = 1 ;// 资源类型 0 专栏 1 图文 2 音频 3 视频 4 直播
var re_id = ""; //资源id
var pa_id = ""; //专栏id
var ch_type = 0; //渠道类型：0-针对具体资源（单笔或专栏），1-首页（不针对具体资源）
var re_title = "" ;//渠道资源的标题

var search_content;
var comment_attr;
var resource_type;

function resource(id, price, title){
    this.id = id;
    this.price = price;
    this.title = title;
}

$(document).ready(function () {
    reBack();
    setTopUrlCookie('channel_listop','统计分发');
    re_id = $('#image_text_list').find("option:selected").attr('value');
    pa_id = $('#image_text_list').find("option:selected").data('package_id');
    re_title = $('#image_text_list').find("option:selected").html();

    //初始化资源【类型】下拉框
    changeResourceType();
    //初始化资源【列表】下拉框
    initResourceSelect();

    $(document).keypress(function(e) {
        // 回车键事件
        if(e.which == 13) {
            searchChannel();
        }
    });



})

//增加试听渠道
$('#lisAddBtn').on('click',function(){
    $('.lisAddBox').addClass('active');
});
$('.lisCancel').on('click',function(){
    $('.lisAddBox').removeClass('active');
})
$('.lisConfirm').on('click',function(){
    var name = $('.lisAddName input').val();
    if(name==''){
        baseUtils.show.redTip('请输入登录账号');
        return false;
    }
    $.get('/channel/addListen/'+name,{},function(data){
        if(data.code==0){
            $('.lisAddName input').val('');
            $('.lisAddBox').removeClass('active');
            window.location.reload();
        }else {
            baseUtils.show.redTip('网络错误');
        }
    });

})

//提交渠道
function submitChannel() {
    //获取到名称
    var channel_name = $("#channel_name").val();
    var payment_type = pay_type;
    var resource_type = re_type;
    var resource_id = re_id;
    var package_id = pa_id;
    var channel_type = ch_type;
    var resource_title = re_title;

    if(channel_name=='') {
        baseUtils.show.redTip('请输入渠道名称！');
        return;
    } else if (resource_id || package_id || channel_type == 1) {
        console.log('渠道名称：'+channel_name+"》》payment_type："+payment_type+"》》resource_type："+resource_type+"》》resource_id："+resource_id+">>package_id："+package_id+">>channel_type："+channel_type+">>resource_title："+resource_title);
        showLoading();
        var url = "/submit_channel";
        $.post(url, {'channel_name': channel_name,'payment_type':payment_type,'resource_type':resource_type,'resource_id':resource_id,'resource_title':resource_title, 'package_id':package_id, 'channel_type':channel_type}, function (result) {
            needRefresh = true;
            hideLoading();
            var code = result.code;
            var info = result.msg;
            setTimeout(function () {
                if(code==0){
                    baseUtils.show.blueTip(info);

                    refresh();
                }else{
                    baseUtils.show.redTip(result.msg,function(){},2000);
                }
                $("#channel_Modal").modal('hide');
            }, 100);

        });

    } else {
        baseUtils.show.redTip('请选择对应内容！');
        return;
    }

}

function refresh() {
    //判断是否需要刷新界面
    if (needRefresh) {
        location.reload(); //重新加载页面
    }
}

//切换排序
function orderByView(orderView) {
    var order_view = GetQueryString('order_view');
    //获取当前页url
    var objUrl = window.location.href; //alert(reurl);
    //去除url后可能带有的#号
    if(objUrl.indexOf('#')>0){
        objUrl = objUrl.replace("#","");
    }
    if(order_view){
        objUrl = objUrl.replace('order_view='+order_view,'order_view='+orderView);
    }else{
        if(objUrl.indexOf('order_view')>0){
            objUrl = objUrl.replace('order_view='+order_view,'order_view='+orderView);
        }else{
            var join = objUrl.indexOf('?')>0? '&' : '?';
            objUrl = objUrl + join +'order_view=' + orderView;
        }
    }
    //将页码重置到第一页
    if(objUrl.indexOf('page')>0) {
        objUrl = objUrl.replace('page='+ GetQueryString('page'), 'page=1');
    }
    //转向目标地址
    window.location.href = objUrl;
}

//回显输入框和选择框的值
function reBack() {
    $('#channel_search_content').val(search_content);
    if(!!comment_attr) $('#channel_attr option[value='+comment_attr+']').prop('selected', true);
    if(!!resource_type) $('#resource_type option[value='+resource_type+']').prop('selected', true);
    //console.log($('#channel_attr option[value='+comment_attr+']'));
}


//模糊查询渠道
function searchChannel(){
    showLoading();
    var channelAttr = $("#channel_attr").val(); //获取到选中的值
    var resourceType = $("#resource_type").val();
    //获取搜索内容
    var search_content = $.trim( $("#channel_search_content").val() ); //获取到选中的值
    if (search_content.length == 0) {
        var url = "/channel_admin?channel_attr=" + encodeURI(channelAttr) + "&resource_type=" + encodeURI(resourceType);
    } else {
        var url = "/channel_admin?channel_attr=" + encodeURI(channelAttr) + "&resource_type=" + encodeURI(resourceType) + "&search_content=" + encodeURI(search_content);
    }
    window.location = url;

}

//切换资源类型
function changeResourceType() {
    $('#type_list').change(function () {
        var type = $('#type_list').find("option:selected").attr('value');
        type = parseInt(type);
        $('.long').addClass('hide');
        console.log(type);
        switch (type){
            case 0:
                $('#image_text_list').removeClass('hide');
                re_id = $('#image_text_list').find("option:selected").attr('value');
                re_title = $('#image_text_list').find("option:selected").html();
                console.log(0);
                pa_id = $('#image_text_list').find("option:selected").data('package_id');
                pay_type = 2;
                re_type = 1;
                ch_type = 0;
                break;
            case 1:
                $('#audio_list').removeClass('hide');
                re_id = $('#audio_list').find("option:selected").attr('value');
                re_title = $('#audio_list').find("option:selected").html();
                console.log(1);
                pa_id = $('#audio_list').find("option:selected").data('package_id');
                pay_type = 2;
                re_type = 2;
                ch_type = 0;
                break;
            case 2:
                $('#video_list').removeClass('hide');
                re_id = $('#video_list').find("option:selected").attr('value');
                re_title = $('#video_list').find("option:selected").html();
                console.log(2);
                pa_id = $('#video_list').find("option:selected").data('package_id');
                pay_type = 2;
                re_type = 3;
                ch_type = 0;
                break;
            case 3:
                $('#alive_list').removeClass('hide');
                re_id = $('#alive_list').find("option:selected").attr('value');
                re_title = $('#alive_list').find("option:selected").html();
                console.log(2);
                pa_id = $('#alive_list').find("option:selected").data('package_id');
                pay_type = 2;
                re_type = 4;
                ch_type = 0;
                break;
            case 4:
                $('#package_list').removeClass('hide');
                console.log(3);
                re_id = "";
                pa_id = $('#package_list').find("option:selected").attr('value');
                re_title = $('#package_list').find("option:selected").html();
                pay_type = 3;
                re_type = 0;
                ch_type = 0;
                break;
            case 5:
                //首页
                $('.long').addClass('hide');
                console.log(4);
                re_id = "";
                pa_id = "";
                re_title = "首页";
                pay_type = 0;
                re_type = 0;
                ch_type = 1;
                break;
        }
    })
}

//初始化资源下拉框
function initResourceSelect() {
    ChangeResourceId('image_text_list');
    ChangeResourceId('audio_list');
    ChangeResourceId('video_list');
    ChangeResourceId('alive_list');
    ChangeResourceId('package_list');
}

//设置资源的id 和 标题
function ChangeResourceId(SelectId) {
    $('#'+SelectId).change(function () {
        var id = $(this).find("option:selected").attr('value');
        var title = $(this).find("option:selected").html();
        //当前在选择的是专栏
        if(re_type==0){
            pa_id = id;
        }else{
            pa_id = $(this).find("option:selected").data('package_id');
            re_id = id;
        }
        re_title = title;
    })
}