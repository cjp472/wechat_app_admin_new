$(function () {
    var business = new Business();
});


function Business() {
    this.data = {};
    this.spec = $('#specialTicket');
    this.valueAdded = false;
    this.inputError = false;
    this.init();
}

Business.prototype = {
    init: function() {
        this.addEvent();
    },
    addEvent: function () {
        var self = this;
        $('#makeInvoice').click(this.submit.bind(this));

        $('#radioBox').on('change', ':radio', function(e) {
            var $ele = $(e.target),
                val = $ele.val();
            if(val == 2) {
                self.spec.show();
                self.valueAdded = true;
            } else {
                self.spec.hide();
                self.valueAdded = false;
            }
        });
    },
    submit: function() {
        var Data = this.getVal(),
            self = this;

        if(self.inputError) {
            return false;
        }
        $.ajax('/create_invoice', {
            type: 'POST',
            data: self.data,
            dataType: 'json'
        }).done(function(json) {
            if(json.code == 1) {
                utils.show.blueTip('提交成功，等待审核', function() {
                    window.location.href = '/invoice_info';
                });
            } else {
                utils.show.redTip(json.msg);
            }
        }).fail(function(error) {
            console.error(error);
            utils.show.redTip('服务器开小差了，请稍后再试！');
        });
    },
    getVal: function() {
        var self = this,
            amount = 0;

        $('#formInfo .inputDefault').each(function(index, item) {
             var $ele = $(item),
             name = $ele.attr('name'),
             valueAdded = $ele.data('value'),
             text = $ele.data('text'),
             value = $.trim( $ele.val() );

             if(!self.valueAdded) {
                 if (valueAdded!='add' && (text != '' && value == '')) {
                     utils.show.redTip(text + '不能为空！');
                     self.inputError = true;
                     return false;
                 } else if (valueAdded!='add') {
                     self.inputError = false;
                     self.data[name] = value;
                 }
             } else {
                 if (text != '' && value == '') {
                     utils.show.redTip(text + '不能为空！');
                     self.inputError = true;
                     return false;
                 } else {
                     self.inputError = false;
                     self.data[name] = value;
                 }
             }

        });

        $(':checkbox').each(function(index, item){
            var $ele = $(item),
                checked = $ele.prop('checked'),
                value = $ele.val();
            if(value == 1 && checked) {
                amount += 4800;
            } else if (value == 2 && checked) {
                amount += 100;
            } else if (value == 3 && checked) {
                var money = Number( $.trim( $('#flowMoney').val() ) );
                if(!money) {
                    utils.show.redTip('充值流量费用输入必须为数字！');
                    self.inputError = true;
                    return false;
                }
                amount += money;
            }
        });
        if(!amount) {
            utils.show.redTip('开票金额不能为0！');
            self.inputError = true;
            return false;
        }
        self.data['invoice_amount'] = amount;
        self.data['invoice_type'] = $('input:radio[name=type]:checked').val();

        return self.data;
    }
}


window.utils = (function () {
    var baseUtils = {
        show: {
            colors: {
                red: '#f06d6b',
                blue: '#2a75ed'
            },

            defaultTip: function (content, callback, time, color) {
                var $Tip = $('#TopPrompt'),
                    $TipText = $Tip.find('.topPromptContent');
                time = time || 2000;
                $TipText.text(content);
                $Tip.css('backgroundColor', this.colors[color]).fadeIn(300);

                setTimeout(function () {
                    $Tip.fadeOut(300);
                    if (callback) callback.call();
                }, time);
            },
            //  蓝色顶部提示条
            blueTip: function (content, callback, time) {
                this.defaultTip(content, callback, time, 'blue');
            },
            //  红色顶部提示条
            redTip: function (content, callback, time) {
                this.defaultTip(content, callback, time, 'red');
            }

        },

        /**
         * 输入的最大价格 100万， 单位：元
         */
        maxInputPrice: 1000000,
    }
    //窗口Id值
    baseUtils.showWindow = function (domId) {
        var htmlString = '<div class="modal-backdrop in"></div>';
        $("body").append(htmlString);
        privateFun.preventScroll();
        $("#" + domId).show();

    };
    baseUtils.hideWindow = function (domId) {
        $("body").find(".modal-backdrop.in").remove();
        privateFun.enableScroll();
        $("#" + domId).hide();

    };

    //显示局部 loading 动画
    baseUtils.showLoading = function (loadingId) {
        $("#_localLoading" + loadingId).show();
    };
    baseUtils.hideLoading = function (loadingId) {
        $("#_localLoading" + loadingId).hide();
    };


    var privateFun = {
        preventScroll: function () {
            // 禁止
            document.body.style.overflow = 'hidden';
            window.addEventListener('touchmove', this._preventDefault);
        },
        enableScroll: function () {
            // 恢复
            document.body.style.overflow = 'auto';
            window.removeEventListener('touchmove', this._preventDefault);
        },
        _preventDefault: function(e) {
            e.preventDefault();
        }

    }

    return baseUtils;

})();