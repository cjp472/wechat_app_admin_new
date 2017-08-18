<template>
    <div class='que-product'>
        <div class='content-wrapper pressWrapper'>
            <div class="contentMain">
                <div class="content-pic-wrapper">
                    <img v-if="item.img_url_compressed" class='content-pic' :src="item.img_url_compressed"/>
                    <img v-else-if="item.img_url_compressed==''" class="content-icon" src="/images/admin/shopDiy/icon-pic@2x.png"/>
                </div>

                <div class='content-info'>
                    <div class="content-info-cnt">
                        <div class='columnist-title' :class="{text_indent:flag}">
                            {{item.title}}
                        </div>
                        <div class='columnist-desc'>{{item.desc}}</div>
                        <div class="que-info-txt">{{item.total_num}}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default{
        props:['item'],
        data: function () {
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
    .content-wrapper {
        display: block;
    }
    .contentMain {
        width: 100%;
        box-sizing: border-box;
        padding: 15px 0;
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
        background-color: #a9ddff;
    }
    .content-pic-wrapper .content-icon{
        width: 24px;
        height: 20px;
    }
    .content-pic {
        float: left;
        width: 90px;
        height: 68px;
        border-radius: 4px;
    }
    .content-info-cnt{
        position: relative;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }
    .content-info-cnt *{
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }
    .content-info {
        overflow: hidden;
        box-sizing: border-box;
        display: table-cell;
        vertical-align: middle;
    }
    .columnist-title {
        display: block;
        max-height: 36px;
        line-height: 18px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-all;
        max-width: 250px;
        text-overflow: ellipsis;
        font-size: 14px;
        color: #353535;
    }
    .text_indent {
        text-indent: -20px;
    }
    .columnist-desc {
        height: 20px;
        line-height: 20px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        width: 250px;
        margin-bottom: 10px;
        float: right;
        font-size: 12px;
        color: #888888;
    }
    .que-info-txt{
        padding-right: 35px;
        font-size: 12px;
        color: #888888;
    }
</style>