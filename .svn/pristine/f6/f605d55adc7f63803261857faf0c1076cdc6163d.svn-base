<template>
    <div class="activity-part">
        <top_title :part_title="activity_part.part_title" :show_all="activity_part.show_all"></top_title>
        <div v-for="item in activity_part.list">
            <activity_item :item="item"></activity_item>
        </div>
    </div>
</template>

<script>
    import top_title from '../top_title.vue';
    import activity_item from './activity_item.vue';
    export default {
        props:['activity_part'],
        data: function () {
            return {

            }
        },
        methods:{

        },
        components:{
            top_title,
            activity_item
        }
    }
</script>

<style>
    .activity-part {
        padding: 0 15px;
        margin-bottom: 5px;
        background-color: #fff;
        overflow: hidden;
        box-sizing: border-box;
        border: solid 1px #eeeeee;
    }
</style>