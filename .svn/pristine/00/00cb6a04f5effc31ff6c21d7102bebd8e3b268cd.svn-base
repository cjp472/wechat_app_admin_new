/**
 * Created by vinceyu on 2017/7/21.
 */
(function (window) {
    function send(data) {
        try {
            var url = "https://report.xiaoe-tech.com:3600/report.gif?" + data;
            if (document.URL.indexOf("inside") == -1) {
                //利用img标签跨域上报
                var _img = new Image();
                _img.src = url;
            }
        } catch (e) {
        }
    }


    //暴露上报方法
    window.e_report = function (app_id, biz_type, biz_id, machine_ip, user_id, user_agent, user_ip, biz_report_type, content, extra) {
        //没有提供的参数,填入一个占位符"-"
        app_id = app_id ? app_id : "-";
        biz_type = biz_type ? biz_type : "-";
        biz_id = biz_id ? biz_id : "-";
        machine_ip = machine_ip ? machine_ip : "-";
        user_id = user_id ? user_id : "-";
        user_agent = user_agent ? user_agent : "-";
        user_ip = user_ip ? user_ip : "-";
        biz_report_type = biz_report_type ? biz_report_type : "-";
        content = content ? content : "-";
        extra = extra ? extra : "-";
        var obj = "app_id=" + app_id + "&" +
            "biz_type=" + biz_type + "&" +
            "biz_id=" + biz_id + "&" +
            "machine_ip=" + machine_ip + "&" +
            "user_id=" + user_id + "&" +
            "user_agent=" + user_agent + "&" +
            "user_ip=" + user_ip + "&" +
            "biz_report_type=" + biz_report_type + "&" +
            "content=" + content + "&" +
            "extra=" + extra;
        send(obj);
    };
})(window);