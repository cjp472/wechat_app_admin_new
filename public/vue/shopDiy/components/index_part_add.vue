<template>
    <div class="index-part-add">
        <div class="main-title">添加模块</div>
        <div class="operation-part">
            <div class="wrapper">
                <span class="add-button-wrapper" v-for="(item,index) in all_module_part">
                    <div class="add-button" :class="{'cannot':item.status!=1 && all_module_arr[index] == 2 || all_module_arr[index] == 5 || all_module_arr[index] == 7 }" @click="addClick(item,index)">
                        <span v-if="item.module == 1"><span class="add-text-icon">+</span>轮播图</span>
                        <span v-if="item.module == 2"><span class="add-text-icon">+</span>分类导航</span>
                        <span v-if="item.module == 3"><span class="add-text-icon">+</span>社群</span>
                        <span v-if="item.module == 4"><span class="add-text-icon">+</span>频道</span>
                        <span v-if="item.module == 5"><span class="add-text-icon">+</span>问答</span>
                        <span v-if="item.module == 6"><span class="add-text-icon">+</span>直播</span>
                        <span v-if="item.module == 7"><span class="add-text-icon">+</span>最新</span>
                        <span v-if="item.module == 8"><span class="add-text-icon">+</span>活动</span>
                    </div>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import EventBus from '../../libs/eventbus.js';
    export default {
        props:['add_module_part'],
        data:function () {
            return {
                all_module_part:[],
                //所有模块的序号
                all_module_arr:[]
            }
        },
        created:function () {
            EventBus.$on('delete_part',this.updateAddPart);
        },
        methods:{
            addClick:function (item,index) {
                //如果添加的类型是分类导航、最新、问答，则只允许添加一个
                let type = this.all_module_arr[index];
                if(item.status !== 1 && type ==2 || type ==5 || type ==7){
//                    alert(item.msg);
                    console.log(type);
                    EventBus.$emit('show_top_prompt_tip',item.msg);
                }else {
                    let parm = {
                        index_sort:item.module,
                        add_type:1
                    }
                    EventBus.$emit('add_part', parm);
                    this.all_module_part.splice(index, 1, {
                        module: type,
                        status: 2,
                        msg: "该模块已添加，不能继续添加"
                    });

                }
            },
            updateAddPart: function (index_sort) {
                for (let i=0;i<this.all_module_arr.length;i++) {
                    if(index_sort === this.all_module_arr[i]){
                        this.all_module_part.splice(i, 1, {
                            module: this.all_module_arr[i],
                            status: 1,
                            msg: "可添加"
                        });
                    }
                }
            }
        },
        watch:{
            add_module_part: function () {
                this.all_module_part = this.add_module_part;
                this.all_module_arr = this.all_module_part.map((item) => {
                    return item.module;
                });
            }
        }
    }
</script>

<style scoped>
    .main-title {
        height: 22px;
        line-height: 22px;
        font-size: 16px;
        text-align: center;
        color: #353535;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .operation-part {
        padding: 0 11px;
        background-color: #efeff4;
        width: 100%;
        box-sizing: border-box;
        font-size: 0;
    }
    .sub-title {
        height: 20px;
        line-height: 20px;
        font-size: 14px;
        color: #666666;
        margin-bottom: 10px;
    }
    .wrapper {
        padding-bottom: 15px;
    }
    .add-button {
        display: inline-block;
        font-size: 14px;
        width: calc((100% - 15px) / 3);
        height: 40px;
        line-height: 40px;
        margin-right: 5px;
        margin-bottom: 5px;
        border-radius: 2px;
        background-color: #ffffff;
        border: solid 1px #dedede;
        text-align: center;
        color: #2a75ed;
        box-sizing: border-box;
        cursor: pointer;
    }
    .add-button-wrapper:nth-child(3), .add-button-wrapper:nth-child(6) {
        margin-right: 0;
    }
    .add-text-icon {
        margin-right: 5px;
        font-size: 16px;
        font-weight: 700;
    }
    .cannot {
        cursor: default;
        color: #888888;
    }
</style>