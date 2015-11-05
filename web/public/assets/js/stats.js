/**
 * Created by isra on 20/10/15.
 */
function pieGrl(jsonTotalResponsePie) {
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
var chart;

function pieGrlLU(jsonTotalResponseLUPie) {
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
var chartColoumn;
function columnGrlLU(jsonTotalResponseLUColumn) {
    // Create the chart
    chartColoumn = new Highcharts.Chart({
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

var personIdGlobal = '';

function statsUser(personid, username, avance, eval){
    $('#surveyUser').html( '' );

    personIdGlobal = personid;

    var siG = 0;
    var noG = 0;
    var noseG = 0;
    var list = "";

    $.each(eval, function(i, item) {
        var si = 0;
        var no = 0;
        var nose = 0;
        $.each(item, function(j, item2) {
            switch(j){
                case 'Sí':
                    siG += parseInt(item2);
                    si += parseInt(item2);
                    break;
                case 'No':
                    noG += parseInt(item2);
                    no += parseInt(item2);
                    break;
                case 'No sé':
                    noseG += parseInt(item2);
                    nose += parseInt(item2);
                    break;
            }
        });
        list += "<option value='"+si+":"+no+":"+nose+":"+i+"'>"+i+"</option>";
    });

    createGraph(siG, noG, noseG);


    var progress = '<h1>avance: <h3>'+avance+'%</h3></h1>' +
                    '<div class="progress progress-bar-info progress-bar-striped">'+
                        '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'+avance+'"aria-valuemin="0" aria-valuemax="100" style="width: '+avance+'%;">'+
                        '</div>'+
                    '</div>'
                    ;

    var divList = "<div class='row'><div class='col-sm-6'><h1>"+username+"</h1></div><div class='col-sm-4 col-sm-offset-2'>"+progress+"</div></div><h5>Selecciona alguna Evaluación para ver su <b>detalle</b>:</h5><div class='well'><select id='evaluacioLU' class='form-control' size='1' onchange='(abc(this.value))'><option value='"+siG+":"+noG+":"+noseG+":0'>Global</option>"+list+"</select></div>";

    $('#statsUser').modal();
    $('#bodyStatsUser').html(divList);

    reflowChart();
}

function reflowChart(){
    console.log('reflowChart');
    chart.reflow();
    chartColoumn.reflow();
}

function abc(rs){
    var arr = rs.split(':');
    createGraph(arr[0],arr[1],arr[2]);
    if(arr[3] != 0){
        $.post( "ajax/stats", { title: arr[3], personId: personIdGlobal })
            .done(function( data ) {
                $('#surveyUser').html( data );
            });
    }else{
        $('#surveyUser').html( '' );
    }

}

function createGraph(si, no, nose){
    var jsonTotalResponseLUPie= jQuery.parseJSON( '[{"name": "Sí", "y": '+si+', "sliced": true, "selected": true},{"name": "No", "y": '+no+', "sliced": false, "selected": false},{"name": "No sé", "y": '+nose+', "sliced": false, "selected": false}]' );
    var jsonTotalResponseLUColumn = jQuery.parseJSON( '[{"name": "Sí", "y": '+si+'},{"name": "No", "y": '+no+'},{"name": "No sé", "y": '+nose+'}]' );
    pieGrlLU(jsonTotalResponseLUPie);
    columnGrlLU(jsonTotalResponseLUColumn);
}


var TablesDatatables = function() {

    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#userList-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 0, 4 ] } ],
                pageLength: 4,
                lengthMenu: [[5,10,15,20,25,30, -1], [5,10,15,20,25,30, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');

        }
    };
}();