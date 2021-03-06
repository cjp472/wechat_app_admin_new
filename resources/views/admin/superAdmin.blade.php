<!doctype html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    <link type=text/css rel="stylesheet" href="../css/admin/base.css?{{env('timestamp')}}">
    <link type=text/css rel="stylesheet" href="../css/external/bootstrap.min.css">
    <link rel="stylesheet" href="../css/external/jquery-alert.css?{{env('timestamp')}}" />
    <script src="../js/external/jquery1.12.4.min.js"></script>
    <script src="../js/external/jquery-alert.js"></script>
    <link type=text/css rel="stylesheet" href="../css/admin/base.css?{{env('timestamp')}}">           {{--base.css--}}

    <style>
        .more_btn {
            float: left;
            cursor: pointer;
            position:relative;
            margin-top:5px;
        }
        .more_btn:hover .more_operate{
            visibility: visible;
            opacity: 1;
        }

        /*更多操作*/
        .more_operate {
            /*position: absolute;*/
            width: 54px;
            padding-top: 10px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            background-color: #ffffff;
            border: solid 1px #deeeff;
            z-index: 100;
            position:absolute;
            visibility: hidden;
            transition-duration: 0.2s;
            transition-property: visibility,opacity;
            line-height:26px;
            top:20px;
            left:0;
            opacity: 0;
        }

        .more_operate div {
            height: 17px;
            line-height: 17px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 12px;
            color: #2a75ed;
            cursor: pointer;
        }

        .title_long {
            width: 150px;
        }
    </style>
</head>
<body>

<div class="base_content" style="top: 0;">
    <div class="base_mainContent">
        {{--顶部小蓝色框提示--}}
        <div class="blue_window_prompt" style="display: none;">
            <div class="blue_window_content"></div>
        </div>
        {{--顶部小红色框提示--}}
        <div class="red_window_prompt" style="display: none;">
            <div class="red_window_content"></div>
        </div>
<div style="padding-bottom: 50px;height: 50px; padding-top: 20px;">
    <form action="to_super_page" method="get">
        <div class="pull-right" style="padding-right: 20px;">
            <button class="btn btn-default" type="submit" style="margin-bottom: 3px"
                    id="pay_search_btn">搜索
            </button>
        </div>
        <div style="float: right; padding-left: 20px;">
            <input type="text" name="content" class="form-control" aria-label="..."
                   id="content"
                   @if(array_key_exists('content',$search_array))value="{{$search_array['content']}}"@else value=""@endif>
        </div>
        {{--<div class="searchSelectArea" style="float: right; width: 95px;">--}}
            {{--<select name="search_type" class="form-control" id="search_type">--}}

                {{--<option value="name" @if($search_array['search_type'] == 'name') selected='true'; @endif>name</option>--}}
                {{--<option @if($search_array['search_type'] == 'nick_name') selected='true'; @endif value="nick_name">nick_name</option>--}}
                {{--<option @if($search_array['search_type'] == 'phone') selected='true'; @endif value="phone">phone</option>--}}
                {{--<option @if($search_array['search_type'] == 'app_id')  selected='true'; @endif value="app_id">app_id</option>--}}
                {{--<option @if($search_array['search_type'] == 'wx_app_id')  selected='true'; @endif value="wx_app_id">wx_app_id</option>--}}
                {{--<option @if($search_array['search_type'] == 'wx_app_name')  selected='true'; @endif value="wx_app_name">wx_app_name</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    </form>
</div>

        {{--table区--}}
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="th_left title_long">name</th>
                <th class="title_long">nick_name</th>
                <th>phone</th>
                <th>app_id</th>
                <th>wx_app_id</th>
                <th class="title_long">wx_app_name</th>
                <th>https</th>
                <th class="title_long">created_at</th>
                <th>Count</th>
                <th>income(¥)</th>
                <th class="title_long">login</th>
            </tr>
            </thead>
            <tbody>
            @foreach($paginator as $v)
                <tr>
{{--                    <td class="td_left">{{$user->openid}}</td>--}}
                    <td class="td_left title_long">{{$v->name}}</td>
                    <td class="title_long">{{$v->nick_name}}</td>
                    <td >{{$v->phone}}</td>
                    <td >{{$v->app_id}}</td>
                    <td >{{$v->wx_app_id}}</td>
                    <td class="title_long">{{$v->wx_app_name}}</td>
                    <td >{{$v->isNewer == 1 ? 'http' : 'https'}}</td>
                    <td class="title_long">{{$v->created_at}}</td>
                    <td >{{number_format($v->sum_count)}}</td>
                    <td >{{number_format($v->sum_income * 0.01)}}</td>

                    <td class="title_long">
                        <a class="btn btn-default" style="float: left" target="_blank"
                           href="{{ '/super_login?openid='.$v->openid }}">login
                        </a>
                        <div class="more_operate_wrapper" style="float: left">
                            <div class="more_btn">&nbsp;&nbsp;更多&nbsp;&nbsp;


                            <div class="more_operate">

                                @if($v->isNewer == 1)
                                    <div class="set_https_btn" onclick="setAppHttps('{{$v->app_id}}')">setHttps</div>
                                @endif

                                <div class="set_close_sq" onclick="setClose('{{$v->app_id}}', '_sq', '{{$v->openid}}')">setClose</div>
                                <script>

                                        /**
                                         * 新建公共方法入口
                                         */
                                        var baseUtils = {

                                            /**
                                             * 显示模块
                                             */
                                            show: {
                                                //  蓝色顶部提示条
                                                blueTip: function (content) {
                                                    $('.blue_window_content').html(content);
                                                    $(".blue_window_prompt").fadeIn(300);
                                                    setTimeout(function () {
                                                        $(".blue_window_prompt").fadeOut(300);

                                                    }, 2000);
                                                },

                                                //  红色顶部提示条
                                                redTip: function (content) {
                                                    $('.red_window_content').html(content);
                                                    $(".red_window_prompt").fadeIn(300);
                                                    setTimeout(function () {
                                                        $(".red_window_prompt").fadeOut(300);

                                                    }, 2000);
                                                },

                                            },

                                            /**
                                             * 其它模块
                                             */

                                        };

                                        function setAppHttps(app_id) {
                                            $.alert('确认是否要切换为https吗?', "info", {
                                                onOk: function () {
                                                    var allParams = {app_id:app_id};
                                                    $.post('/set_app_https', allParams, function (result) {

                                                        if (result.code == 0) {
                                                            baseUtils.show.blueTip("设置https成功！");
                                                            setTimeout(function () {
                                                                window.location.reload();
                                                            }, 500);
                                                        } else {
                                                            console.log(result.msg);
                                                            baseUtils.show.redTip(result.msg);
                                                        }
                                                    });

                                                }
                                            });

                                        };

                                    function setClose(app_id, type, openid) {
                                        $.alert('请谨慎操作,确认是否要封号吗?', "info", {
                                            onOk: function () {
                                                var allParams = {
                                                    app_id:app_id,
                                                    openid:openid,
                                                    type:type
                                                };
                                                $.post('/set_close', allParams, function (result) {

                                                    if (result.code == 0) {
                                                        baseUtils.show.blueTip("封号成功！");
                                                        setTimeout(function () {
                                                            window.location.reload();
                                                        }, 500);
                                                    } else {
                                                        console.log(result.msg);
                                                        baseUtils.show.redTip(result.msg);
                                                    }
                                                });

                                            }
                                        });
                                    }

                                    </script>

                            </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>


        </table>
        <div class="list-page">
            {{--@if(empty($search_array))--}}
                {{--{!! $paginator->render() !!}--}}
            {{--@else--}}
                {!! $paginator->appends($search_array)->render() !!}
            {{--@endif--}}
        </div>
    </div>
</div>

</body>

</html>
