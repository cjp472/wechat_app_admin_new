<template>
    <div class="activity-sidebar">
        活动
    </div>
</template>

<script>
    import EventBus from '../../../libs/eventbus';
    export default {
        props:['activity_part'],
        data:function () {
            return {
                index_data: this.activity_part
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