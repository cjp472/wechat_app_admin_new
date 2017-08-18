<template>
    <div class="question-sidebar">
        问答
    </div>
</template>

<script>
    import EventBus from '../../../libs/eventbus';
    export default {
        props:['question_part'],
        data:function () {
            return {
                index_data: this.question_part
            };
        },
        created:function () {

        },
        methods:{

        }
    }
</script>

<style>

</style>