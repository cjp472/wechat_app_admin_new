<template>
    <div class="listWrapper">
        <div class="content-wrapper pressWrapper">
            <div class="contentMain">
                <div class="content-pic-wrapper blueClass">
                    <!--内容类型-->
                    <span class="activity_text c_green_normal">活动</span>
                    <!--内容类型-->
                    <img v-if="item.img_url_compressed" class="content-pic" :src="item.img_url_compressed" />
                    <img v-if="item.img_url_compressed==''" class="content-pic-default" src="/images/admin/shopDiy/icon-pic@2x.png" />
                </div>
                <div class="content-info">
                    <span class="content-title t2 c2">{{ item.title }}</span>
                    <div class="content-status">
                        <!--<span>活动时间：</span>-->
                        <span>{{ item.start_at }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props: ['item'],
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
    .blueClass {
        background-color: #a9ddff;
    }
    .activity_text {
        position: absolute;
        right: 0;
        top: 0;
        font-size: 10px;
        height: 14px;
        line-height: 14px;
        z-index: 9;
        color: #fff;
        padding: 0 3px;
        border-top-right-radius: 4px;
    }
    .c_green_normal {
        background-color: rgba(9, 187, 7, 1);
    }
    .content-pic-default {
        width: 24px;
        height: 20px;
        border-radius: 4px;
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
</style>