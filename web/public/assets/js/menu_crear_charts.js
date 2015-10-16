var Charts = function() {

    return {
        init: function (stats_bars) {

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
                    data: [1, 3, 4, 7, 2],
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
        }
    };
}();