<template>
    <div class="activity-part">
        <top_title :part_title="activity_part.part_title" :show_all="activity_part.show_all"></top_title>
        <div v-for="item in activity_data">
            <activity_item :item="item"></activity_item>
        </div>
    </div>
</template>

<script>
    import top_title from '../top_title.vue';
    import activity_item from './activity_item.vue';
    export default {
        props:['activity_part','default_activity_part'],
        data: function () {
            return {
                activity_data:{}
            }
        },
        methods:{

        },
        components:{
            top_title,
            activity_item
        },
        created:function(){
            this.set_data();
        },
        watch:{
            activity_part:{
                handler(curVal,oldVal){
                    this.set_data();
                    console.log('activity_part');
                    console.log(curVal);
                },
                deep:true
            }
        },
        methods:{
            set_data:function(){
                if(this.activity_part.status == 0){
                    this.activity_data = this.default_activity_part.list;
                }else {
                    this.activity_data = this.activity_part.list;
                }
            }
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