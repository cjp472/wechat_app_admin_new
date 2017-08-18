/**
 * Created by Jervis_cen on 2017/6/14.
 */

$(function(){
    shortLink();
});

function shortLink(){
    var inputUrl;//输入的url
    //初始化剪贴板
    var clipboard = new Clipboard('.copyHref');
    clipboard.on('success', function(e) {
        baseUtils.show.blueTip("复制成功！请在微信内打开哦。");
        e.clearSelection();
    });
    $(".compress").on('click',function(){//压缩一下
        // console.log('compress test')

        inputUrl=$(".shortInput").val();
        $.ajax({
            type:'POST',
            url:'/assist/st',
            data:{url:inputUrl},
            success:function(data){
                if(data.code==0){
                    // console.log(data.data.url)
                    createDom(1,data.data.url);
                    QScreate(data.data.url);
                    downLoadEvent();
                    baseUtils.show.blueTip("压缩成功");
                }else if(data.code==-1){
                    // console.log(data);
                    createDom(2);
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                console.log(xhr);
                console.error(err);
                console.error(status);
                baseUtils.show.redTip('网络错误，请稍后再试！');
            }
        })
    });


    function createDom(num,shortUrl){
        var rootDiv=$("<div>");
        if(num==1){
            rootDiv.html("<span class='success'>压缩成功</span><div class='shortContent'>生成的短链接：<span class='shortUrl'>"+shortUrl
                +"</span><a data-clipboard-text='"+shortUrl+"'class='clip copyHref'>复制链接</a></div><div class='shortContent'>链接二维码："
                +"<div class='frame'><div id='miniCode'></div></div><a class='clip downPic' download='qsCode.jpeg'>下载二维码</a></div>");
        }else{
            rootDiv.html("<span class='error'>压缩失败：您输入的原链接不是小鹅通链接</span>")
        }
        $('.displayVessel').html(rootDiv);
    }
    //生成二维码方法
    function QScreate(url){
        var qrcode = new QRCode(document.getElementById("miniCode"), {
            text: url,
            width: 100,
            height: 100,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.L
        });
    }

    //单击下载事件
    function downLoadEvent(){
        setTimeout(function(){
            var Src=$("#miniCode img").prop('src');
            // console.log(Src);
            $(".downPic").prop('href',Src);
        })

    }
};