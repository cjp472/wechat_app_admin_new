<template>
    <div class="choose-resource-item" @click="selectClick">
        <div class="resource-item-left">
            <input type="checkbox" :checked="select_click">
            <img :src="item.img_url_compressed"/>
            <span class="title">{{item.title}}</span>
        </div>
        <div class="resource-item-right">
            <span class="show-time">{{item.show_time}}</span>
        </div>
    </div>
</template>

<script>
    import EventBus from '../../../libs/eventbus';
    export default {
        props: ['item','index'],
        data: function(){
            return {
                select_click: false
            };
        },
        methods: {
            selectClick: function(){
                this.select_click = !this.select_click;

                console.log(3453)
                EventBus.$emit('choose_add_item',{select_click:this.select_click,index:this.index});
            }
        },
        watch: {
            item: function () {
                this.select_click = false;
            }
        }
    }
</script>

<style scoped>
    input[type = 'checkbox']{
        width: 24px;
        height: 20px;
        vertical-align: middle
    }

    .choose-resource-item {
        margin: 0 20px;
        padding: 0 10px;
        height: 70px;
        line-height: 70px;
        border-bottom: solid 1px #f2f2f2;
        font-size: 14px;
        color: #666666;
        cursor: pointer;
    }
    .resource-item-left {
        float: left;
    }
    .resource-item-left .select-icon {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 2px;
        border: solid 1px #c1c4ca;
        margin-right: 20px;
    }
    .resource-item-left .selectBgClass {
        background-color: #2a75ed;
    }
    .resource-item-left img {
        width: 40px;
        height: 30px;
        border-radius: 2px;
        margin-right: 10px;
    }
    .resource-item-right {
        float: right;
    }


</style>