<template>
    <div class="que-part">
        <top_title :part_title="question_part.part_title" :show_all="false"></top_title>
        <div v-for="item in question_part.list">
            <question_item :item="item"></question_item>
        </div>
    </div>
</template>

<script>
    import top_title from '../top_title.vue';
    import question_item from './question_item.vue';
    export default {
        props:['question_part'],
        data: function () {
            return {

            }
        },
        methods:{

        },
        components:{
            top_title,
            question_item
        }
    }
</script>

<style>
    .que-part{
        padding: 0 15px;
        margin-bottom: 5px;
        background-color: #fff;
        overflow: hidden;
        box-sizing: border-box;
        border: solid 1px #eeeeee;
    }
</style>