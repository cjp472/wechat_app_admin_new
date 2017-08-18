@extends('helpCenter.commonPage',['tabTitle'=>'freshMan'])
@section('page_css')
@endsection

@section('page_js')
@endsection
@section('mainContent')
    <div class="fHeader">
        <img class="fHeaderImg" src="/images/admin/helpCenter/fresh_man_title.png" alt="">
        <span class="fTitle">新手学习地图</span>
    </div>
    {{--{{dump($freshMan)}}--}}
    <div class="fContent midContent">
        @foreach($freshMan as $key => $value)
            @if($value->document_list)
            <div class="fCatalog">
                <div class="fCatalogTitle">{{$value->title}}</div>
                @foreach($value->document_list as $k  => $v)
                <div class="fPart">
                    <div class="classOrder moreHide">第{{$k+1}}节</div>
                    <div class="fLine">
                        <div class="fLineCircle"></div>
                    </div>
                    <div class="fPartContent">
                        <div class="fPartTitle moreHide">{{$v->name}}</div>
                        {{--<div class="fPartTime">--}}
                            {{--<img class="fPartTimeImg" src="/images/admin/helpCenter/video_length.png" alt="">--}}
                            {{--<span>{{$v->video_length}}</span>--}}
                        {{--</div>--}}
                        <a href="{{"/helpCenter/problem?first_id=".$v->pid."&second_id=".$v->category_id."&document_id=".$v->id}}" target="_blank">
                        <div class="btnMid checkBtn">
                            <div class="checkBtnImg"></div>
                            立即查看
                        </div>
                        </a>
                    </div>
                </div>
                 @endforeach
            </div>
            @endif
            @endforeach
    </div>
@endsection