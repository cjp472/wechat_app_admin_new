
<!--* 首页模块序号 1-轮播图模块、2-分类导航模块、3-社群模块、4-会员及专利模块 5-问答模块 6-直播模块 7-最新模块-->

<template>
    <div class="index-part-drag">
        <div class="navigation-bar">
            <img src="/images/admin/shopDiy/navigation-bar@2x.png"/>
        </div>
        <!--可拖拽编辑区域-->
        <draggable class="drag-group" v-model="list" :options="dragOptions" :move="onMove" @start="isDragging=true" @end="isDragging=false">
            <div class="drag-group-item" :class="{ cursor_move : version_type ===3 }" v-for="(element,index) in list" :key="element.order"
                 @mouseover="mouseOverShow(index)" @click.stop="onEditClick(element,index)" :id="'drag-group-item'+index">
                <!--banner图模块-->
                <banner v-if="element.index_sort == 1" :banner_part="list[index].index_data"></banner>
                <!--分类导航模块-->
                <category v-else-if="element.index_sort == 2" :category_part="index_local_data.category_part"></category>
                <!--社群模块-->
                <community v-else-if="element.index_sort == 3" :community_part="list[index].index_data" :default_community_part="index_local_data.community_part"></community>
                <!--会员专栏模块-->
                <member v-else-if="element.index_sort == 4" :member_part="list[index].index_data" :default_member_part="index_local_data.member_part"></member>
                <!--问答模块-->
                <question v-else-if="element.index_sort == 5" :question_part="index_local_data.question_part"></question>
                <!--直播模块-->
                <alive v-else-if="element.index_sort == 6" :alive_part="list[index].index_data" :default_alive_part="index_local_data.alive_part"></alive>
                <!--最新模块-->
                <recommend v-else-if="element.index_sort == 7" :recommend_part="index_local_data.recommend_part"></recommend>

                <activity v-else-if="element.index_sort == 8" :activity_part="list[index].index_data" :default_activity_part="index_local_data.activity_part"></activity>

                <!--悬浮编辑操作-->
                <div class="actions" v-show="isMouseActive==index">
                    <div class="actions-wrap">
                            <span  v-if="element.index_sort == 1 ||  ((element.index_sort == 3 || element.index_sort == 4 ||  element.index_sort == 6 || element.index_sort == 8) && version_type ===3)
                                        " class="action edit" @click.stop="onEditClick(element,index)">编辑</span>
                        <span v-if="version_type ===3" class="action delete" @click.stop="onDelete(index)">删除</span>
                    </div>
                    <div class="action-tip">
                        <img src="/images/admin/shopDiy/icon-drag@2x.png"/>
                    </div>
                </div>


            </div>
        </draggable>
        <!--增加模块区域-->
        <index_part_add v-show="version_type === 3" :add_module_part="add_module_part"></index_part_add>
    </div>
</template>

<script>
    import EventBus from '../../libs/eventbus.js';
    // 拖拽操作模块
    import draggable from 'vuedraggable';
    //banner图模块
    import banner from './banner_part/banner.vue';
    //分类导航模块
    import category from './category_part/category.vue';
    //社群模块
    import community from './community_part/community.vue';
    //会员专栏模块
    import member from './member_part/member.vue';
    //直播模块
    import alive from './alive_part/alive.vue';
    //问答模块
    import question from './question_part/question.vue';
    //最新模块
    import recommend from './recommend_part/recommend.vue';
    //活动模块
    import activity from './activity_part/activity.vue';
    //    增加模块区域
    import index_part_add from './index_part_add.vue';
    //生成唯一id
    import uuid from '../../libs/generateUUID';


    export default {
        props: ['index_sort', 'index_data', 'index_local_data', 'add_module_part','index_data_id','version_type'],
        data () {
            return {
                //拖拽的列表数据
                list: [],
                isDragging: false,
                delayedDragging: false,
                isMouseActive: 0,
                isClickActive: 0,
                banner_part:''
            }
        },
        created: function () {

            //增加子模块
            EventBus.$on('add_part',this.addIndexSort);

            //监听子模块的资源列表变化
            EventBus.$on('part_list_change',this.partListChange);
        },
        methods: {
            //监听子模块的资源列表变化
            partListChange:function (obj) {
//                console.log("partListChange")

                let index_sort = obj.index_sort;
                let part_list = obj.part_list;
                let module_index = obj.module_index;

                this.list[module_index].index_data.list = part_list;

                if(index_sort === 1){
                }
                else{
                    let part_title = obj.part_title;
                    let status = obj.status;

//
//                    console.log(part_title);
//                    console.log(status);
                    this.list[module_index].index_data.part_title = part_title;
                    this.list[module_index].index_data.status = status;
                }
            },
            onMove ({relatedContext, draggedContext}) {
                if(this.version_type !== 3){
                    return false;
                }
//                return false;
//                const relatedElement = relatedContext.element;
//                const draggedElement = draggedContext.element;
//                console.log('relatedElement'+relatedElement.order);
//                console.log('draggedElement'+draggedElement.order);
//                this.isClickActive = relatedElement.order-1;
            },
            //鼠标悬浮到模块上
            mouseOverShow: function (index) {
                this.isMouseActive = index;
            },
            //点击编辑模块，并将数据同步到子组件
            onEditClick: function (item,index) {

                if(
                    item.index_sort == 1 ||
                    ((item.index_sort == 3 || item.index_sort == 4 ||  item.index_sort == 6 || item.index_sort == 8) && this.version_type ===3)
                ){
                    let topSize = document.getElementById('drag-group-item'+index).offsetTop;

                    let index_sort = item.index_sort;
                    let obj = {
                        //位置索引
                        index: index,
                        //离顶部距离
                        topSize: topSize,
                        //模块标号
                        index_sort: index_sort,
                        //模块数据
                        index_data: item.index_data,
                        //模块唯一id
                        index_data_id:item.index_data_id
                    };
                    this.isClickActive = index;
                    this.sidebarPartPosition(obj);
                }

            },
            //点击编辑后显示编辑模块
            sidebarPartPosition:function (obj) {
                EventBus.$emit('sidebar_part_position',obj);
            },
            //删除模块
            onDelete: function (index) {
                let item = this.list[index];
                let index_sort = item.index_sort;
                this.list.splice(index,1);

                EventBus.$emit('delete_part',index_sort);//与添加模块交互
                //删除后需要隐藏编辑模块
                EventBus.$emit('hide_edit_part');
            },
            //添加模块 add_type 1为往末尾加，2为往头部加
            addIndexSort: function (obj) {

                let index_sort = obj.index_sort;
                let add_type = obj.add_type;

                let part_title='';
                if(index_sort == 3){
                    part_title = '小社群';
                }
                else if(index_sort == 4){
                    part_title = '频道';
                }
                else if(index_sort == 6){
                    part_title = '直播';
                }
                else if(index_sort == 8){
                    part_title = '活动';
                }

                let index_data;
                if(index_sort === 1){
                    index_data = {list: []};
                }
                else if(index_sort === 3 || index_sort === 4 || index_sort === 6 || index_sort === 8){
                    index_data = {part_title:part_title,status:0,list:[]}
                }
                else{
                    index_data = {};
                }
                let item = {
                    index_sort: index_sort,
                    order: this.list.length+1,
                    index_data: index_data,
                    index_data_id:uuid.init()
                };

                if(add_type === 2){
                    this.list.unshift(item);
                }
                else{
                    this.list.push(item);
                }

            }
        },
        computed: {
            dragOptions () {
                return {
                    animation: 150,
                    group: 'description',
                    ghostClass: 'ghost'
                };
            }
        },
        watch: {
            isDragging:function(newValue) {
                console.log('isDragging');
                if (newValue) {
                    this.delayedDragging = true;
                    return
                }
                this.$nextTick(() => {
                    this.delayedDragging = false;
                    EventBus.$emit("hide_edit_part");
                })
            },
            //拖拽数据
            index_sort:function () {
                this.list = this.index_sort.map((item, index) => {
                    return {
                        index_sort: item,
                        order: index + 1,
                        index_data: this.index_data[index],
                        index_data_id:this.index_data_id[index]
                    };
                })
            },
            //监听数据变化更改同步给提交数据
            list:{
                handler: function() {
                    EventBus.$emit('watch_submit',this.list);
                },
                deep:true
            }
        },
        components: {
            draggable,
            banner,
            category,
            community,
            member,
            alive,
            question,
            recommend,
            activity,
            index_part_add
        }
    }
</script>

<style scoped>
    .index-part-drag {
        width: 100%;
        background-color: #efeff4;
    }

    .navigation-bar img {
        width: 100%;
        height: 65px;
    }

    .flip-list-move {
        transition: transform 0.5s;
    }

    .ghost {
        opacity: .5;
        background: #C8EBFB;
    }

    .drag-group-item {
        position: relative;

    }

    .cursor_move{
        cursor: move;
    }

    .drag-group-item .actions {
        display: inline-block;
        position: absolute;
        left: 0;
        bottom: 0;
        height: 100%;
        width: 375px;
        border: 2px dashed rgba(42,117,237,0.7);
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        z-index: 2;
    }
    .drag-group-item .actions .actions-wrap {
        position: absolute;
        bottom: 0;
        right: 0;
        font-size: 0;
    }
    .drag-group-item .actions .action-tip {
        position: absolute;
        top: 50%;
        right: 10px;
        width: 18px;
        height: 14px;
        margin-top: -7px;
    }
    .drag-group-item .actions .action-tip img {
        width: 100%;
        height: 100%;
    }
    .drag-group-item .actions .actions-wrap .action {
        display: inline-block;
        background-color: rgba(0,0,0,0.5);
        color: #fff;
        padding: 2px 6px;
        margin-left: 1px;
        font-size: 12px;
        cursor: pointer;
    }
</style>
