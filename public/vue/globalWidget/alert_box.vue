<template>
    <div class="alert-box">
        <div class="main-box">
            <a class="close-box" @click="closeAlert"></a>
            <div class="content-icon">
                <img src="/images/alert/blue_info_prompt.svg" width="60px">
                <p class="title">提示</p>
            </div>
            <div class="box-text"><p>{{tip_text}}</p></div>
            <div class="button-area">
                <div class="btnGroup"><a class="sgBtn cancel">取消</a><a class="sgBtn ok">确定</a></div>
            </div>
        </div>
    </div>
</template>

<script>
    import EventBus from '../libs/eventbus.js';
    export default {
        data: function(){
            return {
                item: {},
                showAlert: false,
                toastShow: false
            }
        },
        created: function(){
            let that = this;
            EventBus.$on('show_vue_alert', function (item) {
                that.item = item;
                that.showAlert = true;
                if (item.type === 'toast') {
                    that.toastShow = true;
                    setTimeout(() => {
                        that.toastShow = false;
                        that.closeAlert();
                    }, that.item.time || 1500);
                }
            });
        },
        methods: {
            confirmClick: function(){
                if (this.item.confirm && typeof(this.item.confirm) == 'function'){
                    this.item.confirm();
                }
                this.showAlert = false;
            },
            cancelClick: function(){
                if (this.item.cancel && typeof(this.item.cancel) == 'function'){
                    this.item.cancel();
                }
                this.showAlert = false;
            },
            closeAlert: function(){
                this.showAlert = false;
            }
        }
    }
</script>

<style>
    .alert-box {
        opacity: 1;
    }
    .alert-box .main-box {
        padding: 20px;
        border-radius: 10px;
        background-color: #fff;
        position: absolute;
        left: 50%;
        top: 50%;
        z-index: 10000;
        width: 540px;
        margin-left: -270px;
        font-weight: 700;
        color: #353535;
    }
    .alert-box .close-box {
        display: block;
        cursor: pointer;
        width: 18px;
        height: 18px;
        position: absolute;
        top: 20px;
        right: 30px;
        background: url(../../images/icon_Pop-ups_close.svg) center no-repeat;
    }
    .content-icon .title {
        margin: 20px auto 10px;
        line-height: 30px;
        font-size: 20px;
        font-weight: 400;
        color: #353535;
    }
    .main-box .box-text {
        text-align: center;
        margin: 10px auto;
        width: 500px;
        overflow: hidden;
        font: normal normal normal 16px/150px 'Microsoft YaHei';
    }
</style>