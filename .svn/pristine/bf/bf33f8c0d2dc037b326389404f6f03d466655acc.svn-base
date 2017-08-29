// instance: data的作用域
var module = function(instance) {
    var that = this;
    return {
        // 修改表单disabled状态
        chageIuputDisable: function(selector, bol) {
            if (bol) {
                $(selector).attr("disabled", false);
            } else {
                $(selector).attr("disabled", true);
            }
        },
        // 关闭授权失败弹框事件
        closeAlert: function(type) {
            this.chageIuputDisable('#agentSubmitBtn', true);
            $('#agentSubmitBtn').text('下一步');
        },
        // 显示小程序复用服务号认证资质授权弹框
        toProjAuthorize: function(op) {
            var self = this;
            var tipsHtml = '<div class="alert-content">请您授权我们复用服务号认证资质，帮你注册小程序</div>';
            var options = {
                btn: 2, //按钮
                oktext: '前往授权',
                onClose: this.closeAlert.bind(this),
                onOk: function () {
                    // 更新授权状态
                    self.updateState(0, function(){
                        // 显示小程序复用服务号认证资质是否授权成功弹框
                        $("#bindProjModal").modal("show");
                    });
                },
            };
            if (op && typeof(op) == 'object') {
                options = $.extend(true, options, op);
            }
            $.alert(tipsHtml, 'info', options);
        },
        // 未授权微信开放平台的弹框
        unAuthorizeAlert: function(op) {
            var self = this;
            var tipsHtml = '<div class="alert-content">请您授权微信开放平台账号管理权</br>'
                         + '特别提醒：请您务必使用原有服务号，否则将无法帮你代理注册小程序</div>';
            var options = {
                title: '申请失败',
                btn: 2, //按钮
                oktext: '重新授权',
                onClose: this.closeAlert.bind(this),
                onOk: function() {
                    self.updateState(1, function() {
                        // 显示微信开发平台是否授权成功弹框
                        $("#bindModal").modal("show");
                    });
                },
            };
            if (op && typeof(op) == 'object') {
                options = $.extend(true, options, op);
            }
            $.alert(tipsHtml, 'info', options);
        },
        // 已绑定微信开放平台的弹框(手动授权)
        authorizeByself: function(op) {
            var tipsHtml = '<div class="alert-content">您的公众号已绑定其它开放平台，我们无法帮您代理注册小程序，但您可以选择手动配置</div>';
            var options = {
                title: '申请失败',
                btn: 3, //按钮
                oktext: '前往手动配置',
                canceltext: '查看教程',
                onClose: this.closeAlert.bind(this),
                onCancel: function() {
                    window.open('/helpCenter/problem?document_id=d_591580aedb6ed_ZBMk578Q'); // 前往教程(小程序)
                },
                onOk: function () {
                    location.replace('/mini/guide');
                },
            };
            if (op && typeof(op) == 'object') {
                options = $.extend(true, options, op);
            }
            $.alert(tipsHtml, 'info', options);
        },
        // 其它错误弹框
        otherAuthorizeWrong: function(op) {
            // this.toProjAuthorize();return;
            var self = this;
            var tipsHtml = '<div class="alert-content">未知错误，请稍候重试</div>';
            var options = {
                title: '申请失败',
                btn: 2, //按钮
                oktext: '确认',
                onClose: this.closeAlert.bind(this),
                onOk: function () {
                    self.chageIuputDisable('#agentSubmitBtn', true);
                    $('#agentSubmitBtn').text('提交');
                },
            };
            if (op && typeof(op) == 'object') {
                options = $.extend(true, options, op);
            }
            $.alert(tipsHtml, 'info', options);
        },
        // 提交小程序代注册
        submitRegister: function() {
            var self = this;
            if (instance.data.submitState != 0) {
                return;
            }
            instance.data.submitState = 1; // 修改为正在提交
            this.chageIuputDisable('#agentSubmitBtn', false);
            $('#agentSubmitBtn').text('正在提交...');
            var cmd = '/mini/proxy_create_platform';
            $.get(cmd, {}, function(data) {
                $('#agentRegisterBox').hide(); // 隐藏协议弹框
                if (data.code == 0) {
                    $('#agreementBox').hide();
                    // $('#registerBox').show();
                    self.toProjAuthorize(); // 小程序复用服务号认证资质授权
                    instance.data.submitState = 0;
                    return;
                } else if (data.code == 4001) {
                    // 未授权微信开放平台
                    self.unAuthorizeAlert();
                } else if (data.code == 4002) {
                    // 已绑定微信开放平台
                    self.authorizeByself();
                } else {
                    // 其它错误
                    self.otherAuthorizeWrong();
                }
                instance.data.submitState = 0;
            });
        },
        // 跳微信开发平台授权页
        jumpOpenUrl: function() {
            var app_id = $('#app_id').val();
            window.open(miniauthUrl + app_id);
        },
        // 跳小程序资质授权页
        jumpProjUrl: function() {
            var app_id = $('#app_id').val();
            window.open('https://app.inside.xiaoe-tech.com/platform/proxy_register_page/' + app_id);
        },
        // 更新授权状态
        updateState: function(type, cb) {
            var app_id = $('#app_id').val();
            var cmd = '';
            var targetUrl = '';
            if (type == 0) { // 小程序授权
                var cmd = '/mini/authorityForRegister';
                targetUrl = 'https://app.inside.xiaoe-tech.com/platform/proxy_register_page/' + app_id;
            } else if (type == 1) { // 开放平台授权
                var cmd = '/mini/authorityForPublic';
                targetUrl = miniauthUrl + app_id;
            }
            var newTab = window.open('about:blank'); // 防止浏览器拦截
            $.post(cmd,{"app_id":app_id},function(json) {
               if(json.code == 0) {
                   newTab.location.href = targetUrl;
                   cb();
               } else {
                   newTab.close();
                   baseUtils.show.redTip("系统繁忙");
               }
           }, 'json');
       },
    };
}

$(function(){
    var that = this;
    this.data = {
        submitState: 0, //0-未提交, 1-提交中, 2-已提交
    };
    var ctx = module(this);


    // 选择代注册
    $('#agentRegister').click(function() {
        // 显示协议弹框
        $('#agentRegisterBox').show();
    });
    $('#agentMb').click(function() {
         // 隐藏协议弹框
        $('#agentRegisterBox').hide();
    });


    // 同意协议
    $("#agreementCheckbox").click(function () {
        if (that.data.submitState == 1) { // 正在提交
            return;
        }
        if($('#agreementCheckbox').is(':checked')) {
            ctx.chageIuputDisable('#agentSubmitBtn', true);
        } else {
            ctx.chageIuputDisable('#agentSubmitBtn', false);
        }
    });


    // 提交小程序代申请
    $('#agentSubmitBtn').click(function() {
        ctx.submitRegister();
    });

    // 微信开放平台授权成功
    $('#bindSuccess').click(function() {
        var cmd = '/mini/proxy_create_platform';
        $.get(cmd, {}, function(data) {
            $('#bindModal').modal('hide'); // 先隐藏本弹框
            if (data.code == 0) {
                ctx.toProjAuthorize(); // 前往小程序资质授权
                return;
            } else if (data.code == 4001) {
                // 未授权微信开放平台
                ctx.unAuthorizeAlert({
                    title: '授权失败'
                });
            } else if (data.code == 4002) {
                // 已绑定其他微信开放平台
                self.authorizeByself();
            } else {
                // 其它错误
                self.otherAuthorizeWrong();
            }
        });
    });
    //微信开放平台授权失败
	$("#bindFail").click(function() {
        // 跳微信开发平台授权页
        ctx.jumpOpenUrl();
	    $("#bindModal").modal("show");
	});

    // 小程序认证资质授权成功
    $('#bindProjSuccess').click(function() {
        var _app_id = $('#app_id').val();
        // 先判断是否授权成功
        $.get("/mini/checkAuth", {"app_id": _app_id}, function(json){
	        if(json.code==0){
	            $("#bindProjModal").modal("hide");
	            window.location.href = '/mini/info?change=1';
	        } else {
                ctx.toProjAuthorize({
                    title: '授权失败'
                });
	        }
	    });

    });
    // 小程序认证资质授权失败
    $('#bindProjFail').click(function() {
        ctx.jumpProjUrl(); // 跳资质授权页
    });


});
