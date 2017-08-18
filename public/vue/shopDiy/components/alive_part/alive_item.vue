<template>
    <div class="alive_item">
        <div class='live-content-wrapper pressWrapper pressGreyBg'>
            <div class="contentMain">
                <div class="live_img_wrapper" :class="{greenClass: (index)%2==1, blueClass: (index)%2==0}">
                    <img v-if="item.alive_img_url" :src="item.alive_img_url"
                         class="live_img"/>
                    <img v-else-if="(index)%2==0" src="/images/admin/shopDiy/icon-pic@2x.png"
                         class="live_img_default"/>
                    <img v-else-if="(index)%2==1" src="/images/admin/shopDiy/icon-pic@2x.png"
                         class="live_img_default"/>
                    <span class="live_status aliveNewStatus aliveNewPrepare">
                        {{item.start_tag}}
                    </span>
                </div>
                <div class="bottom_wrapper">
                    <div class="alive_title" :class="{text_indent:flag}">{{item.title}}</div>
                    <span v-if="!item.available && item.piece_price!=0" class="live-content-price">{{item.piece_price}}</span>
                    <div style="clear: both"></div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['item','index'],
        data:function () {
            return {
                flag:false
            }
        },
        created:function () {
            let pattern = /^【/;
            if(pattern.test(this.item.title)){
                this.flag = true;
            }
        }
    }
</script>

<style scoped>
    .alive_item {
        position: relative;
    }
    .live-content-wrapper {
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

    .live_img {
        width: 100%;
        height: 100%;
    }

    .live_img_default {
        width: 24px;
        height: 20px;
    }

    .blueClass {
        background-color: #a9ddff;
    }

    .greenClass {
        background-color: #a2efdb;
    }

    .live_img_wrapper {
        position: relative;
        width: 100%;
        height: 110px;
        line-height: 110px;
        text-align: center;
    }

    .bottom_wrapper {
        height: 24px;
        line-height: 24px;
    }

    .alive_title {
        float: left;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        word-wrap: break-word;
        max-width: 290px;
        white-space: nowrap;
        font-size: 12px;
        color: #243042;
    }

    .text_indent {
        text-indent: -20px;
    }

    .live_status {
        /*margin-left: 0.2rem;*/
        /*float: left;*/
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .live-content-price {
        margin-left: 10px;
        padding-top: 1px;
        float: right;
        font-size: 12px;
        color: #ff821f;
    }

    .aliveNewStatus {
        text-align: center;
        height: 15px;
        line-height: 15px;
        box-sizing: border-box;
        padding: 0 5px;
        float: left;
        overflow: hidden;
        color: #FFFFFF;
        font-size: 10px;
    }

    .aliveNewPrepare {
        border: 1px solid #ffffff;
        background-color: #353535;
        border-radius: 2px;
        opacity: 0.6;
    }
</style>