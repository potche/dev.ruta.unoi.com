var Charts = function() {

    return {
        init: function (stats_pie, stats_bars) {
                var stats_barsA = stats_bars.split('|');
                var chart_bars = new Highcharts.Chart({

                    chart: {
                        type: 'column',
                        renderTo: 'bars'
                    },

                    title: {
                        text: 'Respuestas por categorías'
                    },

                    xAxis: {
                        categories: JSON.parse(stats_barsA[0])
                    },

                    yAxis: {
                        allowDecimals: false,
                        min: 0,
                        title: {
                            text: 'Respuestas'
                        }
                    },

                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b><br/>' +
                                this.series.name + ': ' + this.y + '<br/>' +
                                'Total: ' + this.point.stackTotal;
                        }
                    },

                    plotOptions: {
                        column: {
                            stacking: 'normal'
                        }
                    },

                        series: JSON.parse(stats_barsA[1])
                    }, function (chart) {

                        chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
                    }
                );

            var chart_pie = new Highcharts.Chart({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    renderTo: 'pie',
                    type: 'pie'
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
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: "Porcentaje",
                    colorByPoint: true,
                    data: JSON.parse(stats_pie)
                }]
            }, function (chart) {

                chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
            });
        }
    };
}();