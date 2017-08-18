/**
 * Created by Frank on 2017/3/21.
 */


$(function () {
    var business = new Business();
    business.init();

    //  显示小黄条   <小鹅通内容列表全面升级为知识商品，获取更多高级功能使用教程请点击【知识商品教程】>
    if (GetQueryString("prompt") == 1) {
        $(".red_prompt_word").html("小鹅通内容列表全面升级为知识商品，点击查看更多高级功能<a href='/help/system_update' target='_blank'>查看教程</a>");
        $(".red_prompt").show();
    }

});

function Business() {//定义一些公共属性
    this.toolBoxBtn = true;
    this.toDetail = true;
    this.searchObj = {
        state: GetQueryString('state') || 0,
        search_content: GetQueryString('search_content') || '',
        // is_distribute:GetQueryString('is_distribute')|| -1
    }
}

Business.prototype = {
    init: function () { //页面的初始化操作，绑定事件
        if(sessionStorage.getItem('key')==1){//上下移动时间成功刷新后弹窗初始化
            setTimeout(function(){
                baseUtils.show.blueTip('操作成功')
            },300);
            sessionStorage.setItem('key',0);
        }

        var self = this;
        $('#packageList') //跳转详情页
        .on('click', '.listItem', function() {
            if(self.toDetail){
                self.toolBoxBtn = false,
                self.toDetail = false;
                var href = '/package_detail_page?id=' + $(this).data('id');
                contentDetail(href);
            }
        });

        $('#packageList')  //调用工具函数
        .on('click', '.listItem .toolBox li' ,function(e) {
            e.stopPropagation();
            var $ele = $(e.target),
                id = $ele.parentsUntil('#packageList','.listItem').data('id')
                type = $ele.data('type');
            type && self.toolBoxBtn && self.toDetail && (
                self.tool(type, id),
                self.toDetail = false
            );
        });

        $('#searchBtn')
        .on('click', function() { //搜索
            // var is_distribute = $('#selector_distribute').val(),

            var state = $('#selector').val(),
                search_content = $.trim( $('#searchVal').val() );
            if(!!search_content){
                window.location.href = '/package_list_page?state='+state+'&search_content='+search_content;
            } else {
                window.location.href = '/package_list_page?state='+state;
            }
        });

        //初始化搜索条件
        $('#selector').find('option').eq(++this.searchObj.state).attr('selected',true);
        // $('#selector_distribute').find('option').eq(++this.searchObj.is_distribute).attr('selected',true);
        $('#searchVal').on('keypress', function(e) {
            if(e.keyCode == "13") {
                $('#searchBtn').click();
            }
        }).val(this.searchObj.search_content);

        //  监听筛选框选择事件
        $(".selector").on('change', function () {
            $('#searchBtn').click();
        });

    },
    tool: function(type,id) {
        switch(type) {
            case 'edit':
                contentDetail('/package_detail_page?id=' + id);

                break;
            case 'toup':
                this.movePackage(id, true);
                break;
            case 'todown':
                this.movePackage(id, false);
                break;
            case 'soldout':
                this.isShow(id, false);
                break;
            case 'putaway':
                this.isShow(id, true);
                break;
            default:
                console.error('参数错误');
                break;
        }
    },
    movePackage: function(id, changeType) {
        /*
            id: 要移动的专栏id
            changeType： true-上移， false-下移
        */
        var self = this;
        showLoading();
        $.ajax('/change_package_weight', {
            type: 'POST',
            dataType: 'json',
            data: {
                package_id: id,
                //0上移， 1下移
                order_type: changeType ? 0 : 1
            },
            success: function(data) {
                //console.log(data);
                hideLoading();
                if(data.code == 0) {
                    sessionStorage.setItem('key',1);
                    console.log(sessionStorage.getItem('key'));
                    reloadPage();

                } else {
                    self.toolBoxBtn = true;
                    self.toDetail = true;
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.toolBoxBtn = true;
                self.toDetail = true;
                baseUtils.show.redTip('操作失败，请稍后再试');

            }
        });
    },
    isShow: function(id, changeType) {
        /*
            id: 要上下架的专栏id
            changeType： true-上架， false-下架
        */
        var self = this;
        showLoading();
        $.ajax('/change_goods_state',{
            type: 'POST',
            dataType: 'json',
            data: {
                /*
                1-goods_id;
                2-goods_type(0-专栏,1-图文,2-音频,3-视频,4-直播);
                3-operate_type(0-上架,1-下架)
                */
                goods_id: id,
                goods_type: 0,
                operate_type: changeType ? 0 : 1,
            },
            success: function(data) {
                //console.log(data);
                hideLoading();
                if(data.code == 0) {
                    baseUtils.show.blueTip(data.msg);
                    setTimeout(function() {
                        reloadPage();
                    },700);
                } else {
                    self.toolBoxBtn = true;
                    self.toDetail = true;
                    baseUtils.show.redTip(data.msg);
                }
            },
            error: function(xhr, status, err) {
                console.error(err);
                hideLoading();
                self.toolBoxBtn = true;
                self.toDetail = true;
                baseUtils.show.redTip('操作失败，请稍后再试');
            }
        });
    }
}
