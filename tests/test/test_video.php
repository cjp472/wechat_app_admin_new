<!DOCTYPE html>
<html>
<body>
<video id="video_mp4_high" controls
       style="height: 500px;width: 100%;"
       preload="meta"
       x-webkit-airplay="true"
       webkit-playsinline=""
       playsinline="true"
       src="http://200026219.vod.myqcloud.com/200026219_e64df78acfe611e682cc955c7831a62c.f30.mp4"></video>

<video id="video_mp4_high_m3u8" controls
       style="height: 500px;width: 100%;"
       preload="meta"
       x-webkit-airplay="true"
       webkit-playsinline=""
       playsinline="true"
       src="http://200026219.vod.myqcloud.com/200026219_e64df78acfe611e682cc955c7831a62c.f230.av.m3u8"></video>


<!--<button id="play" style="width: 200px;height: 80px;font-size: 24px">播放</button>-->
<div id="video_error_high" style="height: 30px"></div>
<div id="video_error" style="height: 30px"></div>

<script>


    var video_mp4_high = document.getElementById('video_mp4_high');

    var play = document.getElementById('play');
    play.addEventListener('click', function () {
        video_mp4_high.play();
    });
    video_mp4_high.addEventListener('error', function () {
        var code = this.error.code;
        console.log("报错了" + code);
        var USER_STOP = 1;//用户终止
        var NET_ERROR = 2;//网络错误
        var DECODE_ERROR = 3;//解码错误
        var URL_ERROR = 4;//URL无效
        var error_log = document.getElementById('video_error_high');
        switch (code) {
            case USER_STOP:
                //用户自己停止，不管
                error_log.innerHTML = 'USER_STOP';
                break;
            case NET_ERROR:
                error_log.innerHTML = 'NET_ERROR';
                break;
            case DECODE_ERROR:
                error_log.innerHTML = 'DECODE_ERROR';
                break;
            case URL_ERROR:
                error_log.innerHTML = 'URL_ERROR';
                break;
        }
    });

</script>


</body>
</html>
