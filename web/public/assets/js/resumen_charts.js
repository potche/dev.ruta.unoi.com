var chart_bars;
var chart_pie;
var Charts = function() {

    return {
        init: function (stats_pie, stats_bars) {

            var stats = JSON.parse(stats_bars);

                chart_bars = new Highcharts.Chart({

                    chart: {
                        type: 'column',
                        renderTo: 'bars'
                    },
                    credits: {
                        text: 'UNOi',
                        href: 'http://mx.unoi.com/'
                    },
                    title: {
                        text: 'Respuestas por categorías'
                    },

                    xAxis: {
                        type: 'category',
                        categories: stats.categories
                    },

                    yAxis: {
                        allowDecimals: false,
                        //min: 0,
                        title: {
                            text: 'Respuestas'
                        }
                    },

                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b><br/>' +
                                this.series.name + ': ' + this.y + '<br/>';
                        }
                    },

                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:,.0f}'
                            }
                        }
                    },
                    series: stats.series
                    }, function (chart) {

                        chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
                        chart.reflow();
                    }
                );

            chart_pie = new Highcharts.Chart({
                chart: {
                    //plotBackgroundColor: null,
                    //plotBorderWidth: null,
                    //plotShadow: false,
                    renderTo: 'pie',
                    type: 'pie'
                },
                credits: {
                    text: 'UNOi',
                    href: 'http://mx.unoi.com/'
                },
                title: {
                    text: 'Respuestas'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'//,
                            //style: {
                              //  color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            //}
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: "Porcentaje",
                    colorByPoint: true,
                    data: JSON.parse(stats_pie)
                }]
            }, function (chart) {

                chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
                chart.reflow();
            });
        }
    };
}();