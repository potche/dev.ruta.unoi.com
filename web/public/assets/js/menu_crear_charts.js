var Charts = function() {

    return {
        init: function (stats_general) {

            var chart_general = new Highcharts.Chart({
                chart: {
                    type: 'pie',
                    renderTo: 'pie-general'
                },
                colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'],
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
                    data: [{
                        name: "Completado",
                        y: 56.33,
                        drilldown: "Completado"
                    }, {
                        name: "Pendiente",
                        y: 24.03,
                        drilldown: "Pendiente"
                    }, {
                        name: "Sin atender",
                        y: 10.38,
                        drilldown: "Sin atender"
                    }]
                }],
                drilldown: {
                    series: [{
                        name: "Completado",
                        id: "Completado",
                        data: [
                            ["Lista 1", 24.13],
                            ["Lista 2", 17.2],
                            ["Lista 3", 8.11],
                            ["Lista 4", 5.33],
                            ["Lista 5", 1.06],
                            ["Lista 6", 0.5]
                        ]
                    }, {
                        name: "Pendiente",
                        id: "Pendiente",
                        data: [
                            ["Lista 1", 5],
                            ["Lista 2", 4.32],
                            ["Lista 3", 3.68],
                            ["Lista 4", 2.96],
                            ["Lista 5", 2.53],
                            ["Lista 6", 1.45]
                        ]
                    }, {
                        name: "Sin atender",
                        id: "Sin atender",
                        data: [
                            ["Lista 1", 5],
                            ["Lista 2", 4.32],
                            ["Lista 3", 3.68],
                            ["Lista 4", 2.96],
                            ["Lista 5", 2.53],
                            ["Lista 6", 1.45]
                        ]
                    }]
                }
            });
        }
    };
}();