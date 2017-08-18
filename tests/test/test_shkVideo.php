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
       style="height: 100%;width: 100%;"
       x-webkit-airplay="true"
       webkit-playsinline=""
       playsinline="true"
       preload="meta"
       src="http://200026219.vod.myqcloud.com/200026219_9a87704ab55311e6b4ffe794f861e232.f20.mp4?rand="<? echo time() ?>>

</video>

hls
<video id="video_dom" controls
       style="height: 100%;width: 100%;"
       x-webkit-airplay="true"
       webkit-playsinline=""
       playsinline="true"
       preload="meta"
       src="http://200026219.vod.myqcloud.com/200026219_9a87704ab55311e6b4ffe794f861e232.f230.av.m3u8?rand="<? echo time() ?>>

</video>


</body>
</html>