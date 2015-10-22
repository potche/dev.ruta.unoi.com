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
                }]
    });
}

//---------------------------------------

function pieGrlLU(jsonTotalResponseLUPie) {
    var chart = new Highcharts.Chart({
        chart: {
            type: 'pie',
            renderTo: 'containerPieLU',
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
            data: jsonTotalResponseLUPie
        }]
    });
}

//---------------------------------------
$( "#schoolId" ).change(function() {
    $("#schoolFrm").submit();
});

function statsUser(username, jsonListUser){
    var listUser = JSON.parse(jsonListUser);
    var list = "";
    var si = 0;
    var no = 0;
    var nose = 0;
    $.each(listUser, function(i, item) {
        list += "<li>"+listUser[i].title+"</li>"
        si += listUser[i].si;
        no += listUser[i].no;
        nose += listUser[i].nose;
    });

    //console.log(list);
    var jsonTotalResponseLUPie= "[{name: 'Si', y: "+si+", sliced: true, selected: true},{name: 'No', y: "+no+", sliced: false, selected: false},{name: 'No se', y: "+nose+", sliced: false, selected: false}]";
    console.log(jsonTotalResponseLUPie);

    pieGrlLU(jsonTotalResponseLUPie);

    var divList = "<h1>"+username+"</h1><h5>Evaluaciones Realizadas:</h5><ol>"+list+"<li>Sí: "+si+"</li>"+"</ol>";

    $('#statsUser').modal();
    $('#bodyStatsUser').html(divList);




}