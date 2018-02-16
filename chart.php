<?php

?>
<!DOCTYPE html>
<html>
<head>
    <title>Statistics</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/bootstrap3.3.7.min.css">
    <link href="css/Source+Sans+Pro.css" rel="stylesheet">
    <link rel="stylesheet" href="css/swiper-3.4.2.min.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <script src="https://cdn.hcharts.cn/highcharts/highcharts.js"></script>
</head>
<body>
    <div class="container" style="margin-top:30px;">
        <div id="chart0" style="margin-bottom:20px;"></div>
        <div id="chart" style="margin-bottom:20px;"></div>
        <div class="input-group">
            <input id="target" type="text" name="user" class="form-control" onkeyup="search_user()" autocomplete="off">
            <span class="input-group-btn">
                <button class="btn btn-default" onclick="display_latest()">Latest</button>
            </span>
        </div>
        <div id="result" style="padding-bottom:20px;"></div>
    </div>
    <script src="js/jquery1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function search_user() {
        var url = 'search.php?user='+escape($('#target').val())
        $.ajax({
            url: url,
            success: function(feedback) {
                $('#result').html(feedback)
            },
            error: function() {
                alert('error')
            },
            cache: false
        })
    }
    function display_latest() {
        var url = 'search.php?mode=latest'
        $.ajax({
            url: url,
            // url: 'search.php',
            success: function(feedback) {
                // alert(url)
                $('#result').html(feedback)
            },
            error: function() {
                alert('error')
            },
            cache: false
        })
    }

    Highcharts.setOptions({
        global: {useUTC: false},
        plotOptions: {series: {marker: {enabled: true}}},
        tooltip: {enabled: true}
    });
//1
    var datechart
    $.getJSON('https://class.kyrie.top/search.php?requestType=overall', function(data) {
        datechart = new Highcharts.Chart({
            chart: {
                renderTo: 'chart0',
                defaultSeriesType: 'spline',
            },
            title: {text: 'People: ' + data[0].total + '<br />Downloads: ' + data[0].totaldownloads},
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 1,
                labels:{
                    formatter:function(){
                        var arr = [];
                        data[1].forEach(elem => {
                            arr.push(elem.date)
                        });
                        return arr[this.value];
                    }
                }
            },
            yAxis: [{
                title: {
                    text: 'Daily Downloads',
                    style: {color: '#2b908f',font: '13px sans-serif'}
                },
                min: 0,
                max: 1200,
                plotLines: [{value: 0,width: 1,color: '#808080'}]
            }],//tooltip down here
            series: [{
                name: 'Download People',
                color: '#F8F200',
                data: []
            },{
                name: 'Downloads',
                color: '#353291',
                data: []
            }],
            credits: {
                enabled: false
            }
        });
        data[1].forEach(elem => {
            datechart.series[0].addPoint([elem.date,elem.num]);
            console.log('people: ' + elem.num);
        });
        data[2].forEach(elem => {
            datechart.series[1].addPoint([elem.date,elem.num]);
            console.log('all: ' + elem.num);
        });
    })
//2
    // var chart
    $.getJSON('https://class.kyrie.top/search.php?requestType=total', function(data) {
        datechart = new Highcharts.Chart({
            chart: {
                renderTo: 'chart',
                defaultSeriesType: 'spline',
            },
            title: {text: 'People: ' + data[0].total + '<br />Downloads: ' + data[0].totaldownloads},
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 1,
                labels:{
                    formatter:function(){
                        var arr = [];
                        data[1].forEach(elem => {
                            arr.push(elem.date)
                        });
                        return arr[this.value];
                    }
                }
            },
            yAxis: [{
                title: {
                    text: 'Downloads',
                    style: {color: '#2b908f',font: '13px sans-serif'}
                },
                min: 0,
                max: 7000,
                plotLines: [{value: 0,width: 1,color: '#808080'}]
            }],//tooltip down here
            series: [{
                name: 'Download People',
                color: '#F8F200',
                data: []
            },{
                name: 'Downloads',
                color: '#353291',
                data: []
            }],
            credits: {
                enabled: false
            }
        });
        data[1].forEach(elem => {
            datechart.series[0].addPoint([elem.date,elem.num]);
        });
        data[2].forEach(elem => {
            datechart.series[1].addPoint([elem.date,elem.num]);
        });
    })

    // function requestData() {
    //     var url = 'search.php?requestType=total'
    //     $.ajax({
    //         url: url,
    //         success:function(data) {
    //             var jdata = JSON.parse(data);
    //             jdata[1].forEach(elem => {
    //                 chart.series[0].addPoint([elem.date,elem.num]);
    //             });
    //             jdata[2].forEach(elem => {
    //                 chart.series[1].addPoint([elem.date,elem.num]);
    //             });
    //         },
    //         cache:false
    //     })
    // }
    </script>
</body>
</html>
