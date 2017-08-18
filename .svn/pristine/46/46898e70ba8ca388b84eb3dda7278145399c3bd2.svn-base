$(function () {
   communityList.init();
});
var i = 0;
var iTime = null;
function  remainTime() {

    //检测是否设置成功

    $.ajax('isCommunityHaveAdmin', {
        type: 'GET',
        dataType: 'json',
        data:{'community_id':$('#community_id').val()},
        success:function (data) {
            i += 1;
            // console.log('---请求次数---：');
            // console.log(i);
            //
            // console.log('---检测返回值---:');
            // console.log(data);


            if(i >= 300){
                i = 0;
                clearTimeout(iTime);
                $(".darkScreen").fadeOut(300);//弹窗隐藏

                var text = '您长时间未扫码  请重新验证';

                $.alert(text,'info',{
                    oktext: '我知道了',
                    btn: 2,
                    onOk: function() {
                        window.location.href = "/smallCommunity/communityList";
                    },
                    onClose:function () {
                        window.location.href = "/smallCommunity/communityList";
                    }
                });

            }


            if(data.code == 0){
                clearTimeout(iTime);
                var text = '群主设置成功';
                $(".darkScreen").fadeOut(300);//弹窗隐藏
                $.alert(text,'info',{
                    oktext: '我知道了',
                    btn: 2,
                    onOk: function() {
                        window.location.href = "/smallCommunity/communityList";
                    },
                    onClose:function () {
                        window.location.href = "/smallCommunity/communityList";
                    }
                });
            }
        }
    });

    iTime = setTimeout("remainTime()",1500);
}
var communityList=function () {
    var communityList={};
    var selectNum,
        searchContent,
        ajaxList=0,
        userArr = [],
        step = 1,
        ckeckedUserID = '',
        holderChangeState,
        oldAdminName = '',
        newAdminName = '',
        QRLink = '',
        resultState = 0,
        alertText = '';




    function getUserInfo(id,step,search) {
        $.ajax('getUserInfo',{
            type:'POST',
            textType:'json',
            data:{
                community_id:id,
                step:step,
                search:search
            },

            success:function (data) {
                $(".loadingS").fadeOut(300);
                console.log(id);
                if(data.code==0){
                    userArr = data.data;
                    console.log(userArr);
                    var i = 0;
                    for (i = 0; i < userArr.length;i++){
                        text = '<div class="holderListPart" data-name="' + userArr[i].wx_nickname + '" data-userid="' + userArr[i].user_id + '"><div class="holderCheckBox"> <input class="with-gap" id="' + userArr[i].user_id + '" name="group2" type="radio" /> <label for="' + userArr[i].user_id + '"> </label> </div> <div class="holderListImg"><img src="' + userArr[i].wx_avatar + '"></div> <span class="holderListName">' + userArr[i].wx_nickname + '</span> </div>'
                        $('.holderListContent').append(text);
                    }

                } else {

                    if (resultState == 0){
                        text='<p class="searchNone">'  + alertText + ' </p>';
                        $('.holderListContent').append(text);
                        resultState = 1;
                    }

                }
            },
            error:function (xhr,status,err) {
                console.log(err);
                baseUtils.show.redTip('网络错误，请稍后再试！');
            }
        })
        
    }
    
    function getQRLink(id) {
        $.ajax('getCommunityLinkSetAdmin',{
            type:'POST',
            textType:'json',
            data:{
                community_id:id
            },
            success:function (data) {
                console.log(data);
                if(data.code==0){
                    QRLink = data.data;
                }
            },
            error:function (xhr,status,err) {
                console.log(err);
                QRLink = '服务器出错了 请稍后再试';
            }
        })
        
    }

    function changeCommunityState(id,community_state){
        $.ajax('changeCommunityState',{
            type:'POST',
            textType:'json',
            data:{
                id:id,
                community_state:community_state

            },
            success:function (data) {
                if(data.code==0){
                    if(community_state==0){
                        baseUtils.show.blueTip('已上架，当前小社群将在店内展示');
                    }else{
                        baseUtils.show.blueTip('已下架，当前小社群将不在店内展示');
                    }
                    location.reload();
                }else{
                    baseUtils.show.redTip('操作失败!');
                    console.log(data.msg);
                }
            },
            error:function (xhr,status,err) {
                console.log(err);
                baseUtils.show.redTip('网络错误，请稍后再试！');
            }
        })
    }







    communityList.init=function () {

        //  显示设置群主的提示
        var is_new = GetQueryString("is_new");
        if (is_new == 1){
            $(".newCommunityTipWrapper").fadeIn(300);
            setTimeout(function () {
                $(".newCommunityTipWrapper").fadeOut(700);
            }, 5000);
        }




        //复制到剪贴板
        (function () {
            var clipboard = new Clipboard('.copyHref');
            clipboard.on('success', function(e) {
                baseUtils.show.blueTip("复制成功！请在微信内打开哦 。");
                e.clearSelection();
            });
        })();


        $("body").on('mouseenter','.productCountBox',function () {
            $(this).next().fadeIn(200);
        });


        $("body").on('mouseleave','.productCountBox',function () {
            $(this).next().fadeOut(200);
        });

        $(".toolBox").on('click','.setHolderBtn',function () {
            resultState = 0;
            step = 1;
            var id = $(this).data('id');
            $('#community_id').val(id);
            $(".darkScreen").fadeIn(300);
            $('.holderSearchInput').val('');
            $('.holderListContent').empty();
            getUserInfo(id,1);

            holderChangeState=0;
            $('.setAdmin').show();  //显示二维码
            getQRLink(id);
            alertText = '暂无结果，请微信扫一扫设置群主';
        });

        $(".toolBox").on('click','.changeHolderBtn',function () {
            resultState = 0;
            step = 1;
            var id = $(this).data('id');
            $('#community_id').val(id);
            $(".darkScreen").fadeIn(300);
            $('.holderSearchInput').val('');
            $('.holderListContent').empty();
            getUserInfo(id,1);
            alertText = '暂无结果';

            holderChangeState=1;
            $('.setAdmin').hide();  //隐藏二维码
            oldAdminName = $(this).data('old');
        });

        $(".GroupHolderSetBox").on('click','.holderSetClose',function () {
            $(".darkScreen").fadeOut(300);
        });


        //社群搜索
        $("body").on('click','.searchAllBtn',function () {
            selectNum=$("#selector").val();
            searchContent=$.trim($(".inputSearchAll").val());
            window.location='communityList?ruler='+selectNum+"&search="+searchContent;
        });

        //  点击筛选触发搜索
        $("#selector").on("change", function () {
             $(".searchAllBtn").click();
        });

        //  回车触发搜索
        $(".inputSearchAll").on("keypress", function (e) {
            if (e.keyCode == 13) {
                $(".searchAllBtn").click();
            }
        });

        //用户搜索
        $(".GroupHolderSetBox").on('click','.holderSearchBtn',function () {
            resultState = 0;
            step = 1;
            $(".loadingS").fadeIn(300);
            $('.holderListContent').empty();
            getUserInfo($('#community_id').val(),step,$('.holderSearchInput').val());

        })

        //用户列表下拉显示下一页
        $(".holderList").scroll(function () {
            var viewH=$(this).height();//可见高度
            var contentH =$(this).get(0).scrollHeight;//内容高度
            var scrollTop =$(this).scrollTop();//滚动高度
            console.log(contentH);
            console.log(viewH);
            console.log(scrollTop);

            if(contentH - viewH - scrollTop<=35){

                if(ajaxList==0) {
                    step++;
                    getUserInfo($('#community_id').val(),step,$('.holderSearchInput').val());
                    console.log('已经滚到底部了');
                    ajaxList=1;
                }
            }else{
                ajaxList=0;
            }
        });
    //  用户列表子单元点击事件关联
        $(".holderList").on('click','.holderListPart',function () {

            $(this).find("input").prop("checked", true);
            //    获取选中用户的id名
            ckeckedUserID = $(this).data('userid');
            newAdminName = $(this).data('name');

            console.log(ckeckedUserID);
        });

        $('.boxConfirm').click(function () {
           if(holderChangeState==1){
               $.alert('当前群主为'+ oldAdminName +',确认将群主转移给' + newAdminName + '吗？转移后，前群主曾经发的动态保留在群主动态中，新群主新发的动态将显示在群主动态中' , 'info',{
                   btn:3,
                   onOk:function () {

                       $.ajax('setCommunityAdmin',{
                           type:'POST',
                           textType:'json',
                           data:{
                               community_id:$('#community_id').val(),
                               user_id:ckeckedUserID
                           },
                           success:function (data) {
                               if(data.code==0){
                                   baseUtils.show.blueTip('操作成功！');
                                   window.location.href = "/smallCommunity/communityList";
                               }
                           },
                           error:function (xhr,status,err) {
                               console.log(err);
                               baseUtils.show.redTip('网络错误，请稍后再试！');
                           }
                       });
                   }
               });
           }else {
               $.ajax('setCommunityAdmin', {
                   type: 'POST',
                   textType: 'json',
                   data: {
                       community_id: $('#community_id').val(),
                       user_id: ckeckedUserID
                   },
                   success: function (data) {
                       if (data.code == 0) {
                           baseUtils.show.blueTip('操作成功！');
                           window.location.href = "/smallCommunity/communityList";
                       }
                   },
                   error: function (xhr, status, err) {
                       console.log(err);
                       baseUtils.show.redTip('网络错误，请稍后再试！');
                   }
               });
           }
        });

    //   设置微信好友为群主
        $(".GroupHolderSetBox").on('mouseenter','.setWxFriend',function () {
            // 生成二维码

            i = 0;
            var qrcodeConfirmPayDirCode = new QRCode(document.getElementById("qrImgId"),
                {
                    text:QRLink,
                    width: 120,
                    height: 120,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.L
                });
                $(".qrCodePaper").fadeIn(100);
            remainTime();



        });

        $(".GroupHolderSetBox").on('mouseleave','.setWxFriend',function () {
            clearTimeout(iTime);
            $("#qrImgId").empty();
            $(".qrCodePaper").fadeOut(10);

        });
        


    //    上下架操作
        $(".toolBox").on('click','.communityHide',function () {
            var communityId=$(this).data('id');
            var communityState=1;
            changeCommunityState(communityId, communityState);
        });

        $(".toolBox").on('click','.communityShow',function () {
            var communityId=$(this).data('id');
            var communityState=0;
            changeCommunityState(communityId, communityState);
        })

    };

    return communityList;
}();