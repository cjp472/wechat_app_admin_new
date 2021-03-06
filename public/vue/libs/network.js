/**
 * Created by vince on 17/2/17.
 */

import n_zepto from 'n-zepto';

//1、cmd路由，不要带斜杠，直接写业务路由
//2、数据传进来一个js对象{}
//3、回调参数（serverData(object)）

exports.request = function (cmd, bizdata, callback) {

    if(cmd[0] === "/"){
        cmd = cmd.slice(1);
    }

    n_zepto.ajax({
        type: 'POST',
        url: "/" + cmd,
        dataType: "json",
        data:  bizdata,
        success: function (serverData) {
            callback(serverData);
        },
        error: function () {
            callback(null);
        }
    });
};