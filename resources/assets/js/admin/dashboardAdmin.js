$(document).ready(function () {

    getInternetExplorerVersion();

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

    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();
    $.get('/getGrowthTrend?type=' + type + '&day=' + day, function (result) {
        mCharts.hideLoading();
        // console.log(result);

        result = JSON.parse(result);

        var timeArray = [];
        var dataArray = [];

        for (var i = 0; i < result.length; i++) {
            timeArray[i] = result[i]['date'];
            if (type == "income") {
                dataArray[i] = (result[i]['value'] * 0.01).toFixed(2);
            } else {
                dataArray[i] = result[i]['value'];
            }
        }

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
                            color: "#000000",
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
    })

}

/**
 * 今日收入新增+昨日收入新增
 * @param echats_id
 * @param type
 * @param day
 */
function initEcharts_compare(echats_id, type, day) {

    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();

    var titleText = "";
    var legengText = [];
    if (type == "user") {
        titleText = "用户增长趋势";
        legengText[0] = "今日新增用户";
        legengText[1] = "昨日新增用户";
    } else if (type == "income") {
        titleText = "收入增长趋势";
        legengText[0] = "今日新增收入(元)";
        legengText[1] = "昨日新增收入(元)";
    } else if (type == "active") {
        titleText = "活跃增长趋势"
        legengText[0] = "今日活跃用户"
        legengText[1] = "昨日活跃用户"
    }

    $.get('/getGrowthTrend?type=' + type + '&day=' + day, function (result) {
        mCharts.hideLoading();
        // console.log(result);

        result = JSON.parse(result);

        var timeSet = [];
        var dataSet = [];

        for (var j = 0; j< result.length; j++) {
            var timeArray = [];
            var dataArray = [];

            for (var i = 0; i < result[j].length; i++) {
                timeArray[i] = result[j][i]['date'];
                if (type == "income") {
                    dataArray[i] = (result[j][i]['value'] * 0.01).toFixed(2);
                } else {
                    dataArray[i] = result[j][i]['value'];
                }
            }
            timeSet[j] = timeArray;
            dataSet[j] = dataArray;
        }



        option = {
            //title: {
            //    text: titleText
            //},
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: [legengText[0], legengText[1]]
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
            //axisTick: {
            //    lineStyle: {
            //        type: 'dashed'
            //    }
            //},
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    data: timeSet[0],
                    splitLine: {show: false}
                },
                //{
                //    type: 'category',
                //    boundaryGap: false,
                //    data: timeSet[1],
                //    splitLine: {show: false}
                //}
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
                            color: "#000000",
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
                    name: legengText[0],
                    type: 'line',
                    stack: '',
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
                    data: dataSet[0]
                },
                {
                    name: legengText[1],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    data: dataSet[1]
                }
            ]
        };


        mCharts.setOption(option);
        window.onresize = function () { //图表自适应div大小
            mCharts.resize();
        };
    })

}

/**
 * 今日用户新增+昨日用户新增+今日付费用户+昨日付费用户
 * @param echats_id
 * @param type
 * @param day
 */
function initEcharts_compare_pay(echats_id, type, day) {

    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();

    var titleText = "";
    var legengText = [];
    if (type == "user") {
        titleText = "用户增长趋势";
        legengText[0] = "今日新增用户";
        legengText[1] = "昨日新增用户";
        legengText[2] = "今日付费用户";
        legengText[3] = "昨日付费用户";
    } else if (type == "income") {
        titleText = "收入增长趋势";
        legengText[0] = "今日新增收入(元)";
        legengText[1] = "昨日新增收入(元)";
    } else if (type == "active") {
        titleText = "活跃增长趋势";
        legengText[0] = "今日活跃用户";
        legengText[1] = "昨日活跃用户";
    }

    $.get('/getGrowthTrend?type=' + type + '&day=' + day, function (result) {
        mCharts.hideLoading();
        // console.log(result);

        result = JSON.parse(result);

        var timeSet = [];
        var dataSet = [];

        for (var j = 0; j< result.length; j++) {
            var timeArray = [];
            var dataArray = [];

            for (var i = 0; i < result[j].length; i++) {
                timeArray[i] = result[j][i]['date'];
                if (type == "income") {
                    dataArray[i] = (result[j][i]['value'] * 0.01).toFixed(2);
                } else {
                    dataArray[i] = result[j][i]['value'];
                }
            }
            timeSet[j] = timeArray;
            dataSet[j] = dataArray;
        }



        option = {
            //title: {
            //    text: titleText
            //},
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: [legengText[0], legengText[1], legengText[2], legengText[3]],
                selected: {
                    '今日新增用户': false,
                    '昨日新增用户': false,
                    '今日付费用户': true,
                    '昨日付费用户': true
                }
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
            //axisTick: {
            //    lineStyle: {
            //        type: 'dashed'
            //    }
            //},
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    data: timeSet[0],
                    splitLine: {show: false}
                },
                //{
                //    type: 'category',
                //    boundaryGap: false,
                //    data: timeSet[1],
                //    splitLine: {show: false}
                //}
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
                            color: "#000000",
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
                    name: legengText[0],
                    type: 'line',
                    stack: '',
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
                    data: dataSet[0]
                },
                {
                    name: legengText[1],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    data: dataSet[1]
                },
                {
                    name: legengText[2],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#078c32"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#078c32"
                        }
                    },
                    data: dataSet[2]
                },
                {
                    name: legengText[3],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#0dfb5a"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#0dfb5a"
                        }
                    },
                    data: dataSet[3]
                }
            ]
        };


        mCharts.setOption(option);
        window.onresize = function () { //图表自适应div大小
            mCharts.resize();
        };
    })

}
//新增用户 + 付费用户 日线
function initEcharts_pay(echats_id, type, day) {

    var titleText = "";
    var legengText = [];
    if (type == "user") {
        titleText = "用户增长趋势";
        legengText[0] = "新增用户";
        legengText[1] = "付费用户";
    } else if (type == "income") {
        titleText = "收入增长趋势";
        legengText = "新增收入(元)";
    } else if (type == "active") {
        titleText = "活跃增长趋势"
        legengText = "活跃用户"
    }

    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();
    $.get('/getGrowthTrend?type=' + type + '&day=' + day, function (result) {
        mCharts.hideLoading();
         //console.log(result);

        result = JSON.parse(result);

        var timeSet = [];
        var dataSet = [];

        for (var j = 0; j< result.length; j++) {
            var timeArray = [];
            var dataArray = [];

            for (var i = 0; i < result[j].length; i++) {
                timeArray[i] = result[j][i]['date'];
                if (type == "income") {
                    dataArray[i] = (result[j][i]['value'] * 0.01).toFixed(2);
                } else {
                    dataArray[i] = result[j][i]['value'];
                }
            }
            timeSet[j] = timeArray;
            dataSet[j] = dataArray;
        }

        option = {
            //title: {
            //    text: titleText
            //},
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: [legengText[0], legengText[1]],
                selected: {
                    '新增用户': false,
                    '付费用户': true
                }
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
            //axisTick: {
            //    lineStyle: {
            //        type: 'dashed'
            //    }
            //},
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
                            color: "#000000",
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
                    name: legengText[0],
                    type: 'line',
                    stack: '',
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
                    data: dataSet[0]
                },
                {
                    name: legengText[1],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    data: dataSet[1]
                },
            ]
        };


        mCharts.setOption(option);
        window.onresize = function () { //图表自适应div大小
            mCharts.resize();
        };
    })

}

//活跃用户 + 活跃付费用户 日线
function initEcharts_active(echats_id, type, day) {

    var titleText = "";
    var legengText = [];
    if (type == "user") {
        titleText = "用户增长趋势";
        legengText[0] = "新增用户";
        legengText[1] = "付费用户";
    } else if (type == "income") {
        titleText = "收入增长趋势";
        legengText[0] = "新增收入(元)";
    } else if (type == "active") {
        titleText = "活跃增长趋势"
        legengText[0] = "活跃用户"
        legengText[1] = "活跃付费用户"
    }

    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();
    $.get('/getGrowthTrend?type=' + type + '&day=' + day, function (result) {
        mCharts.hideLoading();
         //console.log(result);

        result = JSON.parse(result);

        var timeSet = [];
        var dataSet = [];

        for (var j = 0; j< result.length; j++) {
            var timeArray = [];
            var dataArray = [];

            for (var i = 0; i < result[j].length; i++) {
                timeArray[i] = result[j][i]['date'];
                if (type == "income") {
                    dataArray[i] = (result[j][i]['value'] * 0.01).toFixed(2);
                } else {
                    dataArray[i] = result[j][i]['value'];
                }
            }
            timeSet[j] = timeArray;
            dataSet[j] = dataArray;
        }

        option = {
            //title: {
            //    text: titleText
            //},
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: [legengText[0], legengText[1]],
                selected: {
                    '活跃用户': false,
                    '活跃付费用户': true
                }
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
            //axisTick: {
            //    lineStyle: {
            //        type: 'dashed'
            //    }
            //},
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
                            color: "#000000",
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
                    name: legengText[0],
                    type: 'line',
                    stack: '',
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
                    data: dataSet[0]
                },
                {
                    name: legengText[1],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    data: dataSet[1]
                },
            ]
        };


        mCharts.setOption(option);
        window.onresize = function () { //图表自适应div大小
            mCharts.resize();
        };
    })

}


/**
 * 今日活跃用户+昨日活跃用户+今日付费活跃用户+昨日付费活跃用户
 * @param echats_id
 * @param type
 * @param day
 */
function initEcharts_compare_active(echats_id, type, day) {

    var mCharts = echarts.init(document.getElementById(echats_id));
    mCharts.showLoading();

    var titleText = "";
    var legengText = [];
    if (type == "user") {
        titleText = "用户增长趋势";
        legengText[0] = "今日新增用户";
        legengText[1] = "昨日新增用户";
    } else if (type == "income") {
        titleText = "收入增长趋势";
        legengText[0] = "今日新增收入(元)";
        legengText[1] = "昨日新增收入(元)";
    } else if (type == "active") {
        titleText = "活跃增长趋势";
        legengText[0] = "今日活跃用户";
        legengText[1] = "昨日活跃用户";
        legengText[2] = "今日活跃付费用户";
        legengText[3] = "昨日活跃付费用户";
    }

    $.get('/getGrowthTrend?type=' + type + '&day=' + day, function (result) {
        mCharts.hideLoading();
        // console.log(result);

        result = JSON.parse(result);

        var timeSet = [];
        var dataSet = [];

        for (var j = 0; j< result.length; j++) {
            var timeArray = [];
            var dataArray = [];

            for (var i = 0; i < result[j].length; i++) {
                timeArray[i] = result[j][i]['date'];
                if (type == "income") {
                    dataArray[i] = (result[j][i]['value'] * 0.01).toFixed(2);
                } else {
                    dataArray[i] = result[j][i]['value'];
                }
            }
            timeSet[j] = timeArray;
            dataSet[j] = dataArray;
        }



        option = {
            //title: {
            //    text: titleText
            //},
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: [legengText[0], legengText[1], legengText[2], legengText[3]],
                selected: {
                    '今日活跃用户': false,
                    '昨日活跃用户': false,
                    '今日活跃付费用户': true,
                    '昨日活跃付费用户': true
                }
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
            //axisTick: {
            //    lineStyle: {
            //        type: 'dashed'
            //    }
            //},
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    data: timeSet[0],
                    splitLine: {show: false}
                },
                //{
                //    type: 'category',
                //    boundaryGap: false,
                //    data: timeSet[1],
                //    splitLine: {show: false}
                //}
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
                            color: "#000000",
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
                    name: legengText[0],
                    type: 'line',
                    stack: '',
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
                    data: dataSet[0]
                },
                {
                    name: legengText[1],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#37B8AE"
                        }
                    },
                    data: dataSet[1]
                },
                {
                    name: legengText[2],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#078c32"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#078c32"
                        }
                    },
                    data: dataSet[2]
                },
                {
                    name: legengText[3],
                    type: 'line',
                    stack: '',
                    smooth: 'true',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: "#0dfb5a"
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: "#0dfb5a"
                        }
                    },
                    data: dataSet[3]
                }
            ]
        };


        mCharts.setOption(option);
        window.onresize = function () { //图表自适应div大小
            mCharts.resize();
        };
    })

}

//用户新增 + 付费用户
function selectDate(day) {
    switch (day) {
        case 0:
            $("#todayUserBtn").addClass("active");
            $("#yesterdayUserBtn").removeClass("active");
            $("#sevenUserBtn").removeClass("active");
            $("#thirtyUserBtn").removeClass("active");
            break;
        case 1:
            $("#todayUserBtn").removeClass("active");
            $("#yesterdayUserBtn").addClass("active");
            $("#sevenUserBtn").removeClass("active");
            $("#thirtyUserBtn").removeClass("active");
            break;
        case 7:
            $("#todayUserBtn").removeClass("active");
            $("#yesterdayUserBtn").removeClass("active");
            $("#sevenUserBtn").addClass("active");
            $("#thirtyUserBtn").removeClass("active");
            break;
        case 30:
            $("#todayUserBtn").removeClass("active");
            $("#yesterdayUserBtn").removeClass("active");
            $("#sevenUserBtn").removeClass("active");
            $("#thirtyUserBtn").addClass("active");
            break;
    }

    if (day == 0 || day == 1) {
        initEcharts_compare_pay('echarts_content', 'user', 0);
        //initEcharts_compare('echarts_content', 'user', 0);
        //initEcharts_compare('echarts_content', 'user', 1);
    } else {
        initEcharts_pay('echarts_content', 'user', day);
    }
}

//活跃用户 + 付费活跃用户
function selectActiveDate(day) {
    switch (day) {
        case 0:
            $("#todayActiveBtn").addClass("active");
            $("#yesterdayActiveBtn").removeClass("active");
            $("#sevenActiveBtn").removeClass("active");
            $("#thirtyActiveBtn").removeClass("active");
            break;
        case 1:
            $("#todayActiveBtn").removeClass("active");
            $("#yesterdayActiveBtn").addClass("active");
            $("#sevenActiveBtn").removeClass("active");
            $("#thirtyActiveBtn").removeClass("active");
            break;
        case 7:
            $("#todayActiveBtn").removeClass("active");
            $("#yesterdayActiveBtn").removeClass("active");
            $("#sevenActiveBtn").addClass("active");
            $("#thirtyActiveBtn").removeClass("active");
            break;
        case 30:
            $("#todayActiveBtn").removeClass("active");
            $("#yesterdayActiveBtn").removeClass("active");
            $("#sevenActiveBtn").removeClass("active");
            $("#thirtyActiveBtn").addClass("active");
            break;
    }

    if (day == 0 || day == 1) {
        initEcharts_compare_active('echarts_acitve', 'active', day);
    } else {
        initEcharts_active('echarts_acitve', 'active', day);
    }
}

//收入新增
function selectIncomeDate(day) {
    switch (day) {
        case 0:
            $("#todayIncomeBtn").addClass("active");
            $("#yesterdayIncomeBtn").removeClass("active");
            $("#sevenIncomeBtn").removeClass("active");
            $("#thirtyIncomeBtn").removeClass("active");
            break;
        case 1:
            $("#todayIncomeBtn").removeClass("active");
            $("#yesterdayIncomeBtn").addClass("active");
            $("#sevenIncomeBtn").removeClass("active");
            $("#thirtyIncomeBtn").removeClass("active");
            break;
        case 7:
            $("#todayIncomeBtn").removeClass("active");
            $("#yesterdayIncomeBtn").removeClass("active");
            $("#sevenIncomeBtn").addClass("active");
            $("#thirtyIncomeBtn").removeClass("active");
            break;
        case 30:
            $("#todayIncomeBtn").removeClass("active");
            $("#yesterdayIncomeBtn").removeClass("active");
            $("#sevenIncomeBtn").removeClass("active");
            $("#thirtyIncomeBtn").addClass("active");
            break;
    }

    if (day == 0 || day == 1) {
        initEcharts_compare('echarts_income', 'income', day);
    } else {
        initEcharts('echarts_income', 'income', day);
    }
}