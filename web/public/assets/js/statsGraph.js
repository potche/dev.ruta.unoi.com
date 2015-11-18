/**
 * Created by isra on 9/11/15.
 */
function pieGrl(jsonTotalResponsePie, nameSchool) {
    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });

    var chartGrl = new Highcharts.Chart({
        chart: {
            type: 'pie',
            renderTo: 'container',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        credits: {
            text: 'UNOi',
            href: 'http://mx.unoi.com/'
        },
        title: {
            text: nameSchool
        },
        subtitle: {
            text: 'Porcentaje de Respuestas.'
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
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Respuesta',
            data: jsonTotalResponsePie
            }]
    },
        function (chart) {
            chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
        }
    );
}

//---------------------------------------------
function columnGrl(jsonTotalResponseColumn, nameSchool) {
    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });

    // Create the chart
    var chartColoumn = new Highcharts.Chart({
        chart: {
            type: 'column',
            renderTo: 'containerColumn'
        },
        credits: {
            text: 'UNOi',
            href: 'http://mx.unoi.com/'
        },
        title: {
            text: nameSchool
        },
        subtitle: {
            text: 'Cantidad de Respuestas.'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total de preguntas contestadas'
            }

        },
        legend: {
            enabled: false
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
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
        },

        series: [{
                    name: 'Respuesta',
                    colorByPoint: true,
                    data: jsonTotalResponseColumn
                }]
    },
        function (chart) {
            chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
        }
    );
}

//---------------------------------------
var chart;

function pieGrlLU(jsonTotalResponseLUPie, title) {
    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });

    chart = new Highcharts.Chart({
        chart: {
            type: 'pie',
            renderTo: 'containerPieLU',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        credits: {
            text: 'UNOi',
            href: 'http://mx.unoi.com/'
        },
        title: {
            text: title
        },
        subtitle: {
            text: 'Porcentaje de Respuestas.'
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
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Respuesta',
            data: jsonTotalResponseLUPie
        }]
    },
        function (chart) {
            chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
        }
    );
}

//---------------------------------------------
var chartColoumn;
function columnGrlLU(jsonTotalResponseLUColumn, title) {
    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });

    // Create the chart
    chartColoumn = new Highcharts.Chart({
        chart: {
            type: 'column',
            renderTo: 'containerColumnLU'
        },
        credits: {
            text: 'UNOi',
            href: 'http://mx.unoi.com/'
        },
        title: {
            text: title
        },
        subtitle: {
            text: 'Cantidad de Respuestas.'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total de preguntas contestadas'
            }

        },
        legend: {
            enabled: false
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

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
        },

        series: [{
            name: 'Respuesta',
            colorByPoint: true,
            data: jsonTotalResponseLUColumn
        }]
    },
        function (chart) {
            chart.renderer.image('https://staticmx.unoi.com/global/logos/color_trans_sin.png', 10, 0, 50, 50).add();
        }
    );
}