<template>
    <div class="alive-part">
        <top_title :part_title="alive_part.part_title" :show_all="alive_part.show_all"></top_title>
        <div  v-for="(item,index) in alive_data">
            <alive_item :item="item" :index="index"></alive_item>
        </div>
    </div>
</template>

<script>
    import top_title from '../top_title.vue';
    import alive_item from './alive_item.vue';
    export default {
        props:['alive_part','default_alive_part'],
        data:function () {
            return{
                alive_data:{}
            }
        },
        components: {
            top_title,
            alive_item
        },
        created:function(){
            this.set_data();
        },
        watch:{
            alive_part:{
                handler(curVal,oldVal){
                    this.set_data();
                },
                deep:true
            }
        },
        methods:{
            set_data:function(){
                if(this.alive_part.status == 0){
                    this.alive_data = this.default_alive_part.list;
                }else {
                    this.alive_data = this.alive_part.list;
                }
            }
        }

    }
</script>

<style>
    .alive-part {
        padding: 0 15px;
        margin-bottom: 5px;
        background-color: #fff;
        overflow: hidden;
        box-sizing: border-box;
        border: solid 1px #eeeeee;
    }
</style>