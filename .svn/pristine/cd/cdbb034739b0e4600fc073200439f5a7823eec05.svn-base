<div class="listBox"style="display:block">
    <ul class="listItem">
        @foreach($data as $value)
            <li>
                <input id="{{$value->id}}" type="checkBox" value="{{$value->id}}" data-type="{{$value->goods_type}}">
                <label for="{{$value->id}}">
                    <div>
                        <img src="{{$value->img_url_compressed}}" alt="{{$value->title}}">
                        <span>{{$value->title}}</span>
                    </div>
                    <span>
                        @if($value->goods_type==1)
                            图文
                        @elseif($value->goods_type==2)
                            音频
                        @elseif($value->goods_type==3)
                            视频
                        @elseif($value->goods_type==4)
                            直播
                        @elseif($value->goods_type==5)
                            专栏
                        @elseif($value->goods_type==6)
                            会员
                        @endif
                </span>
                    <span>{{$value->created_at}}</span>
                </label>
            </li>
        @endforeach
    </ul>
</div>