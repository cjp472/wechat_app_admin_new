// creat by Jervis
var newFormFlag=false;//方法锁变量
$(function(){
    init();
    newCoupon();
});

function newCoupon(){
    var tabStatus = 0;//tab选项卡状态初始状态0
    var limitStatus= 0;//使用条件限制 0-无条件使用 1-使用限制条件
    var hasChoice=[];//TODO:考虑到后台传值，需要更加完善功能
    var allFormMsg;//表单信息对象
    var couponName;//优惠券名称
    var couponPrice;//优惠券面额
    var popWay;//发行方式
    var goodsRange=[];//商品范围数组id--tab
    var isTen=0;//使用条件
    var timeStart;//开始时间
    var timeEnd;//结束时间
    var popNum;//发行量
    var peopleLim;//每人限领
    var isToDetail;//是否投放至商品详情页
    var type;//页面回传数 0-商品 1-店铺
    var page_type;//当前页面是编辑页还是创建页  -0创建  1编辑
    var cmp=$.trim($('[name="count"]').val());//变量为编辑页发行量旧值
    popWay="0";
    page_type=$('.formContent').data("page_type");
    //初始化已有数组对象
    var editId=GetQueryString("id");
    console.log(editId);
    if(GetQueryString("type")==0 || GetQueryString("type")==1 ){//获取type值，编辑页与创建页
        type=GetQueryString("type");
        console.log('shang')
    }else{
        type=$('.plusArea').attr('data-plus-type');
        console.log('xia')
    }
    //编辑页初始化hasChoice值 
    $('.addedList .addedItem').each(function(){
        hasChoice.push($(this).data('id'));
    })
    console.log(type);
    //日期选择器初始化    
    aliveTimeConfig(".dateSetInput");
    +function(){
        //当前创建的优惠券是： 0-商品 1-店铺
        if(type==0){
            $('.isGoods').attr('style','');
        }else{
            $('.isGoods').attr('style','display:none');
        }
        //表格头部提示--不再提示
        $('.remindTop a').on('click',function(){
            var url='/coupon/close/' + $(this).siblings('span').attr('data-place');
            $('.remindTop').remove();

            $.ajax({
                type: "GET",
                url: url,
                data:{
                },
                success: function(data) {
                    if(data.code==0){
                        // console.log(data);
                        baseUtils.show.blueTip(data.msg)
                    }else{
                        // console.log(data);
                        baseUtils.show.blueTip(data.msg)
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
            })

        });
        
        // 优惠券使用范围限定
        $('[for="useCdn_1"]').on('click',function(){
            $('[name="re_price"]').removeAttr("disabled").removeAttr("readonly");
        });
        $('[for="useCdn_2"]').on('click',function(){
            $('[name="re_price"]').attr("disabled",true).attr("readonly",true).val('');
            setCondition();
        });
        // 添加商品范围
        $(".plusArea").on('click',function(){
            // $(this).unbind("click"); 
            console.log('plusArea test')
            $('.initList').html();
            $('.addBox [type="checkBox"]').removeAttr('checked');
            $('#addModal').fadeIn(200);

            // $(this).bind("click");
        });
        //关闭弹窗
        $('.closePop').on('click',function(){
            $('#addModal').fadeOut(200);
        });
        // tab切换
        $('div.tab a').on('click',function(){
            $(this).addClass("clicked").siblings().removeClass("clicked");
            
            tabStatus=$(this).attr("data-type")
            console.log(tabStatus);

            switch(tabStatus){
                case "0":$('#tab_0').show().siblings("#tab_1,#tab_2").hide()
                break;
                case "1":$('#tab_1').show().siblings("#tab_0,#tab_2").hide()
                break;
                case "2":$('#tab_2').show().siblings("#tab_1,#tab_0").hide()
                break;
            }

        });
        
        //商品范围搜索按键部分
        $('#forTab').on('click',function(){
            var kw = $('#kwContent').val();
            // console.log(kw);
            // console.log(tabStatus)
            // if(kw.length==0){
            //     baseUtils.show.redTip("请输入搜索内容！");
            //     return false;
            // }
            $.ajax({
                type: "GET",
                url: '/coupon/add_products',
                dataType: "html",
                data:{
                    state:tabStatus,
                    kw:kw
                },
                success: function(data) {
                    $('.initList').html(data);
                    switch(tabStatus){
                        case "0":$('#tab_0').show().siblings("#tab_1,#tab_2").hide()
                        break;
                        case "1":$('#tab_1').show().siblings("#tab_0,#tab_2").hide()
                        break;
                        case "2":$('#tab_2').show().siblings("#tab_1,#tab_0").hide()
                        break;
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
            })
        });
        // 部分事件（使用条件与商家发放）
        $('input.func').on('click',function(){
            console.log('teste');
            $(this).addClass("funcClick").siblings().removeClass("funcClick");
            console.log($(this).val());
            if($(this).val()=="用户领取"){
                $('.aboutGet').show();
                popWay="0";//用户领取
            }else{
                $('.aboutGet').hide();
                $('.aboutGet #putOn').removeAttr('checked')
                popWay="1";//商家发放
            }
            
        });
        //添加按钮部分
        $('#add').on('click',function(){
            var arrSingle=[];
            var arrPro=[];
            var arrNum=[];
            var stateAll=[];
            //获取弹窗列表中每个选项的特殊值
            $('#tab_0 input[type="checkbox"]:checked').each(function(){
                arrSingle.push($(this).val());
                var state=[];
                state.push($(this).attr('data-name'));
                state.push($(this).attr('data-img'));
                state.push('单品');
                state.push($(this).val());
                state.push($(this).attr('data-style'));
                stateAll.push(state);
            });
            $('#tab_1 input[type="checkbox"]:checked').each(function(){
                arrPro.push($(this).val());
                var state=[];
                state.push($(this).attr('data-name'));
                state.push($(this).attr('data-img'));
                state.push('专栏');
                state.push($(this).val());
                state.push($(this).attr('data-style'));
                stateAll.push(state);
            })
            $('#tab_2 input[type="checkbox"]:checked').each(function(){
                arrNum.push($(this).val());
                var state=[];
                state.push($(this).attr('data-name'));
                state.push($(this).attr('data-img'));
                state.push('会员');
                state.push($(this).val());
                state.push($(this).attr('data-style'));
                stateAll.push(state);
            });
            // console.log(stateAll);
            // console.log(arrNum);
            // console.log(arrPro);
            console.log(arrSingle);
            var newArr= arrSingle.concat(arrNum,arrPro);
            
            console.log(hasChoice);
            if(newArr==""){
                baseUtils.show.redTip("请选择要添加的商品。");
                return false;
            }
            if(unique(hasChoice,newArr)==1){
                baseUtils.show.redTip("请勿重复选择添加的商品。");
                return false;
            }else{
                for(i=0;i<stateAll.length;i++){
                    $('.addedList').append(
                        '<div data-tab=' +stateAll[i][4]+ ' class="addedItem" data-id="' + stateAll[i][3]
                        + '"><span>'+stateAll[i][2] + '</span><img src="' + stateAll[i][1]
                        + '" alt="'+stateAll[i][0] + '"><span class="spanCon">' + stateAll[i][0]
                        + '</span><button type="button" class="close closeItem">×</button></div>'
                    )
                }
                hasChoice=hasChoice.concat(newArr);
                $('#addModal').fadeOut(200);
            }

        });
        //删除已选商品
        $(".addedList").on("click",".closeItem",function () {
            $(this).prev().prev().click();
            $(this).parent().remove();
            var deleteItem=$(this).parent().attr('data-id');
            console.log(deleteItem);
            hasChoice.remove(deleteItem);
            console.log(hasChoice);

        });

        //保存提交部分(submit)
        $('#submitList').on('click',function(){
            // console.log("ceshi"+newFormFlag);
            if(newFormFlag){
                return false;               
            }
            newFormFlag=true;
            goodsRange=[];//置空数组，防止提交失败时生成重复id
            $('.addedItem').each(function(){
                goodsRange.push($(this).attr('data-id')+'--'+$(this).attr('data-tab'));
                // console.log(state);
            });
            console.log(goodsRange);  

            //获取值
            couponName=$.trim($('[name="title"]').val());
            couponPrice=Math.round(($.trim($('[name="price"]').val()))*100);
            //获取使用条件
            if($('#useCdn_1').prop('checked')){
                // isTen=parseFloat($('[name="re_price"]').val())*100;
                limitStatus = 1;
                isTen=Math.round(parseFloat($('[name="re_price"]').val())*100) || "empty";
                console.log("使用条件"+isTen);
            }else if($('#useCdn_2').prop('checked')){
                limitStatus = 0;
                isTen= 0 ;
            };
            // isTen=Math.round(parseFloat($('[name="re_price"]').val())*100) || 0;
            console.log("isTen",isTen)
            timeStart=$.trim($('[name="valid_at"]').val());
            timeEnd=$.trim($('[name="invalid_at"]').val());
            popNum=$.trim($('[name="count"]').val());
            peopleLim=$('[name="receive_rule"]').val();
            if($('[name="extension"]').prop('checked')){
                isToDetail=1;
            }else{
                isToDetail=0;
            }

            //传出数据对象
            allFormMsg={
                title:couponName,
                price:couponPrice,
                receive_rule:peopleLim,
                valid_at:timeStart,
                invalid_at:timeEnd,
                require_price:isTen,
                count:popNum,
                spread_type:popWay,
                is_show:isToDetail,
                resource:goodsRange,
                type:type
            }
            if(editId){
                allFormMsg.id=editId;
            }
            // console.log(allFormMsg);

            console.log(allFormMsg.price)
            console.log(allFormMsg.require_price)
            if(page_type==0 && checkForm(allFormMsg,cmp,limitStatus)){
                submitForm("/coupon/add",allFormMsg);
            }else if(page_type==1 && checkForm(allFormMsg,cmp)){
                console.log('check is ture')
                submitForm("/coupon/editCoupon",allFormMsg);
            }
        })

    }()
    //提交时验证表单
    function checkForm(info,cmp,limit) {
        newFormFlag=false;
        if ($formCheck.emptyString(info.title)) {
            baseUtils.show.redTip("优惠券名称不能为空！");
            console.log(newFormFlag);
            return false;
        }
        if (info.title.length >= 10) {
            baseUtils.show.redTip("优惠券名称不能超过10个字！");
            console.log(newFormFlag);
            return false;
        }
        if ($formCheck.emptyString(info.price)) {
            baseUtils.show.redTip("优惠券面额不能为空！");
            return false;
        }
        if (info.price > 1000000000) {//给后台传值为分，最终判断值后会乘100
            baseUtils.show.redTip("优惠券面额不能大于10000000！");
            return false;
        }
        if(parseFloat(info.count)<parseFloat(cmp)){
            baseUtils.show.redTip("修改发行量只能增不能减！");
            return false;
        }
        if ($formCheck.emptyString(info.valid_at)&&$formCheck.emptyString(info.invalid_at)) {
            baseUtils.show.redTip("有效期起止时间不能为空！");
            return false;
        }
        if ($formCheck.emptyString(info.count)) {
            baseUtils.show.redTip("发行数量不能为空！");
            return false;
        }
        if (info.count > 1000000) {
            baseUtils.show.redTip("发行数量不能超过1,000,000张！");
            return false;
        }
        // if(info.type==0){
        //     if ($formCheck.emptyString(info.resource)) {
        //         baseUtils.show.redTip("商品范围不能为空！");
        //         return false;
        //     }
        // }
        if (!$formCheck.checkTime(info.valid_at)) {
            baseUtils.show.redTip("有效期开始时间格式错误");
            return false;
        }
        
        if (!$formCheck.checkTime(info.invalid_at)) {
            baseUtils.show.redTip("有效期结束时间格式错误");
            return false;
        }
        if(info.require_price === "empty"){
            baseUtils.show.redTip("使用条件不能为空！");
            return false;
        }
        if(!$formCheck.checkTimeOrder(info.valid_at,info.invalid_at)){
            baseUtils.show.redTip("有效期结束时间必须大于开始时间！");
            return false;
        }
        if (!$formCheck.checkNum(info.price)) {
            baseUtils.show.redTip("您的面额输入格式有误，请重新输入！");
            return false;
        }
        if (!$formCheck.checkNum(info.count)) {
            baseUtils.show.redTip("您的发行量输入格式有误，请重新输入！");
            return false;
        }
        if(info.require_price==null){
            baseUtils.show.redTip("优惠券面额不能为空！");
            return false;
        }
        if(info.price>=info.require_price && limit == 1){
            baseUtils.show.redTip("优惠券面额要小于使用条件！");
            return false;
        }
        newFormFlag=true ;
        
        
        // if(uploadChannelType== 1 && info.payment_type== 2 && info.piece_price > baseUtils.maxInputPrice * 100){
        //     baseUtils.show.redTip("价格不能大于 " + baseUtils.maxInputPrice + " 张");
        //     return false;
        // }
        return true;
    }
    //表单提交方法
    function submitForm(url,allmag){
        $.ajax(url,{
            type:'POST',
            dataType:'JSON',
            data:allmag,
            success:function(data){
                if(data.code==0){
                    baseUtils.show.blueTip(data.msg);
                    window.location.href="/coupon/index"
                }else if(data.code==-1){
                    baseUtils.show.redTip(data.msg);
                    newFormFlag=false;
                    // reloadPage();
                }
            },
            error: function(xhr,status,err){
                newFormFlag=false;
                hideLoading();
                console.error(err);
                baseUtils.show.redTip('网络错误，请稍后再试！');
            }
        })
    }
    //查重方法
    function unique(arr1,arr2){
        var item=0;
        for(var i=0;i<arr1.length;i++){
            if(arr2.indexOf(arr1[i])!=-1){
                item=1;
            }
        }
        return item;
    }
    //删除数组元素方法
    Array.prototype.remove = function(val) {
        var index = this.indexOf(val);
        if (index > -1) {
            this.splice(index, 1);
        }
    };
    // init()
    // function init(){
    //     switch(tabStatus){
    //         case "0":$('#tab_0').show().$("#tab_1,#tab_2").hidden()
    //         break;
    //         case "1":$('#tab_1').show().$("#tab_0,#tab_2").hidden()
    //         break;
    //         case "2":$('#tab_2').show().$("#tab_1,#tab_0").hidden()
    //         break;
    //     }
    // }
}
function init(){//初始化优惠券商品弹窗内容
    $.ajax({
        type: "GET",
        url: '/coupon/add_products',
        dataType: "html",
        success: function(data) {
            
            $('.initList').html(data);
        },
        error: function(xhr, status, err) {
            console.log(xhr);
            console.error(err);
            console.error(status);
            baseUtils.show.redTip('网络错误，请稍后再试！');
            //hideLoading();
            // $(".loadingS").fadeOut(300);
        }
    })
}
function clearNoNum(obj){ 
   obj.value = obj.value.replace(/[^\d.]/g,"");  //清除“数字”和“.”以外的字符  
   obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的  
   obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$","."); 
   obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3');//只能输入两个小数  
   if(obj.value.indexOf(".")< 0 && obj.value !=""){//以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额 
       obj.value= parseFloat(obj.value); 
   } 
} 
function setCondition(){
    $('.theTen').html(parseFloat($('[name="price"]').val())+0.1);
}