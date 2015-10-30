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
            margin: [0, 0, 0, 0],
            spacingTop: 0,
            spacingBottom: 0,
            spacingLeft: 0,
            spacingRight: 0,
            plotOptions: {
                pie: {
                    size:'100%',
                    dataLabels: {
                        enabled: false
                    }
                }
            },
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

//---------------------------------------------
function columnGrlLU(jsonTotalResponseLUColumn) {
    // Create the chart
    var chartColoumn = new Highcharts.Chart({
        chart: {
            type: 'column',
            renderTo: 'containerColumnLU'
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
            data: jsonTotalResponseLUColumn
        }]
    });
}

//---------------------------------------
$( "#schoolId" ).change(function() {
    $("#schoolFrm").submit();
});



function statsUser(personid, username, avance){

    console.log(personid);
    console.log(username);
    console.log(avance);
    /*
    var listUser =jsonListUser;
    var list = "";
    var si = 0;
    var no = 0;
    var nose = 0;
    $.each(listUser, function(i, item) {
        list += "<option value='"+listUser[i].si+"|"+listUser[i].no+"|"+listUser[i].nose+"'>"+listUser[i].title+"</option>"
        si += listUser[i].si;
        no += listUser[i].no;
        nose += listUser[i].nose;
    });

    createGraph(si, no, nose);

    var progress = '<h1>avance: <h3>'+avance+'%</h3></h1>' +
                    '<div class="progress progress-bar-info progress-bar-striped">'+
                        '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'+avance+'"aria-valuemin="0" aria-valuemax="100" style="width: '+avance+'%;">'+
                        '</div>'+
                    '</div>'
                    ;

    var divList = "<div class='row'><div class='col-sm-6'><h1>"+username+"</h1></div><div class='col-sm-4 col-sm-offset-2'>"+progress+"</div></div><h5>Evaluaciones Realizadas:</h5><div class='well'><select id='evaluacioLU' class='form-control' size='1' onchange='(abc(this.value))'><option value='"+si+"|"+no+"|"+nose+"'>Global</option>"+list+"</select></div>";

    $('#statsUser').modal();
    $('#bodyStatsUser').html(divList);
    */
}

function abc(rs){
    var arr = rs.split('|');
    createGraph(arr[0],arr[1],arr[2]);
}

function createGraph(si, no, nose){
    var jsonTotalResponseLUPie= jQuery.parseJSON( '[{"name": "Sí", "y": '+si+', "sliced": true, "selected": true},{"name": "No", "y": '+no+', "sliced": false, "selected": false},{"name": "No sé", "y": '+nose+', "sliced": false, "selected": false}]' );
    var jsonTotalResponseLUColumn = jQuery.parseJSON( '[{"name": "Sí", "y": '+si+'},{"name": "No", "y": '+no+'},{"name": "No sé", "y": '+nose+'}]' );
    pieGrlLU(jsonTotalResponseLUPie);
    columnGrlLU(jsonTotalResponseLUColumn);
}
