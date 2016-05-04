var TablesDatatables = function() {

    return {
        init: function() {

            /**
             * Implementación de datatable para la tabla de respuestas
             */

            App.datatables();
            $('#tbl-datatable').DataTable({
                pageLength: -1,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'Ver Todo']]
            });

            $('.dataTables_filter input').attr('placeholder', 'Buscar');
            $('#tbl-datatable_info').hide();
        }
    };
}();

function getResume(surveyId, personId){
    $.get( "/api/v0/result/survey/"+surveyId+"/person/"+personId)
        .done(function( data ) {

            console.log(data.persons[0].surveys[0].questions)
            $('#table-tareas').html(createDataTableRes(data.persons[0].surveys[0].questions));
        })
        .fail(function(error) {
            console.log( error );
        })
        .always(function() {
            console.log("valid Assigned finished")
        });
}

function getSurveyId(uri){
    var usriArr = uri.split('/');
    var zise = usriArr.length;

    return usriArr[zise-1];
}

function createDataTableRes(dat){
    var dataTable =
        '<div class="table-responsive">'+
            '<table id="tbl-datatable-tarea" class="table table-bordered">'+
                '<thead>'+
                    '<tr>'+
                        '<th class="hidden-sm hidden-xs"><b>#</b></th>'+
                        '<th class="text-center"><b>Pregunta</b></th>'+
                        '<th class="text-center">'+
                            '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Respuesta</b></span>'+
                            '<span class="visible-xs-inline"><b><i class="fa fa-pencil-square-o"></i></b></span>'+
                        '</th>'+
                        '<th class="text-center">'+
                            '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Comentario</b></span>'+
                            '<span class="visible-xs-inline"><b><i class="fa fa-commenting-o"></i></b></span>'+
                        '</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody>';


    $.each(dat, function (key, value) {
        dataTable += '';
        console.log(value);
    });

    dataTable +=
                '</tbody>'+
            '</table>'+
        '</div>';

    return dataTable;
}