$(document).ready(function () {
    //initBackground("dashboard_admin");
    setToolbarTitle("仪表盘");
    selectDate(0);          //用户新增默认今天
    selectIncomeDate(0);    //收入默认今天
    selectActiveDate(0);    //活跃默认今天
});

function initEcharts(echats_id, type, day) {

    var titleText = "";
    var legengText = "";
    if (type == "user") {
        titleText = "用户增长趋势";
        legengText = "新增用户";
    } else if (type == "income") {
        titleText = "收入增长趋势";
        legengText = "新增收入(元)";
    } else if (type == "active") {
        titleText = "活跃增长趋势"
        legengText = "活跃用户"
    }

    var date = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23];
    var value = [
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
        parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000];

    if (day == 7) {
        var curDate = new Date();
        date = [
            new Date(curDate.getTime() - 1 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 2 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 3 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 4 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 5 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 6 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 7 * 24 * 60 * 60 * 1000).toLocaleDateString()
        ];
        value = [
            parseInt(10000 * Math.random()), parseInt(10000 * Math.random()), parseInt(10000 * Math.random()),
            parseInt(10000 * Math.random()), parseInt(10000 * Math.random()), parseInt(10000 * Math.random()),
            parseInt(10000 * Math.random())
        ];
    } else if (day == 30) {
        var curDate = new Date();
        date = [
            new Date(curDate.getTime() - 1 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 2 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 3 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 4 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 5 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 6 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 7 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 8 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 9 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 10 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 11 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 12 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 13 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 14 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 15 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 16 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 17 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 18 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 19 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 20 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 21 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 22 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 23 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 24 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 25 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 26 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 27 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 28 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 29 * 24 * 60 * 60 * 1000).toLocaleDateString(),
            new Date(curDate.getTime() - 30 * 24 * 60 * 60 * 1000).toLocaleDateString()
        ];
        value = [
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000,
            parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000, parseInt(10000 * Math.random()) + 20000
        ];
    }


    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();
    mCharts.hideLoading();

    var timeArray = date;
    var dataArray = value;

    //for (var i = 0; i < result.length; i++) {
    //    timeArray[i] = result[i]['date'];
    //    dataArray[i] = result[i]['value'];
    //}

    option = {
        //title: {
        //    text: titleText
        //},
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: [legengText],
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        axisTick: {
            lineStyle: {
                type: 'dashed'
            }
        },
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: timeArray,
                splitLine: {show: false}
            }
        ],
        yAxis: [
            {
                type: 'value',
                axisLabel: {
                    formatter: function (value, index) {
                        if (value % 1 === 0) {
                            return value;
                        }
                    }
                },
                axisLine: {
                    show: false,
                    lineStyle: {
                        //color: "#000000",
                        opacity: 0,
                        //type: 'dashed'
                    }
                },
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: ["#F9F9F9", "#FEFEFE"]
                    }
                },
                minInterval: 1,
                splitLine: {show: false},
                splitNumber: 6
            }
        ],
        series: [
            {
                name: legengText,
                type: 'line',
                stack: '总量',
                smooth: 'true',
                label: {
                    normal: {
                        show: true,
                        position: 'top'
                    }
                },
                itemStyle: {
                    normal: {
                        color: "#578ebf"
                    }
                },
                lineStyle: {
                    normal: {
                        color: "#578ebf"
                    }
                },
                //areaStyle: {
                //    normal: {
                //        color: "#578ebf"
                //    }
                //},
                data: dataArray
            }
        ]
    };


    mCharts.setOption(option);
    window.onresize = function () { //图表自适应div大小
        mCharts.resize();
    };

}

function selectDate(day) {
    switch (day) {
        case 0:
            $("#todayUserBtn").addClass("active");
            $("#sevenUserBtn").removeClass("active");
            $("#thirtyUserBtn").removeClass("active");
            break;
        case 7:
            $("#todayUserBtn").removeClass("active");
            $("#sevenUserBtn").addClass("active");
            $("#thirtyUserBtn").removeClass("active");
            break;
        case 30:
            $("#todayUserBtn").removeClass("active");
            $("#sevenUserBtn").removeClass("active");
            $("#thirtyUserBtn").addClass("active");
            break;
    }

    initEcharts('echarts_content', 'user', day);
}

function selectActiveDate(day) {
    switch (day) {
        case 0:
            $("#todayActiveBtn").addClass("active");
            $("#sevenActiveBtn").removeClass("active");
            $("#thirtyActiveBtn").removeClass("active");
            break;
        case 7:
            $("#todayActiveBtn").removeClass("active");
            $("#sevenActiveBtn").addClass("active");
            $("#thirtyActiveBtn").removeClass("active");
            break;
        case 30:
            $("#todayActiveBtn").removeClass("active");
            $("#sevenActiveBtn").removeClass("active");
            $("#thirtyActiveBtn").addClass("active");
            break;
    }

    initEcharts('echarts_acitve', 'active', day);
}

function selectIncomeDate(day) {
    switch (day) {
        case 0:
            $("#todayIncomeBtn").addClass("active");
            $("#sevenIncomeBtn").removeClass("active");
            $("#thirtyIncomeBtn").removeClass("active");
            break;
        case 7:
            $("#todayIncomeBtn").removeClass("active");
            $("#sevenIncomeBtn").addClass("active");
            $("#thirtyIncomeBtn").removeClass("active");
            break;
        case 30:
            $("#todayIncomeBtn").removeClass("active");
            $("#sevenIncomeBtn").removeClass("active");
            $("#thirtyIncomeBtn").addClass("active");
            break;
    }

    initEcharts('echarts_income', 'income', day);
}

function toAccount() {
    window.location.href = '/accountmanage';
}