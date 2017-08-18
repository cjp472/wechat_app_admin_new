<template>
    <div class="category_part">
        <div class="category_tab_inner" v-for="item in category_part.list">
            <div class="category_tab">
                <div class="category_tab_icon_box">
                    <img :src="item.icon_url"/>
                </div>
                <div class="category_tab_hint">{{item.category_name}}</div>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
</template>

<script>
    export default {
        props:['category_part'],
        data:function () {
            return {

            }
        },
        methods: {

        }
    }
</script>

<style>
    .category_part {
        height: 80px;
        width: 100%;
        background-color: #fff;
        margin-bottom: 5px;
        font-size: 0;
        box-sizing: border-box;
        border: solid 1px #eeeeee;
    }
    .category_tab_inner {
        float: left;
        width: 25%;
        height: 100%;
        overflow: hidden;
        padding: 0;
        font-size: 0;
        box-sizing: border-box;
    }
    .category_tab {
        margin-top: 18px;
    }
    .category_tab_icon_box {
        text-align: center;
    }
    .category_tab_icon_box>img{
        width: 24px;
        height: 24px;
    }
    .category_tab_hint {
        width: 100%;
        height: 17px;
        margin-top: 5px;
        font-size: 12px;
        color: #888888;
        text-align: center;
    }
</style>