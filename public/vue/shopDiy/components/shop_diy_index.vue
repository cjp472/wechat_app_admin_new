<template>
    <div class="app-design">
        <!--左边的拖拽预览及增加模块部分-->
        <div class="app-preview" v-show="!is_show_loading">
            <index_part_drag
                    :index_sort="index_sort"
                    :index_data="index_data"
                    :add_module_part="add_module_part"
                    :index_local_data="index_local_data"
                    :index_data_id="index_data_id"
                    :version_type="version_type"
            ></index_part_drag>
        </div>
        <!--修改名称-->
        <set_index_name :current_name = "index_name" :share_info = "share_info"></set_index_name>
        <!--模块编辑部分-->
        <div class="app-sidebar">
            <sidebar_part></sidebar_part>
        </div>
        <!--保存模块部分-->
        <save_bar :index_name="index_name" :share_info = "share_info" :submit_data="submit_data" v-show="!is_show_loading"></save_bar>
        <!--浮层选择数据搜索-->
        <choose_box></choose_box>
        <!--全局loading-->
        <loading v-show="is_show_loading"></loading>
        <!--提交时全局loading-->
        <div class="show-submit-loading" v-show="is_show_submit_loading">
            <loading></loading>
        </div>
        <!--全局提示框-->
        <top_prompt></top_prompt>
        <notice_prompt></notice_prompt>
    </div>
</template>

<script>
    import NetWork from '../../libs/network';
    import EventBus from '../../libs/eventbus';
//    左边的拖拽预览及增加模块部分
    import index_part_drag from './index_part_drag.vue';
//    模块编辑部分
    import sidebar_part from './sidebar_part.vue';
//    保存模块部分
    import save_bar from './save_bar.vue';
//    浮层选择数据搜索
    import choose_box from './choose_box/choose_box.vue';
//    全局loading
    import loading from '../../globalWidget/loading.vue';
    import top_prompt from '../../globalWidget/top_prompt.vue';
    import notice_prompt from '../../globalWidget/notice_prompt.vue';
    //生成唯一id
    import uuid from '../../libs/generateUUID';
    //设置首页名
    import set_index_name from './set_index_name.vue';

    export default{
        data: function () {
            return {
                //版本 1-基础版;2-成长版;3-专业版
                version_type:1,
                //模块标号数组
                index_sort: [],
                //模块数据数组
                index_data: [],
                //模块唯一标识id数组
                index_data_id:[],
                //增加模块的数据
                add_module_part: [],
                submit_data:{
                    index_sort:[],
                    index_data:[]
                },
                //首页名称
                index_name:'',
                //分享的信息
                share_info:{},
                //是否显示全局样式标识
                is_show_loading:false,
                //提交时显示loading的标识
                is_show_submit_loading: false,
                //样式显示数据
                index_local_data: {
                    banner_part:{
                        img_url:''
                    },
                    category_part:{
                        list:[
                            {
                                icon_url:"/images/admin/shopDiy/icon-1@2x.png",
                                category_name:"分类一",
                            },
                            {
                                icon_url:"/images/admin/shopDiy/icon-2@2x.png",
                                category_name:"分类二",
                            },
                            {
                                icon_url:"/images/admin/shopDiy/icon-3@2x.png",
                                category_name:"分类三",
                            },
                            {
                                icon_url:"/images/admin/shopDiy/icon-4@2x.png",
                                category_name:"分类四",
                            }
                        ]
                    }
                },
//                index_sort:[1, 2, 3, 4, 5, 6, 7],
//                index_data:[
//                    {
//                        list:[
//                            {
//                                id:"1",
//                                title:"1",
//                                image_url:"",
//                                img_url_compressed:"",
//                                //跳转类型，0-不跳转， 1-图文，2-音频，3-视频，4-圈子贴子，5-url, 6-专栏
//                                skip_type:"",
//                                //跳转目的地,如果不是直接跳转的链接，就都是相应资源id
//                                skip_target:"",
//                                skip_title:""
//                            },
//                            {
//                                id:"2",
//                                title:"2",
//                                image_url:"",
//                                img_url_compressed:"",
//                                //跳转类型，0-不跳转， 1-图文，2-音频，3-视频，4-圈子贴子，5-url, 6-专栏
//                                skip_type:"",
//                                //跳转目的地,如果不是直接跳转的链接，就都是相应资源id
//                                skip_target:"",
//                                skip_title:""
//                            },
//                            {
//                                id:"3",
//                                title:"3",
//                                image_url:"",
//                                img_url_compressed:"",
//                                //跳转类型，0-不跳转， 1-图文，2-音频，3-视频，4-圈子贴子，5-url, 6-专栏
//                                skip_type:"",
//                                //跳转目的地,如果不是直接跳转的链接，就都是相应资源id
//                                skip_target:"",
//                                skip_title:""
//                            }
//                        ]
//                    },
//                    {
//                        list:[]
//                    },
//                    {
//                        part_title:"小社群",
//                        //0则使用默认配置
//                        status:1,
//                        list:[
//                            {
//                                id:"",
//                                img_url_compressed:"",
//                                title:""
//                            }
//                        ]
//                    },
//                    {
//                        part_title:"会员",
//                        //0则使用默认配置
//                        status:1,
//                        list:[
//                            {
//                                id:"1",
//                                img_url_compressed:"http://wechatapppro-1252524126.file.myqcloud.com/image/5f901770e1d8b9d71ca9003dd2a66b77.jpg",
//                                title:"频道一",
//                                is_member: 0
//                            },
//                            {
//                                id:"1",
//                                img_url_compressed:"http://wechatapppro-1252524126.file.myqcloud.com/image/5f901770e1d8b9d71ca9003dd2a66b77.jpg",
//                                title:"频道二",
//                                is_member: 1
//                            },
//                            {
//                                id:"1",
//                                img_url_compressed:"http://wechatapppro-1252524126.file.myqcloud.com/image/5f901770e1d8b9d71ca9003dd2a66b77.jpg",
//                                title:"频道三",
//                                is_member: 0
//                            }
//                        ]
//                    },
//                    {
//                        list:[]
//                    },
//                    {
//                        part_title:"直播",
//                        //0则使用默认配置
//                        status:1,
//                        list:[
//                            {
//                                id:"1",
//                                img_url_compressed:"http://wechatapppro-1252524126.file.myqcloud.com/image/5f901770e1d8b9d71ca9003dd2a66b77.jpg",
//                                title:"直播一"
//                            },
//                            {
//                                id:"2",
//                                img_url_compressed:"http://wechatapppro-1252524126.file.myqcloud.com/image/5f901770e1d8b9d71ca9003dd2a66b77.jpg",
//                                title:"直播二"
//                            },
//                            {
//                                id:"3",
//                                img_url_compressed:"http://wechatapppro-1252524126.file.myqcloud.com/image/5f901770e1d8b9d71ca9003dd2a66b77.jpg",
//                                title:"直播三"
//                            }
//                        ]
//                    },
//                    {
//                        list:[]
//                    },
//                    {
//                        list:[]
//                    }
//                ],
            }
        },
        created:function () {
            let that = this;
            that.is_show_loading = true;
            NetWork.request("load_diy_setting",{},function (data) {
                if(parseInt(data.code) === 0){
                    let version_type = data.data.version_type;
                    let index_sort = data.data.index_sort;
                    let index_data = data.data.index_data;

                    //不是专业版且没有轮播图模块，手动加一个空模块
                    if(version_type !== 3 && index_sort.indexOf(1)<0){
                        index_sort.unshift(1);
                        let add_data = {
                            list:[]
                        };
                        index_data.unshift(add_data);

                    }

                    if(version_type !== 3){
                        EventBus.$emit('is_show_notice_prompt',
                            {
                                text:'专业版用户可拥有增删模块、模块排序、自定义模块内容等更多高级功能',
                                url:'/upgrade_account',
                                button:'立即升级'
                            }
                                )
                    }

                    that.index_sort = index_sort;
                    that.index_data = data.data.index_data;
                    that.add_module_part = data.data.add_module_part;
                    that.is_show_loading = false;
                    that.version_type = version_type;
                    that.index_name = data.data.index_title;
                    that.share_info = data.data.index_info;
                    //数据动态显示 --roam
                    //that.index_local_data.banner_part.img_url = that.index_data[0].list[0].img_url_compressed || that.index_data[0].list[0].img_url || '';
                    let question_info = data.data.index_data_default[3];
                    that.index_local_data.question_part = {
                        part_title:question_info.part_title,
                        list:[
                            {
                                img_url_compressed:question_info.queInfo.img_url_compressed,
                                title: question_info.queInfo.title,
                                desc: question_info.queInfo.desc,
                                total_num: question_info.queInfo.queCount
                            }
                        ]
                    };
                    let recommend_info = data.data.index_data_default[5];
                    that.index_local_data.recommend_part = {
                        part_title:recommend_info.part_title,
                        show_all:recommend_info.show_all,
                        list:recommend_info.categoryInfo
                    };

                    let alive_info = data.data.index_data_default[4];
                    that.index_local_data.alive_part = {
                        part_title:alive_info.part_title,
                        show_all:true,
                        list:alive_info.list
                    };

                    let active_info = data.data.index_data_default[6];
                    that.index_local_data.activity_part = {
                        part_title:active_info.part_title,
                        show_all:true,
                        list:active_info.list
                    };

                    let community_info = data.data.index_data_default[1];
                    that.index_local_data.community_part = {
                        part_title:community_info.part_title,
                        list:community_info.list
                    };
                    let member_info = data.data.index_data_default[2];
                    that.index_local_data.member_part = {
                        part_title:member_info.part_title,
                        list:member_info.list
                    };
                    //数据动态显示final --roam
                    let index_data_id = [];
                    for(let i=0,j=that.index_sort.length;i<j;i++){
                        index_data_id.push(uuid.init());
                    }
                    that.index_data_id = index_data_id;
                }else{
                    that.is_show_loading = false;
                    EventBus.$emit('show_top_prompt_tip','读取配置错误，请刷新页面重试');
//                    alert('读取配置错误，请刷新页面重试');
                }
            });

            //监听提交数据的改变
            EventBus.$on('watch_submit',this.watchSubmit);
            //提交数据时显示loading
            EventBus.$on('is_show_submit_loading',function (bool) {
                that.is_show_submit_loading = bool;
            });

            EventBus.$on('watch_index_info',function(index_info){
                that.index_name = index_info.index_title;
                that.share_info = {
                    wx_share_title:index_info.wx_share_title,
                    wx_share_content: index_info.wx_share_content,
                    wx_share_image: "http://wechatapppro-1252524126.file.myqcloud.com/apppcHqlTPT3482/image/44f1c2cec42ec4b1ba37bcc110f1677c.jpeg",
                    wx_share_image_compressed: "http://wechatapppro-1252524126.file.myqcloud.com/apppcHqlTPT3482/image/compress/44f1c2cec42ec4b1ba37bcc110f1677c.jpeg"
                }
            });
        },
        methods: {
            //监听拖拽模块的数据变化，同步给保存提交模块
            watchSubmit:function (dragArr) {
                //还原成可提交的数据格式
                let index_sort = [];
                let index_data = [];
                console.log(dragArr);
                for(let i=0,j=dragArr.length;i<j;i++){
                    let tmp_sort = dragArr[i].index_sort;
                    let tmp_data = {};
                    if(
                        tmp_sort == 1 ||
                        tmp_sort == 3 ||
                        tmp_sort == 4 ||
                        tmp_sort == 6 ||
                        tmp_sort == 8
                    ){
                        tmp_data =  dragArr[i].index_data;
                    }

                    index_sort.push(tmp_sort);
                    index_data.push(tmp_data)
                }

                let result = {
                    index_sort:index_sort,
                    index_data:index_data
                };
                this.submit_data = result;
            }
        },
        components: {
            index_part_drag,
            sidebar_part,
            choose_box,
            save_bar,
            loading,
            top_prompt,
            notice_prompt,
            set_index_name
        }
    }
</script>

<style>
    .app-design {
        position: relative;
        width: 100%;
        background-color: #fff;
        overflow: hidden;
        padding-top: 30px;
        padding-bottom: 70px;
        min-height: 670px;
    }
    .app-preview {
        position: relative;
        float: left;
        width: 375px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background: #fff;
        margin-left: 60px;
    }
    .app-sidebar {
        position: relative;
        float: left;
        width: 580px;
        margin-left: 10px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background: #fff;
    }
    .show-submit-loading {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        z-index: 1000;
    }
</style>