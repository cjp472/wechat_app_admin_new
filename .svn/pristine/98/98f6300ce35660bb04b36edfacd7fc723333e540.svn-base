<!--自定义资源模块-->
<template>
    <div class="resource-sidebar">
        <!--自定义标题-->
        <div class="resource-sidebar-title">
            <div class="resource-sidebar-text">自定义标题</div>
            <input class="resource-title-input" v-model="inside_data.part_title" maxlength="20"/>
        </div>
        <!--自定义显示规则-->
        <div class="resource-sidebar-rule">
            <div class="resource-sidebar-text">显示规则</div>
            <div class="resource-sidebar-radio">
                <input type="radio" id="default" value="0" v-model="inside_data.status" class="with-gap">
                <label for="default">默认规则</label>
                <br>
                <input type="radio" id="diy" value="1" v-model="inside_data.status" class="with-gap">
                <label for="diy">自定义内容</label>
            </div>
        </div>
        <!--资源列表-->
        <div class="resource-sidebar-action" v-show="inside_data.status == 1">
            <!--选择资源-->
            <div class="resource-sidebar-choose" @click="chooseResource">
                <img src="/images/admin/shopDiy/icon-add-sidebar@2x.png"><span>{{chooseText}}</span>
            </div>
            <!--资源拖拽列表-->
            <draggable class="drag-group" v-model="inside_data.resource_list" :options="dragOptions" :move="onMove" @start="isDragging=true" @end="isDragging=false">
                    <div class="drag-group-item" v-for="(item,resource_list_index) in inside_data.resource_list" :key="item.order"
                         @mouseover="mouseOverShow(resource_list_index)" :class="{blueColor:isActive==resource_list_index}">

                        <resource_drag_item :item="item.resource_item" :index_sort="index_sort"></resource_drag_item>

                        <div class="resource-show-tip-wrap" v-show="isActive==resource_list_index">
                            <img src="/images/admin/shopDiy/icon-drag-small@2x.png"/>
                            <img @click="deleteListResource(resource_list_index)" class="delete" src="/images/admin/shopDiy/icon-delete@2x.png"/>
                        </div>

                    </div>
            </draggable>
        </div>
    </div>
</template>

<script>
    import EventBus from '../../../libs/eventbus';
//    拖拽模块
    import draggable from 'vuedraggable';
//    资源拖拽列表
    import resource_drag_item from './resource_drag_item.vue';
    export default {
        props:['index','index_sort','index_data'],
        data:function () {
            return {
                //子组件数据
                inside_data:{
                    part_title:this.index_data.part_title,
                    status:this.index_data.status,
                    //资源列表数据
                    resource_list: this.index_data.list.map((item, index) => {
                        return {resource_item: item, order: index + 1};
                    })
                },
                isDragging: false,
                delayedDragging: false,
                isActive: 0,
            };
        },
        created:function () {
            //增加资源到编辑栏
            EventBus.$on("add_list_to_sidebar",this.addListToSidebar);
        },
        methods:{
            //添加列表资源
            addListToSidebar: function (obj) {


                let resource_list_length = this.inside_data.resource_list.length;

                //增加显示资源
                let temp = obj.need_add_data_list.map((item, index) => {
                    return {resource_item: item, order: resource_list_length + index + 1};
                });

                //增加资源的时候改为自定义
                if(temp.length>0){
                    let status = this.inside_data.status;
                    if(!parseInt(status)){
                        this.inside_data.status = 1;
                    }
                }
                this.inside_data.resource_list = this.inside_data.resource_list.concat(temp);


            },
            //删除列表资源
            deleteListResource: function (index) {
                //删除显示的资源
                this.inside_data.resource_list.splice(index,1);


                if(this.inside_data.resource_list.length === 0){
                    this.inside_data.status = 0;
                }
            },
            //选择资源，显示搜索框
            chooseResource: function () {
                let parm = {
                    index:this.index,
                    index_sort:this.index_sort,
                    index_data:this.index_data
                };
                //显示资源搜索框
                EventBus.$emit('is_show_choose_box',parm);
            },
            onMove: function () {},
            mouseOverShow: function (index) {
                this.isActive = index;
            },

        },
        computed: {
            dragOptions () {
                return {
                    animation: 150,
                    group: 'alive',
                    ghostClass: 'ghost'
                };
            },
            //选择资源按键标题
            chooseText:function () {
                let chooseText = "";
                let index_sort = this.index_sort;
                if(index_sort == 3){
                    chooseText = '选择社群';
                }
                else if(index_sort == 4){
                    chooseText = '选择频道';
                }
                else if(index_sort == 6){
                    chooseText = '选择直播';
                }
                return chooseText;
            }
        },
        watch: {
            //子组件增删改后同步给父组件
            inside_data:{
                handler: function () {
                    let part_list = [];


                    //还原回列表数据，同步给父组件
                    let resource_list = this.inside_data.resource_list;
                    for(let i=0,j=resource_list.length;i<j;i++){
                        part_list.push(resource_list[i].resource_item);
                    }

                    let parm = {
                        //模块的位置
                        module_index:this.index,
                        //模块内的资源列表
                        part_list:part_list,
                        index_sort:this.index_sort,
                        part_title:this.inside_data.part_title,
                        status:this.inside_data.status,
                    };
                    EventBus.$emit('part_list_change',parm);

                },
                deep: true
            },
            isDragging (newValue) {
                if (newValue) {
                    this.delayedDragging = true;
                    return
                }
                this.$nextTick(() => {
                    this.delayedDragging = false;
                })
            },
            //数据变化后资源列表随之变化
            index_data: function () {
                let that = this;
                if(!that.index_data){
                    return;
                }

                that.inside_data = {
                    part_title:that.index_data.part_title,
                    status:that.index_data.status,
                    //资源列表数据
                    resource_list: that.index_data.list.map((item, index) => {
                        return {resource_item: item, order: index + 1};
                    })
                }

            }
        },
        components:{
            draggable,
            resource_drag_item
        }
    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform 0.5s;
    }

    .ghost {
        opacity: .5;
        background: #C8EBFB;
    }

    .drag-group-item {
        position: relative;
        cursor: move;
    }
    .resource-sidebar {
        padding: 10px 0;
    }
    .resource-sidebar-title {
        width: 100%;
        height: 36px;
        line-height: 36px;
    }
    .resource-sidebar-text {
        float: left;
        width: 70px;
        margin-right: 20px;
    }
    .resource-title-input {
        float: left;
        display: block;
        width: 468px;
        height: 100%;
        border: 1px solid #e5e5e5;
        padding: 0 10px;
    }
    .resource-sidebar-rule {
        width: 100%;
        height: 80px;
        margin-top: 30px;
    }
    .resource-sidebar-radio {
        float: left;
        width: 468px;
        height: 100%;
        margin-top: -6px;
        margin-left: -3px;
    }
    .resource-sidebar-action {
        width: 466px;
        margin-left: 90px;
        border-radius: 2px;
        background-color: #ffffff;
        border: solid 1px #e5e5e5;
        padding: 10px;
    }
    .resource-sidebar-choose {
        width: 92px;
        height: 34px;
        line-height: 34px;
        border-radius: 2px;
        border: dashed 1px #2a75ed;
        font-size: 14px;
        color: #2a75ed;
        text-align: center;
        margin-bottom: 15px;
        cursor: pointer;
    }
    .resource-sidebar-choose img {
        width: 10px;
        height: 10px;
        margin-right: 10px;
        vertical-align: middle;
        margin-top: -3px;
    }
    .blueColor {
        background-color: #f6faff;
    }
    .resource-show-tip-wrap {
        position: absolute;
        top: 50%;
        right: 10px;
        margin-top: -6px;
        font-size: 0;
        z-index: 4;
    }
    .resource-show-tip-wrap img {
        display: inline-block;
        width: 12px;
        height: 12px;
    }
    .resource-show-tip-wrap .delete {
        margin-left: 10px;
        cursor: pointer;
    }
</style>