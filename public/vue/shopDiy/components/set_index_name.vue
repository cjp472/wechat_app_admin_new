<style>
    .set_table{
        text-align: left;
    }
    #set_index_name{
        border:1px solid #eee;
        padding:20px;
        width:570px;
        margin-left:10px;
        float: left;
    }
    .set_table tr td.title{
        width:110px;
        font-size:14px;
        color:#353535;
        line-height:50px;
    }
    .set_table tr td.cont{
        width:410px;
    }
    .set_table tr td.cont input{
        width:100%;
        border:1px solid #eee;
        line-height:36px;
        margin:0 7px;
        text-indent:10px;
    }
    .share_img_tip{
        color: #b2b2b2;
        font-size:14px;
        line-height:24px;
    }
    .select_img_btn{
        width: 100px;
        height: 36px;
        border-radius: 2px;
        background-color: #fafbfc;
        border: solid 1px #e5e7eb;
        font-size: 14px;
        text-align: center;
        color: #353535;
        display: block;
        position: relative;
        line-height:36px;
    }
    .select_img_btn input{
        position: absolute;
        width:100%;
        height:100%;
        top:0;
        left:0;
        opacity:0;
    }
</style>

<template>

    <div id="set_index_name">
        <table class="set_table">
            <tr>
                <td class="title">首页名称</td>
                <td class="cont"><input placeholder="请输入首页名称" type="text" class="testclass" id="set_name" :value="current_name" @change="setName()">
                </td>
            </tr>
            <tr>
                <td class="title">页面分享标题</td>
                <td class="cont"><input placeholder="用户通过微信分享时，会自动显示分享标题" type="text" class="testclass" id="share_title" :value="share_info.wx_share_title" @change="setName()">
                </td>
            </tr>
            <tr>
                <td class="title">页面分享描述</td>
                <td class="cont"><input placeholder="用户通过微信分享时，会自动显示分享描述" type="text" class="testclass" id="share_content" :value="share_info.wx_share_content" @change="setName()">
                </td>
            </tr>
            <tr>
                <td class="title">页面分享配图</td>
                <td class="">
                    <img :src="share_info.wx_share_image || share_info.wx_share_image_compressed" alt="">
                    <p class="share_img_tip">图片格式为：bmp,jpeg,jpg,gif,尺寸1:1，不可大于2M</p>

                    <div class="select_img_btn">
                        选择图片
                        <input type="file" >
                    </div>
                </td>
            </tr>


        </table>

        <div>

        </div>
    </div>

</template>

<script>
    import NetWork from '../../libs/network';
    import EventBus from '../../libs/eventbus';

    export default{
        props:['current_name','share_info'],
        data: function () {
            return {
                c_index_name:'',
                c_share_info:{}
            }
        },
        created: function(){

        },
        methods: {
            setName:function(){
                let name = document.getElementById('set_name').value;
                let share_title = document.getElementById('share_title').value;
                let share_content = document.getElementById('share_content').value;
                //let share_content = document.getElementById('share_content').value;
                let index_info = {
                    index_title:name,
                    wx_share_title:share_title,
                    wx_share_content:share_content,
                    wx_share_image:this.share_info.wx_share_image
                };
                EventBus.$emit('watch_index_info',index_info);
            }
        }
    }

</script>