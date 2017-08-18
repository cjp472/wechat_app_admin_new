<template>
    <div class="notice-prompt" v-show="is_show" :style="{ top: topSize + 'px' }">
        <div class="notice-prompt-word">
            {{notice_text}}<a :href="skip_link">{{button_text}}</a>
        </div>
        <div class="notice-close-wrapper" @click="closeNotice">
            <img src="/images/icon_pop_up_close.svg"/>
        </div>
    </div>
</template>

<script>
    import EventBus from '../libs/eventbus.js';
    export default {
        data: function () {
            return {
                topSize: 50,
                notice_text:'',
                skip_link:'',
                button_text:'',
                is_show: false
            };
        },
        created: function () {
            let that = this;
            EventBus.$on('is_show_notice_prompt',function (obj) {
                that.is_show = true;
                that.notice_text = obj.text;
                that.skip_link = obj.url;
                that.button_text = obj.button;
            });
            window.addEventListener('scroll', this.onScroll);
        },
        methods: {
            closeNotice: function () {
                this.is_show = false;
            },
            onScroll: function () {
                let scrollTop = document.body.scrollTop;
                if(scrollTop <= 50){
                    this.topSize = 50-scrollTop;
                }else{
                    this.topSize = 0;
                }
            }
        }
    }
</script>

<style>
    .notice-prompt {
        background-color: #629eff;
        width: 100%;
        height: 40px;
        position: fixed;
        top: 50px;
        left: 160px;
        right: 0;
    }
    .notice-prompt-word {
        width: 80%;
        height: 40px;
        line-height: 40px;
        float: left;
        margin-left: 20px;
        font-size: 14px;
        color: #fff;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .notice-prompt-word a {
        color: #629eff;
        background: #fff;
        display: inline-block;
        height: 28px;
        line-height: 28px;
        border-radius: 2px;
        padding: 0 5px;
        margin: 0 10px;
        font-size: 12px;
        text-decoration: none!important;
        cursor: pointer!important;
    }
    .notice-close-wrapper {
        background-color: #629eff;
        height: 12px;
        width: 12px;
        float: right;
        margin: 14px 180px 14px 0;
        cursor: pointer;
    }
    .notice-close-wrapper img {
        width: 100%;
        height: 100%;
        float: left;
        vertical-align: middle;
        border: 0;
    }
</style>