{{--仪表盘特殊情况,只有一个账号管理菜单--}}
<!doctype html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>鹅晓</title>
    <link type=text/css rel="stylesheet" href="http://admin.inside.xiaoe-tech.com/css/external/bootstrap.min.css">
    <link type=text/css rel="stylesheet" href="http://admin.inside.xiaoe-tech.com/css/admin/base.css?{{env('timestamp')}}">
    <script src="http://admin.inside.xiaoe-tech.com/js/external/jquery.js"></script>
    <script type="text/javascript" src="http://admin.inside.xiaoe-tech.com/js/external/bootstrap.min.js"></script>
    <script type="text/javascript" src="http://admin.inside.xiaoe-tech.com/js/admin/base.js?{{env('timestamp')}}"></script>
    <link type=text/css rel="stylesheet" href="../css/admin/accountManage.css?{{env('timestamp')}}">
    <link rel="stylesheet" type="text/css" href="../css/external/xcConfirm.css?{{env('timestamp')}}">
    <script src="../js/external/xcConfirm.js"></script>
    {{--<script type="text/javascript" src="../js/admin/accountManage.js"></script>--}}
    <link type=text/css rel="stylesheet" href="../css/admin/dashboard.css?{{env('timestamp')}}">
    <script type="text/javascript" src="../js/external/echarts.js?{{env('timestamp')}}"></script>
    <script type="text/javascript" src="../js/admin/falseDashboard.js"></script>
</head>
<body>

<div class="base_slide">
    <div class="base_logo_div">
        <a href="/dash">
            <img src="../images/logo-white.png"/>
        </a>
    </div>
    <div class="base_menu">
        <div class="base_menu_sub" onclick="toAccount()">
            <span>账户管理</span>
        </div>
    </div>
</div>

<div class="base_right">
    <div class="base_topBar">
        <div class="base_exit" onclick="exitLogin()">
            <img src="../images/exit.png" style="margin-top: 16px;float:left;"/>
            <span>退出</span>
        </div>
    </div>
    <div class="base_content">
        <div class="base_mainContent">
            <div style="width: 100%;">
                <div style="text-align: center;">

                    <div style="display: block;margin: 20px 20px 20px 20px;">

                        <table cellspacing="0" cellpadding="0" class="list" id="IndexPreviewTableList">
                            <tbody>
                            <tr class="title">
                                <th></th>
                                <th>新增用户</th>
                                <th>活跃用户</th>
                                <th>总用户</th>
                                <th>新增收入(元)</th>
                                <th>总收入(元)</th>
                            </tr>
                            <tr class="highlight">
                                <td class="normal">今日</td>
                                <td class="">18000</td>
                                <td class="">28000</td>
                                <td class="">8000000</td>
                                <td class="">18000</td>
                                <td class="">8888888</td>
                            </tr>
                            <tr><td class="normal">昨日</td>
                                <td class="">18000</td>
                                <td class="">28000</td>
                                <td class="">8000000</td>
                                <td class="">18000</td>
                                <td class="">8888888</td>
                            </tr>
                            <tr class="normal">
                                <td class="normal">7天前</td>
                                <td class="">18000</td>
                                <td class="">28000</td>
                                <td class="">8000000</td>
                                <td class="">18000</td>
                                <td class="">8888888</td>
                            </tr>
                            <tr class="last"><td class="normal">30天前</td>
                                <td class="">18000</td>
                                <td class="">28000</td>
                                <td class="">8000000</td>
                                <td class="">18000</td>
                                <td class="">8888888</td>
                            </tr>
                            </tbody></table>

                    </div>
                    <div style="clear: both"></div>
                </div>



            </div>

            <div style="background: #F9F9F9;height: 15px;width: 102%;align-self: center;margin-left: -10px"></div>

            <div style="width: 100%;">
                <div style="text-align: center;">
                    <div style="display: inline-block;margin-top: 10px">
                        <span style="font-size: 20px;margin-left: 150px">用户增长趋势分析</span>
                    </div>
                    <div style="display: inline-block;float: right;margin-right: 20px;margin-top: 20px">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default active" id="todayUserBtn" onclick="selectDate(0)">今天</button>
                            <button type="button" class="btn btn-default" id="sevenUserBtn" onclick="selectDate(7)">7天</button>
                            <button type="button" class="btn btn-default" id="thirtyUserBtn" onclick="selectDate(30)">30天</button>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div id="echarts_content" class="echats_class">

                </div>

            </div>

            <div style="background: #F9F9F9;height: 15px;width: 102%;align-self: center;margin-left: -10px"></div>

            <div style="width: 100%;">
                <div style="text-align: center;">
                    <div style="display: inline-block;margin-top: 10px">
                        <span style="font-size: 20px;margin-left: 150px">用户活跃趋势分析</span>
                    </div>
                    <div style="display: inline-block;float: right;margin-right: 20px;margin-top: 20px">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default active" id="todayActiveBtn" onclick="selectActiveDate(0)">今天</button>
                            <button type="button" class="btn btn-default" id="sevenActiveBtn" onclick="selectActiveDate(7)">7天</button>
                            <button type="button" class="btn btn-default" id="thirtyActiveBtn" onclick="selectActiveDate(30)">30天</button>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div id="echarts_acitve" class="echats_class">

                </div>

            </div>

            <div style="background: #F9F9F9;height: 15px;width: 102%;align-self: center;margin-left: -10px"></div>

            <div style="width: 100%;">
                <div style="text-align: center;">
                    <div style="display: inline-block;margin-top: 10px">
                        <span style="font-size: 20px;margin-left: 150px">收入增长趋势分析</span>
                    </div>
                    <div style="display: inline-block;float: right;margin-right: 20px;margin-top: 20px">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default active" id="todayIncomeBtn" onclick="selectIncomeDate(0)">今天</button>
                            <button type="button" class="btn btn-default" id="sevenIncomeBtn" onclick="selectIncomeDate(7)">7天</button>
                            <button type="button" class="btn btn-default" id="thirtyIncomeBtn" onclick="selectIncomeDate(30)">30天</button>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div id="echarts_income" class="echats_class">

                </div>

            </div>
        </div>
    </div>
</div>

<div id="base_loading">
    <img id="login_progressImage" src="../images/ajax-loader.gif"/>
</div>


</body>

</html>

