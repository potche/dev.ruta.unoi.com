/**
 * Created by isra on 20/10/15.
 */
function pieGrl(jsonTotalResponsePie) {
    var chart = new Highcharts.Chart({
        chart: {
            type: 'pie',
            renderTo: 'container',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Estadistica General'
        },
        subtitle: {
            text: 'En porcentaje.'
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
    });
}

//---------------------------------------------
function columnGrl(jsonTotalResponseColumn, jsonTotalResponseDDColumn) {
    // Create the chart
    var chartColoumn = new Highcharts.Chart({
        chart: {
            type: 'column',
            renderTo: 'containerColumn'
        },
        title: {
            text: 'Estadistica General'
        },
        subtitle: {
            text: 'Cantidad de Respuestas.'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total percent market share'
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
                    format: '{point.y}'
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
                }],
        drilldown: {
            series: [jsonTotalResponseDDColumn]
            }
    });
}


$( "#schoolId" ).change(function() {
    $("#schoolFrm").submit();
});

function statsUser(username){
    $('#statsUser').modal();
    $('#bodyStatsUser').html('<h1>'+username+'</h1>');
    //console.log(username);
}