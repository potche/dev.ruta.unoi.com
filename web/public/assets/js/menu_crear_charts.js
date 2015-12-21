var chart_general;
var Charts = function() {

    return {
        init: function (stats_general) {

            chart_general = new Highcharts.Chart({
                chart: {
                    type: 'pie',
                    renderTo: 'pie-general'
                },
                colors: ['#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'],
                title: {
                    text: 'Avance global en evaluaciones existentes'
                },
                subtitle: {
                    text: 'Seleccionar una sección para ver detalle'
                },
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.y:.1f}%'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
                },
                series: [{
                    name: "Porcentaje",
                    colorByPoint: true,
                    data: JSON.parse(stats_general)
                }]
            }, function (chart) {

                chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
                chart.reflow();
            });
        }
    };
}();