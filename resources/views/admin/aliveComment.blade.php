<?php
$pageData = [];
$pageData['sideActive'] = 'content_comment';
$pageData['barTitle'] = '社群运营';
?>
@extends('admin.baseLayout',$pageData)


@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/aliveComment.css?{{env('timestamp')}}" />
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/aliveComment.js?{{env('timestamp')}}"></script>
    <script>
        $(document).ready(function () {
            var reurlinfo = GetQueryString('reurl');
            if(reurlinfo.indexOf('alive')>0){
                setTopUrlInfo('alive_listop');
            }

        });
    </script>
@endsection


@section('base_mainContent')
    {{--搜索框--}}
    <div class="searchArea">
        <div class="searchButtonArea">
            <button class="btn btn-default" id="searchButton">搜索</button>
        </div>

        <div class="searchInputArea">
            <input id="search" type="text" class="form-control" aria-label="..." />
        </div>

        <div class="searchSelectArea">
            <select class="form-control" id="ruler" >
                <option selected value="0">评论内容</option>
                <option value="1">创建时间</option>
                <option value="2">评论人</option>
            </select>
        </div>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>身份</th>
                <th>内容</th>
                <th>状态</th>
                <th>发表时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
            <tr>
                <td><img src="{{$value['wx_avatar']}}" style="cursor:pointer;"
                 onclick="jumpDetail('{{session('app_id')}}','{{$value['user_id']}}')"/></td>
                <td>{{$value['wx_nickname']}}</td>
                <td>{{$value['user_type']}}</td>
                <td class="content_long">{{$value['msg_content']}}</td>

                @if($value['msg_state']==0)
                <td>显示</td>
                @else
                <td>隐藏</td>
                @endif

                <td>{{$value['created_at']}}</td>

                @if($value['msg_state']==0)
                <td>
                    <button class="btn btn-default" onclick="changeAliveComment('{{$value['id']}}',1)">隐藏</button>
                </td>
                @else
                <td>
                    <button class="btn btn-default" onclick="changeAliveComment('{{$value['id']}}',0)">显示</button>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>

    </table>
    @if(count($data)==0)
        <p style="text-align: center;">暂无数据</p>
    @endif
    {{--页标--}}
    <div class="list-page">
        @if(empty($search))
            <?php echo $allInfo->appends(['alive_id'=>$aliveId,'ruler' => $ruler,'reurl'=>$reurl])->render(); ?>
        @else
            <?php echo $allInfo->appends(['alive_id'=>$aliveId,'ruler' => $ruler, 'search'=> $search, 'reurl'=>$reurl])->render(); ?>
        @endif
    </div>
@stop

