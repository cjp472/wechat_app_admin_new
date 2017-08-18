//对价格输入格式做限制
function clearNoNum(value, currentDom) {
    //清除"数字"和"."以外的字符
    value = value.replace(/[^\d.]/g, "");

    //验证第一个字符是数字而不是
    value = value.replace(/^\./g, "");

    //只保留第一个. 清除多余的
    value = value.replace(/\.{2,}/g, ".");
    value = value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");

    value = value.replace(/^0[\d]+/g, "0");

    //只能输入两个小数
    value = value.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');

    $(currentDom).val(value);
}

function onlyNum(value, currentDom) {
    //清除"数字"以外的字符
    value = value.replace(/[^\d]/g, "");

    $(currentDom).val(value);


}

// 表单验证函数
var $formCheck = (function () {
    var formCheck = {};

    // 检查是否空字符串
    formCheck.emptyString = function (str) {
        str = $.trim(str);
        if(!str || str.length === 0){
            return true;
        }
        return false;
    };

    // 检查是否是大于0的整数数字
    formCheck.checkNum = function (num) {
        num = parseInt(num);

        if(isNaN(num) || num<=0){
            return false;
        }
        return true;
    };

    // 检查是否是否为正整数 或 0，< 是则返回true >
    formCheck.isNumPositiveAndInteger = function (num) {
        if(isNaN(num) || num < 0) {
            return false;
        }
        if (! /^[1-9][\d]*|0$/.test(num)) {
            return false;
        }
        return true;
    };

    // 检查时间格式是否正确 2017-03-03 06:00:00
    formCheck.checkTime = function (time) {
        var timeRe = /^\d{4}-(?:0\d|1[0-2])-(?:[0-2]\d|3[01])( (?:[01]\d|2[0-3])\:[0-5]\d\:[0-5]\d)?$/;
        if(timeRe.test(time)){
            return true
        }
        return false;
    };

    // 检查时间先后验证
    formCheck.checkTimeOrder = function (startTime,endTime) {
        if(this.checkTime(startTime) && this.checkTime(endTime)){
            startTime = new Date(startTime.replace(/-/g, "/")).getTime();
            endTime = new Date(endTime.replace(/-/g, "/")).getTime();
            if(endTime>=startTime){
                return true;
            }
        }
        return false;
    };

    // 检查密码是否格式
    formCheck.checkAccount=function (account) {
        //判断密码长度是否符合
        if(account.length<6&&account.length>0||account.length>18){
            return false;
        }
        var re = /^[0-9a-zA-Z]*$/g;  //是否包含特殊字符
        if (!re.test(account)) {
            //存在特殊字符
            return false;
        }
            return true;
    }
    
    // 检查密码是否格式
    formCheck.checkPassword=function (password) {
        //判断密码长度是否符合
        console.log(password.length);
        if(password.length<6&&password.length>0||password.length>16){
            return false;
        }
       var re = /^[0-9a-zA-Z]*$/g;  //只能是数字或字母，不包括下划线的正则表达式
        if (!re.test(password)) {
            //存在特殊字符
            return false;
        }
            return true;
    };

    formCheck.checkPhone=function (phoneNum){
        var re = /^1[34578]\d{9}$/g;//手机号验证
        if (!re.test(phoneNum)) {
            //手机号正确
            return false;
        }
        return true;
    };

    return formCheck;

})();

