var Charts = function() {

    return {
        init: function (stats_pie, stats_bars) {

                var chart_bars = new Highcharts.Chart({

                    chart: {
                        type: 'column',
                        renderTo: 'bars'
                    },

                    title: {
                        text: 'Respuestas por categorías'
                    },

                    xAxis: {
                        categories: ['Categoría 1', 'Categoría 2', 'Categoría 3', 'Categoría 4', 'Categoría 5']
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

                    series: [{
                        name: 'Sí',
                        data: [5, 3, 4, 7, 2],
                        stack: 'good'
                    }, {
                        name: 'No',
                        data: [3, 4, 4, 2, 5],
                        stack: 'bad'
                    }, {
                        name: 'No sé',
                        data: [2, 5, 6, 2, 1],
                        stack: 'bad'
                    }]
                });

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
                    name: "Procentaje",
                    colorByPoint: true,
                    data: [{
                        name: "Si",
                        y: 56.33
                    }, {
                        name: "No",
                        y: 24.03,
                        sliced: true,
                        selected: true
                    }, {
                        name: "No Sé",
                        y: 10.38
                    }]
                }]
            });
        }
    };
}();