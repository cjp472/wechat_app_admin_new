<template>
    <div class="community-part">
        <top_title :part_title="community_part.part_title" :show_all="community_part.show_all"></top_title>
        <div style="clear: both"></div>
        <div  v-for="(item,index) in community_part.list">
            <community_item :item="item" :index="index"></community_item>
        </div>
    </div>
</template>

<script>
    import community_item from './community_item.vue';
    import top_title from '../top_title.vue';
    export default{
        props:['community_part'],
        data: function () {
            return {

            }
        },
        components: {
            community_item,
            top_title
        },
    }
</script>

<style>
    .community-part {
        padding: 0 15px;
        margin-bottom: 5px;
        background-color: #fff;
        overflow: hidden;
        box-sizing: border-box;
        border: solid 1px #eeeeee;
    }
</style>