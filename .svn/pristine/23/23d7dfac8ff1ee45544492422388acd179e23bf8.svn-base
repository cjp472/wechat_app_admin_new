<!DOCTYPE html>
<html>
<body>

<!--<video width="500" height="500"-->
<!--       controls="controls"-->
<!--       autoplay="autoplay"-->
<!--       src="./audio_test/output.m3u8">-->
<!--</video>-->

mp4
<video id="video_dom" controls
       style="height: 80%;width: 100%;"
       x-webkit-airplay="true"
       webkit-playsinline=""
       playsinline="true"
       preload="meta"
       src="http://200026219.vod.myqcloud.com/200026219_adbd91cac26a11e6bd8c575b4bd12e5c.f20.mp4?rand="<? echo time() ?>>
</video>

<div id="error" style="height: 30px"></div>

<!--<video id="video_dom" controls-->
<!--       style="height: 100%;width: 100%;"-->
<!--       x-webkit-airplay="true"-->
<!--       webkit-playsinline=""-->
<!--       playsinline="true"-->
<!--       preload="meta"-->
<!--       src="http://200026219.vod.myqcloud.com/200026219_a43c20ccb78811e6b4ffe794f861e232.f20.mp4?rand="-->
<? // echo time() ?><!-->-->
<!--</video>-->

<script>
    var video = document.getElementById('video_dom');
    video.addEventListener('error', function () {
        var code = this.error.code;
        console.log("报错了"+code);
        var USER_STOP = 1;//用户终止
        var NET_ERROR = 2;//网络错误
        var DECODE_ERROR = 3;//解码错误
        var URL_ERROR = 4;//URL无效
        var error_log =  document.getElementById('error');
        switch (code) {
            case USER_STOP:
                //用户自己停止，不管
                error_log.innerHTML='USER_STOP';
                break;
            case NET_ERROR:
                error_log.innerHTML='NET_ERROR';
                break;
            case DECODE_ERROR:
                error_log.innerHTML='DECODE_ERROR';
                break;
            case URL_ERROR:
                error_log.innerHTML='URL_ERROR';
                break;
        }
    });

</script>

</body>
</html>