@extends('helpCenter.commonPage',['tabTitle'=>'index'])
@section('page_css')
@endsection

@section('page_js')
@endsection
@section('mainContent')
    @if(count($usual_document)!=0)
        <div class="iPart">
            <div class="iPartTitle">
                新手上路
                <a class="iGetMore" target="_blank" href="/helpCenter/problem?first_id&second_id&document_id=">更多</a>
            </div>
            @foreach($usual_document as $key => $value)
                <a href="{{"/helpCenter/problem?first_id=".$value->first_id."&second_id=".$value->category_id."&document_id=".$value->id}}" target="_blank">
                    <div class="commonProblemPart">
                        <img class="cpPartIcon" src="{{$value->img_url}}" alt="">
                        <div class="cpPartContent">
                            <div class="cpPartTitle">{{$value->name}}</div>
                            <div class="cpPartIntro">{{$value->summary}}</div>
                        </div>
                        <img class="cpPartArrow" src="/images/admin/helpCenter/icon_arrow.png" alt="">
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if(count($nav_index)!=0)
        <div class="iPart">
            <div class="iPartTitle">
                功能导航
                <a class="iGetMore" target="_blank" href="/helpCenter/problem?first_id&second_id&document_id=">更多</a>
            </div>
            @foreach($nav_index as $key => $value)
                <a href="{{"/helpCenter/problem?first_id=".$value->id."&second_id=&document_id="}}" target="_blank">
                    <div class="functionGuidePart">
                        <img class="fgPartIcon" src="{{$value->img_url}}" alt="">
                        <div class="fgPartTitle">{{$value->title}}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if(count($hot_index)!=0)
        <div class="iPart">
            <div class="iPartTitle">
                热门专题
                <a class="iGetMore" target="_blank" href="/helpCenter/problem?first_id&second_id&document_id=">更多</a>
            </div>
            @foreach($hot_index as $key => $value)
                <a href="{{"/helpCenter/problem?first_id=".$value->pid."&second_id=".$value->id."&document_id="}}" target="_blank">
                    <div class="hotTopicPart">
                        <img class="htPartIcon" src="{{$value->img_url}}" alt="">
                        <div class="htPartTitle">{{$value->title}}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection