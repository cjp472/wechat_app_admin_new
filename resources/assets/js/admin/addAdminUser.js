/**
 * Created by Stuphin on 2016/12/16.
 */
var params={};
$(document).ready(function()
{
    init();
    //新增/修改，根据方式修改提交的url
    var subUrl = '/admin/doChild/add';
    //如果是编辑页面，设为true，不进行用户名重复验证
    var setFlag = false;
    if($('#userInfo').val()!=''){
        subUrl = '/admin/doChild/edit/'+$('#userInfo').val();
        setFlag = true;
    }

    //检测账号合法性
    $("#username").on('keyup',function()
    {
        if(setFlag)return false;
        var username=$(this).val();
        if(username.length<6){
            $('.inputUsername').removeClass('active');
            $('.inputUsername').eq(0).addClass('active');
            $(".nameIcon").eq(0).addClass("hide");

            return false;
        }else {
            $('.inputUsername').eq(0).removeClass('active');
        }
        if(!checkPass(username)){
            $('.inputUsername').removeClass('active');
            $('.inputUsername').eq(1).addClass('active');
            $(".nameIcon").addClass("hide");
            return false;
        }else {
            $('.inputUsername').eq(1).removeClass('active');
        }
        $.get("/checkUsername/"+username,function(data)
        {
            if(data.code==0)
            {
                $(".nameIcon").eq(0).removeClass("hide");
                $("#finish").attr("disabled",false);
                $('.inputUsername').eq(2).removeClass('active');
            }
            else
            {
                $(".nameIcon").eq(0).addClass("hide");
                $('.inputUsername').eq(2).addClass('active');
                $("#finish").attr("disabled",true);
            }
        });
    });

    //检测密码合法性.

    $('#password').on('keyup',function(){
        if (!checkPass($(this).val())||$(this).val().length<6) {
            $('.passUsername').eq(0).addClass('active');
            $(".passIcon").addClass("hide");

        }else {
            $('.passUsername').eq(0).removeClass('active');
            $(".passIcon").removeClass("hide");

        }
    });
    $('#repassword').on('keyup',function(){
        if($(this).val()!=$('#password').val()){
            $('.passUsername').eq(1).addClass('active');
            $(".repassIcon").addClass("hide");
        }else {
            $('.passUsername').eq(1).removeClass('active');
            $(".repassIcon").removeClass("hide");
        }
    });

    //验证密码字符  字母或数字
    function checkPass(theObj) {
        var reg = /^[A-Za-z0-9]+$/;
        if (reg.test(theObj)) {
            return true;
        }
        return false;
    }

    //新增账户
    var subFlag = false;
    $("#finish").click(function()
    {

        //必填项判断
        var mustFlag = false;
        $('.mustSet').each(function(index,el){
            if($(this).val()==''||$(this).val()==null){
                baseUtils.show.redTip($(this).data('tips'));
                mustFlag=true;
                return false;
            }
        });
        if(mustFlag){
            return false;
        }
        if($('#username').val()<6){
            baseUtils.show.redTip('登录账户名称必须大于6位');
        }

        var password = $('#password').val();
        if(password<6){
            baseUtils.show.redTip('密码长度不够');
            return false;
        }
        if(!checkPass(password)){
            baseUtils.show.redTip('密码应为6-16位字符,可包含数字、字母(区分大小写)')
        }
        if($('#password').val()!=$('#repassword').val()){
            baseUtils.show.redTip('两次输入的密码不一致');
            return false;
        }

        //没选择权限
        if($("input:checkbox:checked").length==0)
        {
            baseUtils.show.redTip("亲,请至少选择一项权限哦");
            return false;
        }

        //提交数据
        var param = {};

        param["role_name"]=$('#role_name').val();
        param["phone"]=$('#phone').val();
        param["username"]=$('#username').val();
        param["password"]=$('#password').val();
        param["repassword"]=$('#repassword').val();


        var priId = {};
        $('input:checkbox').each(function(){
           priId[$(this).attr('id')] = $(this).is(':checked')?1:0;
        });
        param['privilege'] = priId;

        //防止重复点击
        if(subFlag){
            return false;
        }
        subFlag = true;
        $.post(subUrl,{"params":param},function(data)
        {
            if(data.code==0)
            {
                baseUtils.show.blueTip("设置成功",function()
                {
                    window.location.href="/admin/child";
                });
            }
            else
            {
                baseUtils.show.redTip("设置失败");
                subFlag=false;
            }
        });
    });
    //顶级权限开关
    $('.setTopPri').on('click',function(){
        var els = $(this).parent('.checkArea').siblings('.checkArea').children('input');
        if($(this).siblings('input').is(':checked')){
            els.prop('checked',false);
        }else {
            els.prop('checked',true);
        }
    });
    //二级权限设置时判断是不是会影响顶级
    $('.setChildPri').on('click',function(){
        var els = $(this).parent('.setChildPriBox').siblings('.checkArea');
        var childEls = $(this).parent('.setChildPriBox').siblings('.setChildPriBox');
        if(!$(this).siblings('input').is(':checked')){
            els.eq(0).children('input').prop('checked',true);
        }
        if(!childEls.children('input').is(':checked')&&$(this).siblings('input').is(':checked')){
            els.eq(0).children('input').prop('checked',false);
        }
    });
});


//初始化
function init()
{
    //  Content区域小标题
    //showContentTitle();
    // if (is_huidu == 1) {
    //     appendContentHeader("账户一览", "/accountview", false);
    // }
    // appendContentHeader("账号管理", "/accountmanage", true);
    // appendContentHeader("接入配置", "/h5setting", false);
    // appendContentHeader("小程序设置", "/smallprogramsetting", false);
}

