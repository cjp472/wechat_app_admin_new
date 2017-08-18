/**
 * Created by jserk on 2017/6/2.
 */
$(function () {
    createPlan.init();
});

var createPlan = function () {
    var createPlan = {};

    var coupon_id;
    var coupon_name;
    var leftNum;
    var package_id;
    var membersNum;
    var is_member;
    var member_name;
    var form_conpon_id;
    var form_package_id;
    var coupon_num;

    function searchCoupon(name) {
        $.ajax({
            type: "GET",
            url: '/coupon/getCoupon',
            dataType: "html",
            data: {
                name: name
            },
            success: function (data) {
                $('.couponsContent').html(data);
            },
            error: function (xhr, status, err) {
                console.log(xhr);
                console.error(err);
                console.error(status);
                baseUtils.show.redTip('网络错误，请稍后再试！');
                //hideLoading();
                // $(".loadingS").fadeOut(300);
            }
        });
    }
    
    function searchMembers(name) {
        $.ajax({
            type: "GET",
            url: '/coupon/getResource',
            dataType: "html",
            data: {
                name: name
            },
            success: function (data) {

                $('.membersContent').html(data);
            },
            error: function (xhr, status, err) {
                console.log(xhr);
                console.error(err);
                console.error(status);
                baseUtils.show.redTip('网络错误，请稍后再试！');
                //hideLoading();
                // $(".loadingS").fadeOut(300);
            }
        });
    }
    createPlan.init = function () {
        searchCoupon("");
        searchMembers("");

        $("body").on('click', '.couponsTr', function () {
            coupon_id = $(this).data('id');
            leftNum = $(this).data('leftnum');
            coupon_name=$(this).data('name');
            coupon_num = $(this).children('#couponNum').html();

            $(this).find("input").prop("checked", true);
        })

        $("body").on('click', '.membersTr', function () {
            package_id = $(this).data('id');
            membersNum=$(this).data('membersnum');
            is_member=$(this).data('is_member');
            member_name=$(this).data('name');

            $(this).find("input").prop("checked", true);
        })

        $("body").on('click', 'tr', function () {

        });

        $("body").on('click', '.closeAddBox,.boxClose', function () {
           $(".darkScreen2,.darkScreen1").fadeOut(200);
        });

        $("body").on('click', '.selectMembers', function () {
            $(".darkScreen1").fadeIn(200);
        });

        $("body").on('click', '.selectCoupons', function () {
            $(".darkScreen2").fadeIn(200);
        });

        $("body").on('click', '#searchMembers', function () {
            var memberName=$("#memberName").val();
            //searchMembers(memberName);
        });
        $("body").on('click', '#searchCoupons', function () {
            var couponsName=$("#couponsName").val();
            //searchMembers(couponsName);
        });

        $("body").on('click', '#addCoupons', function () {
            $(".darkScreen2,.darkScreen1").fadeOut(200);
            $(".selectCoupons").hide();
            $(".conponInfo").show();
            $(".couponName").text(coupon_name);
            $(".couponNum").text(coupon_num+'张');
            $('#f_cou_name').attr("data-id",coupon_id);
        });
        $('.membersContent').on('click','.membersTr',function(){
            package_id = $(this).children('label').attr('for');
            member_num = $(this).children('.memberNum').html();
        });
        //确定添加目标人群
        $("body").on('click', '#addMembers', function () {
            form_conpon_id=package_id;
            $(".darkScreen,.darkScreen1").fadeOut(200);

            if(form_conpon_id!=""){

                $(".selectMembers").hide();
                $(".memberInfo").show();
                if(is_member==1){
                    $(".memberType").text()
                }
                $(".memberName").text(member_name);
                $(".memberNum").text(member_num);
                $('#f_mem_name').attr("data-id",package_id);

            }
        });
        $('.memberInfoEdit').on('click',function(){
           $('.darkScreen1').fadeIn(200);

        });
        $('.memberInfoDel').on('click',function(){
            $('.memberInfo').hide();
            $('.selectMembers').show();
        });
        $('.couponEdit').on('click',function(){
            $('.darkScreen2').fadeIn(200);

        });
        $('.couponDel').on('click',function(){
            $('.conponInfo').hide();
            $('.selectCoupons').show();
        });

        $(".pageConfirm").on("click",function(){

            var params = {
                title:$('#f_name').val(),
                cou_id:coupon_id,
                cou_name:coupon_name,
                resource_id:package_id,
                resource_name:member_name,
                count:membersNum
            }

            $.ajax({
                type: "GET",
                url: '/coupon/addPlan',
                dataType: "json",
                data: {
                    "params": params
                },
                success: function (data) {
                    $('.submit-tip').html(data.msg);
                    window.location.href='/coupon/index';
                },
                error: function (xhr, status, err) {
                    console.log(xhr);
                    console.error(err);
                    console.error(status);
                    baseUtils.show.redTip('网络错误，请稍后再试！');
                    //hideLoading();
                    // $(".loadingS").fadeOut(300);
                }
            });
            /*
            $('body').on('click','.list-search',function(){
                alert(1);
                var str = trim($(this).sibling('input').val());
                if(str==''){
                    baseUtils.show.redTip('请输入搜索内容');
                    return false;
                }
                $('.'+$('this').data('class')).find('.list-title').each(function(index,el){
                    console.log($(this).html());
                    if(!$(this).html().indexOf(str)){
                        $(this).parents('td').parents('tr').hide();
                    }else{
                        $(this).parents('td').parents('tr').show();
                    }
                });
            });
            */
        });
    };

    return createPlan;
}();