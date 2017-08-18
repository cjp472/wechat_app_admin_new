{{--直播间显示设置--}}
<div  class="alive-show-set-modal">
    {{--透明灰色背景--}}
    <div class="alive-show-set-bg"></div>
    {{--弹窗内容--}}
    <div class="alive-show-set-wrapper">
        {{--头部--}}
        <div class="alive-head">
            <span>直播间显示设置</span>
            <div class="close-icon"></div>
        </div>
        {{--中间部分--}}
        <div class="alive-show-center">
            {{--参与人次--}}
            <div class="alive-show-person">
                <div class="alive-show-h2"><span>参与人次</span><span class="star-sign">*</span></div>
                <div class="alive-show-control">
                    <div class="alive-show-item choose-1" id="num_all_show"><span class="choose-btn choose-active"></span><span>对所有人可见</span></div>
                    <div class="alive-show-item choose-1" id="num_teacher_show"><span class="choose-btn"></span><span>仅讲师可见</span></div>
                </div>
            </div>
            {{--打赏提醒--}}
            <div class="alive-show-hint">
                <div class="alive-show-h2"><span>打赏提醒</span><span class="star-sign">*</span></div>
                <div class="alive-show-control">
                    <div class="alive-show-item choose-2" id="reword_all_show"><span class="choose-btn choose-active"></span><span>对所有人可见</span></div>
                    <div class="alive-show-item choose-2" id="reword_teacher_show"><span class="choose-btn"></span><span>仅讲师和打赏者可见</span></div>
                </div>
            </div>
        </div>
        {{--尾部--}}
        <div class="alive-show-footer">
            <button id="alive_show_cancel_btn" class="cancel-btn">取消</button>
            <button id="alive_show_save_btn" class="confirm-btn">保存</button>
        </div>
    </div>
</div>

<script>
    var $aliveShowSet = (function(){

        var $aliveShowSet = {};

        $aliveShowSet.showSet = function(that, aliveId, showViewCount, showReward){
            // 回填数据
            if(showViewCount){
                // 讲师可见
                $('#num_teacher_show > .choose-btn').addClass('choose-active');
                $('#num_all_show > .choose-btn').removeClass('choose-active');
            }else{
                // 所有人可见
                $('#num_all_show > .choose-btn').addClass('choose-active');
                $('#num_teacher_show > .choose-btn').removeClass('choose-active');
            }
            if(showReward){
                // 讲师可见
                $('#reword_teacher_show > .choose-btn').addClass('choose-active');
                $('#reword_all_show > .choose-btn').removeClass('choose-active');
            }else{
                // 所有人可见
                $('#reword_all_show > .choose-btn').addClass('choose-active');
                $('#reword_teacher_show > .choose-btn').removeClass('choose-active');
            }

            // 显示模态框
            $(".alive-show-set-modal").fadeIn(300);

            // 点击保存
            $('#alive_show_save_btn').unbind('click').click(function(){

                console.log("已经点击保存");

                var show_view_count = 1,
                    show_reward = 1;

                if($('#num_all_show').children('.choose-btn').hasClass('choose-active')){
                    show_view_count = 0;
                }
                if($('#reword_all_show').children('.choose-btn').hasClass('choose-active')){
                    show_reward = 0;
                }

                $.ajax("/set_alive_config", {
                    type: "POST",
                    dataType: "json",
                    data: {
                        id: aliveId,
                        config_show_view_count: show_view_count,
                        config_show_reward: show_reward
                    },
                    success: function (result) {
                        if (result.code === 0) {
                            $(".alive-show-set-modal").fadeOut(300);
                            that.data('config_show_view_count', show_view_count);
                            that.data('config_show_reward',show_reward);
                            console.log("保存成功");
                        } else {
                            baseUtils.show.redTip("操作失败，请稍后重试！");
                        }
                    },
                    error: function (xhr, status, err) {
                        console.log(err);
                        baseUtils.show.redTip("网络错误，请稍后再试！");
                    },
                });
            });

        };

        $aliveShowSet.init = function(){
            // 直播间显示设置
            $(".close-icon,#alive_show_cancel_btn").click(function(){ // 关闭
                $(".alive-show-set-modal").fadeOut(300);
            });
            $('.choose-1').on('click',function(){ // 按钮点击切换
                $('.choose-1').children('.choose-btn').removeClass('choose-active');
                $(this).children('.choose-btn').addClass('choose-active');
            });
            $('.choose-2').on('click',function(){ // 按钮点击切换
                $('.choose-2').children('.choose-btn').removeClass('choose-active');
                $(this).children('.choose-btn').addClass('choose-active');
            });
            // 点击“直播间显示设置”按钮
            $('body').on('click','.alive-show-set-btn', function(){
//            $('.alive-show-set-btn').click(function(){
//                var showViewCount = $(this).parents('.tr_body').data('config_show_view_count'),
//                    showReward = $(this).parents('.tr_body').data('config_show_reward'),
//                    resId = $(this).parents(".tr_body").data("resource_id");

                var showViewCount = $(this).data('config_show_view_count'),
                    showReward = $(this).data('config_show_reward'),
                    resId = $(this).data('resource_id');

                $aliveShowSet.showSet($(this),resId,showViewCount,showReward);

            });

        };

        return $aliveShowSet;
    })();
</script>

<style>
    .alive-show-item {
        cursor: pointer;
    }
    .confirm-btn {
        width: 100px;
        height: 36px;
        background-color: #2a75ed;
        color: #fff;
        font-size: 14px;
        border: none;
        border-radius: 2px;
        margin-left: 20px;
    }
    .confirm-btn:active {
        opacity: 0.8;
    }
    .cancel-btn {
        width: 100px;
        height: 36px;
        border: 1px solid #e5e7eb;
        background-color: #fafbfc;
        color: #353535;
        font-size: 14px;
        border-radius: 2px;
    }
    .cancel-btn:active {
        opacity: 0.8;
    }

    .choose-active {
        position: relative;
        border: 1px solid #2a75ed !important;
    }
    .choose-active:after {
        content: '';
        display: block;
        position: absolute;
        top: 1px;
        left: 1px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #2a75ed;

    }
    .choose-btn {
        display: inline-block;
        position: relative;
        top: 2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid #dcdcdc;
        margin-right: 10px;
    }

    .alive-show-center > div {
        font-size: 0;
    }

    .alive-show-control {
        display: inline-block;
        height: 57px;
        font-size: 14px;
        margin-left: 61px;
        color: #243042;
    }

    .alive-show-control > div:first-child {
        margin-bottom: 17px;
    }

    .star-sign {
        position: relative;
        top: -4px;
        left: 4px;
        color: #e64340;
    }

    .alive-show-ctr-item {

    }

    .alive-show-h2 {
        display: inline-block;
        height: 57px;
        width: 189px;
        font-size: 14px;
        text-align: right;
        vertical-align: top;
        color: #353535;
    }

    .alive-show-footer {
        height: 66px;
        text-align: center;
    }

    .alive-show-center {
        padding: 55px 0 50px 0;
    }
    .alive-show-person {
        margin-bottom: 35px;
    }

    .alive-head {
        height: 52px;
        background-color: #fafbfc;
        border-radius: 10px 10px  0 0;
        margin-top: -1px;
    }
    .alive-head > span {
        height: 52px;
        line-height: 52px;
        padding-left: 20px;
        font-size: 16px;
        color: #353535;
    }
    .close-icon {
        position: relative;
        float: right;
        padding: 26px;
        cursor: pointer;
    }
    .close-icon:after {
        content: '';
        display: block;
        position: absolute;
        top: 25px;
        right: 20px;
        width: 17px;
        border: 1px solid #d8d8d8;
        -webkit-transform: rotate(45deg);
        -moz-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        -o-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    .close-icon:before {
        content: '';
        display: block;
        position: absolute;
        top: 25px;
        right: 20px;
        width: 17px;
        border: 1px solid #d8d8d8;
        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        -o-transform: rotate(-45deg);
        transform: rotate(-45deg);
    }

    .alive-show-set-modal {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 1000;
        display: none;
    }
    .alive-show-set-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
    }
    .alive-show-set-wrapper {
        width: 520px;
        position: absolute;
        top: 50%;
        left: 50%;
        border-radius: 10px;
        background-color: #fff;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }
</style>