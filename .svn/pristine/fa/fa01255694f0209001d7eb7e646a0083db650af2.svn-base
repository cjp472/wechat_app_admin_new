<!DOCTYPE HTML>
<html>
<header>
</header>
<body>
<div class="t2">
    音频测试
</div>

<div style="margin-top: 20px">
    <div>m3u8</div>
    <audio id="m3u8"
           controls="controls"
           style="height: 200px;width: 100%"
           src="http://wechatappdev-10011692.file.myqcloud.com/appRY9yTVR18157/audio/999a193b9e4cdc179a92759761ef6308/999a193b9e4cdc179a92759761ef6308.m3u8"
    </audio>
</div>


<!--<button id="play_aac" style="width: 200px;height: 80px;font-size: 24px">播放</button>-->


<div style="margin-top: 20px">
    <div>外部链接</div>
    <audio id="mp3_src"
           style="height: 200px;width: 100%"
           controls
           src="http://m10.music.126.net/20170122220747/0edbba4cde9f4e4dfc56f063825e6440/ymusic/993f/bdf3/a0b6/b9ddc3ecdec1ef2afe162b1aaa9b1a54.mp3">
    </audio>
</div>


<button id="play" style="width: 200px;height: 80px;font-size: 24px">播放</button>


<div id="audio_error" style="height: 30px"></div>

<script>
//    var audio_m3u8 = document.getElementById('m3u8');
    var audio = document.getElementById('mp3_src');
//    var play_m3u8 = document.getElementById('play_m3u8');
    var play = document.getElementById('play');
    var error_log =  document.getElementById('audio_error');

    play.addEventListener('click',function () {
        try {
            alert('准备播放了');
            audio.play();
        }catch (e){
            error_log.innerHTML = e;
        }

    });
//
//    play_m3u8.addEventListener('click',function () {
//        try {
//            alert('准备播放了');
//            audio_m3u8.play();
//        }catch (e){
//            error_log.innerHTML = e;
//        }
//    });


    audio.addEventListener('error', function () {
        var code = this.error.code;
        console.log("报错了"+code);
        var USER_STOP = 1;//用户终止
        var NET_ERROR = 2;//网络错误
        var DECODE_ERROR = 3;//解码错误
        var URL_ERROR = 4;//URL无效

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