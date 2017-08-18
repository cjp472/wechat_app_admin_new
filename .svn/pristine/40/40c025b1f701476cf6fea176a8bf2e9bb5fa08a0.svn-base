<template>
    <div class="group-wrapper">
        <div class="group">
            <div class="group-pic-wrapper" :class="{greenClass:index==1}">
                <img v-if="item.img_url" class="group-img" :src="item.img_url">
                <img v-else-if="item.img_url==''" class="group-img-default" src="/images/admin/shopDiy/icon-pic@2x.png">
            </div>
            <div class="group-detail">
                <span class="group-title">{{ item.title }}</span>
                <div class="group-info">
                    <span>{{ item.member_count }}人加入</span>
                    <span>|</span>
                    <span>{{ item.feeds_count }}条动态</span>
                </div>
                <div v-show="item.joinStatus" class="has-add">已加入</div>
                <div v-show="!item.joinStatus" class="add">加入</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['item','index'],
        data: function(){
            return {

            }
        },
        methods: {

        }
    }
</script>

<style scoped>
    .group-wrapper {
        display: block;
        background-color: #fff;
    }
    .group {
        padding: 15px 0;
        overflow: hidden;
        border-bottom: 1px solid #f0f0f0;
    }
    .group-pic-wrapper {
        overflow: hidden;
        display: inline-block;
        width: 55px;
        height: 55px;
        line-height: 55px;
        text-align: center;
        background-color: #a9ddff;
    }
    .greenClass {
        background-color: #a2efdb !important;
    }
    .group-img {
        background-size: 55px 55px;
        width: 55px;
        height: 55px;
        float: left;
        border-radius: 4px;
    }
    .group-img-default {
        width: 24px;
        height: 20px;
    }
    .group-detail {
        float: right;
        position: relative;
        margin: 0 0 5px 8px;
        width: 278px;
    }
    .add, .has-add{
        position: absolute;
        top: 5px;
        right: 0;
        width: 55px;
        height: 27px;
        line-height: 27px;
        text-align: center;
        border-radius: 4px;
        font-size: 12px;
    }
    .add {
        background-color: #17cd46;
        color: #fff;
    }
    .has-add {
        display: inline-block;
    }
    .group-title {
        display: block;
        vertical-align: top;
        margin-bottom: 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-all;
        width: 220px;
        line-height: 20px;
        font-size: 14px;
    }
    .group-info {
        height: 17px;
        font-size: 12px;
        text-align: justify;
        color: #888888;
    }
</style>