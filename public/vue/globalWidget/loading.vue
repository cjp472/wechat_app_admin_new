<!-- 正在加载组件 -->
<template>
    <div class="global-widget-loading">
        <div class="loading-content">
            <svg viewBox="25 25 50 50" class="circular">
                <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
            </svg>
            <p class="loadingText">{{tip_txt || '加载中'}}</p>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['tip_txt']
    }
</script>

<style scoped>
    .global-widget-loading{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        z-index: 998;
        background: hsla(0,0%,100%,.9);
    }
    .loading-content{
        position: absolute;
        width: 100px;
        height: 90px;
        top: 50%;
        left: 50%;
        margin-left: -45px;
        margin-top: -50px;
        background: hsla(0,0%,100%,.9)
    }
    .global-widget-loading .loadingText {
        text-align: center;
        color: #20a0ff;
        margin: 3px 0;
        font-size: 14px
    }

    .global-widget-loading .circular {
        width: 42px;
        height: 42px;
        -webkit-animation: loading-rotate 2s linear infinite;
        animation: loading-rotate 2s linear infinite
    }

    .global-widget-loading .path {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: 0;
        stroke-width: 2;
        stroke: #20a0ff;
        stroke-linecap: round;
        -webkit-animation: loading-dash 1.5s ease-in-out infinite;
        animation: loading-dash 1.5s ease-in-out infinite
    }

    @keyframes loading-dash {
        0% {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0
        }
        50% {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: -40px
        }
        100% {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: -120px
        }
    }

    @-webkit-keyframes loading-dash {
        0% {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0
        }
        50% {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: -40px
        }
        100% {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: -120px
        }
    }

    @keyframes loading-rotate {
        100% {
            -webkit-transform: rotate(1turn);
            transform: rotate(1turn)
        }
    }

    @-webkit-keyframes loading-rotate {
        100% {
            -webkit-transform: rotate(1turn);
            transform: rotate(1turn)
        }
    }
</style>
