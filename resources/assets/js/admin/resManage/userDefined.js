/**
 * Created by Jervis on 2017/7/12.
 */

$(function(){
   userDefined.init();
   userDefined.Event();
});

var userDefined={
    product_id:""
};
//初始化当前页面中弹框的列表项
userDefined.init = function(){
    userDefined.product_id = $('#product_id').data('pid');
    $.ajax({
        type: "GET",
        url: '/user_defined_list?id='+ userDefined.product_id,
        dataType: "html",
        success: function(data) {
            $('.initList').html(data);
            console.log("data is already")
        },
        error: function(xhr, status, err) {
            console.log(xhr);
            console.error(err);
            console.error(status);
            baseUtils.show.redTip('网络错误，请稍后再试！');
            //hideLoading();
            // $(".loadingS").fadeOut(300);
        }
    });

    return userDefined;
};

userDefined.Event = function(){

    //触发选择弹窗
    $('#addContent').on('click',function(){
        $("#addModal").fadeIn();
    });

    //关闭弹窗
    $('.closePop').on('click',function(){
        $('#addModal').fadeOut(200);
    });

    //弹窗搜索事件
    $('#forTab').on('click',function(){
        var kw = $('#kwContent').val();
        console.log('kw :'+kw);
        $.ajax({
            type: "GET",
            url: '/user_defined_list',
            dataType: "html",
            data:{
                content:kw
            },
            success: function(data) {
                $('.initList').html(data);
            },
            error: function(xhr, status, err) {
                console.log(xhr);
                console.error(err);
                console.error(status);
                baseUtils.show.redTip('网络错误，请稍后再试！');
            }
        })
    })

    $('#add').on('click',function(){
        var option = [];
        // var hasSelect = [];
        $('[type="checkBox"]:checked').each(function(){//拼接二维数组传值
           var arr = [];
           // hasSelect.push($(this).val());
           arr.push($(this).val(),userDefined.product_id,$(this).attr('data-type'));
           option.push(arr);
        });
        console.log(option);
       // 查重
       //  if(unique()){
       //      baseUtils.show.redTip('已添加的单品不可重复添加');
       //  }else{
            $.ajax({
                type: "GET",
                url: '/user_defined_add',
                data:{option:option},
                success: function(data) {

                    if(data.code==0){
                        baseUtils.show.blueTip("添加成功！");
                        reloadPage();
                    }else{
                        baseUtils.show.redTip("添加失败！")
                    }
                },
                error: function(xhr, status, err) {
                    console.log(xhr);
                    console.error(err);
                    console.error(status);
                    baseUtils.show.redTip('网络错误，请稍后再试！');
                    //hideLoading();
                    // $(".loadingS").fadeOut(300);
                }
            });
        // }


    });
    //删除产品选项+删除前弹窗确认
    $(".hasAddItem").on("click",".closeItem",function () {
        // $(this).prev().prev().click();
        $that=$(this);
        var pid = $that.parent().data('pro-id');
        console.log(pid);
        $.alert("移除该内容后，移除后该会员用户将无法查看此内容，您确认要移除么？","info",{
            title:"提示",
            icon:"blue",
            btn:3,
            onOk:function(){
                $.ajax({
                    type: "GET",
                    url: '/user_defined_del?id='+pid,
                    dataType: "html",
                    success: function(data) {
                        console.log(data);
                        $that.parent().remove();
                        baseUtils.show.blueTip('移除成功');
                        reloadPage();

                    },
                error: function(xhr, status, err) {
                        console.log(xhr);
                        console.error(err);
                        console.error(status);
                        baseUtils.show.redTip('网络错误，请稍后再试！');
                    }
                });
            }
        });


    });

    // function unique(arr1,arr2){
    //     var item=0;
    //     for(var i=0;i<arr1.length;i++){
    //         if(arr2.indexOf(arr1[i])!=-1){
    //             item=1;
    //         }
    //     }
    //     return item;
    // }

    return userDefined;
};