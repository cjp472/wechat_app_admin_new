/**
 * Created by Roam on 2017/6/23.
 */

$(function(){
   shareUse.init();
})

var shareUse = function(){
    var shareUse = {};
    var shareInfo = {
        num:0,
        id:'',
        share_knock:0,
        listen_count:0
    };


    shareUse.init = function(){
        //显示弹窗
        var nowEl;
        $('.searchAllBtn').on('click',function(){
            var searchText = $(this).siblings('input').val();
            url = '/invite/shareUseList/'+$(this).data('url')+'?search='+searchText;
            window.location.href=url;
        });
        $('.shareNumSet').on('click',function(){
            var el = $(this).parent('td');
            nowEl = el;
            shareInfo.id = $(this).data('good-id');
            shareInfo.listen_count = $(this).data('listen-count');
            var numText = el.siblings('.share_use_num').html();
            //判断开启与关闭设置数据
            if(numText === "未参与"){
                $('#shareClose').prop('checked',true);
                $('.set_share_good_num').prop('disabled',true);
                shareInfo.num=0;
                shareInfo.share_knock=0;
            }else{
                $('#shareOpen').prop('checked',true);
                $('.set_share_good_num').prop('disabled',false);
                shareInfo.num = numText;
                shareInfo.share_knock=1;
            }

            $('.set_share_good_title').html(el.siblings('.td_resource').children('p').html());
            $('.set_share_good_num').val(shareInfo.listen_count);
            $('.set_share_num').addClass('active');
        });
        //开启分享
        $('#shareOpen').on('click',function(){
            shareInfo.share_knock=1;
            $('.set_share_good_num').prop('disabled',false);
        });
        //关闭分享
        $('#shareClose').on('click',function(){
            shareInfo.share_knock=0;
            $('.set_share_good_num').prop('disabled',true);
        });

        $('.cancel_sale_btn').on('click',function(){
            $('.set_share_num').removeClass('active');
        });

        //提交数据
        $('.confirm_sale_btn').on('click',function(){
            shareInfo.listen_count = $('.set_share_good_num').val();
            if(shareInfo.share_knock===1&&shareInfo.listen_count<1){
                baseUtils.show.redTip('领取上限不能小于1');
                return false;
            }
            $('#base_loading').show();
            $.ajax('/invite/setShareNum',{
                type:'get',
                data:shareInfo,
                success:function(data){
                    if(data.code===0){
                        $('.set_share_num').removeClass('active');
                        if(shareInfo.share_knock==0){
                            nowEl.siblings('.share_use_num').html("未参与");
                        }else {
                            nowEl.siblings('.share_use_num').html(shareInfo.listen_count);
                        }
                        nowEl.children('a').data('listen-count',shareInfo.listen_count);
                        baseUtils.show.blueTip('设置成功');
                    }else{
                        $('.set_share_num').removeClass('active');
                        baseUtils.show.redTip(data.msg);
                    }

                },
                error:function (data) {
                   baseUtils.show.redTip('网络错误');
                },
                complete:function(){
                    $('#base_loading').hide();
                }
            });

        });
    };
    return shareUse;
}();