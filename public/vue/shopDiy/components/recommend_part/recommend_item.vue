<template>
    <div class="listWrapper">
        <div class="content-wrapper pressWrapper">
            <div class="contentMain">
                <div class="content-pic-wrapper blueClass" :class="{greenClass: isGreenActive, yellowClass: isYellowActive}">
                    <img v-if="item.srcType==1" class="srcType" src="/images/admin/shopDiy/icon-imageText@2x.png" />
                    <img v-if="item.srcType==2" class="srcType" src="/images/admin/shopDiy/icon-play-audio-default@2x.png" />
                    <img v-if="item.srcType==3" class="srcType" src="/images/admin/shopDiy/icon-video@2x.png" />
                    <img v-if="item.img_url_compressed" class="content-pic" :src="item.img_url_compressed" />
                    <img v-if="item.img_url_compressed==''" class="content-pic-default" src="/images/admin/shopDiy/icon-pic@2x.png" />
                </div>
                <div class="content-info">
                    <span class="content-title">{{ item.title }}</span>
                    <div class="content-status">
                        <div class="content-count" v-if="item.view_count">
                            <div class="content-num">
                                {{ item.view_count }}
                            </div>
                        </div>
                        <div class="content-count" v-if="item.comment_count">
                            <div class="content-num">
                                {{ item.comment_count }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props: ['item','index'],
        data:function () {
            return {
                isGreenActive: false,
                isYellowActive: false
            }
        },
        created: function () {
            if(this.index === 1){
                this.isGreenActive = true;
            }
            if(this.index === 2){
                this.isYellowActive = true;
            }
        },
        methods: {

        }
    }
</script>

<style>
    .content-wrapper {
        display: block;
    }
    .contentMain {
        width: 100%;
        box-sizing: border-box;
        padding: 15px 0;
        border-bottom: 1px solid #f3f3f3;
        overflow: hidden;
        display: table;
        outline: 0;
    }
    .content-pic-wrapper {
        overflow: hidden;
        position: relative;
        width: 90px;
        height: 68px;
        line-height: 68px;
        text-align: center;
        margin-right: 10px;
    }
    .greenClass {
        background-color: #a2efdb !important;
    }

    .yellowClass {
        background-color: #fff0a9 !important;
    }
    .blueClass {
        background-color: #a9ddff;
    }
    .srcType {
        position: absolute;
        right: 5px;
        bottom: 5px;
        width: 24px;
        height: 24px;
        z-index: 9;
    }
    .content-pic {
        width: 100%;
        height: 100%;
        border-radius: 4px;
    }
    .content-pic-default {
        width: 24px;
        height: 20px;
        border-radius: 4px;
    }
    .content-info {
        overflow: hidden;
        box-sizing: border-box;
        display: table-cell;
        vertical-align: middle;
    }
    .content-title {
        display: block;
        max-height: 36px;
        line-height: 18px;
        margin-bottom: 35px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-all;
        max-width: 240px;
        text-overflow: ellipsis;
        font-size: 14px;
        color: #353535;
    }
    .content-status {
        width: 240px;
        height: 15px;
        font-size: 12px;
        color: #888888;
    }
    .content-num {
        float: left;
        height: 15px;
        line-height: 15px;
    }

    .content-count {
        float: left;
        margin-right: 10px;
        height: 100%;
        overflow: hidden;
    }
</style>