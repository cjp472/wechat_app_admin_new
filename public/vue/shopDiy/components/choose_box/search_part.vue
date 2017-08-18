<template>
    <div class="choose-box-search">
        <input class="choose-search-input" type="text" v-model="input_word" placeholder="输入名称"/>
        <span class="choose-search-button" @click="sendWord">搜索</span>
        <img class="choose-search-icon" src="/images/admin/shopDiy/icon_fenxiao_search.png"/>
    </div>
</template>

<script>
    import EventBus from '../../../libs/eventbus';
    export default {
        data: function () {
            return {
                input_word:'',
                id_arr:[]
            }
        },
        created: function () {
            let that = this;
            //获取已选中的资源id
            EventBus.$on('get_resource_choosed_id',function (obj) {
                console.log(obj.id_arr)

                that.id_arr = obj.id_arr;
            });
            EventBus.$on('clear_input_word',function () {
                that.input_word = '';
            });
        },
        methods: {
            sendWord: function () {
                let that = this;
                //将搜索框数据同步给搜索模块
                EventBus.$emit('get_input_word',
                    {
                        input_word:that.input_word,
                        id_arr:that.id_arr
                    });
            }
        }
    }
</script>

<style>
    .choose-box-search {
        position: relative;
        height: 36px;
        line-height: 36px;
        margin: 20px 0 20px 30px;
    }
    .choose-search-input {
        display: inline-block;
        width: 240px;
        height: 100%;
        border-radius: 2px;
        border: solid 1px #dcdcdc;
        padding: 0 10px 0 36px;
    }
    .choose-search-button {
        display: inline-block;
        width: 80px;
        height: 100%;
        margin-left: 10px;
        border-radius: 2px;
        background-color: #fafbfc;
        border: solid 1px #e5e7eb;
        cursor: pointer;
        font-size: 14px;
        text-align: center;
        color: #666666;
    }
    .choose-search-icon {
        position: absolute;
        display: block;
        left: 10px;
        top: 50%;
        margin-top: -8px;
        width: 16px;
        height: 16px;
    }
</style>