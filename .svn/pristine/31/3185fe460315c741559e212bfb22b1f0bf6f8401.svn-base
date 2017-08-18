
var search_content;
var resource_attr;

$(document).ready(function () {
    $.cookie('content_create','');
    changeTab();
    reBack();
    reSearch();
    //复制到剪贴板
    (function () {
        var clipboard = new Clipboard('.linkCopy');
        clipboard.on('success', function(e) {
            baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
            e.clearSelection();
        });
    })();
    //复制到剪贴板
    // $('#copyBtn0').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page0').val();}
    // });
    // $('#copyBtn1').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page1').val();}
    // });
    // $('#copyBtn2').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page2').val();}
    // });
    // $('#copyBtn3').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page3').val();}
    // });
    // $('#copyBtn4').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page4').val();}
    // });
    // $('#copyBtn5').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page5').val();}
    // });
    // $('#copyBtn6').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page6').val();}
    // });
    // $('#copyBtn7').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page7').val();}
    // });
    // $('#copyBtn8').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page8').val();}
    // });
    // $('#copyBtn9').zclip({path: "js/external/ZeroClipboard.swf",
    //     copy: function(){return $('#page9').val();}
    // });

});

function changeTab() {
    $("#tab_audio").click(function () { // 音频列表
        window.location.href = "/audio_list";
    });
    $("#tab_video").click(function () { // 视频列表
        window.location.href = "/video_list";
    });
    $("#tab_article").click(function () {// 图文列表
        window.location.href = "/article_list";
    });
    $("#tab_package").click(function () {// 专栏列表
        window.location.href = "/package_list";
    });
    $("#tab_alive").click(function () {// 直播列表
        window.location.href = "/alive";
    });
    $("#tab_member").click(function () {// 会员页
        window.location.href = "/member_list";
    });
}

//更新资源路径
function updateResourceState(resource_type,new_state,id) {
    var allParams = {};
    var params = {};
    params['state'] = new_state;
    allParams['resource_type'] = resource_type;
    allParams['id'] = id;
    allParams['params'] = params;
    $.post('/edit_package_save', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code == 0)
        {
            baseUtils.show.blueTip(msg,function(){window.location.reload()},1500);
        } else
        {
            baseUtils.show.redTip(msg);
        }
    });
}
//删除资源操作
function deleteResource(resource_type,id){
    window.wxc.xcConfirm("您确定要删除吗?","confirm",{onOk:function()
    {
        updateResourceState(resource_type,2,id);
    }});
}
//更新专栏完结状态
function updatePackageFinishedState(new_state,id) {
    var allParams ={};
    var params = {};
    allParams['id'] = id;
    params['finished_state'] = new_state;
    allParams['params'] = params;
    $.post('/edit_package_finished', allParams, function (result) {
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
function updatePackageFinishedStater(new_state,id,thisObj) {
    var itemTitle = $.fixedWidth(thisObj.parent().parent().find(".item_title").text(),28);
    var resourceTitle = "提示： 专栏 "+itemTitle;
    resourceTitle = new_state==0? resourceTitle+" 已完结" : resourceTitle+ " 更新中";
    var thisInfos = new_state==0? "将专栏状态设置为 更新中？" : "将专栏状态设置为 已完结？";
    window.wxc.xcConfirm(thisInfos, 'confirm',{title:resourceTitle,onOk:function()
    {
        updatePackageFinishedState(new_state,id);
    }});
}
//更新专栏排序操作
function updatePackageWeight(new_weight,id) {
    var allParams ={};
    allParams['id'] = id;
    allParams['order_weight'] = new_weight;
    $.post('/edit_package_weight', allParams, function (result) {
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
//显示或隐藏专栏的最新显示
function h5newestHide(hide_state,id){
    var allParams ={};
    allParams['id'] = id;
    allParams['hide_state'] = hide_state;//
    $.post('/h5newest_hide', allParams, function (result) {
        hideLoading();
        var code = result.code;
        var msg = result.msg;
        if (code ==0) {
            baseUtils.show.blueTip(msg,function () {window.location.reload()},1500);
        } else {
            baseUtils.show.redTip(msg);
        }
    });
}
function h5newestHider(hide_state,id,thisObj){
    var resourceTitle = "提示： 专栏 "+thisObj.parent().parent().find(".item_title").text();
    var infos = "设置为" + thisObj.attr('title') + "？";
    window.wxc.xcConfirm(infos, 'confirm',{title:resourceTitle,onOk:function()
    {
        h5newestHide(hide_state,id);
    }});
}

//资源搜索
function searchResource(resource_type){
    //取选择字段和内容
    var resource_attr = $('#resource_attr').val();
    var search_content = $('#resource_search_content').val().trim();
    var url = '';
    if(resource_type=='audio'){
        url = "/audio_list?&resource_attr=" + encodeURI(resource_attr) + "&search_content=" + encodeURI(search_content);
    }else if(resource_type=='video'){
        url = "/video_list?&resource_attr=" + encodeURI(resource_attr) + "&search_content=" + encodeURI(search_content);
    }else if(resource_type=='article'){
        url = "/article_list?&resource_attr=" + encodeURI(resource_attr) + "&search_content=" + encodeURI(search_content);
    }
    window.location = url;
}

//回显搜索框内的值
function reBack() {

    if(search_content!=undefined){ //专栏会出现这种情况~
        var resource_search_content = document.getElementById("resource_search_content");

        if (search_content != "") {
            resource_search_content.value = search_content;
            var resource_attr_element = document.getElementById("resource_attr");
            if (resource_attr_element.length > 0) {
                for (var i = 0; i < resource_attr_element.options.length; i++) {
                    if (resource_attr_element.options[i].value == resource_attr) {
                        resource_attr_element.options[i].selected = true;
                        break;
                    }
                }
            }
        }
    }

}

//列表搜索框回车触发搜索
function reSearch() {
    //回车搜索
    $(document).keypress(function(e)
    {
        if(e.which == 13)
        {
            $('#resource_search_btn').trigger("click");
        }
    });
}

(function($){
    $.extend($,{
        fixedWidth:function(str,length,char){
            str=str.toString();
            if(!char) char="...";
            var num=length-lengthB(str);
            if(num<0)
            {
                str=substringB(str,length-lengthB(char))+char;
            }
            return str;
            function substringB(str,length)
            {
                var num=0,len=str.length,tenp="";
                if(len)
                {
                    for(var i=0;i<len;i++)
                    {
                        if(num>length) break;
                        if(str.charCodeAt(i)>255)
                        {
                            num+=2;
                            tenp+=str.charAt(i);
                        }
                        else
                        {
                            num++;
                            tenp+=str.charAt(i);
                        }
                    }
                    return tenp;
                }
                else
                {
                    return null;
                }
            }
            function lengthB(str)
            {
                var num=0,len=str.length;
                if(len)
                {
                    for(var i=0;i<len;i++)
                    {
                        if(str.charCodeAt(i)>255)
                        {
                            num+=2;
                        }
                        else
                        {
                            num++;
                        }
                    }
                    return num;
                }
                else
                {
                    return 0;
                }
            }
        }
    });
})(jQuery);

// var str="希望您在这里能够有一定的收获";
// document.write($.fixedWidth(str,20));