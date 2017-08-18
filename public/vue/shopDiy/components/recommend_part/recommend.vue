<template>
    <div class="recommend-part">
        <top_title :part_title="recommend_part.part_title" :show_all="false"></top_title>
        <div v-for="(item,index) in recommend_part.list">
            <recommend_item :item="item" :index="index"></recommend_item>
        </div>
    </div>
</template>

<script>
    import top_title from '../top_title.vue';
    import recommend_item from './recommend_item.vue';
    export default {
        props:['recommend_part'],
        data: function () {
            return {

            }
        },
        methods:{

        },
        components:{
            top_title,
            recommend_item
        }
    }
</script>

<style>
    .recommend-part {
        padding: 0 15px;
        margin-bottom: 5px;
        background-color: #fff;
        overflow: hidden;
        box-sizing: border-box;
        border: solid 1px #eeeeee;
    }
</style>