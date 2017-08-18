<!--资源选择框-->
<template>
    <div class="alive-drag-item">
        <span class="label-title">{{label}}</span><img :src="item.img_url_compressed"/><span class="alive-drag-item-title">{{item.title}}</span>
    </div>
</template>

<script>
    export default {
        props:['item','index_sort'],
        computed:{
            label:function () {
                let labelText = "";
                let index_sort = this.index_sort;
                let item = this.item;

                if(index_sort == 3){
                    labelText = '社群';
                }
                else if(index_sort == 4){
                    if(item.is_member == 0){
                        labelText = '专栏';
                    }
                    else{
                        labelText = '会员'
                    }
                }
                else if(index_sort == 6){
                    labelText = '直播';
                }
                return labelText;
            }
        }
    }
</script>

<style>
    .alive-drag-item {
        width: 100%;
        height: 40px;
        line-height: 40px;
        padding-left: 5px;
        margin: 5px 0;
    }
    .label-title {
        font-size: 14px;
        margin-right: 50px;
        color: #666666;
    }
    .alive-drag-item img {
        width: 40px;
        height: 30px;
        border-radius: 2px;
        margin-right: 10px;
    }
    .alive-drag-item-title {
        font-size: 14px;
        color: #666666;
        width: 250px;
    }
</style>